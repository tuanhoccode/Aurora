@extends('client.layouts.default')

@section('title', 'Đơn hàng của tôi')


<style>
    .order-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        transition: transform 0.2s, box-shadow 0.2s;
        overflow: hidden;
    }

    .order-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    }

    .order-card .card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 12px 20px;
    }

    .order-status-badge .badge {
        position: relative;
        padding: 12px 24px;
        border-radius: 8px;
        font-size: 1.1rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        font-weight: 600;
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
        border-radius: 8px;
        padding: 12px 15px;
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
        border-radius: 8px;
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
        font-size: 0.85rem;
        color: #666;
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
        padding: 12px 16px;
        border-bottom: 1px solid #f0f0f0
    }

    .sp-like {
        background: #ee4d2d;
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        padding: 4px 8px;
        border-radius: 2px
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
        color: #00bfa5;
        display: flex;
        align-items: center;
        gap: 6px
    }

    .sp-done {
        color: #ee4d2d;
        font-weight: 700
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
        padding: 10px 20px;
        font-weight: 700;
        cursor: pointer;
        border: 1px solid transparent;
        font-size: 14px
    }

    .sp-btn-primary {
        background: #ee4d2d;
        border-color: #ee4d2d;
        color: #fff
    }

    .sp-btn-ghost {
        background: #fff;
        border: 1px solid #dcdcdc;
        color: #333
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
</style>

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 fw-bold">Danh sách đơn hàng</h1>
                <p class="text-muted mt-1">Quản lý các đơn hàng của bạn</p>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($orders->isEmpty())
            <div class="card shadow-sm rounded-3 border-0">
                <div class="card-body text-center p-5">
                    <i
                        class="fas {{ request()->has('search') ? 'fa-search' : 'fa-shopping-cart' }} fa-3x text-muted mb-3"></i>
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
                    <div class="mb-4">
                        <form action="{{ route('client.orders') }}" method="GET" class="w-100">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control form-control-lg"
                                    placeholder="Tìm kiếm theo tên sản phẩm hoặc mã đơn hàng..."
                                    value="{{ request('search') }}"
                                    style="border-top-right-radius: 0.375rem; border-bottom-right-radius: 0.375rem;">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-search me-2"></i>Tìm kiếm
                                </button>
                            </div>
                        </form>
                    </div>

                    <ul class="nav nav-tabs mb-3">
                        <li class="nav-item">
                            <a class="nav-link {{ !request('filter') ? 'active' : '' }}"
                                href="{{ route('client.orders') }}">
                                <i class="fas fa-list me-1"></i> Tất cả ({{ $allCount ?? 0 }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request('filter') === 'pending_payment' ? 'active' : '' }}"
                                href="{{ route('client.orders', ['filter' => 'pending_payment']) }}">
                                <i class="fas fa-money-bill-wave me-1"></i> Chờ thanh toán
                                ({{ $pendingPaymentCount ?? 0 }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request('filter') === 'processing' ? 'active' : '' }}"
                                href="{{ route('client.orders', ['filter' => 'processing']) }}">
                                <i class="fas fa-tasks me-1"></i> Đang xử lý ({{ $processingCount ?? 0 }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request('filter') === 'shipping' ? 'active' : '' }}"
                                href="{{ route('client.orders', ['filter' => 'shipping']) }}">
                                <i class="fas fa-truck me-1"></i> Đang giao hàng ({{ $shippingCount ?? 0 }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request('filter') === 'completed' ? 'active' : '' }}"
                                href="{{ route('client.orders', ['filter' => 'completed']) }}">
                                <i class="fas fa-check-circle me-1"></i> Đã giao hàng ({{ $completedCount ?? 0 }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request('filter') === 'cancelled' ? 'active' : '' }}"
                                href="{{ route('client.orders', ['filter' => 'cancelled']) }}">
                                <i class="fas fa-ban me-1"></i> Đã hủy ({{ $cancelledCount ?? 0 }})
                            </a>
                        </li>
                    </ul>

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

                            <!-- Shopee-like order card (products display replaced) -->
                            <div class="sp-card mb-4">
                                <div class="sp-head">
                                    <div class="d-flex align-items-center">
                                        <span class="sp-like">Sản phẩm đã mua</span>
                                        <span class="shop ms-2">{{ $shopName }}</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        @if ($statusId == 4)
                                            <span class="sp-ok"><i class="fas fa-truck"></i> Giao hàng thành công</span>
                                            <span>|</span>
                                            <span class="sp-done">HOÀN THÀNH</span>
                                        @else
                                            <span class="text-muted">{{ $statusName }}</span>
                                        @endif
                                    </div>
                                </div>

                                @foreach ($order->items as $item)
                                    @php
                                        $img = $item->product->thumbnail
                                            ? asset('storage/' . $item->product->thumbnail)
                                            : asset('assets2/img/product/2/prodcut-1.jpg');
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
                                        <img class="sp-thumb" src="{{ $img }}" alt="{{ $item->product->name }}">
                                        <div class="flex-grow-1">
                                            <div class="sp-title">{{ $item->product->name }}</div>
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
                                @endforeach

                                <div class="sp-foot">
                                    <div class="sp-total">Thành tiền:
                                        <b>{{ number_format($order->total_amount, 0, ',', '.') }} ₫</b>
                                    </div>

                                    <div class="card-footer bg-white p-3 border-top border-radius-12">
                                        <div class="d-flex justify-content-between align-items-center order-actions">
                                            <div class="d-flex gap-2">

                                            </div>
                                            <div class="d-flex gap-2">
                                                @if ($order->canBeCancelled())
                                                    <button type="button" class="btn btn-danger checkout__btn-main"
                                                        onclick="openCancelModal({{ $order->id }}, '{{ $order->code }}')">
                                                        <i class="fas fa-times-circle me-1"></i> Hủy đơn hàng
                                                    </button>
                                                @elseif($order->isCancelled())
                                                    <button type="button" class="btn btn-danger" disabled>
                                                        <i class="fas fa-ban me-1"></i> Đã hủy
                                                    </button>
                                                @endif
                                                <a href="{{ route('client.orders.show', $order->id) }}"
                                                    class="btn btn-primary checkout__btn-main">
                                                    <i class="fas fa-eye me-1"></i> Xem chi tiết
                                                </a>
                                                @if ($order->is_paid == 0 && $order->payment_id == 2 && $order->cancelled_at==NULL)
                                                    <a href="{{ route('checkout.retry-payment', $order->code) }}"
                                                        class="btn btn-primary checkout__btn-main">
                                                        <i class="fas fa-redo"></i> Quay lại thanh toán
                                                    </a>
                                                @endif
                                                 @if (
                                                    $order->is_paid == 1 &&
                                                        $order->cancelled_at == null &&
                                                        $order->statusHistories()->where('order_status_id', 4)->where('is_current', 1)->exists() &&
                                                        !\App\Models\Refund::where('order_id', $order->id)->where('status', 'pending')->exists())
                                                    <a href="{{ route('refund.form', $order->code) }}"
                                                        class="tp-checkout-btn checkout__btn-main tp-checkout-btn-hover-alt">
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

@endsection

@push('styles')
    <style>
        .nav-tabs .nav-link {
            color: #495057;
            border: none;
            padding: 0.75rem 1.25rem;
            font-weight: 500;
            border-radius: 0.25rem 0.25rem 0 0;
            transition: all 0.2s;
        }

        .nav-tabs .nav-link:hover {
            border-color: transparent;
            background-color: rgba(0, 0, 0, 0.025);
        }

        .nav-tabs .nav-link.active {
            color: #0d6efd;
            background-color: #fff;
            border-bottom: 3px solid #0d6efd;
        }

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
    </style>
@endpush

@push('scripts')
    <script>
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
    </script>
@endpush
