<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận đơn hàng #{{ $order->code }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .order-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 10px 10px 0 0;
        }
        .order-details {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            margin: 1rem 0;
        }
        .product-item {
            border-bottom: 1px solid #dee2e6;
            padding: 1rem 0;
        }
        .product-item:last-child {
            border-bottom: none;
        }
        .total-section {
            background-color: #e9ecef;
            border-radius: 8px;
            padding: 1.5rem;
            margin-top: 1rem;
        }
        .status-badge {
            background-color: #28a745;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: bold;
        }
        .product-image {
            max-width: 80px;
            height: auto;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
        .product-image-placeholder {
            width: 80px;
            height: 80px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<body class="bg-light py-5">
    <div class="container">
        <div class="mx-auto col-md-10 col-lg-8">
            <div class="card shadow-sm rounded-4">
                <!-- Header -->
                <div class="order-header text-center">
                    <h2 class="mb-2">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        Đặt hàng thành công!
                    </h2>
                    <p class="mb-0">Cảm ơn bạn đã mua sắm tại Aurora</p>
                </div>

                <div class="card-body p-4">
                    <!-- Order Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">Thông tin đơn hàng</h5>
                            <p class="mb-1"><strong>Mã đơn hàng:</strong> <span class="text-primary">{{ $order->code }}</span></p>
                            <p class="mb-1"><strong>Ngày đặt:</strong> {{ $order->created_at->format('H:i:s d/m/Y') }}</p>
                            <p class="mb-1"><strong>Phương thức thanh toán:</strong> 
                                @if($order->payment_id == 1)
                                    <span class="badge bg-warning">Thanh toán khi nhận hàng (COD)</span>
                                @else
                                    <span class="badge bg-success">VNPay</span>
                                @endif
                            </p>
                            <p class="mb-1"><strong>Trạng thái thanh toán:</strong> 
                                @if($order->is_paid)
                                    <span class="badge bg-success">Đã thanh toán</span>
                                @else
                                    <span class="badge bg-warning">Chưa thanh toán</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">Thông tin giao hàng</h5>
                            <p class="mb-1"><strong>Người nhận:</strong> {{ $order->fullname }}</p>
                            <p class="mb-1"><strong>Số điện thoại:</strong> {{ $order->phone_number }}</p>
                            <p class="mb-1"><strong>Email:</strong> {{ $order->email }}</p>
                            <p class="mb-1"><strong>Địa chỉ:</strong> {{ $order->address }}</p>
                            <p class="mb-1"><strong>Phương thức vận chuyển:</strong> {{ $order->shipping_type }}</p>
                        </div>
                    </div>

                    <!-- Products -->
                    <div class="order-details">
                        <h5 class="text-primary mb-3">Chi tiết sản phẩm</h5>
                        @foreach($order->items as $item)
                        <div class="product-item">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    @if($item->product && $item->product->thumbnail)
                                        <img src="{{ $item->product->image_url }}" 
                                             alt="{{ $item->name }}" 
                                             class="product-image">
                                    @else
                                        <div class="product-image-placeholder">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <h6 class="mb-1">{{ $item->name }}</h6>
                                    @if($item->name_variant)
                                        <small class="text-muted">Biến thể: {{ $item->name_variant }}</small><br>
                                    @endif
                                    @if($item->attributes_variant)
                                        <small class="text-muted">
                                            @php
                                                $attributes = json_decode($item->attributes_variant, true);
                                                if($attributes) {
                                                    echo implode(', ', array_map(function($key, $value) {
                                                        return $key . ': ' . $value;
                                                    }, array_keys($attributes), $attributes));
                                                }
                                            @endphp
                                        </small>
                                    @endif
                                </div>
                                <div class="col-md-2 text-center">
                                    <span class="text-muted">SL: {{ $item->quantity }}</span>
                                </div>
                                <div class="col-md-2 text-end">
                                    <strong>{{ number_format($item->price) }} ₫</strong>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Totals -->
                    <div class="total-section">
                        <div class="row">
                            <div class="col-md-6 offset-md-6">
                                @php
                                    $shippingFee = $order->shipping_type === 'nhanh' ? 30000 : 16500;
                                    $subtotal = $order->total_amount - $shippingFee + $order->discount_amount;
                                @endphp
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Tạm tính:</span>
                                    <span>{{ number_format($subtotal) }} ₫</span>
                                </div>
                                @if($order->discount_amount > 0)
                                <div class="d-flex justify-content-between mb-2 text-success">
                                    <span>Giảm giá:</span>
                                    <span>-{{ number_format($order->discount_amount) }} ₫</span>
                                </div>
                                @endif
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Phí vận chuyển:</span>
                                    <span>{{ number_format($shippingFee) }} ₫</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <strong>Tổng cộng:</strong>
                                    <strong class="text-primary fs-5">{{ number_format($order->total_amount) }} ₫</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($order->note)
                    <div class="mt-3">
                        <h6 class="text-primary">Ghi chú:</h6>
                        <p class="text-muted">{{ $order->note }}</p>
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="text-center mt-4">
                        <a href="{{ route('client.orders.show', $order->id) }}" class="btn btn-primary px-4 me-2">
                            <i class="bi bi-eye me-1"></i>Xem chi tiết đơn hàng
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary px-4">
                            <i class="bi bi-house me-1"></i>Tiếp tục mua sắm
                        </a>
                    </div>

                    <!-- Footer -->
                    <div class="card-footer text-muted text-center small mt-4">
                        <p class="mb-1">Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi:</p>
                        <p class="mb-1">📧 Email: support@aurora.com</p>
                        <p class="mb-1">📞 Hotline: 1900-xxxx</p>
                        <p class="mb-0">Email này được gửi tự động. Vui lòng không trả lời</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html> 