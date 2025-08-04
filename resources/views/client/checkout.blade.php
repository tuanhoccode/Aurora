@extends('client.layouts.default')

@section('title', 'Thanh toán')

@section('content')
<style>
    :root {
        --primary: #4a90e2;
        --primary-dark: #357abd;
        --text-dark: #2d3748;
        --text-muted: #64748b;
        --border: #e2e8f0;
        --shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        --radius: 8px;
        --transition: all 0.2s ease;
        --input-height: 36px;
        --input-font-size: 0.85rem;
    }

    .checkout__section {
        background: #f8fafc;
        padding: 1rem;
        min-height: auto;
    }

    .checkout__container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 0 10px;
        display: grid;
        gap: 1rem;
        grid-template-columns: 1fr 350px;
    }

    .checkout__block {
        background: #fff;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 1rem;
        border: 1px solid var(--border);
        transition: var(--transition);
    }

    .checkout__block:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    }

    .checkout__block-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 0.6rem;
        position: relative;
        padding-bottom: 0.3rem;
    }

    .checkout__block-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 30px;
        height: 2px;
        background: var(--primary);
    }

    .checkout__user-info p,
    .checkout__address-info p {
        margin-bottom: 0.4rem;
        font-size: 0.9rem;
        color: var(--text-muted);
        line-height: 1.4;
    }

    .checkout__user-info strong {
        color: var(--text-dark);
        font-weight: 600;
    }

    .checkout__address-selection {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.8rem;
        background: #f8fafc;
        border-radius: var(--radius);
        padding: 0.8rem;
        border: 1px solid var(--border);
    }

    .checkout__address-selection label {
        font-size: 0.9rem;
        font-weight: 500;
        color: var(--text-dark);
    }

    .checkout__address-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .checkout__address-item input[type="radio"] {
        width: 18px;
        height: 18px;
        margin: 0 0.5rem 0 0;
        flex-shrink: 0;
        border: 1px solid var(--border);
        border-radius: 4px;
    }

    .checkout__address-item input[type="radio"]:checked {
        background-color: var(--primary);
        border-color: var(--primary);
    }

    .checkout__address-item .address-details {
        flex: 1;
    }

    .checkout__address-item .badge {
        font-size: 0.7rem;
        border-radius: 12px;
        padding: 0.2rem 0.5rem;
        background: var(--primary);
        color: #fff;
    }

    .checkout__cart-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .checkout__cart-header,
    .checkout__cart-item {
        display: grid;
        grid-template-columns: 50px 2fr 1fr 1fr 1fr;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 0;
        font-size: 0.85rem;
    }

    .checkout__cart-header {
        font-weight: 600;
        color: var(--text-muted);
        border-bottom: 1px solid var(--border);
        text-transform: uppercase;
    }

    .checkout__cart-item {
        background: #f8fafc;
        border-radius: var(--radius);
        margin-bottom: 0.3rem;
        padding: 0.5rem;
    }

    .checkout__cart-item img {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid var(--border);
    }

    .checkout__cart-item p {
        margin: 0;
        font-size: 0.85rem;
        color: var(--text-dark);
        font-weight: 500;
    }

    .checkout__cart-item small {
        color: var(--text-muted);
        font-size: 0.75rem;
    }

    .checkout__note {
        border-radius: var(--radius);
        border: 1px solid var(--border);
        background: #f8fafc;
        font-size: var(--input-font-size);
        padding: 0.5rem;
        width: 100%;
        height: var(--input-height);
        resize: none;
        transition: var(--transition);
    }

    .checkout__note:focus {
        border-color: var(--primary);
        outline: none;
        box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.1);
    }

    .checkout__shipping-method .form-check {
        border-radius: var(--radius);
        border: 1px solid var(--border);
        padding: 0.8rem;
        margin-bottom: 0.5rem;
        cursor: pointer;
        position: relative;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .checkout__shipping-method .form-check:hover,
    .checkout__shipping-method .form-check.selected {
        border-color: var(--primary);
        box-shadow: var(--shadow);
    }

    .checkout__shipping-method .form-check-input {
        width: 18px;
        height: 18px;
        margin: 0 0.5rem 0 0;
        flex-shrink: 0;
        border: 1px solid var(--border);
        border-radius: 4px;
    }

    .checkout__shipping-method .form-check-input:checked {
        background-color: var(--primary);
        border-color: var(--primary);
    }

    .checkout__shipping-method .form-check-label {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text-dark);
        flex: 1;
    }

    .checkout__shipping-method .form-check p {
        margin: 0.2rem 0 0;
        font-size: 0.8rem;
        color: var(--text-muted);
    }

    .checkout__payment-method {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.8rem;
        background: #f8fafc;
        border-radius: var(--radius);
        padding: 0.8rem;
        border: 1px solid var(--border);
    }

    .checkout__payment-method label,
    .checkout__address-selection label {
        font-size: 0.9rem;
        font-weight: 500;
        color: var(--text-dark);
    }

    .checkout__payment-method img {
        width: 40px;
        vertical-align: middle;
    }

    .checkout__coupon-group {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .checkout__coupon-group .form-control {
        border-radius: var(--radius);
        border: 1px solid var(--border);
        font-size: var(--input-font-size);
        height: var(--input-height);
        padding: 0.5rem;
        flex: 1;
        transition: var(--transition);
    }

    .checkout__coupon-group .form-control:focus {
        border-color: var(--primary);
        outline: none;
        box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.1);
    }

    .checkout__coupon-group .btn,
    .checkout__btn-main,
    .checkout__submit-btn {
        border-radius: var(--radius);
        font-size: var(--input-font-size);
        padding: 0.5rem;
        background: var(--primary);
        color: #fff;
        border: none;
        transition: var(--transition);
        height: var(--input-height);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .checkout__coupon-group .btn:hover,
    .checkout__btn-main:hover,
    .checkout__submit-btn:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(74, 144, 226, 0.2);
    }

    .checkout__summary-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .checkout__summary-list li {
        display: flex;
        justify-content: space-between;
        font-size: 0.9rem;
        margin-bottom: 0.4rem;
    }

    .checkout__summary-list .total {
        font-size: 1rem;
        font-weight: 600;
        color: var(--primary);
        padding: 0.5rem;
        border-radius: var(--radius);
        border: 1px solid var(--border);
    }

    .alert {
        border-radius: var(--radius);
        padding: 0.5rem;
        margin-bottom: 0.8rem;
        font-size: 0.85rem;
    }

    .alert-success { background: #d4edda; color: #155724; }
    .alert-danger { background: #f8d7da; color: #721c24; }

    .modal-content {
        border-radius: var(--radius);
        box-shadow: var(--shadow);
    }

    .modal-header,
    .modal-footer {
        border-color: var(--border);
        padding: 0.8rem;
    }

    .modal-title {
        font-size: 1rem;
        font-weight: 600;
    }

    .modal-body {
        padding: 1rem;
    }

    .modal-body .form-check {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .modal-body .form-check-input {
        width: 18px;
        height: 18px;
        margin: 0;
        border: 1px solid var(--border);
        border-radius: 4px;
    }

    .modal-body .form-check-input:checked {
        background-color: var(--primary);
        border-color: var(--primary);
    }

    @media (max-width: 991.98px) {
        .checkout__container {
            grid-template-columns: 1fr;
        }
        .checkout__cart-header,
        .checkout__cart-item {
            grid-template-columns: 40px 1.5fr 1fr 1fr 1fr;
            font-size: 0.8rem;
        }
    }

    @media (max-width: 600px) {
        .checkout__section { padding: 0.5rem; }
        .checkout__block { padding: 0.8rem; }
        .checkout__coupon-group { flex-direction: column; align-items: stretch; }
        .checkout__payment-method, .checkout__address-selection { flex-direction: column; align-items: flex-start; }
        .checkout__btn-main, .checkout__submit-btn { width: 100%; }
    }
</style>

<section class="checkout__section">
    <div class="checkout__container">
        <div>
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="checkout__block">
                <div class="checkout__block-title">Thông tin khách hàng</div>
                <div class="checkout__user-info">
                    @if (auth()->check())
                        <p><strong>Họ và tên:</strong> {{ $user->fullname }}</p>
                        <p><strong>Email:</strong> {{ $user->email }}</p>
                        <p><strong>Số điện thoại:</strong> {{ $user->phone_number ?? 'Chưa cung cấp' }}</p>
                        @if ($user->avatar)
                            <p><strong>Avatar:</strong> <img src="{{ asset($user->avatar) }}" alt="Avatar" width="40" style="border-radius:50%;border:1px solid #e3e6ea;"></p>
                        @else
                            <p class="text-muted">Avatar: Chưa có</p>
                        @endif
                    @else
                        <p class="text-muted">Vui lòng đăng nhập để xem thông tin khách hàng.</p>
                    @endif
                </div>
            </div>
            <div class="checkout__block">
                <div class="checkout__block-title">Địa chỉ nhận hàng</div>
                <div class="checkout__address-selection">
                    <div>
                        <label id="address_label">
                            @if (auth()->check() && $addresses->count() > 0)
                                @php
                                    $selectedAddress = $addresses->firstWhere('id', old('selected_address', session('checkout_address_id', $defaultAddress->id ?? '')));
                                @endphp
                                {{ $selectedAddress ? ($selectedAddress->fullname ?? 'Chưa cung cấp họ tên') . ' (+84) ' . ($selectedAddress->phone_number && preg_match('/^0[0-9]{9}$/', $selectedAddress->phone_number) ? $selectedAddress->phone_number : 'Số điện thoại không hợp lệ') . ' - ' . ($selectedAddress->street ? $selectedAddress->street . ', ' : '') . ($selectedAddress->ward ? $selectedAddress->ward . ', ' : '') . ($selectedAddress->district ? $selectedAddress->district . ', ' : '') . ($selectedAddress->province ?? 'Chưa cung cấp tỉnh/thành phố') : 'Chưa chọn địa chỉ' }}
                                @if ($selectedAddress && $selectedAddress->is_default)
                                    <span class="badge bg-primary">Mặc định</span>
                                @endif
                            @else
                                Chưa có địa chỉ
                            @endif
                        </label>
                    </div>
                    <button class="checkout__btn-main" data-bs-toggle="modal" data-bs-target="#addressModal">Thay đổi</button>
                </div>
                @error('selected_address')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="checkout__block">
                <div class="checkout__block-title">Sản phẩm</div>
                <ul class="checkout__cart-list">
                    <li class="checkout__cart-header">
                        <span></span>
                        <span>Sản phẩm</span>
                        <span>Đơn giá</span>
                        <span>Số lượng</span>
                        <span>Thành tiền</span>
                    </li>
                    @foreach ($cartItems->groupBy('shop_id') as $shopId => $items)
                        @foreach ($items as $item)
                            @php
                                $product = $item->product;
                                $variant = $item->productVariant;
                                $unitPrice = $item->price_at_time;

                                // Function to get attribute value
                                $getAttrValue = function ($entity, $keywords) {
                                    if (!$entity || !isset($entity->attributeValues)) {
                                        return null;
                                    }
                                    foreach ($entity->attributeValues as $attrVal) {
                                        $attrName = strtolower($attrVal->attribute->name ?? '');
                                        foreach ($keywords as $kw) {
                                            if (str_contains($attrName, $kw)) {
                                                return $attrVal->value;
                                            }
                                        }
                                    }
                                    return null;
                                };

                                $size = $getAttrValue($variant, ['size', 'kích']);
                                $color = $getAttrValue($variant, ['color', 'màu']);

                                // Get the correct image for variant or product
                                if ($variant) {
                                    if (!empty($variant->img)) {
                                        $img = asset('storage/' . $variant->img);
                                    } elseif ($variant->images && $variant->images->count() > 0) {
                                        $img = asset('storage/' . $variant->images->first()->url);
                                    } else {
                                        $img = $product->image_url ?? asset('assets/img/product/placeholder.jpg');
                                    }
                                } else {
                                    $img = $product->image_url ?? asset('assets/img/product/placeholder.jpg');
                                }
                            @endphp
                            <li class="checkout__cart-item">
                                <img src="{{ $img }}"
                                    alt="{{ $product->name ?? 'Sản phẩm ' . $item->product_id }}">
                                <div>
                                    <p>{{ $product->name ?? 'Sản phẩm ' . $item->product_id }}</p>
                                    @if ($variant)
                                        <small>
                                            @if ($size)
                                                Kích thước: {{ $size }}
                                            @endif
                                            @if ($color)
                                                Màu: {{ $color }}
                                            @endif
                                            @if (!$size && !$color)
                                                Loại: {{ $item->variant_name ?? 'N/A' }}
                                            @endif
                                        </small>
                                    @endif
                                </div>
                                <span>{{ number_format($unitPrice ?? $product->price) }}</span>
                                <span>{{ $item->quantity }}</span>
                                <span>{{ number_format(($unitPrice ?? $product->price) * $item->quantity) }}</span>
                            </li>
                        @endforeach
                    @endforeach
                </ul>
                <form action="{{ route('checkout.update') }}" method="POST">
                    @csrf
                    <label for="note">Lời nhắn:</label>
                    <textarea name="note" id="note" class="checkout__note" placeholder="Lưu ý cho Người bán..."
                        onchange="this.form.submit()">{{ old('note', session('note', '')) }}</textarea>
                    @error('note')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </form>
            </div>
        </div>

        <div>
            <div class="checkout__block checkout__shipping-method">
                <div class="checkout__block-title">Phương thức vận chuyển</div>
                <form action="{{ route('checkout.update') }}" method="POST" id="shippingForm">
                    @csrf
                    <div
                        class="form-check {{ old('shipping_type', session('shipping_type', 'thường')) === 'thường' ? 'selected' : '' }}">
                        <input class="form-check-input" type="radio" id="normal_shipping" name="shipping_type"
                            value="thường"
                            {{ old('shipping_type', session('shipping_type', 'thường')) === 'thường' ? 'checked' : '' }}
                            onchange="this.form.submit()" required>
                        <div>
                            <label class="form-check-label" for="normal_shipping">
                                Giao hàng thường - ₫16.500
                            </label>
                            <p class="text-muted small">
                                Dự kiến giao hàng từ
                                <strong>{{ \Carbon\Carbon::today()->addDays(2)->format('d/m/Y') }}</strong>
                                đến <strong>{{ \Carbon\Carbon::today()->addDays(4)->format('d/m/Y') }}</strong>
                            </p>
                            <p class="text-muted small">
                                Nhận Voucher <strong>₫15.000</strong> nếu giao hàng sau
                                <strong>{{ \Carbon\Carbon::today()->addDays(4)->format('d/m/Y') }}</strong>
                            </p>
                        </div>
                    </div>
                    <div
                        class="form-check {{ old('shipping_type', session('shipping_type', 'thường')) === 'nhanh' ? 'selected' : '' }}">
                        <input class="form-check-input" type="radio" id="fast_shipping" name="shipping_type"
                            value="nhanh"
                            {{ old('shipping_type', session('shipping_type', 'thường')) === 'nhanh' ? 'checked' : '' }}
                            onchange="this.form.submit()">
                        <div>
                            <label class="form-check-label" for="fast_shipping">
                                Giao hàng nhanh - ₫30.000
                            </label>
                            <p class="text-muted small">
                                Dự kiến giao hàng trong vòng <strong>4 giờ</strong> nếu đặt trước 16:00 hôm nay
                            </p>
                            <p class="text-muted small">
                                Hỗ trợ <strong>đồng kiểm</strong> (kiểm tra hàng trước khi nhận)
                            </p>
                        </div>
                    </div>
                    @error('shipping_type')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </form>
            </div>
            <div class="checkout__block">
                <div class="checkout__block-title">Phương thức thanh toán</div>
                <div class="checkout__payment-method">
                    <div>
                        <label id="payment_method_label">
                            {{ old('payment_method', session('payment_method', 'cod')) === 'cod' ? 'Thanh toán khi nhận hàng' : 'VNPay' }}
                            @if (old('payment_method', session('payment_method', 'cod')) === 'vnpay')
                                <img src="{{ asset('assets/img/icon/vnpay-logo.png') }}" alt="VNPay" width="40">
                            @endif
                        </label>
                    </div>
                    <button class="checkout__btn-main" data-bs-toggle="modal" data-bs-target="#paymentModal">Thay đổi</button>
                </div>
                @error('payment_method')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="checkout__block">
                <div class="checkout__block-title">Mã giảm giá</div>
                <form action="{{ route('checkout.apply-coupon') }}" method="POST" class="checkout__coupon-group">
                    @csrf
                    <input type="text" name="coupon_code" class="form-control" placeholder="Nhập mã giảm giá"
                        value="{{ old('coupon_code') }}">
                    <button type="submit" class="btn">Áp dụng</button>
                    @error('coupon_code')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </form>
                @if ($coupon)
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <p class="text-success mb-0">
                            Mã {{ $coupon->code }} được áp dụng: Giảm {{ number_format($discount) }} ₫
                            @if ($coupon->discount_type === 'percent')
                                ({{ $coupon->discount_value }}%)
                            @else
                                ({{ number_format($coupon->discount_value) }} ₫)
                            @endif
                        </p>
                        <form action="{{ route('checkout.remove-coupon') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm checkout__btn-main">Xóa</button>
                        </form>
                    </div>
                @endif
            </div>
            <div class="checkout__block">
                <div class="checkout__block-title">Tổng thanh toán ({{ $cartItems->sum('quantity') }} sản phẩm)</div>
                <ul class="checkout__summary-list">
                    <li><span>Tổng tiền hàng</span><span>{{ number_format($cartTotal) }} ₫</span></li>
                    <li><span>Tổng tiền phí vận chuyển</span><span>{{ number_format($shippingFee) }} ₫</span></li>
                    @if ($coupon)
                        <li><span>Giảm giá ({{ $coupon->code }})</span><span class="text-danger">-{{ number_format($discount) }} ₫</span></li>
                    @endif
                    <li class="total"><span>Tổng thanh toán</span><span>{{ number_format($cartTotal + $shippingFee - $discount) }} ₫</span></li>
                </ul>
                <form id="checkoutForm" action="{{ route('checkout.process') }}" method="POST">
                    @csrf
                    <input type="hidden" name="shipping_type" value="{{ old('shipping_type', session('shipping_type', 'thường')) }}">
                    <input type="hidden" name="payment_method" value="{{ old('payment_method', session('payment_method', 'cod')) }}">
                    <input type="hidden" name="address_id" value="{{ old('address_id', session('checkout_address_id', $defaultAddress->id ?? '')) }}">
                    <input type="hidden" name="note" value="{{ old('note', session('note', '')) }}">
                    <button type="submit" class="checkout__submit-btn">Đặt hàng</button>
                    <p class="text-muted small mt-1">Nhấn "Đặt hàng" đồng nghĩa với việc bạn đồng ý tuân theo <a href="#">Điều khoản</a></p>
                    @error('address_id') <span class="text-danger">{{ $message }}</span> @enderror
                    @error('shipping_type') <span class="text-danger">{{ $message }}</span> @enderror
                    @error('payment_method') <span class="text-danger">{{ $message }}</span> @enderror
                </form>
            </div>
        </div>
    </div>

    <!-- Address Modal -->
    <div class="modal fade" id="addressModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chọn địa chỉ nhận hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('checkout.update') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        @if (auth()->check() && $addresses->count() > 0)
                            @foreach ($addresses as $address)
                                <div class="form-check checkout__address-item">
                                    <input type="radio" id="address_{{ $address->id }}" name="selected_address" value="{{ $address->id }}"
                                        class="form-check-input"
                                        {{ $address->id == old('selected_address', session('checkout_address_id', $defaultAddress->id ?? '')) ? 'checked' : '' }}>
                                    <div class="address-details">
                                        <label for="address_{{ $address->id }}" class="form-check-label">
                                            <strong>{{ $address->fullname ?? 'Chưa cung cấp họ tên' }}</strong> (+84)
                                            {{ $address->phone_number && preg_match('/^0[0-9]{9}$/', $address->phone_number) ? $address->phone_number : 'Số điện thoại không hợp lệ' }}
                                        </label>
                                        <p>
                                            {{ $address->street ? $address->street . ', ' : '' }}
                                            {{ $address->ward ? $address->ward . ', ' : '' }}
                                            {{ $address->district ? $address->district . ', ' : '' }}
                                            {{ $address->province ?? 'Chưa cung cấp tỉnh/thành phố' }}
                                            @if (
                                                !$address->fullname ||
                                                    !$address->phone_number ||
                                                    !$address->province ||
                                                    !$address->district ||
                                                    !$address->ward ||
                                                    !$address->street)
                                                <span class="text-danger">(Thông tin chưa đầy đủ)</span>
                                            @endif
                                        </p>
                                        @if ($address->is_default)
                                            <span class="badge bg-primary">Mặc định</span>
                                        @endif
                                    </div>
                                    <a href="{{ route('address.edit', ['id' => $address->id]) }}" class="btn btn-outline-secondary btn-sm checkout__btn-main">Cập nhật</a>
                                </div>
                            @endforeach
                            <a href="{{ route('address.create') }}" class="btn btn-outline-primary btn-sm checkout__btn-main">Thêm địa chỉ mới</a>
                        @else
                            <p class="text-muted">Chưa có địa chỉ. Vui lòng thêm địa chỉ mới.</p>
                            <a href="{{ route('address.create') }}" class="btn btn-outline-primary btn-sm checkout__btn-main">Thêm địa chỉ</a>
                        @endif
                        @error('selected_address')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light checkout__btn-main" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary checkout__btn-main">Xác nhận</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chọn phương thức thanh toán</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('checkout.update') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-check mb-2">
                            <input type="radio" id="cod_modal" name="payment_method" value="cod"
                                class="form-check-input"
                                {{ old('payment_method', session('payment_method', 'cod')) === 'cod' ? 'checked' : '' }}>
                            <label for="cod_modal" class="form-check-label">Thanh toán khi nhận hàng</label>
                        </div>
                        <div class="form-check mb-2">
                            <input type="radio" id="vnpay_modal" name="payment_method" value="vnpay"
                                class="form-check-input"
                                {{ old('payment_method', session('payment_method', 'cod')) === 'vnpay' ? 'checked' : '' }}>
                            <label for="vnpay_modal" class="form-check-label">VNPay <img
                                    src="{{ asset('assets/img/icon/vnpay-logo.png') }}" alt="VNPay"
                                    width="40"></label>
                        </div>
                        @error('payment_method')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light checkout__btn-main">Hủy</button>
                        <button type="submit" class="btn btn-primary checkout__btn-main">Xác nhận</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const shippingOptions = document.querySelectorAll('.checkout__shipping-method .form-check');
        shippingOptions.forEach(option => {
            const radio = option.querySelector('input[type="radio"]');
            if (radio.checked) {
                option.classList.add('selected');
            }
            option.addEventListener('click', function(e) {
                if (e.target !== radio) {
                    radio.checked = true;
                    updateSelectedState();
                    radio.form.submit();
                }
            });
            radio.addEventListener('change', function() {
                updateSelectedState();
            });
        });

        function updateSelectedState() {
            shippingOptions.forEach(option => {
                const radio = option.querySelector('input[type="radio"]');
                if (radio.checked) {
                    option.classList.add('selected');
                } else {
                    option.classList.remove('selected');
                }
            });
        }
    });
</script>
@endsection
```