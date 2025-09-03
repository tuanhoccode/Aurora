@extends('admin.layouts.app')

@section('title', 'Quản lý đơn hàng')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 fw-bold text-gray-800">Danh sách đơn hàng</h1>
                <p class="text-muted mt-1">Quản lý các đơn hàng trong hệ thống</p>
            </div>
            <div class="d-flex gap-2">
                {{-- Nút bulk action, xuất excel, ... nếu có --}}
            </div>
        </div>
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm rounded" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        <div class="card shadow-sm rounded-3 border-0">
            <div class="card-body p-4">
                <div class="row mb-4">
                    <div class="col-md-12">
                        <form action="{{ route('admin.orders.index') }}" method="GET" class="d-flex gap-2">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm "
                                    value="{{ request('search') }}">
                                <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                            </div>
                            {{-- Có thể thêm filter trạng thái ở đây nếu muốn --}}
                        </form>
                    </div>
                    <div class="col-md-6 text-end">
                        {{-- Bulk Actions hoặc xuất excel nếu có --}}
                    </div>
                </div>
                <ul class="nav nav-tabs mb-3">
                    <li class="nav-item">
                        <a class="nav-link {{ request('filter') == '' ? 'active' : '' }}"
                            href="{{ route('admin.orders.index') }}">
                            <i class="fas fa-list me-1"></i> Tất cả ({{ $orders->total() }})
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request('filter') == 'pending_payment' ? 'active' : '' }}"
                            href="{{ route('admin.orders.index', ['filter' => 'pending_payment']) }}">
                            <i class="fas fa-hourglass-half me-1"></i> Chờ thanh toán ({{ $pendingPaymentCount }})
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request('filter') == 'unfulfilled' ? 'active' : '' }}"
                            href="{{ route('admin.orders.index', ['filter' => 'unfulfilled']) }}">
                            <i class="fas fa-truck-loading me-1"></i> Chưa hoàn thành ({{ $unfulfilledCount }})
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request('filter') == 'completed' ? 'active' : '' }}"
                            href="{{ route('admin.orders.index', ['filter' => 'completed']) }}">
                            <i class="fas fa-check-circle me-1"></i> Đã hoàn thành ({{ $completedCount }})
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request('filter') == 'refunded' ? 'active' : '' }}"
                            href="{{ route('admin.orders.index', ['filter' => 'refunded']) }}">
                            <i class="fas fa-undo me-1"></i> Đã hoàn tiền ({{ $refundedCount }})
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request('filter') == 'failed' ? 'active' : '' }}"
                            href="{{ route('admin.orders.index', ['filter' => 'failed']) }}">
                            <i class="fas fa-times-circle me-1"></i> Thất bại ({{ $failedCount }})
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request('filter') == 'cancelled' ? 'active' : '' }}"
                            href="{{ route('admin.orders.index', ['filter' => 'cancelled']) }}">
                            <i class="fas fa-ban me-1"></i> Đã hủy ({{ $cancelledCount ?? 0 }})
                        </a>
                    </li>
                </ul>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th><i class="fas fa-shopping-cart me-1"></i> ĐƠN HÀNG</th>
                                <th><i class="fas fa-coins me-1"></i> TỔNG TIỀN</th>
                                <th><i class="fas fa-user me-1"></i> KHÁCH HÀNG</th>
                                <th><i class="fas fa-truck me-1"></i> LOẠI GIAO HÀNG</th>
                                <th><i class="fas fa-credit-card me-1"></i> PHƯƠNG THỨC THANH TOÁN</th>
                                <th><i class="fas fa-credit-card me-1"></i> TRẠNG THÁI THANH TOÁN</th>
                                <th><i class="fas fa-check-square me-1"></i> TRẠNG THÁI ĐƠN HÀNG</th>
                                <th><i class="fas fa-calendar-alt me-1"></i> NGÀY</th>
                                <th><i class="fas fa-cog me-1"></i> HÀNH ĐỘNG</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td><a href="{{ route('admin.orders.show', $order->id) }}">#{{ $order->code }}</a>
                                    </td>
                                    <td>{{ number_format($order->total_amount, 0, ',', '.') }} ₫</td>
                                    <td>{{ $order->fullname }}</td>
                                    <td>{{ $order->shipping_type ?? 'Không xác định' }}</td>
                                    <td>
                                        @php
                                            $paymentMethod = strtolower(optional($order->payment)->name ?? 'cod');
                                            $paymentMethod = in_array($paymentMethod, ['cod', 'vnpay']) ? $paymentMethod : 'cod';
                                            $paymentMethodText = $paymentMethod === 'vnpay' ? 'VNPay' : 'COD';
                                        @endphp
                                        <span class="badge bg-info text-dark">{{ $paymentMethodText }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $status = $order->statusHistory()->where('is_current', true)->first();
                                            $paymentStatus = $status
                                                ? match ($status->order_status_id) {
                                                    7 => 'Đã hoàn tiền',
                                                    8 => 'Đã hủy',
                                                    default => $order->is_paid ? 'Đã thanh toán' : 'Chờ thanh toán',
                                                }
                                                : 'Chờ thanh toán';
                                        @endphp
                                        <span
                                            class="badge rounded-pill {{ $paymentStatus == 'Đã thanh toán' ? 'bg-success' : ($paymentStatus == 'Đã hoàn tiền' || $paymentStatus == 'Đã hủy' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                            {{ $paymentStatus }}
                                        </span>
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST"
                                            style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="is_paid" value="{{ $order->is_paid ? 1 : 0 }}">
                                            <input type="hidden" name="customer_confirmation" value="0">
                                            @php
                                                $currentStatusId = $order->statusHistory()->where('is_current', true)->first()?->order_status_id ?? 1;
                                                $validStatuses = isset($orderStatuses[$order->id]) ? $orderStatuses[$order->id] : [1];
                                                if (!is_array($validStatuses)) {
                                                    $validStatuses = [$validStatuses];
                                                }
                                            @endphp
                                            <select name="order_status_id" onchange="this.form.submit()"
                                                class="form-select form-select-sm rounded-pill">
                                                @foreach ($statuses as $status)
                                                    @if(in_array($status->id, $validStatuses))
                                                        <option value="{{ $status->id }}" {{ $status->id == $currentStatusId ? 'selected' : '' }}>
                                                            {{ $status->name }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            @error('order_status_id')
                                                <div class="text-danger"><i class="fas fa-exclamation-circle me-1"></i>
                                                    {{ $message }}</div>
                                            @enderror
                                        </form>
                                    </td>
                                    <td>{{ $order->created_at->format('d/m/Y, H:i') }}</td>
                                    <td>
                                        @if ($order->cancel_reason || $order->cancelled_at)
                                            <div class="mb-2">
                                                <span class="badge bg-danger rounded-pill">
                                                    <i class="fas fa-times-circle me-1"></i>Đã hủy
                                                </span>
                                                @if ($order->cancel_reason)
                                                    <br>
                                                    <small class="text-muted d-block mt-1">
                                                        <i
                                                            class="fas fa-exclamation-triangle me-1"></i>{{ Str::limit($order->cancel_reason, 30) }}
                                                    </small>
                                                @endif
                                                @if ($order->cancelled_at)
                                                    <br>
                                                    <small class="text-muted d-block">
                                                        <i
                                                            class="fas fa-clock me-1"></i>{{ \Carbon\Carbon::parse($order->cancelled_at)->format('d/m/Y H:i') }}
                                                    </small>
                                                @endif
                                            </div>
                                        @endif
                                        <a href="{{ route('admin.orders.show', $order->id) }}"
                                            class="btn btn-outline-primary btn-sm rounded-circle"
                                            title="Xem chi tiết đơn hàng" data-bs-toggle="tooltip"
                                            data-bs-placement="top">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
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
@endsection

@push('styles')
    <style>
        .btn-outline-primary.btn-sm.rounded-circle {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-width: 1.5px;
            transition: all 0.2s ease-in-out;
        }

        .btn-outline-primary.btn-sm.rounded-circle:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.2);
            border-color: #007bff;
            background-color: #007bff;
            color: white;
        }

        .btn-outline-primary.btn-sm.rounded-circle:active {
            transform: translateY(0);
            box-shadow: 0 2px 4px rgba(0, 123, 255, 0.2);
        }

        .btn-outline-primary.btn-sm.rounded-circle i {
            font-size: 0.875rem;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Khởi tạo tooltip cho các nút
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endpush
