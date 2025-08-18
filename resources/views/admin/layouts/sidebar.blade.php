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
        @if (Auth::user()->role === 'admin')
            <li>
                <a class="nav-link {{ request()->routeIs('admin.brands*') ? 'active' : '' }}"
                    href="{{ route('admin.brands.index') }}">
                    <span class="nav-icon"><i class="fas fa-trademark"></i></span> Thương hiệu
                </a>
            </li>
            <li>
                <a class="nav-link {{ request()->routeIs('admin.attributes.*') ? 'active' : '' }}"
                    href="{{ route('admin.attributes.index') }}">
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
        <li>
            <a class="nav-link {{ request()->routeIs('admin.refunds*') ? 'active' : '' }}"
                href="{{ route('admin.refunds.index') }}">
                <span class="nav-icon"><i class="fas fa-undo"></i></span> Hoàn tiền
            </a>
        </li>
        @if (Auth::user()->role === 'admin')
            <li>
                <a class="nav-link {{ request()->routeIs('admin.coupons*') ? 'active' : '' }}"
                    href="{{ route('admin.coupons.index') }}">
                    <span class="nav-icon"><i class="fas fa-ticket-alt"></i></span> Mã giảm giá
                </a>
            </li>
        @endif

        <hr>
        @if (Auth::user()->role === 'admin')
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
                    @if ($hasPendingFeedbacks)
                        <span
                            class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                            <span class="visually-hidden">Có đánh giá/bình luận mới</span>
                        </span>
                    @endif
                </span>
                <style>
                    .sidebar {
                        /* Các style hiện tại */
                    }

                    /* Hiệu ứng chuyển động mượt mà cho submenu */
                    .submenu {
                        max-height: 0;
                        overflow: hidden;
                        transition: max-height 0.3s ease-in-out;
                        list-style: none;
                        margin: 0;
                        padding: 0 0 0 1.5rem;
                    }

                    .has-submenu.open>.submenu {
                        max-height: 200px;
                        /* Điều chỉnh theo chiều cao thực tế của menu con */
                    }

                    /* Hiệu ứng mượt mà cho icon */
                    .fa-chevron-down,
                    .fa-chevron-up {
                        transition: transform 0.3s ease-in-out;
                    }

                    .has-submenu.open .fa-chevron-down {
                        transform: rotate(180deg);
                    }

                    .has-submenu .fa-chevron-up {
                        transform: rotate(180deg);
                    }

                    .has-submenu.open .fa-chevron-up {
                        transform: rotate(0);
                    }

                    /* Đảm bảo không bị giật khi chuyển đổi */
                    .submenu>li {
                        opacity: 0;
                        transform: translateY(-10px);
                        transition: opacity 0.3s ease, transform 0.3s ease;
                        margin: 0;
                        padding: 0;
                    }

                    .has-submenu.open .submenu>li {
                        opacity: 1;
                        transform: translateY(0);
                    }

                    /* Thêm delay cho từng mục con */
                    .has-submenu.open .submenu>li:nth-child(1) {
                        transition-delay: 0.1s;
                    }

                    .has-submenu.open .submenu>li:nth-child(2) {
                        transition-delay: 0.15s;
                    }

                    .has-submenu.open .submenu>li:nth-child(3) {
                        transition-delay: 0.2s;
                    }
                </style>
                Đánh giá sản phẩm
            </a>
        </li>
        @if (Auth::user()->role === 'admin')
            <li>
                <a class="nav-link {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}"
                    href="{{ route('admin.banners.index') }}">
                    <span class="nav-icon"><i class="fas fa-image"></i></span> Banner
                </a>
            </li>
        @endif
        <li class="has-submenu {{ request()->routeIs('admin.blog.*') ? 'active' : '' }}">
            <a href="#" class="nav-link d-flex justify-content-between align-items-center">
                <div>
                    <span class="nav-icon"><i class="fas fa-newspaper"></i></span>
                    <span>Quản lý bài viết</span>
                </div>
                <i class="fas fa-chevron-{{ request()->routeIs('admin.blog.*') ? 'up' : 'down' }} small ms-2"></i>
            </a>
            <ul class="submenu {{ request()->routeIs('admin.blog.*') ? 'show' : '' }}">
                <li class="mb-1">
                    <a class="nav-link {{ request()->routeIs('admin.blog.posts.*') ? 'active' : '' }}"
                        href="{{ route('admin.blog.posts.index') }}">
                        <i class="fas fa-list-ul me-2"></i> Tất cả bài viết
                    </a>
                </li>
                <li class="mb-1">
                    <a class="nav-link {{ request()->routeIs('admin.blog.comments.*') ? 'active' : '' }}"
                        href="{{ route('admin.blog.comments.index') }}">
                        <i class="fas fa-comments me-2"></i> Bình luận bài viết
                        @if ($unapprovedCommentsCount ?? 0 > 0)
                            <span class="badge bg-danger ms-2">{{ $unapprovedCommentsCount }}</span>
                        @endif
                    </a>
                </li>
                <li>
                    <a class="nav-link {{ request()->routeIs('admin.blog.categories.*') ? 'active' : '' }}"
                        href="{{ route('admin.blog.categories.index') }}">
                        <i class="fas fa-folder-open me-2"></i> Quản lý danh mục
                    </a>
                </li>
            </ul>
        </li>

        @if (Auth::user()->role === 'admin')
            <li>
                <a class="nav-link {{ request()->routeIs('admin.contacts*') ? 'active' : '' }}"
                    href="{{ route('admin.contacts.index') }}">
                    <span class="nav-icon"><i class="fas fa-address-book"></i></span> Quản lý liên hệ
                </a>
            </li>
        @endif

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

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Lấy tất cả các menu có class has-submenu
            const submenuToggles = document.querySelectorAll('.has-submenu > a');

            // Hàm đóng tất cả menu khác
            function closeOtherMenus(currentMenu) {
                document.querySelectorAll('.has-submenu').forEach(menu => {
                    if (menu !== currentMenu && menu.classList.contains('open')) {
                        menu.classList.remove('open');
                    }
                });
            }

            submenuToggles.forEach(toggle => {
                // Bỏ sự kiện click mặc định
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    // Lấy phần tử cha li
                    const parentLi = this.parentElement;

                    // Nếu menu đang mở thì đóng lại và dừng
                    if (parentLi.classList.contains('open')) {
                        parentLi.classList.remove('open');
                        return;
                    }

                    // Đóng tất cả các menu khác
                    closeOtherMenus(parentLi);

                    // Mở menu hiện tại
                    parentLi.classList.add('open');
                });
            });

            // Đóng menu khi click ra ngoài
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.has-submenu')) {
                    closeOtherMenus();
                }
            });

            // Mở menu hiện tại nếu đang ở trang con
            const currentMenu = document.querySelector('.has-submenu.active');
            if (currentMenu) {
                currentMenu.classList.add('open');
            }
        });
    </script>
@endpush
