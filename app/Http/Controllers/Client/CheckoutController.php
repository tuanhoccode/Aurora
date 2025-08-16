<?php

namespace App\Http\Controllers\Client;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\OrderItem;
use App\Models\PaymentLog;
use App\Models\UserAddress;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use App\Models\OrderOrderStatus;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\Client\AddressFormRequest;
use App\Http\Requests\Client\SaveAddressRequest;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        try {
            if (!Auth::check()) {
                \Log::warning('User not authenticated', ['user_id' => Auth::id(), 'session_id' => Session::getId()]);
                return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục!');
            }

            $user = Auth::user();
            $cart = Cart::where('user_id', $user->id)
                ->with(['items' => function ($query) {
                    $query->with(['product', 'productVariant']);
                }])
                ->first();

            if (!$cart) {
                \Log::warning('Cart not found', ['user_id' => $user->id, 'session_id' => Session::getId()]);
                return redirect()->route('shopping-cart.index')->with('error', 'Giỏ hàng không tồn tại!');
            }

            // Lấy selected_items từ session, sử dụng query string như dự phòng
            $selectedItems = session('selected_items', []);
            if ($request->query('selected_items')) {
                try {
                    $decodedItems = json_decode($request->query('selected_items'), true);
                    if (is_array($decodedItems) && !empty($decodedItems)) {
                        $selectedItems = array_map('intval', $decodedItems);
                        session(['selected_items' => $selectedItems]);
                        \Log::info('Selected items updated from query', ['selected_items' => $selectedItems, 'session_id' => Session::getId()]);
                    } else {
                        \Log::warning('Invalid selected_items format', ['query' => $request->query('selected_items'), 'session_id' => Session::getId()]);
                    }
                } catch (\Exception $e) {
                    \Log::error('Error decoding selected_items', [
                        'query' => $request->query('selected_items'),
                        'error' => $e->getMessage(),
                        'session_id' => Session::getId()
                    ]);
                }
            }

            if (!is_array($selectedItems) || empty($selectedItems)) {
                \Log::warning('No valid selected items', [
                    'selected_items' => $selectedItems,
                    'query' => $request->query('selected_items'),
                    'session_id' => Session::getId()
                ]);
                return redirect()->route('shopping-cart.index')->with('error', 'Vui lòng chọn ít nhất một sản phẩm để thanh toán!');
            }

            $cartItems = $cart->items->filter(function ($item) use ($selectedItems) {
                return in_array($item->id, $selectedItems);
            })->filter(function ($item) {
                $stock = $item->productVariant ? $item->productVariant->stock : $item->product->stock;
                return $stock >= $item->quantity;
            });

            if ($cartItems->isEmpty()) {
                \Log::warning('No valid cart items after filtering', [
                    'selected_items' => $selectedItems,
                    'user_id' => $user->id,
                    'session_id' => Session::getId()
                ]);
                session()->forget('selected_items');
                return redirect()->route('shopping-cart.index')->with('error', 'Các sản phẩm được chọn không hợp lệ hoặc không đủ số lượng trong kho!');
            }

            $cartTotal = $cartItems->sum(function ($item) {
                $price = $item->price_at_time ?? ($item->productVariant ? $item->productVariant->price : $item->product->price) ?? null;
                if ($price === null) {
                    \Log::error('Price not found for cart item', [
                        'item_id' => $item->id,
                        'product_id' => $item->product_id,
                        'variant_id' => $item->product_variant_id,
                        'session_id' => Session::getId()
                    ]);
                    throw new \Exception('Không thể tính giá sản phẩm: ' . $item->product->name);
                }
                $quantity = $item->quantity ?? 0;
                \Log::debug('Cart Item Calculation', [
                    'product_id' => $item->product_id,
                    'variant_id' => $item->product_variant_id,
                    'price' => $price,
                    'quantity' => $quantity,
                    'subtotal' => $price * $quantity,
                    'session_id' => Session::getId()
                ]);
                return $price * $quantity;
            });

            $addresses = UserAddress::where('user_id', $user->id)->get();
            $defaultAddress = $addresses->where('is_default', 1)->first();

            if (!session('checkout_address_id') && $defaultAddress) {
                session(['checkout_address_id' => $defaultAddress->id]);
            }

            $paymentMethod = session('payment_method', 'cod');
            $shippingType = session('shipping_type', 'thường');
            $shippingFee = $shippingType === 'nhanh' ? 30000 : 16500;
            session([
                'payment_method' => $paymentMethod,
                'shipping_type' => $shippingType,
                'shipping_fee' => $shippingFee
            ]);

            $coupon = session('coupon') ? Coupon::find(session('coupon')->id) : null;
            $discount = 0;
            if ($coupon && $this->isValidCoupon($coupon, $cartTotal)) {
                $discount = $this->calculateDiscount($coupon, $cartTotal);
            } else if ($coupon) {
                \Log::warning('Invalid coupon', ['coupon_id' => $coupon->id, 'cart_total' => $cartTotal, 'session_id' => Session::getId()]);
                session()->forget('coupon');
            }

            \Log::info('Checkout Summary', [
                'user_id' => $user->id,
                'cart_id' => $cart->id,
                'cart_items_count' => $cartItems->count(),
                'cart_total' => $cartTotal,
                'shipping_fee' => $shippingFee,
                'shipping_type' => $shippingType,
                'coupon' => $coupon ? $coupon->toArray() : null,
                'discount' => $discount,
                'selected_items' => $selectedItems,
                'address_id' => session('checkout_address_id'),
                'payment_method' => $paymentMethod,
                'session_id' => Session::getId()
            ]);

            $availableCoupons = Coupon::where('is_active', true)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->where(function ($query) {
                    $query->whereNull('usage_limit')
                        ->orWhereRaw('usage_count < usage_limit');
                })
                ->orderBy('discount_value', 'desc')
                ->get();

            return view('client.checkout', compact(
                'cart',
                'cartItems',
                'cartTotal',
                'addresses',
                'availableCoupons',
                'defaultAddress',
                'shippingFee',
                'user',
                'coupon',
                'discount',
                'shippingType'
            ));
        } catch (\Exception $e) {
            \Log::error('Checkout Error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString(),
                'session_id' => Session::getId()
            ]);
            return redirect()->route('shopping-cart.index')->with('error', 'Đã xảy ra lỗi, vui lòng thử lại!');
        }
    }

    public function update(Request $request)
    {
        try {
            $request->validate([
                'selected_address' => 'nullable|exists:user_addresses,id,user_id,' . Auth::id(),
                'shipping_type' => 'nullable|in:thường,nhanh',
                'payment_method' => 'nullable|in:cod,vnpay',
                'note' => 'nullable|string|max:500',
            ]);

            $shippingFee = $request->shipping_type === 'nhanh' ? 30000 : 16500;
            session([
                'checkout_address_id' => $request->selected_address ?? session('checkout_address_id', null),
                'shipping_type' => $request->shipping_type ?? session('shipping_type', 'thường'),
                'payment_method' => $request->payment_method ?? session('payment_method', 'cod'),
                'note' => $request->note ?? session('note', ''),
                'shipping_fee' => $shippingFee,
            ]);

            if (!session('selected_items')) {
                \Log::warning('No selected items in session, redirecting to cart', ['user_id' => Auth::id(), 'session_id' => Session::getId()]);
                return redirect()->route('shopping-cart.index')->with('error', 'Vui lòng chọn sản phẩm để thanh toán!');
            }

            \Log::info('Checkout updated', [
                'checkout_address_id' => session('checkout_address_id'),
                'shipping_type' => session('shipping_type'),
                'payment_method' => session('payment_method'),
                'note' => session('note'),
                'shipping_fee' => $shippingFee,
                'selected_items' => session('selected_items'),
                'session_id' => Session::getId()
            ]);

            return redirect()->route('checkout')->with('success', 'Cập nhật thông tin thành công!');
        } catch (\Exception $e) {
            \Log::error('Checkout Update Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString(), 'session_id' => Session::getId()]);
            return redirect()->route('checkout')->with('error', 'Đã xảy ra lỗi khi cập nhật, vui lòng thử lại!');
        }
    }

    public function applyCoupon(Request $request)
    {
        try {
            $request->validate([
                'coupon_code' => 'required|string|max:50',
            ]);

            $coupon = Coupon::where('code', $request->coupon_code)
                ->where('is_active', true)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->first();

            $cart = Cart::where('user_id', Auth::id())->first();
            $cartTotal = $cart->items->sum(function ($item) {
                return ($item->price_at_time ?? ($item->productVariant ? $item->productVariant->price : $item->product->price) ?? 0) * ($item->quantity ?? 0);
            });

            if (!$coupon) {
                session()->forget('coupon');
                return redirect()->route('checkout')->with('error', 'Mã giảm giá không hợp lệ hoặc đã hết hạn!');
            }

            if (!$this->isValidCoupon($coupon, $cartTotal)) {
                session()->forget('coupon');
                return redirect()->route('checkout')->with('error', 'Mã giảm giá không áp dụng được cho đơn hàng này!');
            }

            session(['coupon' => $coupon]);
            Log::info('Coupon applied', ['code' => $coupon->code, 'discount_value' => $coupon->discount_value, 'discount_type' => $coupon->discount_type, 'session_id' => Session::getId()]);
            return redirect()->route('checkout')->with('success', 'Áp dụng mã giảm giá thành công!');
        } catch (\Exception $e) {
            Log::error('Apply coupon error: ' . $e->getMessage(), ['session_id' => Session::getId()]);
            return redirect()->route('checkout')->with('error', 'Lỗi khi áp dụng mã giảm giá, vui lòng thử lại!');
        }
    }

    public function applyCouponById(Request $request)
    {
        try {
            $request->validate([
                'coupon_id' => 'required|exists:coupons,id',
            ]);

            $coupon = Coupon::findOrFail($request->coupon_id);

            $cart = Cart::where('user_id', Auth::id())->first();
            $cartTotal = $cart->items->sum(function ($item) {
                return ($item->price_at_time ?? ($item->productVariant ? $item->productVariant->price : $item->product->price) ?? 0) * ($item->quantity ?? 0);
            });

            if (!$this->isValidCoupon($coupon, $cartTotal)) {
                session()->forget('coupon');
                return redirect()->route('checkout')->with('error', 'Mã giảm giá không áp dụng được cho đơn hàng này!');
            }

            session(['coupon' => $coupon]);
            Log::info('Coupon applied by ID', ['coupon_id' => $coupon->id, 'code' => $coupon->code, 'session_id' => Session::getId()]);
            return redirect()->route('checkout')->with('success', 'Áp dụng mã giảm giá thành công!');
        } catch (\Exception $e) {
            Log::error('Apply coupon by ID error: ' . $e->getMessage(), ['session_id' => Session::getId()]);
            return redirect()->route('checkout')->with('error', 'Lỗi khi áp dụng mã giảm giá, vui lòng thử lại!');
        }
    }

    public function removeCoupon(Request $request)
    {
        try {
            session()->forget('coupon');
            Log::info('Coupon removed from session', ['session_id' => Session::getId()]);
            return redirect()->route('checkout')->with('success', 'Đã xóa mã giảm giá!');
        } catch (\Exception $e) {
            Log::error('Remove coupon error: ' . $e->getMessage(), ['session_id' => Session::getId()]);
            return redirect()->route('checkout')->with('error', 'Lỗi khi xóa mã giảm giá, vui lòng thử lại!');
        }
    }

    protected function isValidCoupon($coupon, $cartTotal)
    {
        $isValid = $coupon->is_active &&
            $coupon->start_date <= now() &&
            $coupon->end_date >= now() &&
            ($coupon->usage_limit === null || $coupon->usage_count < $coupon->usage_limit);
        Log::info('Coupon validation', [
            'code' => $coupon->code,
            'is_active' => $coupon->is_active,
            'start_date' => $coupon->start_date,
            'end_date' => $coupon->end_date,
            'usage_limit' => $coupon->usage_limit,
            'usage_count' => $coupon->usage_count,
            'is_valid' => $isValid,
            'session_id' => Session::getId()
        ]);
        return $isValid;
    }

    protected function calculateDiscount($coupon, $cartTotal)
    {
        Log::info('Coupon Debug', [
            'code' => $coupon->code,
            'discount_type' => $coupon->discount_type,
            'discount_value' => $coupon->discount_value,
            'cartTotal' => $cartTotal,
            'session_id' => Session::getId()
        ]);

        if ($coupon->discount_type === 'percent') {
            $discount = $cartTotal * ((float)$coupon->discount_value / 100);
            Log::info('Calculated Discount (percent)', ['discount' => $discount, 'session_id' => Session::getId()]);
            return $discount;
        } else {
            $discount = min((float)$coupon->discount_value, $cartTotal);
            Log::info('Calculated Discount (fix_amount)', ['discount' => $discount, 'session_id' => Session::getId()]);
            return $discount;
        }
    }

    public function createAddress()
    {
        try {
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục!');
            }
            return view('client.address.create');
        } catch (\Exception $e) {
            Log::error('Create address error: ' . $e->getMessage(), ['session_id' => Session::getId()]);
            return redirect()->route('checkout')->with('error', 'Đã xảy ra lỗi, vui lòng thử lại!');
        }
    }

    public function storeAddress(AddressFormRequest $request)
    {
        try {
            $user = Auth::user();
            $data = $request->only([
                'fullname',
                'phone_number',
                'email',
                'province',
                'district',
                'ward',
                'street',
                'address',
                'address_type',
                'is_default',
            ]);
            $data['user_id'] = $user->id;

            if ($request->is_default) {
                UserAddress::where('user_id', $user->id)->update(['is_default' => 0]);
            }

            $address = UserAddress::create($data);
            session(['checkout_address_id' => $address->id]);

            return redirect()->route('checkout')->with('success', 'Địa chỉ đã được thêm thành công!');
        } catch (\Exception $e) {
            Log::error('Store address error: ' . $e->getMessage(), ['session_id' => Session::getId()]);
            return back()->withInput()->with('error', 'Đã xảy ra lỗi khi thêm địa chỉ, vui lòng thử lại!');
        }
    }

    public function editAddress($id = null)
    {
        try {
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục!');
            }

            $user = Auth::user();
            $address = null;
            if ($id) {
                $address = UserAddress::where('id', $id)
                    ->where('user_id', $user->id)
                    ->first();
                if (!$address) {
                    return redirect()->route('checkout')->with('error', 'Địa chỉ không tồn tại hoặc không thuộc về bạn.');
                }
            }

            return view('client.address.edit', compact('user', 'address'));
        } catch (\Exception $e) {
            Log::error('Edit address error: ' . $e->getMessage(), ['session_id' => Session::getId()]);
            return redirect()->route('checkout')->with('error', 'Đã xảy ra lỗi, vui lòng thử lại!');
        }
    }

    public function saveAddress(SaveAddressRequest $request)
    {
        try {
            $user = Auth::user();
            $data = $request->only([
                'fullname',
                'phone_number',
                'email',
                'province',
                'district',
                'ward',
                'street',
                'address',
                'address_type',
                'is_default',
            ]);
            $data['user_id'] = $user->id;

            if ($request->is_default) {
                UserAddress::where('user_id', $user->id)->update(['is_default' => 0]);
            }

            UserAddress::where('id', $request->address_id)
                ->where('user_id', $user->id)
                ->update($data);

            return redirect()->route('checkout')->with('success', 'Địa chỉ đã được cập nhật!');
        } catch (\Exception $e) {
            Log::error('Save address error: ' . $e->getMessage(), ['session_id' => Session::getId()]);
            return back()->withInput()->with('error', 'Đã xảy ra lỗi khi cập nhật địa chỉ, vui lòng thử lại!');
        }
    }

    public function process(Request $request)
    {
        try {
            \Log::info('Starting checkout process', [
                'user_id' => Auth::id(),
                'request_data' => $request->all(),
                'selected_items' => session('selected_items', []),
                'cart_items' => Cart::where('user_id', Auth::id())->with('items')->first()->items->toArray(),
                'session_id' => Session::getId()
            ]);

            // Kiểm tra request lặp lại
            $selectedItems = session('selected_items', []);
            $requestData = $request->only(['shipping_type', 'payment_method', 'address_id', 'note']);
            $requestData['selected_items'] = $selectedItems;
            $requestHash = md5(json_encode($requestData));
            $lastRequestTime = session('last_checkout_time', 0);
            if (session('last_checkout_request') === $requestHash && now()->diffInSeconds(now()->createFromTimestamp($lastRequestTime)) < 30) {
                \Log::warning('Duplicate checkout request detected', [
                    'user_id' => Auth::id(),
                    'request_data' => $requestData,
                    'selected_items' => $selectedItems,
                    'session_id' => Session::getId()
                ]);
                return redirect()->route('checkout')->with('error', 'Yêu cầu thanh toán đã được xử lý, vui lòng không gửi lại!');
            }
            session(['last_checkout_request' => $requestHash, 'last_checkout_time' => now()->timestamp]);

            // Validate input
            $request->validate([
                'address_id' => 'required|exists:user_addresses,id,user_id,' . Auth::id(),
                'shipping_type' => 'required|in:thường,nhanh',
                'payment_method' => 'required|in:cod,vnpay',
                'note' => 'nullable|string',
            ], [
                'address_id.required' => 'Vui lòng chọn hoặc thêm địa chỉ nhận hàng.',
                'address_id.exists' => 'Địa chỉ không hợp lệ.',
                'shipping_type.required' => 'Vui lòng chọn phương thức vận chuyển.',
                'shipping_type.in' => 'Phương thức vận chuyển không hợp lệ.',
                'payment_method.required' => 'Vui lòng chọn phương thức thanh toán.',
                'payment_method.in' => 'Phương thức thanh toán không hợp lệ.',
            ]);

            // Lấy giỏ hàng
            $cart = Cart::where('user_id', Auth::id())->with(['items.product', 'items.productVariant'])->first();
            if (!$cart || $cart->items->isEmpty()) {
                \Log::warning('Cart is empty or not found', ['user_id' => Auth::id(), 'session_id' => Session::getId()]);
                return redirect()->route('shopping-cart.index')->with('error', 'Giỏ hàng trống!');
            }

            // Lấy selected_items từ session
            $selectedItems = session('selected_items', []);
            if (empty($selectedItems)) {
                \Log::warning('No selected items in session', ['user_id' => Auth::id(), 'session_id' => Session::getId()]);
                return redirect()->route('shopping-cart.index')->with('error', 'Vui lòng chọn ít nhất một sản phẩm để thanh toán!');
            }

            // Lọc cart items
            $cartItems = $cart->items->filter(function ($item) use ($selectedItems) {
                return in_array($item->id, $selectedItems);
            })->filter(function ($item) {
                $stock = $item->productVariant ? $item->productVariant->stock : $item->product->stock;
                return $stock >= $item->quantity;
            });

            if ($cartItems->isEmpty()) {
                \Log::warning('No valid cart items after filtering', ['selected_items' => $selectedItems, 'user_id' => Auth::id(), 'session_id' => Session::getId()]);
                session()->forget('selected_items');
                return redirect()->route('shopping-cart.index')->with('error', 'Các sản phẩm được chọn không hợp lệ hoặc không đủ số lượng trong kho!');
            }

            // Lấy thông tin địa chỉ
            $address = UserAddress::where('id', $request->address_id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            \Log::info('Selected address', array_merge($address->toArray(), ['session_id' => Session::getId()]));

            // Tính toán giá trị đơn hàng
            $subtotal = $cartItems->sum(function ($item) {
                $price = $item->price_at_time ?? ($item->productVariant ? $item->productVariant->price : $item->product->price) ?? 0;
                $quantity = $item->quantity ?? 0;
                \Log::debug('Cart Item Calculation', [
                    'product_id' => $item->product_id,
                    'variant_id' => $item->product_variant_id,
                    'price' => $price,
                    'quantity' => $quantity,
                    'subtotal' => $price * $quantity,
                    'session_id' => Session::getId()
                ]);
                return $price * $quantity;
            });

            $shippingFee = $request->shipping_type === 'thường' ? 16500 : 30000;
            $coupon = session('coupon') ? Coupon::find(session('coupon')->id) : null;
            $discount = 0;
            if ($coupon && $this->isValidCoupon($coupon, $subtotal)) {
                $discount = $this->calculateDiscount($coupon, $subtotal);
            } else if ($coupon) {
                \Log::warning('Invalid coupon', ['coupon_id' => $coupon->id, 'subtotal' => $subtotal, 'session_id' => Session::getId()]);
                session()->forget('coupon');
            }
            $total = $subtotal + $shippingFee - $discount;

            // Validate total
            if ($total <= 0) {
                \Log::error('Invalid order total', [
                    'subtotal' => $subtotal,
                    'shipping_fee' => $shippingFee,
                    'discount' => $discount,
                    'total' => $total,
                    'session_id' => Session::getId()
                ]);
                return redirect()->route('checkout')->with('error', 'Số tiền đơn hàng không hợp lệ!');
            }

            // Chuẩn bị dữ liệu đơn hàng
            $orderData = [
                'user_id' => Auth::id(),
                'address_id' => $request->address_id,
                'phone_number' => $address->phone_number,
                'email' => $address->email,
                'fullname' => $address->fullname,
                'address' => implode(', ', [$address->street, $address->ward, $address->district, $address->province]),
                'city' => $address->province,
                'note' => $request->note,
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'shipping_type' => $request->shipping_type,
                'coupon_id' => $coupon ? $coupon->id : null,
                'discount' => $discount,
                'total_amount' => $total,
                'payment_method' => $request->payment_method,
                'cart_items' => $cartItems->map(function ($item) {
                    $price = $item->price_at_time ?? ($item->productVariant ? $item->productVariant->price : $item->product->price) ?? 0;
                    return [
                        'product_id' => $item->product_id,
                        'product_variant_id' => $item->product_variant_id,
                        'name' => $item->product->name,
                        'price' => $price,
                        'quantity' => $item->quantity,
                        'attributes_variant' => $item->productVariant ? json_encode(
                            $item->productVariant->attributeValues->mapWithKeys(function ($attrValue) {
                                return [$attrValue->attribute->name => $attrValue->value];
                            })->toArray(),
                            JSON_UNESCAPED_UNICODE
                        ) : null,
                        'name_variant' => $item->productVariant ? $item->productVariant->name : null,
                        'price_variant' => $price,
                    ];
                })->toArray(),
            ];

            \Log::info('Order data prepared', ['order_data' => $orderData, 'session_id' => Session::getId()]);

            // Tạo đơn hàng
            $order = $this->createOrder($orderData);

            // Xóa các mục đã chọn trong giỏ hàng
            $this->clearCartAndSession($cart, $cartItems);
            \Log::info('After clearCartAndSession (post-order creation)', [
                'user_id' => Auth::id(),
                'cart_exists' => Cart::where('user_id', Auth::id())->exists(),
                'cart_items_count' => Cart::where('user_id', Auth::id())->first() ? Cart::where('user_id', Auth::id())->first()->items()->count() : 0,
                'session_id' => Session::getId()
            ]);

            // Lưu user_id và session data để sử dụng sau VNPay redirect
            session(['post_payment_user_id' => Auth::id(), 'post_payment_session_id' => Session::getId()]);
            Session::save(); // Lưu session ngay lập tức

            if ($request->payment_method === 'cod') {
                return redirect()->route('checkout.success', $order->code)
                    ->with('success', 'Đơn hàng của bạn đã được đặt thành công!');
            } else {
                return $this->vnpayPayment($order, $cart, $cartItems);
            }
        } catch (\Exception $e) {
            \Log::error('Checkout process error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'request' => $request->all(),
                'stack_trace' => $e->getTraceAsString(),
                'session_id' => Session::getId()
            ]);
            return redirect()->route('checkout')
                ->withInput()
                ->with('error', 'Đã xảy ra lỗi khi xử lý đơn hàng: ' . $e->getMessage());
        }
    }

    protected function clearCartAndSession($cart, $cartItems)
    {
        try {
            \Log::info('Starting clearCartAndSession', [
                'cart_id' => $cart ? $cart->id : null,
                'cart_items_count' => $cart ? $cart->items()->count() : 0,
                'selected_items' => session('selected_items', []),
                'session_id' => Session::getId()
            ]);

            if ($cart && !empty(session('selected_items', []))) {
                $selectedItems = session('selected_items', []);
                $deleted = CartItem::where('cart_id', $cart->id)
                    ->whereIn('id', $selectedItems)
                    ->delete();
                \Log::info('Deleted CartItems', [
                    'cart_id' => $cart->id,
                    'deleted_count' => $deleted,
                    'selected_items' => $selectedItems,
                    'session_id' => Session::getId()
                ]);

                // Chỉ xóa giỏ hàng nếu không còn CartItem
                if ($cart->items()->count() === 0) {
                    $cart->delete();
                    \Log::info('Cart deleted', ['cart_id' => $cart->id, 'session_id' => Session::getId()]);
                } else {
                    \Log::info('Cart retained, items remain', [
                        'cart_id' => $cart->id,
                        'remaining_items_count' => $cart->items()->count(),
                        'session_id' => Session::getId()
                    ]);
                }
            } else {
                \Log::warning('No cart or selected_items found, skipping deletion', [
                    'cart_id' => $cart ? $cart->id : null,
                    'selected_items' => session('selected_items', []),
                    'session_id' => Session::getId()
                ]);
            }

            // Xóa session liên quan, giữ lại post_payment_user_id và post_payment_session_id
            session()->forget(['coupon', 'selected_items', 'pending_order_id', 'vnpay_txn_ref', 'last_checkout_request', 'last_checkout_time']);
            \Log::info('Cleared checkout-related sessions', ['session_id' => Session::getId()]);
        } catch (\Exception $e) {
            \Log::error('Error in clearCartAndSession: ' . $e->getMessage(), [
                'cart_id' => $cart ? $cart->id : null,
                'stack_trace' => $e->getTraceAsString(),
                'session_id' => Session::getId()
            ]);
        }
    }

    public function retryPendingPayment(Request $request, $order_code)
    {
        try {
            if (!Auth::check()) {
                \Log::warning('User not authenticated in retry pending payment', [
                    'user_id' => Auth::id(),
                    'order_code' => $order_code,
                    'session_id' => Session::getId()
                ]);
                return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục!');
            }

            $order = Order::where('user_id', Auth::id())
                ->where('code', $order_code)
                ->where('payment_id', 2) // VNPay
                ->where('is_paid', 0) // Chưa thanh toán
                ->first();

            if (!$order) {
                \Log::warning('Invalid retry pending payment attempt', [
                    'user_id' => Auth::id(),
                    'order_code' => $order_code,
                    'session_id' => Session::getId()
                ]);
                return redirect()->route('home')->with('error', 'Đơn hàng không tồn tại hoặc không hợp lệ để thanh toán lại!');
            }

            // Lưu user_id và session_id vào session
            session(['post_payment_user_id' => Auth::id(), 'post_payment_session_id' => Session::getId()]);
            Session::save();

            // Chuyển trực tiếp đến trang thanh toán VNPay
            return $this->vnpayPayment($order, null, null);
        } catch (\Exception $e) {
            \Log::error('Retry pending payment error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'order_code' => $order_code,
                'stack_trace' => $e->getTraceAsString(),
                'session_id' => Session::getId()
            ]);
            return redirect()->route('home')
                ->with('error', 'Lỗi khi thử thanh toán lại: ' . $e->getMessage());
        }
    }

    protected function vnpayPayment($order, $cart = null, $cartItems = null)
    {
        try {
            $vnp_TmnCode = env('VNPAY_TMN_CODE');
            $vnp_HashSecret = env('VNPAY_HASH_SECRET');
            $vnp_Url = env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
            $vnp_Returnurl = route('checkout.vnpay-return');

            // Ghi log cấu hình để kiểm tra
            \Log::debug('VNPay Configuration Check', [
                'vnp_TmnCode' => $vnp_TmnCode,
                'vnp_HashSecret' => $vnp_HashSecret ? substr($vnp_HashSecret, 0, 4) . '****' : null,
                'vnp_Url' => $vnp_Url,
                'vnp_Returnurl' => $vnp_Returnurl,
                'session_id' => Session::getId()
            ]);

            if (!$vnp_TmnCode || !$vnp_HashSecret || !$vnp_Url || !$vnp_Returnurl) {
                \Log::error('Cấu hình VNPay không hợp lệ', [
                    'vnp_TmnCode' => $vnp_TmnCode,
                    'vnp_HashSecret' => $vnp_HashSecret ? '****' : null,
                    'vnp_Url' => $vnp_Url,
                    'vnp_Returnurl' => $vnp_Returnurl,
                    'session_id' => Session::getId()
                ]);
                throw new \Exception('Cấu hình VNPay không hợp lệ.');
            }

            $vnp_TxnRef = $order->code;
            $vnp_OrderInfo = urlencode('Thanh toán đơn hàng ' . $vnp_TxnRef);
            $vnp_Amount = $order->total_amount * 100;
            $vnp_Locale = 'vn';
            $vnp_IpAddr = request()->ip();
            $vnp_CreateDate = now()->format('YmdHis');

            $inputData = [
                'vnp_Version' => '2.1.0',
                'vnp_TmnCode' => $vnp_TmnCode,
                'vnp_Amount' => $vnp_Amount,
                'vnp_Command' => 'pay',
                'vnp_CreateDate' => $vnp_CreateDate,
                'vnp_CurrCode' => 'VND',
                'vnp_IpAddr' => $vnp_IpAddr,
                'vnp_Locale' => $vnp_Locale,
                'vnp_OrderInfo' => $vnp_OrderInfo,
                'vnp_OrderType' => '250000',
                'vnp_ReturnUrl' => $vnp_Returnurl,
                'vnp_TxnRef' => $vnp_TxnRef,
                'vnp_BankCode' => 'NCB',
            ];

            \Log::debug('VNPay request data before sorting', ['inputData' => $inputData, 'session_id' => Session::getId()]);

            // Sắp xếp tham số
            ksort($inputData);

            // Tạo query string với mã hóa đúng
            $query = '';
            $i = 0;
            foreach ($inputData as $key => $value) {
                if ($i++ > 0) {
                    $query .= '&';
                }
                $query .= $key . '=' . urlencode($value);
            }
            $query = str_replace('%20', '+', $query);

            \Log::debug('VNPay query string', ['query' => $query, 'session_id' => Session::getId()]);

            // Tạo chữ ký
            $vnpSecureHash = hash_hmac('sha512', $query, $vnp_HashSecret);

            \Log::debug('VNPay secure hash', [
                'calculated_hash' => $vnpSecureHash,
                'query_string' => $query,
                'session_id' => Session::getId()
            ]);

            $vnp_Url = $vnp_Url . '?' . $query . '&vnp_SecureHash=' . $vnpSecureHash;

            \Log::info('VNPay redirect', [
                'url' => $vnp_Url,
                'txn_ref' => $vnp_TxnRef,
                'amount' => $vnp_Amount,
                'order_id' => $order->id,
                'session_id' => Session::getId()
            ]);

            // Lưu session trước khi redirect
            session(['vnpay_txn_ref' => $vnp_TxnRef, 'pending_order_id' => $order->id]);
            Session::save();

            return redirect($vnp_Url);
        } catch (\Exception $e) {
            \Log::error('VNPay error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'stack_trace' => $e->getTraceAsString(),
                'session_id' => Session::getId()
            ]);
            return redirect()->route('checkout')
                ->with('error', 'Lỗi khi tạo thanh toán VNPay: ' . $e->getMessage());
        }
    }

    public function vnpayReturn(Request $request)
    {
        try {
            \Log::info('VNPay return full URL', [
                'full_url' => $request->fullUrl(),
                'query_params' => $request->query(),
                'all_data' => $request->all(),
                'ip' => $request->ip(),
                'session_id' => Session::getId()
            ]);

            // Kiểm tra các tham số bắt buộc
            if (!$request->has('vnp_TxnRef') || !$request->has('vnp_Amount') || !$request->has('vnp_ResponseCode') || !$request->has('vnp_SecureHash')) {
                \Log::error('VNPay thiếu tham số bắt buộc', [
                    'request_data' => $request->all(),
                    'missing_params' => array_diff(['vnp_TxnRef', 'vnp_Amount', 'vnp_ResponseCode', 'vnp_SecureHash'], array_keys($request->all())),
                    'session_id' => Session::getId()
                ]);
                return redirect()->route('home')->with('error', 'Dữ liệu trả về từ VNPay không hợp lệ!');
            }

            $vnp_SecureHash = $request->input('vnp_SecureHash');
            $vnp_HashSecret = env('VNPAY_HASH_SECRET');

            // Chỉ lấy các tham số bắt đầu bằng vnp_ và loại bỏ vnp_SecureHash, vnp_SecureHashType
            $inputData = array_filter($request->all(), function ($key) {
                return strpos($key, 'vnp_') === 0 && !in_array($key, ['vnp_SecureHash', 'vnp_SecureHashType']);
            }, ARRAY_FILTER_USE_KEY);

            // Loại bỏ các tham số rỗng
            $inputData = array_filter($inputData, function ($value) {
                return $value !== '' && $value !== null;
            });

            // Sắp xếp tham số theo thứ tự bảng chữ cái
            ksort($inputData);

            // Tạo query string với mã hóa đúng
            $query = '';
            $i = 0;
            foreach ($inputData as $key => $value) {
                if ($i++ > 0) {
                    $query .= '&';
                }
                $query .= $key . '=' . urlencode($value);
            }
            $query = str_replace('%20', '+', $query);

            // Tính toán hash
            $hash = hash_hmac('sha512', $query, $vnp_HashSecret);

            \Log::debug('VNPay secure hash verification', [
                'query_string' => $query,
                'calculated_hash' => $hash,
                'provided_hash' => $vnp_SecureHash,
                'input_data' => $inputData,
                'session_id' => Session::getId()
            ]);

            if ($hash === $vnp_SecureHash) {
                $order = Order::where('code', $request->vnp_TxnRef)->firstOrFail();

                // Kiểm tra số tiền
                if ($order->total_amount * 100 != $request->vnp_Amount) {
                    \Log::warning('VNPay amount mismatch', [
                        'order_id' => $order->id,
                        'order_amount' => $order->total_amount * 100,
                        'vnp_amount' => $request->vnp_Amount,
                        'session_id' => Session::getId()
                    ]);
                    return redirect()->route('home')->with('error', 'Số tiền thanh toán không khớp với đơn hàng!');
                }

                // Kiểm tra trạng thái đơn hàng
                if ($order->is_paid) {
                    \Log::warning('Order already paid', [
                        'order_id' => $order->id,
                        'txn_ref' => $request->vnp_TxnRef,
                        'session_id' => Session::getId()
                    ]);
                    return redirect()->route('checkout.success', $order->code)
                        ->with('success', 'Đơn hàng đã được thanh toán trước đó!');
                }

                if ($request->vnp_ResponseCode == '00' && $request->vnp_TransactionStatus == '00') {
                    // Sử dụng giao dịch để cập nhật trạng thái
                    DB::transaction(function () use ($order, $request) {
                        $order->update([
                            'is_paid' => 1,
                            'order_status_id' => 2,
                        ]);

                        PaymentLog::create([
                            'order_id' => $order->id,
                            'payment_method' => 'vnpay',
                            'transaction_id' => $request->vnp_TransactionNo,
                            'amount' => $request->vnp_Amount / 100,
                            'status' => 'success',
                            'response_data' => json_encode($request->all())
                        ]);
                    });

                    // Xóa session liên quan
                    session()->forget(['vnpay_txn_ref', 'pending_order_id', 'selected_items', 'coupon', 'post_payment_user_id', 'post_payment_session_id']);
                    \Log::info('VNPay payment successful', [
                        'order_id' => $order->id,
                        'txn_ref' => $request->vnp_TxnRef,
                        'amount' => $request->vnp_Amount / 100,
                        'session_id' => Session::getId()
                    ]);

                    return redirect()->route('checkout.success', $order->code)
                        ->with('success', 'Thanh toán thành công!');
                } else {
                    // Thanh toán thất bại
                    PaymentLog::create([
                        'order_id' => $order->id,
                        'payment_method' => 'vnpay',
                        'transaction_id' => $request->vnp_TransactionNo,
                        'amount' => $request->vnp_Amount / 100,
                        'status' => 'failed',
                        'response_data' => json_encode($request->all())
                    ]);

                    \Log::warning('VNPay payment failed', [
                        'order_id' => $order->id,
                        'txn_ref' => $request->vnp_TxnRef,
                        'response_code' => $request->vnp_ResponseCode,
                        'transaction_status' => $request->vnp_TransactionStatus,
                        'session_id' => Session::getId()
                    ]);

                    return redirect()->route('home')
                        ->with('error', 'Thanh toán thất bại! Mã lỗi: ' . $request->vnp_ResponseCode);
                }
            } else {
                \Log::error('VNPay invalid secure hash', [
                    'request_data' => $request->all(),
                    'calculated_hash' => $hash,
                    'provided_hash' => $vnp_SecureHash,
                    'query_string' => $query,
                    'session_id' => Session::getId()
                ]);
                return redirect()->route('home')
                    ->with('error', 'Dữ liệu trả về từ VNPay không hợp lệ! Mã lỗi: Invalid checksum');
            }
        } catch (\Exception $e) {
            \Log::error('VNPay return error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'stack_trace' => $e->getTraceAsString(),
                'session_id' => Session::getId()
            ]);
            return redirect()->route('home')
                ->with('error', 'Lỗi khi xử lý thanh toán VNPay: ' . $e->getMessage());
        }
    }

    protected function createOrder(array $orderData)
    {
        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id' => $orderData['user_id'],
                'code' => Str::upper('ORD-' . Str::random(8)),
                'payment_id' => $orderData['payment_method'] === 'cod' ? 1 : 2,
                'phone_number' => $orderData['phone_number'],
                'email' => $orderData['email'],
                'fullname' => $orderData['fullname'],
                'address' => $orderData['address'],
                'city' => $orderData['city'],
                'note' => $orderData['note'],
                'total_amount' => $orderData['total_amount'],
                'shipping_type' => $orderData['shipping_type'],
                'shipping_fee' => $orderData['shipping_fee'] ?? 0,
                'is_paid' => $orderData['payment_method'] === 'cod' ? 0 : 0,
                'is_refunded' => 0,
                'coupon_id' => $orderData['coupon_id'],
                'discount_amount' => $orderData['discount'] ?? 0,
                'is_refunded_canceled' => 0,
                'check_refunded_canceled' => 0,
                'img_refunded_money' => null,
            ]);

            \Log::info('Order created', [
                'order_id' => $order->id,
                'code' => $order->code,
                'total_amount' => $order->total_amount,
                'session_id' => Session::getId()
            ]);

            OrderOrderStatus::create([
                'order_id' => $order->id,
                'order_status_id' => $orderData['payment_method'] === 'cod' ? 1 : 1,
                'modified_by' => Auth::id(),
                'note' => $orderData['payment_method'] === 'cod' ? 'Đơn hàng mới được tạo, đang chờ xác nhận.' : 'Đơn hàng chờ thanh toán VNPay.',
                'employee_evidence' => null,
                'customer_confirmation' => null,
                'is_current' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($orderData['cart_items'] as $item) {
                $price = $item['price'] ?? ($item['product_variant_id'] ? $item['price_variant'] : $item['product']->price);
                if ($price <= 0) {
                    \Log::error('Invalid price for order item', [
                        'product_id' => $item['product_id'],
                        'variant_id' => $item['product_variant_id'],
                        'price' => $price,
                        'session_id' => Session::getId()
                    ]);
                    throw new \Exception('Giá sản phẩm không hợp lệ: ' . $item['name']);
                }

                // Kiểm tra tồn kho
                if ($item['product_variant_id']) {
                    $variant = ProductVariant::lockForUpdate()->find($item['product_variant_id']);
                    if (!$variant) {
                        \Log::error('Product variant not found', [
                            'variant_id' => $item['product_variant_id'],
                            'product_id' => $item['product_id'],
                            'session_id' => Session::getId()
                        ]);
                        throw new \Exception('Biến thể sản phẩm không tồn tại: ' . $item['name']);
                    }
                    if ($variant->stock < $item['quantity']) {
                        \Log::error('Insufficient stock for variant', [
                            'variant_id' => $item['product_variant_id'],
                            'stock' => $variant->stock,
                            'quantity' => $item['quantity'],
                            'session_id' => Session::getId()
                        ]);
                        throw new \Exception('Sản phẩm không đủ số lượng trong kho: ' . $item['name'] . ' (Biến thể: ' . ($variant->name ?? 'N/A') . ', Tồn kho: ' . $variant->stock . ', Yêu cầu: ' . $item['quantity'] . ')');
                    }
                } else {
                    $product = Product::lockForUpdate()->find($item['product_id']);
                    if (!$product) {
                        \Log::error('Product not found', [
                            'product_id' => $item['product_id'],
                            'session_id' => Session::getId()
                        ]);
                        throw new \Exception('Sản phẩm không tồn tại: ' . $item['name']);
                    }
                    if ($product->stock < $item['quantity']) {
                        \Log::error('Insufficient stock for product', [
                            'product_id' => $item['product_id'],
                            'stock' => $product->stock,
                            'quantity' => $item['quantity'],
                            'session_id' => Session::getId()
                        ]);
                        throw new \Exception('Sản phẩm không đủ số lượng trong kho: ' . $item['name'] . ' (Tồn kho: ' . $product->stock . ', Yêu cầu: ' . $item['quantity'] . ')');
                    }
                }

                // Tạo OrderItem
                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->product_id = $item['product_id'];
                $orderItem->product_variant_id = $item['product_variant_id'];
                $orderItem->name = $item['name'];
                $orderItem->price = $price;
                $orderItem->quantity = $item['quantity'];
                $orderItem->attributes_variant = $item['attributes_variant'];
                $orderItem->name_variant = $item['name_variant'];
                $orderItem->price_variant = $item['price_variant'];
                $orderItem->save();

                \Log::info('OrderItem created', [
                    'order_id' => $order->id,
                    'order_item_id' => $orderItem->id,
                    'product_id' => $item['product_id'],
                    'price' => $orderItem->price,
                    'quantity' => $orderItem->quantity,
                    'session_id' => Session::getId()
                ]);

                // Cập nhật tồn kho
                if ($item['product_variant_id']) {
                    $variant->stock = max(0, $variant->stock - $item['quantity']);
                    $variant->save();
                    \Log::info('Updated variant stock', [
                        'variant_id' => $item['product_variant_id'],
                        'new_stock' => $variant->stock,
                        'session_id' => Session::getId()
                    ]);
                } else {
                    $product->stock = max(0, $product->stock - $item['quantity']);
                    $product->save();
                    \Log::info('Updated product stock', [
                        'product_id' => $item['product_id'],
                        'new_stock' => $product->stock,
                        'session_id' => Session::getId()
                    ]);
                }
            }

            if ($orderData['coupon_id']) {
                $coupon = Coupon::find($orderData['coupon_id']);
                if ($coupon) {
                    $coupon->increment('usage_count');
                }
            }

            try {
                Mail::to($order->email)->send(new \App\Mail\OrderConfirmationMail($order));
                \Log::info('Order confirmation email sent', [
                    'order_id' => $order->id,
                    'email' => $order->email,
                    'session_id' => Session::getId()
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to send order confirmation email', [
                    'order_id' => $order->id,
                    'email' => $order->email,
                    'error' => $e->getMessage(),
                    'session_id' => Session::getId()
                ]);
            }

            DB::commit();
            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Create order error: ' . $e->getMessage(), [
                'order_data' => $orderData,
                'stack_trace' => $e->getTraceAsString(),
                'session_id' => Session::getId()
            ]);
            throw $e;
        }
    }

    public function success($order_number)
    {
        try {
            $order = Order::where('code', $order_number)->first();
            if (!$order || ($order->user_id !== Auth::id() && Auth::check())) {
                \Log::error('Invalid order or user mismatch', [
                    'order_number' => $order_number,
                    'user_id' => Auth::id(),
                    'session_id' => Session::getId()
                ]);
                return redirect()->route('home')->with('error', 'Đơn hàng không tồn tại hoặc không thuộc về bạn!');
            }
            return view('client.checkout-success', compact('order'));
        } catch (\Exception $e) {
            \Log::error('Checkout success error: ' . $e->getMessage(), [
                'order_number' => $order_number,
                'user_id' => Auth::id(),
                'stack_trace' => $e->getTraceAsString(),
                'session_id' => Session::getId()
            ]);
            return redirect()->route('home')->with('error', 'Không tìm thấy đơn hàng!');
        }
    }
}
