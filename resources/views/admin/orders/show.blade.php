@extends('admin.layouts.app')

@section('title', 'Chi tiết đơn hàng #' . $order->code)

@push('styles')
<style>
    .section-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        padding: 24px;
        margin-bottom: 24px;
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #111827;
        margin-bottom: 16px;
        border-left: 4px solid #6366F1; /* indigo-500 */
        padding-left: 10px;
    }

    .info-item {
        margin-bottom: 10px;
        font-size: 1rem;
    }

    .btn-group {
        display: flex;
        gap: 12px;
        margin-bottom: 24px;
    }

    .btn-custom {
        padding: 10px 20px;
        font-weight: 600;
        border-radius: 8px;
        border: none;
        transition: 0.2s ease;
        text-decoration: none;
        display: inline-block;
    }

    .btn-back {
        background-color: #6B7280; /* gray-500 */
        color: white;
    }

    .btn-back:hover {
        background-color: #4B5563;
    }

    .btn-update {
        background-color: #6366F1;
        color: white;
    }

    .btn-update:hover {
        background-color: #4F46E5;
    }

    table {
        font-size: 0.95rem;
    }

    table th {
        background-color: #F3F4F6;
        text-transform: uppercase;
        font-weight: 600;
    }

    .badge-status {
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.875rem;
    }

    .badge-paid {
        background-color: #10B981; color: white;
    }

    .badge-unpaid {
        background-color: #F59E0B; color: white;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <h1 class="mb-4 fw-bold text-dark">Chi tiết đơn hàng <span class="text-primary">#{{ $order->code }}</span></h1>

    {{-- Thông tin đơn hàng --}}
    <div class="section-card">
        <h2 class="section-title">Thông tin đơn hàng</h2>
        <div class="row">
            <div class="col-md-6">
                <div class="info-item"><strong>Khách hàng:</strong> {{ $order->fullname }} ({{ $order->phone_number }})</div>
                <div class="info-item"><strong>Email:</strong> {{ $order->email }}</div>
                <div class="info-item"><strong>Địa chỉ:</strong> {{ $order->address }}, {{ $order->city }}</div>
            </div>
            <div class="col-md-6">
                <div class="info-item"><strong>Tổng tiền:</strong> {{ number_format($order->total_amount, 0, ',', '.') }} VNĐ</div>
                <div class="info-item">
                    <strong>Trạng thái thanh toán:</strong>
                    <span class="badge-status {{ $order->is_paid ? 'badge-paid' : 'badge-unpaid' }}">
                        {{ $order->is_paid ? 'Đã thanh toán' : 'Chưa thanh toán' }}
                    </span>
                </div>
                <div class="info-item"><strong>Ghi chú:</strong> {{ $order->note ?? 'Không có' }}</div>
            </div>
        </div>
    </div>

    {{-- Nút điều hướng --}}
    <div class="btn-group">
        <a href="{{ route('admin.orders.index') }}" class="btn-custom btn-back">← Quay lại</a>
        <a href="{{ route('admin.orders.updateStatus', $order->id) }}" class="btn-custom btn-update">Cập nhật trạng thái</a>
    </div>

    {{-- Danh sách sản phẩm --}}
    <div class="section-card">
        <h2 class="section-title">Danh sách sản phẩm</h2>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Tên sản phẩm</th>
                        <th>Biến thể</th>
                        <th>Giá</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->product_variant_id ?? 'Không có' }}</td>
                            <td>{{ number_format($item->price, 0, ',', '.') }} VNĐ</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->price * $item->quantity, 0, ',', '.') }} VNĐ</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
