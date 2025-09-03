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
use Illuminate\Support\Carbon;
use App\Models\OrderOrderStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
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

            // Tính phí vận chuyển dựa trên địa chỉ (copy logic từ update)
            $addressId = session('checkout_address_id', $defaultAddress->id ?? null);
            $selectedAddress = $addressId ? UserAddress::find($addressId) : null;
            $destinationProvince = $selectedAddress ? $selectedAddress->province : 'Hà Nội';
            $normalizedProvince = preg_replace('/^(Thành phố|Tỉnh)\s+/', '', $destinationProvince);

            // Define regions (copy từ update)
            $northernProvinces = ['Hà Nội', 'Bắc Ninh', 'Hưng Yên', 'Hải Dương', 'Hải Phòng', 'Quảng Ninh', 'Bắc Giang', 'Phú Thọ', 'Vĩnh Phúc', 'Ninh Bình', 'Thái Bình', 'Nam Định', 'Hà Nam', 'Hòa Bình', 'Sơn La', 'Điện Biên', 'Lai Châu', 'Lào Cai', 'Yên Bái', 'Tuyên Quang', 'Hà Giang', 'Cao Bằng', 'Bắc Kạn', 'Lạng Sơn', 'Thái Nguyên'];
            $centralProvinces = ['Thanh Hóa', 'Nghệ An', 'Hà Tĩnh', 'Quảng Bình', 'Quảng Trị', 'Thừa Thiên Huế', 'Đà Nẵng', 'Quảng Nam', 'Quảng Ngãi', 'Bình Định', 'Phú Yên', 'Khánh Hòa', 'Ninh Thuận', 'Bình Thuận', 'Kon Tum', 'Gia Lai', 'Đắk Lắk', 'Đắk Nông', 'Lâm Đồng'];
            $southernProvinces = ['Hồ Chí Minh', 'Bình Dương', 'Đồng Nai', 'Bà Rịa - Vũng Tàu', 'Long An', 'Tiền Giang', 'Bến Tre', 'Trà Vinh', 'Vĩnh Long', 'Đồng Tháp', 'An Giang', 'Kiên Giang', 'Cần Thơ', 'Hậu Giang', 'Sóc Trăng', 'Bạc Liêu', 'Cà Mau', 'Bình Phước', 'Tây Ninh'];

            $region = 'northern';
            if (in_array($normalizedProvince, $centralProvinces)) {
                $region = 'central';
            } elseif (in_array($normalizedProvince, $southernProvinces)) {
                $region = 'southern';
            }

            $normalShippingFee = 16500;
            $fastShippingFee = match ($region) {
                'northern' => $normalizedProvince === 'Hà Nội' ? 30000 : 40000,
                'central' => 50000,
                'southern' => 60000,
            };

            $shippingFee = $shippingType === 'nhanh' ? $fastShippingFee : $normalShippingFee;

            $normalShippingDates = match ($region) {
                'northern' => \Carbon\Carbon::today()->addDays($normalizedProvince === 'Hà Nội' ? 1 : 2)->format('d/m/Y') . ' - ' .
                    \Carbon\Carbon::today()->addDays($normalizedProvince === 'Hà Nội' ? 2 : 4)->format('d/m/Y'),
                'central' => \Carbon\Carbon::today()->addDays(3)->format('d/m/Y') . ' - ' .
                    \Carbon\Carbon::today()->addDays(5)->format('d/m/Y'),
                'southern' => \Carbon\Carbon::today()->addDays(4)->format('d/m/Y') . ' - ' .
                    \Carbon\Carbon::today()->addDays(6)->format('d/m/Y'),
            };

            $fastShippingDates = match ($region) {
                'northern' => $normalizedProvince === 'Hà Nội'
                    ? 'Trong 4 giờ nếu đặt trước 16:00'
                    : \Carbon\Carbon::today()->addDay()->format('d/m/Y'),
                'central' => \Carbon\Carbon::today()->addDays(1)->format('d/m/Y') . ' - ' .
                    \Carbon\Carbon::today()->addDays(2)->format('d/m/Y'),
                'southern' => \Carbon\Carbon::today()->addDays(2)->format('d/m/Y') . ' - ' .
                    \Carbon\Carbon::today()->addDays(3)->format('d/m/Y'),
            };

            // Lưu đầy đủ session
            session([
                'payment_method' => $paymentMethod,
                'shipping_type' => $shippingType,
                'shipping_fee' => $shippingFee,
                'normal_shipping_fee' => $normalShippingFee,
                'fast_shipping_fee' => $fastShippingFee,
                'normal_shipping_dates' => $normalShippingDates,
                'fast_shipping_dates' => $fastShippingDates,
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

            // Get the selected address or fallback to session/default
            $selectedAddressId = $request->input('selected_address', session('checkout_address_id'));
            $selectedAddress = $selectedAddressId ? \App\Models\UserAddress::find($selectedAddressId) : null; // Sử dụng find() để tránh exception
            $destinationProvince = $selectedAddress ? $selectedAddress->province : 'Hà Nội';
            $shopProvince = 'Hà Nội';

            // Normalize province name (remove "Thành phố" or "Tỉnh" for matching)
            $normalizedProvince = preg_replace('/^(Thành phố|Tỉnh)\s+/', '', $destinationProvince);

            // Log for debugging
            Log::info('Selected Address', [
                'selected_address_id' => $selectedAddressId,
                'destination_province' => $destinationProvince,
                'normalized_province' => $normalizedProvince,
                'session_id' => Session::getId()
            ]);

            // Define Vietnam's regions (normalized names)
            $northernProvinces = ['Hà Nội', 'Bắc Ninh', 'Hưng Yên', 'Hải Dương', 'Hải Phòng', 'Quảng Ninh', 'Bắc Giang', 'Phú Thọ', 'Vĩnh Phúc', 'Ninh Bình', 'Thái Bình', 'Nam Định', 'Hà Nam', 'Hòa Bình', 'Sơn La', 'Điện Biên', 'Lai Châu', 'Lào Cai', 'Yên Bái', 'Tuyên Quang', 'Hà Giang', 'Cao Bằng', 'Bắc Kạn', 'Lạng Sơn', 'Thái Nguyên'];
            $centralProvinces = ['Thanh Hóa', 'Nghệ An', 'Hà Tĩnh', 'Quảng Bình', 'Quảng Trị', 'Thừa Thiên Huế', 'Đà Nẵng', 'Quảng Nam', 'Quảng Ngãi', 'Bình Định', 'Phú Yên', 'Khánh Hòa', 'Ninh Thuận', 'Bình Thuận', 'Kon Tum', 'Gia Lai', 'Đắk Lắk', 'Đắk Nông', 'Lâm Đồng'];
            $southernProvinces = ['Hồ Chí Minh', 'Bình Dương', 'Đồng Nai', 'Bà Rịa - Vũng Tàu', 'Long An', 'Tiền Giang', 'Bến Tre', 'Trà Vinh', 'Vĩnh Long', 'Đồng Tháp', 'An Giang', 'Kiên Giang', 'Cần Thơ', 'Hậu Giang', 'Sóc Trăng', 'Bạc Liêu', 'Cà Mau', 'Bình Phước', 'Tây Ninh'];

            // Determine region
            $region = 'northern';
            if (in_array($normalizedProvince, $centralProvinces)) {
                $region = 'central';
            } elseif (in_array($normalizedProvince, $southernProvinces)) {
                $region = 'southern';
            }

            // Log region for debugging
            Log::info('Region Determined', [
                'region' => $region,
                'normalized_province' => $normalizedProvince,
                'session_id' => Session::getId()
            ]);

            // Calculate shipping fee and delivery dates
            $shippingType = $request->input('shipping_type', session('shipping_type', 'thường'));
            $normalShippingFee = 16500;
            $fastShippingFee = match ($region) {
                'northern' => $normalizedProvince === 'Hà Nội' ? 30000 : 40000,
                'central' => 50000,
                'southern' => 60000,
            };

            $shippingFee = $shippingType === 'nhanh' ? $fastShippingFee : $normalShippingFee;

            $normalShippingDates = match ($region) {
                'northern' => \Carbon\Carbon::today()->addDays($normalizedProvince === 'Hà Nội' ? 1 : 2)->format('d/m/Y') . ' - ' .
                    \Carbon\Carbon::today()->addDays($normalizedProvince === 'Hà Nội' ? 2 : 4)->format('d/m/Y'),
                'central' => \Carbon\Carbon::today()->addDays(3)->format('d/m/Y') . ' - ' .
                    \Carbon\Carbon::today()->addDays(5)->format('d/m/Y'),
                'southern' => \Carbon\Carbon::today()->addDays(4)->format('d/m/Y') . ' - ' .
                    \Carbon\Carbon::today()->addDays(6)->format('d/m/Y'),
            };

            $fastShippingDates = match ($region) {
                'northern' => $normalizedProvince === 'Hà Nội'
                    ? 'Trong 4 giờ nếu đặt trước 16:00'
                    : \Carbon\Carbon::today()->addDay()->format('d/m/Y'),
                'central' => \Carbon\Carbon::today()->addDays(1)->format('d/m/Y') . ' - ' .
                    \Carbon\Carbon::today()->addDays(2)->format('d/m/Y'),
                'southern' => \Carbon\Carbon::today()->addDays(2)->format('d/m/Y') . ' - ' .
                    \Carbon\Carbon::today()->addDays(3)->format('d/m/Y'),
            };

            // Store in session
            session([
                'checkout_address_id' => $selectedAddressId,
                'shipping_type' => $shippingType,
                'payment_method' => $request->input('payment_method', session('payment_method', 'cod')),
                'note' => $request->input('note', session('note', '')),
                'shipping_fee' => $shippingFee,
                'normal_shipping_fee' => $normalShippingFee,
                'fast_shipping_fee' => $fastShippingFee,
                'normal_shipping_dates' => $normalShippingDates,
                'fast_shipping_dates' => $fastShippingDates,
            ]);

            // Log checkout update
            Log::info('Checkout updated', [
                'checkout_address_id' => session('checkout_address_id'),
                'shipping_type' => session('shipping_type'),
                'payment_method' => session('payment_method'),
                'note' => session('note'),
                'shipping_fee' => $shippingFee,
                'normal_shipping_fee' => $normalShippingFee,
                'fast_shipping_fee' => $fastShippingFee,
                'normal_shipping_dates' => $normalShippingDates,
                'fast_shipping_dates' => $fastShippingDates,
                'selected_items' => session('selected_items'),
                'session_id' => Session::getId()
            ]);

            // Return JSON for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'shipping_fee' => $shippingFee, // Thêm để script sử dụng
                    'shipping_type' => $shippingType, // Thêm để script tính đúng
                    'normal_shipping_fee' => $normalShippingFee,
                    'fast_shipping_fee' => $fastShippingFee,
                    'normal_shipping_dates' => $normalShippingDates,
                    'fast_shipping_dates' => $fastShippingDates,
                    'selected_address' => [
                        'id' => $selectedAddressId,
                        'fullname' => $selectedAddress ? ($selectedAddress->fullname ?? 'Chưa cung cấp họ tên') : 'Chưa chọn',
                        'phone_number' => $selectedAddress && $selectedAddress->phone_number && preg_match('/^0[0-9]{9}$/', $selectedAddress->phone_number) ? $selectedAddress->phone_number : 'Số điện thoại không hợp lệ',
                        'address' => $selectedAddress ? (($selectedAddress->street ? $selectedAddress->street . ', ' : '') . ($selectedAddress->ward ? $selectedAddress->ward . ', ' : '') . ($selectedAddress->district ? $selectedAddress->district . ', ' : '') . ($selectedAddress->province ?? 'Chưa cung cấp tỉnh/thành phố')) : 'Chưa chọn địa chỉ'
                    ]
                ]);
            }

            return redirect()->route('checkout')->with('success', 'Cập nhật thông tin thành công!');
        } catch (\Exception $e) {
            Log::error('Checkout Update Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'session_id' => Session::getId()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Đã xảy ra lỗi khi cập nhật, vui lòng thử lại!'
                ], 500);
            }

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

    /**
     * Hiển thị form tạo địa chỉ mới
     */
    public function createAddress()
    {
        try {
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục!');
            }
            $user = Auth::user();
            $address = null; // Truyền $address là null cho trường hợp tạo mới
            return view('client.address.create', compact('user', 'address'));
        } catch (\Exception $e) {
            Log::error('Create address error: ' . $e->getMessage(), ['session_id' => Session::getId()]);
            return redirect()->route('checkout')->with('error', 'Đã xảy ra lỗi, vui lòng thử lại!');
        }
    }

    /**
     * Lưu địa chỉ mới
     */
    public function storeAddress(Request $request)
    {
        try {
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục!');
            }

            $user = Auth::user();

            // Validate thủ công
            $validator = Validator::make($request->all(), [
                'fullname' => 'required|regex:/^[A-Za-z\sÀ-ỹ]{2,255}$/',
                'phone_number' => 'required|regex:/^0[35789][0-9]{8}$/',
                'email' => 'required|email|max:255',
                'province' => 'required|string|max:100|regex:/^[A-Za-z\sÀ-ỹ]{2,100}$/',
                'district' => 'required|string|max:100|regex:/^[A-Za-z\sÀ-ỹ]{2,100}$/',
                'ward' => 'required|string|max:100|regex:/^[A-Za-z\sÀ-ỹ]{2,100}$/',
                'street' => 'required|string|max:255|regex:/^[A-Za-z0-9\s,À-ỹ]{5,255}$/',
                'address' => 'required|string|max:500',
                'address_type' => 'required|in:home,office',
                'is_default' => 'nullable|boolean',
            ], [
                'fullname.required' => 'Vui lòng nhập họ và tên.',
                'fullname.regex' => 'Họ và tên chỉ chứa chữ cái và dấu cách, từ 2 đến 255 ký tự.',
                'phone_number.required' => 'Vui lòng nhập số điện thoại.',
                'phone_number.regex' => 'Số điện thoại phải bắt đầu bằng 0, theo sau là 9 chữ số (bắt đầu bằng 3, 5, 7, 8, hoặc 9).',
                'email.required' => 'Vui lòng nhập email.',
                'email.email' => 'Email không đúng định dạng.',
                'province.required' => 'Vui lòng chọn tỉnh/thành phố.',
                'province.regex' => 'Tỉnh/Thành phố không hợp lệ.',
                'district.required' => 'Vui lòng chọn quận/huyện.',
                'district.regex' => 'Quận/Huyện không hợp lệ.',
                'ward.required' => 'Vui lòng chọn phường/xã.',
                'ward.regex' => 'Phường/Xã không hợp lệ.',
                'street.required' => 'Vui lòng nhập địa chỉ cụ thể.',
                'street.regex' => 'Địa chỉ cụ thể chứa chữ cái, số, dấu cách hoặc dấu phẩy, từ 5 đến 255 ký tự.',
                'address.required' => 'Địa chỉ đầy đủ không được để trống.',
                'address_type.required' => 'Vui lòng chọn loại địa chỉ.',
                'address_type.in' => 'Loại địa chỉ không hợp lệ.',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

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

            // Xử lý địa chỉ mặc định
            if ($request->is_default) {
                UserAddress::where('user_id', $user->id)->update(['is_default' => 0]);
            }

            // Đảm bảo is_default là 0 nếu không được chọn
            $data['is_default'] = $request->is_default ? 1 : 0;

            $address = UserAddress::create($data);
            session(['checkout_address_id' => $address->id]);

            return redirect()->route('checkout')->with('success', 'Địa chỉ đã được thêm thành công!');
        } catch (\Exception $e) {
            Log::error('Store address error: ' . $e->getMessage(), ['session_id' => Session::getId()]);
            return back()->withInput()->with('error', 'Đã xảy ra lỗi khi thêm địa chỉ, vui lòng thử lại!');
        }
    }

    /**
     * Hiển thị form chỉnh sửa địa chỉ
     */
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

    /**
     * Lưu thông tin chỉnh sửa địa chỉ
     */
    public function saveAddress(Request $request)
    {
        try {
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục!');
            }

            $user = Auth::user();

            // Validate thủ công
            $validator = Validator::make($request->all(), [
                'address_id' => 'required|exists:user_addresses,id',
                'fullname' => 'required|regex:/^[A-Za-z\sÀ-ỹ]{2,255}$/',
                'phone_number' => 'required|regex:/^0[35789][0-9]{8}$/',
                'email' => 'required|email|max:255',
                'province' => 'required|string|max:100|regex:/^[A-Za-z\sÀ-ỹ]{2,100}$/',
                'district' => 'required|string|max:100|regex:/^[A-Za-z\sÀ-ỹ]{2,100}$/',
                'ward' => 'required|string|max:100|regex:/^[A-Za-z\sÀ-ỹ]{2,100}$/',
                'street' => 'required|string|max:255|regex:/^[A-Za-z0-9\s,À-ỹ]{5,255}$/',
                'address' => 'required|string|max:500',
                'address_type' => 'required|in:home,office',
                'is_default' => 'nullable|boolean',
            ], [
                'address_id.required' => 'ID địa chỉ không được để trống.',
                'address_id.exists' => 'Địa chỉ không tồn tại.',
                'fullname.required' => 'Vui lòng nhập họ và tên.',
                'fullname.regex' => 'Họ và tên chỉ chứa chữ cái và dấu cách, từ 2 đến 255 ký tự.',
                'phone_number.required' => 'Vui lòng nhập số điện thoại.',
                'phone_number.regex' => 'Số điện thoại phải bắt đầu bằng 0, theo sau là 9 chữ số (bắt đầu bằng 3, 5, 7, 8, hoặc 9).',
                'email.required' => 'Vui lòng nhập email.',
                'email.email' => 'Email không đúng định dạng.',
                'province.required' => 'Vui lòng chọn tỉnh/thành phố.',
                'province.regex' => 'Tỉnh/Thành phố không hợp lệ.',
                'district.required' => 'Vui lòng chọn quận/huyện.',
                'district.regex' => 'Quận/Huyện không hợp lệ.',
                'ward.required' => 'Vui lòng chọn phường/xã.',
                'ward.regex' => 'Phường/Xã không hợp lệ.',
                'street.required' => 'Vui lòng nhập địa chỉ cụ thể.',
                'street.regex' => 'Địa chỉ cụ thể chứa chữ cái, số, dấu cách hoặc dấu phẩy, từ 5 đến 255 ký tự.',
                'address.required' => 'Địa chỉ đầy đủ không được để trống.',
                'address_type.required' => 'Vui lòng chọn loại địa chỉ.',
                'address_type.in' => 'Loại địa chỉ không hợp lệ.',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

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

            // Xử lý địa chỉ mặc định
            if ($request->is_default) {
                UserAddress::where('user_id', $user->id)->update(['is_default' => 0]);
            }

            // Đảm bảo is_default là 0 nếu không được chọn
            $data['is_default'] = $request->is_default ? 1 : 0;

            UserAddress::where('id', $request->address_id)
                ->where('user_id', $user->id)
                ->update($data);

            return redirect()->route('checkout')->with('success', 'Địa chỉ đã được cập nhật!');
        } catch (\Exception $e) {
            Log::error('Save address error: ' . $e->getMessage(), ['session_id' => Session::getId()]);
            return back()->withInput()->with('error', 'Đã xảy ra lỗi khi cập nhật địa chỉ, vui lòng thử lại!');
        }
    }
    public function deleteAddress($id)
    {
        try {
            if (!Auth::check()) {
                return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập để xóa địa chỉ!'], 401);
            }

            $address = UserAddress::where('id', $id)
                ->where('user_id', Auth::user()->id)
                ->first();

            if (!$address) {
                return response()->json(['success' => false, 'message' => 'Địa chỉ không tồn tại hoặc không thuộc về bạn.'], 404);
            }

            if ($address->is_default) {
                return response()->json(['success' => false, 'message' => 'Không thể xóa địa chỉ mặc định!'], 403);
            }

            $address->delete();
            Log::info('Address deleted successfully', ['address_id' => $id, 'user_id' => Auth::user()->id]);

            // Nếu địa chỉ vừa xóa là địa chỉ đang chọn trong session, xóa session
            if (Session::get('checkout_address_id') == $id) {
                Session::forget('checkout_address_id');
            }

            return response()->json(['success' => true, 'message' => 'Địa chỉ đã được xóa thành công!']);
        } catch (\Exception $e) {
            Log::error('Delete address error: ' . $e->getMessage(), ['address_id' => $id, 'user_id' => Auth::user()->id]);
            return response()->json(['success' => false, 'message' => 'Đã xảy ra lỗi khi xóa địa chỉ!'], 500);
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

            $shippingFee = $this->calculateShippingFee($address, $request->shipping_type);
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
    protected function calculateShippingFee($address, $shippingType)
    {
        // Normalize province name (remove "Thành phố" or "Tỉnh" for matching)
        $destinationProvince = $address ? $address->province : 'Hà Nội';
        $normalizedProvince = preg_replace('/^(Thành phố|Tỉnh)\s+/', '', $destinationProvince);

        // Define Vietnam's regions (normalized names)
        $northernProvinces = ['Hà Nội', 'Bắc Ninh', 'Hưng Yên', 'Hải Dương', 'Hải Phòng', 'Quảng Ninh', 'Bắc Giang', 'Phú Thọ', 'Vĩnh Phúc', 'Ninh Bình', 'Thái Bình', 'Nam Định', 'Hà Nam', 'Hòa Bình', 'Sơn La', 'Điện Biên', 'Lai Châu', 'Lào Cai', 'Yên Bái', 'Tuyên Quang', 'Hà Giang', 'Cao Bằng', 'Bắc Kạn', 'Lạng Sơn', 'Thái Nguyên'];
        $centralProvinces = ['Thanh Hóa', 'Nghệ An', 'Hà Tĩnh', 'Quảng Bình', 'Quảng Trị', 'Thừa Thiên Huế', 'Đà Nẵng', 'Quảng Nam', 'Quảng Ngãi', 'Bình Định', 'Phú Yên', 'Khánh Hòa', 'Ninh Thuận', 'Bình Thuận', 'Kon Tum', 'Gia Lai', 'Đắk Lắk', 'Đắk Nông', 'Lâm Đồng'];
        $southernProvinces = ['Hồ Chí Minh', 'Bình Dương', 'Đồng Nai', 'Bà Rịa - Vũng Tàu', 'Long An', 'Tiền Giang', 'Bến Tre', 'Trà Vinh', 'Vĩnh Long', 'Đồng Tháp', 'An Giang', 'Kiên Giang', 'Cần Thơ', 'Hậu Giang', 'Sóc Trăng', 'Bạc Liêu', 'Cà Mau', 'Bình Phước', 'Tây Ninh'];

        // Determine region
        $region = 'northern';
        if (in_array($normalizedProvince, $centralProvinces)) {
            $region = 'central';
        } elseif (in_array($normalizedProvince, $southernProvinces)) {
            $region = 'southern';
        }

        // Calculate shipping fee
        $normalShippingFee = 16500;
        $fastShippingFee = match ($region) {
            'northern' => $normalizedProvince === 'Hà Nội' ? 30000 : 40000,
            'central' => 50000,
            'southern' => 60000,
        };

        $shippingFee = $shippingType === 'nhanh' ? $fastShippingFee : $normalShippingFee;

        \Log::info('Calculated shipping fee', [
            'province' => $normalizedProvince,
            'shipping_type' => $shippingType,
            'region' => $region,
            'normal_shipping_fee' => $normalShippingFee,
            'fast_shipping_fee' => $fastShippingFee,
            'total_shipping_fee' => $shippingFee,
            'session_id' => \Session::getId()
        ]);

        return $shippingFee;
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

            // Check if the order has timed out (30 minutes from created_at)
            $timeout = Carbon::parse($order->created_at)->addMinutes(30);
            if (Carbon::now()->greaterThan($timeout)) {
                // Cancel the order and restore quantities
                DB::transaction(function () use ($order, $order_code) {
                    // Update order status to canceled
                    $order->is_paid = 0; // Ensure the order is marked as unpaid
                    $order->cancelled_at = now(); // Set the cancellation timestamp
                    $order->cancel_reason = 'Order timed out'; // Optional: Set a reason
                    $order->save();

                    // Restore quantities in products and product_variants tables
                    foreach ($order->items as $item) {
                        if ($item->product_variant_id) {
                            $variant = ProductVariant::lockForUpdate()->find($item->product_variant_id);
                            if ($variant) {
                                $variant->stock += $item->quantity;
                                $variant->save();
                                \Log::info('Restored variant stock', [
                                    'variant_id' => $item->product_variant_id,
                                    'new_stock' => $variant->stock,
                                    'session_id' => Session::getId()
                                ]);
                            }
                        } else {
                            $product = Product::lockForUpdate()->find($item->product_id);
                            if ($product) {
                                $product->stock += $item->quantity;
                                $product->save();
                                \Log::info('Restored product stock', [
                                    'product_id' => $item->product_id,
                                    'new_stock' => $product->stock,
                                    'session_id' => Session::getId()
                                ]);
                            }
                        }
                    }

                    \Log::info('Order timed out and canceled, quantities restored', [
                        'order_id' => $order->id,
                        'order_code' => $order_code,
                        'user_id' => Auth::id(),
                        'session_id' => Session::getId()
                    ]);
                });

                return redirect()->route('home')->with('error', 'Đơn hàng đã hết thời gian thanh toán và bị hủy. Vui lòng tạo đơn hàng mới!');
            }

            // Tạo mã giao dịch mới để tránh lỗi "Giao dịch đã được xử lý"
            $order->code = $order->code . '-' . Str::upper(Str::random(4));
            $order->save();

            \Log::info('Generated new transaction code for retry', [
                'order_id' => $order->id,
                'new_code' => $order->code,
                'session_id' => Session::getId()
            ]);

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
            session()->forget(['vnpay_txn_ref', 'pending_order_id']);
            return redirect()->route('checkout')
                ->with('error', 'Lỗi khi tạo thanh toán VNPay: ' . $e->getMessage());
        }
    }

    public function vnpayReturn(Request $request)
    {
        try {
            $vnp_HashSecret = env('VNPAY_HASH_SECRET');
            $vnp_SecureHash = $request->vnp_SecureHash;
            $inputData = $request->except('vnp_SecureHash');

            ksort($inputData);
            $query = http_build_query($inputData, '', '&', PHP_QUERY_RFC3986);
            $query = str_replace('%20', '+', $query);
            $hash = hash_hmac('sha512', $query, $vnp_HashSecret);

            \Log::debug('VNPay return validation', [
                'received_hash' => $vnp_SecureHash,
                'calculated_hash' => $hash,
                'query_string' => $query,
                'session_id' => Session::getId()
            ]);

            if ($hash !== $vnp_SecureHash) {
                \Log::error('Invalid VNPay secure hash', [
                    'received_hash' => $vnp_SecureHash,
                    'calculated_hash' => $hash,
                    'session_id' => Session::getId()
                ]);
                return redirect()->route('home')->with('error', 'Chữ ký thanh toán không hợp lệ!');
            }

            $order = Order::where('code', $request->vnp_TxnRef)->firstOrFail();

            // Kiểm tra số tiền
            if ($order->total_amount * 100 != $request->vnp_Amount) {
                \Log::warning('VNPay amount mismatch', [
                    'order_id' => $order->id,
                    'order_amount' => $order->total_amount * 100,
                    'vnp_amount' => $request->vnp_Amount,
                    'session_id' => Session::getId()
                ]);
                return redirect()->route('home')->with('error', 'Số tiền thanh toán không khớp!');
            }

            if ($request->vnp_ResponseCode == '00' && $request->vnp_TransactionStatus == '00') {
                // Thanh toán thành công
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

                    // Xóa các mục đã chọn trong giỏ hàng
                    $cart = Cart::where('user_id', $order->user_id)->first();
                    if ($cart) {
                        $selectedItems = session('selected_items', []);
                        CartItem::where('cart_id', $cart->id)
                            ->whereIn('id', $selectedItems)
                            ->delete();
                        if ($cart->items()->count() === 0) {
                            $cart->delete();
                        }
                    }
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
                // Thanh toán thất bại hoặc người dùng dừng
                DB::transaction(function () use ($order, $request) {
                    PaymentLog::create([
                        'order_id' => $order->id,
                        'payment_method' => 'vnpay',
                        'transaction_id' => $request->vnp_TransactionNo ?? null,
                        'amount' => $request->vnp_Amount / 100 ?? $order->total_amount,
                        'status' => 'failed',
                        'response_data' => json_encode($request->all())
                    ]);
                });

                \Log::warning('VNPay payment failed or cancelled', [
                    'order_id' => $order->id,
                    'txn_ref' => $request->vnp_TxnRef,
                    'response_code' => $request->vnp_ResponseCode,
                    'transaction_status' => $request->vnp_TransactionStatus,
                    'session_id' => Session::getId()
                ]);

                return redirect()->route('checkout.retry', $order->code)
                    ->with('error', 'Thanh toán chưa hoàn tất. Vui lòng thử lại hoặc chọn phương thức thanh toán khác.');
            }
        } catch (\Exception $e) {
            \Log::error('VNPay return error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'stack_trace' => $e->getTraceAsString(),
                'session_id' => Session::getId()
            ]);
            return redirect()->route('checkout.retry', $request->vnp_TxnRef ?? 'unknown')
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
