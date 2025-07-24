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
        $cart = Cart::with([
            'items.product.categories',
            'items.productVariant.attributeValues.attribute'
        ])->where('user_id', $userId)->first();

        $cartItems = $cart ? $cart->items : collect();
        foreach ($cartItems as $item) {
            $product = $item->product;
            if ($product && ($product->stock < 1)) {
                $item->relatedProducts = $product->relatedProducts(10);
            }
        }
        /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\CartItem[] $cartItems */
        $cartTotal = $cartItems->sum(function($item) {
            return $item->price_at_time * $item->quantity;
        });
        $discount = 0;
        $shipping = $cartTotal >= 500000 ? 0 : ($cartTotal > 0 ? 30000 : 0);
        // Tổng cộng
        $total = $cartTotal - $discount + $shipping;
        return view('client.shopping-cart.index', compact('cartItems', 'cartTotal', 'discount', 'shipping', 'total'));
    }

    /**
     * Thêm sản phẩm vào giỏ hàng
     */
    public function addToCart(Request $request)
    {
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng!'
                ], 401);
            }
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng!');
        }

        $userId = Auth::id();
        $cart = Cart::firstOrCreate(
            ['user_id' => $userId, 'status' => 'pending'],
            ['created_at' => now(), 'updated_at' => now()]
        );

        $quantity = (int) $request->input('quantity', 1);
        if ($quantity < 1) $quantity = 1;

        $product = \App\Models\Product::find($request->product_id);
        if (!$product) {
            $msg = 'Không tìm thấy sản phẩm!';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 404);
            }
            return redirect()->back()->with('error', $msg);
        }

        if ($product->type === 'variant') {
            // Sản phẩm có biến thể, bắt buộc phải có product_variant_id hợp lệ
            if (!$request->product_variant_id) {
                $msg = 'Vui lòng phân loại hàng!';
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 400);
                }
                return redirect()->back()->with('error', $msg);
            }
            $variant = \App\Models\ProductVariant::find($request->product_variant_id);
            if (!$variant) {
                $msg = 'Không tìm thấy biến thể phù hợp!';
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 404);
                }
                return redirect()->back()->with('error', $msg);
            }
            if ($variant->stock < $quantity) {
                $msg = 'Chỉ còn '.$variant->stock.' sản phẩm trong kho!';
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 400);
                }
                return redirect()->back()->with('error', $msg);
            }
        } else {
            if ($product->stock < $quantity) {
                $msg = 'Chỉ còn '.$product->stock.' sản phẩm trong kho!';
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 400);
                }
                return redirect()->back()->with('error', $msg);
            }
        }

        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $request->product_id)
            ->where('product_variant_id', $request->product_variant_id)
            ->first();

        $currentQtyInCart = $item ? $item->quantity : 0;
        $totalQty = $currentQtyInCart + $quantity;
        if ($product->type === 'variant') {
            $variant = \App\Models\ProductVariant::find($request->product_variant_id);
            if ($variant) {
                if ($currentQtyInCart >= $variant->stock) {
                    $msg = 'Bạn đã có đủ số lượng sản phẩm này trong giỏ hàng.';
                    if ($request->expectsJson()) {
                        return response()->json(['success' => false, 'message' => $msg], 400);
                    }
                    return redirect()->back()->with('error', $msg);
                }
                if ($totalQty > $variant->stock) {
                    $msg = 'Bạn đã có đủ số lượng sản phẩm này trong giỏ hàng.';
                    if ($request->expectsJson()) {
                        return response()->json(['success' => false, 'message' => $msg], 400);
                    }
                    return redirect()->back()->with('error', $msg);
                }
            }
        } else {
            if ($currentQtyInCart >= $product->stock) {
                $msg = 'Bạn đã có đủ số lượng sản phẩm này trong giỏ hàng.';
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 400);
                }
                return redirect()->back()->with('error', $msg);
            }
            if ($totalQty > $product->stock) {
                $msg = 'Bạn đã có đủ số lượng sản phẩm này trong giỏ hàng.';
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 400);
                }
                return redirect()->back()->with('error', $msg);
            }
        }

        if ($item) {
            $item->quantity = $totalQty;
            $item->save();
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $request->product_id,
                'product_variant_id' => $request->product_variant_id,
                'quantity' => $quantity,
                'price_at_time' => $request->price,
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã thêm sản phẩm vào giỏ hàng!'
            ]);
        }
        return redirect()->back()->with('success', 'Đã thêm sản phẩm vào giỏ hàng!');
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng
     */
    public function removeFromCart($itemId)
    {
        if (!Auth::check()) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng đăng nhập!'
                ], 401);
            }
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập!');
        }

        $userId = Auth::id();
        $cart = Cart::where('user_id', $userId)->where('status', 'pending')->first();
        
        if ($cart) {
            $item = CartItem::where('cart_id', $cart->id)->where('id', $itemId)->first();
            if ($item) {
                $item->delete();
                
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Đã xóa sản phẩm khỏi giỏ hàng!',
                        'product_id' => $item->product_id,
                        'product_variant_id' => $item->product_variant_id,
                    ]);
                }
                // SỬA: luôn redirect về trang giỏ hàng
                return redirect()->route('shopping-cart.index')->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng!');
            }
        }
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm!'
            ], 404);
        }
        // SỬA: luôn redirect về trang giỏ hàng
        return redirect()->route('shopping-cart.index')->with('error', 'Không tìm thấy sản phẩm!');
    }

    /**
     * Xóa nhiều sản phẩm khỏi giỏ hàng
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids) || empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Không có sản phẩm nào được chọn để xóa!'
            ], 400);
        }
        $userId = Auth::id();
        $cart = Cart::where('user_id', $userId)->where('status', 'pending')->first();
        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy giỏ hàng!'
            ], 404);
        }
        $deleted = $cart->items()->whereIn('id', $ids)->delete();
        return response()->json([
            'success' => true,
            'deleted' => $deleted,
            'message' => 'Đã xóa thành công ' . $deleted . ' sản phẩm khỏi giỏ hàng!'
        ]);
    }

    /**
     * Cập nhật số lượng sản phẩm trong giỏ hàng
     */
    public function update(Request $request, $itemId)
    {
        $item = CartItem::find($itemId);
        if (!$item) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy sản phẩm trong giỏ hàng!'
                ], 404);
            }
            return redirect()->back()->with('error', 'Không tìm thấy sản phẩm trong giỏ hàng!');
        }
        $newQty = (int) $request->input('quantity');
        if ($newQty < 1) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Số lượng không hợp lệ!'
                ], 400);
            }
            return redirect()->back()->with('error', 'Số lượng không hợp lệ!');
        }
        // Kiểm tra tồn kho
        $stock = null;
        $productName = '';
        if ($item->product_variant_id) {
            $variant = $item->productVariant;
            if (!$variant) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Biến thể sản phẩm không tồn tại!'
                    ], 404);
                }
                return redirect()->back()->with('error', 'Biến thể sản phẩm không tồn tại!');
            }
            $stock = $variant->stock;
            $productName = $variant->sku ?? ($item->product->name ?? 'Sản phẩm');
        } else {
            $product = $item->product;
            if (!$product) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Sản phẩm không tồn tại!'
                    ], 404);
                }
                return redirect()->back()->with('error', 'Sản phẩm không tồn tại!');
            }
            $stock = $product->stock;
            $productName = $product->name;
        }
        if ($stock === null) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không xác định được tồn kho sản phẩm!'
                ], 400);
            }
            return redirect()->back()->with('error', 'Không xác định được tồn kho sản phẩm!');
        }
        if ($stock < 1) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sản phẩm này đã hết hàng!'
                ], 400);
            }
            return redirect()->back()->with('error', 'Sản phẩm này đã hết hàng!');
        }
        if ($newQty > $stock) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ còn ' . $stock . ' sản phẩm trong kho!'
                ], 400);
            }
            return redirect()->back()->with('error', 'Chỉ còn ' . $stock . ' sản phẩm trong kho!');
        }
        $item->quantity = $newQty;
        $item->save();
        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('shopping-cart.index')->with('success', 'Cập nhật số lượng thành công!');
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
     * Lấy số lượng sản phẩm trong giỏ hàng
     */
    public function getCartCount()
    {
        if (!Auth::check()) {
            return response()->json(['count' => 0]);
        }

        $userId = Auth::id();
        $cart = Cart::where('user_id', $userId)
            ->where('status', 'pending')
            ->with('items')
            ->first();

        $count = $cart ? $cart->items->count() : 0;

        return response()->json(['count' => $count]);
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