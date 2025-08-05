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
            $cart = Cart::where('user_id', Auth::id())->with(['items.product', 'items.productVariant'])->first();

            if (!$cart) {
                \Log::warning('Cart not found', ['user_id' => Auth::id()]);
                return redirect()->route('shopping-cart.index')->with('error', 'Giỏ hàng không tồn tại!');
            }

            // Lấy selected_items từ session, sử dụng query string như dự phòng
            $selectedItems = session('selected_items', $request->query('selected_items') ? json_decode($request->query('selected_items'), true) : []);

            // Kiểm tra selected_items là mảng hợp lệ và không rỗng
            if (!is_array($selectedItems) || empty($selectedItems)) {
                \Log::warning('No valid selected items', ['selected_items' => $selectedItems, 'query' => $request->query('selected_items')]);
                return redirect()->route('shopping-cart.index')->with('error', 'Vui lòng chọn ít nhất một sản phẩm để thanh toán!');
            }

            // Lưu selected_items vào session
            session(['selected_items' => $selectedItems]);

            // Lọc cart items dựa trên selected_items
            $cartItems = $cart->items()->whereIn('id', $selectedItems)->get();

            if ($cartItems->isEmpty()) {
                \Log::warning('No matching cart items for selected_items', ['selected_items' => $selectedItems]);
                return redirect()->route('shopping-cart.index')->with('error', 'Các sản phẩm được chọn không hợp lệ hoặc đã bị xóa!');
            }

            // Tính tổng giá trị giỏ hàng
            $cartTotal = $cartItems->sum(function ($item) {
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

            \Log::info('Checkout Summary', [
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
            \Log::error('Checkout Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
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

            $order = Order::create([
                'user_id' => Auth::id(),
                'code' => Str::upper('ORD-' . Str::random(8)),
                'payment_id' => $request->payment_method === 'cod' ? 1 : 2,
                'phone_number' => $address->phone_number,
                'email' => $address->email,
                'fullname' => $address->fullname,
                'address' => implode(', ', [$address->street, $address->ward, $address->district, $address->province]),
                'city' => $address->province,
                'note' => $request->note,
                'total_amount' => $total,
                'shipping_type' => $request->shipping_type,
                'is_paid' => 0,
                'is_refunded' => 0,
                'coupon_id' => $coupon ? $coupon->id : null,
                'is_refunded_canceled' => 0,
                'check_refunded_canceled' => 0,
                'img_refunded_money' => null,
            ]);

            \Log::info('Order created', ['order_id' => $order->id, 'code' => $order->code, 'address' => $order->address, 'city' => $order->city]);

            if ($coupon) {
                $coupon->increment('usage_count');
            }

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

            foreach ($cartItems as $item) {
                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->product_id = $item->product_id;
                $orderItem->product_variant_id = $item->product_variant_id;
                $orderItem->name = $item->product->name ?? 'Sản phẩm ' . $item->product_id;
                $orderItem->price = $item->price_at_time ?? $item->product->price ?? 0;
                $orderItem->quantity = $item->quantity ?? 0;

                // Lưu thông tin biến thể nếu có
                if ($item->productVariant) {
                    $variantAttributes = [];
                    // Lấy thông tin thuộc tính từ biến thể
                    if ($item->productVariant->attributeValues) {
                        foreach ($item->productVariant->attributeValues as $attrValue) {
                            if ($attrValue->attribute) {
                                $variantAttributes[$attrValue->attribute->name] = $attrValue->value;
                            }
                        }
                    }
                    // Lưu dưới dạng JSON vào cột attributes_variant
                    if (!empty($variantAttributes)) {
                        $orderItem->attributes_variant = json_encode($variantAttributes, JSON_UNESCAPED_UNICODE);
                        \Log::info('Saving variant attributes:', [
                            'attributes' => $variantAttributes,
                            'json' => $orderItem->attributes_variant
                        ]);
                    } else {
                        \Log::warning('No variant attributes to save for variant', [
                            'variant_id' => $item->productVariant->id
                        ]);
                    }
                    // Lưu tên và giá biến thể
                    $orderItem->name_variant = $item->productVariant->name ?? null;
                    $orderItem->price_variant = $item->price_at_time ?? $item->productVariant->price ?? $item->product->price;
                } else {
                    \Log::info('No variant found for item', ['item_id' => $item->id]);
                }

                $orderItem->save();

                // 🔻 Trừ tồn kho biến thể (nếu có)
                if ($item->product_variant_id) {
                    $variant = ProductVariant::find($item->product_variant_id);
                    if ($variant && $variant->stock > 0) {
                        $variant->stock = max(0, $variant->stock - $item->quantity);
                        $variant->save();
                    }
                }

                // 🔻 Trừ tồn kho sản phẩm gốc
                $product = Product::find($item->product_id);
                if ($product && $product->stock > 0) {
                    $product->stock = max(0, $product->stock - $item->quantity);
                    $product->save();
                }
            }

            foreach ($cartItems as $item) {
                $item->delete();
            }

            if ($cart->items()->count() === 0) {
                $cart->delete();
            }
            if ($cart->items()->count() === 0) {
                $cart->delete();
            }
            session()->forget(['coupon', 'selected_items']);

            if ($request->payment_method === 'cod') {
                return redirect()->route('checkout.success', $order->code)
                    ->with('success', 'Đơn hàng của bạn đã được đặt thành công!');
            } else {
                return $this->vnpayPayment($order);
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

    protected function vnpayPayment(Order $order)
    {
        try {
            $vnp_TmnCode = '3ANN0P8R';
            $vnp_HashSecret = '63J7LYXN9JWZ7BRLGXUXYJ7UH5Q1TA6Y';
            if (!$vnp_TmnCode || !$vnp_HashSecret) {
                throw new \Exception('Cấu hình VNPay không hợp lệ.');
            }

            $vnp_Url = 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html';
            $vnp_Returnurl = route('vnpay.return');

            $vnp_TxnRef = $order->code;
            $vnp_OrderInfo = 'Thanh toán đơn hàng ' . $order->code;
            $vnp_Amount = $order->total_amount * 100;
            Log::info('VNPay Amount', ['vnp_Amount' => $vnp_Amount]);
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

            Log::info('VNPay URL: ' . $vnp_Url);
            return redirect($vnp_Url);
        } catch (\Exception $e) {
            Log::error('VNPay error: ' . $e->getMessage());
            return redirect()->route('checkout')
                ->withInput()
                ->with('error', 'Lỗi khi tạo thanh toán VNPay: ' . $e->getMessage());
        }
    }

    public function vnpayReturn(Request $request)
    {
        try {
            if (!Auth::check()) {
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

            Log::info('VNPay return data: ' . json_encode($inputData));
            Log::info('VNPay secure hash: ' . $vnp_SecureHash . ' | Calculated: ' . $secureHash);

            $order = Order::where('code', $request->vnp_TxnRef)->first();

            if ($secureHash === $vnp_SecureHash && $order) {
                if ($request->vnp_ResponseCode === '00') {
                    $order->update(['is_paid' => 1]);

                    PaymentLog::create([
                        'order_id' => $order->id,
                        'txn_ref' => $request->vnp_TxnRef,
                        'response_code' => $request->vnp_ResponseCode,
                        'transaction_no' => $request->vnp_TransactionNo ?? null,
                        'amount' => $request->vnp_Amount / 100,
                        'bank_code' => $request->vnp_BankCode ?? null,
                        'response_data' => json_encode($inputData),
                    ]);

                    OrderOrderStatus::create([
                        'order_id' => $order->id,
                        'order_status_id' => 2,
                        'modified_by' => Auth::id(),
                        'note' => 'Thanh toán VNPay thành công',
                        'employee_evidence' => null,
                        'customer_confirmation' => null,
                        'is_current' => 1,
                    ]);

                    return redirect()->route('checkout.success', $order->code)
                        ->with('success', 'Thanh toán VNPay thành công!');
                } else {
                    $order->update(['is_paid' => 0]);

                    Log::error('VNPay failed: ResponseCode = ' . $request->vnp_ResponseCode);
                    return redirect()->route('checkout')
                        ->withInput()
                        ->with('error', 'Thanh toán VNPay thất bại với mã lỗi: ' . $request->vnp_ResponseCode);
                }
            }

            Log::error('VNPay verification failed: Invalid secure hash or order not found');
            return redirect()->route('checkout')
                ->withInput()
                ->with('error', 'Lỗi xác thực thanh toán VNPay!');
        } catch (\Exception $e) {
            Log::error('VNPay return error: ' . $e->getMessage());
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
            Log::error('Checkout success error: ' . $e->getMessage());
            return redirect()->route('shopping-cart.index')->with('error', 'Không tìm thấy đơn hàng!');
        }
    }
}
