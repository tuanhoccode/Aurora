@extends('client.layouts.default')

@section('title', 'Chi tiết đơn hàng')

@section('content')
<style>
    /* CSS style */
    .tp-order-detail-area {
        background-color: #f7f8fa;
        padding-top: 3rem;
        padding-bottom: 6rem;
    }

    .order-detail-header {
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.07);
        border: 1px solid #e9ecef;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    /* ... (tiếp tục các style khác) ... */
</style>

<!-- breadcrumb area start -->
<section class="breadcrumb__area">
    <div class="container">
        <div class="row">
            <div class="col-xxl-12">
                <div class="breadcrumb__content p-relative z-index-1">
                    <h3 class="breadcrumb__title">Chi tiết đơn hàng #{{ $order->id }}</h3>
                    <div class="breadcrumb__list">
                        <span><a href="{{ route('home') }}">Trang chủ</a></span>
                        <span><a href="{{ route('orders.index') }}">Đơn hàng</a></span>
                        <span>Chi tiết</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- breadcrumb area end -->

<!-- order detail area start -->
<section class="tp-order-detail-area">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="order-detail-header">
                    <div class="order-detail-header__status bg-{{ $order->currentStatus->status->name === 'Đã hoàn thành' ? 'success' : ($order->currentStatus->status->name === 'Đang giao hàng' ? 'warning' : 'primary') }}">
                        <i class="fas fa-circle-check"></i>
                        {{ $order->currentStatus->status->name }}
                    </div>
                    <div class="order-detail-header__info">
                        <div class="order-detail-header__info-item">
                            <i class="fas fa-clock"></i>
                            <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="order-detail-header__info-item">
                            <i class="fas fa-truck"></i>
                            <span>{{ $order->payment_method }}</span>
                        </div>
                        <div class="order-detail-header__info-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>{{ $order->shipping_address }}</span>
                        </div>
                        <div class="order-detail-header__info-item">
                            <i class="fas fa-user"></i>
                            <span>{{ $order->user->name }}</span>
                        </div>
                        <div class="order-detail-header__info-item">
                            <i class="fas fa-phone"></i>
                            <span>{{ $order->user->phone }}</span>
                        </div>
                        <div class="order-detail-header__info-item">
                            <i class="fas fa-envelope"></i>
                            <span>{{ $order->user->email }}</span>
                        </div>
                        <div class="order-detail-header__info-item">
                            <i class="fas fa-sticky-note"></i>
                            <span>{{ $order->note ?? 'Không có ghi chú' }}</span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Phần thông tin người mua và thanh toán -->
                    <div class="col-lg-4">
                        <div class="order-info-box">
                            <h4 class="order-info-box__title">Thông tin người mua</h4>
                            <div class="order-info-box__content">
                                <p><strong>Tên người mua:</strong> {{ $order->user->name }}</p>
                                <p><strong>Email:</strong> {{ $order->user->email }}</p>
                                <p><strong>Số điện thoại:</strong> {{ $order->user->phone }}</p>
                            </div>
                        </div>

                        <div class="order-info-box">
                            <h4 class="order-info-box__title">Thông tin giao hàng</h4>
                            <div class="order-info-box__content">
                                <p><strong>Địa chỉ:</strong> {{ $order->shipping_address }}</p>
                                <p><strong>Phương thức thanh toán:</strong> {{ $order->payment_method }}</p>
                                <p><strong>Ghi chú:</strong> {{ $order->note ?? 'Không có ghi chú' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Phần danh sách sản phẩm -->
                    <div class="col-lg-8">
                        <div class="order-items-grid">
                            @foreach($order->items as $item)
                            <div class="order-item-card">
                                <img src="{{ $item->product->image_url ?? asset('assets2/img/product/2/default.png') }}" 
                                     alt="{{ $item->product->name }}" 
                                     class="order-item-card__image">

                                <div class="order-item-card__info">
                                    <h4 class="name" title="{{ $item->product->name }}">{{ $item->product->name }}</h4>
                                    <div class="meta-attributes">
                                        <div class="meta" title="{{ $item->product->sku }}"><strong>Mã:</strong> {{ $item->product->sku }}</div>
                                        @if ($item->productVariant)
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
                                                $size = $getAttrValue($item->productVariant, ['size', 'kích']);
                                                $color = $getAttrValue($item->productVariant, ['color', 'màu']);
                                                
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

                                <div class="order-item-card__quantity">
                                    <div class="cart-qty">
                                        <span class="qty-input">{{ $item->quantity }}</span>
                                    </div>
                                </div>

                                <div class="order-item-card__price">
                                    {{ number_format($item->price_at_time * $item->quantity, 0, ',', '.') }}đ
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Phần tóm tắt đơn hàng -->
                    <div class="col-lg-4">
                        <div class="order-summary-box">
                            <h4 class="order-summary-title">Tóm tắt đơn hàng</h4>
                            <div class="order-summary__item">
                                <span>Sản phẩm</span>
                                <span>{{ $order->items->sum('quantity') }} sản phẩm</span>
                            </div>
                            <div class="order-summary__item">
                                <span>Tạm tính</span>
                                <span>{{ number_format($order->items->sum(function($item) {
                                    return $item->price_at_time * $item->quantity;
                                }), 0, ',', '.') }}đ</span>
                            </div>
                            <div class="order-summary__item">
                                <span>Phí vận chuyển</span>
                                <span class="text-success">Miễn phí</span>
                            </div>
                            <div class="order-summary__total">
                                <span>Tổng cộng</span>
                                <span>{{ number_format($order->total_amount, 0, ',', '.') }}đ</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- order detail area end -->
@endsection