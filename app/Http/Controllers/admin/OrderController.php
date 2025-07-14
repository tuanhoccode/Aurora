<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use App\Models\OrderOrderStatus;
use App\Models\OrderStatusHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('items')->latest()->paginate(10);
        return view('admin.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with('items')->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatusForm(Order $order)
    {
        $statuses = OrderStatus::all();
        $currentStatus = $order->statusHistory()->where('is_current', true)->first();
        return view('admin.orders.update-status', compact('order', 'statuses', 'currentStatus'));
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            // 1. Validate dữ liệu
            $request->validate([
                'order_status_id' => 'required|integer|exists:order_statuses,id',
                'is_paid' => 'required|boolean',
                'note' => 'nullable|string|max:255',
            ]);

            // 2. Lấy đơn hàng
            $order = Order::findOrFail($id);

            // 3. Danh sách trạng thái hợp lệ
            $validTransitions = [
                1 => [2, 8],       // Chờ xác nhận → Chờ gửi hàng, Đã hủy
                2 => [9, 8],       // Chờ gửi hàng → Đã gửi hàng, Đã hủy
                9 => [3, 8],       // Đã gửi hàng → Đang giao, Đã hủy
                3 => [4, 5, 8],    // Đang giao → Giao hàng thành công, Chờ trả hàng, Đã hủy
                5 => [6, 8],       // Chờ trả hàng → Đã trả hàng, Đã hủy
                6 => [7],          // Đã trả hàng → Hoàn tiền
                4 => [7],          // Giao hàng thành công → Hoàn tiền
                7 => [],           // Hoàn tiền → Dừng
                8 => []            // Đã hủy → Dừng
            ];

            // 4. Trạng thái hiện tại
            $currentStatus = $order->statusHistory()->where('is_current', true)->first();
            $from = $currentStatus?->order_status_id;
            $to = $request->order_status_id;

            // 5. Kiểm tra hợp lệ
            if ($from !== null && !in_array($to, $validTransitions[$from] ?? [])) {
                $currentName = OrderStatus::find($from)?->name ?? 'Không rõ';
                $targetName = OrderStatus::find($to)?->name ?? 'Không rõ';
                return redirect()->back()
                    ->with('error', "Không thể chuyển từ '$currentName' sang '$targetName'!");
            }

            // 6. Bắt đầu transaction
            DB::beginTransaction();

            // 7. Đánh dấu trạng thái hiện tại là không còn nữa
            if ($currentStatus) {
                $currentStatus->update(['is_current' => false]);
            }

            // 8. Tự động đánh dấu đã thanh toán nếu trạng thái là "Giao hàng thành công"
            $isPaid = $to == 4 ? true : $request->is_paid;

            // 9. Chuẩn bị note cho cả Order và OrderStatusHistory
            $note = $request->note ?? 'Chuyển trạng thái: ' . OrderStatus::find($to)?->name;

            // 10. Thêm bản ghi mới vào lịch sử
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'order_status_id' => $to,
                'modifier_id' => Auth::id(),
                'note' => $note,
                'is_current' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 11. Cập nhật trạng thái và note trong bảng Order
            $order->update([
                'order_status_id' => $to,
                'is_paid' => $isPaid,
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

            return redirect()->route('admin.orders.show', $order->id)
                ->with('success', 'Cập nhật trạng thái đơn hàng thành công!');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Cập nhật trạng thái thất bại', [
                'order_id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Lỗi khi cập nhật trạng thái đơn hàng!');
        }
    }
}