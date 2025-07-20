@extends('client.layouts.default')

@section('title', 'Thanh toán')

@section('content')
    <section class="tp-checkout-area pb-120" data-bg-color="#EFF1F5">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <!-- Success or Error Messages -->
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <!-- User Information Section -->
                    <div class="tp-checkout-place white-bg mb-4 p-4">
                        <h3 class="tp-checkout-place-title">Thông tin khách hàng</h3>
                        @if (auth()->check())
                            <div>
                                <p class="mb-1"><strong>Họ và tên:</strong> {{ $user->fullname }}</p>
                                <p class="mb-1"><strong>Email:</strong> {{ $user->email }}</p>
                                <p class="mb-1"><strong>Số điện thoại:</strong>
                                    {{ $user->phone_number ?? 'Chưa cung cấp' }}</p>
                                @if ($user->avatar)
                                    <p class="mb-1"><strong>Avatar:</strong> <img src="{{ asset($user->avatar) }}"
                                            alt="Avatar" width="50"></p>
                                @else
                                    <p class="mb-1 text-muted">Avatar: Chưa có</p>
                                @endif
                            </div>
                        @else
                            <p class="text-muted">Vui lòng đăng nhập để xem thông tin khách hàng.</p>
                        @endif
                    </div>

                    <!-- Shipping Address Section -->
                    <div class="tp-checkout-place white-bg mb-4 p-4">
                        <h3 class="tp-checkout-place-title">Địa chỉ nhận hàng</h3>
                        @if (auth()->check() && $addresses->count() > 0)
                            <form action="{{ route('checkout.update') }}" method="POST">
                                @csrf
                                @foreach ($addresses as $address)
                                    <div class="border p-3 mb-3 {{ $address->is_default ? 'bg-light' : '' }}">
                                        <p class="mb-1">
                                            <input type="radio" name="selected_address" value="{{ $address->id }}"
                                                {{ $address->id == old('selected_address', session('checkout_address_id', $defaultAddress->id ?? '')) ? 'checked' : '' }}
                                                onchange="this.form.submit()">
                                            <strong>{{ $address->fullname ?? 'Chưa cung cấp họ tên' }}</strong>
                                            (+84)
                                            {{ $address->phone_number && preg_match('/^0[0-9]{9}$/', $address->phone_number) ? $address->phone_number : 'Số điện thoại không hợp lệ' }}
                                        </p>
                                        <p class="mb-1">
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
                                            class="btn btn-outline-primary btn-sm rounded-pill mt-2">Cập nhật</a>
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

                    <!-- Cart Items Section -->
                    <div class="tp-checkout-place white-bg mb-4 p-4">
                        <h3 class="tp-checkout-place-title">Sản phẩm</h3>
                        <div class="tp-order-info-list">
                            <ul>
                                <li class="tp-order-info-list-header">
                                    <h4>Sản phẩm</h4>
                                    <h4>Đơn giá</h4>
                                    <h4>Số lượng</h4>
                                    <h4>Thành tiền</h4>
                                </li>
                                @foreach ($cartItems->groupBy('shop_id') as $shopId => $items)
                                    @foreach ($items as $item)
                                        <li class="tp-order-info-list-desc">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $item->product->image ?? asset('assets/img/product/placeholder.jpg') }}"
                                                    alt="{{ $item->product->name }}" width="50" class="me-2">
                                                <p>
                                                    {{ $item->product->name ?? 'Sản phẩm ' . $item->product_id }}
                                                    @if ($item->product_variant_id)
                                                        <br><small>Loại: {{ $item->variant_name ?? 'N/A' }}</small>
                                                    @endif
                                                </p>
                                            </div>
                                            <span>{{ number_format($item->price_at_time ?? $item->product->price) }}</span>
                                            <span>{{ $item->quantity }}</span>
                                            <span>{{ number_format(($item->price_at_time ?? $item->product->price) * $item->quantity) }}</span>
                                        </li>
                                    @endforeach
                                @endforeach
                            </ul>
                        </div>
                        <div class="mt-3">
                            <form action="{{ route('checkout.update') }}" method="POST">
                                @csrf
                                <label>Lời nhắn:</label>
                                <textarea name="note" class="form-control" placeholder="Lưu ý cho Người bán..." onchange="this.form.submit()">{{ old('note', session('note', '')) }}</textarea>
                                @error('note')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </form>
                        </div>
                    </div>

                    <!-- Shipping Method Section -->
                    <div class="tp-checkout-place white-bg mb-4 p-4">
                        <h4 class="tp-checkout-place-title">Phương thức vận chuyển</h4>
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

                    <!-- Payment Method Section -->
                    <div class="tp-checkout-place white-bg mb-4 p-4">
                        <h3 class="tp-checkout-place-title">Phương thức thanh toán</h3>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <label id="payment_method_label">
                                    {{ old('payment_method', session('payment_method', 'cod')) === 'cod' ? 'Thanh toán khi nhận hàng' : 'VNPay' }}
                                    @if (old('payment_method', session('payment_method', 'cod')) === 'vnpay')
                                        <img src="{{ asset('assets/img/icon/vnpay-logo.png') }}" alt="VNPay"
                                            width="50">
                                    @endif
                                </label>
                            </div>
                            <button class="btn btn-outline-primary btn-sm rounded-pill" data-bs-toggle="modal"
                                data-bs-target="#paymentModal">Thay đổi</button>
                        </div>
                        @error('payment_method')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Coupon Section -->
                    <div class="tp-checkout-place white-bg mb-4 p-4">
                        <h3 class="tp-checkout-place-title">Mã giảm giá</h3>
                        <form action="{{ route('checkout.apply-coupon') }}" method="POST">
                            @csrf
                            <div class="input-group mb-3">
                                <input type="text" name="coupon_code" class="form-control"
                                    placeholder="Nhập mã giảm giá" value="{{ old('coupon_code') }}">
                                <button type="submit" class="btn btn-primary rounded-pill">Áp dụng</button>
                            </div>
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

                    <!-- Order Summary and Submit -->
                    <div class="tp-checkout-place white-bg p-4">
                        <h3 class="tp-checkout-place-title">Tổng thanh toán ({{ $cartItems->sum('quantity') }} sản phẩm)
                        </h3>
                        <div class="tp-order-info-list">
                            <ul>
                                <li class="tp-order-info-list-subtotal">
                                    <span>Tổng tiền hàng</span>
                                    <span>{{ number_format($cartTotal) }} ₫</span>
                                </li>
                                <li class="tp-order-info-list-shipping">
                                    <span>Tổng tiền phí vận chuyển</span>
                                    <span>{{ number_format($shippingFee) }} ₫</span>
                                </li>
                                @if ($coupon)
                                    <li class="tp-order-info-list-discount">
                                        <span>Giảm giá ({{ $coupon->code }})</span>
                                        <span>-{{ number_format($discount) }} ₫</span>
                                    </li>
                                @endif
                                <li class="tp-order-info-list-total">
                                    <span>Tổng thanh toán</span>
                                    <span>{{ number_format($cartTotal + $shippingFee - $discount) }} ₫</span>
                                </li>
                            </ul>
                        </div>
                        <form id="checkoutForm" action="{{ route('checkout.process') }}" method="POST">
                            @csrf
                            <input type="hidden" name="shipping_type"
                                value="{{ old('shipping_type', session('shipping_type', 'thường')) }}">
                            <input type="hidden" name="payment_method"
                                value="{{ old('payment_method', session('payment_method', 'cod')) }}">
                            <input type="hidden" name="address_id"
                                value="{{ old('address_id', session('checkout_address_id', $defaultAddress->id ?? '')) }}">
                            <input type="hidden" name="note" value="{{ old('note', session('note', '')) }}">
                            <div class="tp-checkout-btn-wrapper mt-3">
                                <button type="submit" class="tp-checkout-btn w-100">Đặt hàng</button>
                                <p class="text-muted small mt-2">Nhấn "Đặt hàng" đồng nghĩa với việc bạn đồng ý tuân theo
                                    <a href="#">Điều khoản</a>
                                </p>
                            </div>
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
            </div>
        </div>
    </section>

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
@endsection
