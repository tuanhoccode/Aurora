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
use App\Http\Requests\Client\AddressFormRequest;
use App\Http\Requests\Client\SaveAddressRequest;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        try {
            if (!Auth::check()) {
                \Log::warning('User not authenticated', ['user_id' => Auth::id()]);
                return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục!');
            }

            $user = Auth::user();
            $cart = Cart::where('user_id', $user->id)
                ->with(['items' => function ($query) {
                    $query->with(['product', 'productVariant']);
                }])
                ->first();

            if (!$cart) {
                \Log::warning('Cart not found', ['user_id' => $user->id]);
                return redirect()->route('shopping-cart.index')->with('error', 'Giỏ hàng không tồn tại!');
            }

            // Lấy selected_items từ session, sử dụng query string như dự phòng
            $selectedItems = session('selected_items', []);
            if ($request->query('selected_items')) {
                try {
                    $decodedItems = json_decode($request->query('selected_items'), true);
                    if (is_array($decodedItems) && !empty($decodedItems)) {
                        $selectedItems = array_map('intval', $decodedItems); // Chuyển thành số nguyên
                        session(['selected_items' => $selectedItems]); // Lưu vào session
                        \Log::info('Selected items updated from query', ['selected_items' => $selectedItems]);
                    } else {
                        \Log::warning('Invalid selected_items format', ['query' => $request->query('selected_items')]);
                    }
                } catch (\Exception $e) {
                    \Log::error('Error decoding selected_items', [
                        'query' => $request->query('selected_items'),
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Kiểm tra selected_items là mảng hợp lệ và không rỗng
            if (!is_array($selectedItems) || empty($selectedItems)) {
                \Log::warning('No valid selected items', [
                    'selected_items' => $selectedItems,
                    'query' => $request->query('selected_items')
                ]);
                return redirect()->route('shopping-cart.index')->with('error', 'Vui lòng chọn ít nhất một sản phẩm để thanh toán!');
            }

            // Lọc cart items dựa trên selected_items và kiểm tra stock
            $cartItems = $cart->items->filter(function ($item) use ($selectedItems) {
                return in_array($item->id, $selectedItems);
            })->filter(function ($item) {
                $stock = $item->productVariant ? $item->productVariant->stock : $item->product->stock;
                return $stock >= $item->quantity;
            });

            if ($cartItems->isEmpty()) {
                \Log::warning('No valid cart items after filtering', [
                    'selected_items' => $selectedItems,
                    'user_id' => $user->id
                ]);
                session()->forget('selected_items');
                return redirect()->route('shopping-cart.index')->with('error', 'Các sản phẩm được chọn không hợp lệ hoặc không đủ số lượng trong kho!');
            }

            // Tính tổng giá trị giỏ hàng
            $cartTotal = $cartItems->sum(function ($item) {
                $price = $item->price_at_time ?? $item->product->price ?? null;
                if ($price === null) {
                    \Log::error('Price not found for cart item', [
                        'item_id' => $item->id,
                        'product_id' => $item->product_id
                    ]);
                    throw new \Exception('Không thể tính giá sản phẩm: ' . $item->product->name);
                }
                $quantity = $item->quantity ?? 0;
                \Log::debug('Cart Item Calculation', [
                    'product_id' => $item->product_id,
                    'variant_id' => $item->product_variant_id,
                    'price' => $price,
                    'quantity' => $quantity,
                    'subtotal' => $price * $quantity
                ]);
                return $price * $quantity;
            });

            // Lấy danh sách địa chỉ
            $addresses = UserAddress::where('user_id', $user->id)->get();
            $defaultAddress = $addresses->where('is_default', 1)->first();

            // Đặt địa chỉ mặc định nếu chưa có trong session
            if (!session('checkout_address_id') && $defaultAddress) {
                session(['checkout_address_id' => $defaultAddress->id]);
            }

            // Đặt mặc định phương thức thanh toán và vận chuyển
            $paymentMethod = session('payment_method', 'cod');
            $shippingType = session('shipping_type', 'thường');
            $shippingFee = $shippingType === 'nhanh' ? 30000 : 16500;
            session([
                'payment_method' => $paymentMethod,
                'shipping_type' => $shippingType,
                'shipping_fee' => $shippingFee
            ]);

            // Xử lý mã giảm giá
            $coupon = session('coupon') ? Coupon::find(session('coupon')->id) : null;
            $discount = 0;
            if ($coupon && $this->isValidCoupon($coupon, $cartTotal)) {
                $discount = $this->calculateDiscount($coupon, $cartTotal);
            } else if ($coupon) {
                \Log::warning('Invalid coupon', ['coupon_id' => $coupon->id, 'cart_total' => $cartTotal]);
                session()->forget('coupon');
            }

            // Log dữ liệu để debug
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
                'payment_method' => $paymentMethod
            ]);

            // Lấy danh sách mã giảm giá có sẵn
            $availableCoupons = Coupon::where('is_active', true)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->where(function ($query) {
                    $query->whereNull('usage_limit')
                          ->orWhereRaw('usage_count < usage_limit');
                })
                ->orderBy('discount_value', 'desc')
                ->get();

            Log::info('Checkout Summary', [
                'cartTotal' => $cartTotal,
                'shippingFee' => $shippingFee,
                'shippingType' => $shippingType,
                'coupon' => $coupon ? $coupon->toArray() : null,
                'discount' => $discount,
                'selected_items' => $selectedItems,
                'address_id' => session('checkout_address_id'),
                'payment_method' => session('payment_method')
            ]);

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
                'shippingType',
                'availableCoupons'
            ));
        } catch (\Exception $e) {
            \Log::error('Checkout Error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
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

            // Đảm bảo giữ nguyên selected_items trong session
            if (!session('selected_items')) {
                \Log::warning('No selected items in session, redirecting to cart', ['user_id' => Auth::id()]);
                return redirect()->route('shopping-cart.index')->with('error', 'Vui lòng chọn sản phẩm để thanh toán!');
            }

            \Log::info('Checkout updated', [
                'checkout_address_id' => session('checkout_address_id'),
                'shipping_type' => session('shipping_type'),
                'payment_method' => session('payment_method'),
                'note' => session('note'),
                'shipping_fee' => $shippingFee,
                'selected_items' => session('selected_items')
            ]);

            return redirect()->route('checkout')->with('success', 'Cập nhật thông tin thành công!');
        } catch (\Exception $e) {
            \Log::error('Checkout Update Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
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
                return ($item->price_at_time ?? $item->product->price ?? 0) * ($item->quantity ?? 0);
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
            Log::info('Coupon applied', ['code' => $coupon->code, 'discount_value' => $coupon->discount_value, 'discount_type' => $coupon->discount_type]);
            return redirect()->route('checkout')->with('success', 'Áp dụng mã giảm giá thành công!');
        } catch (\Exception $e) {
            Log::error('Apply coupon error: ' . $e->getMessage());
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
                return ($item->price_at_time ?? $item->product->price ?? 0) * ($item->quantity ?? 0);
            });

            if (!$this->isValidCoupon($coupon, $cartTotal)) {
                session()->forget('coupon');
                return redirect()->route('checkout')->with('error', 'Mã giảm giá không áp dụng được cho đơn hàng này!');
            }

            session(['coupon' => $coupon]);
            Log::info('Coupon applied by ID', ['coupon_id' => $coupon->id, 'code' => $coupon->code]);
            return redirect()->route('checkout')->with('success', 'Áp dụng mã giảm giá thành công!');
        } catch (\Exception $e) {
            Log::error('Apply coupon by ID error: ' . $e->getMessage());
            return redirect()->route('checkout')->with('error', 'Lỗi khi áp dụng mã giảm giá, vui lòng thử lại!');
        }
    }

    public function removeCoupon(Request $request)
    {
        try {
            session()->forget('coupon');
            Log::info('Coupon removed from session');
            return redirect()->route('checkout')->with('success', 'Đã xóa mã giảm giá!');
        } catch (\Exception $e) {
            Log::error('Remove coupon error: ' . $e->getMessage());
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
            'is_valid' => $isValid
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
        ]);

        if ($coupon->discount_type === 'percent') {
            $discount = $cartTotal * ((float)$coupon->discount_value / 100);
            Log::info('Calculated Discount (percent)', ['discount' => $discount]);
            return $discount;
        } else {
            $discount = min((float)$coupon->discount_value, $cartTotal);
            Log::info('Calculated Discount (fix_amount)', ['discount' => $discount]);
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
            Log::error('Create address error: ' . $e->getMessage());
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
            Log::error('Store address error: ' . $e->getMessage());
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
            Log::error('Edit address error: ' . $e->getMessage());
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
            Log::error('Save address error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Đã xảy ra lỗi khi cập nhật địa chỉ, vui lòng thử lại!');
        }
    }

    public function process(Request $request)
    {
        try {
            \Log::info('Starting checkout process', [
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);

            // Kiểm tra request lặp lại
            $requestHash = md5(json_encode($request->all()));
            $lastRequestTime = session('last_checkout_time', 0);
            if (session('last_checkout_request') === $requestHash && now()->diffInSeconds(now()->createFromTimestamp($lastRequestTime)) < 60) {
                \Log::warning('Duplicate checkout request detected', [
                    'user_id' => Auth::id(),
                    'request_data' => $request->all()
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
                \Log::warning('Cart is empty or not found', ['user_id' => Auth::id()]);
                return redirect()->route('shopping-cart.index')->with('error', 'Giỏ hàng trống!');
            }

            // Lấy selected_items từ session
            $selectedItems = session('selected_items', []);
            if (empty($selectedItems)) {
                \Log::warning('No selected items in session', ['user_id' => Auth::id()]);
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
                \Log::warning('No valid cart items after filtering', ['selected_items' => $selectedItems, 'user_id' => Auth::id()]);
                session()->forget('selected_items');
                return redirect()->route('shopping-cart.index')->with('error', 'Các sản phẩm được chọn không hợp lệ hoặc không đủ số lượng trong kho!');
            }

            // Lấy thông tin địa chỉ
            $address = UserAddress::where('id', $request->address_id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            \Log::info('Selected address', $address->toArray());

            // Tính toán giá trị đơn hàng
            $subtotal = $cartItems->sum(function ($item) {
                $price = $item->price_at_time ?? $item->product->price ?? 0;
                $quantity = $item->quantity ?? 0;
                \Log::debug('Cart Item Calculation', [
                    'product_id' => $item->product_id,
                    'variant_id' => $item->product_variant_id,
                    'price' => $price,
                    'quantity' => $quantity,
                    'subtotal' => $price * $quantity
                ]);
                return $price * $quantity;
            });

            $shippingFee = $request->shipping_type === 'thường' ? 16500 : 30000;
            $coupon = session('coupon') ? Coupon::find(session('coupon')->id) : null;
            $discount = 0;
            if ($coupon && $this->isValidCoupon($coupon, $subtotal)) {
                $discount = $this->calculateDiscount($coupon, $subtotal);
            } else if ($coupon) {
                \Log::warning('Invalid coupon', ['coupon_id' => $coupon->id, 'subtotal' => $subtotal]);
                session()->forget('coupon');
            }
            $total = $subtotal + $shippingFee - $discount;

            // Lưu thông tin đơn hàng vào session
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
                    return [
                        'product_id' => $item->product_id,
                        'product_variant_id' => $item->product_variant_id,
                        'name' => $item->product->name,
                        'price' => $item->price_at_time ?? $item->product->price ?? 0,
                        'quantity' => $item->quantity,
                        'attributes_variant' => $item->productVariant ? json_encode(
                            $item->productVariant->attributeValues->mapWithKeys(function ($attrValue) {
                                return [$attrValue->attribute->name => $attrValue->value];
                            })->toArray(),
                            JSON_UNESCAPED_UNICODE
                        ) : null,
                        'name_variant' => $item->productVariant ? $item->productVariant->name : null,
                        'price_variant' => $item->price_at_time ?? ($item->productVariant ? $item->productVariant->price : $item->product->price),
                    ];
                })->toArray(),
            ];

            \Log::info('Order data prepared', ['order_data' => $orderData]);

            session(['pending_order' => $orderData]);

            if ($request->payment_method === 'cod') {
                $order = $this->createOrder($orderData);
                $this->clearCartAndSession($cart, $cartItems);
                session()->forget(['pending_order', 'selected_items', 'last_checkout_request', 'last_checkout_time']);
                return redirect()->route('checkout.success', $order->code)
                    ->with('success', 'Đơn hàng của bạn đã được đặt thành công!');
            } else {
                return $this->vnpayPayment($orderData);
            }
        } catch (\Exception $e) {
            \Log::error('Checkout process error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'request' => $request->all(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('checkout')
                ->withInput()
                ->with('error', 'Đã xảy ra lỗi khi xử lý đơn hàng, vui lòng kiểm tra lại thông tin!');
        }
    }

    protected function createOrder(array $orderData)
    {
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
            'is_paid' => $orderData['payment_method'] === 'cod' ? 0 : 1,
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
            'address' => $order->address,
            'city' => $order->city,
            'discount_amount' => $order->discount_amount
        ]);

        // Tạo trạng thái đơn hàng
        OrderOrderStatus::create([
            'order_id' => $order->id,
            'order_status_id' => $orderData['payment_method'] === 'cod' ? 1 : 2,
            'modified_by' => Auth::id(),
            'note' => $orderData['payment_method'] === 'cod' ? 'Đơn hàng mới được tạo, đang chờ xác nhận.' : 'Thanh toán VNPay thành công',
            'employee_evidence' => null,
            'customer_confirmation' => null,
            'is_current' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Tạo các mục đơn hàng
        foreach ($orderData['cart_items'] as $item) {
            $orderItem = new OrderItem();
            $orderItem->order_id = $order->id;
            $orderItem->product_id = $item['product_id'];
            $orderItem->product_variant_id = $item['product_variant_id'];
            $orderItem->name = $item['name'];
            $orderItem->price = $item['price'];
            $orderItem->quantity = $item['quantity'];
            $orderItem->attributes_variant = $item['attributes_variant'];
            $orderItem->name_variant = $item['name_variant'];
            $orderItem->price_variant = $item['price_variant'];
            $orderItem->save();

            // Trừ tồn kho
            if ($item['product_variant_id']) {
                $variant = ProductVariant::find($item['product_variant_id']);
                if ($variant && $variant->stock >= $item['quantity']) {
                    $variant->stock = max(0, $variant->stock - $item['quantity']);
                    $variant->save();
                }
            }
            $product = Product::find($item['product_id']);
            if ($product && $product->stock >= $item['quantity']) {
                $product->stock = max(0, $product->stock - $item['quantity']);
                $product->save();
            }
        }

        // Tăng số lần sử dụng coupon
        if ($orderData['coupon_id']) {
            $coupon = Coupon::find($orderData['coupon_id']);
            if ($coupon) {
                $coupon->increment('usage_count');
            }
        }

        // Gửi email xác nhận
        try {
            Mail::to($order->email)->send(new \App\Mail\OrderConfirmationMail($order));
            \Log::info('Order confirmation email sent', [
                'order_id' => $order->id,
                'email' => $order->email
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to send order confirmation email', [
                'order_id' => $order->id,
                'email' => $order->email,
                'error' => $e->getMessage()
            ]);
        }

        return $order;
    }

    protected function clearCartAndSession($cart, $cartItems)
    {
        if ($cart) {
            if ($cartItems instanceof \Illuminate\Database\Eloquent\Collection) {
                // Xử lý khi $cartItems là tập hợp các model Eloquent
                $cart->items()->whereIn('id', $cartItems->pluck('id'))->delete();
            } elseif ($cartItems instanceof \Illuminate\Support\Collection) {
                // Xử lý khi $cartItems là Collection chứa mảng
                $variantIds = $cartItems->pluck('product_variant_id')->filter()->toArray();
                if (!empty($variantIds)) {
                    CartItem::whereIn('product_variant_id', $variantIds)
                        ->where('cart_id', $cart->id)
                        ->delete();
                }
            } else {
                // Dự phòng: xóa tất cả các mục liên quan đến giỏ hàng
                $cart->items()->delete();
            }

            // Xóa giỏ hàng nếu không còn mục nào
            if ($cart->items()->count() === 0) {
                $cart->delete();
            }
        }

        // Xóa dữ liệu session
        session()->forget(['coupon', 'selected_items', 'pending_order', 'vnpay_txn_ref']);
    }

    protected function vnpayPayment(array $orderData)
    {
        try {
            $vnp_TmnCode = '3ANN0P8R';
            $vnp_HashSecret = '63J7LYXN9JWZ7BRLGXUXYJ7UH5Q1TA6Y';
            if (!$vnp_TmnCode || !$vnp_HashSecret) {
                throw new \Exception('Cấu hình VNPay không hợp lệ.');
            }

            $vnp_Url = 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html';
            $vnp_Returnurl = route('vnpay.return');

            $vnp_TxnRef = Str::upper('ORD-' . Str::random(8));
            $vnp_OrderInfo = 'Thanh toán đơn hàng ' . $vnp_TxnRef;
            $vnp_Amount = $orderData['total_amount'] * 100;
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

            ksort($inputData);
            $query = http_build_query($inputData);
            $vnpSecureHash = hash_hmac('sha512', $query, $vnp_HashSecret);
            $vnp_Url = $vnp_Url . '?' . $query . '&vnp_SecureHash=' . $vnpSecureHash;

            // Lưu mã giao dịch vào session
            session(['vnpay_txn_ref' => $vnp_TxnRef]);

            \Log::info('VNPay redirect', [
                'url' => $vnp_Url,
                'txn_ref' => $vnp_TxnRef,
                'amount' => $vnp_Amount
            ]);

            return redirect($vnp_Url);
        } catch (\Exception $e) {
            \Log::error('VNPay error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('checkout')
                ->withInput()
                ->with('error', 'Lỗi khi tạo thanh toán VNPay: ' . $e->getMessage());
        }
    }

    public function vnpayReturn(Request $request)
    {
        try {
            if (!Auth::check()) {
                \Log::warning('User not authenticated in VNPay return', ['user_id' => Auth::id()]);
                return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để hoàn tất thanh toán!');
            }

            $vnp_HashSecret = '63J7LYXN9JWZ7BRLGXUXYJ7UH5Q1TA6Y';
            $vnp_SecureHash = $request->vnp_SecureHash;
            $inputData = $request->all();
            unset($inputData['vnp_SecureHash']);
            unset($inputData['vnp_SecureHashType']);

            ksort($inputData);
            $query = http_build_query($inputData);
            $secureHash = hash_hmac('sha512', $query, $vnp_HashSecret);

            \Log::info('VNPay return data', [
                'input_data' => $inputData,
                'secure_hash' => $vnp_SecureHash,
                'calculated_hash' => $secureHash
            ]);

            $orderData = session('pending_order');
            $txnRef = session('vnpay_txn_ref');

            if (!$orderData || !$txnRef || $txnRef !== $request->vnp_TxnRef) {
                \Log::error('Invalid VNPay return data', [
                    'txn_ref' => $request->vnp_TxnRef,
                    'session_txn_ref' => $txnRef,
                    'order_data' => $orderData
                ]);
                return redirect()->route('checkout')
                    ->with('error', 'Dữ liệu thanh toán VNPay không hợp lệ!');
            }

            if ($secureHash === $vnp_SecureHash && $request->vnp_ResponseCode === '00') {
                // Thanh toán thành công, tạo đơn hàng
                $orderData['payment_method'] = 'vnpay';
                $order = $this->createOrder($orderData);

                // Ghi log thanh toán
                PaymentLog::create([
                    'order_id' => $order->id,
                    'txn_ref' => $request->vnp_TxnRef,
                    'response_code' => $request->vnp_ResponseCode,
                    'transaction_no' => $request->vnp_TransactionNo ?? null,
                    'amount' => $request->vnp_Amount / 100,
                    'bank_code' => $request->vnp_BankCode ?? null,
                    'response_data' => json_encode($inputData),
                ]);

                // Xóa giỏ hàng và session
                $cart = Cart::where('user_id', Auth::id())->with('items')->first();
                if ($cart) {
                    $this->clearCartAndSession($cart, collect($orderData['cart_items']));
                }

                return redirect()->route('checkout.success', $order->code)
                    ->with('success', 'Thanh toán VNPay thành công!');
            } else {
                \Log::error('VNPay failed', [
                    'response_code' => $request->vnp_ResponseCode,
                    'txn_ref' => $request->vnp_TxnRef
                ]);
                return redirect()->route('checkout')
                    ->withInput()
                    ->with('error', 'Thanh toán VNPay thất bại với mã lỗi: ' . $request->vnp_ResponseCode);
            }
        } catch (\Exception $e) {
            \Log::error('VNPay return error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('checkout')
                ->withInput()
                ->with('error', 'Lỗi xử lý thanh toán VNPay: ' . $e->getMessage());
        }
    }

    public function success($order_number)
    {
        try {
            $order = Order::where('user_id', Auth::id())->where('code', $order_number)->firstOrFail();
            return view('client.checkout-success', compact('order'));
        } catch (\Exception $e) {
            \Log::error('Checkout success error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('shopping-cart.index')->with('error', 'Không tìm thấy đơn hàng!');
        }
    }
}
