@extends('client.layouts.default')

@section('title', 'Thanh toán')

@section('content')
<style>
    .checkout__section {
        background: #f7f8fa;
        min-height: 100vh;
        padding: 0;
    }
    .checkout__container {
        width: 100%;
        max-width: 980px;
        margin: 0 auto;
        padding: 0 12px;
        box-sizing: border-box;
    }
    .checkout__block {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.07);
        padding: 2.2rem 2rem 1.7rem 2rem;
        margin-bottom: 28px;
        font-family: 'Segoe UI', Arial, sans-serif;
        width: 100%;
        box-sizing: border-box;
    }
    .checkout__block-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #23272f;
        margin-bottom: 1.2rem;
        letter-spacing: 0.01em;
    }
    .checkout__user-info p,
    .checkout__address-info p {
        margin-bottom: 0.5rem;
        font-size: 1.08rem;
        color: #444;
    }
    .checkout__address-item {
        border-radius: 10px;
        border: 1.2px solid #e3e6ea;
        background: #f8fafd;
        padding: 1rem 1.2rem;
        margin-bottom: 1rem;
        transition: border 0.18s, box-shadow 0.18s;
        position: relative;
    }
    .checkout__address-item.bg-light {
        background: #eaf4ff;
        border-color: #1677ff;
        box-shadow: 0 2px 8px rgba(22,119,255,0.07);
    }
    .checkout__address-item .badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        font-size: 0.95rem;
        border-radius: 8px;
        padding: 0.3rem 0.7rem;
    }
    .checkout__address-item .btn {
        margin-top: 0.5rem;
    }
    .checkout__cart-list {
        list-style: none;
        padding-left: 0;
        margin-bottom: 0;
    }
    .checkout__cart-header,
    .checkout__cart-item {
        display: grid;
        grid-template-columns: 60px 2.5fr 1fr 1fr 1fr;
        align-items: center;
        gap: 0.7rem;
        font-size: 1.08rem;
        padding: 0.7rem 0;
    }
    .checkout__cart-header {
        font-weight: 700;
        color: #7b7e85;
        border-bottom: 2px solid #e9ecef;
        margin-bottom: 0.5rem;
    }
    .checkout__cart-item {
        background: #f9fafb;
        border-radius: 10px;
        margin-bottom: 0.5rem;
        box-shadow: 0 1px 4px rgba(44,62,80,0.04);
        font-weight: 500;
    }
    .checkout__cart-item img {
        width: 48px;
        height: 48px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #e3e6ea;
        background: #fff;
    }
    .checkout__cart-item p {
        margin-bottom: 0;
        font-size: 1.05rem;
        color: #23272f;
        font-weight: 600;
    }
    .checkout__cart-item small {
        color: #7b7e85;
        font-size: 0.97rem;
    }
    .checkout__note {
        border-radius: 8px;
        border: 1.2px solid #e3e6ea;
        background: #f8fafd;
        font-size: 1.05rem;
        padding: 0.7rem 1rem;
        margin-top: 0.7rem;
        margin-bottom: 0.5rem;
        min-height: 44px;
    }
    .checkout__shipping-method .form-check {
        background: #f8fafd;
        border-radius: 10px;
        border: 1.2px solid #e3e6ea;
        padding: 1rem 1.2rem;
        margin-bottom: 1rem;
        transition: border 0.18s;
    }
    .checkout__shipping-method .form-check-input:checked ~ .form-check-label {
        color: #1677ff;
        font-weight: 600;
    }
    .checkout__payment-method {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1.2rem;
    }
    .checkout__coupon-group {
        display: flex;
        flex-direction: row;
        gap: 0.5rem;
        align-items: center;
        flex-wrap: nowrap;
        margin-bottom: 0.5rem;
    }
    .checkout__coupon-group .form-control {
        border-radius: 8px;
        border: 1.2px solid #e2e8f0;
        font-size: 1rem;
        height: 40px;
        box-shadow: none;
        padding: 0.2rem 1rem;
        background: #fff;
        color: #23272f;
        flex: 1 1 auto;
    }
    .checkout__coupon-group .btn {
        min-width: 110px;
        font-weight: 700;
        box-shadow: 0 2px 8px rgba(44, 62, 80, 0.10);
        border-radius: 999px;
        height: 40px;
        background: linear-gradient(90deg, #1677ff 60%, #4fc3f7 100%);
        color: #fff;
        border: none;
        font-size: 1.08rem;
        padding: 0 1.5rem;
        transition: background 0.18s, box-shadow 0.18s, color 0.18s;
        display: flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
    }
    .checkout__btn-main {
        min-width: 110px;
        font-weight: 700;
        box-shadow: 0 2px 8px rgba(44, 62, 80, 0.10);
        border-radius: 999px;
        height: 40px;
        background: linear-gradient(90deg, #1677ff 60%, #4fc3f7 100%);
        color: #fff !important;
        border: none;
        font-size: 1.08rem;
        padding: 0 1.5rem;
        transition: background 0.18s, box-shadow 0.18s, color 0.18s;
        display: flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
    }
    .checkout__btn-main:hover, .checkout__btn-main:focus {
        background: linear-gradient(90deg, #0056d6 60%, #1677ff 100%);
        color: #fff !important;
        box-shadow: 0 4px 16px rgba(44, 62, 80, 0.13);
        text-decoration: none;
    }
    @media (max-width: 600px) {
        .checkout__coupon-group {
            flex-direction: column;
            align-items: stretch;
            gap: 0.5rem;
        }
        .checkout__coupon-group .btn {
            width: 100%;
            min-width: 0;
            padding: 0.7rem 0;
        }
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
        font-size: 1.08rem;
        margin-bottom: 0.7rem;
    }
    .checkout__summary-list .total {
        font-size: 1.25rem;
        font-weight: 800;
        color: #1677ff;
    }
    .checkout__submit-btn {
        background: linear-gradient(90deg, #1677ff 60%, #4fc3f7 100%);
        color: #fff;
        font-size: 1.18rem;
        font-weight: 700;
        border-radius: 12px;
        padding: 1.1rem 0;
        width: 100%;
        margin-top: 1.2rem;
        box-shadow: 0 2px 8px rgba(44, 62, 80, 0.10);
        border: none;
        transition: background 0.18s, box-shadow 0.18s;
        text-transform: none;
        display: flex;
        align-items: center;
        justify-content: center;
        letter-spacing: 0.01em;
    }
    .checkout__submit-btn:hover,
    .checkout__submit-btn:focus {
        background: linear-gradient(90deg, #0056d6 60%, #1677ff 100%);
        color: #fff;
        box-shadow: 0 4px 16px rgba(44, 62, 80, 0.13);
    }
    @media (max-width: 991.98px) {
        .checkout__container { max-width: 100vw; padding: 0 4px; }
        .checkout__block { padding: 1.2rem 1vw 1rem 1vw; }
        .checkout__cart-header, .checkout__cart-item { grid-template-columns: 48px 2fr 1fr 1fr 1fr; font-size: 0.98rem; }
    }
    @media (max-width: 600px) {
        .checkout__block { padding: 0.7rem 2vw 0.7rem 2vw; }
        .checkout__cart-header, .checkout__cart-item { grid-template-columns: 36px 1.5fr 1fr 1fr 1fr; font-size: 0.93rem; }
        .checkout__submit-btn { font-size: 1rem; padding: 0.7rem 0; }
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
                        <p><strong>Avatar:</strong> <img src="{{ asset($user->avatar) }}" alt="Avatar" width="50" style="border-radius:50%;border:1.5px solid #e3e6ea;"></p>
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
                    <a href="{{ route('address.create') }}"
                        class="btn btn-outline-primary btn-sm rounded-pill">Thêm địa chỉ</a>
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
                <textarea name="note" id="note" class="checkout__note" placeholder="Lưu ý cho Người bán..." onchange="this.form.submit()">{{ old('note', session('note', '')) }}</textarea>
                @error('note')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </form>
        </div>
        <div class="checkout__block checkout__shipping-method">
            <div class="checkout__block-title">Phương thức vận chuyển</div>
            <form action="{{ route('checkout.update') }}" method="POST" id="shippingForm">
                @csrf
                <div class="form-check mb-3">
                    <input class="form-check-input" type="radio" id="normal_shipping" name="shipping_type"
                        value="thường"
                        {{ old('shipping_type', session('shipping_type', 'thường')) === 'thường' ? 'checked' : '' }}
                        onchange="this.form.submit()" required>
                    <label class="form-check-label" for="normal_shipping">
                        Giao hàng thường - ₫16.500
                    </label>
                    <p class="text-muted small mb-1">
                        Dự kiến giao hàng từ {{ \Carbon\Carbon::today()->addDays(2)->format('d/m/Y') }}
                        đến {{ \Carbon\Carbon::today()->addDays(4)->format('d/m/Y') }}
                    </p>
                    <p class="text-muted small">
                        Nhận Voucher ₫15.000 nếu giao hàng sau
                        {{ \Carbon\Carbon::today()->addDays(4)->format('d/m/Y') }}.
                    </p>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" id="fast_shipping" name="shipping_type"
                        value="nhanh"
                        {{ old('shipping_type', session('shipping_type', 'thường')) === 'nhanh' ? 'checked' : '' }}
                        onchange="this.form.submit()">
                    <label class="form-check-label" for="fast_shipping">
                        Giao hàng nhanh - ₫30.000
                    </label>
                    <p class="text-muted small mb-1">
                        Dự kiến giao hàng trong vòng 4 giờ nếu đặt trước 16:00 hôm nay.
                    </p>
                    <p class="text-muted small">
                        Hỗ trợ đồng kiểm (kiểm tra hàng trước khi nhận).
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
                <button class="checkout__btn-main" data-bs-toggle="modal"
                    data-bs-target="#paymentModal">Thay đổi</button>
            </div>
            @error('payment_method')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="checkout__block">
            <div class="checkout__block-title">Mã giảm giá</div>
            <form action="{{ route('checkout.apply-coupon') }}" method="POST" class="checkout__coupon-group">
                @csrf
                <input type="text" name="coupon_code" class="form-control"
                    placeholder="Nhập mã giảm giá" value="{{ old('coupon_code') }}">
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
                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill">Xóa mã giảm giá</button>
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
                        <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary rounded-pill">Xác nhận</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
