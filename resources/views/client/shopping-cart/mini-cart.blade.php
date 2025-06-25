<!-- cart mini area start -->
<div class="cartmini__area cartmini__style-darkRed">
    <div class="cartmini__wrapper d-flex justify-content-between flex-column">
        <div class="cartmini__top-wrapper">
            <div class="cartmini__top p-relative">
                <div class="cartmini__top-title">
                    <h4>Giỏ hàng (<span id="mini-cart-item-count">0</span>)</h4>
                </div>
                <div class="cartmini__close">
                    <button type="button" class="cartmini__close-btn cartmini-close-btn"><i
                            class="fal fa-times"></i></button>
                </div>
            </div>

            <div class="cartmini__shipping-info" id="mini-cart-shipping-info">
                {{-- Thông tin phí vận chuyển sẽ được JS chèn vào đây --}}
            </div>
            <div class="cartmini__widget" id="cartmini-widget-container">
                @if(isset($miniCartItems) && count($miniCartItems))
                    @foreach($miniCartItems as $item)
                        <div class="cartmini__widget-item">
                            <img src="{{ $item->image_url }}" alt="{{ $item->name }}">
                            <div class="flex-grow-1">
                                <div class="cartmini__item-title">{{ $item->name }}</div>
                                @if(!empty($item->variant))
                                    <div class="cartmini__item-variant">{{ $item->variant }}</div>
                                @endif
                                <div class="cartmini__item-qty">Số lượng: {{ $item->quantity }}</div>
                            </div>
                            <div class="cartmini__item-price">{{ number_format($item->price * $item->quantity, 0, ',', '.') }}₫</div>
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
            <div class="cartmini__checkout-title mb-20">
                <h4>Tổng phụ:</h4>
                <span id="cartmini-subtotal">{{ isset($miniCartSubtotal) ? number_format($miniCartSubtotal, 0, ',', '.') . ' ₫' : '0 ₫' }}</span>
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
    box-shadow: 0 2px 8px rgba(134,25,68,0.04);
    border: 1.5px solid #e2c6d6;
    transition: box-shadow 0.2s, background 0.2s;
}
.cartmini__widget-item:hover {
    background: #f3e6ef;
    box-shadow: 0 4px 16px rgba(134,25,68,0.10);
}
.cartmini__widget-item img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 12px;
    margin-right: 16px;
    border: 2px solid #e2c6d6;
    background: #fff;
}
.cartmini__widget-item .flex-grow-1 {
    min-width: 0;
}
.cartmini__widget-item .cartmini__remove-btn {
    background: #fff;
    border: 1.5px solid #90caf9;
    color: #1976d2;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 1.1rem;
    margin-left: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s, color 0.2s, border 0.2s;
    box-shadow: 0 1px 4px rgba(144,202,249,0.07);
    padding: 0;
}
.cartmini__widget-item .cartmini__remove-btn:hover, .cartmini__widget-item .cartmini__remove-btn:focus {
    background: #90caf9;
    color: #fff;
    border: 1.5px solid #90caf9;
}
.cartmini__item-title {
    font-weight: 700;
    font-size: 1rem;
    color: #222d3a;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.cartmini__item-variant {
    font-size: 0.95rem;
    color: #888;
    margin-bottom: 2px;
}
.cartmini__item-qty {
    font-size: 0.95rem;
    color: #861944;
    font-weight: 500;
}
.cartmini__item-price {
    font-weight: 700;
    color: #861944;
    margin-left: 10px;
    font-size: 1.05rem;
    white-space: nowrap;
}
.cartmini__checkout-title h4 {
    font-size: 1.1rem;
    font-weight: 600;
    color: #222d3a;
}
.cartmini__checkout-title span {
    font-size: 1.2rem;
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
</style>
<!-- Đã loại bỏ toàn bộ script dữ liệu mẫu. Hãy dùng JS thực tế để render dữ liệu mini-cart. -->