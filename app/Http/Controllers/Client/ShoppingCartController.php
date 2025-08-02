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
        if (!Auth::check()) {
            return view('client.shopping-cart.index', [
                'cartItems' => collect(),
                'cartTotal' => 0,
                'discount' => 0,
                'shipping' => 0,
                'total' => 0
            ]);
        }

        $userId = Auth::id();
        $cartItems = collect();
        
        // Lấy giỏ hàng từ session
        $sessionCart = session("cart_{$userId}", []);
        
        if (!empty($sessionCart)) {
            // Lấy tất cả product IDs và variant IDs
            $productIds = collect($sessionCart)->pluck('product_id')->unique()->toArray();
            $variantIds = collect($sessionCart)->pluck('product_variant_id')->filter()->unique()->toArray();
            
            // Tối ưu query - lấy tất cả products và variants một lần
            $products = \App\Models\Product::select('id', 'name', 'slug', 'price', 'sale_price', 'thumbnail', 'stock', 'type', 'is_active')
                ->with(['categories:id,name'])
                ->whereIn('id', $productIds)
                ->get()
                ->keyBy('id');
                
            $variants = collect();
            if (!empty($variantIds)) {
                $variants = \App\Models\ProductVariant::select('id', 'product_id', 'sku', 'stock', 'regular_price', 'sale_price', 'img')
                    ->with(['attributeValues:id,attribute_id,value', 'attributeValues.attribute:id,name'])
                    ->whereIn('id', $variantIds)
                    ->get()
                    ->keyBy('id');
            }
            
            foreach ($sessionCart as $itemData) {
                $product = $products->get($itemData['product_id']);
                if (!$product) continue;
                
                $variant = null;
                if (isset($itemData['product_variant_id']) && $itemData['product_variant_id']) {
                    $variant = $variants->get($itemData['product_variant_id']);
                }
                
                // Tạo object giả để tương thích với view
                $item = (object) [
                    'id' => $itemData['id'] ?? uniqid(),
                    'product_id' => $product->id,
                    'product_variant_id' => $variant ? $variant->id : null,
                    'quantity' => $itemData['quantity'],
                    'product' => $product,
                    'productVariant' => $variant,
                    'price_at_time' => $itemData['price_at_time'] ?? null,
                ];
                
                // Kiểm tra trạng thái sản phẩm
                if (!$product->is_active) {
                    $item->is_discontinued = true;
                } else {
                    $item->is_discontinued = false;
                }
                
                // Kiểm tra giá hiện tại của sản phẩm
                $currentPrice = $variant ? $variant->current_price : $product->current_price;
                
                // Lưu giá hiện tại vào item để sử dụng trong view
                $item->current_price = $currentPrice;
                
                // Nếu có thuộc tính price_at_time, so sánh để phát hiện thay đổi
                if (isset($item->price_at_time) && $item->price_at_time != $currentPrice) {
                    // Đánh dấu sản phẩm có giá thay đổi
                    $item->price_changed = true;
                    $item->old_price = $item->price_at_time;
                }
                
                // Cập nhật price_at_time cho lần so sánh tiếp theo
                $item->price_at_time = $currentPrice;
                
                $cartItems->push($item);
            }
        }
        
        $cartTotal = $cartItems->sum(function($item) {
            // Chỉ tính tổng cho sản phẩm còn hoạt động
            if (!$item->is_discontinued) {
                return $item->current_price * $item->quantity;
            }
            return 0;
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

        // Kiểm tra sản phẩm có đang hoạt động không
        if (!$product->is_active) {
            $msg = 'Sản phẩm này đã ngừng kinh doanh!';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 400);
            }
            return redirect()->back()->with('error', $msg);
        }

        if ($product->type === 'variant') {
            // Sản phẩm có biến thể, bắt buộc phải có product_variant_id hợp lệ
            if (!$request->product_variant_id) {
                $msg = 'Vui lòng chọn đầy đủ màu và kích cỡ!';
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

        // Lấy giỏ hàng từ session
        $sessionCart = session("cart_{$userId}", []);
        
        // Kiểm tra sản phẩm đã có trong giỏ hàng chưa
        $existingItem = null;
        foreach ($sessionCart as $key => $item) {
            if ($item['product_id'] == $request->product_id && 
                $item['product_variant_id'] == $request->product_variant_id) {
                $existingItem = $key;
                break;
            }
        }

        if ($existingItem !== null) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sản phẩm này đã có trong giỏ hàng!'
                ], 400);
            }
            return redirect()->back()->with('error', 'Sản phẩm này đã có trong giỏ hàng!');
        }

        // Thêm sản phẩm vào giỏ hàng session
        $currentPrice = $request->product_variant_id ? 
            \App\Models\ProductVariant::find($request->product_variant_id)->current_price : 
            $product->current_price;
            
        $sessionCart[] = [
            'id' => uniqid(),
            'product_id' => $request->product_id,
            'product_variant_id' => $request->product_variant_id,
            'quantity' => $quantity,
            'price_at_time' => $currentPrice,
        ];

        session(["cart_{$userId}" => $sessionCart]);

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
        $sessionCart = session("cart_{$userId}", []);
        
        // Tìm và xóa item
        foreach ($sessionCart as $key => $item) {
            if ($item['id'] == $itemId) {
                $removedItem = $sessionCart[$key];
                unset($sessionCart[$key]);
                session(["cart_{$userId}" => array_values($sessionCart)]);
                
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Đã xóa sản phẩm khỏi giỏ hàng!',
                        'product_id' => $removedItem['product_id'],
                        'product_variant_id' => $removedItem['product_variant_id'],
                    ]);
                }
                return redirect()->route('shopping-cart.index')->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng!');
            }
        }
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm!'
            ], 404);
        }
        return redirect()->route('shopping-cart.index')->with('error', 'Không tìm thấy sản phẩm!');
    }

    /**
     * Xóa nhiều sản phẩm khỏi giỏ hàng
     */
    public function bulkDelete(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập!'
            ], 401);
        }

        $ids = $request->input('ids', []);
        if (!is_array($ids) || empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Không có sản phẩm nào được chọn để xóa!'
            ], 400);
        }
        
        $userId = Auth::id();
        $sessionCart = session("cart_{$userId}", []);
        
        $deleted = 0;
        foreach ($sessionCart as $key => $item) {
            if (in_array($item['id'], $ids)) {
                unset($sessionCart[$key]);
                $deleted++;
            }
        }
        
        session(["cart_{$userId}" => array_values($sessionCart)]);
        
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
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng đăng nhập!'
                ], 401);
            }
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập!');
        }

        $userId = Auth::id();
        $sessionCart = session("cart_{$userId}", []);
        
        // Tìm item trong session
        $itemIndex = null;
        $item = null;
        foreach ($sessionCart as $key => $cartItem) {
            if ($cartItem['id'] == $itemId) {
                $itemIndex = $key;
                $item = $cartItem;
                break;
            }
        }
        
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
        
        if ($item['product_variant_id']) {
            $variant = \App\Models\ProductVariant::find($item['product_variant_id']);
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
            $productName = $variant->sku ?? 'Sản phẩm';
        } else {
            $product = \App\Models\Product::find($item['product_id']);
            if (!$product) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Sản phẩm không tồn tại!'
                    ], 404);
                }
                return redirect()->back()->with('error', 'Sản phẩm không tồn tại!');
            }
            
            // Kiểm tra sản phẩm có đang hoạt động không
            if (!$product->is_active) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Sản phẩm này đã ngừng kinh doanh!'
                    ], 400);
                }
                return redirect()->back()->with('error', 'Sản phẩm này đã ngừng kinh doanh!');
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
        
        // Cập nhật số lượng trong session
        $sessionCart[$itemIndex]['quantity'] = $newQty;
        session(["cart_{$userId}" => $sessionCart]);
        
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
        if (!Auth::check()) {
            return view('client.shopping-cart.mini-cart', ['cartItems' => collect()]);
        }

        $userId = Auth::id();
        $sessionCart = session("cart_{$userId}", []);
        $cartItems = collect();
        
        foreach ($sessionCart as $itemData) {
            $product = \App\Models\Product::find($itemData['product_id']);
            if (!$product) continue;
            
            $variant = null;
            if (isset($itemData['product_variant_id']) && $itemData['product_variant_id']) {
                $variant = \App\Models\ProductVariant::with('attributeValues.attribute')->find($itemData['product_variant_id']);
            }
            
            $item = (object) [
                'id' => $itemData['id'],
                'product_id' => $product->id,
                'product_variant_id' => $variant ? $variant->id : null,
                'quantity' => $itemData['quantity'],
                'product' => $product,
                'productVariant' => $variant,
            ];
            
            $cartItems->push($item);
        }

        return view('client.shopping-cart.mini-cart', compact('cartItems'));
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
        $sessionCart = session("cart_{$userId}", []);
        $count = count($sessionCart);

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

    /**
     * Ẩn thông báo giá thay đổi cho sản phẩm
     */
    public function dismissPriceChange($itemId)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập!'
            ], 401);
        }

        $userId = Auth::id();
        $sessionCart = session("cart_{$userId}", []);
        
        // Tìm item trong session
        $found = false;
        foreach ($sessionCart as $item) {
            if ($item['id'] == $itemId) {
                $found = true;
                break;
            }
        }

        if (!$found) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm trong giỏ hàng!'
            ], 404);
        }

        // Có thể lưu trạng thái đã xem vào session nếu cần
        // Hiện tại chỉ trả về success để frontend ẩn thông báo
        return response()->json([
            'success' => true,
            'message' => 'Đã ẩn thông báo giá thay đổi!'
        ]);
    }
} 