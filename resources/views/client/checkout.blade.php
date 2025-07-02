@extends('client.layouts.default')

@section('title', 'Thanh toán')

@section('content')
    <!-- checkout area start -->
    <section class="tp-checkout-area pb-120" data-bg-color="#EFF1F5">
        <div class="container">
            <div class="row">
                <div class="col-lg-7">
                    <div class="tp-checkout-bill-area">
                        <h3 class="tp-checkout-bill-title">Thông tin thanh toán</h3>

                        {{-- Hiển thị lỗi --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="tp-checkout-bill-form">
                            <form action="{{ route('checkout.process') }}" method="POST">
                                @csrf
                                <div class="tp-checkout-bill-inner">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="tp-checkout-input">
                                                <label>Họ và tên <span>*</span></label>
                                                <input type="text" name="fullname" value="{{ old('fullname') }}" placeholder="Họ và tên" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="tp-checkout-input">
                                                <label>Địa chỉ <span>*</span></label>
                                                <input type="text" name="address" value="{{ old('address') }}" placeholder="Số nhà và tên đường" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="tp-checkout-input">
                                                <label>Thành phố <span>*</span></label>
                                                <input type="text" name="city" value="{{ old('city') }}" placeholder="Thành phố" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="tp-checkout-input">
                                                <label>Số điện thoại <span>*</span></label>
                                                <input type="text" name="phone_number" value="{{ old('phone_number') }}" placeholder="Số điện thoại" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="tp-checkout-input">
                                                <label>Email <span>*</span></label>
                                                <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="tp-checkout-input">
                                                <label>Ghi chú đơn hàng (tùy chọn)</label>
                                                <textarea name="note" placeholder="Ghi chú về đơn hàng, ví dụ: lưu ý đặc biệt khi giao hàng">{{ old('note') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tp-checkout-payment">
                                    <h3 class="tp-checkout-bill-title">Phương thức thanh toán</h3>
                                    <div class="tp-checkout-payment-item">
                                        <input type="radio" id="cod" name="payment_method" value="cod" checked>
                                        <label for="cod">Thanh toán khi nhận hàng (COD)</label>
                                    </div>
                                    <div class="tp-checkout-payment-item">
                                        <input type="radio" id="vnpay" name="payment_method" value="vnpay">
                                        <label for="vnpay">VNPay <img src="{{ asset('assets/img/icon/vnpay-logo.png') }}" alt="VNPay"></label>
                                    </div>
                                </div>

                                <div class="tp-checkout-btn-wrapper">
                                    <button type="submit" class="tp-checkout-btn w-100">Đặt hàng</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="tp-checkout-place white-bg">
                        <h3 class="tp-checkout-place-title">Đơn hàng của bạn</h3>
                        <div class="tp-order-info-list">
                            <ul>
                                <li class="tp-order-info-list-header">
                                    <h4>Sản phẩm</h4>
                                    <h4>Tổng</h4>
                                </li>
                                @foreach ($cart->items as $item)
                                    <li class="tp-order-info-list-desc">
                                        <p>{{ $item->product->name ?? 'Sản phẩm ' . $item->product_id }} <span> x {{ $item->quantity }}</span></p>
                                        <span>{{ number_format($item->quantity) }}</span>
                                    </li>
                                @endforeach
                                <li class="tp-order-info-list-subtotal">
                                    <span>Tạm tính</span>
                                    <span>{{ number_format($cart->items->sum(fn($item) => $item->price_at_time * $item->quantity)) }}</span>
                                </li>
                                <li class="tp-order-info-list-shipping">
                                    <span>Phí vận chuyển</span>
                                    <span>20.000</span>
                                </li>
                                <li class="tp-order-info-list-total">
                                    <span>Tổng cộng</span>
                                    <span>{{ number_format($cart->items->sum(fn($item) => $item->price_at_time * $item->quantity) + 20000) }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <!-- checkout area end -->
@endsection
