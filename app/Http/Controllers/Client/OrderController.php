<?php
namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with(['currentStatus.status', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('client.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Không có quyền truy cập đơn hàng này');
        }

        $order->load([
            'items.product',
            'items.productVariant.attributes.attribute',
            'items.productVariant.attributes.value',
            'currentStatus.status'
        ]);

        return view('client.orders.show', compact('order'));
    }
}