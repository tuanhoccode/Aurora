@extends('client.layouts.default')

@section('title', 'Đơn hàng của tôi')

@section('content')
<style>
    .tp-orders-area {
        background-color: #f7f8fa;
        padding-top: 3rem;
        padding-bottom: 6rem;
    }

    .table {
        font-size: 1rem;
    }

    .table th {
        font-weight: 600;
        color: #23272f;
        background-color: #f7f8fa;
        border-top: none;
        padding: 1rem 0.75rem;
    }

    .table td {
        padding: 1rem 0.75rem;
        vertical-align: middle;
    }

    .table td:first-child {
        font-weight: 600;
    }

    .table td:last-child {
        text-align: right;
    }

    .table tbody tr {
        border-bottom: 1px solid #e9ecef;
    }

    .table tbody tr:hover {
        background-color: #f7f8fa;
    }

    .badge {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
        border-radius: 20px;
    }

    .btn {
        padding: 0.5rem 1.5rem;
        font-size: 1rem;
        border-radius: 24px;
        min-width: 100px;
        text-align: center;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn i {
        font-size: 1rem;
    }

    .order-item__empty {
        text-align: center;
        padding: 4rem 0;
    }

    .order-item__empty i {
        font-size: 5rem;
        color: #e9ecef;
        margin-bottom: 1.25rem;
    }

    .order-item__empty h4 {
        font-size: 1.75rem;
        color: #23272f;
        margin-bottom: 0.75rem;
    }

    .order-item__empty p {
        font-size: 1.1rem;
        color: #7b7e85;
        margin-bottom: 1.5rem;
    }

    .pagination {
        font-size: 1rem;
    }

    .pagination .page-link {
        padding: 0.5rem 1rem;
        border-radius: 24px;
    }
</style>

<!-- breadcrumb area start -->
<section class="breadcrumb__area">
    <div class="container">
        <div class="row">
            <div class="col-xxl-12">
                <div class="breadcrumb__content p-relative z-index-1">
                    <h3 class="breadcrumb__title">Danh sách đơn hàng</h3>
                    <div class="breadcrumb__list">
                        <span><a href="{{ route('home') }}">Trang chủ</a></span>
                        <span>Đơn hàng</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- breadcrumb area end -->

<!-- orders area start -->
<section class="tp-orders-area">
    <div class="container">
        <div class="row">
            <div class="col-12">
                @if($orders->isEmpty())
                    <div class="order-item__empty">
                        <i class="fas fa-box-open"></i>
                        <h4>Chưa có đơn hàng nào</h4>
                        <p>Bạn chưa đặt mua sản phẩm nào</p>
                        <a href="{{ route('home') }}" class="btn">
                            <i class="fas fa-shopping-cart me-1"></i> Mua sắm ngay
                        </a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Mã đơn hàng</th>
                                    <th>Ngày đặt</th>
                                    <th>Phương thức thanh toán</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th class="text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody id="order-list">
                                @php $defaultShow = 10; @endphp
                                @foreach($orders as $index => $order)
                                <tr class="order-item{{ $index >= $defaultShow ? ' d-none-by-js' : '' }}">
                                    <td>#{{ $order->code }}</td>
                                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($order->payment && $order->payment->logo)
                                            <img src="{{ $order->payment->logo_url }}" alt="{{ $order->payment->name }}" style="width: 24px; height: 24px; margin-right: 8px;">
                                        @else
                                            <i class="fas fa-money-bill-wave me-1"></i>
                                        @endif
                                        {{ $order->payment ? $order->payment->name : 'Chưa xác định' }}
                                    </td>
                                    <td>{{ number_format($order->total_amount, 0, ',', '.') }}đ</td>
                                    <td>
                                        <span class="badge bg-{{ $order->currentStatus ? ($order->currentStatus->status->name === 'Đã hoàn thành' ? 'success' : ($order->currentStatus->status->name === 'Đang giao hàng' ? 'warning' : 'primary')) : 'primary' }}">
                                            {{ $order->currentStatus ? $order->currentStatus->status->name : 'Chờ xác nhận' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('client.orders.show', ['order' => $order->id]) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye me-1"></i> Xem chi tiết
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
<!-- orders area end -->
@endsection

@section('scripts')
<script>
    const defaultShow = {{ $defaultShow }};
    const total = {{ $orders->count() }};
    const items = document.querySelectorAll('.order-item');
    const showAllBtn = document.getElementById('show-all-btn');
    const showingCount = document.getElementById('showing-count');
    let currentVisible = defaultShow;

    if (showAllBtn) {
        showAllBtn.addEventListener('click', function() {
            let nextVisible = currentVisible + defaultShow;
            for (let i = currentVisible; i < nextVisible && i < total; i++) {
                items[i].classList.remove('d-none-by-js');
            }
            currentVisible += defaultShow;
            showingCount.textContent = Math.min(currentVisible, total);
            if (currentVisible >= total) {
                showAllBtn.style.display = 'none';
            }
        });
    }
</script>
@endsection