<!-- cart mini area start -->
<div class="cartmini__overlay"></div>
<div class="cartmini__area cartmini__style-neutral">
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
                        <div class="cartmini__widget-item d-flex align-items-center">
                            <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}">
                            <div class="cartmini__item-info flex-grow-1">
                                <div class="cartmini__item-title">{{ $item->product->name }}</div>
                                @php
                                    $variant = $item->productVariant;
                                @endphp
                                @if ($variant)
                                    @php
                                        $getAttrValue = function($entity, $keywords) {
                                            if (!$entity || !isset($entity->attributeValues)) return null;
                                            foreach ($entity->attributeValues as $attrVal) {
                                                $attrName = strtolower($attrVal->attribute->name ?? '');
                                                foreach ($keywords as $kw) {
                                                    if (str_contains($attrName, $kw)) return $attrVal->value;
                                                }
                                            }
                                            return null;
                                        };
                                        $size = $getAttrValue($variant, ['size', 'kích']);
                                        $color = $getAttrValue($variant, ['color', 'màu']);
                                        
                                        $colorMap = [
                                            'đỏ' => '#FF0000', 'xanh' => '#00FF00', 'xanh lá' => '#00FF00', 'xanh dương' => '#0074D9',
                                            'vàng' => '#FFD600', 'đen' => '#000000', 'trắng' => '#FFFFFF', 'xám' => '#CBCBCB',
                                            'tím' => '#800080', 'cam' => '#FFA500', 'hồng' => '#FF69B4',
                                        ];
                                        $colorHex = '#e0e0e0'; // Default color
                                        if ($color) {
                                            $colorKey = strtolower(trim($color));
                                            foreach ($colorMap as $key => $hex) {
                                                if (strpos($colorKey, $key) !== false) {
                                                    $colorHex = $hex;
                                                    break;
                                                }
                                            }
                                        }
                                    @endphp
                                    <div class="cartmini__item-variant d-flex align-items-center gap-3 mb-1">
                                        <span class="cartmini__item-size">Size: <b>{{ $size ?? 'N/A' }}</b></span>
                                        <span class="cartmini__item-color d-flex align-items-center">
                                            Màu:
                                            <span class="cartmini__color-dot ms-1" style="background:{{ $colorHex }}"></span>
                                            <b class="ms-1">{{ $color ?? 'N/A' }}</b>
                                        </span>
                                    </div>
                                @endif
                                <div class="cartmini__item-qty">Số lượng: <b>{{ $item->quantity }}</b></div>
                            </div>
                            <div class="cartmini__item-actions">
                                <div class="cartmini__item-price">{{ number_format($item->price_at_time * $item->quantity, 0, ',', '.') }}₫</div>
                                <button class="cartmini__remove-btn" title="Xóa sản phẩm" data-id="{{ $item->id }}"><i class="fa-regular fa-trash-can"></i></button>
                            </div>
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
                <h4 class="mb-0">Tổng Phụ:</h4>
                <span id="cartmini-subtotal" class="fw-bold">{{ isset($miniCartSubtotal) ? number_format($miniCartSubtotal, 0, ',', '.') . ' ₫' : '0 ₫' }}</span>
            </div>
            <div class="cartmini__checkout-btn">
                <a href="{{ route('shopping-cart.index') }}" class="tp-btn tp-btn-border w-100 mb-10">Xem Giỏ Hàng</a>
                <a href="{{ route('shopping-cart.checkout') }}" class="tp-btn w-100 tp-btn-checkout">Thanh toán</a>
            </div>
        </div>
    </div>
</div>

<style>
.cartmini__area.cartmini__style-neutral {
    background-color: #f8f9fa;
    border-left: 1px solid #e9ecef;
    width: 33.33vw;
    min-width: 420px;
    max-width: 550px;
}

.cartmini__close-btn-new {
    position: absolute;
    top: 20px;
    right: 24px;
    font-size: 1.5rem;
    color: #909090;
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

.cartmini__top-title h4 {
    font-weight: 600;
    color: #1a202c;
    font-size: 1.5rem;
    padding: 1.5rem 1.75rem 1rem;
    margin-bottom: 0;
}

.cartmini__widget {
    padding: 0 1.75rem;
}

.cartmini__widget-item {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    border: 1px solid #e9ecef;
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1.25rem;
    margin-bottom: 1rem;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.cartmini__widget-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.08);
}

.cartmini__widget-item img {
    width: 70px;
    height: 70px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #f0f2f5;
    flex-shrink: 0;
}

.cartmini__item-info {
    min-width: 0; /* Important for flexbox ellipsis */
    flex-grow: 1;
    padding-top: 2px;
}

.cartmini__item-title {
    font-weight: 600;
    font-size: 1rem;
    color: #2d3748;
    margin-bottom: 0.5rem;
    white-space: normal;
    overflow: visible;
    text-overflow: clip;
}

.cartmini__item-actions {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.75rem; /* 12px */
    flex-shrink: 0;
    padding-top: 2px;
}

.cartmini__item-variant {
    font-size: 0.9rem;
    color: #718096;
    margin-bottom: 0.35rem;
}

.cartmini__item-variant b {
    color: #4a5568;
}

.cartmini__color-dot {
    display: inline-block;
    width: 15px;
    height: 15px;
    border-radius: 50%;
    border: 1px solid #ccc;
    vertical-align: middle;
}

.cartmini__item-qty {
    font-size: 0.9rem;
    color: #4a5568;
}

.cartmini__item-price {
    font-weight: 600;
    color: #1a202c;
    font-size: 1rem;
    white-space: nowrap;
    margin-bottom: auto; /* Pushes price to the top */
}

.cartmini__remove-btn {
    background: #f1f3f5;
    border: 1px solid #f1f3f5;
    color: #868e96;
    width: 38px;
    height: 38px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    padding: 0;
    flex-shrink: 0;
}
.cartmini__remove-btn:hover, .cartmini__remove-btn:focus {
    background: #e64951;
    border-color: #e64951;
    color: #fff;
    transform: scale(1.05);
}

/* Checkout Area */
.cartmini__checkout {
    background: #fff;
    padding: 1.5rem 1.75rem;
    border-top: 1px solid #e9ecef;
    box-shadow: 0 -4px 20px rgba(0,0,0,0.05);
}

.cartmini__checkout-title h4 {
    font-size: 1rem;
    font-weight: 500;
    color: #4a5568;
    padding: 0;
}
.cartmini__checkout-title span {
    font-size: 1.5rem;
    font-weight: 700;
    color: #c81f55;
}

.cartmini__checkout-btn .tp-btn {
    width: 100%;
    text-align: center;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    padding: 0.85rem;
    transition: all 0.2s;
}

.cartmini__checkout-btn .tp-btn-border {
    background-color: #fff;
    color: #2d3748;
    border: 1px solid #dee2e6;
}
.cartmini__checkout-btn .tp-btn-border:hover {
    background-color: #f8f9fa;
    color: #1a202c;
    border-color: #ced4da;
}

.cartmini__checkout-btn .tp-btn-checkout {
    background-color: #1a202c;
    color: #fff;
    border: 1px solid #1a202c;
}
.cartmini__checkout-btn .tp-btn-checkout:hover {
    background-color: #000;
    border-color: #000;
    transform: translateY(-2px);
}

/* Empty Cart */
.cartmini__empty {
    padding: 4rem 1rem;
    text-align: center;
}
.cartmini__empty img {
    max-width: 100px;
    margin-bottom: 1.5rem;
}
.cartmini__empty p {
    font-size: 1rem;
    color: #718096;
    margin-bottom: 1.5rem;
}
.cartmini__empty .tp-btn {
    background: #1a202c;
    color: #fff;
    border-radius: 8px;
    font-weight: 600;
    padding: 0.75rem 1.5rem;
    font-size: 0.95rem;
}
.cartmini__empty .tp-btn:hover {
    background: #000;
}

</style>
<!-- Đã loại bỏ toàn bộ script dữ liệu mẫu. Hãy dùng JS thực tế để render dữ liệu mini-cart. -->