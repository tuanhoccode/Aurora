@extends('client.layouts.default')

@section('title', 'Thanh toán')

@section('content')
    <style>
        .checkout__section {
            background: #f8fafc;
            min-height: 100vh;
            padding: 2rem 0;
        }

        .checkout__container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            box-sizing: border-box;
        }

        .checkout__block {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 2.5rem;
            margin-bottom: 2rem;
            font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
            width: 100%;
            box-sizing: border-box;
            border: 1px solid #e2e8f0;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .checkout__block:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        .checkout__block-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 1.5rem;
            letter-spacing: -0.01em;
            position: relative;
            padding-bottom: 0.5rem;
        }

        .checkout__block-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: #4a90e2;
            border-radius: 2px;
        }

        .checkout__user-info p,
        .checkout__address-info p {
            margin-bottom: 0.8rem;
            font-size: 1.05rem;
            color: #4a5568;
            line-height: 1.6;
        }

        .checkout__user-info strong {
            color: #2d3748;
            font-weight: 600;
        }

        .checkout__address-item {
            border-radius: 12px;
            border: 1.5px solid #e2e8f0;
            background: #f8fafc;
            padding: 1.3rem;
            margin-bottom: 1rem;
            transition: all 0.2s ease;
            position: relative;
            cursor: pointer;
        }

        .checkout__address-item:hover {
            border-color: #4a90e2;
            box-shadow: 0 4px 15px rgba(74, 144, 226, 0.1);
            transform: translateY(-1px);
        }

        .checkout__address-item.bg-light {
            background: #ebf8ff;
            border-color: #4a90e2;
            box-shadow: 0 4px 15px rgba(74, 144, 226, 0.15);
        }

        .checkout__address-item .badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            font-size: 0.85rem;
            border-radius: 16px;
            padding: 0.3rem 0.8rem;
            background: #4a90e2;
            color: white;
            font-weight: 500;
        }

        .checkout__address-item .btn {
            margin-top: 0.8rem;
            border-radius: 8px;
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: all 0.2s ease;
        }

        .checkout__cart-list {
            list-style: none;
            padding-left: 0;
            margin-bottom: 0;
        }

        .checkout__cart-header,
        .checkout__cart-item {
            display: grid;
            grid-template-columns: 70px 2.5fr 1fr 1fr 1fr;
            align-items: center;
            gap: 1rem;
            font-size: 1rem;
            padding: 1rem 0;
        }

        .checkout__cart-header {
            font-weight: 600;
            color: #4a5568;
            border-bottom: 2px solid #e2e8f0;
            margin-bottom: 0.8rem;
            padding-bottom: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            font-size: 0.85rem;
        }

        .checkout__cart-item {
            background: #f8fafc;
            border-radius: 12px;
            margin-bottom: 0.6rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            font-weight: 500;
            padding: 1rem;
            transition: all 0.2s ease;
        }

        .checkout__cart-item:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .checkout__cart-item img {
            width: 55px;
            height: 55px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #fff;
            transition: transform 0.2s ease;
        }

        .checkout__cart-item:hover img {
            transform: scale(1.02);
        }

        .checkout__cart-item p {
            margin-bottom: 0;
            font-size: 1rem;
            color: #2d3748;
            font-weight: 600;
            line-height: 1.4;
        }

        .checkout__cart-item small {
            color: #718096;
            font-size: 0.9rem;
            font-weight: 400;
        }

        .checkout__note {
            border-radius: 8px;
            border: 1.5px solid #e2e8f0;
            background: #f8fafc;
            font-size: 1rem;
            padding: 0.8rem 1rem;
            margin-top: 0.8rem;
            margin-bottom: 0.6rem;
            min-height: 50px;
            transition: all 0.2s ease;
            resize: vertical;
        }

        .checkout__note:focus {
            outline: none;
            border-color: #4a90e2;
            box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.1);
        }

        .checkout__shipping-method .form-check {
            background: #fff;
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            padding: 1.3rem;
            margin-bottom: 1rem;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .checkout__shipping-method .form-check:hover {
            border-color: #4a90e2;
            box-shadow: 0 4px 15px rgba(74, 144, 226, 0.1);
            transform: translateY(-1px);
        }

        .checkout__shipping-method .form-check-input:checked~.form-check-label {
            color: #4a90e2;
            font-weight: 600;
        }

        .checkout__shipping-method .form-check-label {
            font-size: 1.05rem;
            font-weight: 600;
            color: #1a202c;
            cursor: pointer;
            margin-bottom: 0.5rem;
            display: block;
        }

        .checkout__shipping-method .form-check p {
            margin-bottom: 0.3rem;
            font-size: 0.95rem;
            color: #64748b;
            line-height: 1.4;
        }

        .checkout__shipping-method .form-check p strong {
            color: #1a202c;
            font-weight: 600;
        }

        .checkout__shipping-method .form-check p:last-child {
            margin-bottom: 0;
        }

        .checkout__payment-method {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1.2rem;
            background: #f8fafc;
            border-radius: 12px;
            padding: 1.2rem;
            border: 1.5px solid #e2e8f0;
        }

        .checkout__payment-method label {
            font-size: 1rem;
            font-weight: 500;
            color: #2d3748;
            margin: 0;
        }

        .checkout__coupon-group {
            display: flex;
            flex-direction: row;
            gap: 0.8rem;
            align-items: center;
            flex-wrap: nowrap;
            margin-bottom: 0.8rem;
        }

        .checkout__coupon-group .form-control {
            border-radius: 8px;
            border: 1.5px solid #e2e8f0;
            font-size: 1rem;
            height: 45px;
            box-shadow: none;
            padding: 0.6rem 1rem;
            background: #fff;
            color: #2d3748;
            flex: 1 1 auto;
            transition: all 0.2s ease;
        }

        .checkout__coupon-group .form-control:focus {
            outline: none;
            border-color: #4a90e2;
            box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.1);
        }

        .checkout__coupon-group .btn {
            min-width: 120px;
            font-weight: 500;
            box-shadow: 0 2px 8px rgba(74, 144, 226, 0.15);
            border-radius: 8px;
            height: 45px;
            background: #4a90e2;
            color: #fff;
            border: none;
            font-size: 1rem;
            padding: 0 1.5rem;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
        }

        .checkout__coupon-group .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(74, 144, 226, 0.25);
            background: #357abd;
        }

        .checkout__btn-main {
            min-width: 120px;
            font-weight: 500;
            box-shadow: 0 2px 8px rgba(74, 144, 226, 0.15);
            border-radius: 8px;
            height: 45px;
            background: #4a90e2;
            color: #fff !important;
            border: none;
            font-size: 1rem;
            padding: 0 1.5rem;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
        }

        .checkout__btn-main:hover,
        .checkout__btn-main:focus {
            background: #357abd;
            color: #fff !important;
            box-shadow: 0 4px 15px rgba(74, 144, 226, 0.25);
            text-decoration: none;
            transform: translateY(-1px);
        }

        .checkout__summary-list {
            list-style: none;
            padding-left: 0;
            margin-bottom: 0;
        }

        .checkout__summary-list li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 1rem;
            margin-bottom: 0.8rem;
            padding: 0.6rem 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .checkout__summary-list li:last-child {
            border-bottom: none;
        }

        .checkout__summary-list .total {
            font-size: 1.2rem;
            font-weight: 700;
            color: #4a90e2;
            background: #f8fafc;
            padding: 0.8rem 1.2rem;
            border-radius: 8px;
            margin-top: 0.8rem;
            border: 1.5px solid #e2e8f0;
        }

        .checkout__submit-btn {
            background: #4a90e2;
            color: #fff;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 12px;
            padding: 1rem 0;
            width: 100%;
            margin-top: 1.2rem;
            box-shadow: 0 4px 15px rgba(74, 144, 226, 0.2);
            border: none;
            transition: all 0.2s ease;
            text-transform: none;
            display: flex;
            align-items: center;
            justify-content: center;
            letter-spacing: -0.01em;
        }

        .checkout__submit-btn:hover,
        .checkout__submit-btn:focus {
            background: #357abd;
            color: #fff;
            box-shadow: 0 6px 20px rgba(74, 144, 226, 0.3);
            transform: translateY(-1px);
        }

        /* Alert styling */
        .alert {
            border-radius: 8px;
            border: none;
            padding: 0.8rem 1.2rem;
            margin-bottom: 1.2rem;
            font-weight: 500;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
        }

        /* Modal styling */
        .modal-content {
            border-radius: 16px;
            border: none;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            border-bottom: 1.5px solid #e2e8f0;
            padding: 1.2rem 1.5rem;
        }

        .modal-title {
            font-weight: 600;
            color: #2d3748;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            border-top: 1.5px solid #e2e8f0;
            padding: 1.2rem 1.5rem;
        }

        .form-check-input {
            width: 1.1em;
            height: 1.1em;
            border: 1.5px solid #e2e8f0;
            border-radius: 4px;
        }

        .form-check-input:checked {
            background-color: #4a90e2;
            border-color: #4a90e2;
        }

        .checkout__shipping-method .form-check-input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }

        .checkout__shipping-method .form-check {
            position: relative;
        }

        .checkout__shipping-method .form-check::before {
            content: '';
            position: absolute;
            top: 1rem;
            right: 1rem;
            width: 24px;
            height: 24px;
            border: 2px solid #e2e8f0;
            border-radius: 50%;
            background: #fff;
            transition: all 0.2s ease;
            z-index: 1;
        }

        .checkout__shipping-method .form-check.selected::before {
            background: #4a90e2;
            border-color: #4a90e2;
        }

        .checkout__shipping-method .form-check::after {
            content: '✓';
            position: absolute;
            top: 1rem;
            right: 1rem;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 0.8rem;
            font-weight: bold;
            opacity: 0;
            transition: all 0.2s ease;
            z-index: 2;
        }

        .checkout__shipping-method .form-check.selected::after {
            opacity: 1;
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .checkout__container {
                max-width: 100vw;
                padding: 0 15px;
            }

            .checkout__block {
                padding: 2rem 1.5rem;
            }

            .checkout__cart-header,
            .checkout__cart-item {
                grid-template-columns: 55px 2fr 1fr 1fr 1fr;
                font-size: 0.95rem;
            }
        }

        @media (max-width: 600px) {
            .checkout__section {
                padding: 1rem 0;
            }

            .checkout__block {
                padding: 1.5rem 1rem;
                margin-bottom: 1.5rem;
            }

            .checkout__cart-header,
            .checkout__cart-item {
                grid-template-columns: 45px 1.5fr 1fr 1fr 1fr;
                font-size: 0.85rem;
                gap: 0.5rem;
                padding: 0.8rem;
            }

            .checkout__submit-btn {
                font-size: 1rem;
                padding: 0.8rem 0;
            }

            .checkout__coupon-group {
                flex-direction: column;
                align-items: stretch;
                gap: 0.8rem;
            }

            .checkout__coupon-group .btn {
                width: 100%;
                min-width: 0;
            }

            .checkout__payment-method {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.8rem;
            }
        }
    </style>

    <section class="checkout__section">
        <div class="checkout__container">
            <div class="@if (session('success') || session('error')) mb-3 @endif">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
            </div>
            <div class="checkout__block">
                <div class="checkout__block-title">Thông tin khách hàng</div>
                <div class="checkout__user-info">
                    @if (auth()->check())
                        <p><strong>Họ và tên:</strong> {{ $user->fullname }}</p>
                        <p><strong>Email:</strong> {{ $user->email }}</p>
                        <p><strong>Số điện thoại:</strong> {{ $user->phone_number ?? 'Chưa cung cấp' }}</p>
                        @if ($user->avatar)
                            <p><strong>Avatar:</strong> <img src="{{ asset($user->avatar) }}" alt="Avatar" width="50"
                                    style="border-radius:50%;border:1.5px solid #e3e6ea;"></p>
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
                <div class="checkout__address-info">
                    @if (auth()->check() && $addresses->count() > 0)
                        <form action="{{ route('checkout.update') }}" method="POST">
                            @csrf
                            <!-- Thêm trường ẩn để gửi selected_items -->
                            <input type="hidden" name="selected_items"
                                value="{{ json_encode(session('selected_items', [])) }}">
                            @foreach ($addresses as $address)
                                <div class="checkout__address-item {{ $address->is_default ? 'bg-light' : '' }}">
                                    <p>
                                        <input type="radio" name="selected_address" value="{{ $address->id }}"
                                            {{ $address->id == old('selected_address', session('checkout_address_id', $defaultAddress->id ?? '')) ? 'checked' : '' }}
                                            onchange="this.form.submit()">
                                        <strong>{{ $address->fullname ?? 'Chưa cung cấp họ tên' }}</strong>
                                        (+84)
                                        {{ $address->phone_number && preg_match('/^0[0-9]{9}$/', $address->phone_number) ? $address->phone_number : 'Số điện thoại không hợp lệ' }}
                                    </p>
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
                                    <a href="{{ route('address.edit', ['id' => $address->id]) }}"
                                        class="checkout__btn-main mt-2">Cập nhật</a>
                                </div>
                            @endforeach
                            <div class="mb-3">
                                <a href="{{ route('address.create') }}"
                                    class="btn btn-outline-secondary btn-sm rounded-pill">Thêm địa chỉ mới</a>
                            </div>
                            @error('selected_address')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </form>
                    @else
                        <p class="text-muted">Chưa có địa chỉ. Vui lòng thêm địa chỉ mới.</p>
                        <a href="{{ route('address.create') }}" class="btn btn-outline-primary btn-sm rounded-pill">Thêm
                            địa chỉ</a>
                    @endif
                </div>
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
                            <li class="checkout__cart-item">
                                <img src="{{ $item->product->image_url ?? asset('assets/img/product/placeholder.jpg') }}"
                                    alt="{{ $item->product->name }}">
                                <div>
                                    <p>{{ $item->product->name ?? 'Sản phẩm ' . $item->product_id }}</p>
                                    @if ($item->product_variant_id)
                                        <small>Loại: {{ $item->variant_name ?? 'N/A' }}</small>
                                    @endif
                                </div>
                                <span>{{ number_format($item->price_at_time ?? $item->product->price) }}</span>
                                <span>{{ $item->quantity }}</span>
                                <span>{{ number_format(($item->price_at_time ?? $item->product->price) * $item->quantity) }}</span>
                            </li>
                        @endforeach
                    @endforeach
                </ul>
                <form action="{{ route('checkout.update') }}" method="POST" class="mt-3">
                    @csrf
                    <label for="note">Lời nhắn:</label>
                    <textarea name="note" id="note" class="checkout__note" placeholder="Lưu ý cho Người bán..."
                        onchange="this.form.submit()">{{ old('note', session('note', '')) }}</textarea>
                    @error('note')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </form>
            </div>
            <div class="checkout__block checkout__shipping-method">
                <div class="checkout__block-title">Phương thức vận chuyển</div>
                <form action="{{ route('checkout.update') }}" method="POST" id="shippingForm">
                    @csrf
                    <div
                        class="form-check mb-3 {{ old('shipping_type', session('shipping_type', 'thường')) === 'thường' ? 'selected' : '' }}">
                        <input class="form-check-input" type="radio" id="normal_shipping" name="shipping_type"
                            value="thường"
                            {{ old('shipping_type', session('shipping_type', 'thường')) === 'thường' ? 'checked' : '' }}
                            onchange="this.form.submit()" required>
                        <label class="form-check-label" for="normal_shipping">
                            Giao hàng thường - ₫16.500
                        </label>
                        <p class="text-muted small mb-1">
                            Dự kiến giao hàng từ
                            <strong>{{ \Carbon\Carbon::today()->addDays(2)->format('d/m/Y') }}</strong>
                            đến <strong>{{ \Carbon\Carbon::today()->addDays(4)->format('d/m/Y') }}</strong>
                        </p>
                        <p class="text-muted small">
                            Nhận Voucher <strong>₫15.000</strong> nếu giao hàng sau
                            <strong>{{ \Carbon\Carbon::today()->addDays(4)->format('d/m/Y') }}</strong>
                        </p>
                    </div>
                    <div
                        class="form-check {{ old('shipping_type', session('shipping_type', 'thường')) === 'nhanh' ? 'selected' : '' }}">
                        <input class="form-check-input" type="radio" id="fast_shipping" name="shipping_type"
                            value="nhanh"
                            {{ old('shipping_type', session('shipping_type', 'thường')) === 'nhanh' ? 'checked' : '' }}
                            onchange="this.form.submit()">
                        <label class="form-check-label" for="fast_shipping">
                            Giao hàng nhanh - ₫30.000
                        </label>
                        <p class="text-muted small mb-1">
                            Dự kiến giao hàng trong vòng <strong>4 giờ</strong> nếu đặt trước 16:00 hôm nay
                        </p>
                        <p class="text-muted small">
                            Hỗ trợ <strong>đồng kiểm</strong> (kiểm tra hàng trước khi nhận)
                        </p>
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
                                <img src="{{ asset('assets/img/icon/vnpay-logo.png') }}" alt="VNPay" width="50">
                            @endif
                        </label>
                    </div>
                    <button class="checkout__btn-main" data-bs-toggle="modal" data-bs-target="#paymentModal">Thay
                        đổi</button>
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
                    <div class="d-flex justify-content-between align-items-center mt-3">
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
                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill">Xóa mã giảm
                                giá</button>
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
                        <li><span>Giảm giá ({{ $coupon->code }})</span><span
                                class="text-danger">-{{ number_format($discount) }} ₫</span></li>
                    @endif
                    <li class="total"><span>Tổng thanh
                            toán</span><span>{{ number_format($cartTotal + $shippingFee - $discount) }} ₫</span></li>
                </ul>
                <form id="checkoutForm" action="{{ route('checkout.process') }}" method="POST">
                    @csrf
                    <input type="hidden" name="shipping_type"
                        value="{{ old('shipping_type', session('shipping_type', 'thường')) }}">
                    <input type="hidden" name="payment_method"
                        value="{{ old('payment_method', session('payment_method', 'cod')) }}">
                    <input type="hidden" name="address_id"
                        value="{{ old('address_id', session('checkout_address_id', $defaultAddress->id ?? '')) }}">
                    <input type="hidden" name="note" value="{{ old('note', session('note', '')) }}">
                    <button type="submit" class="checkout__submit-btn">Đặt hàng</button>
                    <p class="text-muted small mt-2">Nhấn "Đặt hàng" đồng nghĩa với việc bạn đồng ý tuân theo
                        <a href="#">Điều khoản</a>
                    </p>
                    @error('address_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    @error('shipping_type')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    @error('payment_method')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </form>
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
                            <div class="form-check mb-3">
                                <input type="radio" id="cod_modal" name="payment_method" value="cod"
                                    class="form-check-input"
                                    {{ old('payment_method', session('payment_method', 'cod')) === 'cod' ? 'checked' : '' }}>
                                <label for="cod_modal" class="form-check-label">Thanh toán khi nhận hàng</label>
                            </div>
                            <div class="form-check mb-3">
                                <input type="radio" id="vnpay_modal" name="payment_method" value="vnpay"
                                    class="form-check-input"
                                    {{ old('payment_method', session('payment_method', 'cod')) === 'vnpay' ? 'checked' : '' }}>
                                <label for="vnpay_modal" class="form-check-label">VNPay <img
                                        src="{{ asset('assets/img/icon/vnpay-logo.png') }}" alt="VNPay"
                                        width="50"></label>
                            </div>
                            @error('payment_method')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light rounded-pill"
                                data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-primary rounded-pill">Xác nhận</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cải thiện trải nghiệm chọn phương thức vận chuyển
            const shippingOptions = document.querySelectorAll('.checkout__shipping-method .form-check');

            shippingOptions.forEach(option => {
                const radio = option.querySelector('input[type="radio"]');

                // Thêm class selected khi radio được chọn
                if (radio.checked) {
                    option.classList.add('selected');
                }

                // Xử lý khi click vào toàn bộ option
                option.addEventListener('click', function(e) {
                    if (e.target !== radio) {
                        radio.checked = true;
                        updateSelectedState();
                        radio.form.submit();
                    }
                });

                // Xử lý khi radio thay đổi
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
