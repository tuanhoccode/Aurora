@extends('client.layouts.default')

@section('title', 'Đơn hàng của tôi')

<style>
    :root {
        --brand: #ee4d2d;
        --brand-light: #fff6f5;
        --text: #222;
        --muted: #757575;
        --line: #f5f5f5;
        --bg: #f5f5f5;
        --border-radius: 4px;
        --box-shadow: 0 1px 2px 0 rgba(0, 0, 0, .13);
    }

    /* Tabs */
    .tabs {
        display: flex;
        gap: 0;
        padding: 0;
        overflow-x: auto;
        scrollbar-width: none;
        background: #fff;
        border-bottom: 2px solid var(--brand);
        margin-bottom: 20px;
    }

    .tabs::-webkit-scrollbar {
        display: none;
    }

    .tab-btn {
        white-space: nowrap;
        padding: 16px 24px;
        border: none;
        background: transparent;
        color: #555;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        transition: all .2s ease;
        position: relative;
        margin-right: 2px;
    }

    .tab-btn:hover {
        color: #111;
        background: #f0f0f0;
    }

    .tab-btn.active {
        color: var(--brand);
        background: #fff;
        font-weight: 500;
    }

    .tab-btn.active::after {
        content: "";
        position: absolute;
        left: 0;
        right: 0;
        bottom: -2px;
        height: 2px;
        background: var(--brand);
    }

    /* Search bar */
    .search-bar {
        background: #fff;
        padding: 20px;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        margin-bottom: 20px;
    }

    .search-input {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border: 1px solid #d8d8d8;
        background: #fff;
        border-radius: 2px;
        max-width: 600px;
        transition: all .2s;
    }

    .search-input:focus-within {
        border-color: var(--brand);
        box-shadow: 0 0 0 1px var(--brand);
    }

    .search-input input {
        border: none;
        outline: none;
        width: 100%;
        font-size: 14px;
        color: #333;
    }

    .search-input svg {
        flex: 0 0 auto;
        color: var(--muted);
    }

    .order-card {
        border: none;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        transition: all 0.2s;
        overflow: hidden;
        margin-bottom: 20px;
        background: #fff;
    }

    .order-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    }

    .order-card .card-header {
        background: #fff9f8;
        border-bottom: 1px solid #f5f5f5;
        padding: 16px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .order-status-badge .badge {
        position: relative;
        padding: 6px 12px;
        border-radius: 2px;
        font-size: 14px;
        font-weight: 500;
        text-transform: none;
        letter-spacing: normal;
    }

    .order-status-badge .badge i {
        font-size: 1.2rem;
        margin-right: 8px;
    }

    .pulse-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 50%;
        margin-left: 8px;
        animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(0.8);
            opacity: 1;
        }

        70% {
            transform: scale(1.3);
            opacity: 0.7;
        }

        100% {
            transform: scale(0.8);
            opacity: 1;
        }
    }

    .bg-warning {
        background: linear-gradient(45deg, #ffc107 0%, #ff9800 100%) !important;
    }

    .bg-info {
        background: linear-gradient(45deg, #17a2b8 0%, #00bcd4 100%) !important;
    }

    .bg-primary {
        background: linear-gradient(45deg, #2196f3 0%, #3f51b5 100%) !important;
    }

    .bg-success {
        background: linear-gradient(45deg, #4caf50 0%, #8bc34a 100%) !important;
    }

    .bg-danger {
        background: linear-gradient(45deg, #f44336 0%, #e91e63 100%) !important;
    }

    .status-badge i {
        font-size: 1.1rem;
    }

    .status-badge i {
        font-size: 1rem;
    }

    .order-item {
        transition: background-color 0.2s;
        padding: 16px;
        border-bottom: 1px solid #f5f5f5;
        display: flex;
        align-items: center;
    }

    .order-item:hover {
        background-color: #f8f9fa;
    }

    .order-item img {
        border-radius: 8px;
        border: 1px solid #eee;
    }

    .total-amount {
        font-size: 1.25rem;
        font-weight: 700;
        color: #d70018;
        text-shadow: 0 1px 2px rgba(215, 0, 24, 0.1);
    }

    .order-actions .btn {

        padding: 6px 16px;
        font-weight: 500;
        transition: all 0.2s;
    }

    .order-actions .btn i {
        margin-right: 6px;
    }

    .order-date {
        color: #666;
        font-size: 0.9rem;
    }

    .order-code {
        font-weight: 600;
        color: #2a2a2a;
        font-size: 1.1rem;
    }

    .product-name {
        font-weight: 600;
        color: #2a2a2a;
        margin-bottom: 4px;
    }

    .product-variant,
    .product-quantity {
        font-size: 14px;
        color: #888;
        margin-top: 4px;
    }

    .product-price {
        font-weight: 600;
        color: #2a2a2a;
    }

    .status-badge.bg-warning {
        background: linear-gradient(45deg, #ffc107 0%, #ff9800 100%) !important;
        color: #000;
        text-shadow: 0 1px 1px rgba(255, 255, 255, 0.5);
    }

    .status-badge.bg-warning::before {
        background: linear-gradient(45deg, #ffc107 0%, #ff9800 100%);
    }

    .status-badge.bg-info {
        background: linear-gradient(45deg, #17a2b8 0%, #00bcd4 100%) !important;
        color: #fff;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }

    .status-badge.bg-info::before {
        background: linear-gradient(45deg, #17a2b8 0%, #00bcd4 100%);
    }

    .status-badge.bg-primary {
        background: linear-gradient(45deg, #2196f3 0%, #3f51b5 100%) !important;
        color: #fff;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }

    .status-badge.bg-primary::before {
        background: linear-gradient(45deg, #2196f3 0%, #3f51b5 100%);
    }

    .status-badge.bg-success {
        background: linear-gradient(45deg, #4caf50 0%, #8bc34a 100%) !important;
        color: #fff;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }

    .status-badge.bg-success::before {
        background: linear-gradient(45deg, #4caf50 0%, #8bc34a 100%);
    }

    .status-badge.bg-danger {
        background: linear-gradient(45deg, #f44336 0%, #e91e63 100%) !important;
        color: #fff;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }

    .status-badge.bg-danger::before {
        background: linear-gradient(45deg, #f44336 0%, #e91e63 100%);
    }

    .payment-status {
        font-size: 0.9rem;
        padding: 6px 12px;
        border-radius: 15px;
        font-weight: 500;
    }

    .payment-status.bg-success {
        background-color: #d4edda !important;
        color: #155724;
    }

    .payment-status.bg-warning {
        background-color: #fff3cd !important;
        color: #856404;
    }

    /* ===== Shopee-like item row (sp-*) ===== */
    .sp-card {
        border: 1px solid #eee;
        border-radius: 2px;
        overflow: hidden;
        background: #fff
    }

    .sp-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 20px;
        border-bottom: 1px solid #f0f0f0;
        font-size: 15px;
        font-weight: 500;
        background: #fff9f8;
        border-left: 4px solid var(--brand);
        margin-bottom: 2px;
    }

    .sp-head .btn-danger {
        background: #fff;
        border: 1px solid #d8d8d8;
        color: #555;
    }

    .btn-danger:hover {
        background: #f8f8f8;
        border-color: #d8d8d8;
        color: #222;
    }

    .sp-like {
        background: #ee4d2d;
        color: #fff;
        font-size: 12px;
        font-weight: 500;
        padding: 4px 8px;
        border-radius: 2px;
        margin-right: 8px;
    }

    .sp-head .shop {
        font-weight: 700;
        margin-left: 8px
    }

    .sp-mini {
        border: 1px solid #ddd;
        background: #fff;
        border-radius: 2px;
        padding: 6px 10px;
        font-size: 12px;
        cursor: pointer;
        margin-left: 6px
    }

    .sp-ok {
        color: #00a67c;

    }

    .sp-ok i {
        color: #00a67c;
    }

    .sp-done {
        color: #00a67c;

    }

    .sp-row {
        display: flex;
        gap: 12px;
        padding: 14px 16px;
        border-bottom: 1px solid #f0f0f0
    }

    .sp-thumb {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border: 1px solid #eee;
        border-radius: 2px
    }

    .sp-title {
        font-size: 16px;
        color: #333;
        margin: 2px 0 6px
    }

    .sp-variant,
    .sp-qty {
        font-size: 13px;
        color: #7a7a7a;
        margin-top: 2px
    }

    .sp-price {
        text-align: right;
        min-width: 140px;
        white-space: nowrap
    }

    .sp-old {
        color: #9e9e9e;
        text-decoration: line-through;
        margin-right: 8px
    }

    .sp-new {
        color: #ee4d2d;
        font-weight: 700
    }

    .sp-foot {
        background: #fff7f2;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 12px;
        padding: 16px
    }

    .sp-total {
        font-size: 16px
    }

    .sp-total b {
        color: #ee4d2d;
        font-size: 22px
    }

    .sp-btn {
        border-radius: 2px;
        padding: 8px 16px;
        font-weight: 400;
        cursor: pointer;
        border: 1px solid transparent;
        font-size: 14px;
        height: 36px;
        min-width: 120px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .btn-primary {
        background: var(--brand);
        border: 1px solid var(--brand);
        color: #fff;
    }

    .btn-primary:hover {
        background: #f05d40;
        border-color: #f05d40;
        color: #fff;
    }

    .sp-btn-ghost {
        background: #fff;
        border: 1px solid #d8d8d8;
        color: #555;
    }

    .sp-btn-ghost:hover {
        background: #f8f8f8;
    }

    .btn-outline-primary {
        border: 1px solid var(--brand);
        color: var(--brand);
        background: #fff;
    }

    .btn-outline-primary:hover {
        background: rgba(238, 77, 45, 0.1);
        color: var(--brand);
        border-color: var(--brand);
    }

    @media (max-width:576px) {
        .sp-row {
            flex-wrap: wrap
        }

        .sp-price {
            width: 100%;
            text-align: left
        }
    }

    /* Search bar styling */
    .input-group .form-control-lg {
        border: 1px solid #e5e7eb;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        box-shadow: none;
    }

    .input-group .btn-primary {
        background-color: #ff4d2d;
        border-color: #ff4d2d;
        font-weight: 500;
        padding: 0.75rem 1.5rem;
        transition: all 0.2s;
    }

    .input-group .btn-primary:hover {
        background-color: #f04120;
        border-color: #f04120;
    }

    /* Enhanced Tabs styling */
    .nav-tabs {
        border: none;
        background: #fff;
        border-radius: 8px;
        padding: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        margin-bottom: 24px;
        display: block;
        white-space: nowrap;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        -ms-overflow-style: none;
        scrollbar-width: none;
        width: 100%;
    }

    .nav-tabs::-webkit-scrollbar {
        display: none;
    }

    .nav-tabs .nav-link {
        color: #6b7280;
        border: none;
        font-weight: 500;
        padding: 0.75rem 1.25rem;
        white-space: nowrap;
        border-bottom: 2px solid transparent;
        border-radius: 0.25rem 0.25rem 0 0;
        transition: all 0.2s;
    }

    .nav-tabs .nav-link:hover:not(.active) {
        color: #ee4d2d;
        border-color: #ee4d2d;
        background: #fff9f8;
    }

    .nav-tabs .nav-link.active {
        color: #fff;
        background: #ee4d2d;
        border-color: #ee4d2d;
        box-shadow: 0 2px 8px rgba(238, 77, 45, 0.25);
        transform: translateY(-2px);
    }

    .nav-tabs .badge {
        margin-left: 6px;
        font-weight: 600;
        padding: 2px 6px;
        border-radius: 10px;
        font-size: 11px;
        min-width: 22px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .nav-tabs .nav-link:not(.active) .badge {
        background: #f1f1f1;
        color: #666;
    }

    .nav-tabs .nav-link.active .badge {
        background: rgba(255, 255, 255, 0.25);
        color: #fff;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .input-group .btn-primary {
            padding: 0.75rem 1rem;
        }

        .nav-tabs .nav-link {
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }
    }

    /* Shopee-like button styles */
    .card-footer .btn {
        padding: 8px 20px;
        font-size: 14px;
        border-radius: 2px;
        font-weight: 400;
        text-transform: none;
        min-width: 120px;
        text-align: center;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 120px;
        height: 40px;
    }

    /* Primary button (Xem chi tiết) */
    .card-footer .btn-primary {
        background-color: #ee4d2d;
        border: 1px solid #ee4d2d;
        color: #fff;
    }

    .card-footer .btn-primary:hover {
        background-color: #d7381d;
        border-color: #d7381d;
    }

    /* Success button (Xác nhận đã nhận hàng) */
    .card-footer .btn-success {
        background-color: #00bfa5;
        border: 1px solid #00bfa5;
        color: #fff;
    }

    .card-footer .btn-success:hover {
        background-color: #00a08a;
        border-color: #00a08a;
    }

    /* Danger button (Hủy đơn hàng) */
    .card-footer .btn-danger {
        background-color: #fff;
        color: #ee4d2d;
    }

    .card-footer .btn-danger:hover {
        background-color: #f5f5f5;
        border-color: #d9d9d9;
        color: #ee4d2d;
    }

    /* Disabled state */
    .card-footer .btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    /* Button group spacing */
    .card-footer .order-actions .d-flex.gap-2 {
        gap: 12px !important;
    }

    /* Button icons */
    .card-footer .btn i {
        margin-right: 6px;
        font-size: 14px;
    }
</style>

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h4 mb-2 fw-bold" style="color: #222;">Đơn hàng của tôi</h1>
                <p class="text-muted small">Xem thông tin chi tiết và theo dõi đơn hàng của bạn</p>
            </div>
        </div>

        <div class="mb-4">
            <form action="{{ route('client.orders') }}" method="GET" class="w-100">
                <div class="input-group">
                    <input type="text" name="search" class="form-control form-control-lg"
                        placeholder="Tìm kiếm theo tên sản phẩm hoặc mã đơn hàng..." value="{{ request('search') }}"
                        style="border-top-right-radius: 0.375rem; border-bottom-right-radius: 0.375rem;">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-search me-2"></i>Tìm kiếm
                    </button>
                </div>
            </form>
        </div>

        <ul class="nav nav-tabs mb-3">
            <li class="nav-item">
                <a class="nav-link {{ !request('filter') ? 'active' : '' }}" href="{{ route('client.orders') }}">
                    <i class="fas fa-list me-1"></i> Tất cả ({{ $statusCounts['all'] ?? 0 }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('filter') === 'pending_payment' ? 'active' : '' }}"
                    href="{{ route('client.orders', ['filter' => 'pending_payment']) }}">
                    <i class="fas fa-money-bill-wave me-1"></i> Chờ thanh toán ({{ $statusCounts['pending_payment'] ?? 0 }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('filter') === 'processing' ? 'active' : '' }}"
                    href="{{ route('client.orders', ['filter' => 'processing']) }}">
                    <i class="fas fa-tasks me-1"></i> Đang xử lý ({{ $statusCounts['processing'] ?? 0 }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('filter') === 'shipping' ? 'active' : '' }}"
                    href="{{ route('client.orders', ['filter' => 'shipping']) }}">
                    <i class="fas fa-truck me-1"></i> Vận chuyển ({{ $statusCounts['shipping'] ?? 0 }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('filter') === 'delivered' ? 'active' : '' }}"
                    href="{{ route('client.orders', ['filter' => 'delivered']) }}">
                    <i class="fas fa-truck-arrow-right me-1"></i> Giao thành công ({{ $statusCounts['delivered'] ?? 0 }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('filter') === 'completed' ? 'active' : '' }}"
                    href="{{ route('client.orders', ['filter' => 'completed']) }}">
                    <i class="fas fa-check-circle me-1"></i> Hoàn thành ({{ $statusCounts['completed'] ?? 0 }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('filter') === 'cancelled' ? 'active' : '' }}"
                    href="{{ route('client.orders', ['filter' => 'cancelled']) }}">
                    <i class="fas fa-ban me-1"></i> Đã hủy ({{ $statusCounts['cancelled'] ?? 0 }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('filter') === 'return_refund' ? 'active' : '' }}"
                    href="{{ route('client.orders', ['filter' => 'return_refund']) }}">
                    <i class="fas fa-undo-alt me-1"></i> Trả hàng ({{ $statusCounts['return_refund'] ?? 0 }})
                </a>
            </li>
        </ul>

        @if ($orders->isEmpty())
            <div class="card text-center p-5" style="background: #fff;">
                <div class="card-body">
                    <div class="mb-4">
                        <i class="fas {{ request()->has('search') ? 'fa-search' : 'fa-shopping-bag' }} fa-4x"
                            style="color: #f5f5f5;"></i>
                    </div>
                    @if (request()->has('search'))
                        <h4 class="text-muted">Không tìm thấy đơn hàng phù hợp</h4>
                        <p class="text-muted">Không có đơn hàng nào khớp với từ khóa "{{ request('search') }}"</p>
                    @else
                        <h4 class="text-muted">Bạn chưa có đơn hàng nào</h4>
                        <p class="text-muted">Hãy mua sắm và tạo đơn hàng mới</p>
                    @endif
                    <a href="{{ route('shop') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-shopping-bag me-2"></i>Tiếp tục mua sắm
                    </a>
                    @if (request()->has('search'))
                        <a href="{{ route('client.orders') }}" class="btn btn-outline-secondary mt-3 ms-2">
                            <i class="fas fa-times me-2"></i>Xóa bộ lọc
                        </a>
                    @endif
                </div>
            </div>
        @else
            <div class="card shadow-sm rounded-3 border-0">
                <div class="card-body p-4">
                    <div class="order-list">
                        @foreach ($orders as $order)
                            @php
                                $currentStatus = $order->currentStatus;
                                $statusId = $currentStatus ? $currentStatus->order_status_id : 1;
                                $statusName =
                                    $currentStatus && $currentStatus->status
                                        ? $currentStatus->status->name
                                        : 'Chờ xác nhận';

                                $badgeClass = match ($statusId) {
                                    1 => 'warning',
                                    2 => 'info',
                                    3 => 'primary',
                                    4 => 'success',
                                    5, 6, 7, 8 => 'danger',
                                    default => 'secondary',
                                };

                                $paymentStatus = $order->is_paid ? 'Đã thanh toán' : 'Chờ thanh toán';
                                $paymentBadgeClass = $order->is_paid ? 'success' : 'warning';

                                // get first item shop name fallback
                                $firstItem = $order->items->first();
                                $shopName =
                                    data_get($firstItem, 'product.store.name') ??
                                    (data_get($firstItem, 'product.brand.name') ??
                                        (data_get($firstItem, 'product.user.name') ?? 'Cửa hàng'));
                            @endphp

                            <div class="sp-card mb-4">
                                <div class="sp-head">
                                    <div class="d-flex align-items-center">
                                        <span class="sp-like">Sản phẩm đã mua</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        @if ($statusId == 10)
                                            <span class="sp-ok"><i class="fas fa-truck"></i> Giao hàng thành công</span>
                                            <span>|</span>
                                            <span class="sp-done">ĐÃ HOÀN THÀNH</span>
                                        @else
                                            <span class="text-muted">{{ $statusName }}</span>
                                        @endif
                                    </div>
                                </div>

                                @foreach ($order->items as $item)
                                    @php
                                        // Ưu tiên lấy ảnh từ biến thể, nếu không có thì lấy từ sản phẩm cha
                                        $img =
                                            $item->variant && $item->variant->img
                                                ? asset('storage/' . $item->variant->img)
                                                : ($item->product->thumbnail
                                                    ? asset('storage/' . $item->product->thumbnail)
                                                    : asset('assets2/img/product/2/prodcut-1.jpg'));
                                        $old =
                                            $item->original_price ??
                                            (data_get($item, 'variant.original_price') ??
                                                data_get($item, 'product.price'));
                                        $showOld = $old && $old > $item->price;
                                        $variantText = null;
                                        if ($item->product_variant_id && $item->variant) {
                                            $vals = $item->variant->relationLoaded('attributeValues')
                                                ? $item->variant->attributeValues
                                                : $item->variant->attributeValues()->with('attribute')->get();
                                            $variantText = $vals->pluck('value')->filter()->implode(' / ');
                                        }
                                    @endphp


                                    <div class="sp-row">
                                        <a href="{{ route('client.orders.show', $order->id) }}" class="d-block">
                                            <img class="sp-thumb" src="{{ $img }}"
                                                alt="{{ $item->product->name }}">
                                        </a>
                                        <div class="flex-grow-1">
                                            <a href="{{ route('client.orders.show', $order->id) }}"
                                                class="text-dark text-decoration-none">
                                                <div class="sp-title">{{ $item->product->name }}</div>
                                            </a>
                                            <div class="shop">Thương hiệu: {{ $shopName }}</div>
                                            @if ($variantText)
                                                <div class="sp-variant">Phân loại hàng: {{ $variantText }}</div>
                                            @endif
                                            <div class="sp-qty">x{{ $item->quantity }}</div>
                                        </div>
                                        <div class="sp-price">
                                            @if ($showOld)
                                                <span class="sp-old">{{ number_format($old, 0, ',', '.') }} ₫</span>
                                            @endif
                                            <span class="sp-new">{{ number_format($item->price, 0, ',', '.') }} ₫</span>
                                        </div>
                                    </div> 
                                    @if ($statusId == 10 && !$order->refund()->whereIn('status', ['pending', 'approved'])->exists())
                                        <div class="d-flex gap-2 mt-2 mb-2">
                                            @if ($item->review)
                                                <button class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1"
                                                    data-bs-toggle = "modal"
                                                    data-bs-target = "#viewReviewModal"
                                                    data-rating = "{{ $item->review->rating }}"
                                                    data-text = "{{ $item->review->review_text }}"
                                                    data-images='@json($item->review->images->map(fn($img) => asset('storage/' . $img->image_path)))'
                                                    data-product-name = "{{ $item->product->name }}"
                                                    data-variant = "{{ $variantText }}"
                                                    data-thumb="{{ $item->product->thumbnail
                                                        ? asset('storage/' . $item->product->thumbnail)
                                                        : asset('assets2/img/product/2/prodcut-1.jpg') }}">
                                                    <i class="fas fa-eye me-1"></i> Xem đánh giá
                                                </button>     
                                            @elseif($item->canReviewItem()) 
                                                <button class="btn btn-outline-warning btn-sm d-flex align-items-center gap-1"
                                                    data-bs-toggle="modal" data-bs-target="#reviewModal"
                                                    data-product-id="{{ $item->product->id }}"
                                                    data-order-item-id="{{ $item->id }}"
                                                    data-product-name="{{ $item->product->name }}"
                                                    data-variant="{{ $variantText }}">
                                                    <i class="fas fa-star"></i>Đánh giá sản phẩm
                                                </button>                                                  
                                            @endif
                                        </div>
                                    @endif
                                @endforeach

                                <div class="sp-foot">
                                    <div class="sp-total">Thành tiền:
                                        <b>{{ number_format($order->total_amount, 0, ',', '.') }} ₫</b>
                                    </div>

                                    <div class="card-footer bg-white p-3 border-top border-radius-12">
                                        <div class="d-flex justify-content-between align-items-center order-actions">
                                            <div class="d-flex gap-2">

                                            </div>
                                            <div class="d-flex gap-2 flex-wrap">
                                                @if ($order->canBeCancelled())
                                                    <button type="button" class="btn btn-danger checkout__btn-main"
                                                        onclick="openCancelModal({{ $order->id }}, '{{ $order->code }}')">
                                                        <i class="fas fa-times-circle me-1"></i> Hủy đơn hàng
                                                    </button>
                                                @elseif($order->isCancelled())
                                                    <form action="{{ route('client.orders.reorder', $order) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-primary me-2">
                                                            <i class="fas fa-shopping-cart me-1"></i> Mua lại
                                                        </button>
                                                    </form>
                                                    <button type="button" class="btn btn-danger" disabled>
                                                        <i class="fas fa-ban me-1"></i> Đã hủy
                                                    </button>
                                                @endif

                                                @php
                                                    $isDelivered =
                                                        $order->currentStatus &&
                                                        $order->currentStatus->order_status_id == 4; // 4 = Giao hàng thành công
                                                    $isCompleted =
                                                        $order->currentStatus &&
                                                        $order->currentStatus->order_status_id == 10; // 10 = Nhận hàng thành công
                                                @endphp

                                                @if ($isDelivered && !$order->isConfirmationExpired())
                                                    <form
                                                        action="{{ route('client.orders.confirm-delivery', $order->id) }}"
                                                        method="POST" class="d-inline confirm-delivery-form">
                                                        @csrf
                                                        @method('POST')
                                                        <button type="submit" class="btn btn-success me-2"
                                                            onclick="return confirm('Bạn có chắc chắn đã nhận được hàng?');">
                                                            <i class="fas fa-check-circle me-1"></i> Xác nhận đã nhận hàng
                                                        </button>
                                                    </form>
                                                @elseif($order->isCompleted())
                                                    <form action="{{ route('client.orders.reorder', $order) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-primary me-2">
                                                            <i class="fas fa-shopping-cart me-1"></i> Mua lại
                                                        </button>
                                                    </form>
                                                @endif
                                                <a href="{{ route('client.orders.show', $order->id) }}"
                                                    class="btn btn-primary d-flex align-items-center gap-1 btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                    {{ $order->isCancelled() ? 'Xem chi tiết hủy đơn' : 'Xem chi tiết' }}
                                                </a>

                                                @if ($order->is_paid == 0 && $order->payment_id == 2 && $order->cancelled_at == null)
                                                    <a href="{{ route('checkout.retry-payment', $order->code) }}"
                                                        class="btn btn-warning d-flex align-items-center gap-1 btn-sm">
                                                        <i class="fas fa-redo"></i> Quay lại thanh toán
                                                    </a>
                                                @endif

                                                @if (
                                                    $order->is_paid == 1 &&
                                                        $order->cancelled_at == null &&
                                                        $order->statusHistories()->where('order_status_id', 10)->where('is_current', 1)->exists() &&
                                                        !\App\Models\Refund::where('order_id', $order->id)->where('status', 'pending')->exists() &&
                                                        $order->created_at >= now()->subDays(7))
                                                    <a href="{{ route('refund.form', $order->code) }}"
                                                        class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1">
                                                        <i class="fas fa-undo"></i> Yêu cầu hoàn trả
                                                    </a>
                                                @endif

                    </div>
                </div>
            </div>
    </div>
    </div>
    <!-- /Shopee-like card -->
    @endforeach

    <div class="d-flex justify-content-between align-items-center mt-4">
        <div class="text-muted">
            Hiển thị {{ $orders->firstItem() }} đến {{ $orders->lastItem() }} trong tổng số
            {{ $orders->total() }} đơn hàng
        </div>
        <div>
            {{ $orders->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
        </div>
    </div>
    </div>
    </div>
    </div>
    @endif
    </div>

    <!-- Modal Hủy đơn hàng -->
    <div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelOrderModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelOrderModalLabel">Hủy đơn hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="cancelOrderForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="orderCode" class="form-label">Mã đơn hàng</label>
                            <input type="text" class="form-control" id="orderCode" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="cancel_reason" class="form-label">Lý do hủy đơn <span
                                    class="text-danger">*</span></label>
                            <select name="cancel_reason" id="cancel_reason" class="form-select" required>
                                <option value="">Chọn lý do hủy...</option>
                                <option value="Đặt nhầm sản phẩm">Đặt nhầm sản phẩm</option>
                                <option value="Muốn thay đổi thông tin đơn hàng">Muốn thay đổi thông tin đơn hàng</option>
                                <option value="Thay đổi phương thức thanh toán">Thay đổi phương thức thanh toán</option>
                                <option value="Giao hàng chậm">Xử lý đơn hàng chậm</option>
                                <option value="Đổi ý">Đổi ý</option>
                                <option value="Khác">Khác</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="cancel_note" class="form-label">Ghi chú (tùy chọn)</label>
                            <textarea name="cancel_note" id="cancel_note" class="form-control" rows="3"
                                placeholder="Nhập thêm thông tin về lý do hủy đơn..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-times me-2"></i>Xác nhận hủy đơn
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openCancelModal(orderId, orderCode) {
            document.getElementById('orderCode').value = orderCode;
            document.getElementById('cancelOrderForm').action = '{{ route('client.orders.cancel', ':orderId') }}'.replace(
                ':orderId', orderId);

            // Reset form
            document.getElementById('cancel_reason').value = '';
            document.getElementById('cancel_note').value = '';

            // Show modal
            var modal = new bootstrap.Modal(document.getElementById('cancelOrderModal'));
            modal.show();
        }

        // Xử lý khi chọn "Khác" trong lý do hủy
        document.getElementById('cancel_reason').addEventListener('change', function() {
            if (this.value === 'Khác') {
                this.style.display = 'none';
                var otherReasonInput = document.createElement('input');
                otherReasonInput.type = 'text';
                otherReasonInput.name = 'cancel_reason';
                otherReasonInput.className = 'form-control';
                otherReasonInput.placeholder = 'Nhập lý do hủy đơn...';
                otherReasonInput.required = true;
                this.parentNode.appendChild(otherReasonInput);
                otherReasonInput.focus();
            }
        });

        // Reset modal khi đóng
        document.getElementById('cancelOrderModal').addEventListener('hidden.bs.modal', function() {
            var select = document.getElementById('cancel_reason');
            select.style.display = 'block';
            select.value = '';

            // Xóa input "Khác" nếu có
            var otherInput = select.parentNode.querySelector('input[name="cancel_reason"]');
            if (otherInput) {
                otherInput.remove();
            }

            document.getElementById('cancel_note').value = '';
        });
    </script>
    <!-- Modal đánh giá sp -->
    <div class="modal fade" id="reviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="reviewForm" action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="product_id" id="review_product_id">
                    <input type="hidden" name="order_item_id" id="review_order_item_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Đánh giá sản phẩm</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-2">
                            <strong id="reviewProductName"></strong> <br>
                            <strong class="text-muted" id="reviewVariantText"></strong>
                        </div>
                        <div class="mb-3">
                            <label>Đánh giá của bạn:</label>
                            <div class="d-flex gap-1 rating-group">
                                @for ($i = 1; $i <= 5; $i++)
                                    <input type="radio" name="rating" id="star{{ $i }}"
                                        value="{{ $i }}" class="d-none"
                                        {{ old('rating') == $i ? 'checked' : '' }}>
                                    <label for="star{{ $i }}" class="star-label"
                                        data-index="{{ $i }}">
                                        <i class="fa-regular fa-star" style="color: #ccc;"></i>
                                    </label>
                                @endfor
                            </div>
                            @error('rating')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <textarea name="review_text" class="form-control" placeholder="Viết đánh giá của bạn..."></textarea>
                        </div>
                        @error('review_text')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div class="mb-3">
                            <input type="file" name="images[]" multiple accept="images/*" class="form-control"
                                id="reviewImagesInput">
                        </div>
                        <div id="reviewImagesPreview" class="d-flex flex-wrap gap-2 mb-3"></div>
                        @error('images.*')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Script modal đánh giá sp -->
    <script>
        // Gán product_id khi mở modal
        var reviewModal = document.getElementById('reviewModal');
        reviewModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const productId = button.getAttribute('data-product-id');
            const orderItemId = button.getAttribute('data-order-item-id');
            const productName = button.getAttribute('data-product-name');
            const variantText = button.getAttribute('data-variant');

            // Cập nhật URL form với product_id
            const form = document.getElementById('reviewForm');
            form.action = '{{ route('client.store') }}';
            document.getElementById('review_product_id').value = productId;
            document.getElementById('review_order_item_id').value = orderItemId;

            //Gán tên sp phân loại
            document.getElementById('reviewProductName').textContent = productName;
            document.getElementById('reviewVariantText').textContent = variantText ? `Phân loại: ${variantText}` :
                '';
        });


        //màu sao
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.rating-group').forEach(group => {
                const radios = group.querySelectorAll('input[name="rating"]');
                const labels = group.querySelectorAll('.star-label');

                labels.forEach((label, idx) => {
                    label.addEventListener('click', () => {
                        // Check radio tương ứng
                        radios[idx].checked = true;

                        // Update màu sao
                        labels.forEach((lbl, i) => {
                            const icon = lbl.querySelector('i');
                            if (i <= idx) {
                                icon.classList.remove('fa-regular');
                                icon.classList.add('fa-solid');
                                icon.style.color = '#ffc107';
                            } else {
                                icon.classList.remove('fa-solid');
                                icon.classList.add('fa-regular');
                                icon.style.color = '#ccc';
                            }
                        });
                    });
                });

                // Giữ màu nếu form validation fail
                const checkedRadio = group.querySelector('input[name="rating"]:checked');
                if (checkedRadio) {
                    const idx = Array.from(radios).indexOf(checkedRadio);
                    labels.forEach((lbl, i) => {
                        const icon = lbl.querySelector('i');
                        if (i <= idx) {
                            icon.classList.remove('fa-regular');
                            icon.classList.add('fa-solid');
                            icon.style.color = '#ffc107';
                        } else {
                            icon.classList.remove('fa-solid');
                            icon.classList.add('fa-regular');
                            icon.style.color = '#ccc';
                        }
                    });
                }
            });
            //Thêm hiển thị khung ảnh
            document.getElementById('reviewImagesInput').addEventListener('change', function(event) {
                const previewContainer = document.getElementById('reviewImagesPreview');
                previewContainer.innerHTML = ''; // Xóa các ảnh cũ

                const files = event.target.files;

                if (files) {
                    Array.from(files).forEach(file => {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.style.width = '80px';
                            img.style.height = '80px';
                            img.style.objectFit = 'cover';
                            img.classList.add('rounded'); // có thể thêm class tùy ý
                            previewContainer.appendChild(img);
                        }
                        reader.readAsDataURL(file);
                    });
                }
            });
        });
    </script>

    <!-- Modal hiển thị xem đánh giá -->
    <div class="modal fade" id="viewReviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Đánh giá của bạn</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-start mb-3 border-bottom pb-3">
                        <img id="viewReviewThumb" class="rounded me-3" width="80" height="80"
                            style="object-fit:cover" alt="Ảnh sản phẩm">
                        <div>
                            <h6 id="viewReviewProduct" class="mb-1"></h6>
                            <p id="viewReviewVariant" class="text-muted small mb-1"></p>
                            <div id="viewReviewRating" class="mb-1"></div>
                        </div>
                    </div>
                    <p id="viewReviewText" class="mb-3"></p>
                    <div id="viewReviewImages" class="d-flex flex-wrap gap-2"></div>

                </div>
            </div>

        </div>
    </div>
    <!-- script xem đánh giá  -->
    <script>
        var viewReviewModal = document.getElementById('viewReviewModal');
        viewReviewModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;

            const rating = button.getAttribute('data-rating');
            const text = button.getAttribute('data-text');
            const images = JSON.parse(button.getAttribute('data-images') || '[]');
            const product = button.getAttribute('data-product-name');
            const variant = button.getAttribute('data-variant') || '';
            const thumb = button.getAttribute('data-thumb');

            // Hiển thị sao
            const stars = Array.from({
                    length: 5
                }, (_, i) =>
                `<i class="fa${i < rating ? 's' : 'r'} fa-star" style="color:${i < rating ? '#ffc107' : '#ccc'}"></i>`
            ).join('');
            document.getElementById('viewReviewRating').innerHTML = stars;

            // Text
            document.getElementById('viewReviewText').textContent = text || '';

            // Product info
            document.getElementById('viewReviewProduct').textContent = product;
            document.getElementById('viewReviewVariant').textContent = variant ? 'Phân loại: ' + variant : '';
            document.getElementById('viewReviewThumb').src = thumb;

            // Xóa ảnh cũ trước khi thêm ảnh mới
            const imgContainer = document.getElementById('viewReviewImages');
            imgContainer.innerHTML = ''; // Xóa nội dung cũ

            // Thêm ảnh mới
            images.forEach(url => {
                let img = document.createElement('img');
                img.src = url; // Đường dẫn đã được xử lý trong Blade
                img.className = "rounded border";
                img.style.width = "100px";
                img.style.height = "100px";
                img.style.objectFit = "cover";
                imgContainer.appendChild(img);
            });
        });
    </script>

@endsection

@push('styles')
    <style>
        /* Search bar styling */
        .input-group .form-control-lg {
            border: 1px solid #e5e7eb;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            box-shadow: none;
        }

        .input-group .btn-primary {
            background-color: #ff4d2d;
            border-color: #ff4d2d;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            transition: all 0.2s;
        }

        .input-group .btn-primary:hover {
            background-color: #f04120;
            border-color: #f04120;
        }

        /* Tabs styling */
        .nav-tabs {
            border-bottom: 1px solid #e5e7eb;
            flex-wrap: nowrap;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            -ms-overflow-style: -ms-autohiding-scrollbar;
            scrollbar-width: none;
        }

        .nav-tabs::-webkit-scrollbar {
            display: none;
        }

        .nav-tabs .nav-link {
            color: #666;
            border: 1px solid #e8e8e8;
            font-weight: 500;
            padding: 0.75rem 1.25rem;
            white-space: nowrap;
            border-bottom: 2px solid transparent;
            border-radius: 0.25rem 0.25rem 0 0;
            transition: all 0.2s;
        }

        .nav-tabs .nav-link:hover {
            color: #ff4d2d;
            background-color: rgba(0, 0, 0, 0.025);
            border-color: transparent;
        }

        .nav-tabs .nav-link.active {
            color: #ff4d2d;
            background-color: #fff;
            border-bottom: 3px solid #ff4d2d;
        }

        .nav-tabs .nav-link i {
            margin-right: 0.25rem;
        }

        @media (max-width: 768px) {
            .input-group .btn-primary {
                padding: 0.75rem 1rem;
            }

            .nav-tabs .nav-link {
                padding: 0.5rem 0.75rem;
                font-size: 0.875rem;
            }
        }

        /* Table styling */


        .table th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            border-top: none;
            padding: 0.75rem 1rem;
        }

        .table td {
            padding: 1rem;
            vertical-align: middle;
        }

        .badge {
            font-weight: 500;
            padding: 0.4em 0.8em;
            font-size: 0.8em;
        }

        .btn-sm.rounded-circle {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .product-variant {
            margin-bottom: 10px;
        }

        .product-variant {
            margin-top: 8px;
        }

        .product-variant .text-muted {
            min-width: 100px;
            display: inline-block;
        }

        .product-variant .sku-badge {
            background: #e3f2fd;
            color: #1976d2;
            border-radius: 4px;
            padding: 2px 8px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .pagination {
            margin-bottom: 0;
        }

        .pagination .page-link {
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 2px;
        }

        .pagination .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        /* Nút hành động */
        .btn-detail,
        .btn-track,
        .btn-cancel-order {
            padding: 0.5rem 1.25rem;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            transition: all 0.2s ease-in-out;
            border: 1px solid transparent;
        }

        .btn-detail {
            background-color: #f8f9fa;
            color: #0d6efd;
            border: 1px solid #dee2e6;
        }

        .btn-detail:hover {
            background-color: #e9ecef;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            color: #0d6efd;
        }

        .btn-track {
            background-color: #0d6efd;
            color: white;
            border: 1px solid #0d6efd;
        }

        .btn-track:hover {
            background-color: #0b5ed7;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(13, 110, 253, 0.3);
            color: white;
        }

        .btn-cancel-order {
            background-color: #fff5f5;
            color: #dc3545;
            border: 1px solid #f1aeb5;
        }

        .btn-cancel-order:hover {
            background-color: #f8d7da;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        .btn i {
            font-size: 0.9em;
        }

        .payment-status {
            padding: 0.35rem 0.75rem;
            border-radius: 50rem;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .bg-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .bg-paid {
            background-color: #d4edda;
            color: #155724;
        }

        .bg-failed {
            background-color: #f8d7da;
            color: #721c24;
        }

        .bg-refunded {
            background-color: #e2e3e5;
            color: #383d41;
        }

        /* Màu sao */
        .star-label {
            cursor: pointer;
            font-size: 24px;
        }

        .star-label i {
            transition: color 0.2s;
        }

        /* hover màu vàng */
        .star-label:hover~.star-label i,
        .star-label:hover i {
            color: #ffc107 !important;
        }

        /* sao được chọn */
        input[type="radio"]:checked~label i {
            color: #ffc107 !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Function to handle reorder
        function handleReorder(button) {
            const orderId = button.getAttribute('data-order-id');
            console.log('Handling reorder for order:', orderId);

            if (!orderId) {
                console.error('No order ID found');
                return;
            }

            // Show loading state
            const originalHTML = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Đang xử lý...';

            // Create form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/orders/' + orderId + '/reorder';
            form.style.display = 'none';

            // Add CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);

            // Add method spoofing
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'POST';
            form.appendChild(methodInput);

            // Submit form
            document.body.appendChild(form);
            form.submit();
        }

        // Add click handler when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
        const buttons = document.querySelectorAll('.reorder-btn');
        console.log('Found', buttons.length, 'reorder buttons');

        buttons.forEach(button => {
            button.onclick = function(e) {
                e.preventDefault();
                handleReorder(this);
            };

            console.log('=== Bắt đầu xử lý mua lại đơn hàng ===');
            console.log('Order ID:', orderId);

            if (!orderId) {
                console.error('Không tìm thấy orderId');
                alert('Không tìm thấy thông tin đơn hàng');
                return false;
            }

            // Vô hiệu hóa nút và hiển thị loading
            button.disabled = true;
            const originalHTML = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Đang xử lý...';

            // Tạo form ẩn để gửi request
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/orders/' + orderId + '/reorder';
            form.style.display = 'none';

            // Thêm CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);

            // Thêm method spoofing cho POST
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'POST';
            form.appendChild(methodInput);

            // Thêm form vào body và submit
            document.body.appendChild(form);
            form.submit();

            // Đảm bảo form được submit
            setTimeout(() => {
                if (document.body.contains(form)) {
                    document.body.removeChild(form);
                }
                button.disabled = false;
                button.innerHTML = originalHTML;
            }, 1000);
        });
        });
        });
    </script>

    <script>
        // Hàm xử lý nút Mua lại
        window.handleReorder = function(button, event, orderId) {
            event.preventDefault();
            event.stopPropagation();

            console.log('=== Bắt đầu xử lý mua lại đơn hàng ===');
            console.log('Order ID:', orderId);

            if (!orderId) {
                console.error('Không tìm thấy orderId');
                alert('Không tìm thấy thông tin đơn hàng');
                return false;
            }

            // Vô hiệu hóa nút và hiển thị loading
            button.disabled = true;
            const originalHTML = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Đang xử lý...';

            // Tạo form ẩn để gửi request
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/orders/' + orderId + '/reorder';
            form.style.display = 'none';

            // Thêm CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);

            // Thêm method spoofing cho POST
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'POST';
            form.appendChild(methodInput);

            // Thêm form vào body và submit
            document.body.appendChild(form);
            form.submit();

            // Đảm bảo form được submit
            setTimeout(() => {
                if (document.body.contains(form)) {
                    document.body.removeChild(form);
                }
                button.disabled = false;
                button.innerHTML = originalHTML;
                alert('Đang xử lý yêu cầu mua lại đơn hàng...');
            }, 1000);
        }
        // Hàm hiển thị lỗi
        function showError(message) {
            console.error('Error:', message);
            Swal.fire({
                title: 'Lỗi!',
                text: message,
                icon: 'error',
                confirmButtonText: 'Đóng',
                confirmButtonColor: '#dc3545'
            });
        }

        // Xử lý nút Mua lại - phiên bản đơn giản
        document.addEventListener('click', function(e) {
            if (e.target.closest('.reorder-btn')) {
                e.preventDefault();
                const button = e.target.closest('.reorder-btn');
                const orderId = button.getAttribute('data-order-id');
                const originalHTML = button.innerHTML;

                console.log('=== Bắt đầu xử lý mua lại đơn hàng ===');
                console.log('Order ID:', orderId);

                if (!orderId) {
                    console.error('Không tìm thấy orderId');
                    alert('Không tìm thấy thông tin đơn hàng');
                    return false;
                }

                // Vô hiệu hóa nút và hiển thị loading
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Đang xử lý...';

                // Tạo form ẩn để gửi request
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/orders/' + orderId + '/reorder';
                form.style.display = 'none';

                // Thêm CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken;
                form.appendChild(csrfInput);

                // Thêm method spoofing cho POST
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'POST';
                form.appendChild(methodInput);

                // Thêm form vào body và submit
                document.body.appendChild(form);
                form.submit();
            }
        });
        // Khởi tạo tooltip
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // Xử lý khi chọn "Khác" trong lý do hủy
        document.addEventListener('DOMContentLoaded', function() {
            var cancelReasonSelect = document.getElementById('cancel_reason');
            if (cancelReasonSelect) {
                cancelReasonSelect.addEventListener('change', function() {
                    if (this.value === 'Khác') {
                        this.style.display = 'none';
                        var otherReasonInput = document.createElement('input');
                        otherReasonInput.type = 'text';
                        otherReasonInput.name = 'cancel_reason';
                        otherReasonInput.className = 'form-control';
                        otherReasonInput.placeholder = 'Nhập lý do hủy đơn...';
                        otherReasonInput.required = true;
                        this.parentNode.appendChild(otherReasonInput);
                        otherReasonInput.focus();
                    }
                });
            }

            // Reset modal khi đóng
            var cancelModal = document.getElementById('cancelOrderModal');
            if (cancelModal) {
                cancelModal.addEventListener('hidden.bs.modal', function() {
                    var select = document.getElementById('cancel_reason');
                    if (select) {
                        select.style.display = 'block';
                        select.value = '';

                        // Xóa input "Khác" nếu có
                        var otherInput = select.parentNode.querySelector('input[name="cancel_reason"]');
                        if (otherInput) {
                            otherInput.remove();
                        }

                        var cancelNote = document.getElementById('cancel_note');
                        if (cancelNote) {
                            cancelNote.value = '';
                        }
                    }
                });
            }
        });

        // Hàm mở modal hủy đơn hàng
        function openCancelModal(orderId, orderCode) {
            var orderCodeInput = document.getElementById('orderCode');
            var cancelOrderForm = document.getElementById('cancelOrderForm');
            if (orderCodeInput) orderCodeInput.value = orderCode;
            if (cancelOrderForm) cancelOrderForm.action = '{{ route('client.orders.cancel', ':orderId') }}'.replace(
                ':orderId', orderId);

            // Reset form
            var cancelReason = document.getElementById('cancel_reason');
            if (cancelReason) {
                cancelReason.value = '';
            }

            var cancelNote = document.getElementById('cancel_note');
            if (cancelNote) {
                cancelNote.value = '';
            }

            // Hiển thị modal
            var modalEl = document.getElementById('cancelOrderModal');
            if (modalEl) {
                var modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        }

        // Hàm hiển thị lỗi
        function showError(message) {
            console.error('Error:', message);
            Swal.fire({
                title: 'Lỗi!',
                text: message,
                icon: 'error',
                confirmButtonText: 'Đóng',
                confirmButtonColor: '#dc3545'
            });
        }

        // Xử lý nút Mua lại
        $(document).on('click', '.reorder-btn', function(e) {
            e.preventDefault();
            const button = $(this);
            const orderId = button.data('order-id');
            const originalText = button.html();

            console.log('=== Bắt đầu xử lý mua lại đơn hàng ===');
            console.log('Order ID:', orderId);
            console.log('Button:', button);

            // Kiểm tra orderId
            if (!orderId) {
                console.error('Không tìm thấy orderId');
                showError('Không tìm thấy thông tin đơn hàng');
                return false;
            }

            // Vô hiệu hóa nút và hiển thị loading
            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Đang xử lý...');

            // Gửi yêu cầu AJAX
            const url = '{{ url('orders') }}/' + orderId + '/reorder';
            console.log('Gửi yêu cầu AJAX đến:', url);

            $.ajax({
                url: url,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    console.log('=== Phản hồi từ máy chủ ===');
                    console.log('Response:', response);

                    // Bật lại nút
                    button.prop('disabled', false).html(originalText);

                    if (response && response.success) {
                        // Chuyển hướng trực tiếp đến giỏ hàng
                        if (response.redirect) {
                            window.location.href = response.redirect;
                            return;
                        }

                        // Nếu không có redirect URL, chuyển hướng đến giỏ hàng mặc định
                        window.location.href = '{{ route('client.shopping-cart.index') }}';
                    } else {
                        // Xử lý khi response không có success = true
                        const errorMessage = response && response.message ?
                            response.message :
                            'Có lỗi xảy ra khi xử lý yêu cầu. Vui lòng thử lại sau.';
                        showError(errorMessage);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    console.error('Response:', xhr.responseText);

                    // Bật lại nút
                    button.prop('disabled', false).html(originalText);

                    // Hiển thị thông báo lỗi
                    let errorMessage = 'Có lỗi xảy ra khi xử lý yêu cầu';
                    try {
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseText) {
                            const response = JSON.parse(xhr.responseText);
                            errorMessage = response.message || errorMessage;
                        }
                    } catch (e) {
                        console.error('Error parsing error response:', e);
                        errorMessage = xhr.responseText || errorMessage;
                    }

                    // Hiển thị thông báo lỗi chi tiết hơn
                    Swal.fire({
                        title: 'Lỗi!',
                        html: `<div style="text-align: left;">
                        <p>${errorMessage}</p>
                        <div class="mt-3 text-muted small">
                            <div>Mã lỗi: ${xhr.status} (${xhr.statusText})</div>
                            ${xhr.responseJSON && xhr.responseJSON.errors ? 
                                '<div class="mt-2">Chi tiết lỗi: ' + 
                                JSON.stringify(xhr.responseJSON.errors) + '</div>' : ''}
                        </div>
                    </div>`,
                        icon: 'error',
                        confirmButtonText: 'Đóng',
                        confirmButtonColor: '#dc3545',
                        width: '500px'
                    });
                }
            });
        });

        // Xử lý form xác nhận đã nhận hàng
        $(document).on('submit', 'form.confirm-delivery-form', function(e) {
            // Không cần ngăn chặn hành vi mặc định nữa
            const form = $(this);
            const button = form.find('button[type="submit"]');

            // Vô hiệu hóa nút để tránh submit nhiều lần
            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Đang xử lý...');

            // Cho phép form submit bình thường
            return true;
        });
    </script>
@endpush
