<div id="loading">
    <div id="loading-center">
        <div id="loading-center-absolute">
            <!-- loading content here -->
            <div class="tp-preloader-content">
                <div class="tp-preloader-logo">
                    <div class="tp-preloader-circle">
                        <svg width="190" height="190" viewBox="0 0 380 380" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <circle stroke="#D9D9D9" cx="190" cy="190" r="180" stroke-width="6"
                                stroke-linecap="round"></circle>
                            <circle stroke="red" cx="190" cy="190" r="180" stroke-width="6"
                                stroke-linecap="round"></circle>
                        </svg>
                    </div>
                    <img src="{{ asset('assets2/img/logo/preloader/preloader-icon.svg') }}" alt="">
                </div>
                <h3 class="tp-preloader-title">Aurora</h3>
                <p class="tp-preloader-subtitle">Loading</p>
            </div>
        </div>
    </div>
</div>
<!-- pre loader area end -->

<!-- back to top start -->
<div class="back-to-top-wrapper">
    <button id="back_to_top" type="button" class="back-to-top-btn">
        <svg width="12" height="7" viewBox="0 0 12 7" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M11 6L6 1L1 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                stroke-linejoin="round" />
        </svg>
    </button>
</div>
<!-- search area start -->
<section class="tp-search-area tp-search-style-secondary">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <div class="tp-search-form">
                    <div class="tp-search-close text-center mb-20">
                        <button class="tp-search-close-btn tp-search-close-btn"></button>
                    </div>
                    <form action="#">
                        <div class="tp-search-input mb-10">
                            <input type="text" placeholder="Tìm kiếm sản phẩm...">
                            <button type="submit"><i class="flaticon-search-1"></i></button>
                        </div>
                        <div class="tp-search-category">
                            <span>Tìm kiếm theo : </span>
                            <a href="#">Nam, </a>
                            <a href="#">Nữ, </a>
                            <a href="#">Trẻ em, </a>
                            <a href="#">Áo, </a>
                            <a href="#">Quần</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- search area end -->

@include('client.shopping-cart.mini-cart')

<header>
    <div class="tp-header-area tp-header-style-darkRed tp-header-height">
        <!-- header bottom start -->
        <div id="header-sticky" class="tp-header-bottom-2 tp-header-sticky">
            <div class="container">
                <div class="tp-mega-menu-wrapper p-relative">
                    <div class="row align-items-center">
                        <div class="col-xl-2 col-lg-5 col-md-5 col-sm-4 col-6">
                            <div class="logo">
                                <a href="{{ route('home') }}">
                                    <img src="{{ asset('assets2/img/logo/logo.svg') }}" alt="logo">
                                </a>
                            </div>
                        </div>
                        <div class="col-xl-5 d-none d-xl-block">
                            <div class="main-menu menu-style-2">
                                <nav class="tp-main-menu-content">
                                    <ul>
                                        <li class="has-mega-menu">
                                            <a href="{{ route('home') }}">Trang chủ</a>
                                        </li>
                                        <li class="has-dropdown has-mega-menu">
                                            <a href="shop.html">Cửa hàng</a>
                                            <div class="shop-mega-menu tp-submenu tp-mega-menu">
                                                <div class="row">
                                                    <div class="col-lg-4">
                                                        <div class="shop-mega-menu-list">
                                                            <a href="shop.html" class="shop-mega-menu-title">Trang cửa
                                                                hàng</a>
                                                            <ul>
                                                                <li><a href="shop-category.html">Danh mục lưới</a></li>
                                                                <li><a href="shop.html">Bố cục lưới</a></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div class="shop-mega-menu-list">
                                                            <a href="shop.html" class="shop-mega-menu-title">Tính
                                                                năng</a>
                                                            <ul>
                                                                <li><a href="shop-filter-dropdown.html">Bộ lọc
                                                                        dropdown</a></li>
                                                                <li><a href="shop-filter-offcanvas.html">Bộ lọc
                                                                        Offcanvas</a></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div class="shop-mega-menu-img">
                                                            <img src="{{ asset('assets2/img/menu/product/menu-product-img-1.jpg') }}"
                                                                alt="">
                                                            <div class="shop-mega-menu-btn">
                                                                <a href="shop-category.html"
                                                                    class="tp-menu-showcase-btn tp-menu-showcase-btn-2">Điện
                                                                    thoại</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="has-dropdown has-mega-menu">
                                            <a href="">Sản phẩm</a>
                                            <ul class="tp-submenu tp-mega-menu mega-menu-style-2">
                                                <li class="has-dropdown">
                                                    <a href="shop.html" class="mega-menu-title">Trang cửa hàng</a>
                                                    <ul class="tp-submenu">
                                                        <li><a href="shop-category.html">Chỉ danh mục</a></li>
                                                        <li><a href="shop-filter-offcanvas.html">Cửa hàng lưới</a></li>
                                                    </ul>
                                                </li>
                                                <li class="has-dropdown">
                                                    <a href="shop.html" class="mega-menu-title">Thương mại điện tử</a>
                                                    <ul class="tp-submenu">
                                                        <li><a href="">Giỏ hàng</a></li>
                                                        <li><a href="order.html">Theo dõi đơn hàng</a></li>
                                                    </ul>
                                                </li>
                                                <li class="has-dropdown">
                                                    <a href="shop.html" class="mega-menu-title">Trang khác</a>
                                                    <ul class="tp-submenu">
                                                        <li><a href="about.html">Giới thiệu</a></li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </li>
                                        <li class="has-dropdown">
                                            <a href="blog.html">Blog</a>
                                            <ul class="tp-submenu">
                                                <li><a href="blog.html">Blog tiêu chuẩn</a></li>
                                                <li><a href="blog-grid.html">Blog lưới</a></li>
                                                <li><a href="blog-list.html">Blog danh sách</a></li>
                                                <li><a href="blog-details.html">Chi tiết blog</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="{{ route('contact') }}">Liên hệ</a></li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                        <div class="col-xl-5 col-lg-7 col-md-7 col-sm-8 col-6">
                            <div class="tp-header-bottom-right d-flex align-items-center justify-content-end pl-30">
                                <div class="tp-header-search-2 d-none d-sm-block">
                                    <form action="#">
                                        <input type="text" placeholder="Tìm kiếm sản phẩm...">
                                        <button type="submit">
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M9 17C13.4183 17 17 13.4183 17 9C17 4.58172 13.4183 1 9 1C4.58172 1 1 4.58172 1 9C1 13.4183 4.58172 17 9 17Z"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path d="M18.9999 19L14.6499 14.65" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                                <div class="tp-header-action d-flex align-items-center ml-30">
                                    <div class="tp-header-action-item d-none d-lg-block">
                                        <a href="" class="tp-header-action-btn">
                                            <svg width="22" height="20" viewBox="0 0 22 20" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M11.239 18.8538C13.4096 17.5179 15.4289 15.9456 17.2607 14.1652C18.5486 12.8829 19.529 11.3198 20.1269 9.59539C21.2029 6.25031 19.9461 2.42083 16.4289 1.28752C14.5804 0.692435 12.5616 1.03255 11.0039 2.20148C9.44567 1.03398 7.42754 0.693978 5.57894 1.28752C2.06175 2.42083 0.795919 6.25031 1.87187 9.59539C2.46978 11.3198 3.45021 12.8829 4.73806 14.1652C6.56988 15.9456 8.58917 17.5179 10.7598 18.8538L10.9949 19L11.239 18.8538Z"
                                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path
                                                    d="M7.26062 5.05302C6.19531 5.39332 5.43839 6.34973 5.3438 7.47501"
                                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>
                                            <span class="tp-header-action-badge">0
                                                {{-- {{ Auth::check() ? Auth::user()->wishlist()->count() : 0 }} --}}
                                            </span>
                                        </a>
                                    </div>
                                    <div class="tp-header-action-item">
                                        <button class="tp-header-action-btn cartmini-open-btn">
                                            <svg width="21" height="22" viewBox="0 0 21 22" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M6.48626 20.5H14.8341C17.9004 20.5 20.2528 19.3924 19.5847 14.9348L18.8066 8.89359C18.3947 6.66934 16.976 5.81808 15.7311 5.81808H5.55262C4.28946 5.81808 2.95308 6.73341 2.4771 8.89359L1.69907 14.9348C1.13157 18.889 3.4199 20.5 6.48626 20.5Z"
                                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path
                                                    d="M6.34902 5.5984C6.34902 3.21232 8.28331 1.27803 10.6694 1.27803V1.27803C11.8184 1.27316 12.922 1.72619 13.7362 2.53695C14.5504 3.3477 15.0081 4.44939 15.0081 5.5984V5.5984"
                                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path d="M7.70365 10.1018H7.74942" stroke="currentColor"
                                                    stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path d="M13.5343 10.1018H13.5801" stroke="currentColor"
                                                    stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>
                                            <span class="tp-header-action-badge cart-count">
                                                @if(Auth::check())
                                                    @php
                                                        $cart = \App\Models\Cart::where('user_id', Auth::id())
                                                            ->where('status', 'pending')
                                                            ->with('items')
                                                            ->first();
                                                        $cartCount = $cart ? $cart->items->count() : 0;
                                                    @endphp
                                                    {{ $cartCount }}
                                                @else
                                                    0
                                                @endif
                                            </span>
                                        </button>
                                    </div>
                                    <div class="tp-header-action-item has-dropdown">
                                        <a href="#" class="tp-header-account-toggle">
                                            <i class="fa fa-user"></i>
                                            @if (Auth::check())
                                                {{ Auth::user()->fullname }}
                                            @else
                                                Tài khoản
                                            @endif
                                        </a>
                                        <ul class="tp-submenu tp-account-menu">
                                            @if (Auth::check())
                                                <li class="tp-account-menu-header">
                                                    <span class="tp-account-menu-name">{{ Auth::user()->fullname }}</span>
                                                </li>
                                                @if (Auth::user()->role === 'admin')
                                                    <li><a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-screwdriver-wrench"></i> Trang quản trị</a></li>
                                                @endif
                                                <li><a href="{{ route('showProfile') }}"><i class="fa-solid fa-user"></i> Hồ sơ</a></li>
                                                <li><a href=""><i class="fa-solid fa-heart"></i> Yêu thích</a></li>
                                                <li><a href="{{ route('shopping-cart.index') }}"><i class="fa-solid fa-cart-shopping"></i> Giỏ hàng</a></li>
                                                <li><a href=""><i class="fa-solid fa-box"></i> Đơn hàng của tôi</a></li>
                                                <li class="tp-account-menu-divider"></li>
                                                <li>
                                                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                        <i class="fa-solid fa-arrow-right-from-bracket"></i> Đăng xuất
                                                    </a>
                                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                                        @csrf
                                                    </form>
                                                </li>
                                            @else
                                                <li><a href="{{ route('login') }}"><i class="fa-solid fa-arrow-right-to-bracket"></i> Đăng nhập / Đăng ký</a></li>
                                            @endif
                                        </ul>
                                    </div>
                                    <div class="tp-header-action-item tp-header-hamburger mr-20 d-xl-none">
                                        <button type="button" class="tp-offcanvas-open-btn">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="16"
                                                viewBox="0 0 30 16">
                                                <rect x="10" width="20" height="2" fill="currentColor" />
                                                <rect x="5" y="7" width="25" height="2"
                                                    fill="currentColor" />
                                                <rect x="10" y="14" width="20" height="2"
                                                    fill="currentColor" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<style>
    .tp-header-action-item.has-dropdown {
        position: relative;
    }

    .tp-header-action-item.has-dropdown>.tp-submenu {
        position: absolute;
        right: 0;
        top: 100%;
        min-width: 180px;
        background: #fff;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        border-radius: 4px;
        padding: 10px 0;
        z-index: 100;
        opacity: 0;
        visibility: hidden;
        transition: all 0.2s;
    }

    .tp-header-action-item.has-dropdown:hover>.tp-submenu,
    .tp-header-action-item.has-dropdown:focus-within>.tp-submenu {
        opacity: 1;
        visibility: visible;
    }

    .tp-header-action-item.has-dropdown>.tp-submenu li {
        list-style: none;
        padding: 0 20px;
    }

    .tp-header-action-item.has-dropdown>.tp-submenu li a,
    .tp-header-action-item.has-dropdown>.tp-submenu li span {
        display: block;
        color: #222;
        padding: 6px 0;
        text-decoration: none;
        font-size: 15px;
    }

    .tp-header-action-item.has-dropdown>.tp-submenu li a:hover {
        color: #c0392b;
    }

    .tp-account-menu {
        width: 240px;
        padding: 10px 0;
    }
    .tp-account-menu-header {
        padding: 10px 20px;
        border-bottom: 1px solid #f0f0f0;
        margin-bottom: 10px;
        line-height: 1.4;
    }
    .tp-account-menu-name {
        display: block;
        font-weight: 600;
        color: #1c1c1e;
    }
    .tp-account-menu-email {
        display: block;
        font-size: 0.85rem;
        color: #777;
    }
    .tp-account-menu li a {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 1rem;
        padding: 8px 20px;
    }
    .tp-account-menu li a i {
        width: 16px;
        text-align: center;
        color: #888;
    }
    .tp-account-menu-divider {
        height: 1px;
        background-color: #f0f0f0;
        margin: 10px 0;
    }
    .tp-account-menu li > span {
        display: none; /* Hide the old username span */
    }
    .tp-header-action-item.has-dropdown .tp-account-menu {
        left: 50%;
        transform: translateX(-50%);
    }
</style>

{{-- Mini cart --}}
@include('client.shopping-cart.mini-cart')
