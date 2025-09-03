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
use App\Mail\OrderCancellationMail;
use Illuminate\Support\Facades\Mail;

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
        
        // Danh sách các trạng thái hợp lệ để chuyển đổi
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
        
        // Tạo mảng chứa valid_statuses cho từng đơn hàng
        $orderStatuses = [];
        
        // Lấy danh sách tất cả trạng thái để tối ưu truy vấn
        $allStatuses = OrderStatus::all()->keyBy('id');
        
        foreach ($orders as $order) {
            $currentStatus = $order->statusHistory()->where('is_current', true)->first();
            if ($currentStatus) {
                $validStatuses = $validTransitions[$currentStatus->order_status_id] ?? [];
                $validStatuses[] = $currentStatus->order_status_id; // Thêm trạng thái hiện tại
                $orderStatuses[$order->id] = array_unique($validStatuses);
            } else {
                $orderStatuses[$order->id] = [1]; // Mặc định là trạng thái chờ xác nhận
            }
        }

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

              

                OrderStatusHistory::create($data);
                $order->update(['order_status_id' => 1]);
            }
        }

        return view('admin.orders.index', [
            'orders' => $orders,
            'statuses' => $statuses,
            'pendingPaymentCount' => $pendingPaymentCount,
            'unfulfilledCount' => $unfulfilledCount,
            'completedCount' => $completedCount,
            'refundedCount' => $refundedCount,
            'failedCount' => $failedCount,
            'orderStatuses' => $orderStatuses
        ]);
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
            10 => 'Nhận hàng thành công',
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

            // Kiểm tra hợp lệ
            if (!in_array($to, $validTransitions[$from] ?? [])) {
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
            // $newCode = $this->generateNewOrderCode($order, $to);

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
                // 'code' => $newCode,
                'note' => $note,
            ]);

            DB::commit();

            // Gửi email thông báo nếu đơn hàng bị hủy (status_id = 8)
            if ($to == 8) {
                try {
                    $refundInfo = null;
                    
                    // Nếu là đơn hàng VNPay đã thanh toán, thêm thông tin hoàn tiền
                    if ($order->payment_id == 2 && $order->is_paid) {
                        $refundInfo = [
                            'transaction_id' => 'Đang xử lý',
                            'amount' => $order->total_amount,
                            'status' => 'pending'
                        ];
                    }
                    
                    Mail::to($order->email)->send(new OrderCancellationMail(
                        $order, 
                        $note, 
                        $refundInfo
                    ));
                    
                    Log::info('Email thông báo hủy đơn hàng (Admin) đã được gửi', [
                        'order_id' => $order->id,
                        'order_code' => $order->code,
                        'user_email' => $order->email,
                        'admin_id' => Auth::id(),
                        'payment_method' => $order->payment_id == 2 ? 'VNPay' : 'COD'
                    ]);
                } catch (\Exception $e) {
                    Log::error('Lỗi gửi email thông báo hủy đơn hàng (Admin)', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

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