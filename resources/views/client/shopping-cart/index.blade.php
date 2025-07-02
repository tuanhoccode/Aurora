@extends('client.layouts.default')

@section('title', 'Giỏ hàng - Aurora')

@section('content')
<style>
    /* Nâng cấp toàn diện giao diện giỏ hàng */
    .tp-cart-area {
        background-color: #f7f8fa;
        padding-top: 3rem;
        padding-bottom: 6rem;
    }

    .breadcrumb__area {
        background-color: #fff;
        border-bottom: 1px solid #e9ecef;
    }

    /* Bố cục Grid cho danh sách sản phẩm */
    .cart-items-grid {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    /* Cart Header for Desktop */
    .cart-header-row {
        display: grid;
        grid-template-columns: 110px 2.7fr 1fr 1.1fr 1.1fr 56px;
        gap: 1.2rem;
        font-size: 1.08rem;
        font-weight: 700;
        color: #7b7e85;
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 1rem;
        margin-bottom: 1rem;
        background: transparent;
        align-items: center;
    }

    .cart-header-row__product {
        grid-column: 1 / 3;
    }

    /* Thẻ sản phẩm */
    .cart-item-card {
        display: grid;
        grid-template-columns: 110px 2.5fr 1fr 1.1fr 1.1fr 56px;
        align-items: center;
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.07);
        border: 1px solid #e9ecef;
        padding: 1.2rem 1.5rem;
        transition: box-shadow 0.2s;
        gap: 1.2rem;
        min-height: 92px;
    }

    .cart-item-card:hover {
        box-shadow: 0 8px 32px rgba(0,0,0,0.12);
    }

    .cart-item-card__image {
        width: 90px;
        height: 90px;
        object-fit: cover;
        border-radius: 12px;
        border: 1px solid #f0f2f5;
        display: block;
        margin: 0 auto;
    }

    .cart-item-card__info {
        display: flex;
        flex-direction: column;
        justify-content: center;
        min-width: 0;
    }

    .cart-item-card__info .name {
        font-size: 1.13rem;
        font-weight: 700;
        color: #23272f;
        margin-bottom: 0.18rem;
        line-height: 1.25;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 220px;
        display: block;
    }

    .cart-item-card__info .meta-attributes {
        font-size: 0.97rem;
        color: #7b7e85;
        display: flex;
        flex-direction: column;
        gap: 2px;
        margin-top: 2px;
        min-width: 0;
        max-width: 220px;
    }

    .cart-item-card__info .meta-attributes .meta {
        display: flex;
        align-items: center;
        gap: 4px;
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 200px;
    }

    .cart-item-card__info .meta-attributes strong {
        font-weight: 500;
        color: #2d3748;
        margin-right: 2px;
    }

    .cart-item-card__info .meta-attributes .color-dot {
        display: inline-block;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 1px solid #ccc;
        margin-right: 6px;
        vertical-align: middle;
    }

    .cart-item-card__price,
    .cart-item-card__total {
        font-size: 1.08rem;
        font-weight: 600;
        color: #1a202c;
        text-align: center;
        max-width: 90px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .cart-item-card__quantity {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 0;
    }

    .cart-item-card__quantity .cart-qty {
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fff;
        border: 1.5px solid #e2e8f0;
        border-radius: 24px;
        height: 40px;
        min-width: 110px;
        max-width: 140px;
        padding: 0 6px;
        gap: 0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
    }

    .cart-qty .qty-btn {
        background: none;
        border: none;
        color: #4a90e2;
        font-size: 1.3rem;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        cursor: pointer;
        transition: background 0.18s, color 0.18s;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        outline: none;
    }

    .cart-qty .qty-btn:hover {
        background: #e3f0fc;
        color: #1565c0;
    }

    .cart-qty .qty-input {
        border: none;
        background: #fff;
        width: 38px;
        height: 36px;
        text-align: center;
        font-weight: 700;
        font-size: 1.15rem;
        color: #23272f !important;
        border-radius: 8px;
        outline: none;
        box-shadow: none;
        margin: 0 2px;
        display: inline-block;
        vertical-align: middle;
        max-width: 48px;
        min-width: 0;
    }

    .cart-qty .qty-input:focus {
        background: #f4faff;
    }

    .cart-item-card__remove form {
        display: contents;
    }

    .cart-item-card__remove {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .cart-item-card__remove .remove-btn {
        background: #f7f8fa;
        border: 1px solid #e2e8f0;
        color: #b0b3b8;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        transition: all 0.18s;
    }

    .cart-item-card__remove .remove-btn:hover {
        background: #ef5350;
        border-color: #ef5350;
        color: #fff;
    }

    /* Summary Box */
    .cart-summary-box {
        background: #fff;
        border-radius: 22px;
        box-shadow: 0 6px 32px rgba(0,0,0,0.09);
        border: 1px solid #e9ecef;
        padding: 2.4rem 2rem 2rem 2rem;
        position: sticky;
        top: 2rem;
        min-width: 280px;
        max-width: 100%;
    }

    .cart-summary-title {
        font-size: 1.35rem;
        font-weight: 800;
        color: #23272f;
        margin-bottom: 1.2rem;
        letter-spacing: 0.01em;
        text-align: left;
    }

    .cart-summary__item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.1rem;
        font-size: 1.05rem;
        color: #4a5568;
    }

    .cart-summary__item .text-success {
        color: #2ecc71 !important;
        font-weight: 600;
    }

    .cart-summary__coupon {
        margin: 1.2rem 0 1.5rem 0;
    }

    .cart-summary__coupon .coupon-toggle {
        color: #2d3748;
        text-decoration: none;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 1rem;
    }

    .cart-summary__coupon .coupon-input-group {
        display: flex;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
        margin-top: 0.7rem;
    }

    .cart-summary__coupon .form-control {
        border: none;
        box-shadow: none;
        height: 44px;
        font-size: 1rem;
    }

    .cart-summary__coupon .btn {
        background-color: #2d3748;
        color: #fff;
        border-radius: 0;
        padding: 0 1.5rem;
        font-weight: 600;
    }

    .cart-summary__total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 1.22rem;
        font-weight: 700;
        color: #23272f;
        margin: 1.2rem 0 0.7rem 0;
    }

    .checkout-btn {
        display: block;
        width: 100%;
        text-align: center;
        background-color: #23272f;
        color: #fff;
        padding: 1.1rem 0;
        border-radius: 12px;
        text-decoration: none;
        font-size: 1.13rem;
        font-weight: 800;
        margin-top: 1.2rem;
        transition: background 0.18s, color 0.18s, box-shadow 0.18s;
        box-shadow: 0 2px 8px rgba(44,62,80,0.07);
        letter-spacing: 0.01em;
    }

    .checkout-btn:hover {
        background: #4a90e2;
        color: #fff;
        box-shadow: 0 4px 16px rgba(44,62,80,0.13);
    }

    @media (max-width: 991.98px) {
        .cart-header-row, .cart-item-card {
            grid-template-columns: 90px 2fr 1fr 1fr 1fr 44px;
            font-size: 0.98rem;
        }
        .cart-summary-box { padding: 1.2rem 1rem; }
        .cart-summary-title { font-size: 1.13rem; }
        .checkout-btn { font-size: 1rem; padding: 0.9rem 0; }
    }

    @media (max-width: 600px) {
        .cart-header-row, .cart-item-card {
            grid-template-columns: 1fr;
            display: block;
        }
        .cart-item-card {
            padding: 1rem;
        }
        .cart-item-card__quantity .cart-qty {
            min-width: 80px;
            max-width: 100%;
            height: 34px;
        }
        .cart-qty .qty-input {
            width: 28px;
            height: 28px;
            font-size: 1rem;
        }
        .cart-qty .qty-btn {
            width: 28px;
            height: 28px;
            font-size: 1.05rem;
        }
    }

    .cart-summary-box, .cart-summary-title, .cart-summary__item, .cart-summary__total, .checkout-btn {
        font-family: 'Segoe UI', Arial, sans-serif !important;
    }
</style>

<!-- breadcrumb area start -->
<section class="breadcrumb__area include-bg pt-95 pb-50">
    <div class="container">
        <div class="row">
            <div class="col-xxl-12">
                <div class="breadcrumb__content p-relative z-index-1">
                    <h3 class="breadcrumb__title">Giỏ hàng của bạn</h3>
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
<section class="tp-cart-area">
    <div class="container">
        <div class="row">
            @if (isset($cartItems) && count($cartItems))
            <div class="col-lg-7 mb-4 mb-lg-0">
                <div class="cart-items-grid">
                    {{-- Cart Header --}}
                    <div class="cart-header-row">
                        <div class="cart-header-row__product">Sản phẩm</div>
                        <div>Giá</div>
                        <div>Số lượng</div>
                        <div class="cart-header-row__total">Tạm tính</div>
                        <div></div> {{-- for remove icon --}}
                    </div>
                    @foreach ($cartItems as $item)
                    @php
                    $product = $item->product;
                    $variant = $item->productVariant;
                    $unitPrice = $item->price_at_time;
                    @endphp
                    <div class="cart-item-card" data-item-id="{{ $item->id }}" data-unit-price="{{ $unitPrice }}">
                        <img src="{{ $product->image_url ?? asset('assets2/img/product/2/default.png') }}" alt="{{ $product->name }}" class="cart-item-card__image">

                        <div class="cart-item-card__info">
                            <h4 class="name" title="{{ $product->name }}">{{ $product->name }}</h4>
                            <div class="meta-attributes">
                                <div class="meta" title="{{ $variant->sku ?? $product->sku }}"><strong>Mã:</strong> {{ $variant->sku ?? $product->sku }}</div>
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
                                        $colorHex = '#e0e0e0';
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
                                    <div class="meta" title="{{ $size ?? 'N/A' }}"><strong>Size:</strong> {{ $size ?? 'N/A' }}</div>
                                    <div class="meta" title="{{ $color ?? 'N/A' }}"><strong>Màu:</strong>
                                        <span class="color-dot" style="background:{{ $colorHex }};"></span>
                                        {{ $color ?? 'N/A' }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="cart-item-card__price">
                            <span>{{ number_format($unitPrice, 0, ',', '.') }}₫</span>
                        </div>

                        <div class="cart-item-card__quantity">
                            <div class="cart-qty">
                                <button type="button" class="qty-btn" onclick="updateCartQty({{ $item->id }}, -1)">-</button>
                                <input type="text" class="qty-input" value="{{ $item->quantity }}" data-item-id="{{ $item->id }}">
                                <button type="button" class="qty-btn" onclick="updateCartQty({{ $item->id }}, 1)">+</button>
                            </div>
                        </div>

                        <div class="cart-item-card__total">
                            <span class="line-total-price">{{ number_format($unitPrice * $item->quantity, 0, ',', '.') }}₫</span>
                        </div>

                        <div class="cart-item-card__remove">
                            <form action="{{ url('/shopping-cart/remove/' . $item->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="remove-btn" title="Xóa sản phẩm">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="col-lg-4 offset-lg-1">
                <div class="cart-summary-box">
                    <h4 class="cart-summary-title">Tóm tắt đơn hàng</h4>
                    <div class="cart-summary__item">
                        <span>Tạm tính (<span class="cart-item-count">{{ $cartItems->sum('quantity') }}</span> sản phẩm)</span>
                        <span id="cart-subtotal">
  {{ isset($cartTotal) ? number_format($cartTotal + 20000, 0, ',', '.') : '0' }}₫
</span>
                    </div>
                    <div class="cart-summary__item">
                        <span>Phí vận chuyển</span>
                        <span class="text-success">20.000</span>
                    </div>
                    
                    <div class="cart-summary__coupon my-4">
                        <a href="#coupon-form" class="coupon-toggle" data-bs-toggle="collapse" aria-expanded="false" aria-controls="coupon-form">
                            <i class="fa-light fa-tag"></i> Bạn có mã giảm giá?
                        </a>
                        <div class="collapse mt-3" id="coupon-form">
                            <form class="coupon-input-group">
                                <input type="text" class="form-control" placeholder="Nhập mã giảm giá">
                                <button type="submit" class="btn">Áp dụng</button>
                            </form>
                        </div>
                    </div>

                    <hr class="my-4">
                    <div class="cart-summary__total">
                        <span>Tổng cộng</span>
                        <span id="cart-grand-total">{{ isset($cartTotal) ? number_format($cartTotal+ 20000, 0, ',', '.') : '0' }}₫</span>
                    </div>
                    <a href="{{ route('shopping-cart.checkout') }}" class="checkout-btn">Tiến hành thanh toán <i class="fa-solid fa-arrow-right-long"></i></a>
                </div>
            </div>
            @else
            <div class="col-12">
                <div class="cart-empty text-center p-5 bg-white rounded-3">
                    <i class="fa-light fa-cart-shopping" style="font-size: 5rem; color: #dee2e6;"></i>
                    <h4 class="mt-4">Giỏ hàng của bạn còn trống</h4>
                    <p class="text-muted">Cùng khám phá hàng ngàn sản phẩm tuyệt vời tại Aurora nhé!</p>
                    <a href="{{ route('home') }}" class="tp-btn">Bắt đầu mua sắm</a>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
<!-- cart area end -->
@endsection

@section('scripts')
<script>
    function updateCartQty(itemId, change) {
        let input = document.querySelector('.qty-input[data-item-id="' + itemId + '"]');
        if (!input) return;

        let currentQty = parseInt(input.value);
        let newQty = currentQty + change;
        if (newQty < 1) return;

        fetch('/shopping-cart/update/' + itemId, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                quantity: newQty
            })
        })
        .then(res => res.json().then(data => ({
            ok: res.ok,
            data
        })))
        .then(({
            ok,
            data
        }) => {
            if (ok && data.success) {
                input.value = newQty;
                updateCartSummary();
            } else {
                alert(data.message || 'Có lỗi xảy ra khi cập nhật giỏ hàng!');
            }
        }).catch(error => {
            console.error('Fetch Error:', error);
            alert('Không thể cập nhật giỏ hàng. Vui lòng thử lại.');
        });
    }

    function updateCartSummary() {
        let subtotal = 0;
        let totalItems = 0;

        document.querySelectorAll('.cart-item-card').forEach(itemRow => {
            const unitPrice = parseFloat(itemRow.dataset.unitPrice) || 0;
            const qtyInput = itemRow.querySelector('.qty-input');
            const quantity = parseInt(qtyInput.value) || 0;
            const lineTotal = unitPrice * quantity;

            subtotal += lineTotal;
            totalItems += quantity;

            const lineTotalEl = itemRow.querySelector('.line-total-price');
            if (lineTotalEl) {
                lineTotalEl.textContent = lineTotal.toLocaleString('vi-VN') + '₫';
            }
        });

        const formattedTotal = subtotal.toLocaleString('vi-VN') + '₫';

        document.querySelectorAll('#cart-subtotal, #cart-grand-total').forEach(el => {
            el.textContent = formattedTotal;
        });

        document.querySelectorAll('.cart-item-count').forEach(el => {
            el.textContent = totalItems;
        })

        document.dispatchEvent(new CustomEvent('cart:updated'));

        if (totalItems === 0) {
            const cartRow = document.querySelector('.tp-cart-area .container .row');
            if (cartRow) {
                cartRow.innerHTML = `
                <div class="col-12">
                    <div class="cart-empty text-center p-5 bg-white rounded-3">
                        <i class="fa-light fa-cart-shopping" style="font-size: 5rem; color: #dee2e6;"></i>
                        <h4 class="mt-4">Giỏ hàng của bạn còn trống</h4>
                        <p class="text-muted">Cùng khám phá hàng ngàn sản phẩm tuyệt vời tại Aurora nhé!</p>
                        <a href="{{ route('home') }}" class="tp-btn">Bắt đầu mua sắm</a>
                    </div>
                </div>
                `;
            }
        }
    }

    function changeCartQty(itemId, value) {
        let newQty = parseInt(value);
        if (isNaN(newQty) || newQty < 1) newQty = 1;
        updateCartQty(itemId, newQty - parseInt(document.querySelector('.qty-input[data-item-id="'+itemId+'"]').value));
    }
</script>
@endsection
