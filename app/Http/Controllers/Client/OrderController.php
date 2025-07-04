<?php
namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
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
}
