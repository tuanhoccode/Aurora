<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Refund;
use App\Models\RefundItem;
use App\Models\Notification;
use App\Mail\RefundStatusMail;
use App\Mail\OrderCancellationMail;
use App\Models\OrderStatusHistory;
use App\Services\VNPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class RefundController extends Controller
{
    public function __construct()
    {
    }

    public function form($order_code)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để gửi yêu cầu hoàn tiền.');
        }

        $order = Order::where('code', $order_code)
            ->where('user_id', Auth::id())
            ->where('is_paid', 1)
            ->whereNull('cancelled_at')
            ->whereHas('statusHistories', function ($query) {
                $query->where('order_status_id', 10)->where('is_current', 1);
            })
            ->whereDoesntHave('refund', function ($query) {
                $query->where('status', 'pending');
            })
            ->with([
                'items' => function ($query) {
                    $query->with(['product', 'variant.attributes.attribute']);
                }
            ])
            ->firstOrFail();

        return view('client.refund', compact('order'));
    }

    public function submit(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để gửi yêu cầu hoàn tiền.');
        }

        $validBanks = [
            'Vietcombank',
            'Techcombank',
            'MBBank',
            'BIDV',
            'Agribank',
            'VPBank',
            'Sacombank',
            'ACB'
        ];

        $order = Order::findOrFail($request->input('order_id'));

        $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'total_amount' => 'required|numeric|min:0.01|max:' . $order->total_amount,
            'reason' => 'required|in:product_defective,changed_mind,wrong_item_delivered,other',
            'bank_account' => ['required', 'string', 'regex:/^[0-9]{8,20}$/'],
            'user_bank_name' => ['required', 'string', 'max:100'],
            'bank_name' => ['required', 'string', 'in:' . implode(',', $validBanks)],
            'reason_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'items' => 'required|json',
        ], [
            'total_amount.required' => 'Vui lòng nhập số tiền hoàn.',
            'total_amount.numeric' => 'Số tiền hoàn phải là số.',
            'total_amount.min' => 'Số tiền hoàn phải lớn hơn 0.',
            'total_amount.max' => 'Số tiền hoàn không được vượt quá tổng giá trị đơn hàng.',
            'reason.required' => 'Vui lòng chọn lý do hoàn tiền.',
            'reason.in' => 'Lý do hoàn tiền không hợp lệ.',
            'bank_account.required' => 'Vui lòng nhập số tài khoản ngân hàng.',
            'bank_account.regex' => 'Số tài khoản phải là số và có độ dài từ 8 đến 20 ký tự.',
            'user_bank_name.required' => 'Vui lòng nhập tên chủ tài khoản.',
            'user_bank_name.max' => 'Tên chủ tài khoản không được vượt quá 100 ký tự.',
            'bank_name.required' => 'Vui lòng chọn tên ngân hàng.',
            'bank_name.in' => 'Ngân hàng không hợp lệ. Vui lòng chọn từ danh sách.',
            'reason_image.image' => 'Tệp tải lên phải là ảnh.',
            'reason_image.mimes' => 'Ảnh phải có định dạng JPG, JPEG hoặc PNG.',
            'reason_image.max' => 'Ảnh không được vượt quá 5MB.',
            'items.required' => 'Danh sách sản phẩm không được để trống.',
            'items.json' => 'Danh sách sản phẩm không hợp lệ.',
        ]);

        try {
            DB::beginTransaction();

            $order = Order::where('id', $request->order_id)
                ->where('user_id', Auth::id())
                ->where('is_paid', 1)
                ->whereNull('cancelled_at')
                ->whereHas('statusHistories', function ($query) {
                    $query->where('order_status_id', 10)->where('is_current', 1);
                })
                ->whereDoesntHave('refund', function ($query) {
                    $query->where('status', 'pending');
                })
                ->firstOrFail();

            $imagePath = null;
            if ($request->hasFile('reason_image')) {
                $imagePath = $request->file('reason_image')->store('refunds', 'public');
                Log::info('Hình ảnh minh chứng đã lưu', ['reason_image' => $imagePath]);
            }

            $refund = Refund::create([
                'order_id' => $request->order_id,
                'user_id' => Auth::id(),
                'total_amount' => $request->total_amount,
                'bank_account' => $request->bank_account,
                'user_bank_name' => strtoupper($request->user_bank_name),
                'bank_name' => $request->bank_name,
                'reason' => $request->reason,
                'reason_image' => $imagePath,
                'status' => 'pending',
                'is_send_money' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);



            $items = json_decode($request->items, true);
            if (!$items || !is_array($items)) {
                throw new \Exception('Danh sách sản phẩm không hợp lệ.');
            }

            $refundItems = array_map(function ($item) use ($refund) {
                return [
                    'refund_id' => $refund->id,
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'],
                    'name' => $item['name'],
                    'name_variant' => $item['name_variant'] ?? 'N/A',
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'price_variant' => $item['price_variant'],
                    'quantity_variant' => $item['quantity_variant'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }, $items);

            RefundItem::insert($refundItems);

            Notification::create([
                'user_id' => Auth::id(),
                'order_id' => $request->order_id,
                'read' => 0,
                'type' => '1',
                'message' => "Yêu cầu hoàn tiền mới cho đơn hàng #{$request->order_id} từ người dùng #" . Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::info('Yêu cầu hoàn tiền đã được gửi', [
                'refund_id' => $refund->id,
                'order_id' => $request->order_id,
                'user_id' => Auth::id(),
                'total_amount' => $request->total_amount,
                'bank_account' => $request->bank_account,
                'user_bank_name' => strtoupper($request->user_bank_name),
                'bank_name' => $request->bank_name,
                'reason' => $request->reason,
                'reason_image' => $imagePath,
                'session_id' => \Session::getId()
            ]);

            DB::commit();
            return redirect()->route('home')->with('success', 'Yêu cầu hoàn tiền đã được gửi! Mã yêu cầu: ' . $refund->id);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi gửi yêu cầu hoàn tiền: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'request' => $request->all(),
                'stack_trace' => $e->getTraceAsString(),
                'session_id' => \Session::getId()
            ]);
            return redirect()->back()->withInput()->with('error', 'Đã xảy ra lỗi khi xử lý yêu cầu hoàn tiền. Vui lòng thử lại sau.');
        }
    }

    public function cancelOrder(Request $request, $order_id)
    {
        if (!Auth::check()) {
            return response()->json(['status' => 'error', 'message' => 'Vui lòng đăng nhập để hủy đơn hàng.'], 401);
        }

        $request->validate([
            'reason' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $order = Order::where('id', $order_id)
                ->where('user_id', Auth::id())
                ->where('is_paid', 1)
                ->whereNull('cancelled_at')
                ->firstOrFail();

            $order->update([
                'cancelled_at' => now(),
                'cancel_reason' => $request->reason,
                'updated_at' => now(),
            ]);

            Notification::create([
                'user_id' => Auth::id(),
                'order_id' => $order_id,
                'read' => 0,
                'type' => 'order_cancelled',
                'message' => "Đơn hàng #{$order_id} đã được hủy bởi người dùng #" . Auth::id() . ". Lý do: {$request->reason}",
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Gửi email thông báo hủy đơn hàng
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
                    $request->reason,
                    $refundInfo
                ));

                Log::info('Email thông báo hủy đơn hàng (RefundController) đã được gửi', [
                    'order_id' => $order->id,
                    'order_code' => $order->code,
                    'user_email' => $order->email,
                    'payment_method' => $order->payment_id == 2 ? 'VNPay' : 'COD'
                ]);
            } catch (\Exception $e) {
                Log::error('Lỗi gửi email thông báo hủy đơn hàng (RefundController)', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Hủy đơn hàng thành công!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function adminIndex(Request $request)
    {
        $query = Refund::with(['user', 'order']);

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Tìm kiếm theo mã đơn hàng hoặc tên khách hàng
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('order', function ($orderQuery) use ($search) {
                    $orderQuery->where('code', 'like', "%{$search}%");
                })->orWhereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('fullname', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        $refunds = $query->orderBy('created_at', 'desc')->paginate(10);

        // Lấy thống kê tổng quan - đảm bảo có tất cả status
        $stats = Refund::selectRaw('status, COUNT(*) as count')
                       ->groupBy('status')
                       ->pluck('count', 'status')
                       ->toArray();

        // Đảm bảo có tất cả status keys
        $allStatuses = ['pending', 'receiving', 'completed', 'rejected', 'failed', 'cancel'];
        foreach ($allStatuses as $status) {
            if (!isset($stats[$status])) {
                $stats[$status] = 0;
            }
        }



        return view('admin.refunds.index', compact('refunds', 'stats'));
    }

       public function adminShow($id)
    {
        try {
            $refund = Refund::with([
                'items.product',
                'items.productVariant.attributeValues.attribute',
                'user',
                'order' => function ($query) {
                    $query->with('statusHistory');
                }
            ])->findOrFail($id);
            return view('admin.refunds.show', compact('refund'));
        } catch (\Exception $e) {
            Log::error('Error loading refund details', [
                'refund_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('admin.refunds.index')->with('error', 'Không tìm thấy yêu cầu hoàn tiền.');
        }
    }

    public function adminUpdate(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,receiving,completed,rejected,failed,cancel',
            'admin_reason' => 'nullable|string',
            'is_send_money' => 'required|boolean',
            'admin_reason_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:5120',
            ],
        ], [
            'status.required' => 'Vui lòng chọn trạng thái.',
            'status.in' => 'Trạng thái không hợp lệ.',
            'admin_reason_image.image' => 'Tệp tải lên phải là ảnh.',
            'admin_reason_image.mimes' => 'Ảnh phải có định dạng JPG, JPEG hoặc PNG.',
            'admin_reason_image.max' => 'Ảnh không được vượt quá 5MB.',
        ]);

        try {
            DB::beginTransaction();

            $refund = Refund::findOrFail($id);
            $finalStatuses = ['completed', 'rejected', 'failed', 'cancel'];

            // Kiểm tra trạng thái hoàn tiền hiện tại
            if (in_array($refund->status, $finalStatuses)) {
                Log::warning('Attempt to update final refund status', [
                    'refund_id' => $id,
                    'current_status' => $refund->status,
                    'attempted_status' => $request->status,
                ]);
                return redirect()->back()->with('error', 'Không thể cập nhật trạng thái vì yêu cầu hoàn tiền đã ở trạng thái cuối: ' . $this->getReasonText($refund->status));
            }

            // Kiểm tra chuyển đổi trạng thái hoàn tiền hợp lệ
            $validTransitions = [
                'pending' => ['receiving'],
                'receiving' => ['completed', 'rejected', 'failed', 'cancel'],
            ];

            if (isset($validTransitions[$refund->status]) && !in_array($request->status, $validTransitions[$refund->status])) {
                Log::warning('Invalid status transition attempted', [
                    'refund_id' => $id,
                    'current_status' => $refund->status,
                    'attempted_status' => $request->status,
                ]);
                return redirect()->back()->with('error', "Không thể chuyển từ trạng thái {$this->getReasonText($refund->status)} sang trạng thái {$this->getReasonText($request->status)}.");
            }

            // Kiểm tra điều kiện khi trạng thái là completed
            if ($request->status === 'completed') {
                if (!$request->is_send_money) {
                    Log::warning('Attempt to set status to completed without sending money', [
                        'refund_id' => $id,
                        'is_send_money' => $request->is_send_money,
                    ]);
                    return redirect()->back()->with('error', 'Không thể chuyển trạng thái sang hoàn thành vì chưa hoàn tiền.');
                }

                if (!$request->hasFile('admin_reason_image') && !$refund->admin_reason_image) {
                    Log::warning('Attempt to set status to completed without admin reason image', [
                        'refund_id' => $id,
                    ]);
                    return redirect()->back()->with('error', 'Vui lòng tải lên ảnh minh chứng khi chuyển trạng thái sang hoàn thành.');
                }
            }

            // Xử lý ảnh admin_reason_image
            $imagePath = $refund->admin_reason_image;
            if ($request->hasFile('admin_reason_image')) {
                if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = $request->file('admin_reason_image')->store('refunds', 'public');
                Log::info('Admin reason image stored', [
                    'refund_id' => $id,
                    'admin_reason_image' => $imagePath,
                ]);
            }

            // Cập nhật trạng thái hoàn tiền
            $refund->update([
                'status' => $request->status,
                'admin_reason' => $request->admin_reason,
                'is_send_money' => $request->is_send_money,
                'admin_reason_image' => $imagePath,
                'updated_at' => now(),
            ]);

            // Xử lý chuyển trạng thái đơn hàng và lưu ảnh img_refunded_money nếu hoàn tiền thành công
            if ($request->status === 'completed') {
                $order = Order::findOrFail($refund->order_id);
                $currentStatus = $order->statusHistory()->where('is_current', true)->first();
                $from = $currentStatus?->order_status_id ?? 1;
                $to = 7; // Trạng thái "Hoàn tiền"

                // Cho phép chuyển nhiều trạng thái sang Hoàn tiền, bao gồm cả đã hủy
                $orderValidTransitions = [
                    1 => [7], // Chờ xác nhận → Hoàn tiền
                    2 => [7], // Chờ lấy hàng → Hoàn tiền
                    3 => [7], // Đang giao → Hoàn tiền
                    4 => [7], // Giao hàng thành công → Hoàn tiền
                    8 => [7], // Đã hủy → Hoàn tiền
                    9 => [7], // Gửi hàng → Hoàn tiền
                    10 => [7], // Hoàn tất → Hoàn tiền
                ];

                if (!in_array($to, $orderValidTransitions[$from] ?? [7])) {
                    $currentName = $this->getOrderStatusText($from);
                    $targetName = $this->getOrderStatusText($to);
                    $validStatuses = implode(', ', array_map(fn($id) => $this->getOrderStatusText($id), $orderValidTransitions[$from] ?? []));
                    $errorMessage = "Không thể chuyển trạng thái đơn hàng từ '$currentName' sang '$targetName'. Các trạng thái hợp lệ: " . ($validStatuses ?: '7 - Hoàn tiền');
                    Log::warning('Invalid order status transition attempted', [
                        'order_id' => $order->id,
                        'current_status' => $from,
                        'attempted_status' => $to,
                    ]);
                    return redirect()->back()->with('error', $errorMessage);
                }

                // Xử lý VNPay refund nếu đơn hàng thanh toán bằng VNPay
                $vnpayRefundSuccess = true;
                $vnpayTransactionId = null;

                if ($order->payment_id == 2 && $order->is_paid) {
                    try {
                        // Lấy transaction_id từ PaymentLog
                        $paymentLog = \App\Models\PaymentLog::where('order_id', $order->id)
                            ->where('response_code', '00') // VNPay success code
                            ->first();

                        if ($paymentLog && $paymentLog->transaction_no) {
                            $vnpayService = new VNPayService();
                            $vnpayRefundSuccess = $vnpayService->refund(
                                $refund,
                                $paymentLog->transaction_no,
                                $refund->total_amount,
                                $order->code
                            );
                            $vnpayTransactionId = $paymentLog->transaction_no;


                        } else {
                            Log::warning('VNPay transaction not found for refund', [
                                'refund_id' => $refund->id,
                                'order_id' => $order->id
                            ]);
                            // Không chặn hoàn thành nếu thiếu transaction_no; giả định xử lý thủ công
                            $vnpayRefundSuccess = true;
                        }
                    } catch (\Exception $e) {
                        Log::error('VNPay refund failed', [
                            'refund_id' => $refund->id,
                            'order_id' => $order->id,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                        $vnpayRefundSuccess = false;
                    }
                }

                // Nếu VNPay refund thất bại, không cho phép hoàn thành
                if ($order->payment_id == 2 && !$vnpayRefundSuccess) {
                    return redirect()->back()->with('error', 'Hoàn tiền VNPay thất bại. Vui lòng kiểm tra lại thông tin giao dịch.');
                }

                // Đánh dấu trạng thái hiện tại là không còn
                if ($currentStatus) {
                    $currentStatus->update(['is_current' => false]);
                }

                // Chuẩn bị dữ liệu cho lịch sử trạng thái
                $note = $request->admin_reason ?? 'Chuyển trạng thái: ' . $this->getOrderStatusText($to);
                if ($vnpayTransactionId) {
                    $note .= " | VNPay Transaction: {$vnpayTransactionId}";
                }

                $data = [
                    'order_id' => $order->id,
                    'order_status_id' => $to,
                    'modifier_id' => Auth::id() ?? 1,
                    'note' => $note,
                    'is_current' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Tạo bản ghi lịch sử trạng thái
                $history = OrderStatusHistory::create($data);

                if (!$history) {
                    throw new \Exception('Không thể tạo lịch sử trạng thái đơn hàng.');
                }

                // Cập nhật trạng thái đơn hàng và lưu img_refunded_money
                $order->update([
                    'order_status_id' => $to,
                    'note' => $note,
                    'img_refunded_money' => $imagePath, // Lưu ảnh từ admin_reason_image
                    'updated_at' => now(),
                ]);


            }

            // Tạo thông báo cho người dùng
            Notification::create([
                'user_id' => $refund->user_id,
                'order_id' => $refund->order_id,
                'read' => 0,
                'type' => '1',
                'message' => "Yêu cầu hoàn tiền #{$id} đã được cập nhật trạng thái thành: {$this->getReasonText($request->status)}",
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Gửi email thông báo
            if ($refund->user && $refund->user->email) {
                try {
                    // Thêm thông tin VNPay refund vào email nếu cần
                    $refundInfo = null;
                    if ($request->status === 'completed' && isset($vnpayTransactionId)) {
                        $refundInfo = [
                            'transaction_id' => $vnpayTransactionId,
                            'amount' => $refund->total_amount,
                            'status' => 'completed',
                            'estimated_time' => '3-5 ngày làm việc'
                        ];
                    }

                    Mail::to($refund->user->email)->send(new RefundStatusMail($refund, $refundInfo));

                } catch (\Exception $e) {
                    Log::error('Failed to send refund status email', [
                        'refund_id' => $id,
                        'user_email' => $refund->user->email,
                        'error' => $e->getMessage(),
                    ]);
                }
            } else {
                Log::warning('No email found for user', [
                    'refund_id' => $id,
                    'user_id' => $refund->user_id,
                ]);
            }

            DB::commit();
            return redirect()->route('admin.refunds.show', $id)->with('success', 'Cập nhật yêu cầu hoàn tiền thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating refund status', [
                'refund_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'Lỗi khi cập nhật: ' . $e->getMessage());
        }
    }

    public static function getReasonText($reason)
    {
        $reasons = [
            'product_defective' => 'Sản phẩm lỗi',
            'changed_mind' => 'Thay đổi ý định',
            'wrong_item_delivered' => 'Giao sai hàng',
            'other' => 'Khác',
            'pending' => 'Đang chờ',
            'receiving' => 'Đang nhận hàng',
            'completed' => 'Hoàn thành',
            'rejected' => 'Từ chối',
            'failed' => 'Thất bại',
            'cancel' => 'Hủy',
        ];
        return $reasons[$reason] ?? $reason;
    }

    public static function getOrderStatusText($statusId)
    {
        $statuses = [
            1 => 'Chờ xác nhận',
            2 => 'Chờ lấy hàng',
            3 => 'Đang giao',
            4 => 'Giao hàng thành công',
            5 => 'Chờ trả hàng',
            6 => 'Đã trả hàng',
            7 => 'Hoàn tiền',
            8 => 'Đã hủy',
            9 => 'Gửi hàng',
            10 => 'Hoàn tất',
        ];
        return $statuses[$statusId] ?? 'Không rõ';
    }
}
