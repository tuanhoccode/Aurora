@extends('client.layouts.default')

@section('title', 'Giỏ hàng - Aurora')

@section('content')
    <style>
        .cart-table {
            width: 100%;
            background: #e6f9f3;
            border-radius: 12px;
            margin-bottom: 32px;
            border-collapse: separate;
            border-spacing: 0;
            box-shadow: 0 2px 12px rgba(94,209,178,0.04);
            overflow: hidden;
        }

        .cart-table th,
        .cart-table td {
            vertical-align: middle;
            background: #fff;
            border: none;
        }

        .cart-table th {
            background: #e6f9f3;
            font-weight: 600;
            font-size: 1rem;
            padding: 18px 16px;
            color: #222d3a;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .cart-table td {
            padding: 20px 16px;
            border-bottom: 1px solid #f0f0f0;
        }

        .cart-table tr:last-child td {
            border-bottom: none;
        }

        .cart-product-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            background: #e6f9f3;
            display: block;
            margin: 0 auto;
            transition: transform 0.2s;
        }

        .cart-product-img:hover {
            transform: scale(1.05);
        }

        .cart-product-name {
            font-weight: 500;
            font-size: 1.1rem;
            color: #222d3a;
            margin-left: 0;
            text-decoration: none;
            transition: color 0.2s;
            display: block;
        }

        .cart-product-name:hover {
            color: #38b99d;
        }

        .cart-product-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .cart-qty-modern {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1.5px solid #5ed1b2;
            border-radius: 999px;
            background: #fff;
            padding: 0 8px;
            width: fit-content;
            min-width: 110px;
            height: 38px;
            overflow: hidden;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .qty-btn {
            width: 32px;
            height: 32px;
            border: none;
            background: none;
            font-size: 18px;
            color: #333;
            cursor: pointer;
            transition: background 0.2s;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qty-btn:hover {
            background: #e6f9f3;
            color: #38b99d;
        }

        .qty-input {
            width: 40px;
            border: none;
            text-align: center;
            font-size: 16px;
            outline: none;
            background: transparent;
            -moz-appearance: textfield;
        }

        .qty-input::-webkit-outer-spin-button,
        .qty-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .cart-remove-btn {
            color: #5ed1b2;
            background: none;
            border: none;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            border-radius: 20px;
            padding: 8px 16px;
            transition: all 0.2s;
        }

        .cart-remove-btn:hover {
            color: #fff;
            background: #5ed1b2;
        }

        .cart-price {
            color: #5ed1b2;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .cart-empty {
            text-align: center;
            color: #64748b;
            padding: 60px 0;
            font-size: 1.1rem;
        }

        .cart-summary {
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(94,209,178,0.06);
            background: #fff;
            padding: 32px 24px;
            margin-bottom: 24px;
            color: #222d3a;
        }

        .cart-summary-title {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: #222d3a;
        }

        .cart-summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 14px;
            color: #6b7280;
            font-size: 1rem;
        }

        .cart-summary-row .cart-summary-total {
            font-weight: 700;
            font-size: 1.15rem;
        }

        .cart-summary-btn {
            margin-top: 22px;
            width: 100%;
            font-size: 1.08rem;
            font-weight: 600;
            border-radius: 999px;
            padding: 14px 0;
            background: #5ed1b2;
            color: #fff;
            border: none;
            transition: background 0.2s;
        }

        .cart-summary-btn:hover {
            background: #38b99d;
        }

        .coupon-form {
            margin: 20px 0;
            padding: 16px;
            background: #f8f9fb;
            border-radius: 10px;
        }

        .coupon-form label {
            color: #344767;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .coupon-form .input-group {
            display: flex;
            align-items: stretch;
        }

        .coupon-form .form-control {
            border: 1.5px solid #861944;
            border-right: none;
            border-radius: 8px 0 0 8px !important;
            background: #fff;
            height: 44px;
            font-size: 1rem;
            box-shadow: none;
        }

        .coupon-form .btn-primary {
            background: #5ed1b2;
            border: none;
            border-radius: 0 8px 8px 0;
            font-weight: 600;
            transition: background 0.2s;
        }

        .coupon-form .btn-primary:hover {
            background: #38b99d;
        }

        .update-cart-btn {
            padding: 12px 24px;
            border-radius: 8px;
            background: #e6f9f3;
            color: #5ed1b2;
            border: 1.5px solid #5ed1b2;
            font-weight: 500;
            transition: all 0.2s;
        }

        .update-cart-btn:hover {
            background: #5ed1b2;
            color: #fff;
            border-color: #38b99d;
        }

        @media (max-width: 767px) {
            .cart-table th,
            .cart-table td {
                font-size: 0.95rem;
                padding: 12px 8px;
            }

            .cart-product-img {
                width: 60px;
                height: 60px;
            }

            .cart-product-name {
                font-size: 1rem;
            }

            .cart-qty-modern {
                width: 110px;
                height: 34px;
            }

            .qty-btn {
                flex: 0 0 34px;
                width: 34px;
                height: 34px;
            }

            .qty-input {
                font-size: 0.875rem;
            }

            .cart-summary {
                padding: 24px 20px;
            }
        }

        .cart-empty i {
            color: #861944 !important;
        }

        .cart-empty .btn {
            background: #5ed1b2 !important;
            color: #fff !important;
            border-radius: 999px;
            transition: background 0.2s;
        }

        .cart-empty .btn:hover {
            background: #38b99d !important;
        }

        .btn-custom {
            background: #fff;
            color: #861944;
            border: 1.5px solid #861944;
            border-radius: 999px;
            padding: 12px 32px;
            font-size: 1.1rem;
            font-weight: 500;
            transition: background 0.2s, color 0.2s, border 0.2s;
            cursor: pointer;
            outline: none;
            box-shadow: none;
            display: inline-block;
        }
        .btn-custom:hover,
        .btn-custom:focus {
            background: #861944;
            color: #fff;
            border-color: #861944;
        }
        .btn-custom-solid {
            background: #861944;
            color: #fff;
            border: 1.5px solid #861944;
            border-radius: 999px;
            padding: 12px 32px;
            font-size: 1.1rem;
            font-weight: 500;
            transition: background 0.2s, color 0.2s, border 0.2s;
            cursor: pointer;
            outline: none;
            box-shadow: none;
            display: inline-block;
        }
        .btn-custom-solid:hover,
        .btn-custom-solid:focus {
            background: #fff;
            color: #861944;
            border-color: #861944;
        }
        .btn-custom:active, .btn-custom-solid:active {
            opacity: 0.92;
        }
        .input-group .form-control {
            border: 1.5px solid #861944;
            border-radius: 8px 0 0 8px !important;
            background: #fff;
            height: 44px;
            font-size: 1rem;
            box-shadow: none;
            border-right: 1.5px solid #861944;
        }
        .input-group .btn-custom {
            border: 1.5px solid #861944;
            border-radius: 0 8px 8px 0 !important;
            background: #fff;
            color: #861944;
            height: 44px;
            font-size: 1rem;
            padding: 0 24px;
            font-weight: 500;
            transition: background 0.2s, color 0.2s, border 0.2s;
            border-left: 1.5px solid #861944;
        }
        .input-group .btn-custom:hover,
        .input-group .btn-custom:focus {
            background: #861944;
            color: #fff;
            border-left: 1.5px solid #861944;
            border-right: 1.5px solid #861944;
        }
    </style>
    <!-- breadcrumb area start -->
    <section class="breadcrumb__area include-bg pt-95 pb-50">
        <div class="container">
            <div class="row">
                <div class="col-xxl-12">
                    <div class="breadcrumb__content p-relative z-index-1">
                        <h3 class="breadcrumb__title">Giỏ hàng
                            @if(isset($cartItems) && count($cartItems))
                                <span style="font-size:1rem; color:#5ed1b2; font-weight:400;"> ({{ $cartItems->sum('quantity') }} sản phẩm)</span>
                            @endif
                        </h3>
                        <div class="breadcrumb__list">
                            <span><a href="{{ route('home') }}">Trang chủ</a></span>
                            <span>Giỏ hàng</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- breadcrumb area end -->

    <!-- cart area start -->
    <section class="tp-cart-area pb-120">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mb-4 mb-lg-0">
                    @php
                        // $cartItems và $cartTotal sẽ được truyền từ controller, không dùng dữ liệu mẫu ở đây nữa
                    @endphp
                    @if (isset($cartItems) && count($cartItems))
                        <table class="cart-table">
                            <thead>
                                <tr>
                                    <th style="width: 10%"></th>
                                    <th style="width: 40%">Sản phẩm</th>
                                    <th style="width: 18%" class="text-end">Giá</th>
                                    <th style="width: 18%" class="text-center">Số lượng</th>
                                    <th style="width: 14%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cartItems as $item)
                                    <tr>
                                        <td>
                                            <img src="{{ $item->product->image_url }}" class="cart-product-img" alt="">
                                        </td>
                                        <td>
                                            <a href="{{ route('product.show', $item->product->slug) }}" class="cart-product-name">{{ $item->product->name }}</a>
                                        </td>
                                        <td class="text-end cart-price">
                                            {{ number_format($item->price, 0, ',', '.') }}₫
                                        </td>
                                        <td class="text-center">
                                            <form action="{{ route('cart.update', $item->id) }}" method="POST" style="display:inline-flex; align-items:center; gap:4px;">
                                                @csrf
                                                <div class="cart-qty-modern">
                                                    <button type="button" class="qty-btn qty-btn-minus" onclick="this.parentNode.querySelector('input[type=number]').stepDown()">－</button>
                                                    <input type="number" name="quantity" class="qty-input" value="{{ $item->quantity }}" min="1" max="99">
                                                    <button type="button" class="qty-btn qty-btn-plus" onclick="this.parentNode.querySelector('input[type=number]').stepUp()">＋</button>
                                                </div>
                                                <button type="submit" class="btn-custom" style="margin-left:6px; font-size:0.95rem; padding:6px 12px;">Cập nhật</button>
                                            </form>
                                        </td>
                                        <td class="text-end">
                                            <form action="{{ route('cart.remove', $item->id) }}" method="POST" style="display:inline">
                                                @csrf
                                                <button class="cart-remove-btn btn-custom" type="submit">× Xóa</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="update-cart-btn btn-custom" onclick="location.reload()">
                                <i class="fas fa-sync-alt me-2"></i>Cập nhật giỏ hàng
                            </button>
                        </div>
                    @else
                        <div class="cart-empty">
                            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 60px 0;">
                                <i class="fas fa-shopping-cart mb-3 d-block" style="font-size: 4rem; color: #861944;"></i>
                                <div style="font-size: 1.25rem; font-weight: 600; color: #222d3a; margin-bottom: 8px;">Giỏ hàng của bạn đang trống</div>
                                <div style="color: #6b7280; font-size: 1rem; margin-bottom: 24px;">Bạn chưa thêm sản phẩm nào vào giỏ hàng.<br>Khám phá các sản phẩm hấp dẫn của Aurora ngay nhé!</div>
                                <a href="{{ route('home') }}" class="btn-custom">Tiếp tục mua sắm</a>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="col-lg-4">
                    <div class="cart-summary">
                        <div class="cart-summary-title">Tóm tắt đơn hàng</div>
                        <div class="cart-summary-row">
                            <span>Tạm tính</span>
                            <span>{{ isset($cartTotal) ? number_format($cartTotal, 0, ',', '.') : '0' }}₫</span>
                        </div>
                        <div class="cart-summary-row">
                            <span>Phí vận chuyển</span>
                            <span>0đ</span>
                        </div>
                        <form action="#" method="POST" class="coupon-form">
                            @csrf
                            <div>
                                <label for="coupon" class="form-label">Mã giảm giá</label>
                                <div class="input-group">
                                    <input type="text" name="coupon" id="coupon" class="form-control" placeholder="Nhập mã giảm giá">
                                    <button class="btn-custom" type="submit">Áp dụng</button>
                                </div>
                            </div>
                        </form>
                        <hr style="border-top: 1px solid #e5e7eb; margin: 24px 0;">
                        <div class="cart-summary-row">
                            <span class="cart-summary-total">Tổng cộng</span>
                            <span class="cart-summary-total">0₫</span>
                        </div>
                        <a href="#" class="btn-custom-solid cart-summary-btn">Tiến hành thanh toán</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- cart area end -->
@endsection
