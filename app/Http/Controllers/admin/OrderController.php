<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\OrderStatusRequest;
use App\Models\OrderStatusHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['items', 'statusHistory' => function ($query) {
            $query->where('is_current', true);
        }]);

        // Đếm số lượng đơn hàng theo trạng thái
        $pendingPaymentCount = Order::where('is_paid', false)->whereHas('statusHistory', function ($q) {
            $q->where('is_current', true)->whereNotIn('order_status_id', [7, 8]);
        })->count();
        $unfulfilledCount = Order::whereHas('statusHistory', function ($q) {
            $q->where('is_current', true)->whereIn('order_status_id', [1]);
        })->count();
        $completedCount = Order::whereHas('statusHistory', function ($q) {
            $q->where('is_current', true)->whereIn('order_status_id', [4]);
        })->count();
        $refundedCount = Order::whereHas('statusHistory', function ($q) {
            $q->where('is_current', true)->whereIn('order_status_id', [7]);
        })->count();
        $failedCount = Order::whereHas('statusHistory', function ($q) {
            $q->where('is_current', true)->whereIn('order_status_id', [8]);
        })->count();

        // Lọc theo trạng thái
        if ($filter = $request->query('filter')) {
            switch ($filter) {
                case 'pending_payment':
                    $query->where('is_paid', false)->whereHas('statusHistory', function ($q) {
                        $q->where('is_current', true)->whereNotIn('order_status_id', [7, 8]);
                    });
                    break;
                case 'unfulfilled':
                    $query->whereHas('statusHistory', function ($q) {
                        $q->where('is_current', true)->whereIn('order_status_id', [1]);
                    });
                    break;
                case 'completed':
                    $query->whereHas('statusHistory', function ($q) {
                        $q->where('is_current', true)->whereIn('order_status_id', [4]);
                    });
                    break;
                case 'refunded':
                    $query->whereHas('statusHistory', function ($q) {
                        $q->where('is_current', true)->whereIn('order_status_id', [7]);
                    });
                    break;
                case 'failed':
                    $query->whereHas('statusHistory', function ($q) {
                        $q->where('is_current', true)->whereIn('order_status_id', [8]);
                    });
                    break;
            }
        }

        // Tìm kiếm
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('fullname', 'like', "%{$search}%");
            });
        }

        $orders = $query->latest()->paginate(10);
        $statuses = OrderStatus::all();

        // Kiểm tra và thêm trạng thái mặc định nếu cần
        foreach ($orders as $order) {
            if (!$order->statusHistory()->where('is_current', true)->exists()) {
                $data = [
                    'order_id' => $order->id,
                    'order_status_id' => 1, // Chờ xác nhận
                    'modifier_id' => Auth::id() ?? 1,
                    'note' => 'Trạng thái mặc định',
                    'is_current' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Thêm customer_confirmation nếu cột tồn tại
                if (Schema::hasColumn('order_status_histories', 'customer_confirmation')) {
                    $data['customer_confirmation'] = 0;
                }

                OrderStatusHistory::create($data);
                $order->update(['order_status_id' => 1]);
            }
        }

        return view('admin.orders.index', compact(
            'orders',
            'statuses',
            'pendingPaymentCount',
            'unfulfilledCount',
            'completedCount',
            'refundedCount',
            'failedCount'
        ));
    }

    public function show($id)
    {
        $user = Auth::user()->load('address');
        $order = Order::with('items.product')->findOrFail($id);
        $statuses = OrderStatus::all();
        $currentStatus = $order->statusHistory()->where('is_current', true)->first();

        // Tính toán trạng thái thanh toán
        $paymentStatus = $currentStatus ? match ($currentStatus->order_status_id) {
            7 => 'Đã hoàn tiền',
            8 => 'Đã hủy',
            default => $order->is_paid ? 'Đã thanh toán' : 'Chờ thanh toán',
        } : 'Chờ thanh toán';

        // Tính toán trạng thái hoàn thành
        $fulfillmentStatus = $currentStatus ? match ($currentStatus->order_status_id) {
            1 => 'Chưa hoàn thành',
            2 => 'Sẵn sàng nhận hàng',
            3 => 'Đang xử lý',
            4 => 'Đã hoàn thành',
            5 => 'Đang xử lý',
            6 => 'Đang xử lý',
            7 => 'Đã hoàn tiền',
            8 => 'Đã hủy',
            9 => 'Sẵn sàng nhận hàng',
            default => 'Chưa hoàn thành',
        } : 'Chưa hoàn thành';

        return view('admin.orders.show', compact('order','user', 'statuses', 'currentStatus', 'paymentStatus', 'fulfillmentStatus'));
    }

    public function updateStatus(OrderStatusRequest $request, $id)
    {
        try {
            // Lấy đơn hàng
            $order = Order::findOrFail($id);

            // Danh sách trạng thái hợp lệ
            $validTransitions = [
                1 => [2, 8],       // Chờ xác nhận → Chờ lấy hàng, Đã hủy
                2 => [9, 8],       // Chờ lấy hàng → Gửi hàng, Đã hủy
                9 => [3, 8],       // Gửi hàng → Đang giao, Đã hủy
                3 => [4, 5, 8],    // Đang giao → Giao hàng thành công, Chờ trả hàng, Đã hủy
                5 => [6, 8],       // Chờ trả hàng → Đã trả hàng, Đã hủy
                6 => [7],          // Đã trả hàng → Hoàn tiền
                4 => [7],          // Giao hàng thành công → Hoàn tiền
                7 => [],           // Hoàn tiền → Dừng
                8 => []            // Đã hủy → Dừng
            ];

            // Trạng thái hiện tại
            $currentStatus = $order->statusHistory()->where('is_current', true)->first();
            $from = $currentStatus?->order_status_id ?? 1;
            $to = $request->order_status_id;

            // CHẶN cập nhật nếu trạng thái hiện tại là Đã hủy
            if ($from == 8) {
                return redirect()->back()->with('error', "Đơn hàng đã bị hủy và không thể cập nhật trạng thái nữa!");
            }

            // 5. Kiểm tra hợp lệ
            if ($from !== null && !in_array($to, $validTransitions[$from] ?? [])) {
                $currentName = OrderStatus::find($from)?->name ?? 'Không rõ';
                $targetName = OrderStatus::find($to)?->name ?? 'Không rõ';
                $validStatuses = implode(', ', array_map(fn($id) => OrderStatus::find($id)?->name ?? 'Không rõ', $validTransitions[$from] ?? []));
                $errorMessage = "Không thể chuyển từ '$currentName' sang '$targetName'. Các trạng thái hợp lệ: " . ($validStatuses ?: 'Không có trạng thái nào hợp lệ.');
                return redirect()->back()->with('error', $errorMessage);
            }

            // Bắt đầu transaction
            DB::beginTransaction();

            // Đánh dấu trạng thái hiện tại là không còn nữa
            if ($currentStatus) {
                $currentStatus->update(['is_current' => false]);
            }

            // Tự động đánh dấu đã thanh toán nếu trạng thái là "Giao hàng thành công"
            $isPaid = $to == 4 ? true : $request->is_paid;

            // Chuẩn bị note
            $note = $request->note ?? 'Chuyển trạng thái: ' . OrderStatus::find($to)?->name;

            // Tạo mã đơn hàng mới
            $newCode = $this->generateNewOrderCode($order, $to);

            // Chuẩn bị dữ liệu cho lịch sử trạng thái
            $data = [
                'order_id' => $order->id,
                'order_status_id' => $to,
                'modifier_id' => Auth::id() ?? 1,
                'note' => $note,
                'is_current' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Thêm customer_confirmation nếu cột tồn tại
            if (Schema::hasColumn('order_status_histories', 'customer_confirmation')) {
                $data['customer_confirmation'] = $request->customer_confirmation ?? 0;
            }

            // Thêm bản ghi mới vào lịch sử
            $history = OrderStatusHistory::create($data);

            if (!$history) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Không thể tạo lịch sử trạng thái!');
            }

            // Cập nhật trạng thái và các trường khác trong bảng Order
            $order->update([
                'order_status_id' => $to,
                'is_paid' => $isPaid,
                'code' => $newCode,
                'note' => $note,
            ]);

            // 12. Trừ kho khi chuyển sang trạng thái giao hàng thành công
            if ($from != 4 && $to == 4) {
                foreach ($order->items as $item) {
                    if ($item->variant) {
                        $item->variant->decrement('stock', $item->quantity);
                    } else if ($item->product) {
                        $item->product->decrement('stock', $item->quantity);
                    }
                }
            }

            DB::commit();

            return redirect()->back()->with('success', 'Cập nhật trạng thái đơn hàng thành công!');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Cập nhật trạng thái thất bại', [
                'order_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', 'Lỗi khi cập nhật trạng thái đơn hàng: ' . $e->getMessage());
        }
    }

    private function generateNewOrderCode(Order $order, $newStatusId)
    {
        $prefix = match ($newStatusId) {
            1 => 'PND', // Chờ xác nhận
            2 => 'RTP', // Chờ lấy hàng
            3 => 'SHP', // Đang giao
            4 => 'CMP', // Giao hàng thành công
            5 => 'RTN', // Chờ trả hàng
            6 => 'RTD', // Đã trả hàng
            7 => 'RFD', // Hoàn tiền
            8 => 'CNL', // Đã hủy
            9 => 'PKP', // Gửi hàng
            default => 'ORD',
        };

        return $prefix . '-' . $order->id . '-' . now()->format('YmdHis');
    }
}