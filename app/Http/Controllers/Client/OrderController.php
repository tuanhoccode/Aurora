<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderOrderStatus;
use App\Models\OrderStatus;
use App\Models\OrderStatusHistory;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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
                              ->whereNotIn('order_status_id', [4, 7, 8]); // Không phải đã giao, đã hoàn tiền hoặc đã hủy
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
                case 'delivered':
                    // Giao hàng thành công (chưa xác nhận)
                    $query->whereHas('statusHistory', function ($q) {
                        $q->where('is_current', true)
                          ->where('order_status_id', 4) // Giao hàng thành công
                          ->whereDoesntHave('order.statusHistory', function($q) {
                              $q->where('order_status_id', 10); // Chưa chuyển sang trạng thái Hoàn thành (10)
                          });
                    });
                    break;
                case 'completed':
                    // Đã hoàn thành (đã xác nhận nhận hàng)
                    $query->whereHas('statusHistory', function ($q) {
                        $q->where('is_current', true)
                          ->where('order_status_id', 10); // Hoàn thành (10)
                    });
                    break;
                case 'cancelled':
                    // Đã hủy
                    $query->whereHas('statusHistory', function ($q) {
                        $q->where('is_current', true)
                          ->where('order_status_id', 8); // Đã hủy
                    });
                    break;
                    
                case 'return_refund':
                    // Trả hàng/Hoàn tiền
                    $query->whereHas('statusHistory', function ($q) {
                        $q->where('is_current', true)
                          ->whereIn('order_status_id', [5, 6, 7]); // Đang xử lý hoàn tiền, Đã hoàn tiền, Từ chối hoàn tiền
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
        $user = Auth::user();
        
        // Lấy tất cả đơn hàng của user
        $allOrders = $user->orders()->with('statusHistory')->get();
        
// Khởi tạo các biến đếm
        $allCount = $allOrders->count();
        $pendingPaymentCount = 0;
        $processingCount = 0;
        $shippingCount = 0;
        $deliveredCount = 0;
        $completedCount = 0;
        $cancelledCount = 0;
        $returnRefundCount = 0;
        
        // Đếm số lượng đơn hàng theo từng trạng thái
        foreach ($allOrders as $order) {
            $currentStatus = $order->statusHistory->where('is_current', true)->first();
            
            if (!$currentStatus) {
                continue;
            }
            
            $statusId = $currentStatus->order_status_id;
            
            // Xử lý từng trạng thái đơn hàng
            switch (true) {
                case $statusId == 1:
                    $processingCount++;
                    break;
                    
                case in_array($statusId, [2, 3]):
                    $shippingCount++;
                    break;
                    
                case $statusId == 4:
                    // Kiểm tra xem đã có trạng thái hoàn thành (10) chưa
                    $hasCompleted = $order->statusHistory->contains('order_status_id', 10);
                    if (!$hasCompleted) {
                        $deliveredCount++;
                    }
                    break;
                    
                case $statusId == 10:
                    $completedCount++;
                    break;
                    
                case $statusId == 8:
                    $cancelledCount++;
                    break;
                    
                case in_array($statusId, [5, 6, 7]):
                    // Đang xử lý hoàn tiền, Đã hoàn tiền, Từ chối hoàn tiền
                    $returnRefundCount++;
                    break;
            }
            
            // Đếm đơn hàng chờ thanh toán
            if (!$order->is_paid && !in_array($statusId, [4, 7, 8, 10])) {
                $pendingPaymentCount++;
            }
        }

        // Tạo mảng kết quả để truyền vào view
        $statusCounts = [
            'all' => $allCount,
            'pending_payment' => $pendingPaymentCount,
            'processing' => $processingCount,
            'shipping' => $shippingCount,
            'delivered' => $deliveredCount,
            'completed' => $completedCount,
            'cancelled' => $cancelledCount,
            'return_refund' => $returnRefundCount
        ];
        
        return view('client.orders.index', compact('orders', 'statusCounts', 'search'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(404);
        }

        $order->load(['items.product', 'statusHistory.status', 'payment']);

        return view('client.orders.show', compact('order'));
    }

    /**
     * Xử lý mua lại đơn hàng
     */
    public function reorder(Order $order, Request $request)
    {
        if ($order->user_id !== Auth::id()) {
            abort(404);
        }

        // Kiểm tra trạng thái đơn hàng có được phép mua lại không
        $allowedStatuses = ['COMPLETED', 'CANCELLED', 'DELIVERED'];
        $currentStatus = $order->currentStatus->status->code ?? '';
        
        if (!in_array($currentStatus, $allowedStatuses)) {
            return back()->with('reorder_status', [
                'type' => 'danger',
                'message' => 'Không thể mua lại đơn hàng này.'
            ]);
        }

        DB::beginTransaction();
        try {
            // Lấy hoặc tạo giỏ hàng của người dùng
            $cart = Cart::firstOrCreate(
                ['user_id' => Auth::id()],
                ['total_price' => 0]
            );

            $addedItems = 0;
            $unavailableItems = [];

            // Duyệt qua các sản phẩm trong đơn hàng cũ
            foreach ($order->items as $item) {
                // Kiểm tra sản phẩm còn bán không
                $product = $item->product;
                if (!$product || !$product->is_active || $product->stock < 1) {
                    $unavailableItems[] = $product ? $product->name : 'Sản phẩm không tồn tại';
                    continue;
                }

                // Kiểm tra biến thể (nếu có)
                $variant = null;
                if ($item->variant_id) {
                    $variant = ProductVariant::find($item->variant_id);
                    if (!$variant || $variant->quantity < 1) {
                        $unavailableItems[] = $product->name . ($variant ? ' - ' . $variant->name : '');
                        continue;
                    }
                }

                // Thêm vào giỏ hàng
                $quantity = min($variant ? $variant->quantity : $product->stock, $item->quantity);
                
                // Kiểm tra xem sản phẩm đã có trong giỏ chưa
                $existingCartItem = $cart->items()
                    ->where('product_id', $product->id)
                    ->where('variant_id', $variant ? $variant->id : null)
                    ->first();

                if ($existingCartItem) {
                    // Nếu đã có thì cộng thêm số lượng (không vượt quá số lượng có sẵn)
                    $newQuantity = min(
                        $existingCartItem->quantity + $quantity,
                        $variant ? $variant->quantity : $product->stock
                    );
                    $existingCartItem->update(['quantity' => $newQuantity]);
                } else {
                    // Nếu chưa có thì tạo mới
                    $cart->items()->create([
                        'product_id' => $product->id,
                        'variant_id' => $variant ? $variant->id : null,
                        'quantity' => $quantity,
                        'price' => $item->price_variant ?? $item->price
                    ]);
                }

                $addedItems++;
            }

            // Cập nhật tổng tiền giỏ hàng
            $cart->updateTotalPrice();

            DB::commit();

            // Thông báo kết quả
            $message = 'Đã thêm ' . $addedItems . ' sản phẩm vào giỏ hàng.';
            
            if (!empty($unavailableItems)) {
                $message .= ' Không thể thêm ' . count($unavailableItems) . ' sản phẩm do hết hàng hoặc ngừng kinh doanh.';
            }

            // Kiểm tra nếu có yêu cầu chuyển hướng
            if ($request->has('redirect_to_cart')) {
                return redirect()->route('shopping-cart.index')->with('success', $message);
            }

            return back()->with('reorder_status', [
                'type' => 'success',
                'message' => $message
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi mua lại đơn hàng: ' . $e->getMessage());
            
            return back()->with('reorder_status', [
                'type' => 'danger',
                'message' => 'Có lỗi xảy ra khi xử lý yêu cầu. Vui lòng thử lại sau.'
            ]);
        }
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
    
    /**
     * Xác nhận đã nhận hàng
     */
    public function confirmDelivery(Order $order)
    {
        // Kiểm tra quyền truy cập
        if ($order->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Bạn không có quyền xác nhận đơn hàng này');
        }
        
        $deliveredStatusId = 4; // ID cho trạng thái Giao hàng thành công
        $completedStatusId = 10; // ID cho trạng thái Hoàn thành
        
        // Kiểm tra xem đơn hàng đã ở trạng thái giao hàng thành công chưa
        $currentStatus = $order->statusHistory()
            ->where('is_current', 1)
            ->first();
            
        if (!$currentStatus || $currentStatus->order_status_id !== $deliveredStatusId) {
            return redirect()->back()->with('error', 'Chỉ có thể xác nhận đơn hàng đã được giao thành công');
        }
        
        DB::beginTransaction();
        try {
            // Cập nhật trạng thái cũ
            $order->statusHistory()
                ->where('is_current', 1)
                ->update(['is_current' => 0]);
                
            // Tạo trạng thái mới là Hoàn thành (10)
            OrderOrderStatus::create([
                'order_id' => $order->id,
                'order_status_id' => $completedStatusId, // Sử dụng ID 10 cho trạng thái Hoàn thành
                'modified_by' => Auth::id(),
                'note' => 'Khách hàng đã xác nhận nhận hàng',
                'is_current' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'order_status_id' => $completedStatusId, // Sử dụng ID 10 cho trạng thái Hoàn thành
                'modifier_id' => Auth::id(),
                'note' => 'Khách hàng đã xác nhận nhận hàng',
                'is_current' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            DB::commit();
            
            return redirect()->route('client.orders')->with('success', 'Đã xác nhận nhận hàng thành công');
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Lỗi khi xác nhận nhận hàng: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xác nhận nhận hàng: ' . $e->getMessage()
            ], 500);
        }
    }
}
