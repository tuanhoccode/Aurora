<!doctype html>
<html class="no-js" lang="zxx">

<!-- Mirrored from html.storebuild.shop/shofy-prv/shofy/index-2.html by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 18 May 2025 07:19:23 GMT -->

<head>
   <meta charset="utf-8">
   <meta http-equiv="x-ua-compatible" content="ie=edge">
   <title>@yield('title', 'Aurora') </title>
   <meta name="description" content="">
   <meta name="viewport" content="width=device-width, initial-scale=1">

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
                              <span>New Arrivals 2023</span>
                              <h3 class="tp-slider-title-2">The Clothing Collection</h3>
                              <div class="tp-slider-btn-2">
                                 <a href="shop.html" class="tp-btn tp-btn-border">Shop Collection</a>
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
                              <span>Best Selling 2023</span>
                              <h3 class="tp-slider-title-2">The Summer Collection</h3>
                              <div class="tp-slider-btn-2">
                                 <a href="shop.html" class="tp-btn tp-btn-border">Shop Collection</a>
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
                              <span>Winter Has Arrived</span>
                              <h3 class="tp-slider-title-2">Amazing New designs</h3>
                              <div class="tp-slider-btn-2">
                                 <a href="shop.html" class="tp-btn tp-btn-border">Shop Collection</a>
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
      <section class="tp-banner-area mt-20">
         <div class="container-fluid tp-gx-40">
            <div class="row tp-gx-20">
               <div class="col-xxl-4 col-lg-6">
                  <div class="tp-banner-item-2 p-relative z-index-1 grey-bg-2 mb-20 fix">
                     <div class="tp-banner-thumb-2 include-bg transition-3"
                        data-background="{{asset('assets2/img/banner/2/banner-1.jpg')}}"></div>
                     <h3 class="tp-banner-title-2">
                        <a href="shop.html">T-Shirt Tunic <br> Tops Blouse</a>
                     </h3>
                     <div class="tp-banner-btn-2">
                        <a href="shop.html" class="tp-btn tp-btn-border tp-btn-border-sm">Shop Now
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
               </div>
               <div class="col-xxl-4 col-lg-6">
                  <div class="tp-banner-item-2 p-relative z-index-1 grey-bg-2 mb-20 fix">
                     <div class="tp-banner-thumb-2 include-bg transition-3"
                        data-background="{{asset('assets2/img/banner/2/banner-2.jpg')}}"></div>
                     <h3 class="tp-banner-title-2">
                        <a href="shop.html">Satchel Tote <br> Crossbody Bags</a>
                     </h3>
                     <div class="tp-banner-btn-2">
                        <a href="shop.html" class="tp-btn tp-btn-border tp-btn-border-sm">Shop Now
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
               </div>
               <div class="col-xxl-4 col-lg-6">
                  <div class="tp-banner-item-2 p-relative z-index-1 grey-bg-2 mb-20 fix">
                     <div class="tp-banner-thumb-2 include-bg transition-3"
                        data-background="{{asset('assets2/img/banner/2/banner-3.jpg')}}"></div>
                     <h3 class="tp-banner-title-2">
                        <a href="shop.html">Men's Tennis <br> Walking Shoes</a>
                     </h3>
                     <div class="tp-banner-btn-2">
                        <a href="shop.html" class="tp-btn tp-btn-border tp-btn-border-sm">Shop Now
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
               </div>
            </div>
         </div>
      </section>
      <!-- banner area end -->

      <!-- product action -->
      {{-- <div class="tp-product-action-2 tp-product-action-blackStyle">
         <div class="tp-product-action-item-2 d-flex flex-column">
            <button type="button" class="tp-product-action-btn-2 tp-product-add-cart-btn">
               <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
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
               <span class="tp-product-tooltip tp-product-tooltip-right">Add to
                  Cart</span>
            </button>
            <button type="button" class="tp-product-action-btn-2 tp-product-quick-view-btn" data-bs-toggle="modal"
               data-bs-target="#producQuickViewModal">
               <svg width="18" height="15" viewBox="0 0 18 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd" clip-rule="evenodd"
                     d="M8.99948 5.06828C7.80247 5.06828 6.82956 6.04044 6.82956 7.23542C6.82956 8.42951 7.80247 9.40077 8.99948 9.40077C10.1965 9.40077 11.1703 8.42951 11.1703 7.23542C11.1703 6.04044 10.1965 5.06828 8.99948 5.06828ZM8.99942 10.7482C7.0581 10.7482 5.47949 9.17221 5.47949 7.23508C5.47949 5.29705 7.0581 3.72021 8.99942 3.72021C10.9407 3.72021 12.5202 5.29705 12.5202 7.23508C12.5202 9.17221 10.9407 10.7482 8.99942 10.7482Z"
                     fill="currentColor" />
                  <path fill-rule="evenodd" clip-rule="evenodd"
                     d="M1.41273 7.2346C3.08674 10.9265 5.90646 13.1215 8.99978 13.1224C12.0931 13.1215 14.9128 10.9265 16.5868 7.2346C14.9128 3.54363 12.0931 1.34863 8.99978 1.34773C5.90736 1.34863 3.08674 3.54363 1.41273 7.2346ZM9.00164 14.4703H8.99804H8.99714C5.27471 14.4676 1.93209 11.8629 0.0546754 7.50073C-0.0182251 7.33091 -0.0182251 7.13864 0.0546754 6.96883C1.93209 2.60759 5.27561 0.00288103 8.99714 0.000185582C8.99894 -0.000712902 8.99894 -0.000712902 8.99984 0.000185582C9.00164 -0.000712902 9.00164 -0.000712902 9.00254 0.000185582C12.725 0.00288103 16.0676 2.60759 17.945 6.96883C18.0188 7.13864 18.0188 7.33091 17.945 7.50073C16.0685 11.8629 12.725 14.4676 9.00254 14.4703H9.00164Z"
                     fill="currentColor" />
               </svg>
               <span class="tp-product-tooltip tp-product-tooltip-right">Quick View</span>
            </button>
            <button type="button" class="tp-product-action-btn-2 tp-product-add-to-wishlist-btn">
               <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd" clip-rule="evenodd"
                     d="M1.60355 7.98635C2.83622 11.8048 7.7062 14.8923 9.0004 15.6565C10.299 14.8844 15.2042 11.7628 16.3973 7.98985C17.1806 5.55102 16.4535 2.46177 13.5644 1.53473C12.1647 1.08741 10.532 1.35966 9.40484 2.22804C9.16921 2.40837 8.84214 2.41187 8.60476 2.23329C7.41078 1.33952 5.85105 1.07778 4.42936 1.53473C1.54465 2.4609 0.820172 5.55014 1.60355 7.98635ZM9.00138 17.0711C8.89236 17.0711 8.78421 17.0448 8.68574 16.9914C8.41055 16.8417 1.92808 13.2841 0.348132 8.3872C0.347252 8.3872 0.347252 8.38633 0.347252 8.38633C-0.644504 5.30321 0.459792 1.42874 4.02502 0.284605C5.69904 -0.254635 7.52342 -0.0174044 8.99874 0.909632C10.4283 0.00973263 12.3275 -0.238878 13.9681 0.284605C17.5368 1.43049 18.6446 5.30408 17.6538 8.38633C16.1248 13.2272 9.59485 16.8382 9.3179 16.9896C9.21943 17.0439 9.1104 17.0711 9.00138 17.0711Z"
                     fill="currentColor" />
                  <path fill-rule="evenodd" clip-rule="evenodd"
                     d="M14.203 6.67473C13.8627 6.67473 13.5743 6.41474 13.5462 6.07159C13.4882 5.35202 13.0046 4.7445 12.3162 4.52302C11.9689 4.41097 11.779 4.04068 11.8906 3.69666C12.0041 3.35175 12.3724 3.16442 12.7206 3.27297C13.919 3.65901 14.7586 4.71561 14.8615 5.96479C14.8905 6.32632 14.6206 6.64322 14.2575 6.6721C14.239 6.67385 14.2214 6.67473 14.203 6.67473Z"
                     fill="currentColor" />
               </svg>
               <span class="tp-product-tooltip tp-product-tooltip-right">Add To
                  Wishlist</span>
            </button>
         </div>
      </div> --}}

      <!-- product area start -->
      <section class="tp-product-area pb-90">
         <div class="container">
            <div class="row">
               <div class="col-xl-12">
                  <div class="tp-section-title-wrapper-2 text-center mb-35">
                     <span class="tp-section-title-pre-2">
                        All Product Shop
                        <svg width="82" height="22" viewBox="0 0 82 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                           <path d="M81 14.5798C0.890564 -8.05914 -5.81154 0.0503902 5.00322 21" stroke="currentColor"
                              stroke-opacity="0.3" stroke-width="2" stroke-miterlimit="3.8637" stroke-linecap="round" />
                        </svg>
                     </span>
                     <h3 class="tp-section-title-2">Customer Favorite Style Product</h3>
                  </div>
               </div>
            </div>
            {{-- Tabs danh mục --}}
            <div class="row">
               <div class="col-xl-12">
                  <div class="tp-product-tab-2 tp-tab mb-50 text-center">
                     <nav>
                        <div class="nav nav-tabs justify-content-center" id="nav-tab" role="tablist">
                           <button class="nav-link active" id="nav-allCollection-tab" data-bs-toggle="tab"
                              data-bs-target="#nav-allCollection" type="button" role="tab"
                              aria-controls="nav-allCollection" aria-selected="true">
                              All Collection
                              <span class="tp-product-tab-tooltip">
                                 {{ $categories->sum(fn($c) => $c->products->count()) }}
                              </span>
                           </button>

                           @foreach ($categories as $category)
                        <button class="nav-link" id="nav-{{ Str::slug($category->name) }}-tab" data-bs-toggle="tab"
                          data-bs-target="#nav-{{ Str::slug($category->name) }}" type="button" role="tab"
                          aria-controls="nav-{{ Str::slug($category->name) }}" aria-selected="false">
                          {{ $category->name }}
                          <span class="tp-product-tab-tooltip">{{ $category->products->count() }}</span>
                        </button>
                     @endforeach
                        </div>
                     </nav>
                  </div>
               </div>
            </div>

            {{-- Tab nội dung --}}
            <div class="row">
               <div class="col-xl-12">
                  <div class="tab-content" id="nav-tabContent">

                     {{-- Tab All --}}
                     <div class="tab-pane fade show active" id="nav-allCollection" role="tabpanel"
                        aria-labelledby="nav-allCollection-tab" tabindex="0">
                        <div class="row">
                           @foreach ($products as $product)
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                          <div class="tp-product-item-2 mb-40">
                            <div class="tp-product-thumb-2 p-relative z-index-1 fix w-img">
                              <a href="#">
                                 <img src="{{ asset('storage/products/' . $product->thumbnail) }}"
                                   alt="{{ $product->name }}">
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
                                     <span class="tp-product-tooltip tp-product-tooltip-right">Add to
                                       Cart</span>
                                   </button>
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
                                     <span class="tp-product-tooltip tp-product-tooltip-right">Quick View</span>
                                   </button>
                                   <button type="button"
                                     class="tp-product-action-btn-2 tp-product-add-to-wishlist-btn">
                                     <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                       xmlns="http://www.w3.org/2000/svg">
                                       <path fill-rule="evenodd" clip-rule="evenodd"
                                          d="M1.60355 7.98635C2.83622 11.8048 7.7062 14.8923 9.0004 15.6565C10.299 14.8844 15.2042 11.7628 16.3973 7.98985C17.1806 5.55102 16.4535 2.46177 13.5644 1.53473C12.1647 1.08741 10.532 1.35966 9.40484 2.22804C9.16921 2.40837 8.84214 2.41187 8.60476 2.23329C7.41078 1.33952 5.85105 1.07778 4.42936 1.53473C1.54465 2.4609 0.820172 5.55014 1.60355 7.98635ZM9.00138 17.0711C8.89236 17.0711 8.78421 17.0448 8.68574 16.9914C8.41055 16.8417 1.92808 13.2841 0.348132 8.3872C0.347252 8.3872 0.347252 8.38633 0.347252 8.38633C-0.644504 5.30321 0.459792 1.42874 4.02502 0.284605C5.69904 -0.254635 7.52342 -0.0174044 8.99874 0.909632C10.4283 0.00973263 12.3275 -0.238878 13.9681 0.284605C17.5368 1.43049 18.6446 5.30408 17.6538 8.38633C16.1248 13.2272 9.59485 16.8382 9.3179 16.9896C9.21943 17.0439 9.1104 17.0711 9.00138 17.0711Z"
                                          fill="currentColor" />
                                       <path fill-rule="evenodd" clip-rule="evenodd"
                                          d="M14.203 6.67473C13.8627 6.67473 13.5743 6.41474 13.5462 6.07159C13.4882 5.35202 13.0046 4.7445 12.3162 4.52302C11.9689 4.41097 11.779 4.04068 11.8906 3.69666C12.0041 3.35175 12.3724 3.16442 12.7206 3.27297C13.919 3.65901 14.7586 4.71561 14.8615 5.96479C14.8905 6.32632 14.6206 6.64322 14.2575 6.6721C14.239 6.67385 14.2214 6.67473 14.203 6.67473Z"
                                          fill="currentColor" />
                                     </svg>
                                     <span class="tp-product-tooltip tp-product-tooltip-right">Add To
                                       Wishlist</span>
                                   </button>
                                 </div>
                              </div>
                            </div>

                            <div class="tp-product-content-2 pt-15">
                              <div class="tp-product-tag-2">
                                 <a href="#">{{ $product->brand->name ?? 'No Brand' }}</a>
                              </div>

                              <h3 class="tp-product-title-2">
                                 <a href="#">{{ $product->name }}</a>
                              </h3>

                              <div class="tp-product-rating-icon tp-product-rating-icon-2">
                                 @for ($i = 0; $i < 5; $i++)
                            <span><i class="fa-solid fa-star"></i></span>
                          @endfor
                              </div>

                              <div class="tp-product-price-wrapper-2">
                                 <span
                                   class="tp-product-price-2 new-price">${{ number_format($product->price, 2) }}</span>
                                 @if ($product->original_price && $product->original_price > $product->price)
                            <span
                              class="tp-product-price-2 old-price">${{ number_format($product->original_price, 2) }}</span>
                          @endif
                              </div>
                            </div>
                          </div>
                        </div>
                     @endforeach
                        </div>
                     </div>

                     @foreach ($categories as $category)
                   <div class="tab-pane fade" id="nav-{{ Str::slug($category->name) }}" role="tabpanel"
                     aria-labelledby="nav-{{ Str::slug($category->name) }}-tab" tabindex="0">
                     <div class="row">
                        @foreach ($category->products as $product)
                     <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                       <div class="tp-product-item-2 mb-40">
                        <div class="tp-product-thumb-2 p-relative z-index-1 fix w-img">
                          <a href="#">
                            <img src="{{ asset('storage/app/public/products/' . $product->thumbnail) }}"
                              alt="{{ $product->name }}">
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
                               <span class="tp-product-tooltip tp-product-tooltip-right">Add to
                                 Cart</span>
                              </button>
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
                               <span class="tp-product-tooltip tp-product-tooltip-right">Quick View</span>
                              </button>
                              <button type="button"
                               class="tp-product-action-btn-2 tp-product-add-to-wishlist-btn">
                               <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                 <path fill-rule="evenodd" clip-rule="evenodd"
                                   d="M1.60355 7.98635C2.83622 11.8048 7.7062 14.8923 9.0004 15.6565C10.299 14.8844 15.2042 11.7628 16.3973 7.98985C17.1806 5.55102 16.4535 2.46177 13.5644 1.53473C12.1647 1.08741 10.532 1.35966 9.40484 2.22804C9.16921 2.40837 8.84214 2.41187 8.60476 2.23329C7.41078 1.33952 5.85105 1.07778 4.42936 1.53473C1.54465 2.4609 0.820172 5.55014 1.60355 7.98635ZM9.00138 17.0711C8.89236 17.0711 8.78421 17.0448 8.68574 16.9914C8.41055 16.8417 1.92808 13.2841 0.348132 8.3872C0.347252 8.3872 0.347252 8.38633 0.347252 8.38633C-0.644504 5.30321 0.459792 1.42874 4.02502 0.284605C5.69904 -0.254635 7.52342 -0.0174044 8.99874 0.909632C10.4283 0.00973263 12.3275 -0.238878 13.9681 0.284605C17.5368 1.43049 18.6446 5.30408 17.6538 8.38633C16.1248 13.2272 9.59485 16.8382 9.3179 16.9896C9.21943 17.0439 9.1104 17.0711 9.00138 17.0711Z"
                                   fill="currentColor" />
                                 <path fill-rule="evenodd" clip-rule="evenodd"
                                   d="M14.203 6.67473C13.8627 6.67473 13.5743 6.41474 13.5462 6.07159C13.4882 5.35202 13.0046 4.7445 12.3162 4.52302C11.9689 4.41097 11.779 4.04068 11.8906 3.69666C12.0041 3.35175 12.3724 3.16442 12.7206 3.27297C13.919 3.65901 14.7586 4.71561 14.8615 5.96479C14.8905 6.32632 14.6206 6.64322 14.2575 6.6721C14.239 6.67385 14.2214 6.67473 14.203 6.67473Z"
                                   fill="currentColor" />
                               </svg>
                               <span class="tp-product-tooltip tp-product-tooltip-right">Add To
                                 Wishlist</span>
                              </button>
                            </div>
                          </div>
                        </div>

                        <div class="tp-product-content-2 pt-15">
                          <div class="tp-product-tag-2">
                            <a href="#">{{ $product->brand->name ?? 'No Brand' }}</a>
                          </div>

                          <h3 class="tp-product-title-2">
                            <a href="#">{{ $product->name }}</a>
                          </h3>

                          <div class="tp-product-rating-icon tp-product-rating-icon-2">
                            @for ($i = 0; $i < 5; $i++)
                        <span><i class="fa-solid fa-star"></i></span>
                       @endfor
                          </div>

                          <div class="tp-product-price-wrapper-2">
                            <span
                              class="tp-product-price-2 new-price">${{ number_format($product->price, 2) }}</span>
                            @if ($product->original_price && $product->original_price > $product->price)
                        <span
                          class="tp-product-price-2 old-price">${{ number_format($product->original_price, 2) }}</span>
                       @endif
                          </div>
                        </div>
                       </div>
                     </div>
                   @endforeach
                     </div>
                   </div>
                @endforeach

                  </div>
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
                        Best Seller This Week’s
                        <svg width="82" height="22" viewBox="0 0 82 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                           <path d="M81 14.5798C0.890564 -8.05914 -5.81154 0.0503902 5.00322 21" stroke="currentColor"
                              stroke-opacity="0.3" stroke-width="2" stroke-miterlimit="3.8637" stroke-linecap="round" />
                        </svg>
                     </span>
                     <h3 class="tp-section-title-2">This Week's Featured</h3>
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
                                 <span class="tp-product-tooltip tp-product-tooltip-right">Add to Cart</span>
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
                                 <span class="tp-product-tooltip tp-product-tooltip-right">Quick View</span>
                              </button>
                              <button type="button" class="tp-product-action-btn-2 tp-product-add-to-wishlist-btn">
                                 <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                       d="M1.60355 7.98635C2.83622 11.8048 7.7062 14.8923 9.0004 15.6565C10.299 14.8844 15.2042 11.7628 16.3973 7.98985C17.1806 5.55102 16.4535 2.46177 13.5644 1.53473C12.1647 1.08741 10.532 1.35966 9.40484 2.22804C9.16921 2.40837 8.84214 2.41187 8.60476 2.23329C7.41078 1.33952 5.85105 1.07778 4.42936 1.53473C1.54465 2.4609 0.820172 5.55014 1.60355 7.98635ZM9.00138 17.0711C8.89236 17.0711 8.78421 17.0448 8.68574 16.9914C8.41055 16.8417 1.92808 13.2841 0.348132 8.3872C0.347252 8.3872 0.347252 8.38633 0.347252 8.38633C-0.644504 5.30321 0.459792 1.42874 4.02502 0.284605C5.69904 -0.254635 7.52342 -0.0174044 8.99874 0.909632C10.4283 0.00973263 12.3275 -0.238878 13.9681 0.284605C17.5368 1.43049 18.6446 5.30408 17.6538 8.38633C16.1248 13.2272 9.59485 16.8382 9.3179 16.9896C9.21943 17.0439 9.1104 17.0711 9.00138 17.0711Z"
                                       fill="currentColor" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                       d="M14.203 6.67473C13.8627 6.67473 13.5743 6.41474 13.5462 6.07159C13.4882 5.35202 13.0046 4.7445 12.3162 4.52302C11.9689 4.41097 11.779 4.04068 11.8906 3.69666C12.0041 3.35175 12.3724 3.16442 12.7206 3.27297C13.919 3.65901 14.7586 4.71561 14.8615 5.96479C14.8905 6.32632 14.6206 6.64322 14.2575 6.6721C14.239 6.67385 14.2214 6.67473 14.203 6.67473Z"
                                       fill="currentColor" />
                                 </svg>
                                 <span class="tp-product-tooltip tp-product-tooltip-right">Add To Wishlist</span>
                              </button>
                              <button type="button" class="tp-product-action-btn-2 tp-product-add-to-compare-btn">
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
                                 <span class="tp-product-tooltip tp-product-tooltip-right">Add To Compare</span>
                              </button>
                           </div>
                        </div>
                     </div>
                     <div class="tp-product-content-2 pt-15">
                        <div class="tp-product-tag-2">
                           <a href="#">Shoes, </a>
                           <a href="#">Work Dress</a>
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
                        <!-- product action -->
                        {{-- <div class="tp-product-action-2 tp-product-action-blackStyle">
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
                                 <span class="tp-product-tooltip tp-product-tooltip-right">Add to Cart</span>
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
                                 <span class="tp-product-tooltip tp-product-tooltip-right">Quick View</span>
                              </button>
                              <button type="button" class="tp-product-action-btn-2 tp-product-add-to-wishlist-btn">
                                 <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                       d="M1.60355 7.98635C2.83622 11.8048 7.7062 14.8923 9.0004 15.6565C10.299 14.8844 15.2042 11.7628 16.3973 7.98985C17.1806 5.55102 16.4535 2.46177 13.5644 1.53473C12.1647 1.08741 10.532 1.35966 9.40484 2.22804C9.16921 2.40837 8.84214 2.41187 8.60476 2.23329C7.41078 1.33952 5.85105 1.07778 4.42936 1.53473C1.54465 2.4609 0.820172 5.55014 1.60355 7.98635ZM9.00138 17.0711C8.89236 17.0711 8.78421 17.0448 8.68574 16.9914C8.41055 16.8417 1.92808 13.2841 0.348132 8.3872C0.347252 8.3872 0.347252 8.38633 0.347252 8.38633C-0.644504 5.30321 0.459792 1.42874 4.02502 0.284605C5.69904 -0.254635 7.52342 -0.0174044 8.99874 0.909632C10.4283 0.00973263 12.3275 -0.238878 13.9681 0.284605C17.5368 1.43049 18.6446 5.30408 17.6538 8.38633C16.1248 13.2272 9.59485 16.8382 9.3179 16.9896C9.21943 17.0439 9.1104 17.0711 9.00138 17.0711Z"
                                       fill="currentColor" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                       d="M14.203 6.67473C13.8627 6.67473 13.5743 6.41474 13.5462 6.07159C13.4882 5.35202 13.0046 4.7445 12.3162 4.52302C11.9689 4.41097 11.779 4.04068 11.8906 3.69666C12.0041 3.35175 12.3724 3.16442 12.7206 3.27297C13.919 3.65901 14.7586 4.71561 14.8615 5.96479C14.8905 6.32632 14.6206 6.64322 14.2575 6.6721C14.239 6.67385 14.2214 6.67473 14.203 6.67473Z"
                                       fill="currentColor" />
                                 </svg>
                                 <span class="tp-product-tooltip tp-product-tooltip-right">Add To Wishlist</span>
                              </button>
                              <button type="button" class="tp-product-action-btn-2 tp-product-add-to-compare-btn">
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
                                 <span class="tp-product-tooltip tp-product-tooltip-right">Add To Compare</span>
                              </button>
                           </div>
                        </div> --}}
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
                        <!-- product action -->
                        {{-- <div class="tp-product-action-2 tp-product-action-blackStyle">
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
                                 <span class="tp-product-tooltip tp-product-tooltip-right">Add to Cart</span>
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
                                 <span class="tp-product-tooltip tp-product-tooltip-right">Quick View</span>
                              </button>
                              <button type="button" class="tp-product-action-btn-2 tp-product-add-to-wishlist-btn">
                                 <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                       d="M1.60355 7.98635C2.83622 11.8048 7.7062 14.8923 9.0004 15.6565C10.299 14.8844 15.2042 11.7628 16.3973 7.98985C17.1806 5.55102 16.4535 2.46177 13.5644 1.53473C12.1647 1.08741 10.532 1.35966 9.40484 2.22804C9.16921 2.40837 8.84214 2.41187 8.60476 2.23329C7.41078 1.33952 5.85105 1.07778 4.42936 1.53473C1.54465 2.4609 0.820172 5.55014 1.60355 7.98635ZM9.00138 17.0711C8.89236 17.0711 8.78421 17.0448 8.68574 16.9914C8.41055 16.8417 1.92808 13.2841 0.348132 8.3872C0.347252 8.3872 0.347252 8.38633 0.347252 8.38633C-0.644504 5.30321 0.459792 1.42874 4.02502 0.284605C5.69904 -0.254635 7.52342 -0.0174044 8.99874 0.909632C10.4283 0.00973263 12.3275 -0.238878 13.9681 0.284605C17.5368 1.43049 18.6446 5.30408 17.6538 8.38633C16.1248 13.2272 9.59485 16.8382 9.3179 16.9896C9.21943 17.0439 9.1104 17.0711 9.00138 17.0711Z"
                                       fill="currentColor" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                       d="M14.203 6.67473C13.8627 6.67473 13.5743 6.41474 13.5462 6.07159C13.4882 5.35202 13.0046 4.7445 12.3162 4.52302C11.9689 4.41097 11.779 4.04068 11.8906 3.69666C12.0041 3.35175 12.3724 3.16442 12.7206 3.27297C13.919 3.65901 14.7586 4.71561 14.8615 5.96479C14.8905 6.32632 14.6206 6.64322 14.2575 6.6721C14.239 6.67385 14.2214 6.67473 14.203 6.67473Z"
                                       fill="currentColor" />
                                 </svg>
                                 <span class="tp-product-tooltip tp-product-tooltip-right">Add To Wishlist</span>
                              </button>
                              <button type="button" class="tp-product-action-btn-2 tp-product-add-to-compare-btn">
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
                                 <span class="tp-product-tooltip tp-product-tooltip-right">Add To Compare</span>
                              </button>
                           </div>
                        </div> --}}
                     </div>
                     <div class="tp-product-content-2 pt-15">
                        <div class="tp-product-tag-2">
                           <a href="#">Shoe, </a>
                           <a href="#">Men's</a>
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
                        <!-- product action -->
                        {{-- <div class="tp-product-action-2 tp-product-action-blackStyle">
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
                                 <span class="tp-product-tooltip tp-product-tooltip-right">Add to Cart</span>
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
                                 <span class="tp-product-tooltip tp-product-tooltip-right">Quick View</span>
                              </button>
                              <button type="button" class="tp-product-action-btn-2 tp-product-add-to-wishlist-btn">
                                 <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                       d="M1.60355 7.98635C2.83622 11.8048 7.7062 14.8923 9.0004 15.6565C10.299 14.8844 15.2042 11.7628 16.3973 7.98985C17.1806 5.55102 16.4535 2.46177 13.5644 1.53473C12.1647 1.08741 10.532 1.35966 9.40484 2.22804C9.16921 2.40837 8.84214 2.41187 8.60476 2.23329C7.41078 1.33952 5.85105 1.07778 4.42936 1.53473C1.54465 2.4609 0.820172 5.55014 1.60355 7.98635ZM9.00138 17.0711C8.89236 17.0711 8.78421 17.0448 8.68574 16.9914C8.41055 16.8417 1.92808 13.2841 0.348132 8.3872C0.347252 8.3872 0.347252 8.38633 0.347252 8.38633C-0.644504 5.30321 0.459792 1.42874 4.02502 0.284605C5.69904 -0.254635 7.52342 -0.0174044 8.99874 0.909632C10.4283 0.00973263 12.3275 -0.238878 13.9681 0.284605C17.5368 1.43049 18.6446 5.30408 17.6538 8.38633C16.1248 13.2272 9.59485 16.8382 9.3179 16.9896C9.21943 17.0439 9.1104 17.0711 9.00138 17.0711Z"
                                       fill="currentColor" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                       d="M14.203 6.67473C13.8627 6.67473 13.5743 6.41474 13.5462 6.07159C13.4882 5.35202 13.0046 4.7445 12.3162 4.52302C11.9689 4.41097 11.779 4.04068 11.8906 3.69666C12.0041 3.35175 12.3724 3.16442 12.7206 3.27297C13.919 3.65901 14.7586 4.71561 14.8615 5.96479C14.8905 6.32632 14.6206 6.64322 14.2575 6.6721C14.239 6.67385 14.2214 6.67473 14.203 6.67473Z"
                                       fill="currentColor" />
                                 </svg>
                                 <span class="tp-product-tooltip tp-product-tooltip-right">Add To Wishlist</span>
                              </button>
                              <button type="button" class="tp-product-action-btn-2 tp-product-add-to-compare-btn">
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
                                 <span class="tp-product-tooltip tp-product-tooltip-right">Add To Compare</span>
                              </button>
                           </div>
                        </div> --}}
                     </div>
                     <div class="tp-product-content-2 pt-15">
                        <div class="tp-product-tag-2">
                           <a href="#">Bag, </a>
                           <a href="#">Wonder</a>
                        </div>
                        <h3 class="tp-product-title-2">
                           <a href="product-details.html">Tommy Hilfiger Women’s Jaden</a>
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
                     <a href="shop.html" class="tp-btn tp-btn-border tp-btn-border-sm">Shop All Product</a>
                  </div>
               </div>
            </div>
         </div>
      </section>
      <!-- best seller area end -->

      <!-- testimonial area start -->
      <section class="tp-testimonial-area grey-bg-7 pt-130 pb-135">
         <div class="container">
            <div class="row justify-content-center">
               <div class="col-xl-12">
                  <div class="tp-testimonial-slider p-relative z-index-1">
                     <div class="tp-testimonial-shape">
                        <span class="tp-testimonial-shape-gradient"></span>
                     </div>
                     <h3 class="tp-testimonial-section-title text-center">The Review Are In</h3>
                     <div class="row justify-content-center">
                        <div class="col-xl-8 col-lg-8 col-md-10">
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
                                       <p>“ How you use the city or town name is up to you. All results may be freely
                                          used in any work.”</p>
                                    </div>
                                    <div
                                       class="tp-testimonial-user-wrapper d-flex align-items-center justify-content-center">
                                       <div class="tp-testimonial-user d-flex align-items-center">
                                          <div class="tp-testimonial-avater mr-10">
                                             <img src="{{asset('assets2/img/users/user-2.jpg')}}" alt="">
                                          </div>
                                          <div class="tp-testimonial-user-info tp-testimonial-user-translate">
                                             <h3 class="tp-testimonial-user-title">Theodore Handle</h3>
                                             <span class="tp-testimonial-designation">CO Founder</span>
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
                                       <p>“Very happy with our choice to take our daughter to Brave care. The entire
                                          team was great! Thank you!”</p>
                                    </div>
                                    <div
                                       class="tp-testimonial-user-wrapper d-flex align-items-center justify-content-center">
                                       <div class="tp-testimonial-user d-flex align-items-center">
                                          <div class="tp-testimonial-avater mr-10">
                                             <img src="{{asset('assets2/img/users/user-3.jpg')}}" alt="">
                                          </div>
                                          <div class="tp-testimonial-user-info tp-testimonial-user-translate">
                                             <h3 class="tp-testimonial-user-title">Shahnewaz Sakil</h3>
                                             <span class="tp-testimonial-designation">UI/UX Designer</span>
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
                                       <p>“Thanks for all your efforts and teamwork over the last several months! Thank
                                          you so much”</p>
                                    </div>
                                    <div
                                       class="tp-testimonial-user-wrapper d-flex align-items-center justify-content-center">
                                       <div class="tp-testimonial-user d-flex align-items-center">
                                          <div class="tp-testimonial-avater mr-10">
                                             <img src="{{asset('assets2/img/users/user-4.jpg')}}" alt="">
                                          </div>
                                          <div class="tp-testimonial-user-info tp-testimonial-user-translate">
                                             <h3 class="tp-testimonial-user-title">James Dopli</h3>
                                             <span class="tp-testimonial-designation">Developer</span>
                                          </div>
                                       </div>
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


      {{-- <div class="modal fade tp-product-modal tp-product-modal-styleDarkRed" id="producQuickViewModal"
         tabindex="-1" aria-labelledby="producQuickViewModal" aria-hidden="true">
         <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
               <div class="tp-product-modal-content d-lg-flex align-items-start">
                  <button type="button" class="tp-product-modal-close-btn" data-bs-toggle="modal"
                     data-bs-target="#producQuickViewModal"><i class="fa-regular fa-xmark"></i></button>
                  <div class="tp-product-details-thumb-wrapper tp-tab d-sm-flex">
                     <nav>
                        <div class="nav nav-tabs flex-sm-column " id="productDetailsNavThumb" role="tablist">
                           <button class="nav-link active" id="nav-1-tab" data-bs-toggle="tab" data-bs-target="#nav-1"
                              type="button" role="tab" aria-controls="nav-1" aria-selected="true">
                              <img src="{{asset('assets2/img/product/details/3/nav/product-details-nav-1.jpg')}}"
                                 alt="">
                           </button>
                           <button class="nav-link" id="nav-2-tab" data-bs-toggle="tab" data-bs-target="#nav-2"
                              type="button" role="tab" aria-controls="nav-2" aria-selected="false">
                              <img src="{{asset('assets2/img/product/details/3/nav/product-details-nav-2.jpg')}}"
                                 alt="">
                           </button>
                           <button class="nav-link" id="nav-3-tab" data-bs-toggle="tab" data-bs-target="#nav-3"
                              type="button" role="tab" aria-controls="nav-3" aria-selected="false">
                              <img src="{{asset('assets2/img/product/details/3/nav/product-details-nav-3.jpg')}}"
                                 alt="">
                           </button>
                           <button class="nav-link" id="nav-4-tab" data-bs-toggle="tab" data-bs-target="#nav-4"
                              type="button" role="tab" aria-controls="nav-4" aria-selected="false">
                              <img src="{{asset('assets2/img/product/details/3/nav/product-details-nav-4.jpg')}}"
                                 alt="">
                           </button>
                        </div>
                     </nav>
                     <div class="tab-content m-img" id="productDetailsNavContent">
                        <div class="tab-pane fade show active" id="nav-1" role="tabpanel" aria-labelledby="nav-1-tab"
                           tabindex="0">
                           <div class="tp-product-details-nav-main-thumb">
                              <img src="{{asset('assets2/img/product/details/3/main/product-details-main-1.jpg')}}"
                                 alt="">
                           </div>
                        </div>
                        <div class="tab-pane fade" id="nav-2" role="tabpanel" aria-labelledby="nav-2-tab" tabindex="0">
                           <div class="tp-product-details-nav-main-thumb">
                              <img src="{{asset('assets2/img/product/details/3/main/product-details-main-2.jpg')}}"
                                 alt="">
                           </div>
                        </div>
                        <div class="tab-pane fade" id="nav-3" role="tabpanel" aria-labelledby="nav-3-tab" tabindex="0">
                           <div class="tp-product-details-nav-main-thumb">
                              <img src="{{asset('assets2/img/product/details/3/main/product-details-main-3.jpg')}}"
                                 alt="">
                           </div>
                        </div>
                        <div class="tab-pane fade" id="nav-4" role="tabpanel" aria-labelledby="nav-4-tab" tabindex="0">
                           <div class="tp-product-details-nav-main-thumb">
                              <img src="{{asset('assets2/img/product/details/3/main/product-details-main-4.jpg')}}"
                                 alt="">
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="tp-product-details-wrapper">
                     <div class="tp-product-details-category">
                        <span>Shirt, Women</span>
                     </div>
                     <h3 class="tp-product-details-title">Brown Gown for Women</h3>

                     <!-- inventory details -->
                     <div class="tp-product-details-inventory d-flex align-items-center mb-10">
                        <div class="tp-product-details-stock mb-10">
                           <span>In Stock</span>
                        </div>
                        <div class="tp-product-details-rating-wrapper d-flex align-items-center mb-10">
                           <div class="tp-product-details-rating">
                              <span><i class="fa-solid fa-star"></i></span>
                              <span><i class="fa-solid fa-star"></i></span>
                              <span><i class="fa-solid fa-star"></i></span>
                              <span><i class="fa-solid fa-star"></i></span>
                              <span><i class="fa-solid fa-star"></i></span>
                           </div>
                           <div class="tp-product-details-reviews">
                              <span>(36 Reviews)</span>
                           </div>
                        </div>
                     </div>
                     <p>A Screen Everyone Will Love: Whether your family is streaming or video chatting with friends
                        tablet A8... <span>See more</span></p>

                     <!-- price -->
                     <div class="tp-product-details-price-wrapper mb-20">
                        <span class="tp-product-details-price old-price">$320.00</span>
                        <span class="tp-product-details-price new-price">$236.00</span>
                     </div>

                     <!-- variations -->
                     <div class="tp-product-details-variation">
                        <!-- single item -->
                        <div class="tp-product-details-variation-item">
                           <h4 class="tp-product-details-variation-title">Color :</h4>
                           <div class="tp-product-details-variation-list">
                              <button type="button" class="color tp-color-variation-btn">
                                 <span data-bg-color="#F8B655"></span>
                                 <span class="tp-color-variation-tootltip">Yellow</span>
                              </button>
                              <button type="button" class="color tp-color-variation-btn active">
                                 <span data-bg-color="#CBCBCB"></span>
                                 <span class="tp-color-variation-tootltip">Gray</span>
                              </button>
                              <button type="button" class="color tp-color-variation-btn">
                                 <span data-bg-color="#494E52"></span>
                                 <span class="tp-color-variation-tootltip">Black</span>
                              </button>
                              <button type="button" class="color tp-color-variation-btn">
                                 <span data-bg-color="#B4505A"></span>
                                 <span class="tp-color-variation-tootltip">Brown</span>
                              </button>
                           </div>
                        </div>
                     </div>

                     <!-- actions -->
                     <div class="tp-product-details-action-wrapper">
                        <h3 class="tp-product-details-action-title">Quantity</h3>
                        <div class="tp-product-details-action-item-wrapper d-sm-flex align-items-center">
                           <div class="tp-product-details-quantity">
                              <div class="tp-product-quantity mb-15 mr-15">
                                 <span class="tp-cart-minus">
                                    <svg width="11" height="2" viewBox="0 0 11 2" fill="none"
                                       xmlns="http://www.w3.org/2000/svg">
                                       <path d="M1 1H10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                          stroke-linejoin="round" />
                                    </svg>
                                 </span>
                                 <input class="tp-cart-input" type="text" value="1">
                                 <span class="tp-cart-plus">
                                    <svg width="11" height="12" viewBox="0 0 11 12" fill="none"
                                       xmlns="http://www.w3.org/2000/svg">
                                       <path d="M1 6H10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                          stroke-linejoin="round" />
                                       <path d="M5.5 10.5V1.5" stroke="currentColor" stroke-width="1.5"
                                          stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                 </span>
                              </div>
                           </div>
                           <div class="tp-product-details-add-to-cart mb-15 w-100">
                              <button class="tp-product-details-add-to-cart-btn w-100">Add To Cart</button>
                           </div>
                        </div>
                        <button class="tp-product-details-buy-now-btn w-100">Buy Now</button>
                     </div>
                     <div class="tp-product-details-action-sm">
                        <button type="button" class="tp-product-details-action-sm-btn">
                           <svg width="14" height="16" viewBox="0 0 14 16" fill="none"
                              xmlns="http://www.w3.org/2000/svg">
                              <path d="M1 3.16431H10.8622C12.0451 3.16431 12.9999 4.08839 12.9999 5.23315V7.52268"
                                 stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                                 stroke-linejoin="round" />
                              <path d="M3.25177 0.985168L1 3.16433L3.25177 5.34354" stroke="currentColor"
                                 stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                                 stroke-linejoin="round" />
                              <path d="M12.9999 12.5983H3.13775C1.95486 12.5983 1 11.6742 1 10.5295V8.23993"
                                 stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                                 stroke-linejoin="round" />
                              <path d="M10.748 14.7774L12.9998 12.5983L10.748 10.4191" stroke="currentColor"
                                 stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"
                                 stroke-linejoin="round" />
                           </svg>
                           Compare
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
                           Add Wishlist
                        </button>
                        <button type="button" class="tp-product-details-action-sm-btn">
                           <svg width="17" height="16" viewBox="0 0 17 16" fill="none"
                              xmlns="http://www.w3.org/2000/svg">
                              <path
                                 d="M8.575 12.6927C8.775 12.6927 8.94375 12.6249 9.08125 12.4895C9.21875 12.354 9.2875 12.1878 9.2875 11.9907C9.2875 11.7937 9.21875 11.6275 9.08125 11.492C8.94375 11.3565 8.775 11.2888 8.575 11.2888C8.375 11.2888 8.20625 11.3565 8.06875 11.492C7.93125 11.6275 7.8625 11.7937 7.8625 11.9907C7.8625 12.1878 7.93125 12.354 8.06875 12.4895C8.20625 12.6249 8.375 12.6927 8.575 12.6927ZM8.55625 5.0638C8.98125 5.0638 9.325 5.17771 9.5875 5.40553C9.85 5.63335 9.98125 5.92582 9.98125 6.28294C9.98125 6.52924 9.90625 6.77245 9.75625 7.01258C9.60625 7.25272 9.3625 7.5144 9.025 7.79763C8.7 8.08087 8.44063 8.3795 8.24688 8.69352C8.05313 9.00754 7.95625 9.29385 7.95625 9.55246C7.95625 9.68792 8.00938 9.79567 8.11563 9.87572C8.22188 9.95576 8.34375 9.99578 8.48125 9.99578C8.63125 9.99578 8.75625 9.94653 8.85625 9.84801C8.95625 9.74949 9.01875 9.62635 9.04375 9.47857C9.08125 9.23228 9.16562 9.0137 9.29688 8.82282C9.42813 8.63195 9.63125 8.42568 9.90625 8.20402C10.2812 7.89615 10.5531 7.58829 10.7219 7.28042C10.8906 6.97256 10.975 6.62775 10.975 6.246C10.975 5.59333 10.7594 5.06996 10.3281 4.67589C9.89688 4.28183 9.325 4.0848 8.6125 4.0848C8.1375 4.0848 7.7 4.17716 7.3 4.36187C6.9 4.54659 6.56875 4.81751 6.30625 5.17463C6.20625 5.31009 6.16563 5.44863 6.18438 5.59025C6.20313 5.73187 6.2625 5.83962 6.3625 5.91351C6.5 6.01202 6.64688 6.04281 6.80313 6.00587C6.95937 5.96892 7.0875 5.88272 7.1875 5.74726C7.35 5.5256 7.54688 5.35627 7.77813 5.23929C8.00938 5.1223 8.26875 5.0638 8.55625 5.0638ZM8.5 15.7775C7.45 15.7775 6.46875 15.5897 5.55625 15.2141C4.64375 14.8385 3.85 14.3182 3.175 13.6532C2.5 12.9882 1.96875 12.2062 1.58125 11.3073C1.19375 10.4083 1 9.43547 1 8.38873C1 7.35431 1.19375 6.38762 1.58125 5.48866C1.96875 4.58969 2.5 3.80772 3.175 3.14273C3.85 2.47775 4.64375 1.95438 5.55625 1.57263C6.46875 1.19088 7.45 1 8.5 1C9.5375 1 10.5125 1.19088 11.425 1.57263C12.3375 1.95438 13.1313 2.47775 13.8063 3.14273C14.4813 3.80772 15.0156 4.58969 15.4094 5.48866C15.8031 6.38762 16 7.35431 16 8.38873C16 9.43547 15.8031 10.4083 15.4094 11.3073C15.0156 12.2062 14.4813 12.9882 13.8063 13.6532C13.1313 14.3182 12.3375 14.8385 11.425 15.2141C10.5125 15.5897 9.5375 15.7775 8.5 15.7775ZM8.5 14.6692C10.2625 14.6692 11.7656 14.0534 13.0094 12.822C14.2531 11.5905 14.875 10.1128 14.875 8.38873C14.875 6.6647 14.2531 5.18695 13.0094 3.95549C11.7656 2.72404 10.2625 2.10831 8.5 2.10831C6.7125 2.10831 5.20312 2.72404 3.97188 3.95549C2.74063 5.18695 2.125 6.6647 2.125 8.38873C2.125 10.1128 2.74063 11.5905 3.97188 12.822C5.20312 14.0534 6.7125 14.6692 8.5 14.6692Z"
                                 fill="currentColor" stroke="currentColor" stroke-width="0.3" />
                           </svg>
                           Ask a question
                        </button>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div> --}}

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

<!-- Mirrored from html.storebuild.shop/shofy-prv/shofy/index-2.html by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 18 May 2025 07:20:23 GMT -->

</html>