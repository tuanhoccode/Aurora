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
       top: 10px; left: 10px;
       background: #d90429;
       color: #fff;
       padding: 4px 12px;
       border-radius: 4px;
       font-size: 0.95rem;
       font-weight: bold;
       z-index: 20;
       box-shadow: 0 2px 8px rgba(0,0,0,0.08);
     }
     .product-out-of-stock-overlay {
       position: absolute;
       top: 0; left: 0; right: 0; bottom: 0;
       background: rgba(255,255,255,0.8);
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
               <div
                  class="tp-slider-item-2 tp-slider-height-2 p-relative swiper-slide grey-bg-5 d-flex align-items-end">
                  <div class="tp-slider-2-shape">
                     <img class="tp-slider-2-shape-1" src="{{asset('assets2/img/slider/2/shape/shape-1.png')}}" alt="">
                  </div>
                  <div class="container">
                     <div class="row align-items-center">
                        <div class="col-xl-6 col-lg-6 col-md-6">
                           <div class="tp-slider-content-2">
                              <span>Hàng mới về 2023</span>
                              <h3 class="tp-slider-title-2">Bộ sưu tập thời trang</h3>
                              <div class="tp-slider-btn-2">
                                 <a href="shop.html" class="tp-btn tp-btn-border">Xem bộ sưu tập</a>
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
               <div
                  class="tp-slider-item-2 tp-slider-height-2 p-relative swiper-slide grey-bg-5 d-flex align-items-end">
                  <div class="tp-slider-2-shape">
                     <img class="tp-slider-2-shape-1" src="{{asset('assets2/img/slider/2/shape/shape-1.png')}}" alt="">
                  </div>
                  <div class="container">
                     <div class="row align-items-center">
                        <div class="col-xl-6 col-lg-6 col-md-6">
                           <div class="tp-slider-content-2">
                              <span>Bán chạy nhất 2023</span>
                              <h3 class="tp-slider-title-2">Bộ sưu tập mùa hè</h3>
                              <div class="tp-slider-btn-2">
                                 <a href="shop.html" class="tp-btn tp-btn-border">Xem bộ sưu tập</a>
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
                                 <img src="{{asset('assets2/img/slider/2/slider-2.png')}}" alt="">
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div
                  class="tp-slider-item-2 tp-slider-height-2 p-relative swiper-slide grey-bg-5 d-flex align-items-end">
                  <div class="tp-slider-2-shape">
                     <img class="tp-slider-2-shape-1" src="{{asset('assets2/img/slider/2/shape/shape-1.png')}}" alt="">
                  </div>
                  <div class="container">
                     <div class="row align-items-center">
                        <div class="col-xl-6 col-lg-6 col-md-6">
                           <div class="tp-slider-content-2">
                              <span>Mùa đông đã đến</span>
                              <h3 class="tp-slider-title-2">Thiết kế mới tuyệt vời</h3>
                              <div class="tp-slider-btn-2">
                                 <a href="shop.html" class="tp-btn tp-btn-border">Xem bộ sưu tập</a>
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
                                 <img src="{{asset('assets2/img/slider/2/slider-3.png')}}" alt="">
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="tp-swiper-dot tp-slider-2-dot"></div>
         </div>
      </section>
      <!-- slider area end -->

      <!-- banner area start -->
      @php use Illuminate\Support\Str; @endphp
      @php $isSlider = $categories->count() > 3; @endphp
      <section class="tp-banner-area mt-20">
         <div class="container-fluid tp-gx-40">
            @if($isSlider)
            <div class="swiper tp-banner-swiper">
               <div class="swiper-wrapper">
                  @foreach($categories as $i => $category)
                  <div class="swiper-slide">
                     <div class="tp-banner-item-2 custom-banner-flex p-relative z-index-1 grey-bg-2 mb-20 fix">
                        <div class="custom-banner-text">
                           <h3 class="tp-banner-title-2">
                              <a href="{{ route('client.categories.show', $category->id) }}">{{ $category->name }}</a>
                           </h3>
                           <div class="tp-banner-btn-2">
                              <a href="{{ route('client.categories.show', $category->id) }}" class="tp-btn tp-btn-border tp-btn-border-sm">Xem ngay
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
                               <img src="{{ asset('assets2/img/banner/2/banner-' . ($i+1) . '.jpg') }}" alt="{{ $category->name }}">
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
               @foreach($categories->take(3) as $i => $category)
               <div class="col-xxl-4 col-lg-6">
                  <div class="tp-banner-item-2 custom-banner-flex p-relative z-index-1 grey-bg-2 mb-20 fix">
                     <div class="custom-banner-text">
                        <h3 class="tp-banner-title-2">
                           <a href="{{ route('client.categories.show', $category->id) }}">{{ $category->name }}</a>
                        </h3>
                        <div class="tp-banner-btn-2">
                           <a href="{{ route('client.categories.show', $category->id) }}" class="tp-btn tp-btn-border tp-btn-border-sm">Xem ngay
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
                            <img src="{{ asset('assets2/img/banner/2/banner-' . ($i+1) . '.jpg') }}" alt="{{ $category->name }}">
                        @endif
                     </div>
                  </div>
               </div>
               @endforeach
            </div>
            @endif
         </div>
      </section>

      <!-- product area start -->
      <section class="tp-product-area pb-90">
         <div class="container">
            <div class="row">
               <div class="col-xl-12">
                  <div class="tp-section-title-wrapper-2 text-center mb-35">
                     <span class="tp-section-title-pre-2">
                        Tất cả sản phẩm
                        <svg width="82" height="22" viewBox="0 0 82 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                           <path d="M81 14.5798C0.890564 -8.05914 -5.81154 0.0503902 5.00322 21" stroke="currentColor"
                              stroke-opacity="0.3" stroke-width="2" stroke-miterlimit="3.8637" stroke-linecap="round" />
                        </svg>
                     </span>
                     <h3 class="tp-section-title-2">Sản phẩm được yêu thích</h3>
                  </div>
               </div>
            </div>
            <div class="row justify-content-center">
               @foreach ($products as $product)
                 @php
                   $isOutOfStock = $product->type === 'variant'
                       ? ($product->variants->sum('stock') <= 0)
                       : (($product->stock ?? 0) <= 0);
                 @endphp
                 @if(!$isOutOfStock)
                 <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                   <div class="tp-product-item-2 mb-40">
                     <div class="tp-product-thumb-2 p-relative z-index-1 fix w-img">
                       <a href="{{ route('client.product.show', ['slug' => $product->slug]) }}">
                          <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                       </a>
                       <div class="tp-product-action-2 tp-product-action-blackStyle">
                          <div class="tp-product-action-item-2 d-flex flex-column">
                            <form method="POST" action="{{ route('shopping-cart.add') }}" class="add-to-cart-form">
                              @csrf
                              <input type="hidden" name="product_id" value="{{ $product->id }}">
                              <input type="hidden" name="product_variant_id" value="{{ $product->default_variant_id ?? '' }}">
                              <input type="hidden" name="quantity" value="1">
                              <input type="hidden" name="price" value="{{ $product->price }}">
                              <button type="submit" class="tp-product-action-btn-2 tp-product-add-cart-btn">
                                  <svg width="17" height="17" viewBox="0 0 17 17" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                       d="M3.34706 4.53799L3.85961 10.6239C3.89701 11.0923 4.28036 11.4436 4.74871 11.4436H4.75212H14.0265H14.0282C14.4711 11.4436 14.8493 11.1144 14.9122 10.6774L15.7197 5.11162C15.7384 4.97924 15.7053 4.84687 15.6245 4.73995C15.5446 4.63218 15.4273 4.5626 15.2947 4.54393C15.1171 4.55072 7.74498 4.54054 3.34706 4.53799ZM4.74722 12.7162C3.62777 12.7162 2.68001 11.8438 2.58906 10.728L1.81046 1.4837L0.529505 1.26308C0.181854 1.20198 -0.0501969 0.873587 0.00930333 0.526523C0.0705036 0.17946 0.406255 -0.0462578 0.746256 0.00805037L2.51426 0.313534C2.79901 0.363599 3.01576 0.5995 3.04042 0.888012L3.24017 3.26484C15.3748 3.26993 15.4139 3.27587 15.4726 3.28266C15.946 3.3514 16.3625 3.59833 16.6464 3.97849C16.9303 4.35779 17.0493 4.82535 16.9813 5.29376L16.1747 10.8586C16.0225 11.9177 15.1011 12.7162 14.0301 12.7162H14.0259H4.75402H4.74722Z"
                                       fill="currentColor" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                       d="M12.6629 7.67446H10.3067C9.95394 7.67446 9.66919 7.38934 9.66919 7.03804C9.66919 6.68673 9.95394 6.40161 10.3067 6.40161H12.6629C13.0148 6.40161 13.3004 6.68673 13.3004 7.03804C13.3004 7.38934 13.0148 7.67446 12.6629 7.67446Z"
                                       fill="currentColor" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                       d="M4.38171 15.0212C4.63756 15.0212 4.84411 15.2278 4.84411 15.4836C4.84411 15.7395 4.63756 15.9469 4.38171 15.9469C4.12501 15.9469 3.91846 15.7395 3.91846 15.4836C3.91846 15.2278 4.12501 15.0212 4.38171 15.0212Z"
                                       fill="currentColor" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                       d="M4.38082 15.3091C4.28477 15.3091 4.20657 15.3873 4.20657 15.4833C4.20657 15.6763 4.55592 15.6763 4.55592 15.4833C4.55592 15.3873 4.47687 15.3091 4.38082 15.3091ZM4.38067 16.5815C3.77376 16.5815 3.28076 16.0884 3.28076 15.4826C3.28076 14.8767 3.77376 14.3845 4.38067 14.3845C4.98757 14.3845 5.48142 14.8767 5.48142 15.4826C5.48142 16.0884 4.98757 16.5815 4.38067 16.5815Z"
                                       fill="currentColor" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                       d="M13.9701 15.0212C14.2259 15.0212 14.4333 15.2278 14.4333 15.4836C14.4333 15.7395 14.2259 15.9469 13.9701 15.9469C13.7134 15.9469 13.5068 15.7395 13.5068 15.4836C13.5068 15.2278 13.7134 15.0212 13.9701 15.0212Z"
                                       fill="currentColor" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                       d="M13.9692 15.3092C13.874 15.3092 13.7958 15.3874 13.7958 15.4835C13.7966 15.6781 14.1451 15.6764 14.1443 15.4835C14.1443 15.3874 14.0652 15.3092 13.9692 15.3092ZM13.969 16.5815C13.3621 16.5815 12.8691 16.0884 12.8691 15.4826C12.8691 14.8767 13.3621 14.3845 13.969 14.3845C14.5768 14.3845 15.0706 14.8767 15.0706 15.4826C15.0706 16.0884 14.5768 16.5815 13.969 16.5815Z"
                                       fill="currentColor" />
                                  </svg>
                                  <span class="tp-product-tooltip tp-product-tooltip-right">Thêm vào giỏ</span>
                              </button>
                            </form>
                            <button type="button"
                              class="tp-product-action-btn-2 tp-product-quick-view-btn"
                              data-bs-toggle="modal" data-bs-target="#producQuickViewModal">
                              <svg width="18" height="15" viewBox="0 0 18 15" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                   d="M8.99948 5.06828C7.80247 5.06828 6.82956 6.04044 6.82956 7.23542C6.82956 8.42951 7.80247 9.40077 8.99948 9.40077C10.1965 9.40077 11.1703 8.42951 11.1703 7.23542C11.1703 6.04044 10.1965 5.06828 8.99948 5.06828ZM8.99942 10.7482C7.0581 10.7482 5.47949 9.17221 5.47949 7.23508C5.47949 5.29705 7.0581 3.72021 8.99942 3.72021C10.9407 3.72021 12.5202 5.29705 12.5202 7.23508C12.5202 9.17221 10.9407 10.7482 8.99942 10.7482Z"
                                   fill="currentColor" />
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                   d="M1.41273 7.2346C3.08674 10.9265 5.90646 13.1215 8.99978 13.1224C12.0931 13.1215 14.9128 10.9265 16.5868 7.2346C14.9128 3.54363 12.0931 1.34863 8.99978 1.34773C5.90736 1.34863 3.08674 3.54363 1.41273 7.2346ZM9.00164 14.4703H8.99804H8.99714C5.27471 14.4676 1.93209 11.8629 0.0546754 7.50073C-0.0182251 7.33091 -0.0182251 7.13864 0.0546754 6.96883C1.93209 2.60759 5.27561 0.00288103 8.99714 0.000185582C8.99894 -0.000712902 8.99894 -0.000712902 8.99984 0.000185582C9.00164 -0.000712902 9.00164 -0.000712902 9.00254 0.000185582C12.725 0.00288103 16.0676 2.60759 17.945 6.96883C18.0188 7.13864 18.0188 7.33091 17.945 7.50073C16.0685 11.8629 12.725 14.4676 9.00254 14.4703H9.00164Z"
                                   fill="currentColor" />
                              </svg>
                              <span class="tp-product-tooltip tp-product-tooltip-right">Xem nhanh</span>
                            </button>
                            <button type="button"
                              class="tp-product-action-btn-2 tp-product-add-to-wishlist-btn">
                              <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                   d="M1.60355 7.98635C2.83622 11.8048 7.7062 14.8923 9.0004 15.6565C10.299 14.8844 15.2042 11.7628 16.3973 7.98985C17.1806 5.55102 16.4535 2.46177 13.5644 1.53473C12.1647 1.08741 10.532 1.35966 9.40484 2.22804C9.16921 2.40837 8.84214 2.41187 8.60476 2.23329C7.41078 1.33952 5.85105 1.07778 4.42936 1.53473C1.54465 2.4609 0.820172 5.55014 1.60355 7.98635ZM9.00138 17.0711C8.89236 17.0711 8.78421 17.0448 8.68574 16.9914C8.41055 16.8417 1.92808 13.2841 0.348132 8.3872C0.347252 8.3872 0.347252 8.38633 0.347252 8.38633C-0.644504 5.30321 0.459792 1.42874 4.02502 0.284605C5.69904 -0.254635 7.52342 -0.0174044 8.99874 0.909632C10.4283 0.00973263 12.3275 -0.238878 13.9681 0.284605C17.5368 1.43049 18.6446 5.30408 17.6538 8.38633C16.1248 13.2272 9.59485 16.8382 9.3179 16.9896C9.21943 17.0439 9.1104 17.0711 9.00138 17.0711Z"
                                   fill="currentColor" />
                              </svg>
                              <span class="tp-product-tooltip tp-product-tooltip-right">Thêm vào yêu thích</span>
                            </button>
                          </div>
                       </div>
                     </div>
                     <div class="tp-product-content-2 pt-15">
                       <div class="tp-product-tag-2">
                          <a href="#">{{ $product->brand->name ?? 'Không có thương hiệu' }}</a>
                       </div>
                       <h3 class="tp-product-title-2">
                          <a href="{{ route('client.product.show', ['slug' => $product->slug]) }}">{{ $product->name }}</a>
                       </h3>
                       <div class="tp-product-rating-icon tp-product-rating-icon-2">
                          @for ($i = 0; $i < 5; $i++)
                            <span><i class="fa-solid fa-star"></i></span>
                          @endfor
                       </div>
                       <div class="tp-product-price-wrapper-2">
                          <span class="tp-product-price-2 new-price">${{ number_format($product->price, 2) }}</span>
                          @if ($product->original_price && $product->original_price > $product->price)
                            <span class="tp-product-price-2 old-price">${{ number_format($product->original_price, 2) }}</span>
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
                        Bán chạy tuần này
                        <svg width="82" height="22" viewBox="0 0 82 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                           <path d="M81 14.5798C0.890564 -8.05914 -5.81154 0.0503902 5.00322 21" stroke="currentColor"
                              stroke-opacity="0.3" stroke-width="2" stroke-miterlimit="3.8637" stroke-linecap="round" />
                        </svg>
                     </span>
                     <h3 class="tp-section-title-2">Nổi bật tuần này</h3>
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-xl-3 col-lg-4 col-sm-6">
                  <div class="tp-product-item-2 mb-40">
                     <div class="tp-product-thumb-2 p-relative z-index-1 fix w-img">
                        <a href="product-details.html">
                           <img src="{{asset('assets2/img/product/2/prodcut-9.jpg')}}" alt="">
                        </a>
                        <!-- product action -->
                        <div class="tp-product-action-2 tp-product-action-blackStyle">
                           <div class="tp-product-action-item-2 d-flex flex-column">
                              <button type="button" class="tp-product-action-btn-2 tp-product-add-cart-btn">
                                 <svg width="17" height="17" viewBox="0 0 17 17" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                       d="M3.34706 4.53799L3.85961 10.6239C3.89701 11.0923 4.28036 11.4436 4.74871 11.4436H4.75212H14.0265H14.0282C14.4711 11.4436 14.8493 11.1144 14.9122 10.6774L15.7197 5.11162C15.7384 4.97924 15.7053 4.84687 15.6245 4.73995C15.5446 4.63218 15.4273 4.5626 15.2947 4.54393C15.1171 4.55072 7.74498 4.54054 3.34706 4.53799ZM4.74722 12.7162C3.62777 12.7162 2.68001 11.8438 2.58906 10.728L1.81046 1.4837L0.529505 1.26308C0.181854 1.20198 -0.0501969 0.873587 0.00930333 0.526523C0.0705036 0.17946 0.406255 -0.0462578 0.746256 0.00805037L2.51426 0.313534C2.79901 0.363599 3.01576 0.5995 3.04042 0.888012L3.24017 3.26484C15.3748 3.26993 15.4139 3.27587 15.4726 3.28266C15.946 3.3514 16.3625 3.59833 16.6464 3.97849C16.9303 4.35779 17.0493 4.82535 16.9813 5.29376L16.1747 10.8586C16.0225 11.9177 15.1011 12.7162 14.0301 12.7162H14.0259H4.75402H4.74722Z"
                                       fill="currentColor" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                       d="M12.6629 7.67446H10.3067C9.95394 7.67446 9.66919 7.38934 9.66919 7.03804C9.66919 6.68673 9.95394 6.40161 10.3067 6.40161H12.6629C13.0148 6.40161 13.3004 6.68673 13.3004 7.03804C13.3004 7.38934 13.0148 7.67446 12.6629 7.67446Z"
                                       fill="currentColor" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                       d="M4.38171 15.0212C4.63756 15.0212 4.84411 15.2278 4.84411 15.4836C4.84411 15.7395 4.63756 15.9469 4.38171 15.9469C4.12501 15.9469 3.91846 15.7395 3.91846 15.4836C3.91846 15.2278 4.12501 15.0212 4.38171 15.0212Z"
                                       fill="currentColor" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                       d="M4.38082 15.3091C4.28477 15.3091 4.20657 15.3873 4.20657 15.4833C4.20657 15.6763 4.55592 15.6763 4.55592 15.4833C4.55592 15.3873 4.47687 15.3091 4.38082 15.3091ZM4.38067 16.5815C3.77376 16.5815 3.28076 16.0884 3.28076 15.4826C3.28076 14.8767 3.77376 14.3845 4.38067 14.3845C4.98757 14.3845 5.48142 14.8767 5.48142 15.4826C5.48142 16.0884 4.98757 16.5815 4.38067 16.5815Z"
                                       fill="currentColor" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                       d="M13.9701 15.0212C14.2259 15.0212 14.4333 15.2278 14.4333 15.4836C14.4333 15.7395 14.2259 15.9469 13.9701 15.9469C13.7134 15.9469 13.5068 15.7395 13.5068 15.4836C13.5068 15.2278 13.7134 15.0212 13.9701 15.0212Z"
                                       fill="currentColor" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                       d="M13.9692 15.3092C13.874 15.3092 13.7958 15.3874 13.7958 15.4835C13.7966 15.6781 14.1451 15.6764 14.1443 15.4835C14.1443 15.3874 14.0652 15.3092 13.9692 15.3092ZM13.969 16.5815C13.3621 16.5815 12.8691 16.0884 12.8691 15.4826C12.8691 14.8767 13.3621 14.3845 13.969 14.3845C14.5768 14.3845 15.0706 14.8767 15.0706 15.4826C15.0706 16.0884 14.5768 16.5815 13.969 16.5815Z"
                                       fill="currentColor" />
                                 </svg>
                                 <span class="tp-product-tooltip tp-product-tooltip-right">Thêm vào giỏ</span>
                              </button>
                              <button type="button" class="tp-product-action-btn-2 tp-product-quick-view-btn"
                                 data-bs-toggle="modal" data-bs-target="#producQuickViewModal">
                                 <svg width="18" height="15" viewBox="0 0 18 15" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                       d="M8.99948 5.06828C7.80247 5.06828 6.82956 6.04044 6.82956 7.23542C6.82956 8.42951 7.80247 9.40077 8.99948 9.40077C10.1965 9.40077 11.1703 8.42951 11.1703 7.23542C11.1703 6.04044 10.1965 5.06828 8.99948 5.06828ZM8.99942 10.7482C7.0581 10.7482 5.47949 9.17221 5.47949 7.23508C5.47949 5.29705 7.0581 3.72021 8.99942 3.72021C10.9407 3.72021 12.5202 5.29705 12.5202 7.23508C12.5202 9.17221 10.9407 10.7482 8.99942 10.7482Z"
                                       fill="currentColor" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                       d="M1.41273 7.2346C3.08674 10.9265 5.90646 13.1215 8.99978 13.1224C12.0931 13.1215 14.9128 10.9265 16.5868 7.2346C14.9128 3.54363 12.0931 1.34863 8.99978 1.34773C5.90736 1.34863 3.08674 3.54363 1.41273 7.2346ZM9.00164 14.4703H8.99804H8.99714C5.27471 14.4676 1.93209 11.8629 0.0546754 7.50073C-0.0182251 7.33091 -0.0182251 7.13864 0.0546754 6.96883C1.93209 2.60759 5.27561 0.00288103 8.99714 0.000185582C8.99894 -0.000712902 8.99894 -0.000712902 8.99984 0.000185582C9.00164 -0.000712902 9.00164 -0.000712902 9.00254 0.000185582C12.725 0.00288103 16.0676 2.60759 17.945 6.96883C18.0188 7.13864 18.0188 7.33091 17.945 7.50073C16.0685 11.8629 12.725 14.4676 9.00254 14.4703H9.00164Z"
                                       fill="currentColor" />
                                 </svg>
                                 <span class="tp-product-tooltip tp-product-tooltip-right">Xem nhanh</span>
                              </button>
                              <button type="button"
                                class="tp-product-action-btn-2 tp-product-add-to-wishlist-btn"
                                @if($isOutOfStock) disabled style="opacity:0.5;cursor:not-allowed;" @endif>
                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                   xmlns="http://www.w3.org/2000/svg">
                                   <path fill-rule="evenodd" clip-rule="evenodd"
                                      d="M1.60355 7.98635C2.83622 11.8048 7.7062 14.8923 9.0004 15.6565C10.299 14.8844 15.2042 11.7628 16.3973 7.98985C17.1806 5.55102 16.4535 2.46177 13.5644 1.53473C12.1647 1.08741 10.532 1.35966 9.40484 2.22804C9.16921 2.40837 8.84214 2.41187 8.60476 2.23329C7.41078 1.33952 5.85105 1.07778 4.42936 1.53473C1.54465 2.4609 0.820172 5.55014 1.60355 7.98635ZM9.00138 17.0711C8.89236 17.0711 8.78421 17.0448 8.68574 16.9914C8.41055 16.8417 1.92808 13.2841 0.348132 8.3872C0.347252 8.3872 0.347252 8.38633 0.347252 8.38633C-0.644504 5.30321 0.459792 1.42874 4.02502 0.284605C5.69904 -0.254635 7.52342 -0.0174044 8.99874 0.909632C10.4283 0.00973263 12.3275 -0.238878 13.9681 0.284605C17.5368 1.43049 18.6446 5.30408 17.6538 8.38633C16.1248 13.2272 9.59485 16.8382 9.3179 16.9896C9.21943 17.0439 9.1104 17.0711 9.00138 17.0711Z"
                                      fill="currentColor" />
                                   <path fill-rule="evenodd" clip-rule="evenodd"
                                      d="M14.203 6.67473C13.8627 6.67473 13.5743 6.41474 13.5462 6.07159C13.4882 5.35202 13.0046 4.7445 12.3162 4.52302C11.9689 4.41097 11.779 4.04068 11.8906 3.69666C12.0041 3.35175 12.3724 3.16442 12.7206 3.27297C13.919 3.65901 14.7586 4.71561 14.8615 5.96479C14.8905 6.32632 14.6206 6.64322 14.2575 6.6721C14.239 6.67385 14.2214 6.67473 14.203 6.67473Z"
                                      fill="currentColor" />
                                </svg>
                                <span class="tp-product-tooltip tp-product-tooltip-right">Thêm vào yêu thích</span>
                              </button>
                              <button type="button"
                                class="tp-product-action-btn-2 tp-product-add-to-compare-btn"
                                @if($isOutOfStock) disabled style="opacity:0.5;cursor:not-allowed;" @endif>
                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                   xmlns="http://www.w3.org/2000/svg">
                                   <path d="M11.4144 6.16828L14 3.58412L11.4144 1" stroke="currentColor"
                                      stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                   <path d="M1.48883 3.58374L14 3.58374" stroke="currentColor" stroke-width="1.5"
                                      stroke-linecap="round" stroke-linejoin="round" />
                                   <path d="M4.07446 8.32153L1.48884 10.9057L4.07446 13.4898" stroke="currentColor"
                                      stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                   <path d="M14 10.9058H1.48883" stroke="currentColor" stroke-width="1.5"
                                      stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <span class="tp-product-tooltip tp-product-tooltip-right">Thêm vào so sánh</span>
                              </button>
                           </div>
                        </div>
                     </div>
                     <div class="tp-product-content-2 pt-15">
                        <div class="tp-product-tag-2">
                           <a href="#">Giày, </a>
                           <a href="#">Đồ lót</a>
                        </div>
                        <h3 class="tp-product-title-2">
                           <a href="product-details.html">Govicta Men's Shoes Leather</a>
                        </h3>
                        <div class="tp-product-rating-icon tp-product-rating-icon-2">
                           <span><i class="fa-solid fa-star"></i></span>
                           <span><i class="fa-solid fa-star"></i></span>
                           <span><i class="fa-solid fa-star"></i></span>
                           <span><i class="fa-solid fa-star"></i></span>
                           <span><i class="fa-solid fa-star"></i></span>
                        </div>
                        <div class="tp-product-price-wrapper-2">
                           <span class="tp-product-price-2 new-price">$76.00</span>
                           <span class="tp-product-price-2 old-price">$120.00</span>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="col-xl-3 col-lg-4 col-sm-6">
                  <div class="tp-product-item-2 mb-40">
                     <div class="tp-product-thumb-2 p-relative z-index-1 fix w-img">
                        <a href="product-details.html">
                           <img src="{{asset('assets2/img/product/2/prodcut-10.jpg')}}" alt="">
                        </a>
                     </div>
                     <div class="tp-product-content-2 pt-15">
                        <div class="tp-product-tag-2">
                           <a href="#">Backpack, </a>
                           <a href="#">School Bag</a>
                        </div>
                        <h3 class="tp-product-title-2">
                           <a href="product-details.html">Backpack, School, Travel</a>
                        </h3>
                        <div class="tp-product-rating-icon tp-product-rating-icon-2">
                           <span><i class="fa-solid fa-star"></i></span>
                           <span><i class="fa-solid fa-star"></i></span>
                           <span><i class="fa-solid fa-star"></i></span>
                           <span><i class="fa-solid fa-star"></i></span>
                           <span><i class="fa-solid fa-star"></i></span>
                        </div>
                        <div class="tp-product-price-wrapper-2">
                           <span class="tp-product-price-2 new-price">$82.00</span>
                           <span class="tp-product-price-2 old-price">$99.00</span>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="col-xl-3 col-lg-4 col-sm-6">
                  <div class="tp-product-item-2 mb-40">
                     <div class="tp-product-thumb-2 p-relative z-index-1 fix w-img">
                        <a href="product-details.html">
                           <img src="{{asset('assets2/img/product/2/prodcut-11.jpg')}}" alt="">
                        </a>
                     </div>
                     <div class="tp-product-content-2 pt-15">
                        <div class="tp-product-tag-2">
                           <a href="#">Giày, </a>
                           <a href="#">Nam</a>
                        </div>
                        <h3 class="tp-product-title-2">
                           <a href="product-details.html">Legendary Whitetails Men's.</a>
                        </h3>
                        <div class="tp-product-rating-icon tp-product-rating-icon-2">
                           <span><i class="fa-solid fa-star"></i></span>
                           <span><i class="fa-solid fa-star"></i></span>
                           <span><i class="fa-solid fa-star"></i></span>
                           <span><i class="fa-solid fa-star"></i></span>
                           <span><i class="fa-solid fa-star"></i></span>
                        </div>
                        <div class="tp-product-price-wrapper-2">
                           <span class="tp-product-price-2 new-price">$36.00</span>
                           <span class="tp-product-price-2 old-price">$72.00</span>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="col-xl-3 col-lg-4 col-sm-6">
                  <div class="tp-product-item-2 mb-40">
                     <div class="tp-product-thumb-2 p-relative z-index-1 fix w-img">
                        <a href="product-details.html">
                           <img src="{{asset('assets2/img/product/2/prodcut-12.jpg')}}" alt="">
                        </a>
                     </div>
                     <div class="tp-product-content-2 pt-15">
                        <div class="tp-product-tag-2">
                           <a href="#">Túi, </a>
                           <a href="#">Wonder</a>
                        </div>
                        <h3 class="tp-product-title-2">
                           <a href="product-details.html">Tommy Hilfiger Women's Jaden</a>
                        </h3>
                        <div class="tp-product-rating-icon tp-product-rating-icon-2">
                           <span><i class="fa-solid fa-star"></i></span>
                           <span><i class="fa-solid fa-star"></i></span>
                           <span><i class="fa-solid fa-star"></i></span>
                           <span><i class="fa-solid fa-star"></i></span>
                           <span><i class="fa-solid fa-star"></i></span>
                        </div>
                        <div class="tp-product-price-wrapper-2">
                           <span class="tp-product-price-2 new-price">$44.00</span>
                           <span class="tp-product-price-2 old-price">$66.00</span>
                        </div>
                     </div>
                  </div>
               </div>
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
                  <div class="tp-section-title-wrapper-2 mb-50">
                     <span class="tp-section-title-pre-2">
                        Đánh giá của khách hàng
                        <svg width="82" height="22" viewBox="0 0 82 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                           <path d="M81 14.5798C0.890564 -8.05914 -5.81154 0.0503902 5.00322 21" stroke="currentColor"
                              stroke-opacity="0.3" stroke-width="2" stroke-miterlimit="3.8637" stroke-linecap="round" />
                        </svg>
                     </span>
                     <h3 class="tp-section-title-2">Những đánh giá tích cực từ khách hàng</h3>
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
                           <div class="tp-testimonial-item text-center mb-20 swiper-slide">
                              <div class="tp-testimonial-rating">
                                 <span><i class="fa-solid fa-star"></i></span>
                                 <span><i class="fa-solid fa-star"></i></span>
                                 <span><i class="fa-solid fa-star"></i></span>
                                 <span><i class="fa-solid fa-star"></i></span>
                                 <span><i class="fa-solid fa-star"></i></span>
                              </div>
                              <div class="tp-testimonial-content">
                                 <p>" Cách bạn sử dụng tên thành phố hoặc thị trấn là tùy bạn. Tất cả kết quả có thể sử dụng tự do cho bất kỳ công việc nào."</p>
                              </div>
                              <div
                                 class="tp-testimonial-user-wrapper d-flex align-items-center justify-content-center">
                                 <div class="tp-testimonial-user d-flex align-items-center">
                                    <div class="tp-testimonial-avater mr-10">
                                       <img src="{{asset('assets2/img/users/user-2.jpg')}}" alt="">
                                    </div>
                                    <div class="tp-testimonial-user-info tp-testimonial-user-translate">
                                       <h3 class="tp-testimonial-user-title">Theodore Handle</h3>
                                       <span class="tp-testimonial-designation">Đồng sáng lập</span>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="tp-testimonial-item text-center mb-20 swiper-slide">
                              <div class="tp-testimonial-rating">
                                 <span><i class="fa-solid fa-star"></i></span>
                                 <span><i class="fa-solid fa-star"></i></span>
                                 <span><i class="fa-solid fa-star"></i></span>
                                 <span><i class="fa-solid fa-star"></i></span>
                                 <span><i class="fa-solid fa-star"></i></span>
                              </div>
                              <div class="tp-testimonial-content">
                                 <p>"Rất hài lòng khi đưa con gái đến Brave care. Toàn bộ đội ngũ rất tuyệt vời! Cảm ơn!"</p>
                              </div>
                              <div
                                 class="tp-testimonial-user-wrapper d-flex align-items-center justify-content-center">
                                 <div class="tp-testimonial-user d-flex align-items-center">
                                    <div class="tp-testimonial-avater mr-10">
                                       <img src="{{asset('assets2/img/users/user-3.jpg')}}" alt="">
                                    </div>
                                    <div class="tp-testimonial-user-info tp-testimonial-user-translate">
                                       <h3 class="tp-testimonial-user-title">Shahnewaz Sakil</h3>
                                       <span class="tp-testimonial-designation">Thiết kế UI/UX</span>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="tp-testimonial-item text-center mb-20 swiper-slide">
                              <div class="tp-testimonial-rating">
                                 <span><i class="fa-solid fa-star"></i></span>
                                 <span><i class="fa-solid fa-star"></i></span>
                                 <span><i class="fa-solid fa-star"></i></span>
                                 <span><i class="fa-solid fa-star"></i></span>
                                 <span><i class="fa-solid fa-star"></i></span>
                              </div>
                              <div class="tp-testimonial-content">
                                 <p>"Cảm ơn vì tất cả nỗ lực và tinh thần đồng đội trong những tháng vừa qua! Cảm ơn rất nhiều"</p>
                              </div>
                              <div
                                 class="tp-testimonial-user-wrapper d-flex align-items-center justify-content-center">
                                 <div class="tp-testimonial-user d-flex align-items-center">
                                    <div class="tp-testimonial-avater mr-10">
                                       <img src="{{asset('assets2/img/users/user-4.jpg')}}" alt="">
                                    </div>
                                    <div class="tp-testimonial-user-info tp-testimonial-user-translate">
                                       <h3 class="tp-testimonial-user-title">James Dopli</h3>
                                       <span class="tp-testimonial-designation">Lập trình viên</span>
                                    </div>
                                 </div>
                              </div>
                           </div>
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
                                 <path d="M10.7222 1H31.5555V19.0556H10.7222V1Z" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" />
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
                                 <path d="M10.3636 1V34" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
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
                                 <mask id="mask0_1211_583" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0"
                                    y="0" width="31" height="30">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M0 0H30.0024V29.9998H0V0Z"
                                       fill="white" />
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
