<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Refund;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Notification;
use App\Mail\RefundStatusMail;
use App\Services\VNPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class RefundController extends Controller
{
    /**
     * Hàm khởi tạo
     * Áp dụng middleware để yêu cầu người dùng phải đăng nhập và có vai trò admin
     */
    public function __construct()
    {
    }

    /**
     * Hiển thị danh sách yêu cầu hoàn tiền
     * Lấy tất cả yêu cầu hoàn tiền cùng với thông tin đơn hàng, người dùng, và các mặt hàng hoàn tiền
     * Sắp xếp theo thời gian tạo giảm dần
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $refunds = Refund::with(['order', 'user', 'refundItems'])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('admin.refunds.index', compact('refunds'));
    }

    /**
     * Cập nhật trạng thái yêu cầu hoàn tiền
     * Xác thực dữ liệu đầu vào và xử lý logic hoàn tiền
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Refund $refund
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Refund $refund)
    {
        // Xác thực dữ liệu đầu vào
        $request->validate([
            'status' => 'required|in:pending,receiving,completed,rejected,failed,cancel', // Trạng thái phải thuộc các giá trị cho phép
            'admin_reason' => 'nullable|string|required_if:status,rejected,failed|max:255', // Lý do admin bắt buộc nếu trạng thái là rejected hoặc failed
            'img_refunded_money' => 'nullable|file|mimes:jpg,png,pdf|max:10240|required_if:status,completed', // File bằng chứng hoàn tiền bắt buộc nếu trạng thái là completed
        ]);

        // Kiểm tra tính hợp lệ của đơn hàng cho hoàn tiền (trong 7 ngày, trạng thái Giao hàng thành công hoặc Gửi hàng)
        if ($request->status == 'completed' && !$refund->order->isEligibleForRefund()) {
            return back()->withErrors(['status' => 'Đơn hàng đã quá thời gian cho phép hoàn tiền (7 ngày) hoặc không hợp lệ.']);
        }

        // Kiểm tra số tiền hoàn không vượt quá số tiền đơn hàng
        if ($request->status == 'completed' && $refund->total_amount > $refund->order->total_amount) {
            return back()->withErrors(['status' => 'Số tiền hoàn vượt quá số tiền thanh toán của đơn hàng.']);
        }

        // Kiểm tra tài khoản ngân hàng đã được xác thực
        if ($request->status == 'completed' && $refund->bank_account_status != 'verified') {
            return back()->withErrors(['status' => 'Thông tin tài khoản ngân hàng chưa được xác thực.']);
        }

        // Cập nhật trạng thái và lý do của yêu cầu hoàn tiền
        $refund->status = $request->status;
        $refund->admin_reason = $request->admin_reason;

        // Xử lý khi trạng thái là completed
        if ($request->status == 'completed') {
            $refund->is_send_money = true;
            $refund->bank_account_status = 'verified';

            // Nếu là thanh toán VNPay
            if ($refund->order->payment && $refund->order->payment->name == 'VNPay') {
                $vnpay = new VNPayService();
                $paymentLog = $refund->order->paymentLogs()->first();
                $transactionId = $paymentLog ? $paymentLog->txn_ref : null;
                // Kiểm tra giao dịch VNPay và thực hiện hoàn tiền
                if (!$transactionId || !$vnpay->refund($refund, $transactionId, $refund->total_amount, $refund->order->code)) {
                    return back()->withErrors(['status' => 'Lỗi khi thực hiện hoàn tiền qua VNPay.']);
                }
            } else {
                // Nếu là COD, yêu cầu tải lên bằng chứng hoàn tiền
                if ($request->hasFile('img_refunded_money')) {
                    $refund->order->img_refunded_money = $request->file('img_refunded_money')->store('refunds', 'public');
                    $refund->order->save();
                } else {
                    return back()->withErrors(['img_refunded_money' => 'Vui lòng tải lên bằng chứng hoàn tiền cho đơn COD.']);
                }
            }

            // Cập nhật tồn kho cho các mặt hàng hoàn tiền
            foreach ($refund->refundItems as $item) {
                if ($item->variant_id) {
                    $variant = ProductVariant::find($item->variant_id);
                    if ($variant) {
                        $variant->increment('stock', $item->quantity); // Tăng tồn kho cho biến thể sản phẩm
                    }
                } else {
                    $product = Product::find($item->product_id);
                    if ($product && $product->type == 'single') {
                        $product->increment('stock', $item->quantity); // Tăng tồn kho cho sản phẩm đơn
                    }
                }
            }
        }

        // Lưu thay đổi vào yêu cầu hoàn tiền
        $refund->save();

        // Thông báo tương ứng với trạng thái
        $statusMessages = [
            'pending' => 'Yêu cầu hoàn tiền đang chờ xử lý.',
            'receiving' => 'Yêu cầu hoàn tiền đang được nhận hàng.',
            'completed' => "Yêu cầu hoàn tiền đã hoàn thành. Số tiền {$refund->total_amount} VND đã được chuyển vào tài khoản ngân hàng.",
            'rejected' => 'Yêu cầu hoàn tiền đã bị từ chối.',
            'failed' => 'Yêu cầu hoàn tiền đã thất bại.',
            'cancel' => 'Yêu cầu hoàn tiền đã bị hủy.',
        ];

        // Tạo thông báo trong bảng notifications
        Notification::create([
            'user_id' => $refund->user_id,
            'order_id' => $refund->order_id,
            'message' => "Yêu cầu hoàn tiền #{$refund->id}: {$statusMessages[$request->status]}" . ($request->admin_reason ? " Lý do: {$request->admin_reason}" : ''),
            'type' => 1,
            'read' => 0,
        ]);

        // Gửi email thông báo trạng thái hoàn tiền
        Mail::to($refund->user->email)->send(new RefundStatusMail($refund));

        // Ghi log quá trình xử lý hoàn tiền
        \Log::info('Xử lý hoàn tiền', ['refund_id' => $refund->id, 'status' => $request->status]);

        // Chuyển hướng về danh sách hoàn tiền với thông báo thành công
        return redirect()->route('admin.refunds.index')->with('success', 'Cập nhật trạng thái hoàn tiền thành công.');
    }
}