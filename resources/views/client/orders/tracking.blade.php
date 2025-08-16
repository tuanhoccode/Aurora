@extends('client.layouts.default')

@section('title', 'Theo dõi đơn hàng')

@section('content')
<style>
    .order-tracking-container { 
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); 
        padding: 2rem 0; 
        min-height: 100vh;
    }
    
    .tracking-header { 
        margin-bottom: 2rem; 
        background: #fff; 
        padding: 2rem; 
        border-radius: 16px; 
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: 1px solid #e2e8f0;
    }
    
    .tracking-title { 
        font-size: 1.8rem; 
        font-weight: 700; 
        color: #1a202c; 
        margin-bottom: 1rem;
        letter-spacing: -0.01em;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    }
    
    .tracking-info { 
        color: #4a5568; 
        font-size: 1.05rem; 
        margin-bottom: 0.5rem;
        line-height: 1.6;
    }
    
    .tracking-info i {
        color: #4a90e2;
        width: 16px;
    }
    
    .order-products { 
        background: #fff; 
        border-radius: 16px; 
        padding: 2rem; 
        margin-bottom: 2rem; 
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: 1px solid #e2e8f0;
    }
    
    .order-products-title { 
        font-size: 1.3rem; 
        font-weight: 700; 
        color: #1a202c; 
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .order-products-title i {
        color: #4a90e2;
    }
    
    .product-item { 
        display: flex; 
        align-items: center; 
        padding: 1rem; 
        border-bottom: 1px solid #f1f5f9; 
        border-radius: 12px;
        margin-bottom: 0.5rem;
        transition: all 0.2s ease;
    }
    
    .product-item:hover {
        background: #f8fafc;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    .product-item:last-child { 
        border-bottom: none; 
        margin-bottom: 0;
    }
    
    .product-image { 
        width: 70px; 
        height: 70px; 
        object-fit: cover; 
        border-radius: 12px; 
        margin-right: 1.2rem;
        border: 2px solid #e2e8f0;
        background: #fff;
    }
    
    .product-info { 
        flex: 1; 
    }
    
    .product-name { 
        font-weight: 700; 
        color: #1a202c; 
        font-size: 1.05rem; 
        margin-bottom: 0.3rem;
        line-height: 1.4;
    }
    
    .product-variant { 
        color: #64748b; 
        font-size: 0.95rem; 
        margin-bottom: 0.3rem;
    }
    
    .product-variant .badge {
        background: #e2e8f0;
        color: #475569;
        border: none;
        font-size: 0.85rem;
        padding: 0.3rem 0.6rem;
        margin-right: 0.5rem;
        margin-bottom: 0.3rem;
    }
    
    .product-price { 
        font-weight: 700; 
        color: #dc2626; 
        font-size: 1.1rem;
        margin-bottom: 0.2rem;
    }
    
    .product-quantity { 
        color: #64748b; 
        font-size: 0.9rem;
        font-weight: 500;
    }
    
    .tracking-timeline { 
        background: #fff; 
        border-radius: 16px; 
        padding: 2rem; 
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: 1px solid #e2e8f0;
        height: fit-content;
    }
    
    .tracking-groups { 
        display: flex; 
        gap: 2rem; 
    }
    
    @media (max-width: 900px) { 
        .tracking-groups { 
            flex-direction: column; 
            gap: 2rem; 
        } 
    }
    
    .tracking-group { 
        flex: 1; 
    }
    
    .tracking-group-title { 
        font-size: 1.2rem; 
        color: #4a90e2; 
        font-weight: 700; 
        margin-bottom: 1.5rem; 
        text-align: center;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e2e8f0;
    }
    
    .tracking-step { 
        position: relative; 
        margin-bottom: 2rem; 
        min-height: 100px;
        padding-left: 3rem;
    }
    
    .tracking-step:last-child { 
        margin-bottom: 0; 
    }
    
    .tracking-step::before {
        content: '';
        position: absolute;
        left: 1.5rem;
        top: 2.5rem;
        bottom: -2rem;
        width: 2px;
        background: #e2e8f0;
        z-index: 1;
    }
    
    .tracking-step:last-child::before {
        display: none;
    }
    
    .tracking-step-icon { 
        position: absolute; 
        left: 0.5rem; 
        top: 0.5rem; 
        width: 2.5rem; 
        height: 2.5rem; 
        border-radius: 50%; 
        background: #fff; 
        border: 3px solid #e2e8f0; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        font-size: 1.2rem; 
        color: #94a3b8;
        z-index: 2;
        transition: all 0.3s ease;
    }
    
    .tracking-step.completed .tracking-step-icon { 
        background: #10b981; 
        color: #fff; 
        border-color: #10b981;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }
    
    .tracking-step.active .tracking-step-icon { 
        background: #f59e0b; 
        color: #fff; 
        border-color: #f59e0b;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        animation: pulse 2s infinite;
    }
    
    .tracking-step.pending .tracking-step-icon { 
        background: #f1f5f9; 
        color: #94a3b8; 
        border-color: #e2e8f0;
    }
    
    .tracking-step-title { 
        font-size: 1.1rem; 
        font-weight: 700; 
        color: #1a202c;
        margin-bottom: 0.3rem;
    }
    
    .tracking-step-desc { 
        color: #64748b; 
        font-size: 0.95rem;
        line-height: 1.5;
    }
    
    .tracking-step-date { 
        font-size: 0.9rem; 
        color: #64748b; 
        margin-bottom: 0.3rem;
        font-weight: 500;
    }
    
    .tracking-step-date i {
        color: #4a90e2;
        margin-right: 0.3rem;
    }
    
    .tracking-actions { 
        margin-top: 2rem; 
    }
    
    .btn-cancel-order { 
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); 
        color: #fff; 
        border: none; 
        border-radius: 12px; 
        padding: 0.8rem 2rem; 
        font-size: 1rem; 
        font-weight: 600; 
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    }
    
    .btn-cancel-order:hover { 
        background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(220, 38, 38, 0.4);
    }
    
    .tracking-map {
        background: #fff;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: 1px solid #e2e8f0;
        margin-bottom: 2rem;
    }
    
    .tracking-map iframe {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }
    
    .badge {
        font-size: 0.85rem;
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
        font-weight: 600;
    }
    
    .badge.bg-primary {
        background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%) !important;
    }
    
    .badge.bg-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
    }
    
    .badge.bg-danger {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%) !important;
    }
    
    @keyframes pulse {
        0% {
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }
        50% {
            box-shadow: 0 4px 20px rgba(245, 158, 11, 0.5);
        }
        100% {
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }
    }
    
    @media (max-width: 768px) {
        .tracking-header {
            padding: 1.5rem;
        }
        
        .tracking-title {
            font-size: 1.5rem;
        }
        
        .order-products {
            padding: 1.5rem;
        }
        
        .tracking-timeline {
            padding: 1.5rem;
        }
        
        .product-item {
            padding: 0.8rem;
        }
        
        .product-image {
            width: 60px;
            height: 60px;
        }
        
        .tracking-step {
            padding-left: 2.5rem;
        }
        
        .tracking-step-icon {
            width: 2rem;
            height: 2rem;
            font-size: 1rem;
        }
    }
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
                        $isCancelled = $currentStatusName === 'Đã hủy';
                        // Tạo mảng mapping key => updated_at
                        $statusTimeMap = [];
                        foreach($orderStatusSteps as $step) {
                            $key = $statusMap[$step->status->name] ?? null;
                            if ($key) {
                                $statusTimeMap[$key] = $step->updated_at;
                            }
                        }
                    @endphp
                    @foreach($timeline as $i => $step)
                        @php
                            $stepStatus = 'pending';
                            if ($i < $currentIndex) {
                                $stepStatus = 'completed';
                            } elseif ($i == $currentIndex) {
                                $stepStatus = 'active';
                            }
                            // Nếu đã hủy, các bước từ 0 đến currentIndex đều là cancel
                            $isCancelStep = $isCancelled && $i <= $currentIndex;
                            $stepData = $orderStatusSteps->first(fn($s) => ($statusMap[$s->status->name] ?? null) === $step['key']);
                            $stepTime = $stepData?->created_at;
                            $stepNote = $stepData?->note;
                            $stepModifier = $stepData?->modifier?->name ?? ($stepData ? 'Hệ thống' : null);
                        @endphp
                        <div class="tracking-step {{ $stepStatus }}" style="min-height: 100px;">
                            <div class="tracking-step-icon" style="@if($isCancelStep) background: #dc2626; color: #fff; border-color: #dc2626; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3); @endif">
                                @if($isCancelStep)
                                    <i class="fas fa-times"></i>
                                @else
                                    @if($stepStatus === 'completed')
                                        <i class="fas fa-check"></i>
                                    @elseif($stepStatus === 'active')
                                        <i class="fas fa-truck"></i>
                                    @else
                                        <i class="fas fa-clock"></i>
                                    @endif
                                @endif
                            </div>
                            <div class="tracking-step-title">{{ $step['title'] }}</div>
                            @if($stepTime)
                                <div class="tracking-step-date">
                                    <i class="fas fa-clock me-1"></i>{{ \Carbon\Carbon::parse($stepTime)->format('d/m/Y H:i') }}
                                    @if($stepModifier)
                                        <span class="ms-2"><i class="fas fa-user me-1"></i>{{ $stepModifier }}</span>
                                    @endif
                                </div>
                            @endif
                            @if($stepNote)
                                <div class="tracking-step-desc">{{ $stepNote }}</div>
                            @else
                                <div class="tracking-step-desc">{{ $step['desc'] }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
                <!-- Thông tin thanh toán -->
                @php
                    $currentStatusName = optional(optional($order->currentStatus)->status)->name ?? 'Chờ xác nhận';
                    $showPaymentStatus = $currentStatusName === 'Giao hàng thành công';
                @endphp
                @if($showPaymentStatus)
                <div class="order-products mt-4">
                    <div class="order-products-title">
                        <i class="fas fa-credit-card me-2"></i> Trạng thái thanh toán
                    </div>
                    <div class="product-item" style="background: #d4edda; border: 1px solid #c3e6cb;">
                        <div class="product-info">
                            <div class="product-name" style="color: #155724; font-weight: 600;">
                                <i class="fas fa-check-circle me-2"></i>Đã thanh toán
                            </div>
                            <div class="product-variant" style="color: #155724;">
                                Đơn hàng đã được thanh toán thành công
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                
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
                                        @php
                                            $attributes = json_decode($item->attributes_variant, true) ?? [];
                                        @endphp
                                        @if(!empty($attributes))
                                            @foreach($attributes as $attrName => $attrValue)
                                                <span class="badge bg-secondary me-1">{{ $attrName }}: {{ $attrValue }}</span>
                                            @endforeach
                                        @elseif($item->variant)
                                            @foreach($item->variant->attributeValues as $attrValue)
                                                <span class="badge bg-secondary me-1">{{ $attrValue->attribute->name }}: {{ $attrValue->value }}</span>
                                            @endforeach
                                        @endif
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
       
    </div>
</div>
@endsection 