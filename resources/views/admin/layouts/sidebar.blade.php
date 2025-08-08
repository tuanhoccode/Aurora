<!-- Sidebar -->
<aside class="sidebar">
    <div class="d-flex align-items-center justify-content-center mb-4 px-4">
        <h4 class="text-white m-0">Bảng Quản Trị</h4>
    </div>
    <ul class="nav flex-column mb-auto">

        {{-- Chính --}}
        <li class="sidebar-heading">Chính</li>
        <li>
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                href="{{ route('admin.dashboard') }}">
                <span class="nav-icon"><i class="fas fa-tachometer-alt"></i></span> Bảng điều khiển
            </a>
        </li>
        <li>
            <a class="nav-link {{ request()->routeIs('admin.notifications*') ? 'active' : '' }}"
                href="{{ route('admin.dashboard') }}">
                <span class="nav-icon"><i class="fas fa-bell"></i></span> Thông báo
            </a>
        </li>

        <hr>

        {{-- Danh mục --}}
        <li class="sidebar-heading">Danh mục</li>
        <li>
            <a class="nav-link {{ request()->routeIs('admin.products*') ? 'active' : '' }}"
                href="{{ route('admin.products.index') }}">
                <span class="nav-icon"><i class="fas fa-box"></i></span> Sản phẩm
            </a>
        </li>
        <li>
            <a class="nav-link {{ request()->routeIs('admin.categories*') ? 'active' : '' }}"
                href="{{ route('admin.categories.index') }}">
                <span class="nav-icon"><i class="fas fa-tags"></i></span> Danh mục
            </a>
        </li>
        @if(Auth::user()->role === 'admin')
            <li>
                <a class="nav-link {{ request()->routeIs('admin.brands*') ? 'active' : '' }}"
                    href="{{ route('admin.brands.index') }}">
                    <span class="nav-icon"><i class="fas fa-trademark"></i></span> Thương hiệu
                </a>
            </li>
            <li>
                <a class="nav-link {{ request()->routeIs('admin.attributes.*') ? 'active' : '' }}" href="{{ route('admin.attributes.index') }}">
                    <span class="nav-icon"><i class="fas fa-list-alt"></i></span> Thuộc tính sản phẩm
                </a>
            </li>
        @endif

        <hr>

        {{-- Bán hàng --}}
        <li class="sidebar-heading">Bán hàng</li>
        <li>
            <a class="nav-link {{ request()->routeIs('admin.orders*') ? 'active' : '' }}"
                href="{{ route('admin.orders.index') }}">
                <span class="nav-icon"><i class="fas fa-shopping-cart"></i></span> Đơn hàng
            </a>
        </li>
        @if(Auth::user()->role === 'admin')
            <li>
                <a class="nav-link {{ request()->routeIs('admin.coupons*') ? 'active' : '' }}"
                    href="{{ route('admin.coupons.index') }}">
                    <span class="nav-icon"><i class="fas fa-ticket-alt"></i></span> Mã giảm giá
                </a>
            </li>
        @endif
        <li>
            <a class="nav-link {{ request()->routeIs('admin.roles*') ? 'active' : '' }}"
                href="{{ route('admin.dashboard') }}">
                <span class="nav-icon"><i class="fas fa-user-lock"></i></span> Vai trò & Phân quyền
            </a>
        </li>

        <hr>
        @if(Auth::user()->role === 'admin')
            {{-- Người dùng --}}
            <li class="sidebar-heading">Người dùng</li>
            <li>
                <a class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}"
                    href="{{ route('admin.users.index') }}">
                    <span class="nav-icon"><i class="fas fa-users"></i></span> Người dùng
                </a>
            </li>

        @endif
            <hr>

            {{-- Nội dung --}}
            <li class="sidebar-heading">Nội dung</li>
            <li>
                <a class="nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}"
                    href="{{ route('admin.reviews.comments') }}">
                    <span class="nav-icon position-relative"><i class="fas fa-comments"></i>
                        @if($hasPendingFeedbacks)
                            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                                <span class="visually-hidden">Có đánh giá/bình luận mới</span>
                            </span>
                        @endif
                    </span>
                     Đánh giá sản phẩm
                </a>
            </li>
        @if(Auth::user()->role === 'admin')
            <li>
                <a class="nav-link {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}"
                    href="{{ route('admin.banners.index') }}">
                    <span class="nav-icon"><i class="fas fa-image"></i></span> Banner
                </a>
            </li>
        @endif
        <li>
            <a class="nav-link {{ request()->routeIs('admin.pages*') ? 'active' : '' }}"
                href="{{ route('admin.dashboard') }}">
                <span class="nav-icon"><i class="fas fa-file-alt"></i></span> Trang CMS
            </a>
        </li>
        {{-- <li>
            <a class="nav-link {{ request()->routeIs('admin.product-images.all') ? 'active' : '' }}"
                href="{{ route('admin.product-images.all') }}">
                <span class="nav-icon">
                    <i class="fas fa-images"></i>
                </span>
                Quản lý ảnh
            </a>
        </li> --}}
        <li>
            <a class="nav-link {{ request()->routeIs('admin.stocks.index') ? 'active' : '' }}"
                href="{{ route('admin.stocks.index') }}">
                <span class="nav-icon"><i class="fas fa-boxes"></i></span> Tồn kho
            </a>
        </li>

        <hr>

        {{-- Cài đặt --}}
        <li class="sidebar-heading">Cài đặt</li>
        <li>
            <a class="nav-link {{ request()->routeIs('admin.payments*') ? 'active' : '' }}"
                href="{{ route('admin.dashboard') }}">
                <span class="nav-icon"><i class="fas fa-credit-card"></i></span> Cổng thanh toán
            </a>
        </li>
        <li>
            <a class="nav-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}"
                href="{{ route('admin.dashboard') }}">
                <span class="nav-icon"><i class="fas fa-cogs"></i></span> Thiết lập hệ thống
            </a>
        </li>

    </ul>
</aside>
