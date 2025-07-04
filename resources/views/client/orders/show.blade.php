@extends('client.layouts.default')

@section('title', 'Chi tiết đơn hàng')

@section('content')
<style>
    .tp-order-detail-area {
        background-color: #f7f8fa;
        padding-top: 3rem;
        padding-bottom: 6rem;
    }

    .order-detail-card {
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.07);
        border: 1px solid #e9ecef;
        margin-bottom: 2rem;
        padding: 1.5rem;
    }

    .order-detail-card__header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .order-detail-card__header h4 {
        color: #23272f;
        font-weight: 600;
        margin: 0;
    }

    .order-status {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        color: #fff;
    }

    .order-status.success { background: #28a745; }
    .order-status.warning { background: #ffc107; color: #23272f; }
    .order-status.primary { background: #007bff; }

    .order-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .order-info-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .order-info-item i {
        color: #6c757d;
        font-size: 1.1rem;
    }

    .order-info-item span {
        color: #23272f;
        font-size: 1rem;
    }

    .order-products {
        display: grid;
        gap: 1rem;
    }

    .order-product {
        display: grid;
        grid-template-columns: auto 1fr auto auto;
        gap: 1rem;
        align-items: center;
        padding: 1rem;
        border: 1px solid #e9ecef;
        border-radius: 8px;
    }

    .order-summary {
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.07);
        border: 1px solid #e9ecef;
        padding: 1.5rem;
    }

    .order-summary__item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 1rem;
        font-size: 1.1rem;
        color: #23272f;
    }

    .order-summary__total {
        margin-top: 1rem;
        border-top: 2px solid #e9ecef;
        padding-top: 1rem;
        font-weight: 700;
        font-size: 1.3rem;
        color: #23272f;
    }
</style>

<!-- breadcrumb area start -->
<section class="breadcrumb__area">
    <div class="container">
        <div class="row">
            <div class="col-xxl-12">
                <div class="breadcrumb__content p-relative z-index-1">
                    <h3 class="breadcrumb__title">Chi tiết đơn hàng #{{ $order->id }}</h3>
                    <div class="breadcrumb__list">
                        <span><a href="{{ route('home') }}">Trang chủ</a></span>
                        <span><a href="{{ route('client.orders.index') }}">Đơn hàng</a></span>
                        <span>Chi tiết</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- breadcrumb area end -->

<!-- order detail area start -->
<section class="tp-order-detail-area">
    <div class="container">
        <div class="row">
            <!-- Thông tin đơn hàng -->
            <div class="col-lg-4">
                <div class="order-detail-card">
                    <div class="order-detail-card__header">
                        <h4>Thông tin đơn hàng</h4>
                        <span class="order-status bg-{{ $order->currentStatus ? ($order->currentStatus->status->name === 'Đã hoàn thành' ? 'success' : ($order->currentStatus->status->name === 'Đang giao hàng' ? 'warning' : 'primary')) : 'primary' }}">
                            {{ $order->currentStatus ? $order->currentStatus->status->name : 'Chờ xác nhận' }}
                        </span>
                    </div>

                    <div class="order-info">
                        <div class="order-info-item">
                            <i class="fas fa-clock"></i>
                            <span>Ngày đặt: {{ $order->created_at->setTimezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="order-info-item">
                            @if($order->payment && $order->payment->logo)
                                <img src="{{ $order->payment->logo_url }}" alt="{{ $order->payment->name }}" style="width: 24px; height: 24px; margin-right: 8px;">
                            @else
                                <i class="fas fa-money-bill-wave"></i>
                            @endif
                            <span>Phương thức thanh toán: {{ $order->payment ? $order->payment->name : 'Chưa xác định' }}</span>
                        </div>
                        <div class="order-info-item">
                            <i class="fas fa-tag"></i>
                            <span>Mã đơn hàng: {{ $order->code }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thông tin người mua -->
            <div class="col-lg-4">
                <div class="order-detail-card">
                    <h4>Thông tin người mua</h4>
                    <div class="order-info">
                        <div class="order-info-item">
                            <i class="fas fa-user"></i>
                            <span>Họ tên: {{ $order->fullname }}</span>
                        </div>
                        <div class="order-info-item">
                            <i class="fas fa-phone"></i>
                            <span>Số điện thoại: {{ $order->phone_number }}</span>
                        </div>
                        <div class="order-info-item">
                            <i class="fas fa-envelope"></i>
                            <span>Email: {{ $order->email }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thông tin giao hàng -->
            <div class="col-lg-4">
                <div class="order-detail-card">
                    <h4>Thông tin giao hàng</h4>
                    <div class="order-info">
                        <div class="order-info-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Địa chỉ: {{ $order->address }}</span>
                        </div>
                        <div class="order-info-item">
                            <i class="fas fa-city"></i>
                            <span>Thành phố: {{ $order->city }}</span>
                        </div>
                        <div class="order-info-item">
                            <i class="fas fa-sticky-note"></i>
                            <span>Ghi chú: {{ $order->note ?? 'Không có ghi chú' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Danh sách sản phẩm -->
            <div class="col-12">
                <div class="order-detail-card">
                    <h4>Danh sách sản phẩm</h4>
                    <div class="order-products">
                        @foreach($order->items as $item)
                        <div class="order-product">
                            <img src="{{ $item->product->image_url ?? asset('assets2/img/product/2/default.png') }}" 
                                 alt="{{ $item->product->name }}" 
                                 style="width: 80px; height: 80px; object-fit: cover;">
                            
                            <div>
                                <h5>{{ $item->product->name }}</h5>
                                <p class="text-muted">SKU: {{ $item->product->sku }}</p>
                                @if($item->variant)
                                    <p class="text-muted">
                                        Size: {{ $item->variant->attributes->where('name', 'LIKE', '%size%')->first()?->value ?? 'N/A' }}<br>
                                        Màu: {{ $item->variant->attributes->where('name', 'LIKE', '%color%')->first()?->value ?? 'N/A' }}
                                    </p>
                                @endif
                            </div>
                            
                            <div>
                                <span class="badge bg-primary">{{ $item->quantity }} sản phẩm</span>
                            </div>
                            
                            <div>
                                <span class="text-primary">
                                    {{ number_format($item->price_variant ?? $item->price, 0, ',', '.') }}đ
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Tóm tắt đơn hàng -->
            <div class="col-12">
                <div class="order-summary">
                    <h4>Tóm tắt đơn hàng</h4>
                    <div class="order-summary__item">
                        <span>Sản phẩm</span>
                        <span>{{ $order->items->sum('quantity') }} sản phẩm</span>
                    </div>
                    <div class="order-summary__item">
                        <span>Tạm tính</span>
                        <span>{{ number_format($order->items->sum(function($item) {
                            return ($item->price_variant ?? $item->price) * $item->quantity;
                        }), 0, ',', '.') }}đ</span>
                    </div>
                    <div class="order-summary__item">
                        <span>Phí vận chuyển</span>
                        <span class="text-success">20.000đ</span>
                    </div>
                    <div class="order-summary__total">
                        <span>Tổng cộng</span>
                        <span>{{ number_format($order->total_amount, 0, ',', '.') }}đ</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- order detail area end -->
@endsection