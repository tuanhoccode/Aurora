@extends('client.layouts.default')

@section('title', 'Theo dõi đơn hàng')

@section('content')
<style>
    .order-tracking-container { background: #f7f8fa; padding: 2rem 0; }
    .tracking-header { margin-bottom: 2rem; background: #fff; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
    .tracking-title { font-size: 1.5rem; font-weight: 700; color: #23272f; }
    .tracking-info { color: #7b7e85; font-size: 1rem; margin-bottom: 1rem; }
    .order-products { background: #fff; border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
    .order-products-title { font-size: 1.1rem; font-weight: 600; color: #23272f; margin-bottom: 1rem; }
    .product-item { display: flex; align-items: center; padding: 0.5rem 0; border-bottom: 1px solid #f0f0f0; }
    .product-item:last-child { border-bottom: none; }
    .product-image { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; margin-right: 1rem; }
    .product-info { flex: 1; }
    .product-name { font-weight: 600; color: #23272f; font-size: 1rem; margin-bottom: 0.2rem; }
    .product-variant { color: #7b7e85; font-size: 0.95rem; margin-bottom: 0.2rem; }
    .product-price { font-weight: 600; color: #dc3545; font-size: 1rem; }
    .product-quantity { color: #7b7e85; font-size: 0.95rem; }
    .tracking-timeline { background: #fff; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
    .tracking-groups { display: flex; gap: 24px; }
    @media (max-width: 900px) { .tracking-groups { flex-direction: column; gap: 0; } }
    .tracking-group { flex: 1; }
    .tracking-group-title { font-size: 1.05rem; color: #007bff; font-weight: 600; margin-bottom: 1rem; text-align: center; }
    .tracking-step { position: relative; margin-bottom: 2rem; min-height: 80px; }
    .tracking-step:last-child { margin-bottom: 0; }
    .tracking-step-icon { position: absolute; left: -2.2rem; top: 0.1rem; width: 2rem; height: 2rem; border-radius: 50%; background: #fff; border: 2px solid #e9ecef; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; color: #adb5bd; }
    .tracking-step.completed .tracking-step-icon { background: #28a745; color: #fff; border-color: #28a745; }
    .tracking-step.active .tracking-step-icon { background: #ffc107; color: #fff; border-color: #ffc107; }
    .tracking-step.pending .tracking-step-icon { background: #e9ecef; color: #adb5bd; border-color: #e9ecef; }
    .tracking-step-title { font-size: 1rem; font-weight: 600; color: #23272f; }
    .tracking-step-desc { color: #7b7e85; font-size: 0.95rem; }
    .tracking-step-date { font-size: 0.9rem; color: #6c757d; margin-bottom: 0.2rem; }
    .tracking-actions { margin-top: 2rem; }
    .btn-cancel-order { background: #dc3545; color: #fff; border: none; border-radius: 24px; padding: 0.5rem 1.5rem; font-size: 1rem; font-weight: 600; transition: 0.2s; }
    .btn-cancel-order:hover { background: #b52a37; }
</style>
<div class="order-tracking-container">
    <div class="container">
        <div class="tracking-header">
            <div class="tracking-title">Theo dõi đơn hàng #{{ $order->code }}</div>
            <div class="tracking-info">
                <i class="fas fa-calendar-alt me-2"></i> Đặt hàng: {{ $order->created_at->format('d/m/Y, H:i') }}
                <span class="mx-2">|</span>
                <i class="fas fa-credit-card me-2"></i> Thanh toán: <span class="fw-bold text-primary">{{ $order->payment ? $order->payment->name : 'Chưa xác định' }}</span>
                <span class="mx-2">|</span>
                @php
                    $currentStatusName = $order->currentStatus ? $order->currentStatus->status->name : 'Chờ xác nhận';
                @endphp
                <span class="badge bg-{{ $currentStatusName === 'Đã hủy' ? 'danger' : ($currentStatusName === 'Chờ xác nhận' ? 'primary' : 'success') }}">
                    {{ $currentStatusName }}
                </span>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="tracking-map">
                    <iframe
                        width="100%"
                        height="350"
                        frameborder="0"
                        style="border:0"
                        src="https://maps.google.com/maps?q={{ urlencode($order->address . ', ' . $order->city) }}&output=embed"
                        allowfullscreen>
                    </iframe>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="tracking-timeline row" style="display: flex; gap: 0;">
                    @php
                        $timeline = [
                            ['key' => 'cho_xac_nhan', 'title' => 'Chờ xác nhận', 'desc' => 'Đơn hàng đang chờ xác nhận.'],
                            ['key' => 'cho_lay_hang', 'title' => 'Chờ lấy hàng', 'desc' => 'Đơn hàng đang chờ lấy hàng.'],
                            ['key' => 'dang_giao', 'title' => 'Đang giao', 'desc' => 'Đơn hàng đang được giao.'],
                            ['key' => 'giao_hang_thanh_cong', 'title' => 'Giao hàng thành công', 'desc' => 'Đơn hàng đã giao thành công.'],
                            ['key' => 'da_huy', 'title' => 'Đã hủy', 'desc' => 'Đơn hàng đã bị hủy.'],
                        ];
                        $statusMap = [
                            'Chờ xác nhận' => 'cho_xac_nhan',
                            'Chờ lấy hàng' => 'cho_lay_hang',
                            'Đang giao' => 'dang_giao',
                            'Giao hàng thành công' => 'giao_hang_thanh_cong',
                            'Đã hủy' => 'da_huy',
                        ];
                        $currentKey = $statusMap[$currentStatusName] ?? 'cho_xac_nhan';
                        $currentIndex = collect($timeline)->search(fn($item) => $item['key'] === $currentKey);
                    @endphp
                    @foreach($timeline as $i => $step)
                        @php
                            $stepStatus = 'pending';
                            if ($i < $currentIndex) {
                                $stepStatus = 'completed';
                            } elseif ($i == $currentIndex) {
                                $stepStatus = 'active';
                            }
                        @endphp
                        <div class="tracking-step {{ $stepStatus }}" style="min-height: 90px;">
                            <div class="tracking-step-icon">
                                @if($stepStatus === 'completed')
                                    <i class="fas fa-check"></i>
                                @elseif($stepStatus === 'active')
                                    <i class="fas fa-truck"></i>
                                @else
                                    <i class="fas fa-clock"></i>
                                @endif
                            </div>
                            <div class="tracking-step-title">{{ $step['title'] }}</div>
                            <div class="tracking-step-desc">{{ $step['desc'] }}</div>
                        </div>
                    @endforeach
                </div>
                <!-- Sản phẩm đã mua -->
                <div class="order-products mt-4">
                    <div class="order-products-title"><i class="fas fa-shopping-bag me-2"></i> Sản phẩm đã mua</div>
                    @foreach($order->items as $item)
                        <div class="product-item">
                            <img src="{{ $item->product->thumbnail ? Storage::url($item->product->thumbnail) : asset('assets/img/no-image.png') }}" alt="{{ $item->product->name }}" class="product-image">
                            <div class="product-info">
                                <div class="product-name">{{ $item->product->name }}</div>
                                @if($item->variant)
                                    <div class="product-variant">
                                        @foreach($item->variant->attributeValues as $attrValue)
                                            <span class="badge bg-secondary me-1">{{ $attrValue->attribute->name }}: {{ $attrValue->value }}</span>
                                        @endforeach
                                    </div>
                                @endif
                                <div class="product-price">{{ number_format($item->price, 0, ',', '.') }}đ</div>
                                <div class="product-quantity">Số lượng: {{ $item->quantity }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="tracking-actions">
            @if($order->currentStatus && $order->currentStatus->status->name == 'Chờ xác nhận')
                <form method="POST" action="{{ route('client.orders.cancel', ['order' => $order->id]) }}" onsubmit="return confirm('Bạn chắc chắn muốn hủy đơn hàng này?');">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn-cancel-order">
                        <i class="fas fa-times-circle me-1"></i> Hủy đơn hàng
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection 