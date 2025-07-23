@extends('admin.layouts.app')

@section('content')
<div class="container">
    @if (session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
        </div>
    @endif

    <h1><i class="fas fa-shopping-cart me-2"></i> Đơn hàng</h1>
    <div class="mb-4">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link {{ request('filter') == '' ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
                    <i class="fas fa-list me-1"></i> Tất cả ({{ $orders->total() }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('filter') == 'pending_payment' ? 'active' : '' }}" href="{{ route('admin.orders.index', ['filter' => 'pending_payment']) }}">
                    <i class="fas fa-hourglass-half me-1"></i> Chờ thanh toán ({{ $pendingPaymentCount }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('filter') == 'unfulfilled' ? 'active' : '' }}" href="{{ route('admin.orders.index', ['filter' => 'unfulfilled']) }}">
                    <i class="fas fa-truck-loading me-1"></i> Chưa hoàn thành ({{ $unfulfilledCount }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('filter') == 'completed' ? 'active' : '' }}" href="{{ route('admin.orders.index', ['filter' => 'completed']) }}">
                    <i class="fas fa-check-circle me-1"></i> Đã hoàn thành ({{ $completedCount }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('filter') == 'refunded' ? 'active' : '' }}" href="{{ route('admin.orders.index', ['filter' => 'refunded']) }}">
                    <i class="fas fa-undo me-1"></i> Đã hoàn tiền ({{ $refundedCount }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('filter') == 'failed' ? 'active' : '' }}" href="{{ route('admin.orders.index', ['filter' => 'failed']) }}">
                    <i class="fas fa-times-circle me-1"></i> Thất bại ({{ $failedCount }})
                </a>
            </li>
        </ul>
        <div class="mt-3 input-group">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
            <input type="text" class="form-control" placeholder="Tìm kiếm đơn hàng" value="{{ request('search') }}" oninput="window.location.href='{{ route('admin.orders.index') }}?search='+this.value">
        </div>
    </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th><i class="fas fa-shopping-cart me-1"></i> ĐƠN HÀNG</th>
                <th><i class="fas fa-coins me-1"></i> TỔNG TIỀN</th>
                <th><i class="fas fa-user me-1"></i> KHÁCH HÀNG</th>
                <th><i class="fas fa-truck me-1"></i> LOẠI GIAO HÀNG</th>
                <th><i class="fas fa-credit-card me-1"></i> TRẠNG THÁI THANH TOÁN</th>
                <th><i class="fas fa-check-square me-1"></i> TRẠNG THÁI HOÀN THÀNH</th>
                <th><i class="fas fa-calendar-alt me-1"></i> NGÀY</th>
                <th><i class="fas fa-cog me-1"></i> HÀNH ĐỘNG</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
                <tr>
                    <td><a href="{{ route('admin.orders.show', $order->id) }}">#{{ $order->code }}</a></td>
                    <td>{{ number_format($order->total_amount, 0, ',', '.') }} ₫</td>
                    <td>{{ $order->fullname }}</td>
                    <td>{{ $order->shipping_type ?? 'Không xác định' }}</td>
                    <td>
                        @php
                            $status = $order->statusHistory()->where('is_current', true)->first();
                            $paymentStatus = $status ? match ($status->order_status_id) {
                                7 => 'Đã hoàn tiền',
                                8 => 'Đã hủy',
                                default => $order->is_paid ? 'Đã thanh toán' : 'Chờ thanh toán',
                            } : 'Chờ thanh toán';
                        @endphp
                        <span class="{{ $paymentStatus == 'Đã thanh toán' ? 'badge bg-success' : ($paymentStatus == 'Đã hoàn tiền' || $paymentStatus == 'Đã hủy' ? 'badge bg-danger' : 'badge bg-warning') }}">
                            {{ $paymentStatus }}
                        </span>
                    </td>
                    <td>
                        <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="is_paid" value="{{ $order->is_paid ? 1 : 0 }}">
                            <input type="hidden" name="customer_confirmation" value="0">
                            <select name="order_status_id" onchange="this.form.submit()" class="form-select">
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->id }}" {{ $status->id == ($order->statusHistory()->where('is_current', true)->first()?->order_status_id ?? 1) ? 'selected' : '' }}>
                                        {{ $status->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('order_status_id')
                                <div class="text-danger"><i class="fas fa-exclamation-circle me-1"></i> {{ $message }}</div>
                            @enderror
                        </form>
                    </td>
                    <td>{{ $order->created_at->format('d/m/Y, H:i') }}</td>
                    <td>
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye me-1"></i> Chi tiết
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-between align-items-center mt-3">
        <span><i class="fas fa-info-circle me-1"></i> {{ $orders->firstItem() }} đến {{ $orders->lastItem() }} trong số {{ $orders->total() }} đơn hàng</span>
        <div class="pagination-controls">
            @if ($orders->onFirstPage())
                <button class="btn btn-secondary btn-lg me-2" disabled><i class="fas fa-chevron-left"></i></button>
            @else
                <a href="{{ $orders->previousPageUrl() . (request('search') ? '&search=' . request('search') : '') . (request('filter') ? '&filter=' . request('filter') : '') }}" class="btn btn-primary btn-lg me-2">
                    <i class="fas fa-chevron-left"></i>
                </a>
            @endif
            @if ($orders->hasMorePages())
                <a href="{{ $orders->nextPageUrl() . (request('search') ? '&search=' . request('search') : '') . (request('filter') ? '&filter=' . request('filter') : '') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-chevron-right"></i>
                </a>
            @else
                <button class="btn btn-secondary btn-lg" disabled><i class="fas fa-chevron-right"></i></button>
            @endif
        </div>
    </div>
    <div class="mt-2">
        {{ $orders->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
    <div class="mt-2">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-link"><i class="fas fa-list me-1"></i> Xem tất cả</a>
    </div>
</div>
@endsection