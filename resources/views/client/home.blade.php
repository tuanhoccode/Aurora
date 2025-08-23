<!doctype html>
<html class="no-js" lang="zxx">


<!-- Mirrored from html.storebuild.shop/shofy-prv/shofy/index-2.html by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 18 May 2025 07:19:23 GMT -->


<head>
   <meta charset="utf-8">
   <meta http-equiv="x-ua-compatible" content="ie=edge">
   <title>@yield('title', 'Aurora') </title>
   <meta name="description" content="">
   <meta name="viewport" content="width=device-width, initial-scale=1">

   <!-- CSRF Token -->
   <meta name="csrf-token" content="{{ csrf_token() }}">

   <!-- Place favicon.ico in the root directory -->
   <link rel="shortcut icon" type="image/x-icon" href="{{asset('assets2/img/logo/favicon.png')}}">

   <!-- CSS here -->
   <link rel="stylesheet" href="{{asset('assets2/css/bootstrap.css')}}">
   <link rel="stylesheet" href="{{asset('assets2/css/animate.css')}}">
   <link rel="stylesheet" href="{{asset('assets2/css/swiper-bundle.css')}}">
   <link rel="stylesheet" href="{{asset('assets2/css/slick.css')}}">
   <link rel="stylesheet" href="{{asset('assets2/css/magnific-popup.css')}}">
   <link rel="stylesheet" href="{{asset('assets2/css/font-awesome-pro.css')}}">
   <link rel="stylesheet" href="{{asset('assets2/css/flaticon_shofy.css')}}">
   <link rel="stylesheet" href="{{asset('assets2/css/spacing.css')}}">
   <link rel="stylesheet" href="{{asset('assets2/css/main.css')}}">

   <!-- Toastr CSS -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

   <!-- jQuery + Toastr JS (trước </body>) -->
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

   <style>
      .product-badge-outofstock {
         position: absolute;
         top: 10px;
         left: 10px;
         background: #d90429;
         color: #fff;
         padding: 4px 12px;
         border-radius: 4px;
         font-size: 0.95rem;
         font-weight: bold;
         z-index: 20;
         box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
      }

      .product-out-of-stock-overlay {
         position: absolute;
         top: 0;
         left: 0;
         right: 0;
         bottom: 0;
         background: rgba(255, 255, 255, 0.8);
         z-index: 10;
         display: flex;
         align-items: center;
         justify-content: center;
         font-weight: bold;
         color: #d90429;
         font-size: 1.1rem;
         text-align: center;
         border-radius: 8px;
         gap: 8px;
      }

      .tp-product-thumb-2.out-of-stock img {
         filter: grayscale(1) brightness(0.85);
         opacity: 0.7;
      }

      .tp-product-thumb-2.out-of-stock {
         pointer-events: none;
      }

      .tp-product-thumb-2.out-of-stock .tp-product-action-2 {
         display: none;
      }

      .tp-product-thumb-2 img {
         max-width: 100%;
         height: auto;
         display: block;
         margin: 0 auto;
      }
      .tp-banner-swiper {
         width: 100%;
         overflow: hidden;
      }

      /* Banner styling để đảm bảo kích thước đồng đều */
      .tp-banner-item-2 {
         height: 300px !important;
         overflow: hidden;
      }

      /* Đảm bảo ảnh banner hiển thị đúng trong slider */
      .tp-slider-thumb-2 img {
         max-width: 100%;
         height: auto;
         object-fit: cover;
         border-radius: 8px;
      }

      /* Responsive cho ảnh banner */
      @media (max-width: 768px) {
         .tp-slider-thumb-2 img {
            max-height: 200px;
         }
      }
         border-radius: 8px;
      }

      .tp-banner-item-2 .custom-banner-flex {
         height: 100%;
         display: flex;
         align-items: center;
         justify-content: space-between;
         padding: 30px;
      }

      .tp-banner-item-2 .custom-banner-text {
         flex: 1;
         max-width: 50%;
         z-index: 2;
      }

      .tp-banner-item-2 .custom-banner-img {
         flex: 1;
         max-width: 50%;
         height: 100%;
         display: flex;
         align-items: center;
         justify-content: center;
         position: relative;
      }

      .tp-banner-item-2 .custom-banner-img img {
          width: 100%;
          height: 100%;
          object-fit: cover;
          border-radius: 8px;
          max-width: 100%;
          max-height: 100%;
      }

      /* Đảm bảo slider banner có cùng kích thước */
      .tp-banner-swiper .swiper-slide {
         height: 300px;
      }

      .tp-banner-swiper .tp-banner-item-2 {
          height: 100% !important;
          margin-bottom: 0 !important;
       }

       /* Category specific styling */
       .tp-category-area .tp-banner-item-2 {
          transition: transform 0.3s ease;
       }

       .tp-category-area .tp-banner-item-2:hover {
          transform: translateY(-5px);
       }

       .tp-category-area .tp-banner-title-2 a {
          color: #333;
          text-decoration: none;
          transition: color 0.3s ease;
       }

       .tp-category-area .tp-banner-title-2 a:hover {
          color: #ff6b6b;
       }

       /* Grid layout banner styling */
       .tp-banner-area .row .tp-banner-item-2,
       .tp-category-area .row .tp-banner-item-2 {
          height: 300px !important;
          margin-bottom: 20px !important;
      }

       .tp-banner-area .row .tp-banner-item-2 .custom-banner-flex,
       .tp-category-area .row .tp-banner-item-2 .custom-banner-flex {
          height: 100%;
      }

       .tp-banner-area .row .tp-banner-item-2 .custom-banner-img img,
       .tp-category-area .row .tp-banner-item-2 .custom-banner-img img {
          object-fit: cover;
          width: 100%;
          height: 100%;
      }

      /* Responsive cho mobile */
      @media (max-width: 768px) {
         .tp-banner-item-2 {
            height: 200px !important;
         }
         
         .tp-banner-item-2 .custom-banner-flex {
            flex-direction: column;
            text-align: center;
            padding: 20px;
         }
         
         .tp-banner-item-2 .custom-banner-text,
         .tp-banner-item-2 .custom-banner-img {
            max-width: 100%;
         }
         
         .tp-banner-item-2 .custom-banner-img {
            height: 60%;
         }
         
         .tp-banner-swiper .swiper-slide {
            height: 200px;
         }
         
         .tp-banner-area .row .tp-banner-item-2,
         .tp-category-area .row .tp-banner-item-2 {
            height: 200px !important;
         }
      }
   </style>

</head>


<body>

   <!-- header area start -->
   @include('client.layouts.partials.header')
   <!-- header area end -->

   @yield('content')

   <main>

      <!-- slider area start -->
      <section class="tp-slider-area p-relative z-index-1">
         <div class="tp-slider-active-2 swiper-container">
            <div class="swiper-wrapper">
               @forelse($banners as $banner)
               {{-- Debug: Hiển thị thông tin banner --}}
               @if(config('app.debug'))
               <!-- Debug: Banner ID: {{ $banner->id }}, Title: {{ $banner->title }}, Active: {{ $banner->is_active ? 'Yes' : 'No' }}, Sort Order: {{ $banner->sort_order }}, Created: {{ $banner->created_at }}, Image: {{ $banner->image_url }}, Raw Image: {{ $banner->image }}, Count: {{ $loop->iteration }}, Total: {{ $banners->count() }}, Loop Index: {{ $loop->index }} -->
               @endif
               <div class="tp-slider-item-2 tp-slider-height-2 p-relative swiper-slide grey-bg-5 d-flex align-items-end">
                  <div class="tp-slider-2-shape">
                     <img class="tp-slider-2-shape-1" src="{{asset('assets2/img/slider/2/shape/shape-1.png')}}" alt="">
                  </div>
                  <div class="container">
                     <div class="row align-items-center">
                        <div class="col-xl-6 col-lg-6 col-md-6">
                           <div class="tp-slider-content-2">
                              @if($banner->subtitle)
                                 <span>{{ $banner->subtitle }}</span>
                              @endif
                              <h3 class="tp-slider-title-2">{{ $banner->title }}</h3>
                              @if($banner->link)
                                 <div class="tp-slider-btn-2">
                                    <a href="{{ $banner->link }}" class="tp-btn tp-btn-border">Xem chi tiết</a>
                                 </div>
                              @endif
                           </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6">
                           <div class="tp-slider-thumb-2-wrapper p-relative">
                              <div class="tp-slider-thumb-2-shape">
                                 <img class="tp-slider-thumb-2-shape-1"
                                    src="{{asset('assets2/img/slider/2/shape/shape-2.png')}}" alt="">
                                 <img class="tp-slider-thumb-2-shape-2"
                                    src="{{asset('assets2/img/slider/2/shape/shape-3.png')}}" alt="">
                              </div>
                              <div class="tp-slider-thumb-2 text-end">
                                 <span class="tp-slider-thumb-2-gradient"></span>
                                 <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}">
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               @empty
               <!-- Fallback banner nếu không có banner nào -->
               <div class="tp-slider-item-2 tp-slider-height-2 p-relative swiper-slide grey-bg-5 d-flex align-items-end">
                  <div class="tp-slider-2-shape">
                     <img class="tp-slider-2-shape-1" src="{{asset('assets2/img/slider/2/shape/shape-1.png')}}" alt="">
                  </div>
                  <div class="container">
                     <div class="row align-items-center">
                        <div class="col-xl-6 col-lg-6 col-md-6">
                           <div class="tp-slider-content-2">
                              <span>Chào mừng đến với Aurora</span>
                              <h3 class="tp-slider-title-2">Khám phá bộ sưu tập mới</h3>
                              <div class="tp-slider-btn-2">
                                 <a href="{{ route('shop') }}" class="tp-btn tp-btn-border">Xem bộ sưu tập</a>
                              </div>
                           </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6">
                           <div class="tp-slider-thumb-2-wrapper p-relative">
                              <div class="tp-slider-thumb-2-shape">
                                 <img class="tp-slider-thumb-2-shape-1"
                                    src="{{asset('assets2/img/slider/2/shape/shape-2.png')}}" alt="">
                                 <img class="tp-slider-thumb-2-shape-2"
                                    src="{{asset('assets2/img/slider/2/shape/shape-3.png')}}" alt="">
                              </div>
                              <div class="tp-slider-thumb-2 text-end">
                                 <span class="tp-slider-thumb-2-gradient"></span>
                                 <img src="{{asset('assets2/img/slider/2/slider-1.png')}}" alt="">
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               @endforelse
            </div>
            <div class="tp-swiper-dot tp-slider-2-dot"></div>
         </div>
      </section>
      <!-- slider area end -->

      <!-- category area start -->
      @php use Illuminate\Support\Str; @endphp
      @if($categories->count() > 0)
      <section class="tp-category-area mt-20">
         <div class="container-fluid tp-gx-40">
            @php $isSlider = $categories->count() > 3; @endphp
            @if($isSlider)
            <div class="swiper tp-banner-swiper">
               <div class="swiper-wrapper">
                 @foreach($categories->take(6) as $category)
                <div class="swiper-slide">
                  <div class="tp-banner-item-2 custom-banner-flex p-relative z-index-1 grey-bg-2 mb-20 fix">
                   <div class="custom-banner-text">
                     <h3 class="tp-banner-title-2">
                       <a href="{{ route('client.categories.show', $category->id) }}">{{ $category->name }}</a>
                     </h3>
                     <div class="tp-banner-btn-2">
                       <a href="{{ route('client.categories.show', $category->id) }}"
                        class="tp-btn tp-btn-border tp-btn-border-sm">Xem danh mục
                        <svg width="17" height="15" viewBox="0 0 17 15" fill="none"
                          xmlns="http://www.w3.org/2000/svg">
                          <path d="M16 7.49988L1 7.49988" stroke="currentColor" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round" />
                          <path d="M9.9502 1.47554L16.0002 7.49954L9.9502 13.5245" stroke="currentColor"
                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                       </a>
                     </div>
                   </div>
                   <div class="custom-banner-img">
                     @if($category->icon)
                     @if(Str::startsWith($category->icon, ['http://', 'https://']))
                   <img src="{{ $category->icon }}" alt="{{ $category->name }}">
                  @else
                   <img src="{{ asset('storage/' . $category->icon) }}" alt="{{ $category->name }}">
                  @endif
                  @else
                   <img src="{{ asset('assets2/img/product/product-1.jpg') }}"
                     alt="{{ $category->name }}">
                  @endif
                   </div>
                  </div>
                </div>
              @endforeach
               </div>
               <div class="swiper-pagination"></div>
            </div>
         @else
            <div class="row tp-gx-20">
               @foreach($categories->take(3) as $category)
               <div class="col-xxl-4 col-lg-6">
                <div class="tp-banner-item-2 custom-banner-flex p-relative z-index-1 grey-bg-2 mb-20 fix">
                  <div class="custom-banner-text">
                   <h3 class="tp-banner-title-2">
                     <a href="{{ route('client.categories.show', $category->id) }}">{{ $category->name }}</a>
                   </h3>
                   <div class="tp-banner-btn-2">
                     <a href="{{ route('client.categories.show', $category->id) }}"
                       class="tp-btn tp-btn-border tp-btn-border-sm">Xem danh mục
                       <svg width="17" height="15" viewBox="0 0 17 15" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M16 7.49988L1 7.49988" stroke="currentColor" stroke-width="1.5"
                          stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M9.9502 1.47554L16.0002 7.49954L9.9502 13.5245" stroke="currentColor"
                          stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                       </svg>
                     </a>
                   </div>
                  </div>
                  <div class="custom-banner-img">
                   @if($category->icon)
                     @if(Str::startsWith($category->icon, ['http://', 'https://']))
                    <img src="{{ $category->icon }}" alt="{{ $category->name }}">
                   @else
                    <img src="{{ asset('storage/' . $category->icon) }}" alt="{{ $category->name }}">
                   @endif
                @else
                  <img src="{{ asset('assets2/img/product/product-1.jpg') }}"
                   alt="{{ $category->name }}">
                @endif
                  </div>
                </div>
               </div>
            @endforeach
            </div>
         @endif
         </div>
      </section>
      @endif
      <br>
      <!-- product area start -->
      <section class="tp-product-area pb-90">
         <div class="container">
            <div class="row">
               <div class="col-xl-12">
                  <div class="tp-section-title-wrapper-2 text-center mb-35">
                     <span class="tp-section-title-pre-2">
                        Tất Cả Sản Phẩm
                        <svg width="82" height="22" viewBox="0 0 82 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                           <path d="M81 14.5798C0.890564 -8.05914 -5.81154 0.0503902 5.00322 21" stroke="currentColor"
                              stroke-opacity="0.3" stroke-width="2" stroke-miterlimit="3.8637" stroke-linecap="round" />
                        </svg>
                     </span>
                     <h3 class="tp-section-title-2">Sản Phẩm Mới Nhất</h3>
                  </div>
               </div>
            </div>
            <div class="row justify-content-center">
               @foreach ($products as $product)
               @php
               $isOutOfStock = $product->type === 'variant'
                ? ($product->variants->isEmpty())
                : (($product->stock ?? 0) <= 0);
            @endphp
               @if(!$isOutOfStock)
               <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="tp-product-item-2 mb-40">
                  <div class="tp-product-thumb-2 p-relative z-index-1 fix w-img">
                   <a href="{{ route('client.product.show', ['slug' => $product->slug]) }}">
                     <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                   </a>
                  </div>
                  <div class="tp-product-content-2 pt-15">
                   <div class="tp-product-tag-2">
                     <a href="#">{{ $product->brand->name ?? 'Không có thương hiệu' }}</a>
                   </div>
                   <h3 class="tp-product-title-2">
                     <a
                       href="{{ route('client.product.show', ['slug' => $product->slug]) }}">{{ $product->name }}</a>
                   </h3>
                   @php
                   $validReviews = $product->reviews
                    ->where('is_active', 1)
                    ->where('review_id', null)
                    ->where('rating', '>', 0);

                   $avg = $validReviews->count() > 0 ? round($validReviews->avg('rating')) : 0;
                  @endphp
                   <div class="tp-product-rating-icon tp-product-rating-icon-2">
                     @if ($validReviews->count() > 0)
                        <div class="review-item pb-3">
                           <div class="text-warning mb-1">
                              @for ($i = 1; $i <= 5; $i++)
                                 @if ($i <= $avg)
                                    ★
                                 @else
                                    ☆
                                 @endif
                              @endfor
                           </div>
                        </div>
                     @else
                        <p>Chưa có đánh giá.</p>
                     @endif
                   </div>

                   <div class="tp-product-price-wrapper-2">
                     <span class="tp-product-price-2 new-price">{{ number_format($product->display_price, 0, ',', '.') }}
                       <span style="color: red;">đ</span></span>
                     @if ($product->display_original_price && $product->display_original_price > $product->display_price)
                   <span
                     class="tp-product-price-2 old-price">{{ number_format($product->display_original_price, 0, ',', '.') }}
                     <span style="color: red;">đ</span></span>
                  @endif
                   </div>
                  </div>
                </div>
               </div>
            @endif
            @endforeach
            </div>
         </div>
      </section>
      <!-- product area end -->
      <!-- best seller area start -->
      <section class="tp-seller-area pb-140">
         <div class="container">
            <div class="row">
               <div class="col-xl-12">
                  <div class="tp-section-title-wrapper-2 mb-50">
                     <span class="tp-section-title-pre-2">
                        <!-- Sản phẩm nổi bật -->
                        <svg width="82" height="22" viewBox="0 0 82 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                           <path d="M81 14.5798C0.890564 -8.05914 -5.81154 0.0503902 5.00322 21" stroke="currentColor"
                              stroke-opacity="0.3" stroke-width="2" stroke-miterlimit="3.8637" stroke-linecap="round" />
                        </svg>
                     </span>
                     <h3 class="tp-section-title-2">Sản Phẩm Nổi Bật</h3>
                  </div>
               </div>
            </div>
            <div class="row">
               @foreach($featuredThisWeek as $prod)
                  @php
                     $validReviews = $prod->reviews
                         ->where('is_active', 1)
                         ->where('review_id', null)
                         ->where('rating', '>', 0);

                     $avg = $validReviews->count() > 0 ? round($validReviews->avg('rating')) : 0;
                  @endphp
               <div class="col-xl-3 col-lg-4 col-sm-6">
                 <div class="tp-product-item-2 mb-40">
                   <div class="tp-product-thumb-2 p-relative z-index-1 fix w-img">
                     <a href="{{ route('client.product.show', $prod->slug) }}">
                        <img src="{{ asset('storage/' . $prod->thumbnail) }}" alt="{{ $prod->name }}">
                     </a>
                   </div>
                   <div class="tp-product-content-2 pt-15">
                     <h3 class="tp-product-title-2">
                        <a href="{{ route('client.product.show', $prod->slug) }}">
                          {{ Str::limit($prod->name, 30) }}
                        </a>
                     </h3>
                     <div class="tp-product-rating-icon tp-product-rating-icon-2">
                        @if ($validReviews->count() > 0)
                            <div class="review-item">
                                <div class="text-warning">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= $avg)
                                            ★
                                        @else
                                            ☆
                                        @endif
                                    @endfor
                                </div>
                            </div>
                        @else
                            <p>Chưa có đánh giá.</p>
                        @endif
                     </div>
                     <div class="tp-product-price-wrapper-2">
                        <span class="tp-product-price-2 new-price">
                          {{ number_format($prod->display_price, 0, ',', '.') }}
                          <span style="color: red;">đ</span></span>
                        </span>
                        @if($prod->display_original_price && $prod->display_original_price > $prod->display_price)
                     <span class="tp-product-price-2 old-price">
                       {{ number_format($prod->display_original_price, 0, ',', '.') }}
                       <span style="color: red;">đ</span></span>
                     </span>
                   @endif
                     </div>
                   </div>
                 </div>
               </div>
            @endforeach
            </div>
            <div class="row">
               <div class="col-xl-12">
                  <div class="tp-seller-more text-center mt-10">
                     <a href="{{ route('shop') }}" class="tp-btn tp-btn-border tp-btn-border-sm">Xem tất cả sản phẩm</a>
                  </div>
               </div>
            </div>
         </div>
      </section>
      <!-- best seller area end -->
      <!-- testimonial area start -->
      <section class="tp-testimonial-area grey-bg-7 pt-130 pb-135">
         <div class="container">
            <div class="row">
               <div class="col-xl-12">
                  <div class="tp-section-title-wrapper-2 mb-80">
                     <span class="tp-section-title-pre-2"  style="font-size: 28px; font-weight: 500;">
                        Những Đánh Giá Tích Cực Từ Khách Hàng
                        <svg width="82" height="22" viewBox="0 0 82 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                           <path d="M81 14.5798C0.890564 -8.05914 -5.81154 0.0503902 5.00322 21" stroke="currentColor"
                              stroke-opacity="0.3" stroke-width="2" stroke-miterlimit="3.8637" stroke-linecap="round" />
                        </svg>
                     </span>
                     <!-- <h3 class="tp-section-title-2">Những đánh giá tích cực từ khách hàng</h3> -->
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-xl-12">
                  <div class="tp-testimonial-slider p-relative z-index-1">
                     <div class="tp-testimonial-shape">
                        <span class="tp-testimonial-shape-gradient"></span>
                     </div>
                     <div class="tp-testimonial-slider-active swiper-container">
                        <div class="swiper-wrapper">
                           @forelse($topReviews as $review)
                              <div class="tp-testimonial-item text-center mb-20 swiper-slide">
                                <div class="tp-testimonial-rating">
                                  @for($i = 0; $i < 5; $i++)
                              <span><i class="fa-solid fa-star text-warning"></i></span>
                           @endfor
                        </div>
                        <div class="tp-testimonial-content">
                          <p>"{{$review->review_text}}"</p>
                        </div>
                        <div class="tp-testimonial-user-wrapper d-flex align-items-center justify-content-center">
                          <div class="tp-testimonial-user d-flex align-items-center">
                            <div class="tp-testimonial-avater mr-10">
                               <img src="{{ $review->user->avatar
                               ? asset('storage/' . $review->user->avatar)
                               : asset('assets2/img/users/avatars.png') }}" alt="avatar" width="50">
                            </div>
                            <div class="tp-testimonial-user-info tp-testimonial-user-translate">
                               <h3 class="tp-testimonial-user-title">{{$review->user->fullname ?? 'Ẩn Danh'}}
                               </h3>
                               <span class="tp-testimonial-designation">Khách hàng</span>
                            </div>
                          </div>
                        </div>
                     </div>
                     @empty
                        <div class="d-flex justify-content-center align-items-center w-100" style="height: 150px;">
                           <p class="mb-0 text-center">Chưa có đánh giá 5 sao</p>
                        </div>
                     @endforelse

                        </div>
                     </div>
                     <div class="tp-testimonial-arrow d-none d-md-block">
                        <button class="tp-testimonial-slider-button-prev">
                           <svg width="17" height="14" viewBox="0 0 17 14" fill="none"
                              xmlns="http://www.w3.org/2000/svg">
                              <path d="M1.061 6.99959L16 6.99959" stroke="currentColor" stroke-width="1.5"
                                 stroke-linecap="round" stroke-linejoin="round" />
                              <path d="M7.08618 1L1.06079 6.9995L7.08618 13" stroke="currentColor" stroke-width="1.5"
                                 stroke-linecap="round" stroke-linejoin="round" />
                           </svg>
                        </button>
                        <button class="tp-testimonial-slider-button-next">
                           <svg width="17" height="14" viewBox="0 0 17 14" fill="none"
                              xmlns="http://www.w3.org/2000/svg">
                              <path d="M15.939 6.99959L1 6.99959" stroke="currentColor" stroke-width="1.5"
                                 stroke-linecap="round" stroke-linejoin="round" />
                              <path d="M9.91382 1L15.9392 6.9995L9.91382 13" stroke="currentColor" stroke-width="1.5"
                                 stroke-linecap="round" stroke-linejoin="round" />
                           </svg>
                        </button>
                     </div>
                     <div
                        class="tp-testimonial-slider-dot tp-swiper-dot text-center mt-30 tp-swiper-dot-style-darkRed d-md-none">
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </section>
      <!-- testimonial area end -->
      <br><br><br>
      <!-- feature area start -->
        <section class="tp-feature-area tp-feature-border-2 pb-80">
            <div class="container">
                <div class="tp-feature-inner-2">
                    <div class="row align-items-center">
                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                            <div class="tp-feature-item-2 d-flex align-items-start">
                                <div class="tp-feature-icon-2 mr-10">
                                    <span>
                                        <svg width="33" height="27" viewBox="0 0 33 27" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path d="M10.7222 1H31.5555V19.0556H10.7222V1Z" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M10.7222 7.94446H5.16667L1.00001 12.1111V19.0556H10.7222V7.94446Z"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path
                                                d="M25.3055 26C23.3879 26 21.8333 24.4454 21.8333 22.5278C21.8333 20.6101 23.3879 19.0555 25.3055 19.0555C27.2232 19.0555 28.7778 20.6101 28.7778 22.5278C28.7778 24.4454 27.2232 26 25.3055 26Z"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path
                                                d="M7.25001 26C5.33235 26 3.77778 24.4454 3.77778 22.5278C3.77778 20.6101 5.33235 19.0555 7.25001 19.0555C9.16766 19.0555 10.7222 20.6101 10.7222 22.5278C10.7222 24.4454 9.16766 26 7.25001 26Z"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="tp-feature-content-2">
                                    <h3 class="tp-feature-title-2">Giao hàng miễn phí</h3>
                                    <p>Đơn hàng từ tất cả các mặt hàng</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                            <div class="tp-feature-item-2 d-flex align-items-start">
                                <div class="tp-feature-icon-2 mr-10">
                                    <span>
                                        <svg width="21" height="35" viewBox="0 0 21 35" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path d="M10.3636 1V34" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path
                                                d="M17.8636 7H6.61365C5.22126 7 3.8859 7.55312 2.90134 8.53769C1.91677 9.52226 1.36365 10.8576 1.36365 12.25C1.36365 13.6424 1.91677 14.9777 2.90134 15.9623C3.8859 16.9469 5.22126 17.5 6.61365 17.5H14.1136C15.506 17.5 16.8414 18.0531 17.826 19.0377C18.8105 20.0223 19.3636 21.3576 19.3636 22.75C19.3636 24.1424 18.8105 25.4777 17.826 26.4623C16.8414 27.4469 15.506 28 14.1136 28H1.36365"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="tp-feature-content-2">
                                    <h3 class="tp-feature-title-2">Trả lại & Hoàn tiền</h3>
                                    <p>Đảm bảo hoàn lại tiền</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                            <div class="tp-feature-item-2 d-flex align-items-start">
                                <div class="tp-feature-icon-2 mr-10">
                                    <span>
                                        <svg width="31" height="30" viewBox="0 0 31 30" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <mask id="mask0_1211_583" style="mask-type:alpha"
                                                maskUnits="userSpaceOnUse" x="0" y="0" width="31" height="30">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M0 0H30.0024V29.9998H0V0Z" fill="white" />
                                            </mask>
                                            <g mask="url(#mask0_1211_583)">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M13.4168 27.1116C14.3017 27.9756 15.7266 27.9651 16.6056 27.0816L17.6885 26.0017C18.5285 25.1632 19.6894 24.6848 20.8728 24.6848H22.4178C23.6687 24.6848 24.6856 23.6678 24.6856 22.4184V20.875C24.6856 19.6736 25.1506 18.5441 25.9995 17.6937L27.0795 16.6122C27.519 16.1713 27.7544 15.5998 27.7529 14.9938C27.7514 14.3894 27.513 13.8209 27.0825 13.3919L26.001 12.309C25.1506 11.4525 24.6856 10.3246 24.6856 9.12318V7.58277C24.6856 6.33184 23.6687 5.3149 22.4178 5.3149H20.8758C19.6744 5.3149 18.545 4.84842 17.6945 4.00397L16.6116 2.91954C15.7101 2.02709 14.2717 2.03159 13.3913 2.91804L12.3128 3.99947C11.4519 4.84992 10.3225 5.3149 9.12553 5.3149H7.58212C6.33269 5.3164 5.31575 6.33334 5.31575 7.58277V9.12018C5.31575 10.3216 4.84927 11.451 4.00332 12.303L2.93839 13.3694C2.92789 13.3814 2.91739 13.3904 2.90689 13.4009C2.02644 14.2874 2.03094 15.7258 2.91739 16.6062L4.00032 17.6892C4.84927 18.5411 5.31575 19.6706 5.31575 20.872V22.4184C5.31575 23.6678 6.33119 24.6848 7.58212 24.6848H9.12253C10.3255 24.6863 11.4549 25.1527 12.3053 26.0002L13.3868 27.0786C13.3958 27.0891 13.4063 27.0996 13.4168 27.1116ZM14.9972 30.0002C13.8468 30.0002 12.6963 29.5652 11.8159 28.6923C11.8039 28.6803 11.7919 28.6683 11.7799 28.6548L10.715 27.5914C10.2905 27.1699 9.72352 26.9359 9.12055 26.9344H7.58164C5.09029 26.9344 3.06541 24.908 3.06541 22.4182V20.8717C3.06541 20.2688 2.82992 19.7033 2.40694 19.2773L1.32851 18.2004C-0.423392 16.4575 -0.444391 13.6197 1.27601 11.8498C1.28951 11.8363 1.30301 11.8228 1.31651 11.8093L2.40844 10.7143C2.82992 10.2899 3.06541 9.72139 3.06541 9.11993V7.58252C3.06541 5.09266 5.09029 3.06628 7.58014 3.06478H9.12505C9.72652 3.06478 10.2935 2.82929 10.724 2.40482L11.7964 1.32938C13.5498 -0.436017 16.4161 -0.445016 18.1845 1.31288L19.281 2.40932C19.7054 2.83079 20.2724 3.06478 20.8754 3.06478H22.4173C24.9086 3.06478 26.935 5.09116 26.935 7.58252V9.12293C26.935 9.72439 27.169 10.2929 27.5935 10.7203L28.6704 11.7988C29.5239 12.6462 29.9978 13.7787 30.0023 14.9861C30.0068 16.1935 29.5404 17.329 28.6899 18.1854L27.5905 19.2818C27.169 19.7063 26.935 20.2718 26.935 20.8747V22.4182C26.935 24.908 24.9086 26.9344 22.4188 26.9344H20.8724C20.2784 26.9344 19.6979 27.1744 19.2765 27.5929L18.1995 28.6698C17.3191 29.5562 16.1581 30.0002 14.9972 30.0002Z"
                                                    fill="currentColor" />
                                            </g>
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M11.145 19.9811C10.857 19.9811 10.569 19.8716 10.3501 19.6511C9.91058 19.2116 9.91058 18.5006 10.3501 18.0612L18.0596 10.3501C18.4991 9.91064 19.2115 9.91064 19.651 10.3501C20.0905 10.7896 20.0905 11.502 19.651 11.9415L11.94 19.6511C11.721 19.8716 11.433 19.9811 11.145 19.9811Z"
                                                fill="currentColor" />
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M18.7544 20.2476C17.925 20.2476 17.247 19.5772 17.247 18.7477C17.247 17.9183 17.9115 17.2478 18.7409 17.2478H18.7544C19.5839 17.2478 20.2543 17.9183 20.2543 18.7477C20.2543 19.5772 19.5839 20.2476 18.7544 20.2476Z"
                                                fill="currentColor" />
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M11.2548 12.748C10.4254 12.748 9.74744 12.0775 9.74744 11.2481C9.74744 10.4186 10.4119 9.74817 11.2413 9.74817H11.2548C12.0843 9.74817 12.7548 10.4186 12.7548 11.2481C12.7548 12.0775 12.0843 12.748 11.2548 12.748Z"
                                                fill="currentColor" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="tp-feature-content-2">
                                    <h3 class="tp-feature-title-2">Giảm giá cho thành viên</h3>
                                    <p>Một đơn hàng trên 150.000 <span style="color: red;">đ</span></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                            <div class="tp-feature-item-2 d-flex align-items-start">
                                <div class="tp-feature-icon-2 mr-10">
                                    <span>
                                        <svg width="31" height="30" viewBox="0 0 31 30" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M1.5 24.3333V15C1.5 11.287 2.975 7.72602 5.60051 5.10051C8.22602 2.475 11.787 1 15.5 1C19.213 1 22.774 2.475 25.3995 5.10051C28.025 7.72602 29.5 11.287 29.5 15V24.3333"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path
                                                d="M29.5 25.8889C29.5 26.714 29.1722 27.5053 28.5888 28.0888C28.0053 28.6722 27.214 29 26.3889 29H24.8333C24.0082 29 23.2169 28.6722 22.6335 28.0888C22.05 27.5053 21.7222 26.714 21.7222 25.8889V21.2222C21.7222 20.3971 22.05 19.6058 22.6335 19.0223C23.2169 18.4389 24.0082 18.1111 24.8333 18.1111H29.5V25.8889ZM1.5 25.8889C1.5 26.714 1.82778 27.5053 2.41122 28.0888C2.99467 28.6722 3.78599 29 4.61111 29H6.16667C6.99179 29 7.78311 28.6722 8.36656 28.0888C8.95 27.5053 9.27778 26.714 9.27778 25.8889V21.2222C9.27778 20.3971 8.95 19.6058 8.36656 19.0223C7.78311 18.4389 6.99179 18.1111 6.16667 18.1111H1.5V25.8889Z"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="tp-feature-content-2">
                                    <h3 class="tp-feature-title-2">Hỗ trợ 24/7</h3>
                                    <p>Có thắc mắc? Đừng ngần ngại, chúng tôi luôn ở đây để hỗ trợ bạn!</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- feature area end -->
   </main>

   <!-- footer area start -->
   @include('client.layouts.partials.footer')
   <!-- footer area end -->
   @if (session('success'))
      <script>
        toastr.options = {
          "positionClass": "toast-top-right",
          "timeOut": 3000
        };
        toastr.success(@json(session('success')));
      </script>
   @endif

   @if (session('error'))
      <script>
        toastr.options = {
          "positionClass": "toast-top-right",
          "timeOut": 3000
        };
        toastr.error(@json(session('error')));
      </script>
   @endif

   <!-- JS here -->
   <script src="{{asset('assets2/js/vendor/jquery.js')}}"></script>
   <script src="{{asset('assets2/js/vendor/waypoints.js')}}"></script>
   <script src="{{asset('assets2/js/bootstrap-bundle.js')}}"></script>
   <script src="{{asset('assets2/js/meanmenu.js')}}"></script>
   <script src="{{asset('assets2/js/swiper-bundle.js')}}"></script>
   <script src="{{asset('assets2/js/slick.js')}}"></script>
   <script src="{{asset('assets2/js/range-slider.js')}}"></script>
   <script src="{{asset('assets2/js/magnific-popup.js')}}"></script>
   <script src="{{asset('assets2/js/nice-select.js')}}"></script>
   <script src="{{asset('assets2/js/purecounter.js')}}"></script>
   <script src="{{asset('assets2/js/countdown.js')}}"></script>
   <script src="{{asset('assets2/js/wow.js')}}"></script>
   <script src="{{asset('assets2/js/isotope-pkgd.js')}}"></script>
   <script src="{{asset('assets2/js/imagesloaded-pkgd.js')}}"></script>
   <script src="{{asset('assets2/js/ajax-form.js')}}"></script>
   <script src="{{asset('assets2/js/main.js')}}"></script>
</body>

</html>