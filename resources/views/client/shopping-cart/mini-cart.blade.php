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
            </div>
            <div class="cartmini__widget" id="cartmini-widget-container">
                <div class="cartmini__spinner" style="display:none;text-align:center;padding:24px 0;">
                    <i class="fa fa-spinner fa-spin fa-2x" style="color:#861944;"></i>
                </div>
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
                @endphp
                @if(isset($miniCartItems) && count($miniCartItems))
                    @foreach($miniCartItems as $item)
                        @php
                            $product = $item->product;
                            $variant = $item->productVariant;
                            $unitPrice = $item->price_at_time;
                            $stock = $variant ? $variant->stock : $product->stock ?? 0;
                            $size = $getAttrValue($variant, ['size', 'kích']);
                            $color = $getAttrValue($variant, ['color', 'màu']);
                        @endphp
                        <div class="cartmini__widget-item d-flex align-items-center @if($stock < 1) cartmini-item-out-of-stock @endif" style="gap:1rem;" data-product-id="{{ $item->product_id }}" data-variant-id="{{ $item->product_variant_id }}">
                            @if(!empty($product->slug))
                            <a href="{{ route('client.product.show', ['slug' => $product->slug]) }}" class="d-block border border-translucent rounded-2 cart-item-card__image-wrapper">
                                <img src="{{ $product->image_url ?? asset('assets2/img/product/2/default.png') }}" alt="{{ $product->name }}" class="cart-item-card__image" />
                            </a>
                            @else
                                <span class="d-block border border-translucent rounded-2 cart-item-card__image-wrapper">
                                    <img src="{{ $product->image_url ?? asset('assets2/img/product/2/default.png') }}" alt="{{ $product->name }}" class="cart-item-card__image" />
                                </span>
                            @endif
                            <div class="cart-product-info flex-grow-1" style="min-width:0;">
                                <div class="product-name">
                                    @if(!empty($product->slug))
                                    <a href="{{ route('client.product.show', ['slug' => $product->slug]) }}">{{ $product->name }}</a>
                                    @else
                                        <span>{{ $product->name }}</span>
                                    @endif
                                    @if($stock < 1)
                                        <span class="badge bg-danger ms-2">Hết hàng</span>
                                    @endif
                                </div>
                                <div class="product-meta">
                                    <span class="sku">Mã: {{ $variant->sku ?? $product->sku }}</span>
                                    @if ($color)
                                        <span class="color">Màu: {{ $color }}</span>
                                    @endif
                                    @if ($size)
                                        <span class="size">Size: {{ $size }}</span>
                                    @endif
                                    @if (isset($variant) && isset($variant->attributeValues))
                                        @foreach ($variant->attributeValues as $attrVal)
                                            @php $attrName = strtolower($attrVal->attribute->name ?? ''); @endphp
                                            @if (
                                                !str_contains($attrName, 'size') &&
                                                !str_contains($attrName, 'kích') &&
                                                !str_contains($attrName, 'color') &&
                                                !str_contains($attrName, 'màu'))
                                                <span>{{ $attrVal->attribute->name ?? '' }}: {{ $attrVal->value }}</span>
                                            @endif
                                        @endforeach
                                    @endif
                                    @if($stock < 1)
                                        <span class="text-danger small">Sản phẩm này đã hết hàng</span>
                                    @endif
                                </div>
                                <div class="cartmini__item-qty">
                                    Số lượng: <b>{{ $item->quantity > $stock ? $stock : $item->quantity }}</b>
                                </div>
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
            <div class="cartmini__checkout-btn">
                <a href="{{ route('shopping-cart.index') }}" class="tp-btn tp-btn-border w-100 mb-10">Xem Giỏ Hàng</a>
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
    height: 100vh;
    display: flex;
    flex-direction: column;
}
.cartmini__wrapper {
    display: flex;
    flex-direction: column;
    height: 100%;
}
.cartmini__top-wrapper {
    flex: 1 1 auto;
    min-height: 0;
    display: flex;
    flex-direction: column;
}
.cartmini__widget {
    flex: 1 1 auto;
    min-height: 0;
    overflow-y: auto;
    padding: 0 1.75rem;
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
.cart-item-card__image-wrapper {
    margin-right: 10px;
}
.cart-item-card__image {
    width: 70px;
    height: 70px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #f0f2f5;
}
.product-name {
    font-weight: 700;
    font-size: 1.05rem;
    color: #23272f;
    margin-bottom: 2px;
    line-height: 1.3;
    max-width: 180px;
    white-space: normal;
    overflow: hidden;
}
.product-meta {
    font-size: 0.97rem;
    color: #7b7e85;
    display: flex;
    flex-direction: column;
    gap: 2px;
    align-items: flex-start;
    flex-wrap: nowrap;
}
.cartmini-item-out-of-stock {
    opacity: 0.6;
    background: #f8d7da !important;
}
</style>

<script>
$(document)
    .off('click', '.cartmini__remove-btn')
    .on('click', '.cartmini__remove-btn', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var itemId = $btn.data('id');
        if (!itemId) return;
        if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?')) {
            var $spinner = $('.cartmini__spinner');
            $spinner.show();
            $.ajax({
                url: '/shopping-cart/remove/' + itemId,
                method: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    $spinner.hide();
                    if (res.success) {
                        if (window.toastr) {
                            toastr.success(res.message || 'Đã xóa sản phẩm khỏi giỏ hàng!');
                        } else {
                            alert(res.message || 'Đã xóa sản phẩm khỏi giỏ hàng!');
                        }
                        // Phát sự kiện cart:item-removed cho product-detail (dùng ID từ response backend)
                        var event = new CustomEvent('cart:item-removed', { detail: { product_id: res.product_id, variant_id: res.product_variant_id } });
                        document.dispatchEvent(event);
                        if (window.shoppingCart) {
                            window.shoppingCart.updateCartCount();
                            window.shoppingCart.updateMiniCart();
                        } else {
                            location.reload();
                        }
                    } else {
                        if (res.message && res.message.includes('Không tìm thấy sản phẩm')) {
                            if (window.shoppingCart) {
                                window.shoppingCart.updateCartCount();
                                window.shoppingCart.updateMiniCart();
                            } else {
                                location.reload();
                            }
                        } else {
                            alert(res.message || 'Có lỗi xảy ra!');
                        }
                    }
                },
                error: function(xhr) {
                    $spinner.hide();
                    if(xhr.status === 401) {
                        alert('Bạn cần đăng nhập để thao tác!');
                    } else {
                        alert('Không thể xóa sản phẩm. Vui lòng thử lại!');
                    }
                }
            });
        }
    });
</script>