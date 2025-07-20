@extends('client.layouts.default')

@section('title', 'Danh sách đơn hàng của tôi')

@section('content')
<div class="container py-4">
    <h1>Danh sách đơn hàng</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($orders->isEmpty())
        <p>Bạn chưa có đơn hàng nào.</p>
    @else
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Mã đơn hàng</th>
                <th>Ngày tạo</th>
                <th>Tổng tiền</th>
                <th>Phương thức thanh toán</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                @php
                    $currentStatus = $order->currentStatus;
                    $statusId = $currentStatus ? $currentStatus->order_status_id : 1; // fallback về 1 (Chờ xác nhận)
                    $statusName = $currentStatus && $currentStatus->status ? $currentStatus->status->name : 'Chờ xác nhận';
                    $badgeClass = match ($statusId) {
                        1 => 'primary',
                        2 => 'info',
                        3 => 'warning',
                        4 => 'success',
                        5 => 'danger',
                        default => 'primary',
                    };
                @endphp
                <tr>
                    <td>{{ $order->code }}</td>
                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ number_format($order->total_amount) }} đ</td>
                    <td>
                        @if($order->payment && $order->payment->logo)
                            <img src="{{ $order->payment->logo_url }}" alt="{{ $order->payment->name }}" style="width: 24px; height: 24px; margin-right: 8px;">
                        @else
                            <i class="fas fa-money-bill-wave me-1"></i>
                        @endif
                            {{ $order->payment ? $order->payment->name : 'Chưa xác định' }}
                    </td>
                    <td><span class="badge bg-{{ $badgeClass }}">{{ $statusName }}</span></td>
                    <td>
                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; justify-content: center;">
                            <a href="{{ route('client.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary" style="min-width: 40px; padding: 0.4rem 0.9rem; border-radius: 20px;" title="Xem chi tiết">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('client.orders.tracking', $order->id) }}" class="btn btn-sm btn-outline-info" style="min-width: 40px; padding: 0.4rem 0.9rem; border-radius: 20px;" title="Theo dõi đơn hàng">
                                <i class="fas fa-map-marker-alt"></i>
                            </a>

                            @if($statusId === 1)
                                <form action="{{ route('client.orders.cancel', $order->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng này?');">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" style="min-width: 40px; padding: 0.4rem 0.9rem; border-radius: 20px;" title="Hủy đơn">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            @else
                                <button type="button" class="btn btn-sm btn-outline-danger" style="min-width: 40px; padding: 0.4rem 0.9rem; border-radius: 20px; opacity: 0.5; cursor: not-allowed;" title="Chỉ có thể hủy khi đơn hàng đang chờ xác nhận" disabled>
                                    <i class="fas fa-times"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endsection
