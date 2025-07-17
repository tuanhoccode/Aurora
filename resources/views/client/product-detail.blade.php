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
                                        @foreach ($product->images as $image)
                                            <img src="{{ asset('storage/' . $image->url) }}"
                                                class="img-thumbnail thumbnail-preview mb-2"
                                                style="width: 100%; height: 80px; object-fit: cover; cursor: pointer;"
                                                onclick="document.getElementById('mainProductImage').src=this.src">
                                        @endforeach
                                    </div>

                                    <!-- Ảnh chính bên phải -->
                                    <div class="col-10 d-flex justify-content-center">
                                        <div class="card shadow-sm border-0" style="max-width: 500px;">
                                            <div class="position-relative overflow-hidden rounded-4">
                                                <img id="mainProductImage"
                                                    src="{{ asset('storage/' . $product->thumbnail) }}"
                                                    alt="{{ $product->name }}" class="img-fluid w-100"
                                                    style="object-fit: contain; max-height: 500px; transition: transform 0.3s ease;">
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
                                                Vui lòng chọn màu và kích cỡ
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
                                        $unit = $product->sale_price ?? $product->price;
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

                                        {{-- Đơn giá (ẩn số thật để tính toán) --}}
                                        <span id="unit-price" data-unit="{{ $unit }}"
                                            style="display: none;"></span>
                                    </div>

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
                                            // Định nghĩa tất cả các màu hệ thống
                                            $allColors = [
                                                'DO' => ['name' => 'Đỏ', 'hex' => '#FF0000'],
                                                'VANG' => ['name' => 'Vàng', 'hex' => '#F8B655'],
                                                'DEN' => ['name' => 'Đen', 'hex' => '#000000'],
                                                'TRANG' => ['name' => 'Trắng', 'hex' => '#FFFFFF'],
                                                'XAM' => ['name' => 'Xám', 'hex' => '#CBCBCB'],
                                                'XA' => ['name' => 'Xanh', 'hex' => '#00FF00'],
                                            ];

                                            // Tìm tất cả mã màu có thực sự xuất hiện trong SKU
                                            $productColors = collect($product->variants)
                                                ->map(function ($variant) {
                                                    $parts = explode('-', $variant->sku);
                                                    return strtoupper(end($parts)); // Lấy phần cuối của SKU làm mã màu
                                                })
                                                ->unique();

                                            // Các size đang có
                                            $productSizes = collect($product->variants)
                                                ->map(function ($variant) {
                                                    $parts = explode('-', $variant->sku);
                                                    return isset($parts[1]) ? strtoupper($parts[1]) : null;
                                                })
                                                ->filter()
                                                ->unique();

                                            // Tất cả size định nghĩa sẵn
                                            $allSizes = ['S', 'M', 'L', 'XL', 'XXL'];
                                        @endphp
                                        <div class="tp-product-details-variation-item">
                                            <h4 class="tp-product-details-variation-title">Màu :</h4>
                                            <div class="tp-product-details-variation-list">
                                                @foreach ($allColors as $code => $color)
                                                    @php
                                                        $isAvailable = $productColors->contains($code);
                                                    @endphp
                                                    @if ($isAvailable)
                                                        <button type="button" class="tp-color-variation-btn"
                                                            data-color="{{ $code }}">
                                                            <span class="color-circle"
                                                                style="background-color: {{ $color['hex'] }};"></span>
                                                            <span
                                                                class="tp-color-variation-tootltip">{{ $color['name'] }}</span>
                                                        </button>
                                                    @endif
                                                @endforeach

                                            </div>
                                        </div>

                                        <div class="tp-product-details-variation-item">
                                            <h4 class="tp-product-details-variation-title">Kích cỡ :</h4>
                                            <div class="tp-product-details-variation-list">
                                                @foreach ($productSizes as $size)
                                                    <button type="button" class="tp-size-variation-btn"
                                                        data-size="{{ $size }}">
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
                                                    <input id="quantity" name="quantity" class="tp-cart-input"
                                                        type="text" value="1">
                                                    <span id="detail-cart-plus" class="tp-cart-plus">+</span>
                                                </div>
                                            </div>
                                            <div class="tp-product-details-add-to-cart mb-15 w-100">
                                                <form id="detail-add-to-cart-form" class="add-to-cart-form"
                                                    action="{{ route('shopping-cart.add') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                    <input type="hidden" name="product_variant_id"
                                                        id="product_variant_id" value="">
                                                    <input type="hidden" name="variant_sku" id="variant_sku"
                                                        value="">
                                                    <input type="hidden" name="price" id="variant_price"
                                                        value="{{ !$hasVariants ? $product->sale_price ?? $product->price : '' }}">
                                                    <button type="submit"
                                                        class="tp-product-details-add-to-cart-btn w-100">Thêm vào giỏ
                                                        hàng</button>
                                                </form>
                                            </div>
                                        </div>
                                        <form method="POST" action="{{ route('checkout') }}">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="tp-product-details-buy-now-btn w-100">Mua ngay</button>
                                        </form>
                                    </div>
                                    <div class="tp-product-details-action-sm">
                                        <button type="button" class="tp-product-details-action-sm-btn">
                                            <svg width="14" height="16" viewBox="0 0 14 16" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M1 3.16431H10.8622C12.0451 3.16431 12.9999 4.08839 12.9999 5.23315V7.52268"
                                                    stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M3.25177 0.985168L1 3.16433L3.25177 5.34354" stroke="currentColor"
                                                    stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path
                                                    d="M12.9999 12.5983H3.13775C1.95486 12.5983 1 11.6742 1 10.5295V8.23993"
                                                    stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M10.748 14.7774L12.9998 12.5983L10.748 10.4191"
                                                    stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            So sánh sản phẩm
                                        </button>
                                        <button type="button" class="tp-product-details-action-sm-btn">
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
                                        <button type="button" class="tp-product-details-action-sm-btn">
                                            <svg width="17" height="16" viewBox="0 0 17 16" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M8.575 12.6927C8.775 12.6927 8.94375 12.6249 9.08125 12.4895C9.21875 12.354 9.2875 12.1878 9.2875 11.9907C9.2875 11.7937 9.21875 11.6275 9.08125 11.492C8.94375 11.3565 8.775 11.2888 8.575 11.2888C8.375 11.2888 8.20625 11.3565 8.06875 11.492C7.93125 11.6275 7.8625 11.7937 7.8625 11.9907C7.8625 12.1878 7.93125 12.354 8.06875 12.4895C8.20625 12.6249 8.375 12.6927 8.575 12.6927ZM8.55625 5.0638C8.98125 5.0638 9.325 5.17771 9.5875 5.40553C9.85 5.63335 9.98125 5.92582 9.98125 6.28294C9.98125 6.52924 9.90625 6.77245 9.75625 7.01258C9.60625 7.25272 9.3625 7.5144 9.025 7.79763C8.7 8.08087 8.44063 8.3795 8.24688 8.69352C8.05313 9.00754 7.95625 9.29385 7.95625 9.55246C7.95625 9.68792 8.00938 9.79567 8.11563 9.87572C8.22188 9.95576 8.34375 9.99578 8.48125 9.99578C8.63125 9.99578 8.75625 9.94653 8.85625 9.84801C8.95625 9.74949 9.01875 9.62635 9.04375 9.47857C9.08125 9.23228 9.16562 9.0137 9.29688 8.82282C9.42813 8.63195 9.63125 8.42568 9.90625 8.20402C10.2812 7.89615 10.5531 7.58829 10.7219 7.28042C10.8906 6.97256 10.975 6.62775 10.975 6.246C10.975 5.59333 10.7594 5.06996 10.3281 4.67589C9.89688 4.28183 9.325 4.0848 8.6125 4.0848C8.1375 4.0848 7.7 4.17716 7.3 4.36187C6.9 4.54659 6.56875 4.81751 6.30625 5.17463C6.20625 5.31009 6.16563 5.44863 6.18438 5.59025C6.20313 5.73187 6.2625 5.83962 6.3625 5.91351C6.5 6.01202 6.64688 6.04281 6.80313 6.00587C6.95937 5.96892 7.0875 5.88272 7.1875 5.74726C7.35 5.5256 7.54688 5.35627 7.77813 5.23929C8.00938 5.1223 8.26875 5.0638 8.55625 5.0638ZM8.5 15.7775C7.45 15.7775 6.46875 15.5897 5.55625 15.2141C4.64375 14.8385 3.85 14.3182 3.175 13.6532C2.5 12.9882 1.96875 12.2062 1.58125 11.3073C1.19375 10.4083 1 9.43547 1 8.38873C1 7.35431 1.19375 6.38762 1.58125 5.48866C1.96875 4.58969 2.5 3.80772 3.175 3.14273C3.85 2.47775 4.64375 1.95438 5.55625 1.57263C6.46875 1.19088 7.45 1 8.5 1C9.5375 1 10.5125 1.19088 11.425 1.57263C12.3375 1.95438 13.1313 2.47775 13.8063 3.14273C14.4813 3.80772 15.0156 4.58969 15.4094 5.48866C15.8031 6.38762 16 7.35431 16 8.38873C16 9.43547 15.8031 10.4083 15.4094 11.3073C15.0156 12.2062 14.4813 12.9882 13.8063 13.6532C13.1313 14.3182 12.3375 14.8385 11.425 15.2141C10.5125 15.5897 9.5375 15.7775 8.5 15.7775ZM8.5 14.6692C10.2625 14.6692 11.7656 14.0534 13.0094 12.822C14.2531 11.5905 14.875 10.1128 14.875 8.38873C14.875 6.6647 14.2531 5.18695 13.0094 3.95549C11.7656 2.72404 10.2625 2.10831 8.5 2.10831C6.7125 2.10831 5.20312 2.72404 3.97188 3.95549C2.74063 5.18695 2.125 6.6647 2.125 8.38873C2.125 10.1128 2.74063 11.5905 3.97188 12.822C5.20312 14.0534 6.7125 14.6692 8.5 14.6692Z"
                                                    fill="currentColor" stroke="currentColor" stroke-width="0.3" />
                                            </svg>
                                            Đặt một câu hỏi
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
                                <div class="tp-product-details-social">
                                    <span>Share: </span>
                                    <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                                    <a href="#"><i class="fa-brands fa-twitter"></i></a>
                                    <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                                    <a href="#"><i class="fa-brands fa-vimeo-v"></i></a>
                                </div>
                                <div class="tp-product-details-msg mb-15">
                                    <ul>
                                        <li>30 ngày trả hàng dễ dàng</li>
                                        <li>Đặt hàng trước 2:30 chiều để được giao hàng trong ngày</li>
                                    </ul>
                                </div>
                                <div
                                    class="tp-product-details-payment d-flex align-items-center flex-wrap justify-content-between">
                                    <p>Đảm bảo thanh toán an toàn <br> & bảo mật</p>
                                    <img src="assets/img/product/icons/payment-option.png" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
                                            data-bs-target="#nav-review" type="button" role="tab"
                                            aria-controls="nav-review" aria-selected="false">
                                            Đánh giá ({{ $reviewCount }})
                                        </button>
                                        <span id="productTabMarker" class="tp-product-details-tab-line"></span>
                                    </div>
                                </nav>
                                <div class="tab-content" id="navPresentationTabContent">
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
                                                        <span class="fs-4 fw-bold text-warning">
                                                            {!! str_repeat('★', floor($averageRating)) !!}{!! str_repeat('☆', 5 - floor($averageRating)) !!}
                                                        </span>
                                                        <span class="ms-2">({{ number_format($averageRating, 1) }}/5 từ
                                                            {{ $reviewCount }} đánh giá)</span>
                                                    </div>

                                                    <!-- Danh sách đánh giá -->
                                                    @forelse ($product->reviews->where('is_active', 1)->sortByDesc('created_at') as $review)
                                                        <div class="review-item border-bottom pb-3 mb-3">
                                                            <div class="d-flex justify-content-between">
                                                                <strong>{{ $review->user->name ?? 'Khách hàng' }}</strong>
                                                                <small
                                                                    class="text-muted">{{ $review->created_at->format('d/m/Y') }}</small>
                                                            </div>
                                                            <div class="text-warning mb-1">
                                                                {!! str_repeat('★', $review->rating) !!}{!! str_repeat('☆', 5 - $review->rating) !!}
                                                            </div>
                                                            <p class="mb-0">{{ $review->review_text }}</p>
                                                            @if ($review->reason)
                                                                <small class="text-muted">Lý do:
                                                                    {{ $review->reason }}</small>
                                                            @endif
                                                        </div>
                                                    @empty
                                                        <p>Chưa có đánh giá nào cho sản phẩm này.</p>
                                                    @endforelse
                                                </div>
                                                <div class="tp-product-details-review-form">
                                                    <h3 class="tp-product-details-review-form-title">Review
                                                        this
                                                        product</h3>
                                                    <p>Your email address will not be published. Required fields
                                                        are
                                                        marked *</p>
                                                    <form action="#">
                                                        <div
                                                            class="tp-product-details-review-form-rating d-flex align-items-center">
                                                            <p>Your Rating :</p>
                                                            <div
                                                                class="tp-product-details-review-form-rating-icon d-flex align-items-center">
                                                                <span><i class="fa-solid fa-star"></i></span>
                                                                <span><i class="fa-solid fa-star"></i></span>
                                                                <span><i class="fa-solid fa-star"></i></span>
                                                                <span><i class="fa-solid fa-star"></i></span>
                                                                <span><i class="fa-solid fa-star"></i></span>
                                                            </div>
                                                        </div>
                                                        <div class="tp-product-details-review-input-wrapper">
                                                            <div class="tp-product-details-review-input-box">
                                                                <div class="tp-product-details-review-input">
                                                                    <textarea id="msg" name="msg" placeholder="Write your review here..."></textarea>
                                                                </div>
                                                                <div class="tp-product-details-review-input-title">
                                                                    <label for="msg">Your Name</label>
                                                                </div>
                                                            </div>
                                                            <div class="tp-product-details-review-input-box">
                                                                <div class="tp-product-details-review-input">
                                                                    <input name="name" id="name" type="text"
                                                                        placeholder="Shahnewaz Sakil">
                                                                </div>
                                                                <div class="tp-product-details-review-input-title">
                                                                    <label for="name">Your Name</label>
                                                                </div>
                                                            </div>
                                                            <div class="tp-product-details-review-input-box">
                                                                <div class="tp-product-details-review-input">
                                                                    <input name="email" id="email" type="email"
                                                                        placeholder="shofy@mail.com">
                                                                </div>
                                                                <div class="tp-product-details-review-input-title">
                                                                    <label for="email">Your Email</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="tp-product-details-review-suggetions mb-20">
                                                            <div class="tp-product-details-review-remeber">
                                                                <input id="remeber" type="checkbox">
                                                                <label for="remeber">Save my name, email, and
                                                                    website
                                                                    in this browser for the next time I
                                                                    comment.</label>
                                                            </div>
                                                        </div>
                                                        <div class="tp-product-details-review-btn-wrapper">
                                                            <button class="tp-product-details-review-btn">Submit</button>
                                                        </div>
                                                    </form>
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
        <section class="tp-related-product pt-95 pb-120">
            <div class="container">
                <div class="row">
                    <div class="tp-section-title-wrapper-6 text-center mb-40">
                        <span class="tp-section-title-pre-6">Sản phẩm tương tự</span>
                        <h3 class="tp-section-title-6">Có thể bạn sẽ thích</h3>
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
                                                        <a href="{{ route('client.product.show', ['slug' => $related->slug]) }}">{{ $related->name }}</a>
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
                                                    <span class="tp-product-price-2 new-price">{{ number_format($related->price, 0, ',', '.') }}₫</span>
                                                    @if ($related->original_price && $related->original_price > $related->price)
                                                        <span class="tp-product-price-2 old-price">{{ number_format($related->original_price, 0, ',', '.') }}₫</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-center">Không có sản phẩm liên quan.</p>
                                @endforelse
                            </div>
                            <!-- Nút điều hướng -->
                            <div class="swiper-button-prev">
                                <svg viewBox="0 0 32 32" width="28" height="28">
                                    <polyline points="20 8 12 16 20 24" fill="none" stroke="currentColor"
                                        stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <div class="swiper-button-next">
                                <svg viewBox="0 0 32 32" width="28" height="28">
                                    <polyline points="12 8 20 16 12 24" fill="none" stroke="currentColor"
                                        stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    {{-- 1. Link Swiper --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>

    {{-- 2. Script chính --}}
    <script>
        const variants = @json($variantsWithImages);
        const cartProductIds = @json($cartProductIds ?? []);
        const cartVariantIds = @json($cartVariantIds ?? []);

        let selectedColorCode = null;
        let selectedSize = null;

        document.addEventListener("DOMContentLoaded", function() {
            const priceEl = document.getElementById('product-price');
            const oldPriceEl = document.getElementById('product-old-price');
            const stockEl = document.getElementById('product-stock');
            const mainImageEl = document.getElementById('mainProductImage');
            const unitPriceEl = document.getElementById('unit-price');
            const quantityInput = document.getElementById('quantity') || document.querySelector('.tp-cart-input');
            const totalPriceEl = document.getElementById('total-price');
            const subImageGallery = document.getElementById('subImageGallery');

            const formatCurrency = (num) => num.toLocaleString('vi-VN') + '₫';

            function updateTotal() {
                const unit = parseFloat(unitPriceEl.dataset.unit || 0);
                const qty = parseInt(quantityInput.value) || 1;
                quantityInput.value = qty < 1 ? 1 : qty;
                totalPriceEl.textContent = formatCurrency(unit * qty);
            }

            function updateVariantInfo() {
                if (!selectedColorCode || !selectedSize) return;
                const variant = variants.find(v => {
                    const parts = v.sku.toUpperCase().split('-');
                    return parts[1] === selectedSize && parts[2] === selectedColorCode;
                });
                if (!variant) return;

                document.getElementById('sku-display').textContent = variant.sku;
                const price = Number(variant.sale_price ?? variant.regular_price);
                const oldPrice = variant.sale_price ? Number(variant.regular_price) : null;
                priceEl.textContent = formatCurrency(price);
                oldPriceEl.textContent = oldPrice ? formatCurrency(oldPrice) : '';
                oldPriceEl.style.display = oldPrice ? 'inline' : 'none';
                unitPriceEl.dataset.unit = price;
                unitPriceEl.textContent = formatCurrency(price);
                quantityInput.value = 1;
                updateTotal();

                // Chỉ xử lý chức năng: disable input và nút nếu hết hàng
                if (stockEl) {
                    stockEl.textContent = variant.stock > 0 ? `Trong kho: ${variant.stock}` : 'Hết hàng';
                    quantityInput.disabled = variant.stock < 1;
                    const addBtn = document.getElementById('add-to-cart-btn');
                    if (addBtn) addBtn.disabled = variant.stock < 1;
                }

                if (mainImageEl && variant.img) {
                    mainImageEl.style.opacity = 0;
                    setTimeout(() => {
                        mainImageEl.src = '/storage/' + variant.img;
                        mainImageEl.onload = () => mainImageEl.style.opacity = 1;
                    }, 150);
                }

                subImageGallery.innerHTML = '';
                if (variant.images && variant.images.length > 0) {
                    variant.images.forEach(img => {
                        const imgEl = document.createElement('img');
                        imgEl.src = '/storage/' + img.url;
                        imgEl.className = 'img-thumbnail thumbnail-preview';
                        imgEl.style.width = '100%';
                        imgEl.style.height = '80px';
                        imgEl.style.objectFit = 'cover';
                        imgEl.style.cursor = 'pointer';
                        imgEl.addEventListener('click', function() {
                            if (mainImageEl) {
                                mainImageEl.style.opacity = 0;
                                setTimeout(() => {
                                    mainImageEl.src = this.src;
                                    mainImageEl.onload = () => mainImageEl.style.opacity =
                                        1;
                                }, 150);
                            }
                        });
                        subImageGallery.appendChild(imgEl);
                    });
                }

                const skuInput = document.getElementById('variant_sku');
                if (skuInput) skuInput.value = variant.sku;
                const variantIdInput = document.getElementById('product_variant_id');
                if (variantIdInput) variantIdInput.value = variant.id;
                const priceInput = document.getElementById('variant_price');
                if (priceInput) priceInput.value = variant.sale_price ?? variant.regular_price;
                if (typeof checkAndToggleAddToCartBtn === 'function') checkAndToggleAddToCartBtn();
            }

            function filterSizesByColor(colorCode) {
                const availableSizes = variants
                    .filter(v => v.sku.toUpperCase().split('-')[2] === colorCode)
                    .map(v => v.sku.toUpperCase().split('-')[1]);

                document.querySelectorAll('.tp-size-variation-btn').forEach(btn => {
                    const size = btn.dataset.size.toUpperCase();
                    btn.style.display = availableSizes.includes(size) ? '' : 'none';
                });

                if (!availableSizes.includes(selectedSize)) {
                    selectedSize = null;
                    document.querySelectorAll('.tp-size-variation-btn').forEach(btn => btn.classList.remove(
                        'active'));
                }
            }

            document.querySelectorAll('.tp-color-variation-btn').forEach(button => {
                if (button.classList.contains('disabled')) return;
                button.addEventListener('click', function() {
                    document.querySelectorAll('.tp-color-variation-btn').forEach(btn => btn
                        .classList.remove('active'));
                    this.classList.add('active');
                    selectedColorCode = this.dataset.color.toUpperCase();
                    filterSizesByColor(selectedColorCode);
                    updateVariantInfo();
                });
            });

            document.querySelectorAll('.tp-size-variation-btn').forEach(button => {
                if (button.classList.contains('disabled')) return;
                button.addEventListener('click', function() {
                    document.querySelectorAll('.tp-size-variation-btn').forEach(btn => btn.classList
                        .remove('active'));
                    this.classList.add('active');
                    selectedSize = this.dataset.size.toUpperCase();
                    updateVariantInfo();
                });
            });

            document.querySelector('#detail-cart-minus')?.addEventListener('click', () => {
                let val = parseInt(quantityInput.value) || 1;
                if (val > 1) quantityInput.value = val - 1;
                updateTotal();
            });

            function getMaxQty() {
                let maxQty = 9999;
                if (variants.length > 0 && selectedColorCode && selectedSize) {
                    const variant = variants.find(v => {
                        const parts = v.sku.toUpperCase().split('-');
                        return parts[1] === selectedSize && parts[2] === selectedColorCode;
                    });
                    if (variant) maxQty = variant.stock;
                } else {
                    maxQty = {{ $product->stock ?? 9999 }};
                }
                return maxQty;
            }

            document.querySelector('#detail-cart-plus')?.addEventListener('click', () => {
                let val = parseInt(quantityInput.value) || 1;
                let maxQty = getMaxQty();
                if (val >= maxQty) {
                    if (window.toastr) toastr.error('Chỉ còn ' + maxQty + ' sản phẩm trong kho!');
                    quantityInput.value = maxQty;
                } else {
                    quantityInput.value = val + 1;
                }
                updateTotal();
            });

            quantityInput.addEventListener('input', function() {
                let val = parseInt(quantityInput.value) || 1;
                let maxQty = getMaxQty();
                if (val > maxQty) {
                    if (window.toastr) toastr.error('Chỉ còn ' + maxQty + ' sản phẩm trong kho!');
                    val = maxQty;
                }
                if (val < 1) val = 1;
                quantityInput.value = val;
                updateTotal();
            });

            quantityInput.addEventListener('blur', function() {
                let val = parseInt(quantityInput.value) || 1;
                let maxQty = getMaxQty();
                if (val > maxQty) {
                    if (window.toastr) toastr.error('Chỉ còn ' + maxQty + ' sản phẩm trong kho!');
                    quantityInput.value = maxQty;
                }
                updateTotal();
            });

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
        });

        document.addEventListener('DOMContentLoaded', function() {
            const addToCartForm = document.querySelector('#detail-add-to-cart-form');
            const addToCartBtn = addToCartForm ? addToCartForm.querySelector('button[type="submit"]') : null;
            const hasVariants = variants.length > 0;
            const productId = {{ $product->id }};

            function checkAndToggleAddToCartBtn() {
                if (!addToCartBtn) return;
                const qtyInput = document.getElementById('quantity');
                const minusBtn = document.getElementById('detail-cart-minus');
                const plusBtn = document.getElementById('detail-cart-plus');
                // Định nghĩa hàm đồng bộ UI khi đã có trong giỏ
                function setInCartUI() {
                    addToCartBtn.disabled = true;
                    addToCartBtn.textContent = 'Đã có trong giỏ hàng';
                    addToCartBtn.classList.add('in-cart');
                    if (qtyInput) qtyInput.disabled = true;
                    if (minusBtn) minusBtn.classList.add('disabled');
                    if (plusBtn) plusBtn.classList.add('disabled');
                    if (window.toastr && !addToCartBtn.classList.contains('already-toast')) {
                        toastr.error('Sản phẩm này đã có trong giỏ hàng!');
                        addToCartBtn.classList.add('already-toast');
                    }
                }
                function setNormalUI() {
                    addToCartBtn.disabled = false;
                    addToCartBtn.textContent = 'Thêm vào giỏ hàng';
                    addToCartBtn.classList.remove('in-cart', 'already-toast');
                    if (qtyInput) qtyInput.disabled = false;
                    if (minusBtn) minusBtn.classList.remove('disabled');
                    if (plusBtn) plusBtn.classList.remove('disabled');
                }
                if (hasVariants) {
                    const variantId = document.getElementById('product_variant_id').value;
                    const variantIdStr = String(variantId);
                    const cartVariantIdsStr = cartVariantIds.map(String);
                    if (variantId && cartVariantIdsStr.includes(variantIdStr)) {
                        setInCartUI();
                    } else {
                        setNormalUI();
                    }
                } else {
                    if (cartProductIds.includes(Number(productId))) {
                        setInCartUI();
                    } else {
                        setNormalUI();
                    }
                }
            }

            checkAndToggleAddToCartBtn();

            if (hasVariants) {
                document.querySelectorAll('.tp-color-variation-btn, .tp-size-variation-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        setTimeout(checkAndToggleAddToCartBtn, 0);
                    });
                });
            }

            if (addToCartForm) {
                addToCartForm.addEventListener('submit', function(e) {
                    if (hasVariants) {
                        const variantId = document.getElementById('product_variant_id').value;
                        const variantIdStr = String(variantId);
                        const cartVariantIdsStr = cartVariantIds.filter(Boolean).map(String);
                        if (!variantId) {
                            if (window.toastr) toastr.error('Vui lòng chọn đầy đủ màu và kích cỡ!');
                            return;
                        }
                        if (cartVariantIdsStr.includes(variantIdStr)) {
                            e.preventDefault();
                            if (window.toastr) toastr.error('Sản phẩm này đã có trong giỏ hàng!');
                            addToCartBtn.disabled = true;
                            addToCartBtn.textContent = 'Đã có trong giỏ hàng';
                            return false;
                        }
                    } else {
                        if (cartProductIds.includes(Number(productId))) {
                            e.preventDefault();
                            if (window.toastr) toastr.error('Sản phẩm này đã có trong giỏ hàng!');
                            addToCartBtn.disabled = true;
                            addToCartBtn.textContent = 'Đã có trong giỏ hàng';
                            return false;
                        }
                    }
                    e.preventDefault();
                    e.stopImmediatePropagation();

                    if (hasVariants) {
                        const variantId = document.getElementById('product_variant_id').value;
                        if (!variantId) {
                            if (window.toastr) toastr.error('Vui lòng chọn đầy đủ màu và kích cỡ!');
                            return;
                        }
                    }

                    const formData = new FormData(addToCartForm);
                    const qtyInput = document.getElementById('quantity');
                    if (qtyInput) formData.set('quantity', qtyInput.value);

                    fetch(addToCartForm.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    })
                    .then(res => {
                        if (!res.ok) return res.text().then(text => { throw new Error(text || 'Lỗi không xác định từ server') });
                        return res.json();
                    })
                    .then(data => {
                        if (data.success) {
                            if (window.toastr) toastr.success(data.message || 'Đã thêm sản phẩm vào giỏ hàng!');
                            document.dispatchEvent(new CustomEvent('cart:updated'));
                            // Cập nhật biến và disable nút
                            if (hasVariants) {
                                const variantId = document.getElementById('product_variant_id').value;
                                cartVariantIds.push(String(variantId));
                            } else {
                                cartProductIds.push(Number(productId));
                            }
                            checkAndToggleAddToCartBtn();
                        } else {
                            if (window.toastr) toastr.error(data.message || 'Có lỗi xảy ra!');
                        }
                    })
                    .catch(error => {
                        console.error('Lỗi khi thêm vào giỏ hàng:', error);
                        if (window.toastr) toastr.error('Không thể thêm sản phẩm. Vui lòng thử lại sau.');
                    });
                });
            }
        });

        // Kiểm tra số lượng trước khi gửi request (không đổi giao diện input)
        const addToCartForm = document.querySelector('#detail-add-to-cart-form');
        if (addToCartForm) {
            addToCartForm.addEventListener('submit', function(e) {
                const qty = parseInt(document.getElementById('quantity').value) || 1;
                let maxQty = 9999;
                if (variants.length > 0) {
                    const variantId = document.getElementById('product_variant_id').value;
                    const variant = variants.find(v => v.id == variantId);
                    if (variant) maxQty = variant.stock;
                } else {
                    maxQty = {{ $product->stock ?? 9999 }};
                }
                if (qty > maxQty) {
                    e.preventDefault();
                    if (window.toastr) toastr.error('Chỉ còn ' + maxQty + ' sản phẩm trong kho!');
                    else alert('Chỉ còn ' + maxQty + ' sản phẩm trong kho!');
                    document.getElementById('quantity').value = maxQty;
                    return false;
                }
            });
        }

        // Lắng nghe sự kiện cart:item-removed để đồng bộ lại trạng thái nút khi xóa sản phẩm khỏi mini-cart (không dùng API)
        document.addEventListener('cart:item-removed', function(e) {
            const detail = e.detail || {};
            if (typeof detail.product_id !== 'undefined') {
                const idx = cartProductIds.indexOf(Number(detail.product_id));
                if (idx !== -1) cartProductIds.splice(idx, 1);
            }
            if (typeof detail.variant_id !== 'undefined' && detail.variant_id) {
                const idx = cartVariantIds.map(String).indexOf(String(detail.variant_id));
                if (idx !== -1) cartVariantIds.splice(idx, 1);
            }
            checkAndToggleAddToCartBtn();
        });
    </script>

    <script src="https://unpkg.com/imagesloaded@4/imagesloaded.pkgd.min.js"></script>

@endsection
