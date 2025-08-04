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
use App\Models\OrderStatusHistory;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmationMail;
use App\Http\Requests\Client\AddressFormRequest;
use App\Http\Requests\Client\SaveAddressRequest;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        try {
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục!');
            }

            $user = Auth::user();
            $cart = Cart::where('user_id', Auth::id())->first();

            if (!$cart) {
                return redirect()->route('shopping-cart.index')->with('error', 'Giỏ hàng không tồn tại!');
            }

            // Lấy selected_items từ session hoặc query
            $selectedItems = session('selected_items', $request->query('selected_items') ? json_decode($request->query('selected_items'), true) : []);

            // Kiểm tra selected_items là mảng hợp lệ
            if (!is_array($selectedItems)) {
                $selectedItems = [];
                Log::warning('Selected items không hợp lệ', ['selected_items' => $request->query('selected_items')]);
            }

            // Lưu selected_items vào session
            session(['selected_items' => $selectedItems]);

            // Lấy cart items với eager loading
            $cartItems = $cart->items()
                ->with(['product', 'productVariant'])
                ->when(!empty($selectedItems), function ($query) use ($selectedItems) {
                    return $query->whereIn('id', $selectedItems);
                })
                ->get();

            if ($cartItems->isEmpty()) {
                return redirect()->route('shopping-cart.index')->with('error', 'Vui lòng chọn ít nhất một sản phẩm để thanh toán!');
            }

            // Tính tổng giá trị giỏ hàng
            $cartTotal = $cartItems->sum(function ($item) {
                $price = $item->price_at_time ?? $item->product->price ?? 0;
                $quantity = $item->quantity ?? 0;
                // Kiểm tra attributes_variant
                $attributes = $item->attributes_variant ? json_decode($item->attributes_variant, true) : null;
                if (json_last_error() !== JSON_ERROR_NONE) {
                    Log::warning('Invalid attributes_variant JSON', [
                        'cart_item_id' => $item->id,
                        'attributes_variant' => $item->attributes_variant
                    ]);
                    $attributes = null;
                }
                // Kiểm tra variant_name
                $variantName = $item->variant_name ?? ($item->productVariant ? $item->productVariant->name : null);
                Log::debug('Cart Item Calculation', [
                    'product_id' => $item->product_id,
                    'variant_id' => $item->product_variant_id,
                    'variant_name' => $variantName,
                    'attributes_variant' => $attributes,
                    'price' => $price,
                    'quantity' => $quantity,
                    'subtotal' => $price * $quantity
                ]);
                return $price * $quantity;
            });

            // Lấy danh sách địa chỉ
            $addresses = UserAddress::where('user_id', Auth::id())->get();
            $defaultAddress = $addresses->where('is_default', 1)->first();

            // Đặt địa chỉ mặc định nếu chưa có trong session
            if (!session('checkout_address_id') && $defaultAddress) {
                session(['checkout_address_id' => $defaultAddress->id]);
            }

            // Đặt mặc định phương thức thanh toán và vận chuyển
            if (!session('payment_method')) {
                session(['payment_method' => 'cod']);
            }

            if (!session('shipping_type')) {
                session(['shipping_type' => 'thường']);
            }

            $shippingType = old('shipping_type', session('shipping_type', 'thường'));
            $shippingFee = $shippingType === 'nhanh' ? 30000 : 16500;
            session(['shipping_fee' => $shippingFee]);

            // Xử lý mã giảm giá
            $coupon = session('coupon') ? Coupon::find(session('coupon')->id) : null;
            $discount = 0;
            if ($coupon && $this->isValidCoupon($coupon, $cartTotal)) {
                $discount = $this->calculateDiscount($coupon, $cartTotal);
            }

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
                'defaultAddress',
                'shippingFee',
                'user',
                'coupon',
                'discount',
                'shippingType'
            ));
        } catch (\Exception $e) {
            Log::error('Checkout Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('shopping-cart.index')->with('error', 'Đã xảy ra lỗi, vui lòng thử lại!');
        }
    }

    public function update(Request $request)
    {
        try {
            $request->validate([
                'selected_address' => 'nullable|exists:user_addresses,id',
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

            // Giữ lại selected_items trong session
            if (!session('selected_items')) {
                $cart = Cart::where('user_id', Auth::id())->first();
                $selectedItems = $cart ? $cart->items->pluck('id')->toArray() : [];
                session(['selected_items' => $selectedItems]);
            }

            return redirect()->route('checkout')->with('success', 'Cập nhật thông tin thành công!');
        } catch (\Exception $e) {
            Log::error('Checkout Update Error: ' . $e->getMessage());
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
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để đặt hàng!');
            }

            \Log::info('Validating checkout form', $request->all());

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
            ]);

            $cart = Cart::where('user_id', Auth::id())->with('items')->first();

            if (!$cart || $cart->items->isEmpty()) {
                \Log::warning('Cart is empty or not found', ['user_id' => Auth::id()]);
                return redirect()->route('shopping-cart.index')->with('error', 'Giỏ hàng trống!');
            }

            $selectedItems = session('selected_items', []);

            $cartItems = $cart->items()->when(!empty($selectedItems), function ($query) use ($selectedItems) {
                return $query->whereIn('id', $selectedItems);
            })->get();

            if ($cartItems->isEmpty()) {
                \Log::warning('No selected items for checkout', ['selected_items' => $selectedItems]);
                return redirect()->route('shopping-cart.index')->with('error', 'Vui lòng chọn ít nhất một sản phẩm để thanh toán!');
            }

            $address = UserAddress::where('id', $request->address_id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            \Log::info('Selected address', $address->toArray());

            $subtotal = $cartItems->sum(function ($item) {
                return ($item->price_at_time ?? $item->product->price ?? 0) * ($item->quantity ?? 0);
            });
            $shippingFee = $request->shipping_type === 'thường' ? 16500 : 30000;

            $coupon = session('coupon') ? Coupon::find(session('coupon')->id) : null;
            $discount = 0;
            if ($coupon && $this->isValidCoupon($coupon, $subtotal)) {
                $discount = $this->calculateDiscount($coupon, $subtotal);
            }
            $total = $subtotal + $shippingFee - $discount;

            // Lưu thông tin checkout vào session để sử dụng sau khi thanh toán
            session([
                'checkout_data' => [
                    'address_id' => $request->address_id,
                    'shipping_type' => $request->shipping_type,
                    'payment_method' => $request->payment_method,
                    'note' => $request->note,
                    'subtotal' => $subtotal,
                    'shipping_fee' => $shippingFee,
                    'discount' => $discount,
                    'total' => $total,
                    'coupon_id' => $coupon ? $coupon->id : null,
                    'selected_items' => $selectedItems,
                    'cart_items' => $cartItems->toArray(),
                ]
            ]);

            if ($request->payment_method === 'cod') {
                // Với COD, tạo đơn hàng ngay lập tức
                return $this->createOrderFromSession();
            } else {
                // Với VNPay, chuyển đến trang thanh toán
                return $this->vnpayPayment($request);
            }
        } catch (\Exception $e) {
            \Log::error('Checkout process error: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('checkout')
                ->withInput()
                ->with('error', 'Đã xảy ra lỗi khi xử lý đơn hàng, vui lòng kiểm tra lại thông tin!');
        }
    }

    protected function sendOrderConfirmationEmail(Order $order)
    {
        try {
            Mail::to($order->email)->send(new OrderConfirmationMail($order));
            Log::info('Order confirmation email sent successfully', [
                'order_id' => $order->id,
                'email' => $order->email,
                'order_code' => $order->code
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send order confirmation email', [
                'order_id' => $order->id,
                'email' => $order->email,
                'order_code' => $order->code,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    protected function createOrderFromSession($shouldRedirect = true)
    {
        try {
            $checkoutData = session('checkout_data');
            if (!$checkoutData) {
                if ($shouldRedirect) {
                    return redirect()->route('checkout')->with('error', 'Thông tin thanh toán không hợp lệ!');
                } else {
                    throw new \Exception('Thông tin thanh toán không hợp lệ!');
                }
            }

            $address = UserAddress::where('id', $checkoutData['address_id'])
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $order = Order::create([
                'user_id' => Auth::id(),
                'code' => Str::upper('ORD-' . Str::random(8)),
                'payment_id' => $checkoutData['payment_method'] === 'cod' ? 1 : 2,
                'phone_number' => $address->phone_number,
                'email' => $address->email,
                'fullname' => $address->fullname,
                'address' => implode(', ', [$address->street, $address->ward, $address->district, $address->province]),
                'city' => $address->province,
                'note' => $checkoutData['note'],
                'total_amount' => $checkoutData['total'],
                'discount_amount' => $checkoutData['discount'],
                'shipping_type' => $checkoutData['shipping_type'],
                'is_paid' => $checkoutData['payment_method'] === 'cod' ? 0 : 0, // COD chưa thanh toán
                'is_refunded' => 0,
                'coupon_id' => $checkoutData['coupon_id'],
                'is_refunded_canceled' => 0,
                'check_refunded_canceled' => 0,
                'img_refunded_money' => null,
            ]);

            \Log::info('Order created', [
                'order_id' => $order->id,
                'code' => $order->code,
                'payment_method' => $checkoutData['payment_method'],
            ]);

            // Tăng usage count cho coupon
            if ($checkoutData['coupon_id']) {
                Coupon::where('id', $checkoutData['coupon_id'])->increment('usage_count');
            }

            // Tạo trạng thái đơn hàng
            OrderOrderStatus::create([
                'order_id' => $order->id,
                'order_status_id' => 1,
                'modified_by' => Auth::id(),
                'note' => 'Đơn hàng mới được tạo, đang chờ xác nhận.',
                'employee_evidence' => null,
                'customer_confirmation' => null,
                'is_current' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Tạo order items
            foreach ($checkoutData['cart_items'] as $itemData) {
                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->product_id = $itemData['product_id'];
                $orderItem->product_variant_id = $itemData['product_variant_id'];
                $orderItem->name = $itemData['product']['name'] ?? 'Sản phẩm ' . $itemData['product_id'];
                $orderItem->price = $itemData['price_at_time'] ?? $itemData['product']['price'] ?? 0;
                $orderItem->quantity = $itemData['quantity'] ?? 0;

                // Lưu thông tin biến thể nếu có
                if (isset($itemData['product_variant']) && $itemData['product_variant']) {
                    $variantAttributes = [];
                    if (isset($itemData['product_variant']['attribute_values'])) {
                        foreach ($itemData['product_variant']['attribute_values'] as $attrValue) {
                            if (isset($attrValue['attribute'])) {
                                $variantAttributes[$attrValue['attribute']['name']] = $attrValue['value'];
                            }
                        }
                    }
                    if (!empty($variantAttributes)) {
                        $orderItem->attributes_variant = json_encode($variantAttributes, JSON_UNESCAPED_UNICODE);
                    }
                    $orderItem->name_variant = $itemData['product_variant']['name'] ?? null;
                    $orderItem->price_variant = $itemData['price_at_time'] ?? $itemData['product_variant']['price'] ?? $itemData['product']['price'];
                }

                $orderItem->save();

                // Trừ tồn kho
                if ($itemData['product_variant_id']) {
                    $variant = ProductVariant::find($itemData['product_variant_id']);
                    if ($variant && $variant->stock > 0) {
                        $variant->stock = max(0, $variant->stock - $itemData['quantity']);
                        $variant->save();
                    }
                }

                $product = Product::find($itemData['product_id']);
                if ($product && $product->stock > 0) {
                    $product->stock = max(0, $product->stock - $itemData['quantity']);
                    $product->save();
                }
            }

            // Xóa cart items
            $cart = Cart::where('user_id', Auth::id())->first();
            if ($cart) {
                $cart->items()->whereIn('id', $checkoutData['selected_items'])->delete();
                if ($cart->items()->count() === 0) {
                    $cart->delete();
                }
            }

            // Gửi email xác nhận đơn hàng
            $order->load(['items.product']); // Load relationship để hiển thị ảnh trong email
            $this->sendOrderConfirmationEmail($order);

            // Xóa session data
            session()->forget(['coupon', 'selected_items', 'checkout_data']);

            if ($shouldRedirect) {
                return redirect()->route('checkout.success', $order->code)
                    ->with('success', 'Đơn hàng của bạn đã được đặt thành công!');
            } else {
                return $order;
            }
        } catch (\Exception $e) {
            \Log::error('Create order from session error: ' . $e->getMessage());
            if ($shouldRedirect) {
                return redirect()->route('checkout')->with('error', 'Đã xảy ra lỗi khi tạo đơn hàng, vui lòng thử lại!');
            } else {
                throw $e;
            }
        }
    }

    protected function vnpayPayment(Request $request)
    {
        try {
            $checkoutData = session('checkout_data');
            if (!$checkoutData) {
                return redirect()->route('checkout')->with('error', 'Thông tin thanh toán không hợp lệ!');
            }

            $vnp_TmnCode = env('VNPAY_TMN_CODE', '3ANN0P8R');
            $vnp_HashSecret = env('VNPAY_HASH_SECRET', '63J7LYXN9JWZ7BRLGXUXYJ7UH5Q1TA6Y');
            $vnp_Url = env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
            $vnp_ReturnUrl = env('VNPAY_RETURN_URL', route('vnpay.return'));

            if (!$vnp_TmnCode || !$vnp_HashSecret) {
                throw new \Exception('Cấu hình VNPay không hợp lệ.');
            }

            // Tạo order code tạm thời cho VNPay
            $tempOrderCode = Str::upper('TEMP-' . Str::random(8));
            
            // Lưu temp order code vào session
            session(['temp_order_code' => $tempOrderCode]);

            $vnp_TxnRef = $tempOrderCode;
            $vnp_OrderInfo = 'Thanh toán đơn hàng ' . $tempOrderCode;
            $vnp_Amount = $checkoutData['total'] * 100;
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
                'vnp_ReturnUrl' => $vnp_ReturnUrl,
                'vnp_TxnRef' => $vnp_TxnRef,
                'vnp_BankCode' => 'NCB',
            ];

            ksort($inputData);
            $query = http_build_query($inputData);
            $vnpSecureHash = hash_hmac('sha512', $query, $vnp_HashSecret);
            $vnp_Url .= '?' . $query . '&vnp_SecureHash=' . $vnpSecureHash;

            Log::info('VNPay Payment URL', ['url' => $vnp_Url, 'temp_order_code' => $tempOrderCode]);
            return redirect($vnp_Url);
        } catch (\Exception $e) {
            Log::error('VNPay Payment Error: ' . $e->getMessage());
            return redirect()->route('checkout')->with('error', 'Lỗi khi tạo thanh toán VNPay: ' . $e->getMessage());
        }
    }

    public function vnpayReturn(Request $request)
    {
        try {
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để hoàn tất thanh toán!');
            }

            $vnp_HashSecret = env('VNPAY_HASH_SECRET', '63J7LYXN9JWZ7BRLGXUXYJ7UH5Q1TA6Y');
            $vnp_SecureHash = $request->vnp_SecureHash;
            $inputData = $request->except(['vnp_SecureHash', 'vnp_SecureHashType']);

            ksort($inputData);
            $query = http_build_query($inputData);
            $secureHash = hash_hmac('sha512', $query, $vnp_HashSecret);

            Log::info('VNPay Return Data', ['data' => $inputData, 'secure_hash' => $vnp_SecureHash, 'calculated_hash' => $secureHash]);

            // Kiểm tra temp order code
            $tempOrderCode = session('temp_order_code');
            if ($tempOrderCode !== $request->vnp_TxnRef) {
                Log::error('VNPay Return: Temp order code mismatch', [
                    'session_temp_code' => $tempOrderCode,
                    'vnpay_txn_ref' => $request->vnp_TxnRef
                ]);
                return redirect()->route('checkout')->with('error', 'Mã đơn hàng không hợp lệ!');
            }

            if ($secureHash === $vnp_SecureHash) {
                if ($request->vnp_ResponseCode === '00') {
                    // Thanh toán thành công, tạo đơn hàng
                    $order = $this->createOrderFromSession(false);

                    // Cập nhật trạng thái thanh toán
                    $order->update(['is_paid' => 1]);

                    // Gửi email xác nhận đơn hàng
                    $order->load(['items.product']); // Load relationship để hiển thị ảnh trong email
                    $this->sendOrderConfirmationEmail($order);

                    PaymentLog::create([
                        'order_id' => $order->id,
                        'txn_ref' => $request->vnp_TxnRef,
                        'response_code' => $request->vnp_ResponseCode,
                        'transaction_no' => $request->vnp_TransactionNo ?? null,
                        'amount' => $request->vnp_Amount / 100,
                        'bank_code' => $request->vnp_BankCode ?? null,
                        'response_data' => json_encode($inputData),
                    ]);

                    OrderStatusHistory::where('order_id', $order->id)->update(['is_current' => 0]);
                    OrderStatusHistory::create([
                        'order_id' => $order->id,
                        'order_status_id' => 2, // Chờ lấy hàng
                        'modifier_id' => Auth::id() ?? 1,
                        'note' => 'Thanh toán VNPay thành công: ' . ($request->vnp_Amount / 100) . ' VND',
                        'is_current' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Xóa temp order code
                    session()->forget('temp_order_code');

                    return redirect()->route('checkout.success', $order->code)
                        ->with('success', 'Thanh toán VNPay thành công!');
                } else {
                    // Thanh toán thất bại
                    Log::error('VNPay Return: Payment failed', ['response_code' => $request->vnp_ResponseCode]);
                    
                    // Xóa temp order code
                    session()->forget('temp_order_code');
                    
                    return redirect()->route('checkout')->with('error', 'Thanh toán VNPay thất bại với mã lỗi: ' . $request->vnp_ResponseCode);
                }
            }

            Log::error('VNPay Return: Invalid secure hash');
            return redirect()->route('checkout')->with('error', 'Lỗi xác thực thanh toán VNPay!');
        } catch (\Exception $e) {
            Log::error('VNPay Return Error: ' . $e->getMessage());
            return redirect()->route('checkout')->with('error', 'Lỗi xử lý thanh toán VNPay: ' . $e->getMessage());
        }
    }

    public function vnpayCallback(Request $request)
    {
        try {
            $vnp_HashSecret = env('VNPAY_HASH_SECRET', '63J7LYXN9JWZ7BRLGXUXYJ7UH5Q1TA6Y');
            $vnp_SecureHash = $request->vnp_SecureHash;
            $inputData = $request->except(['vnp_SecureHash', 'vnp_SecureHashType']);

            ksort($inputData);
            $query = http_build_query($inputData);
            $secureHash = hash_hmac('sha512', $query, $vnp_HashSecret);

            Log::info('VNPay Callback Data', ['data' => $inputData, 'secure_hash' => $vnp_SecureHash, 'calculated_hash' => $secureHash]);

            if ($secureHash !== $vnp_SecureHash) {
                Log::error('VNPay Callback: Invalid secure hash');
                return response()->json(['RspCode' => '97', 'Message' => 'Invalid Signature']);
            }

            // Kiểm tra temp order code
            $tempOrderCode = session('temp_order_code');
            if ($tempOrderCode !== $request->vnp_TxnRef) {
                Log::error('VNPay Callback: Temp order code mismatch', [
                    'session_temp_code' => $tempOrderCode,
                    'vnpay_txn_ref' => $request->vnp_TxnRef
                ]);
                return response()->json(['RspCode' => '01', 'Message' => 'Order not found']);
            }

            \DB::beginTransaction();

            if ($request->vnp_TransactionType === '02' || $request->vnp_TransactionType === '03') {
                // Xử lý hoàn tiền - cần tìm đơn hàng đã tồn tại
                $order = Order::where('code', 'LIKE', 'ORD-%')->first();
                if (!$order) {
                    Log::error('VNPay Callback: Order not found for refund', ['txn_ref' => $request->vnp_TxnRef]);
                    \DB::rollBack();
                    return response()->json(['RspCode' => '01', 'Message' => 'Order not found for refund']);
                }

                $validTransitions = [
                    1 => [2, 8],       // Chờ xác nhận → Chờ lấy hàng, Đã hủy
                    2 => [9, 8],       // Chờ lấy hàng → Gửi hàng, Đã hủy
                    9 => [3, 8],       // Gửi hàng → Đang giao, Đã hủy
                    3 => [4, 5, 8],    // Đang giao → Giao hàng thành công, Chờ trả hàng, Đã hủy
                    5 => [6, 8],       // Chờ trả hàng → Đã trả hàng, Đã hủy
                    6 => [7],          // Đã trả hàng → Hoàn tiền
                    4 => [7],          // Giao hàng thành công → Hoàn tiền
                    7 => [],           // Hoàn tiền → Dừng
                    8 => []            // Đã hủy → Dừng
                ];

                $currentStatus = $order->statusHistory()->where('is_current', true)->first();
                $from = $currentStatus?->order_status_id ?? 1;

                $to = 7; // Hoàn tiền
                if (!in_array($to, $validTransitions[$from] ?? [])) {
                    Log::warning('VNPay Callback: Invalid status transition for refund, proceeding with warning', [
                        'order_id' => $order->id,
                        'from' => $from,
                        'to' => $to
                    ]);
                }

                if ($request->vnp_ResponseCode === '00') {
                    $order->update([
                        'is_refunded' => 1,
                        'check_refunded_canceled' => 0,
                        'is_refunded_canceled' => 0,
                    ]);

                    OrderStatusHistory::where('order_id', $order->id)->update(['is_current' => 0]);
                    OrderStatusHistory::create([
                        'order_id' => $order->id,
                        'order_status_id' => 7,
                        'modifier_id' => 1,
                        'note' => 'Hoàn tiền VNPay thành công: ' . ($request->vnp_Amount / 100) . ' VND',
                        'is_current' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    PaymentLog::create([
                        'order_id' => $order->id,
                        'txn_ref' => $request->vnp_TxnRef,
                        'response_code' => $request->vnp_ResponseCode,
                        'transaction_no' => $request->vnp_TransactionNo ?? null,
                        'amount' => $request->vnp_Amount / 100,
                        'bank_code' => $request->vnp_BankCode ?? null,
                        'response_data' => json_encode($inputData),
                    ]);

                    Log::info('VNPay Callback: Refund successful', ['order_id' => $order->id]);
                    \DB::commit();
                    return response()->json(['RspCode' => '00', 'Message' => 'Refund Success']);
                } else {
                    OrderStatusHistory::where('order_id', $order->id)->update(['is_current' => 0]);
                    OrderStatusHistory::create([
                        'order_id' => $order->id,
                        'order_status_id' => 8,
                        'modifier_id' => 1,
                        'note' => 'Hoàn tiền VNPay thất bại: ' . ($request->vnp_ResponseCode ?? 'Unknown error'),
                        'is_current' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    Log::error('VNPay Callback: Refund failed', ['order_id' => $order->id, 'response_code' => $request->vnp_ResponseCode]);
                    \DB::commit();
                    return response()->json(['RspCode' => $request->vnp_ResponseCode, 'Message' => 'Refund Failed']);
                }
            } else {
                // Xử lý thanh toán
                if ($request->vnp_ResponseCode === '00') {
                    // Thanh toán thành công, tạo đơn hàng từ session
                    $order = $this->createOrderFromSession(false);
                    
                    // Cập nhật trạng thái thanh toán
                    $order->update(['is_paid' => 1]);

                    // Gửi email xác nhận đơn hàng
                    $order->load(['items.product']); // Load relationship để hiển thị ảnh trong email
                    $this->sendOrderConfirmationEmail($order);

                    OrderStatusHistory::where('order_id', $order->id)->update(['is_current' => 0]);
                    OrderStatusHistory::create([
                        'order_id' => $order->id,
                        'order_status_id' => 2,
                        'modifier_id' => 1,
                        'note' => 'Thanh toán VNPay thành công: ' . ($request->vnp_Amount / 100) . ' VND',
                        'is_current' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    PaymentLog::create([
                        'order_id' => $order->id,
                        'txn_ref' => $request->vnp_TxnRef,
                        'response_code' => $request->vnp_ResponseCode,
                        'transaction_no' => $request->vnp_TransactionNo ?? null,
                        'amount' => $request->vnp_Amount / 100,
                        'bank_code' => $request->vnp_BankCode ?? null,
                        'response_data' => json_encode($inputData),
                    ]);

                    // Xóa temp order code
                    session()->forget('temp_order_code');

                    Log::info('VNPay Callback: Payment successful', ['order_id' => $order->id]);
                    \DB::commit();
                    return response()->json(['RspCode' => '00', 'Message' => 'Payment Success']);
                } else {
                    // Thanh toán thất bại
                    Log::error('VNPay Callback: Payment failed', ['response_code' => $request->vnp_ResponseCode]);
                    
                    // Xóa temp order code
                    session()->forget('temp_order_code');
                    
                    \DB::commit();
                    return response()->json(['RspCode' => $request->vnp_ResponseCode, 'Message' => 'Payment Failed']);
                }
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('VNPay Callback Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['RspCode' => '99', 'Message' => 'Callback processing failed']);
        }
    }

    public function success($order_number)
    {
        try {
            $order = Order::where('user_id', Auth::id())->where('code', $order_number)->firstOrFail();
            return view('client.checkout-success', compact('order'));
        } catch (\Exception $e) {
            Log::error('Checkout Success Error: ' . $e->getMessage());
            return redirect()->route('shopping-cart.index')->with('error', 'Không tìm thấy đơn hàng!');
        }
    }
}
