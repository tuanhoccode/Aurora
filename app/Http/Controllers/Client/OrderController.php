<?php
namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Order;
use Auth;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with([
                'status', 
                'items.product',
                'currentStatus.status',
                'payment'
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('client.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Không có quyền truy cập đơn hàng này');
        }

        $order->load([
            'items.product',
            'items.variant.attributes.attribute',
            'items.variant.attributes.value',
            'status',
            'payment',
            'currentStatus.status'
        ]);

        return view('client.orders.show', compact('order'));
    }

    public function cancel(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Không có quyền hủy đơn hàng này');
        }

        // Kiểm tra đơn hàng đã hủy chưa
        if ($order->cancellation_status === 'approved') {
            return redirect()->back()->with('error', 'Đơn hàng đã được hủy trước đó');
        }

        // Kiểm tra trạng thái đơn hàng
        $currentStatus = optional($order->currentStatus);
        $status = optional($currentStatus)->status;
        
        if (!$status || $status->name !== 'Chờ xác nhận') {
            return redirect()->back()->with('error', 'Đơn hàng không thể hủy vì không ở trạng thái "Chờ xác nhận"');
        }

        // Lưu thông tin hủy đơn
        $order->update([
            'cancellation_reason' => $request->reason,
            'cancellation_note' => $request->note,
            'cancellation_date' => now(),
            'cancellation_by' => Auth::id(),
            'cancellation_status' => 'pending'
        ]);

        // Thêm lịch sử trạng thái
        $order->statusHistory()->create([
            'status_id' => 8, // ID của trạng thái 'Đã hủy' trong bảng order_status
            'is_current' => true,
            'note' => 'Đã hủy đơn hàng: ' . $request->reason
        ]);

        return redirect()->back()->with('success', 'Đã gửi yêu cầu hủy đơn hàng. Đơn hàng sẽ được xử lý sau khi được duyệt.');
    }
}