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
                        @auth
                        <span><a href="{{ route('client.orders') }}">Đơn hàng</a></span>
                        @endauth
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
                        @php
                            $currentStatusName = optional(optional($order->currentStatus)->status)->name ?? 'Chờ xác nhận';
                        @endphp
                        <span class="badge bg-{{ $currentStatusName === 'Đã hủy' ? 'danger' : ($currentStatusName === 'Chờ xác nhận' ? 'primary' : 'success') }}">
                            {{ $currentStatusName }}
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
                        @if($order->cancelled_at)
                        <div class="order-info-item">
                            <i class="fas fa-calendar-times text-danger"></i>
                            <span class="text-danger">Ngày hủy: {{ \Carbon\Carbon::parse($order->cancelled_at)->format('d/m/Y H:i') }}</span>
                        </div>
                        @endif
                        <div class="order-info-item">
                            <i class="fas fa-credit-card {{ $order->is_paid ? 'text-success' : 'text-warning' }}"></i>
                            <span class="{{ $order->is_paid ? 'text-success' : 'text-warning' }} fw-bold">
                                Trạng thái thanh toán: {{ $order->is_paid ? 'Đã thanh toán' : 'Chưa thanh toán' }}
                                @if($order->is_paid && $order->paid_at)
                                    ({{ $order->paid_at->setTimezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') }})
                                @endif
                            </span>
                        </div>
                    </div>
                    
                    @if($order->cancel_reason)
                    <div class="mt-3 p-3 bg-light rounded">
                        <h6 class="text-danger mb-2">
                            <i class="fas fa-exclamation-triangle me-2"></i>Thông tin hủy đơn
                        </h6>
                        <div class="mb-2">
                            <strong>Lý do hủy:</strong> {{ $order->cancel_reason }}
                        </div>
                        @if($order->cancel_note)
                        <div>
                            <strong>Ghi chú:</strong> {{ $order->cancel_note }}
                        </div>
                        @endif
                    </div>
                    @endif
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
                               
                                @php
                                    // Lấy thuộc tính từ cột attributes_variant nếu có
                                    $variantAttributes = $item->attributes_variant ? json_decode($item->attributes_variant, true) : null;
                                @endphp
                                
                                @if($variantAttributes && count($variantAttributes) > 0)
                                    <div class="mt-2">
                                        @foreach($variantAttributes as $attrName => $attrValue)
                                            <span class="badge bg-secondary me-2 mb-1">
                                                {{ $attrName }}: {{ $attrValue }}
                                            </span>

                                        @endforeach
                                    </div>
                                @elseif($item->variant && $item->variant->attributeValues->count() > 0)
                                    {{-- Fallback: Hiển thị từ quan hệ nếu không có dữ liệu trong attributes_variant --}}
                                    <div class="mt-2">
                                        @foreach($item->variant->attributeValues as $attrValue)
                                            @if($attrValue->attribute)
                                                <span class="badge bg-secondary me-2 mb-1">
                                                    {{ $attrValue->attribute->name }}: {{ $attrValue->value }}
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
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
                    <h4>Tổng quan đơn hàng</h4>
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
                        <span>Phí vận chuyển ({{ $order->delivery_type_full_info }})</span>
                        <span class="text-success">{{ $order->shipping_fee_formatted }}</span>
                    </div>
                    @if($order->discount_amount > 0)
                    <div class="order-summary__item text-danger">
                        <span>Mã giảm giá
                            @if($order->coupon)
                                ({{ $order->coupon->code }})
                            @endif
                        </span>
                        <span>-{{ number_format($order->discount_amount, 0, ',', '.') }}đ</span>
                    </div>
                    @endif
                    <div class="order-summary__total">
                        <span>Tổng cộng</span>
                        <span>{{ number_format($order->total_amount, 0, ',', '.') }}đ</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Form hủy đơn hàng -->
@if($order->cancellation_status === null && 
    (optional(optional($order->currentStatus)->status)->code === 'PENDING' || optional(optional($order->currentStatus)->status)->code === 'PROCESSING'))
<div class="container mt-4">
    <div class="card">
        <div class="card-body">
            <h4>Hủy đơn hàng</h4>
            <form action="{{ route('client.orders.cancel', $order->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="reason" class="form-label">Lý do hủy đơn</label>
                    <select name="reason" id="reason" class="form-select" required>
                        <option value="">Chọn lý do...</option>
                        <option value="Không thích sản phẩm">Không thích sản phẩm</option>
                        <option value="Giao hàng chậm">Giao hàng chậm</option>
                        <option value="Đổi ý">Đổi ý</option>
                        <option value="Khác">Khác</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="note" class="form-label">Ghi chú (tùy chọn)</label>
                    <textarea name="note" id="note" class="form-control" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-danger">Gửi yêu cầu hủy đơn</button>
            </form>
        </div>
    </div>
</div>
@endif

<!-- order detail area end -->
@endsection