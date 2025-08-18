<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderOrderStatus;
use App\Models\OrderStatus;
use App\Models\OrderStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->input('filter');
        $search = $request->input('search');
        
        $query = Order::where('user_id', Auth::id())
            ->with([
                'items.product',
                'orderItems',
                'items.review',
                'currentOrderStatus.status',
                'payment'
            ])
            ->orderBy('created_at', 'desc');
            
        // Apply status filter
        if ($filter = $request->query('filter')) {
            switch ($filter) {
                case 'pending_payment':
                    // Chờ thanh toán
                    $query->where('is_paid', false)
                        ->whereHas('statusHistory', function ($q) {
                            $q->where('is_current', true)
                              ->whereNotIn('order_status_id', [7, 8]); // Không phải đã hoàn tiền hoặc đã hủy
                        });
                    break;
                case 'processing':
                    // Đang xử lý (Chờ xác nhận)
                    $query->whereHas('statusHistory', function ($q) {
                        $q->where('is_current', true)
                          ->whereIn('order_status_id', [1]); // Chờ xác nhận
                    });
                    break;
                case 'shipping':
                    // Đang giao hàng
                    $query->whereHas('statusHistory', function ($q) {
                        $q->where('is_current', true)
                          ->whereIn('order_status_id', [2, 3]); // Chờ lấy hàng, Đang giao
                    });
                    break;
                case 'completed':
                    // Đã giao hàng
                    $query->whereHas('statusHistory', function ($q) {
                        $q->where('is_current', true)
                          ->where('order_status_id', 4); // Giao hàng thành công
                    });
                    break;
                case 'cancelled':
                    // Đã hủy
                    $query->whereHas('statusHistory', function ($q) {
                        $q->where('is_current', true)
                          ->where('order_status_id', 8); // Đã hủy
                    });
                    break;
            }
        }
        
        // Apply search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', '%' . $search . '%')
                  ->orWhere('id', $search)
                  ->orWhereHas('items.product', function($q) use ($search) {
                      $q->where('name', 'like', '%' . $search . '%');
                  });
            });
        }
        
        $orders = $query->paginate(10);
        
        // Đếm số lượng đơn hàng theo từng trạng thái
        $allCount = Order::where('user_id', Auth::id())->count();
        
        // Đếm đơn chờ thanh toán
        $pendingPaymentCount = Order::where('user_id', Auth::id())
            ->where('is_paid', false)
            ->whereHas('statusHistory', function($q) {
                $q->where('is_current', true)
                  ->whereNotIn('order_status_id', [7, 8]);
            })->count();
            
        // Đếm đơn đang xử lý (chờ xác nhận)
        $processingCount = Order::where('user_id', Auth::id())
            ->whereHas('statusHistory', function($q) {
                $q->where('is_current', true)
                  ->where('order_status_id', 1);
            })->count();
            
        // Đếm đơn đang giao hàng
        $shippingCount = Order::where('user_id', Auth::id())
            ->whereHas('statusHistory', function($q) {
                $q->where('is_current', true)
                  ->whereIn('order_status_id', [2, 3]);
            })->count();
            
        // Đếm đơn đã giao thành công
        $completedCount = Order::where('user_id', Auth::id())
            ->whereHas('statusHistory', function($q) {
                $q->where('is_current', true)
                  ->where('order_status_id', 4);
            })->count();
            
        // Đếm đơn đã hủy
        $cancelledCount = Order::where('user_id', Auth::id())
            ->whereHas('statusHistory', function($q) {
                $q->where('is_current', true)
                  ->where('order_status_id', 8);
            })->count();

        return view('client.orders.index', compact(
            'orders', 
            'allCount', 
            'pendingPaymentCount', 
            'processingCount', 
            'completedCount', 
            'cancelledCount',
            'shippingCount',
            'search'
        ));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Không có quyền truy cập đơn hàng này');
        }

        $order->load([
            'items', 
            'payment',
            'currentOrderStatus.status',
        ]);

        return view('client.orders.show', compact('order'));
    }

    public function tracking(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Không có quyền truy cập đơn hàng này');
        }

        $order->load([
            'items.product',
            'items.variant.attributes.attribute',
            'items.variant.attributes.value',
            'payment',
            'currentOrderStatus.status',
            'statusHistory.status',
        ]);

        // Các trạng thái cần cho timeline
        $timelineKeys = [
            'Chờ xác nhận',
            'Chờ lấy hàng',
            'Đang giao',
            'Giao hàng thành công',
            'Đã hủy',
        ];

        // Lấy lịch sử trạng thái từ bảng order_order_status (theo thứ tự thời gian, chỉ lấy các trạng thái thuộc timeline)
        $orderStatusSteps = OrderOrderStatus::where('order_id', $order->id)
            ->whereHas('status', function($q) use ($timelineKeys) {
                $q->whereIn('name', $timelineKeys);
            })
            ->with('status')
            ->orderBy('created_at')
            ->get();

        return view('client.orders.tracking', compact('order', 'orderStatusSteps'));
    }

    public function cancel(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Không có quyền hủy đơn hàng này');
        }

        // Validate request
        $request->validate([
            'cancel_reason' => 'required|string|max:255',
            'cancel_note' => 'nullable|string|max:500',
        ]);

        $currentStatusRecord = OrderOrderStatus::where('order_id', $order->id)
            ->where('is_current', 1)
            ->first();

        if (!$currentStatusRecord) {
            return redirect()->back()->with('error', 'Không thể xác định trạng thái hiện tại của đơn hàng!');
        }

        $currentStatusId = $currentStatusRecord->order_status_id;

        $cancelStatus = OrderStatus::where('name', 'Đã hủy')->first();
        if (!$cancelStatus) {
            return redirect()->back()->with('error', 'Không tìm thấy trạng thái Đã hủy!');
        }

        $cancelStatusId = $cancelStatus->id;

        // Kiểm tra trạng thái hiện tại có thể hủy không
        $cancelableStatuses = [1, 2, 8]; // Chờ xác nhận, Chờ lấy hàng, Gửi hàng
        if (!in_array($currentStatusId, $cancelableStatuses)) {
            return redirect()->back()->with('error', 'Không thể hủy đơn hàng ở trạng thái hiện tại!');
        }

        DB::transaction(function () use ($order, $cancelStatusId, $currentStatusRecord, $request) {
            // Hoàn trả kho cho từng sản phẩm trong đơn hàng
            foreach ($order->items as $item) {
                // Hoàn trả kho cho biến thể nếu có
                if ($item->product_variant_id) {
                    $variant = $item->variant;
                    if ($variant) {
                        $variant->stock += $item->quantity;
                        $variant->save();
                    }
                }
                // Hoàn trả kho cho sản phẩm gốc
                $product = $item->product;
                if ($product) {
                    $product->stock += $item->quantity;
                    $product->save();
                }
            }

            // Cập nhật trạng thái cũ thành không hiện tại
            DB::table('order_order_status')
                ->where('order_id', $currentStatusRecord->order_id)
                ->where('order_status_id', $currentStatusRecord->order_status_id)
                ->where('is_current', 1)
                ->update(['is_current' => 0]);

            OrderStatusHistory::where('order_id', $order->id)
                ->where('is_current', 1)
                ->update(['is_current' => 0]);

            // Tạo trạng thái mới
            $cancelNote = "Lý do: {$request->cancel_reason}";
            if ($request->cancel_note) {
                $cancelNote .= " - Ghi chú: {$request->cancel_note}";
            }

            OrderOrderStatus::create([
                'order_id' => $order->id,
                'order_status_id' => $cancelStatusId,
                'modified_by' => Auth::id(),
                'note' => $cancelNote,
                'created_at' => now(),
                'updated_at' => now(),
                'is_current' => 1,
            ]);

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'order_status_id' => $cancelStatusId,
                'modifier_id' => Auth::id(),
                'note' => $cancelNote,
                'is_current' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        // Cập nhật thông tin hủy đơn hàng
        $order->update([
            'cancel_reason' => $request->cancel_reason,
            'cancel_note' => $request->cancel_note,
            'cancelled_at' => now(),
        ]);

        return redirect()->route('client.orders')
            ->with('success', 'Đã hủy đơn hàng thành công!');
    }

    public function updateOldOrderStatuses()
    {
        $orders = Order::all();

        foreach ($orders as $order) {
            $hasCurrent = OrderOrderStatus::where('order_id', $order->id)
                ->where('is_current', 1)
                ->exists();

            if (!$hasCurrent) {
                OrderOrderStatus::create([
                    'order_id' => $order->id,
                    'order_status_id' => 1, // Chờ xác nhận
                    'modified_by' => $order->user_id ?? 1,
                    'note' => 'Tự động cập nhật trạng thái cho đơn hàng cũ',
                    'is_current' => 1,
                    'created_at' => $order->created_at ?? Carbon::now(),
                    'updated_at' => $order->updated_at ?? Carbon::now(),
                ]);
                echo "Đã cập nhật trạng thái cho đơn hàng #{$order->id}\n";
            }
        }

        return redirect()->back()->with('success', 'Đã cập nhật trạng thái cho các đơn hàng cũ thành công!');
    }
}
