<?php
namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Models\User;
class OrderController extends Controller
{
    public function index()
    {
       
        return view('client.orders.index');
    }

    public function show(Order $order)
    {
        
        return view('client.orders.show');
    }
}
