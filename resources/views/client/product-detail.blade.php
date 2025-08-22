@extends('client.layouts.default')
@section('title', 'Chi tiết sản phẩm')
@section('content')
@php
    $hasVariants = isset($product->variants) && count($product->variants) > 0;
@endphp
<style>
    .color-circle {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: inline-block;
        border: 1px solid #ccc;
        box-shadow: 0 0 2px rgba(0, 0, 0, 0.1);
        transition: all 0.2s ease;
    }

    .tp-color-variation-btn.active .color-circle {
        border: 3px solid #000;
        box-shadow: 0 0 6px rgba(0, 0, 0, 0.4);
    }

    /* Cho nút điều hướng màu đen */
    .tp-product-related-slider .swiper-button-next,
    .tp-product-related-slider .swiper-button-prev {
        color: #222;
        background: #fff;
        border-radius: 50%;
        width: 48px;
        height: 48px;
        font-size: 0;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.13);
        border: 2px solid #e0e0e0;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s, color 0.2s, box-shadow 0.2s, transform 0.18s;
        z-index: 10;
    }

    .tp-product-related-slider .swiper-button-next:after,
    .tp-product-related-slider .swiper-button-prev:after {
        display: none;
    }

    .tp-product-related-slider .swiper-button-next svg,
    .tp-product-related-slider .swiper-button-prev svg {
        width: 28px;
        height: 28px;
        display: block;
        fill: #222;
        transition: fill 0.2s;
    }

    .tp-product-related-slider .swiper-button-next:hover,
    .tp-product-related-slider .swiper-button-prev:hover {
        background: #222;
        color: #fff;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.18);
        transform: scale(1.08);
    }

    .tp-product-related-slider .swiper-button-next:hover svg,
    .tp-product-related-slider .swiper-button-prev:hover svg {
        fill: #fff;
    }

    .tp-product-related-slider .swiper-button-prev {
        left: 0;
    }

    .tp-product-related-slider .swiper-button-next {
        right: 0;
    }

    @media (max-width: 768px) {

        .tp-product-related-slider .swiper-button-prev,
        .tp-product-related-slider .swiper-button-next {
            display: none;
        }
    }

    /* --- Related Products Modern Card --- */
    .related-product-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.07);
        border: 1px solid #f0f0f0;
        padding: 22px 18px 18px 18px;
        transition: box-shadow 0.25s, transform 0.2s;
        text-align: center;
        position: relative;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
    }

    .related-product-card:hover {
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.13);
        transform: translateY(-4px) scale(1.03);
    }

    .related-product-thumb {
        width: 100%;
        height: 170px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 18px;
        overflow: hidden;
    }

    .related-product-thumb img {
        max-height: 150px;
        max-width: 100%;
        object-fit: contain;
        border-radius: 10px;
        transition: transform 0.25s;
    }

    .related-product-card:hover .related-product-thumb img {
        transform: scale(1.08);
    }

    .related-product-title {
        font-size: 17px;
        font-weight: 600;
        margin-bottom: 6px;
        color: #222;
        min-height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .related-product-cats {
        font-size: 13px;
        color: #888;
        margin-bottom: 8px;
    }

    .related-product-price {
        font-size: 18px;
        font-weight: 700;
        color: #e53935;
        margin-bottom: 14px;
    }

    .related-product-btn {
        display: inline-block;
        background: var(--tp-theme-primary, #2d8cf0);
        color: #fff;
        border: none;
        border-radius: 6px;
        padding: 7px 18px;
        font-size: 15px;
        font-weight: 500;
        margin-top: auto;
        transition: background 0.2s;
        text-decoration: none;
    }

    .related-product-btn:hover {
        background: #1a6dc2;
        color: #fff;
    }

    @media (max-width: 767px) {
        .related-product-thumb {
            height: 120px;
        }

        .related-product-title {
            font-size: 15px;
        }

        .related-product-card {
            padding: 14px 6px 12px 6px;
        }
    }

    .tp-section-title-wrapper-6 {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 38px;
    }

    .tp-section-title-pre-6 {
        font-size: 17px;
        color: #4a90e2;
        font-weight: 500;
        text-transform: capitalize;
        letter-spacing: 1px;
        margin-bottom: 7px;
        background: none;
        font-family: 'Segoe UI', Arial, sans-serif;
    }

    .tp-section-title-6 {
        font-size: 2.4rem;
        font-weight: 700;
        color: #23272f;
        letter-spacing: 0.5px;
        margin: 0;
        line-height: 1.18;
        position: relative;
        display: inline-block;
        font-family: 'Segoe UI', Arial, sans-serif;
    }

    .tp-section-title-6::after {
        content: '';
        display: block;
        width: 60px;
        height: 4px;
        margin: 10px auto 0 auto;
        border-radius: 2px;
        background: #4a90e2;
        opacity: 0.18;
    }

    .tp-cart-minus.disabled,
    .tp-cart-plus.disabled {
        pointer-events: none;
        opacity: 0.5;
        background: #eee !important;
        color: #aaa !important;
        cursor: not-allowed !important;
    }

    #subImageGallery {
        max-height: 500px;
        overflow-y: auto;
        padding-right: 8px;
    }

    #subImageGallery img {
        width: 100%;
        height: 100px;
        object-fit: cover;
        cursor: pointer;
        border-radius: 6px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    #subImageGallery img:hover {
        transform: scale(1.05);
        box-shadow: 0 0 8px rgba(0, 0, 0, 0.2);
    }

    .product-image-wrapper {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .main-product-image-container {
        max-width: 500px;
    }

    @media (max-width: 768px) {
        .product-image-wrapper {
            flex-direction: column;
            align-items: stretch;
        }
    }

    .main-product-image-container {
        width: 100%;
        max-width: 500px;
        height: 500px;
        /* 👈 cố định chiều cao */
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .main-product-image-container img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        /* 👈 hoặc 'cover' nếu muốn ảnh đầy khung */
    }

    #subImageGallery img {
        cursor: pointer;
        border-radius: 6px;
        transition: border 0.3s;
    }

    #subImageGallery img:hover {
        border: 2px solid #007bff;
    }

    .tp-color-variation-btn {
        width: 45px !important;
        height: 45px !important;
        border-radius: 50% !important;
        padding: 0 !important;
        border: 2px solid #ddd !important;
        /* Viền nhạt mặc định */
        overflow: hidden !important;
        background-color: transparent !important;
        transition: border-color 0.2s ease, box-shadow 0.2s ease !important;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        margin: 5px;
    }

    .tp-color-variation-btn img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        border-radius: 50% !important;
    }

    /* Bỏ hover nếu không cần hiệu ứng */
    .tp-color-variation-btn:hover {
        /* Nếu vẫn muốn viền sáng hơn khi hover, giữ lại dòng dưới */
        border-color: color-mix(in srgb, var(--active-border-color) 30%, #ffffff) !important;
    }

    /* Khi được chọn (active) thì viền đậm */
    .tp-color-variation-btn.active {
        border-color: var(--active-border-color) !important;
        box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.1) !important;
    }
    /* Css ảnh bình luận */
    .review-image {
        width: 100px;
        height: 100px;
        object-fit: cover; /* cắt ảnh để vừa khung mà không méo */
        border-radius: 6px;
        border: 1px solid #ddd;
        transition: transform 0.2s ease-in-out;
    }
    .review-image:hover {
        transform: scale(1.05); /* phóng nhẹ khi hover */
    }
    .review-image-link {
        display: inline-block;
    }
</style>
<main>
    <!-- breadcrumb area start -->
    <section class="breadcrumb__area breadcrumb__style-2 include-bg pt-50 pb-20">
        <div class="container">
            <div class="row">
                <div class="col-xxl-12">
                    <div class="breadcrumb__content p-relative z-index-1">
                        <div class="breadcrumb__list has-icon">
                            <span class="breadcrumb-icon">
                                <svg width="17" height="17" viewBox="0 0 17 17" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M1.42393 16H15.5759C15.6884 16 15.7962 15.9584 15.8758 15.8844C15.9553 15.8104 16 15.71 16 15.6054V6.29143C16 6.22989 15.9846 6.1692 15.9549 6.11422C15.9252 6.05923 15.8821 6.01147 15.829 5.97475L8.75305 1.07803C8.67992 1.02736 8.59118 1 8.5 1C8.40882 1 8.32008 1.02736 8.24695 1.07803L1.17098 5.97587C1.11791 6.01259 1.0748 6.06035 1.04511 6.11534C1.01543 6.17033 0.999976 6.23101 1 6.29255V15.6063C1.00027 15.7108 1.04504 15.8109 1.12451 15.8847C1.20398 15.9585 1.31165 16 1.42393 16ZM10.1464 15.2107H6.85241V10.6202H10.1464V15.2107ZM1.84866 6.48977L8.4999 1.88561L15.1517 6.48977V15.2107H10.9946V10.2256C10.9946 10.1209 10.95 10.0206 10.8704 9.94654C10.7909 9.87254 10.683 9.83096 10.5705 9.83096H6.42848C6.316 9.83096 6.20812 9.87254 6.12858 9.94654C6.04904 10.0206 6.00435 10.1209 6.00435 10.2256V15.2107H1.84806L1.84866 6.48977Z"
                                        fill="#55585B" stroke="#55585B" stroke-width="0.5" />
                                </svg>
                            </span>
                            <span><a href="{{ url('/') }}">Trang chủ</a></span>
                            @foreach ($product->categories as $category)
                                @if ($category->slug)
                                    <span><a
                                            href="{{ route('client.category.show', ['slug' => $category->slug]) }}">{{ $category->name }}</a></span>
                                @else
                                    <span><a href="#">{{ $category->name }}</a></span>
                                @endif
                            @endforeach
                            <span>{{ $product->name }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- breadcrumb area end -->

    <!-- product details area start -->
    <section class="tp-product-details-area">
        <div class="tp-product-details-top pb-115">
            <div class="container">
                <div class="row">
                    <div class="col-xl-7 col-lg-6">
                        <div class="tp-product-details-thumb-wrapper tp-tab d-sm-flex">

                            <div class="row">
                                <!-- Ảnh con bên trái -->
                                <div class="col-2 d-flex flex-column gap-2" id="subImageGallery">
                                    <!-- Ảnh phụ sẽ được render bằng JavaScript -->
                                </div>

                                <!-- Ảnh chính bên phải -->
                                <div class="col-10 d-flex justify-content-center">
                                    <div class="card shadow-sm border-0" style="max-width: 500px;">
                                        <div class="position-relative overflow-hidden rounded-4"
                                            style="width: 100%; height: 500px; display: flex; align-items: center; justify-content: center; background-color: #f9f9f9;">
                                            <img id="mainProductImage"
                                                src="{{ asset('storage/' . $product->thumbnail) }}"
                                                alt="{{ $product->name }}" class="img-fluid"
                                                style="width: 100%; height: 100%; object-fit: contain; transition: transform 0.3s ease;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- col end -->
                    <div class="col-xl-5 col-lg-6">
                        <div class="tp-product-details-wrapper">
                            <div class="tp-product-details-category">
                            </div>
                            <h3 class="tp-product-details-title">{{ $product->name }}</h3>

                            <!-- inventory details -->
                            <div class="tp-product-details-inventory d-flex align-items-center mb-10">
                                <div class="tp-product-details-stock mb-10">
                                    <span id="product-stock">
                                        @if ($hasVariants)
                                            Vui lòng phân loại hàng!
                                        @else
                                            @if ($product->stock > 0)
                                                Trong kho: {{ $product->stock }}
                                            @else
                                                Hết hàng
                                            @endif
                                        @endif
                                    </span>
                                </div>
                                <div class="tp-product-details-rating-wrapper d-flex align-items-center mb-10">
                                    <div class="tp-product-details-rating">
                                        @if ($reviewCount > 0)
                                            <div class="product-rating">
                                                {{-- Hiển thị sao --}}
                                                @for ($i = 1; $i <= 5; $i++)
                                                    @if ($i <= round($averageRating))
                                                        <i class="fas fa-star text-warning"></i>
                                                    @else
                                                        <i class="far fa-star text-muted"></i>
                                                    @endif
                                                @endfor
                                                <span>({{ $reviewCount }} đánh giá)</span>
                                            </div>
                                        @else
                                            <span>Chưa có đánh giá</span>
                                        @endif
                                    </div>
                                    <div class="tp-product-details-reviews">
                                        @php
                                            function formatViews($number)
                                            {
                                                if ($number >= 1_000_000_000_000) {
                                                    return number_format($number / 1_000_000_000_000, 1) . 'T';
                                                } elseif ($number >= 1_000_000_000) {
                                                    return number_format($number / 1_000_000_000, 1) . 'B';
                                                } elseif ($number >= 1_000_000) {
                                                    return number_format($number / 1_000_000, 1) . 'M';
                                                } elseif ($number >= 1_000) {
                                                    return number_format($number / 1_000, 1) . 'K';
                                                }

                                                return number_format($number);
                                            }
                                        @endphp

                                        <span>(Lượt xem: {{ formatViews($product->views) }})</span>
                                    </div>
                                </div>
                            </div>
                            <p>{!! $product->short_description !!}</p>
                            <!-- price -->
                            {{-- Hiển thị giá --}}
                            <div class="tp-product-details-price-wrapper mb-20">
                                @php
                                    if ($product->variants->count()) {
                                        $unit = $product->variants->min(function ($variant) {
                                            return $variant->sale_price ?? $variant->price ?? PHP_INT_MAX;
                                        });

                                        $original = $product->variants->min(function ($variant) {
                                            return $variant->price ?? PHP_INT_MAX;
                                        });

                                        if ($unit === PHP_INT_MAX) {
                                            $unit = 0;
                                        }

                                        if ($original === PHP_INT_MAX) {
                                            $original = null;
                                        }
                                    } else {
                                        $unit = $product->sale_price ?? $product->price ?? 0;
                                        $original = $product->sale_price ? $product->price : null;
                                    }
                                @endphp
                                <div class="tp-product-details-price-wrapper mb-20">
                                    <span class="tp-product-details-price new-price" id="product-price">
                                        {{ number_format($unit, 0, ',', '.') }}₫
                                    </span>
                                    @if ($product->sale_price)
                                        <span class="tp-product-details-price old-price" id="product-old-price">
                                            {{ number_format($product->price, 0, ',', '.') }}₫
                                        </span>
                                    @else
                                        <span class="tp-product-details-price old-price" id="product-old-price"
                                            style="display: none;"></span>
                                    @endif

                                    <span id="unit-price" data-unit="{{ $unit }}" style="display: none;"></span>
                                </div>
                                <!-- @if ($product->variants->isEmpty())
                                    <div class="mt-3">
                                        <strong>Chất liệu:</strong> {{ $product->material }}
                                    </div>
                                @endif

                                @if ($product->variants->isNotEmpty())
                                    <div id="materialSection" class="mt-3 d-none">
                                        <strong>Chất liệu:</strong>
                                        <span id="materialText"></span>
                                    </div>
                                @endif -->
                                {{-- Tổng tiền --}}
                                <div class="mt-2">
                                    <strong>Tổng tiền: </strong>
                                    <span id="total-price">{{ number_format($unit, 0, ',', '.') }}₫</span>
                                </div>


                                {{-- Ẩn giá đơn vị để JS dùng tính toán --}}
                                <span id="unit-price" style="display:none;">{{ $unit }}</span>
                                <!-- variations -->

                                @if ($hasVariants)
                                    @php
                                        // Tất cả mã màu định nghĩa
                                        $allColors = [
                                            'DO' => ['name' => 'Đỏ'],
                                            'VANG' => ['name' => 'Vàng'],
                                            'DEN' => ['name' => 'Đen'],
                                            'TRANG' => ['name' => 'Trắng'],
                                            'XAM' => ['name' => 'Xám'],
                                            'XA' => ['name' => 'Xanh'],
                                            'NA' => ['name' => 'Nâu'],
                                            'BE' => ['name' => 'Be'],
                                            'XN' => ['name' => 'Xanh Ngọc'],
                                        ];

                                        // Lấy danh sách mã màu có thật trong sản phẩm
                                        $productColors = collect($product->variants)
                                            ->map(function ($variant) {
                                                $parts = explode('-', $variant->sku);
                                                return strtoupper(end($parts)); // mã màu
                                            })
                                            ->unique();

                                        // Lấy danh sách size có thật
                                        $productSizes = collect($product->variants)
                                            ->map(function ($variant) {
                                                $parts = explode('-', $variant->sku);
                                                return isset($parts[1]) ? strtoupper($parts[1]) : null;
                                            })
                                            ->filter()
                                            ->unique();

                                        // Danh sách ảnh đại diện cho từng màu
                                        $colorImages = [];
                                        foreach ($product->variants as $variant) {
                                            $parts = explode('-', $variant->sku);
                                            $colorCode = strtoupper(end($parts));
                                            if (!isset($colorImages[$colorCode]) && $variant->img) {
                                                $colorImages[$colorCode] = $variant->img;
                                            }
                                        }

                                        $allSizes = ['S', 'M', 'L', 'XL', 'XXL'];
                                    @endphp

                                    <!-- Màu -->
                                    <div class="tp-product-details-variation-item">
                                        <h4 class="tp-product-details-variation-title">
                                            Màu : <span id="selected-color-name" style="font-weight: normal;"></span>
                                        </h4>
                                        <div class="tp-product-details-variation-list d-flex gap-2 flex-wrap">
                                            @foreach ($allColors as $code => $color)
                                                @php
                                                    $isAvailable = $productColors->contains($code);
                                                    $imgUrl = $colorImages[$code] ?? null;
                                                @endphp

                                                @if ($isAvailable && $imgUrl)
                                                    <button type="button"
                                                        class="tp-color-variation-btn {{ $loop->first ? 'active' : '' }}"
                                                        data-color="{{ $code }}"
                                                        style="--active-border-color: {{ $color['hex'] ?? '#000' }};"
                                                        title="{{ $color['name'] }}">
                                                        <img src="{{ asset('storage/' . $imgUrl) }}" alt="{{ $color['name'] }}">
                                                    </button>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Kích cỡ -->
                                    <div class="tp-product-details-variation-item">
                                        <h4 class="tp-product-details-variation-title">Kích cỡ :</h4>
                                        <div class="tp-product-details-variation-list">
                                            @foreach ($productSizes as $size)
                                                <button type="button" class="tp-size-variation-btn" data-size="{{ $size }}">
                                                    <span>{{ $size }}</span>
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- Số lượng + Thêm giỏ hàng --}}
                                <div class="tp-product-details-action-wrapper">
                                    <h3 class="tp-product-details-action-title">Số lượng</h3>
                                    <div class="tp-product-details-action-item-wrapper d-flex align-items-center">
                                        <div class="tp-product-details-quantity">
                                            <div class="tp-product-quantity mb-15 mr-15">
                                                <span id="detail-cart-minus" class="tp-cart-minus">–</span>
                                                <input id="quantity" name="quantity" class="tp-cart-input" type="text"
                                                    value="1" @if(!$hasVariants && isset($product->stock) && $product->stock < 1) disabled @endif>
                                                <span id="detail-cart-plus" class="tp-cart-plus">+</span>
                                            </div>
                                        </div>
                                        <div class="tp-product-details-add-to-cart mb-15 w-100">
                                            <form id="detail-add-to-cart-form" class="add-to-cart-form"
                                                action="{{ route('client.shopping-cart.add') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                <input type="hidden" name="product_variant_id" id="product_variant_id"
                                                    value="">
                                                <input type="hidden" name="variant_sku" id="variant_sku" value="">
                                                <input type="hidden" name="price" id="variant_price"
                                                    value="{{ $product->sale_price ?? $product->price }}">
                                                <button type="submit" class="tp-product-details-add-to-cart-btn w-100"
                                                    id="add-to-cart-btn" @if(!$hasVariants && isset($product->stock) && $product->stock < 1) disabled @endif>
                                                    Thêm vào giỏ hàng
                                                </button>
                                                @if(!$hasVariants && isset($product->stock) && $product->stock < 1)
                                                    <div class="text-danger mt-2">Sản phẩm đã hết hàng, không thể thêm vào
                                                        giỏ hàng.</div>
                                                @endif
                                            </form>
                                        </div>
                                    </div>
                                    <form method="POST" action="{{ route('checkout') }}">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        {{-- <button type="submit" class="tp-product-details-buy-now-btn w-100">Mua
                                            ngay</button> --}}
                                    </form>
                                </div>
                                <div class="tp-product-details-action-sm">

                                    <button type="button" id="add-to-wishlist-btn"
                                        class="tp-product-details-action-sm-btn btn-add-wishlist"
                                        data-product-id="{{ $product->id }}">
                                        <svg width="17" height="16" viewBox="0 0 17 16" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M2.33541 7.54172C3.36263 10.6766 7.42094 13.2113 8.49945 13.8387C9.58162 13.2048 13.6692 10.6421 14.6635 7.5446C15.3163 5.54239 14.7104 3.00621 12.3028 2.24514C11.1364 1.8779 9.77578 2.1014 8.83648 2.81432C8.64012 2.96237 8.36757 2.96524 8.16974 2.81863C7.17476 2.08487 5.87499 1.86999 4.69024 2.24514C2.28632 3.00549 1.68259 5.54167 2.33541 7.54172ZM8.50115 15C8.4103 15 8.32018 14.9784 8.23812 14.9346C8.00879 14.8117 2.60674 11.891 1.29011 7.87081C1.28938 7.87081 1.28938 7.8701 1.28938 7.8701C0.462913 5.33895 1.38316 2.15812 4.35418 1.21882C5.7492 0.776121 7.26952 0.97088 8.49895 1.73195C9.69029 0.993159 11.2729 0.789057 12.6401 1.21882C15.614 2.15956 16.5372 5.33966 15.7115 7.8701C14.4373 11.8443 8.99571 14.8088 8.76492 14.9332C8.68286 14.9777 8.592 15 8.50115 15Z"
                                                fill="currentColor" />
                                            <path
                                                d="M8.49945 13.8387L8.42402 13.9683L8.49971 14.0124L8.57526 13.9681L8.49945 13.8387ZM14.6635 7.5446L14.5209 7.4981L14.5207 7.49875L14.6635 7.5446ZM12.3028 2.24514L12.348 2.10211L12.3478 2.10206L12.3028 2.24514ZM8.83648 2.81432L8.92678 2.93409L8.92717 2.9338L8.83648 2.81432ZM8.16974 2.81863L8.25906 2.69812L8.25877 2.69791L8.16974 2.81863ZM4.69024 2.24514L4.73548 2.38815L4.73552 2.38814L4.69024 2.24514ZM8.23812 14.9346L8.16727 15.0668L8.16744 15.0669L8.23812 14.9346ZM1.29011 7.87081L1.43266 7.82413L1.39882 7.72081H1.29011V7.87081ZM1.28938 7.8701L1.43938 7.87009L1.43938 7.84623L1.43197 7.82354L1.28938 7.8701ZM4.35418 1.21882L4.3994 1.36184L4.39955 1.36179L4.35418 1.21882ZM8.49895 1.73195L8.42 1.85949L8.49902 1.90841L8.57801 1.85943L8.49895 1.73195ZM12.6401 1.21882L12.6853 1.0758L12.685 1.07572L12.6401 1.21882ZM15.7115 7.8701L15.5689 7.82356L15.5686 7.8243L15.7115 7.8701ZM8.76492 14.9332L8.69378 14.8011L8.69334 14.8013L8.76492 14.9332ZM2.19287 7.58843C2.71935 9.19514 4.01596 10.6345 5.30013 11.744C6.58766 12.8564 7.88057 13.6522 8.42402 13.9683L8.57487 13.709C8.03982 13.3978 6.76432 12.6125 5.49626 11.517C4.22484 10.4185 2.97868 9.02313 2.47795 7.49501L2.19287 7.58843ZM8.57526 13.9681C9.12037 13.6488 10.4214 12.8444 11.7125 11.729C12.9999 10.6167 14.2963 9.17932 14.8063 7.59044L14.5207 7.49875C14.0364 9.00733 12.7919 10.4 11.5164 11.502C10.2446 12.6008 8.9607 13.3947 8.42364 13.7093L8.57526 13.9681ZM14.8061 7.59109C15.1419 6.5613 15.1554 5.39131 14.7711 4.37633C14.3853 3.35729 13.5989 2.49754 12.348 2.10211L12.2576 2.38816C13.4143 2.75381 14.1347 3.54267 14.4905 4.48255C14.8479 5.42648 14.8379 6.52568 14.5209 7.4981L14.8061 7.59109ZM12.3478 2.10206C11.137 1.72085 9.72549 1.95125 8.7458 2.69484L8.92717 2.9338C9.82606 2.25155 11.1357 2.03494 12.2577 2.38821L12.3478 2.10206ZM8.74618 2.69455C8.60221 2.8031 8.40275 2.80462 8.25906 2.69812L8.08043 2.93915C8.33238 3.12587 8.67804 3.12163 8.92678 2.93409L8.74618 2.69455ZM8.25877 2.69791C7.225 1.93554 5.87527 1.71256 4.64496 2.10213L4.73552 2.38814C5.87471 2.02742 7.12452 2.2342 8.08071 2.93936L8.25877 2.69791ZM4.64501 2.10212C3.39586 2.49722 2.61099 3.35688 2.22622 4.37554C1.84299 5.39014 1.85704 6.55957 2.19281 7.58826L2.478 7.49518C2.16095 6.52382 2.15046 5.42513 2.50687 4.48154C2.86175 3.542 3.58071 2.7534 4.73548 2.38815L4.64501 2.10212ZM8.50115 14.85C8.43415 14.85 8.36841 14.8341 8.3088 14.8023L8.16744 15.0669C8.27195 15.1227 8.38645 15.15 8.50115 15.15V14.85ZM8.30897 14.8024C8.19831 14.7431 6.7996 13.9873 5.26616 12.7476C3.72872 11.5046 2.07716 9.79208 1.43266 7.82413L1.14756 7.9175C1.81968 9.96978 3.52747 11.7277 5.07755 12.9809C6.63162 14.2373 8.0486 15.0032 8.16727 15.0668L8.30897 14.8024ZM1.29011 7.72081C1.31557 7.72081 1.34468 7.72745 1.37175 7.74514C1.39802 7.76231 1.41394 7.78437 1.42309 7.8023C1.43191 7.81958 1.43557 7.8351 1.43727 7.84507C1.43817 7.8504 1.43869 7.85518 1.43898 7.85922C1.43913 7.86127 1.43923 7.8632 1.43929 7.865C1.43932 7.86591 1.43934 7.86678 1.43936 7.86763C1.43936 7.86805 1.43937 7.86847 1.43937 7.86888C1.43937 7.86909 1.43937 7.86929 1.43938 7.86949C1.43938 7.86959 1.43938 7.86969 1.43938 7.86979C1.43938 7.86984 1.43938 7.86992 1.43938 7.86994C1.43938 7.87002 1.43938 7.87009 1.28938 7.8701C1.13938 7.8701 1.13938 7.87017 1.13938 7.87025C1.13938 7.87027 1.13938 7.87035 1.13938 7.8704C1.13938 7.8705 1.13938 7.8706 1.13938 7.8707C1.13938 7.8709 1.13938 7.87111 1.13938 7.87131C1.13939 7.87173 1.13939 7.87214 1.1394 7.87257C1.13941 7.87342 1.13943 7.8743 1.13946 7.8752C1.13953 7.87701 1.13962 7.87896 1.13978 7.88103C1.14007 7.88512 1.14059 7.88995 1.14151 7.89535C1.14323 7.90545 1.14694 7.92115 1.15585 7.93861C1.16508 7.95672 1.18114 7.97896 1.20762 7.99626C1.2349 8.01409 1.26428 8.02081 1.29011 8.02081V7.72081ZM1.43197 7.82354C0.623164 5.34647 1.53102 2.26869 4.3994 1.36184L4.30896 1.0758C1.23531 2.04755 0.302663 5.33142 1.14679 7.91665L1.43197 7.82354ZM4.39955 1.36179C5.7527 0.932384 7.22762 1.12136 8.42 1.85949L8.57791 1.60441C7.31141 0.820401 5.74571 0.619858 4.30881 1.07585L4.39955 1.36179ZM8.57801 1.85943C9.73213 1.14371 11.2694 0.945205 12.5951 1.36192L12.685 1.07572C11.2763 0.632908 9.64845 0.842602 8.4199 1.60447L8.57801 1.85943ZM12.5948 1.36184C15.4664 2.27018 16.3769 5.34745 15.5689 7.82356L15.8541 7.91663C16.6975 5.33188 15.7617 2.04893 12.6853 1.07581L12.5948 1.36184ZM15.5686 7.8243C14.9453 9.76841 13.2952 11.4801 11.7526 12.7288C10.2142 13.974 8.80513 14.7411 8.69378 14.8011L8.83606 15.0652C8.9555 15.0009 10.3826 14.2236 11.9413 12.9619C13.4957 11.7037 15.2034 9.94602 15.8543 7.91589L15.5686 7.8243ZM8.69334 14.8013C8.6337 14.8337 8.56752 14.85 8.50115 14.85V15.15C8.61648 15.15 8.73201 15.1217 8.83649 15.065L8.69334 14.8013Z"
                                                fill="currentColor" />
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M12.8384 6.93209C12.5548 6.93209 12.3145 6.71865 12.2911 6.43693C12.2427 5.84618 11.8397 5.34743 11.266 5.1656C10.9766 5.07361 10.8184 4.76962 10.9114 4.48718C11.0059 4.20402 11.3129 4.05023 11.6031 4.13934C12.6017 4.45628 13.3014 5.32371 13.3872 6.34925C13.4113 6.64606 13.1864 6.90622 12.8838 6.92993C12.8684 6.93137 12.8538 6.93209 12.8384 6.93209Z"
                                                fill="currentColor" />
                                            <path
                                                d="M12.8384 6.93209C12.5548 6.93209 12.3145 6.71865 12.2911 6.43693C12.2427 5.84618 11.8397 5.34743 11.266 5.1656C10.9766 5.07361 10.8184 4.76962 10.9114 4.48718C11.0059 4.20402 11.3129 4.05023 11.6031 4.13934C12.6017 4.45628 13.3014 5.32371 13.3872 6.34925C13.4113 6.64606 13.1864 6.90622 12.8838 6.92993C12.8684 6.93137 12.8538 6.93209 12.8384 6.93209"
                                                stroke="currentColor" stroke-width="0.3" />
                                        </svg>
                                        Thêm vào danh sách yêu thích
                                    </button>
                                </div>
                                <div class="tp-product-details-query">
                                    <div class="tp-product-details-query-item d-flex align-items-center">
                                        <span>Mã sản phẩm: </span>
                                        <p id="sku-display">
                                            {{ $product->type === 'variant' ? optional($product->variants->first())->sku : $product->sku ?? 'Đang cập nhật' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="tp-product-details-query-item d-flex align-items-center">
                                    <span>Category: </span>
                                    <p>
                                        {{ $product->categories->first()->name ?? 'Đang cập nhật' }}
                                    </p>
                                </div>
                            </div>
                            <!-- <div class="tp-product-details-social">
                                <span>Share: </span>
                                <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                                <a href="#"><i class="fa-brands fa-twitter"></i></a>
                                <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                                <a href="#"><i class="fa-brands fa-vimeo-v"></i></a>
                            </div> -->
                            <div class="tp-product-details-msg mb-15">
                                <ul>
                                    <li>30 ngày trả hàng dễ dàng</li>
                                    <li>Đặt hàng trước 2:30 chiều để được giao hàng trong ngày</li>
                                </ul>
                            </div>
                            <div class="tp-product-details-payment d-flex align-items-center flex-wrap justify-content-between">
                                <ul class="mb-0" style="list-style: disc; padding-left: 20px;">
                                    <li>Uy tín đặt lên hàng đầu – bạn hoàn toàn yên tâm khi mua sắm</li>
                                    <li>Thanh toán an toàn, bảo mật – mọi thông tin được bảo vệ tuyệt đối</li>
                                </ul>
                                <img src="assets/img/product/icons/payment-option.png" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Đánh giá sản phẩm -->
        <div class="tp-product-details-bottom pb-140">
            <div class="container">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="tp-product-details-tab-nav tp-tab">
                            <nav>
                                <div class="nav nav-tabs justify-content-center p-relative tp-product-tab"
                                    id="navPresentationTab" role="tablist">
                                    <button class="nav-link" id="nav-description-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-description" type="button" role="tab"
                                        aria-controls="nav-description" aria-selected="true">Mô tả chi tiết</button>
                                    <button class="nav-link" id="nav-review-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-review" type="button" role="tab" aria-controls="nav-review"
                                        aria-selected="false">
                                        Đánh giá
                                    </button>
                                    <span id="productTabMarker" class="tp-product-details-tab-line"></span>
                                </div>
                            </nav>
                            <div class="tab-content" id="navPresentationTabContent">
                                <!-- Mô tả sản phẩm  -->
                                <div class="tab-pane fade" id="nav-description" role="tabpanel"
                                    aria-labelledby="nav-description-tab" tabindex="0">
                                    <div class="tp-product-details-desc-wrapper pt-80">
                                        <div class="row justify-content-center">
                                            <div class="col-xl-10">
                                                <div class="tp-product-details-desc-item pb-105">
                                                    @if (!empty($product->description))
                                                        {!! $product->description !!}
                                                    @else
                                                        <div class="row">
                                                            <div class="col-lg-6">
                                                                <div class="tp-product-details-desc-content pt-25">
                                                                    <h3 class="tp-product-details-desc-title">Thông tin
                                                                        sản phẩm đang cập nhật</h3>
                                                                    <p>Chúng tôi sẽ cập nhật mô tả chi tiết sớm nhất.
                                                                        Cảm ơn bạn đã quan tâm đến sản phẩm.</p>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <div class="tp-product-details-desc-thumb">
                                                                    <img src="{{ asset('assets/img/product/details/desc/default-desc.jpg') }}"
                                                                        alt="Thông tin đang cập nhật">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Đánh giá sản phẩm -->
                                <div class="tab-pane fade" id="nav-review" role="tabpanel"
                                    aria-labelledby="nav-review-tab" tabindex="0">
                                    <div class="tp-product-details-review-wrapper pt-60">
                                        <div class="row">
                                            <!-- Customer Reviews -->
                                            <div class="tp-product-details-review-number d-inline-block mb-50">
                                                <h3 class="tp-product-details-review-number-title">Đánh giá của khách
                                                    hàng
                                                </h3>

                                                <!-- Tổng điểm trung bình -->
                                                <div class="mb-3">
                                                    @php
                                                        // Chỉ lấy các đánh giá gốc
                                                        $validReviews = $product->reviews
                                                            ->where('is_active', 1)
                                                            ->where('review_id', null)
                                                            ->where('rating', '>=', 1);
                                                        $averageRating =
                                                            $validReviews->count() > 0
                                                            ? $validReviews->avg('rating')
                                                            : 0;
                                                        $reviewCount = $validReviews->count();
                                                    @endphp

                                                    <span class="fs-4 fw-bold text-warning">
                                                        {!! str_repeat('★', floor($averageRating)) !!}{!! str_repeat('☆', 5 - floor($averageRating)) !!}
                                                    </span>
                                                    <span class="ms-2">({{ number_format($averageRating, 1) }}/5 từ
                                                        {{ $reviewCount }} đánh giá)</span>
                                                </div>
                                            </div>
                                            <div class="tp-product-details-review-list pr-110">
                                                <h3 id="reviews" class="tp-product-details-review-title">ĐÁNH GIÁ SẢN PHẨM</h3>


                                                <!-- Hiển thị reviews -->

                                                @forelse ($reviews as $review)
                                                    <div class="review-item border-bottom pb-3 mb-3">
                                                        <div
                                                            class="tp-product-details-review-avater d-flex align-items-start mb-4">
                                                            <div class="tp-product-details-review-avater-thumb me-3">
                                                                <a href="#">
                                                                    <img src="{{ $review->user->avatar ? asset('storage/' . $review->user->avatar) : asset('assets2/img/users/avatars.png') }}"
                                                                        alt="avatar" width="50">
                                                                </a>
                                                            </div>
                                                            <div class="tp-product-details-review-avater-content">
                                                                <div
                                                                    class="tp-product-details-review-avater-rating text-warning mb-1">
                                                                    {!! str_repeat('<i class="fa-solid fa-star"> </i>', $review->rating) !!}
                                                                    {!! str_repeat('<i class="fa-regular fa-star"> </i>', 5 - $review->rating) !!}
                                                                </div>
                                                                <h3 class="tp-product-details-review-avater-title mb-1">
                                                                    {{ $review->user->fullname }}
                                                                </h3>
                                                                <span
                                                                    class="tp-product-details-review-avater-meta d-block mb-1">{{ $review->created_at->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') }}

                                                                </span>
                                                                <!-- Hiển thị phân loại đã mua  -->
                                                                @if($review->orderItem && $review->orderItem->attributes_variant)
                                                                    @php
                                                                        $attrs = json_decode($review->orderItem->attributes_variant, true);
                                                                    @endphp
                                                                    <div class="mt-1 text-muted small">
                                                                        Phân loại hàng: <strong>
                                                                            @foreach($attrs as $key => $value)
                                                                                {{ ucfirst($key) }} : {{ $value }}@if(!$loop->last), @endif
                                                                            @endforeach
                                                                        </strong>
                                                                    </div>
                                                                @endif
                                                                <div class="tp-product-details-review-avater-comment mb-1">
                                                                    <div class="mt-1 text-muted small">
                                                                        <strong>{{ $review->review_text }}</strong>
                                                                    </div>
                                                                </div>
                                                                @if($review ->images->count())
                                                                    <div class="mt-2 d-flex flex-wrap gap-2">
                                                                        @foreach($review->images as $img)
                                                                            <a href="{{asset('storage/'. $img->image_path)}}" target="_blank" class="review-image-link">
                                                                                <img src="{{asset('storage/'. $img->image_path)}}" alt="Review Images" width="80px" class="review-image">
                                                                            </a>
                                                                        @endforeach
                                                                    </div>
                                                                @endif
                                                                @foreach ($review->replies as $reply)
                                                                    <div
                                                                        class="ms-4 mt-2 ps-3 border-start border-2 border-primary">
                                                                        <strong
                                                                            class="text-primary">Phản hồi của Shop</strong>
                                                                        trả lời:
                                                                        <p class="mb-0">{{ $reply->review_text }}</p>
                                                                        <small
                                                                            class="text-muted">{{ $reply->created_at->format('d/m/Y H:i') }}</small>
                                                                    </div>
                                                                @endforeach

                                                            </div>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <p>Chưa có đánh giá nào</p>
                                                @endforelse
                                                {{-- Thanh phân trang --}}
                                                <div class="mt-4 d-flex justify-content-end" >
                                                    {{ $reviews->withQueryString()->fragment('reviews')->links() }}
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- end col -->

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
        </div>
        </div>
    </section>

    @php
        $currentCategoryIds = $product->categories->pluck('id')->toArray();

        $relatedByCategory = $relatedProducts
            ->filter(function ($related) use ($currentCategoryIds) {
                $relatedCategoryIds = $related->categories->pluck('id')->toArray();
                return count(array_intersect($currentCategoryIds, $relatedCategoryIds)) > 0;
            })
            ->take(4);
    @endphp

    <!-- //Sản phẩm tương tự -->
    <section class="tp-related-product pt-95 pb-120">
        <div class="container">
            <div class="row">
                <div class="tp-section-title-wrapper-6 text-center mb-40">
                    <span class="tp-section-title-pre-6">Sản Phẩm Tương Tự</span>
                    <h3 class="tp-section-title-6">Có Thể Bạn Thích</h3>
                </div>
            </div>
            <div class="position-relative">
                <div class="tp-product-related-slider">
                    <div class="swiper tp-product-related-slider-active">
                        <div class="swiper-wrapper">
                            @forelse ($relatedByCategory as $related)
                                <div class="swiper-slide">
                                    <div class="tp-product-item-2 mb-40">
                                        <div class="tp-product-thumb-2 p-relative z-index-1 fix w-img">
                                            @if (!empty($related->slug))
                                                <a href="{{ route('client.product.show', ['slug' => $related->slug]) }}">
                                                    <img src="{{ $related->image_url }}" alt="{{ $related->name }}">
                                                </a>
                                            @else
                                                <span>
                                                    <img src="{{ $related->image_url }}" alt="{{ $related->name }}">
                                                </span>
                                            @endif
                                        </div>
                                        <div class="tp-product-content-2 pt-15">
                                            <div class="tp-product-tag-2">
                                                <a href="#">{{ $related->brand->name ?? 'Không có thương hiệu' }}</a>
                                            </div>
                                            <h3 class="tp-product-title-2">
                                                @if (!empty($related->slug))
                                                    <a
                                                        href="{{ route('client.product.show', ['slug' => $related->slug]) }}">{{ $related->name }}</a>
                                                @else
                                                    <span>{{ $related->name }}</span>
                                                @endif
                                            </h3>
                                            <div class="tp-product-rating-icon tp-product-rating-icon-2">
                                                @for ($i = 0; $i < 5; $i++)
                                                    <span><i class="fa-solid fa-star"></i></span>
                                                @endfor
                                            </div>
                                            <div class="tp-product-price-wrapper-2">
                                                <span
                                                    class="tp-product-price-2 new-price">{{ number_format($related->price, 0, ',', '.') }}₫</span>
                                                @if ($related->original_price && $related->original_price > $related->price)
                                                    <span
                                                        class="tp-product-price-2 old-price">{{ number_format($related->original_price, 0, ',', '.') }}₫</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="d-flex justify-content-center align-items-center w-100" style="height: 150px;">
                                    <p class="mb-0 text-center">Không có sản phẩm liên quan</p>
                                </div>
                            @endforelse
                        </div>
                        <!-- Nút điều hướng -->
                        <div class="swiper-button-prev">
                            <svg viewBox="0 0 32 32" width="28" height="28">
                                <polyline points="20 8 12 16 20 24" fill="none" stroke="currentColor" stroke-width="3"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                        <div class="swiper-button-next">
                            <svg viewBox="0 0 32 32" width="28" height="28">
                                <polyline points="12 8 20 16 12 24" fill="none" stroke="currentColor" stroke-width="3"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.btn-add-wishlist').forEach(button => {
            button.addEventListener('click', function () {
                const productId = this.dataset.productId;

                fetch('/wishlist/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({
                        product_id: productId
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.message && data.message.includes('đã có trong danh sách yêu thích')) {
                            if (window.toastr) {
                                toastr.error(data.message);
                            }
                        } else {
                            if (window.toastr) {
                                toastr.success(data.message || "Đã thêm vào yêu thích!");
                            }
                        }
                    })
                    .catch(error => {
                        if (window.toastr) {
                            toastr.error("Có lỗi xảy ra!");
                        }
                        console.error(error);
                    });
            });
        });
    });
</script>
{{-- 1. Link Swiper --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />
<script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const colorButtons = document.querySelectorAll(".tp-color-variation-btn");
    const colorNameSpan = document.getElementById("selected-color-name");

    if (colorButtons.length > 0) {
        // Gán mặc định từ nút đang active
        const activeBtn = document.querySelector(".tp-color-variation-btn.active");
        if (activeBtn) {
            colorNameSpan.textContent = activeBtn.getAttribute("title");
        }

        // Lắng nghe click
        colorButtons.forEach(btn => {
            btn.addEventListener("click", function () {
                // Xóa active cũ
                colorButtons.forEach(b => b.classList.remove("active"));
                // Đặt active mới
                this.classList.add("active");

                // Lấy tên màu từ title
                const colorName = this.getAttribute("title") || "Không rõ màu";
                colorNameSpan.textContent = colorName;
            });
        });
    }
});
</script>
{{-- 2. Script chính --}}
<script>
    const defaultImages = @json($defaultImages);
</script>

<script>
    const variants = @json($variantsWithImages);
    const cartProductIds = @json($cartProductIds ?? []);
    const cartVariantIds = @json($cartVariantIds ?? []);

    let selectedColorCode = null;
    let selectedSize = null;

    const formatCurrency = num => num.toLocaleString('vi-VN') + '₫';

    function updateTotal() {
        const unitPriceEl = document.getElementById('unit-price');
        const quantityInput = document.getElementById('quantity') || document.querySelector('.tp-cart-input');
        const totalPriceEl = document.getElementById('total-price');
        const unit = parseFloat(unitPriceEl.dataset.unit || 0);
        let qty = parseInt(quantityInput.value) || 1;
        qty = qty < 1 ? 1 : qty;
        quantityInput.value = qty;
        totalPriceEl.textContent = formatCurrency(unit * qty);
    }

    function updateVariantInfo() {
        if (!selectedColorCode || !selectedSize) return;
        const variant = variants.find(v => {
            const [_, size, color] = v.sku.toUpperCase().split('-');
            return size === selectedSize && color === selectedColorCode;
        });
        if (!variant) return;

        document.getElementById('sku-display').textContent = variant.sku;
        const price = Number(variant.sale_price ?? variant.regular_price);
        const oldPrice = variant.sale_price ? Number(variant.regular_price) : null;
        document.getElementById('product-price').textContent = formatCurrency(price);
        const oldPriceEl = document.getElementById('product-old-price');
        oldPriceEl.textContent = oldPrice ? formatCurrency(oldPrice) : '';
        oldPriceEl.style.display = oldPrice ? 'inline' : 'none';
        const unitPriceEl = document.getElementById('unit-price');
        unitPriceEl.dataset.unit = price;
        unitPriceEl.textContent = formatCurrency(price);

        const quantityInput = document.getElementById('quantity') || document.querySelector('.tp-cart-input');
        quantityInput.value = 1;
        updateTotal();

        const stockEl = document.getElementById('product-stock');
        if (stockEl) {
            stockEl.textContent = variant.stock > 0 ? `Trong kho: ${variant.stock}` : 'Hết hàng';
            quantityInput.disabled = variant.stock < 1;
            const addBtn = document.getElementById('add-to-cart-btn');
            if (addBtn) addBtn.disabled = variant.stock < 1;
        }

        const mainImageEl = document.getElementById('mainProductImage');
        if (mainImageEl && variant.img) {
            mainImageEl.style.opacity = 0;
            setTimeout(() => {
                mainImageEl.src = '/storage/' + variant.img;
                mainImageEl.onload = () => mainImageEl.style.opacity = 1;
            }, 150);
        }

        const subImageGallery = document.getElementById('subImageGallery');
        subImageGallery.innerHTML = '';
        if (variant.images?.length) {
            variant.images.forEach(img => {
                const imgEl = document.createElement('img');
                imgEl.src = '/storage/' + img.url;
                imgEl.className = 'img-thumbnail thumbnail-preview';
                imgEl.style.width = '100%';
                imgEl.style.height = '80px';
                imgEl.style.objectFit = 'cover';
                imgEl.style.cursor = 'pointer';
                imgEl.addEventListener('click', () => {
                    if (mainImageEl && mainImageEl.src !== imgEl.src) {
                        mainImageEl.style.opacity = 0;
                        setTimeout(() => {
                            mainImageEl.src = imgEl.src;
                            mainImageEl.onload = () => mainImageEl.style.opacity = 1;
                        }, 150);
                    }
                });
                subImageGallery.appendChild(imgEl);
            });
        }

        ['variant_sku', 'product_variant_id', 'variant_price'].forEach(id => {
            const el = document.getElementById(id);
            if (!el) return;
            if (id === 'variant_sku') el.value = variant.sku;
            if (id === 'product_variant_id') el.value = variant.id;
            if (id === 'variant_price') el.value = variant.sale_price ?? variant.regular_price;
        });
        // Hiển thị chất liệu nếu có
        const materialSection = document.getElementById('materialSection');
        const materialText = document.getElementById('materialText');
        if (materialSection && materialText) {
            if (variant.material) {
                materialText.textContent = variant.material;
                materialSection.classList.remove('d-none');
            } else {
                materialText.textContent = '';
                materialSection.classList.add('d-none');
            }
        }

        checkAndToggleAddToCartBtn();
    }

    function filterSizesByColor(colorCode) {
        const availableSizes = variants
            .filter(v => v.sku.toUpperCase().split('-')[2] === colorCode)
            .map(v => v.sku.toUpperCase().split('-')[1]);

        document.querySelectorAll('.tp-size-variation-btn').forEach(btn => {
            btn.style.display = availableSizes.includes(btn.dataset.size.toUpperCase()) ? '' : 'none';
        });

        if (!availableSizes.includes(selectedSize)) {
            selectedSize = null;
            document.querySelectorAll('.tp-size-variation-btn').forEach(btn => btn.classList.remove('active'));
        }
    }

    function getMaxQty() {
        if (variants.length && selectedColorCode && selectedSize) {
            const variant = variants.find(v => {
                const [_, size, color] = v.sku.toUpperCase().split('-');
                return size === selectedSize && color === selectedColorCode;
            });
            if (variant) return variant.stock;
        }
        return {{ $product->stock ?? 9999 }};
    }

    function checkAndToggleAddToCartBtn() {
        const addToCartForm = document.querySelector('#detail-add-to-cart-form');
        const addToCartBtn = addToCartForm?.querySelector('button[type="submit"]');
        const qtyInput = document.getElementById('quantity');
        const minusBtn = document.getElementById('detail-cart-minus');
        const plusBtn = document.getElementById('detail-cart-plus');
        const hasVariants = variants.length > 0;
        const productId = {{ $product->id }};

        function setNormalUI() {
            if (!addToCartBtn) return;
            addToCartBtn.disabled = false;
            addToCartBtn.textContent = 'Thêm vào giỏ hàng';
            addToCartBtn.classList.remove('in-cart', 'already-toast');
            if (qtyInput) qtyInput.disabled = false;
            if (minusBtn) minusBtn.classList.remove('disabled');
            if (plusBtn) plusBtn.classList.remove('disabled');
        }

        setNormalUI();
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Gán sự kiện cho các nút màu
        document.querySelectorAll('.tp-color-variation-btn').forEach(btn => {
            if (btn.classList.contains('disabled')) return;
            btn.addEventListener('click', () => {
                document.querySelectorAll('.tp-color-variation-btn').forEach(b => b.classList
                    .remove('active'));
                btn.classList.add('active');
                selectedColorCode = btn.dataset.color.toUpperCase();
                filterSizesByColor(selectedColorCode);
                updateVariantInfo();
            });
        });

        // Gán sự kiện cho các nút size
        document.querySelectorAll('.tp-size-variation-btn').forEach(btn => {
            if (btn.classList.contains('disabled')) return;
            btn.addEventListener('click', () => {
                document.querySelectorAll('.tp-size-variation-btn').forEach(b => b.classList
                    .remove('active'));
                btn.classList.add('active');
                selectedSize = btn.dataset.size.toUpperCase();
                updateVariantInfo();
            });
        });

        // Nút trừ số lượng
        document.getElementById('detail-cart-minus')?.addEventListener('click', () => {
            const quantityInput = document.getElementById('quantity') || document.querySelector(
                '.tp-cart-input');
            let val = parseInt(quantityInput.value) || 1;
            if (val > 1) quantityInput.value = val - 1;
            updateTotal();
        });

        // Nút cộng số lượng
        document.getElementById('detail-cart-plus')?.addEventListener('click', () => {
            const quantityInput = document.getElementById('quantity') || document.querySelector(
                '.tp-cart-input');
            let val = parseInt(quantityInput.value) || 1;
            const maxQty = getMaxQty();
            if (val >= maxQty) {
                if (window.toastr) toastr.error('Chỉ còn ' + maxQty + ' sản phẩm trong kho!');
                quantityInput.value = maxQty;
            } else {
                quantityInput.value = val + 1;
            }
            updateTotal();
        });

        // Giới hạn số lượng nhập tay
        const quantityInput = document.getElementById('quantity') || document.querySelector('.tp-cart-input');
        quantityInput.addEventListener('input', () => {
            let val = parseInt(quantityInput.value) || 1;
            const maxQty = getMaxQty();
            if (val > maxQty) {
                if (window.toastr) toastr.error('Chỉ còn ' + maxQty + ' sản phẩm trong kho!');
                val = maxQty;
            }
            if (val < 1) val = 1;
            quantityInput.value = val;
            updateTotal();
        });
        quantityInput.addEventListener('blur', () => {
            let val = parseInt(quantityInput.value) || 1;
            const maxQty = getMaxQty();
            if (val > maxQty) {
                if (window.toastr) toastr.error('Chỉ còn ' + maxQty + ' sản phẩm trong kho!');
                quantityInput.value = maxQty;
            }
            updateTotal();
        });

        // Khởi tạo slider (giữ nguyên)
        new Swiper('.tp-product-related-slider-active', {
            slidesPerView: 4,
            spaceBetween: 20,
            loop: false,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            breakpoints: {
                1200: {
                    slidesPerView: 4
                },
                992: {
                    slidesPerView: 3
                },
                768: {
                    slidesPerView: 2
                },
                576: {
                    slidesPerView: 1
                },
            }
        });
        const addToCartForm = document.querySelector('#detail-add-to-cart-form');
        if (addToCartForm) {
            addToCartForm.addEventListener('submit', e => {
                e.preventDefault();

                const qtyInput = document.getElementById('quantity');
                const qty = parseInt(qtyInput.value) || 1;
                let maxQty = {{ $product->stock ?? 9999 }};
                const hasVariants = variants.length > 0;
                const productId = {{ $product->id }};
                if (hasVariants) {
                    const variantId = document.getElementById('product_variant_id').value;
                    if (!variantId) {
                        if (window.toastr) toastr.error('Vui lòng phân loại hàng!');
                        return;
                    }
                    const variant = variants.find(v => v.id == variantId);
                    if (variant) maxQty = variant.stock;
                }
                if (qty > maxQty) {
                    if (window.toastr) toastr.error('Chỉ còn ' + maxQty + ' sản phẩm trong kho!');
                    qtyInput.value = maxQty;
                    return;
                }
                // Kiểm tra số lượng tồn kho
                let availableStock = {{ $product->stock ?? 9999 }};
                if (hasVariants) {
                    const variantId = document.getElementById('product_variant_id').value;
                    if (!variantId) {
                        if (window.toastr) toastr.error('Vui lòng chọn biến thể sản phẩm!');
                        return;
                    }
                    const variant = variants.find(v => v.id == variantId);
                    if (variant) availableStock = variant.stock;
                }

                // Kiểm tra số lượng tồn kho tổng cộng (số lượng trong giỏ hàng + số lượng muốn thêm)
                let currentQuantity = 0;
                if (hasVariants) {
                    const variantId = document.getElementById('product_variant_id').value;
                    if (cartVariantIds.includes(String(variantId))) {
                        // Tìm số lượng hiện tại trong giỏ hàng cho biến thể này
                        const cartItems = document.querySelectorAll('.cart-item');
                        cartItems.forEach(item => {
                            const itemVariantId = item.dataset.variantId;
                            if (itemVariantId === variantId) {
                                const itemQty = parseInt(item.querySelector('.cart-item-qty').textContent);
                                currentQuantity += itemQty;
                            }
                        });
                    }
                } else {
                    if (cartProductIds.includes(Number(productId))) {
                        // Tìm số lượng hiện tại trong giỏ hàng cho sản phẩm này
                        const cartItems = document.querySelectorAll('.cart-item');
                        cartItems.forEach(item => {
                            const itemProductId = parseInt(item.dataset.productId);
                            if (itemProductId === productId) {
                                const itemQty = parseInt(item.querySelector('.cart-item-qty').textContent);
                                currentQuantity += itemQty;
                            }
                        });
                    }
                }

                const totalQuantity = currentQuantity + parseInt(qtyInput.value);
                if (totalQuantity > availableStock) {
                    const message = `Đã có đủ sản phẩm trong giỏ hàng`;
                    if (window.toastr) toastr.error(message);
                    qtyInput.value = availableStock - currentQuantity;
                    return;
                }
                const formData = new FormData(addToCartForm);
                if (qtyInput) formData.set('quantity', qtyInput.value);

                fetch('{{ route('client.shopping-cart.add') }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            .getAttribute('content')
                    },
                    body: formData
                })
                    .then(res => {
                        if (!res.ok) {
                            return res.json().then(data => {
                                if (data && data.message) {
                                    throw new Error(data.message);
                                }
                                throw new Error('Lỗi không xác định từ server');
                            });
                        }
                        return res.json();
                    })
                    .then(data => {
                        if (data.success) {
                            if (window.toastr) toastr.success(data.message ||
                                'Đã thêm sản phẩm vào giỏ hàng!');
                            document.dispatchEvent(new CustomEvent('cart:updated'));
                            // Cập nhật cart IDs
                            if (hasVariants) {
                                const variantId = document.getElementById('product_variant_id')
                                    .value;
                                cartVariantIds.push(String(variantId));
                            } else {
                                cartProductIds.push(Number(productId));
                            }
                            checkAndToggleAddToCartBtn();
                        } else {
                            // Không cần hiển thị thông báo lỗi ở đây vì đã xử lý ở catch
                            console.error('Server response error:', data);
                        }
                    })
                    .catch(err => {
                        console.error('Lỗi khi thêm vào giỏ hàng:', err);
                        if (window.toastr) {
                            // Chỉ hiển thị một thông báo lỗi duy nhất từ server
                            if (err instanceof Error && err.message) {
                                toastr.error(err.message);
                            } else {
                                toastr.error('Lỗi không xác định từ server');
                            }
                        }
                    });
            });
        }

        // Đồng bộ khi xóa sản phẩm khỏi mini-cart
        document.addEventListener('cart:item-removed', e => {
            const detail = e.detail || {};
            if ('product_id' in detail) {
                const idx = cartProductIds.indexOf(Number(detail.product_id));
                if (idx !== -1) cartProductIds.splice(idx, 1);
            }
            if ('variant_id' in detail && detail.variant_id) {
                const idx = cartVariantIds.map(String).indexOf(String(detail.variant_id));
                if (idx !== -1) cartVariantIds.splice(idx, 1);
            }
            checkAndToggleAddToCartBtn();
        });
    });
    // Hiển thị ảnh phụ mặc định từ defaultImages khi chưa chọn biến thể
    const subImageGallery = document.getElementById('subImageGallery');
    const mainImageEl = document.getElementById('mainProductImage');

    if (defaultImages.length) {
        subImageGallery.innerHTML = '';
        defaultImages.forEach(img => {
            const imgEl = document.createElement('img');
            imgEl.src = '/storage/' + img.url;
            imgEl.className = 'img-thumbnail thumbnail-preview';
            imgEl.style.width = '100%';
            imgEl.style.height = '80px';
            imgEl.style.objectFit = 'cover';
            imgEl.style.cursor = 'pointer';
            imgEl.addEventListener('click', () => {
                if (mainImageEl) {
                    mainImageEl.style.opacity = 0;
                    setTimeout(() => {
                        mainImageEl.src = imgEl.src;
                        mainImageEl.onload = () => mainImageEl.style.opacity = 1;
                    }, 150);
                }
            });
            subImageGallery.appendChild(imgEl);
        });
    }

    function showUniqueImagesFromAllVariants() {
        const subImageGallery = document.getElementById('subImageGallery');
        const mainImageEl = document.getElementById('mainProductImage');
        if (!subImageGallery) return;

        subImageGallery.innerHTML = '';
        const shownRelativeUrls = new Set();

        const getRelativeUrl = (url) => {
            if (!url) return '';
            return url.replace(/^\/?storage\//, '');
        };

        const addSubImage = (url) => {
            const relativeUrl = getRelativeUrl(url);
            if (!relativeUrl || shownRelativeUrls.has(relativeUrl)) return;

            shownRelativeUrls.add(relativeUrl);

            const imgEl = document.createElement('img');
            imgEl.src = '/storage/' + relativeUrl;
            imgEl.className = 'img-thumbnail thumbnail-preview';
            imgEl.style.width = '100%';
            imgEl.style.height = '80px';
            imgEl.style.objectFit = 'cover';
            imgEl.style.cursor = 'pointer';
            imgEl.addEventListener('click', () => {
                if (mainImageEl) {
                    mainImageEl.style.opacity = 0;
                    setTimeout(() => {
                        mainImageEl.src = imgEl.src;
                        mainImageEl.onload = () => mainImageEl.style.opacity = 1;
                    }, 150);
                }
            });
            subImageGallery.appendChild(imgEl);
        };

        // Ảnh gốc
        defaultImages.forEach(img => {
            if (img.url) addSubImage(img.url);
        });

        // Ảnh chính + phụ từ các biến thể
        variants.forEach(variant => {
            if (variant.img) addSubImage(variant.img);

            if (Array.isArray(variant.images)) {
                variant.images.forEach(img => {
                    if (img.url) addSubImage(img.url);
                });
            }
        });
    }
</script>

<!-- Tránh khi load trang thì vẫn ở lại trang reviews và description -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
        //Gắn sự kiện click: Lưu tab được chọn vào localStorage
        tabButtons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                const target = btn.getAttribute('data-bs-target');
                localStorage.setItem('activeProductTab', target);
            });
        });
        //Khi reload lại, lất tab cũ
        const lastActiveTab = localStorage.getItem('activeProductTab');
        if (lastActiveTab) {
            const tabTriggerEl = document.querySelector(`[data-bs-target="${lastActiveTab}"]`);
            if (tabTriggerEl) {
                new bootstrap.Tab(tabTriggerEl).show();
            }
        }
    });
</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/imagesloaded@4/imagesloaded.pkgd.min.js"></script>

@endsection
