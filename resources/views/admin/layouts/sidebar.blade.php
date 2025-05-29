@push('styles')
{{-- Custom sidebar styles were moved to app.blade.php --}}
@endpush

<aside class="sidebar-animated d-flex flex-column">
    <div class="d-flex align-items-center justify-content-center mb-4 px-4">
        <h4 class="text-white m-0">Admin Panel</h4>
    </div>
    <ul class="nav flex-column mb-auto" style="flex-grow: 1; padding-right: 0.5rem;">
        <li class="sidebar-heading">Main</li>
        <li>
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <span class="nav-icon"><i class="fas fa-tachometer-alt"></i></span> Dashboard
            </a>
        </li>
        <li>
            <a class="nav-link {{ request()->routeIs('admin.notifications*') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <span class="nav-icon"><i class="fas fa-bell"></i></span> Notifications
            </a>
        </li>
        <hr>
        <li class="sidebar-heading">Management</li>
        <li>
            <a class="nav-link {{ request()->routeIs('admin.products*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">
                <span class="nav-icon"><i class="fas fa-box"></i></span> Products
            </a>
        </li>
        <li>
            <a class="nav-link {{ request()->routeIs('admin.categories*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                <span class="nav-icon"><i class="fas fa-tags"></i></span> Categories
            </a>
        </li>
        <li>
            <a class="nav-link {{ request()->routeIs('admin.attributes*') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <span class="nav-icon"><i class="fas fa-clipboard-list"></i></span> Product Attributes
            </a>
        </li>
        <li>
            <a class="nav-link {{ request()->routeIs('admin.attributes*') ? 'active' : '' }}" href="{{ route('admin.brands.index') }}">
                <span class="nav-icon"><i class="fas fa-clipboard-list"></i></span> Brands
            </a>
        </li>
        <li>
            <a class="nav-link {{ request()->routeIs('admin.orders*') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <span class="nav-icon"><i class="fas fa-shopping-cart"></i></span> Orders
            </a>
        </li>
        <li>
            <a class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <span class="nav-icon"><i class="fas fa-users"></i></span> Users
            </a>
        </li>
        <li>
            <a class="nav-link {{ request()->routeIs('admin.admin_users*') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <span class="nav-icon"><i class="fas fa-user-shield"></i></span> Admin Users
            </a>
        </li>
        <li>
            <a class="nav-link {{ request()->routeIs('admin.coupons*') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <span class="nav-icon"><i class="fas fa-gift"></i></span> Coupons
            </a>
        </li>
        <li>
            <a class="nav-link {{ request()->routeIs('admin.reviews*') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <span class="nav-icon"><i class="fas fa-comments"></i></span> Product Reviews
            </a>
        </li>
        <li>
            <a class="nav-link {{ request()->routeIs('admin.banners*') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <span class="nav-icon"><i class="fas fa-image"></i></span> Banners
            </a>
        </li>
        <li>
            <a class="nav-link {{ request()->routeIs('admin.pages*') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <span class="nav-icon"><i class="fas fa-file-alt"></i></span> CMS Pages
            </a>
        </li>
        <hr>
        <li class="sidebar-heading">Settings</li>
        <li>
            <a class="nav-link {{ request()->routeIs('admin.payments*') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <span class="nav-icon"><i class="fas fa-credit-card"></i></span> Payment Gateways
            </a>
        </li>
        <li>
            <a class="nav-link {{ request()->routeIs('admin.roles*') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <span class="nav-icon"><i class="fas fa-user-lock"></i></span> Roles & Permissions
            </a>
        </li>
        <li>
            <a class="nav-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <span class="nav-icon"><i class="fas fa-cogs"></i></span> System Settings
            </a>
        </li>
    </ul>
</aside>

<!-- Divider -->
<hr class="sidebar-divider d-none d-md-block">

<!-- Sidebar Toggler (Sidebar) -->
<div class="text-center d-none d-md-inline">
    <button class="rounded-circle border-0" id="sidebarToggle"></button>
</div> 
