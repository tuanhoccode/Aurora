<?php

namespace App\Http\Controllers;

use middleware;
use App\Models\Order;
use App\Models\Refund;
use App\Models\RefundItem;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Mail\RefundStatusMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class RefundController extends Controller
{
    public function __construct() {}

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
            ->with(['items' => function ($query) {
                $query->with(['product', 'variant.attributes.attribute']);
            }])
            ->firstOrFail();

        return view('client.refund', compact('order'));
    }

    // Client: Gửi yêu cầu hoàn tiền
    public function submit(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['status' => 'error', 'message' => 'Vui lòng đăng nhập để gửi yêu cầu hoàn tiền.'], 401);
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
            // Thêm các ngân hàng khác nếu cần
        ];

        $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'total_amount' => 'required|numeric|min:0.01|max:' . $request->input('total_amount'),
            'reason' => 'required|in:product_defective,changed_mind,wrong_item_delivered,other',
            'bank_account' => [
                'required',
                'string',
                'regex:/^[0-9]{8,20}$/',
            ],
            'user_bank_name' => [
                'required',
                'string',
                'max:100',
            ],
            'bank_name' => [
                'required',
                'string',
                'in:' . implode(',', $validBanks),
            ],
            'items' => 'required|array',
            'items.*.product_id' => 'required|integer',
            'items.*.variant_id' => 'required|integer',
            'items.*.name' => 'required|string',
            'items.*.name_variant' => 'nullable|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.price_variant' => 'required|numeric|min:0',
            'items.*.quantity_variant' => 'required|integer|min:1',
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
            'items.required' => 'Danh sách sản phẩm không được để trống.',
            'items.*.product_id.required' => 'ID sản phẩm không hợp lệ.',
            'items.*.variant_id.required' => 'ID biến thể không hợp lệ.',
            'items.*.quantity.min' => 'Số lượng sản phẩm phải lớn hơn 0.',
            'items.*.price.min' => 'Giá sản phẩm phải lớn hơn hoặc bằng 0.',
            'items.*.price_variant.min' => 'Giá biến thể phải lớn hơn hoặc bằng 0.',
            'items.*.quantity_variant.min' => 'Số lượng biến thể phải lớn hơn 0.',
        ]);

        // Chuẩn hóa user_bank_name thành uppercase
        $userBankName = strtoupper($request->user_bank_name);

        try {
            DB::beginTransaction();

            // Kiểm tra lại điều kiện
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

            // Chuẩn hóa tên chủ tài khoản
            $userBankName = $request->user_bank_name;

            // Tạo yêu cầu hoàn tiền
            $refundId = Refund::create([
                'order_id' => $request->order_id,
                'user_id' => Auth::id(),
                'total_amount' => $request->total_amount,
                'bank_account' => $request->bank_account,
                'user_bank_name' => $userBankName,
                'bank_name' => $request->bank_name,
                'reason' => $request->reason,
                'status' => 'pending',
                'is_send_money' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ])->id;

            // Tạo chi tiết sản phẩm hoàn tiền
            foreach ($request->items as $item) {
                RefundItem::create([
                    'refund_id' => $refundId,
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
                ]);
            }

            // Tạo thông báo cho admin
            Notification::create([
                'user_id' => Auth::id(),
                'order_id' => $request->order_id,
                'read' => 0,
                'type' => '1',
                'message' => "Yêu cầu hoàn tiền mới cho đơn hàng #{$request->order_id} từ người dùng #" . Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            \Log::info('Refund request submitted', [
                'refund_id' => $refundId,
                'order_id' => $request->order_id,
                'user_id' => Auth::id(),
                'total_amount' => $request->total_amount,
                'bank_account' => $request->bank_account,
                'user_bank_name' => $userBankName,
                'bank_name' => $request->bank_name,
                'reason' => $request->reason,
                'session_id' => \Session::getId()
            ]);

            DB::commit();
            return response()->json(['status' => 'success', 'refund_id' => $refundId]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Refund submit error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'request' => $request->all(),
                'stack_trace' => $e->getTraceAsString(),
                'session_id' => \Session::getId()
            ]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // Client: Hủy đơn hàng
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

            // Cập nhật trạng thái hủy đơn hàng bằng cột cancelled_at
            $order->update([
                'cancelled_at' => now(),
                'updated_at' => now(),
            ]);

            // Tạo thông báo cho admin
            Notification::create([
                'user_id' => Auth::id(),
                'order_id' => $order_id,
                'read' => 0,
                'type' => 'order_cancelled',
                'message' => "Đơn hàng #{$order_id} đã được hủy bởi người dùng #" . Auth::id() . ". Lý do: {$request->reason}",
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Hủy đơn hàng thành công!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // Admin: Hiển thị danh sách yêu cầu hoàn tiền
    public function adminIndex()
    {


        $refunds = Refund::with('user', 'order')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.refunds.index', compact('refunds'));
    }

    // Admin: Hiển thị chi tiết yêu cầu hoàn tiền
    public function adminShow($id)
    {


        $refund = Refund::with('items', 'user', 'order')->findOrFail($id);
        return view('admin.refunds.show', compact('refund'));
    }

    // Admin: Cập nhật trạng thái yêu cầu hoàn tiền
    public function adminUpdate(Request $request, $id)
    {
        // Validate dữ liệu đầu vào
        $request->validate([
            'status' => 'required|in:pending,receiving,completed,rejected,failed,cancel',
            'admin_reason' => 'nullable|string',
            'is_send_money' => 'required|boolean',
        ]);

        try {
            // Tìm yêu cầu hoàn tiền
            $refund = Refund::findOrFail($id);

            // Định nghĩa các trạng thái cuối
            $finalStatuses = ['completed', 'rejected', 'failed', 'cancel'];

            // Kiểm tra nếu trạng thái hiện tại là trạng thái cuối
            if (in_array($refund->status, $finalStatuses)) {
                Log::warning('Attempt to update final refund status', [
                    'refund_id' => $id,
                    'current_status' => $refund->status,
                    'attempted_status' => $request->status,
                ]);
                return redirect()->back()->with('error', 'Không thể cập nhật trạng thái vì yêu cầu hoàn tiền đã ở trạng thái cuối: ' . $refund->status);
            }

            // Định nghĩa thứ tự trạng thái hợp lệ
            $validTransitions = [
                'pending' => ['receiving'], // Chỉ cho phép pending -> receiving
                'receiving' => ['completed', 'rejected', 'failed', 'cancel'], // receiving -> các trạng thái cuối
            ];

            // Kiểm tra nếu trạng thái mới không hợp lệ
            if (isset($validTransitions[$refund->status]) && !in_array($request->status, $validTransitions[$refund->status])) {
                Log::warning('Invalid status transition attempted', [
                    'refund_id' => $id,
                    'current_status' => $refund->status,
                    'attempted_status' => $request->status,
                ]);
                return redirect()->back()->with('error', "Không thể chuyển từ trạng thái {$refund->status} sang trạng thái {$request->status}. Vui lòng tuân theo thứ tự: pending -> receiving -> completed/rejected/failed/cancel.");
            }

            // Kiểm tra nếu chuyển sang completed thì is_send_money phải là true
            if ($request->status === 'completed' && !$request->is_send_money) {
                Log::warning('Attempt to set status to completed without sending money', [
                    'refund_id' => $id,
                    'is_send_money' => $request->is_send_money,
                ]);
                return redirect()->back()->with('error', 'Không thể chuyển trạng thái sang completed vì chưa hoàn tiền (is_send_money phải là true).');
            }

            // Cập nhật trạng thái yêu cầu hoàn tiền
            $refund->update([
                'status' => $request->status,
                'admin_reason' => $request->admin_reason,
                'is_send_money' => $request->is_send_money,
                'updated_at' => now(),
            ]);

            // Log cập nhật thành công
            Log::info('Refund status updated successfully', [
                'refund_id' => $id,
                'new_status' => $request->status,
                'admin_reason' => $request->admin_reason,
                'is_send_money' => $request->is_send_money,
            ]);

            // Tạo thông báo cho người dùng
            Notification::create([
                'user_id' => $refund->user_id,
                'order_id' => $refund->order_id,
                'read' => 0,
                'type' => '1',
                'message' => "Yêu cầu hoàn tiền #{$id} đã được cập nhật trạng thái thành: {$request->status}",
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Gửi email thông báo trạng thái hoàn tiền
            if ($refund->user && $refund->user->email) {
                Mail::to($refund->user->email)->send(new RefundStatusMail($refund));
                Log::info('Refund status email sent', [
                    'refund_id' => $id,
                    'user_email' => $refund->user->email,
                ]);
            } else {
                Log::warning('No email found for user', [
                    'refund_id' => $id,
                    'user_id' => $refund->user_id,
                ]);
            }

            return redirect()->route('admin.refunds.show')->with('success', 'Cập nhật yêu cầu hoàn tiền thành công!');
        } catch (\Exception $e) {
            Log::error('Error updating refund status', [
                'refund_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'Lỗi khi cập nhật: ' . $e->getMessage());
        }
    }
}
