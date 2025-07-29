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
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with([
                'items.product',
                'currentOrderStatus.status',
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

    public function cancel(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Không có quyền hủy đơn hàng này');
        }

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

        $validTransitions = [
            1 => [2, $cancelStatusId],
            2 => [9, $cancelStatusId],
            9 => [3, $cancelStatusId],
            3 => [4, 5, $cancelStatusId],
            5 => [6, $cancelStatusId],
            6 => [7],
            4 => [7],
            7 => [],
            $cancelStatusId => [],
        ];

        if (!isset($validTransitions[$currentStatusId]) || !in_array($cancelStatusId, $validTransitions[$currentStatusId])) {
            return redirect()->back()->with('error', 'Không thể hủy đơn hàng ở trạng thái hiện tại!');
        }

        DB::transaction(function () use ($order, $cancelStatusId, $currentStatusRecord) {
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

            DB::table('order_order_status')
                ->where('order_id', $currentStatusRecord->order_id)
                ->where('order_status_id', $currentStatusRecord->order_status_id)
                ->where('is_current', 1)
                ->update(['is_current' => 0]);

            OrderStatusHistory::where('order_id', $order->id)
                ->where('is_current', 1)
                ->update(['is_current' => 0]);

            OrderOrderStatus::create([
                'order_id' => $order->id,
                'order_status_id' => $cancelStatusId,
                'modified_by' => Auth::id(),
                'note' => 'Khách hàng tự hủy đơn',
                'created_at' => now(),
                'updated_at' => now(),
                'is_current' => 1,
            ]);

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'order_status_id' => $cancelStatusId,
                'modifier_id' => Auth::id(),
                'note' => 'Khách hàng tự hủy đơn',
                'is_current' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        $order->update([
            'note' => 'Khách hàng tự hủy đơn',
        ]);

        return redirect()->route('client.orders.tracking', ['order' => $order->id])
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
