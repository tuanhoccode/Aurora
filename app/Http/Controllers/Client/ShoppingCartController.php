<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;
class ShoppingCartController extends Controller
{
    /**
     * Hiển thị trang giỏ hàng
     */
    public function index()
    {
        $userId = Auth::id();
        $cart = Cart::where('user_id', $userId)
            ->where('status', 'pending')
            ->with(['items.product', 'items.productVariant'])
            ->first();

        $cartItems = $cart ? $cart->items : collect();
        /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\CartItem[] $cartItems */
        $cartTotal = $cartItems->sum(function($item) {
            return $item->price_at_time * $item->quantity;
        });

        return view('client.shopping-cart.index', compact('cartItems', 'cartTotal'));
    }

    /**
     * Thêm sản phẩm vào giỏ hàng
     */
    public function addToCart(Request $request)
    {
        $userId = Auth::id();
        $cart = Cart::firstOrCreate(
            ['user_id' => $userId, 'status' => 'pending'],
            ['created_at' => now(), 'updated_at' => now()]
        );

        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $request->product_id)
            ->where('product_variant_id', $request->product_variant_id)
            ->first();

        if ($item) {
            $item->quantity += $request->quantity;
            $item->save();
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $request->product_id,
                'product_variant_id' => $request->product_variant_id,
                'quantity' => $request->quantity,
                'price_at_time' => $request->price,
            ]);
        }

        return redirect()->back()->with('success', 'Đã thêm vào giỏ hàng!');
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng
     */
    public function removeFromCart($itemId)
    {
        $userId = Auth::id();
        $cart = Cart::where('user_id', $userId)->where('status', 'pending')->first();
        if ($cart) {
            CartItem::where('cart_id', $cart->id)->where('id', $itemId)->delete();
        }
        return redirect()->back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng!');
    }

    /**
     * Cập nhật số lượng sản phẩm trong giỏ hàng
     */
    public function updateQuantity(Request $request, $itemId)
    {
        $userId = Auth::id();
        $cart = Cart::where('user_id', $userId)->where('status', 'pending')->first();
        if ($cart) {
            $item = CartItem::where('cart_id', $cart->id)->where('id', $itemId)->first();
            if ($item) {
                $item->quantity = $request->quantity;
                $item->save();
            }
        }
        return redirect()->back()->with('success', 'Đã cập nhật số lượng!');
    }

    /**
     * Hiển thị trang thanh toán
     */
    public function checkout()
    {
        return view('client.shopping-cart.checkout');
    }

    /**
     * Hiển thị mini cart
     */
    public function miniCart()
    {
        return view('client.shopping-cart.mini-cart');
    }

    /**
     * Xử lý đặt hàng
     */
    public function processCheckout(Request $request)
    {
        // Validate dữ liệu
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'shipping_method' => 'required|in:standard,express,free',
            'payment_method' => 'required|in:cod,bank_transfer,momo',
            'cart_data' => 'required|json'
        ]);

        try {
            // Lấy dữ liệu giỏ hàng
            $cartData = json_decode($request->cart_data, true);
            
            if (empty($cartData)) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Giỏ hàng trống!'
                    ]);
                }
                return redirect()->back()->with('error', 'Giỏ hàng trống!');
            }

            // Tính toán tổng tiền
            $subtotal = collect($cartData)->sum(function($item) {
                return $item['price'] * $item['quantity'];
            });

            $shippingCost = $this->calculateShippingCost($request->shipping_method, $subtotal);
            $tax = $subtotal * 0.1; // 10% thuế
            $total = $subtotal + $shippingCost + $tax;

            // Tạo đơn hàng (ở đây bạn sẽ tích hợp với database)
            $order = [
                'order_number' => 'AUR-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'customer_name' => $request->first_name . ' ' . $request->last_name,
                'customer_email' => $request->email,
                'customer_phone' => $request->phone,
                'shipping_address' => $request->address,
                'shipping_city' => $request->city,
                'shipping_district' => $request->district,
                'shipping_postal_code' => $request->postal_code ?? '',
                'shipping_method' => $request->shipping_method,
                'payment_method' => $request->payment_method,
                'order_notes' => $request->order_notes ?? '',
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'tax_amount' => $tax,
                'total_amount' => $total,
                'status' => 'pending',
                'created_at' => now(),
                'items' => $cartData
            ];

            // Lưu đơn hàng vào session để hiển thị ở trang thành công
            Session::put('last_order', $order);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đặt hàng thành công!',
                    'order_id' => $order['order_number'],
                    'redirect_url' => route('shopping-cart.success')
                ]);
            }

            return redirect()->route('shopping-cart.success');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi đặt hàng: ' . $e->getMessage()
                ]);
            }
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi đặt hàng: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị trang đặt hàng thành công
     */
    public function orderSuccess()
    {
        $order = Session::get('last_order');
        
        if (!$order) {
            return redirect()->route('home')->with('error', 'Không tìm thấy thông tin đơn hàng!');
        }

        // Xóa session sau khi hiển thị
        Session::forget('last_order');

        return view('client.shopping-cart.order-success', compact('order'));
    }

    /**
     * Tính phí vận chuyển
     */
    private function calculateShippingCost($method, $subtotal)
    {
        switch ($method) {
            case 'free':
                return $subtotal >= 500000 ? 0 : 30000;
            case 'express':
                return 50000;
            case 'standard':
            default:
                return 30000;
        }
    }
} 