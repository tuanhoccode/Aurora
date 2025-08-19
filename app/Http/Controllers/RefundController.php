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
            ->with(['items' => function($query) {
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

        $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'total_amount' => 'required|numeric',
            'reason' => 'required|in:product_defective,changed_mind,wrong_item_delivered,other',
            'bank_account' => 'required|string',
            'user_bank_name' => 'required|string',
            'bank_name' => 'required|string',
            'items' => 'required|array',
            'items.*.product_id' => 'required|integer',
            'items.*.variant_id' => 'required|integer',
            'items.*.name' => 'required|string',
            'items.*.name_variant' => 'nullable|string', // Cho phép chuỗi rỗng
            'items.*.quantity' => 'required|integer',
            'items.*.price' => 'required|numeric',
            'items.*.price_variant' => 'required|numeric',
            'items.*.quantity_variant' => 'required|integer',
        ]);

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

            // Tạo yêu cầu hoàn tiền
            $refundId = Refund::create([
                'order_id' => $request->order_id,
                'user_id' => Auth::id(),
                'total_amount' => $request->total_amount,
                'bank_account' => $request->bank_account,
                'user_bank_name' => $request->user_bank_name,
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
                    'name_variant' => $item['name_variant'] ?? 'N/A', // Gán mặc định 'N/A' nếu null
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

            DB::commit();
            return response()->json(['status' => 'success', 'refund_id' => $refundId]);
        } catch (\Exception $e) {
            DB::rollBack();
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
        $request->validate([
            'status' => 'required|in:pending,receiving,completed,rejected,failed,cancel',
            'admin_reason' => 'nullable|string',
            'is_send_money' => 'required|boolean',
        ]);

        try {
            $refund = Refund::findOrFail($id);

            // Định nghĩa các trạng thái cuối (không thể quay lại)
            $finalStatuses = ['completed', 'rejected', 'failed', 'cancel'];

            // Kiểm tra nếu trạng thái hiện tại là trạng thái cuối
            if (in_array($refund->status, $finalStatuses)) {
                return redirect()->back()->with('error', 'Không thể cập nhật trạng thái vì yêu cầu hoàn tiền đã ở trạng thái cuối: ' . $refund->status);
            }

            // Định nghĩa thứ tự trạng thái hợp lệ (pending -> receiving -> completed/rejected/failed/cancel)
            $validTransitions = [
                'pending' => ['receiving', 'rejected', 'failed', 'cancel'],
                'receiving' => ['completed', 'rejected', 'failed', 'cancel'],
            ];

            // Kiểm tra nếu trạng thái mới không hợp lệ
            if (isset($validTransitions[$refund->status]) && !in_array($request->status, $validTransitions[$refund->status])) {
                return redirect()->back()->with('error', 'Không thể chuyển từ trạng thái ' . $refund->status . ' sang trạng thái ' . $request->status);
            }

            // Cập nhật trạng thái yêu cầu hoàn tiền
            $refund->update([
                'status' => $request->status,
                'admin_reason' => $request->admin_reason,
                'is_send_money' => $request->is_send_money,
                'updated_at' => now(),
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
            }

            return redirect()->route('admin.refunds.index')->with('success', 'Cập nhật yêu cầu hoàn tiền thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi khi cập nhật: ' . $e->getMessage());
        }
    }
}
