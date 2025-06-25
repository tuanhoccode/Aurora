<!-- cart mini area start -->
<div class="cartmini__overlay"></div>
<div class="cartmini__area cartmini__style-darkRed">
    <button type="button" class="cartmini__close-btn-new cartmini-close-btn" title="Đóng giỏ hàng">
        <i class="fa fa-times"></i>
    </button>
    <div class="cartmini__wrapper d-flex justify-content-between flex-column">
        <div class="cartmini__top-wrapper">
            <div class="cartmini__top p-relative">
                <div class="cartmini__top-title">
                    <h4>Giỏ hàng (<span id="mini-cart-item-count">{{ isset($miniCartItems) ? count($miniCartItems) : 0 }}</span>)</h4>
                </div>
            </div>
            <div class="cartmini__shipping-info" id="mini-cart-shipping-info">
                {{-- Thông tin phí vận chuyển sẽ được JS chèn vào đây --}}
            </div>
            <div class="cartmini__widget" id="cartmini-widget-container">
                <div class="cartmini__spinner" style="display:none;text-align:center;padding:24px 0;">
                    <i class="fa fa-spinner fa-spin fa-2x" style="color:#861944;"></i>
                </div>
                @if(isset($miniCartItems) && count($miniCartItems))
                    @foreach($miniCartItems as $item)
                        <div class="cartmini__widget-item position-relative">
                            <img src="{{ $item->image_url }}" alt="{{ $item->name }}">
                            <div class="flex-grow-1">
                                <div class="cartmini__item-title">{{ $item->name }}</div>
                                @if(!empty($item->variant))
                                    <div class="cartmini__item-variant">{{ $item->variant }}</div>
                                @endif
                                <div class="cartmini__item-qty">Số lượng: {{ $item->quantity }}</div>
                            </div>
                            <div class="cartmini__item-price">{{ number_format($item->price * $item->quantity, 0, ',', '.') }}₫</div>
                            <button class="cartmini__remove-btn" title="Xóa sản phẩm" data-id="{{ $item->id }}"><i class="fa fa-trash"></i></button>
                        </div>
                    @endforeach
                @else
                    <div class="cartmini__empty text-center" id="cartmini-empty">
                        <img src="{{ asset('assets2/img/product/cartmini/empty-cart.png') }}" alt="Giỏ hàng trống">
                        <p>Chưa có sản phẩm nào trong giỏ hàng</p>
                        <a href="{{ route('home') }}" class="tp-btn">Bắt đầu mua sắm</a>
                    </div>
                @endif
            </div>
        </div>
        <div class="cartmini__checkout">
            <div class="cartmini__checkout-title mb-20 d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Tổng phụ:</h4>
                <span id="cartmini-subtotal" class="fw-bold" style="font-size:1.25rem;color:#861944;">{{ isset($miniCartSubtotal) ? number_format($miniCartSubtotal, 0, ',', '.') . ' ₫' : '0 ₫' }}</span>
            </div>
            <div class="cartmini__checkout-btn">
                <a href="{{ route('shopping-cart.index') }}" class="tp-btn tp-btn-border w-100 mb-10">Xem giỏ hàng</a>
                <a href="{{ route('shopping-cart.checkout') }}" class="tp-btn w-100 tp-btn-checkout">Thanh toán</a>
            </div>
        </div>
    </div>
</div>
<!-- cart mini area end -->

<style>
.cartmini__widget-item {
    background: #f8f6fa;
    box-shadow: 0 2px 8px rgba(134,25,68,0.07);
    border: 1.5px solid #e2c6d6;
    border-radius: 18px;
    transition: box-shadow 0.2s, background 0.2s, transform 0.18s;
    display: flex;
    align-items: center;
    padding: 12px 14px;
    margin-bottom: 14px;
    position: relative;
}
.cartmini__widget-item:hover {
    background: #f3e6ef;
    box-shadow: 0 6px 20px rgba(134,25,68,0.13);
    transform: translateY(-2px) scale(1.025);
}
.cartmini__widget-item img {
    width: 68px;
    height: 68px;
    object-fit: cover;
    border-radius: 14px;
    margin-right: 16px;
    border: 2px solid #e2c6d6;
    background: #fff;
    box-shadow: 0 1px 4px rgba(134,25,68,0.07);
}
.cartmini__widget-item .flex-grow-1 {
    min-width: 0;
}
.cartmini__widget-item .cartmini__remove-btn {
    background: #fff;
    border: 1.5px solid #f44336;
    color: #f44336;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 1.15rem;
    margin-left: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s, color 0.2s, border 0.2s, box-shadow 0.18s;
    box-shadow: 0 1px 4px rgba(244,67,54,0.07);
    padding: 0;
    position: absolute;
    top: 12px;
    right: 12px;
    z-index: 2;
}
.cartmini__widget-item .cartmini__remove-btn:hover, .cartmini__widget-item .cartmini__remove-btn:focus {
    background: #f44336;
    color: #fff;
    border: 1.5px solid #f44336;
    box-shadow: 0 2px 8px rgba(244,67,54,0.13);
}
.cartmini__item-title {
    font-weight: 700;
    font-size: 1.05rem;
    color: #222d3a;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.cartmini__item-variant {
    font-size: 0.97rem;
    color: #888;
    margin-bottom: 2px;
}
.cartmini__item-qty {
    font-size: 0.97rem;
    color: #861944;
    font-weight: 500;
}
.cartmini__item-price {
    font-weight: 700;
    color: #861944;
    margin-left: 10px;
    font-size: 1.08rem;
    white-space: nowrap;
}
.cartmini__checkout-title h4 {
    font-size: 1.13rem;
    font-weight: 600;
    color: #222d3a;
}
.cartmini__checkout-title span {
    font-size: 1.25rem;
    font-weight: 700;
    color: #861944;
}
.cartmini__checkout-btn .tp-btn,
.cartmini__checkout-btn .tp-btn-border,
.cartmini__checkout-btn .tp-btn-checkout {
    background: #861944;
    color: #fff;
    border: 1.5px solid #861944;
    border-radius: 999px;
    font-size: 1rem;
    font-weight: 600;
    padding: 10px 0;
    margin-bottom: 6px;
    transition: background 0.25s, color 0.2s, border 0.2s, box-shadow 0.2s, transform 0.15s;
    box-shadow: 0 2px 8px rgba(134,25,68,0.08);
}
.cartmini__checkout-btn .tp-btn:hover,
.cartmini__checkout-btn .tp-btn-border:hover,
.cartmini__checkout-btn .tp-btn-checkout:hover,
.tp-btn:hover,
.tp-btn-border:hover,
.tp-btn-checkout:hover {
    background: #fff !important;
    color: #861944 !important;
    border: 1.5px solid #861944 !important;
    box-shadow: 0 4px 16px rgba(134,25,68,0.13);
    transform: translateY(-2px) scale(1.03);
}
.cartmini__empty img {
    width: 90px;
    margin-bottom: 18px;
}
.cartmini__empty p {
    font-size: 1.1rem;
    color: #888;
    margin-bottom: 18px;
}
.cartmini__empty .tp-btn {
    background: #861944;
    color: #fff;
    border-radius: 999px;
    font-weight: 600;
    padding: 10px 28px;
    font-size: 1rem;
    transition: background 0.25s, color 0.2s, border 0.2s, box-shadow 0.2s, transform 0.15s;
    box-shadow: 0 2px 8px rgba(134,25,68,0.08);
    border: 1.5px solid #861944;
}
.cartmini__empty .tp-btn:hover {
    background: #fff;
    color: #861944;
    border: 1.5px solid #861944;
    box-shadow: 0 4px 16px rgba(134,25,68,0.13);
    transform: translateY(-2px) scale(1.03);
}
@media (max-width: 600px) {
    .cartmini__widget-item {
        flex-direction: column;
        align-items: flex-start;
        padding: 10px 8px;
    }
    .cartmini__widget-item img {
        width: 90vw;
        max-width: 120px;
        height: 60vw;
        max-height: 80px;
        margin-bottom: 8px;
    }
    .cartmini__item-title, .cartmini__item-variant, .cartmini__item-qty, .cartmini__item-price {
        font-size: 1.05rem;
    }
    .cartmini__item-price {
        margin-left: 0;
        margin-top: 4px;
    }
    .cartmini__widget-item .cartmini__remove-btn {
        top: 8px;
        right: 8px;
        width: 32px;
        height: 32px;
        font-size: 1rem;
    }
    .cartmini__area {
        width: 95vw; /* On mobile, take up most of the screen */
        min-width: initial; /* Reset min-width */
    }
}
.cartmini__area {
    width: 33.33vw;
    min-width: 400px;
    max-width: 550px;
}
.cartmini__close-btn-new {
    position: absolute;
    top: 15px;
    right: 18px;
    font-size: 1.5rem;
    color: #888;
    background: transparent;
    border: none;
    cursor: pointer;
    z-index: 100;
    padding: 5px;
    line-height: 1;
    transition: transform 0.25s, color 0.25s;
}
.cartmini__close-btn-new:hover {
    transform: rotate(90deg) scale(1.15);
    color: #f44336;
}
</style>
<!-- Đã loại bỏ toàn bộ script dữ liệu mẫu. Hãy dùng JS thực tế để render dữ liệu mini-cart. -->