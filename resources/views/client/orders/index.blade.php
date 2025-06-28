@extends('client.layouts.default')

@section('title', 'Đơn hàng của tôi')

@section('content')
<style>
    .tp-orders-area {
        background-color: #f7f8fa;
        padding-top: 3rem;
        padding-bottom: 6rem;
    }

    .breadcrumb__area {
        background-color: #fff;
        border-bottom: 1px solid #e9ecef;
    }

    .order-items-grid {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .order-header-row {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr 1fr;
        gap: 1.2rem;
        font-size: 1.08rem;
        font-weight: 700;
        color: #7b7e85;
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 1rem;
        margin-bottom: 1rem;
        background: transparent;
        align-items: center;
    }

    .order-card {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr 1fr;
        align-items: center;
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.07);
        border: 1px solid #e9ecef;
        padding: 1.2rem 1.5rem;
        transition: box-shadow 0.2s;
        gap: 1.2rem;
    }

    .order-card:hover {
        box-shadow: 0 8px 32px rgba(0,0,0,0.12);
    }

    .order-card__info {
        display: flex;
        flex-direction: column;
        justify-content: center;
        min-width: 0;
    }

    .order-card__info .name {
        font-size: 1.13rem;
        font-weight: 700;
        color: #23272f;
        margin-bottom: 0.18rem;
        line-height: 1.25;
    }

    .order-card__price,
    .order-card__status,
    .order-card__actions {
        text-align: center;
        min-width: 0;
    }

    .order-card__status .badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.95rem;
    }

    .order-card__actions .btn {
        background: #23272f;
        color: #fff;
        padding: 0.5rem 1rem;
        border-radius: 24px;
        font-size: 0.95rem;
        transition: all 0.2s;
    }

    .order-card__actions .btn:hover {
        background: #4a90e2;
        color: #fff;
    }

    @media (max-width: 991.98px) {
        .order-header-row, .order-card {
            grid-template-columns: 1fr;
            font-size: 0.98rem;
        }
    }

    @media (max-width: 600px) {
        .order-card {
            padding: 1rem;
        }
    }
</style>

<!-- breadcrumb area start -->
<section class="breadcrumb__area include-bg pt-95 pb-50">
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
                <div class="order-items-grid">
                    @if($orders->isEmpty())
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-box-open fa-4x text-muted"></i>
                            </div>
                            <h4 class="text-muted mb-3">Chưa có đơn hàng nào</h4>
                            <p class="text-muted mb-4">Bạn chưa đặt mua sản phẩm nào</p>
                            <a href="{{ route('home') }}" class="btn btn-primary">
                                <i class="fas fa-shopping-cart me-1"></i> Mua sắm ngay
                            </a>
                        </div>
                    @else
                        <div class="order-header-row">
                            <div>Mã đơn hàng</div>
                            <div>Ngày đặt</div>
                            <div>Tổng tiền</div>
                            <div>Trạng thái</div>
                            <div>Hành động</div>
                        </div>

                        @foreach($orders as $order)
                        <div class="order-card">
                            <div class="order-card__info">
                                <h5 class="name">#{{ $order->id }}</h5>
                                <small class="text-muted">
                                    {{ $order->created_at->format('d/m/Y H:i') }}
                                </small>
                            </div>

                            <div class="order-card__price">
                                <h5 class="text-primary">
                                    {{ number_format($order->total_amount, 0, ',', '.') }}đ
                                </h5>
                            </div>

                            <div class="order-card__status">
                                <span class="badge bg-{{ $order->currentStatus->status->name === 'Đã hoàn thành' ? 'success' : ($order->currentStatus->status->name === 'Đang giao hàng' ? 'warning' : 'primary') }}">
                                    {{ $order->currentStatus->status->name }}
                                </span>
                            </div>

                            <div class="order-card__actions">
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-eye me-1"></i> Xem chi tiết
                                </a>
                            </div>
                        </div>
                        @endforeach

                        <div class="mt-4">
                            <div class="d-flex justify-content-center">
                                {{ $orders->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
<!-- orders area end -->
@endsection