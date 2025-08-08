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
                        default => 'danger',
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

                            @if($order->canBeCancelled())
                                <button type="button" class="btn btn-sm btn-outline-danger" style="min-width: 40px; padding: 0.4rem 0.9rem; border-radius: 20px;" title="Hủy đơn hàng" onclick="openCancelModal({{ $order->id }}, '{{ $order->code }}')">
                                    <i class="fas fa-times"></i>
                                </button>
                            @elseif($order->isCancelled())
                                <button type="button" class="btn btn-sm btn-outline-danger" style="min-width: 40px; padding: 0.4rem 0.9rem; border-radius: 20px; opacity: 0.5; cursor: not-allowed;" title="Đơn hàng đã bị hủy" disabled>
                                    <i class="fas fa-times"></i>
                                </button>
                            @else
                                <button type="button" class="btn btn-sm btn-outline-danger" style="min-width: 40px; padding: 0.4rem 0.9rem; border-radius: 20px; opacity: 0.5; cursor: not-allowed;" title="Không thể hủy đơn hàng ở trạng thái này" disabled>
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

<!-- Modal Hủy đơn hàng -->
<div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelOrderModalLabel">Hủy đơn hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="cancelOrderForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="orderCode" class="form-label">Mã đơn hàng</label>
                        <input type="text" class="form-control" id="orderCode" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="cancel_reason" class="form-label">Lý do hủy đơn <span class="text-danger">*</span></label>
                        <select name="cancel_reason" id="cancel_reason" class="form-select" required>
                            <option value="">Chọn lý do hủy...</option>
                            <option value="Đặt nhầm sản phẩm">Đặt nhầm sản phẩm</option>
                            <option value="Muốn thay đổi thông tin đơn hàng">Muốn thay đổi thông tin đơn hàng</option>
                            <option value="Thay đổi phương thức thanh toán">Thay đổi phương thức thanh toán</option>
                            <option value="Giao hàng chậm">Xử lý đơn hàng chậm</option>
                            <option value="Đổi ý">Đổi ý</option>
                            <option value="Khác">Khác</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="cancel_note" class="form-label">Ghi chú (tùy chọn)</label>
                        <textarea name="cancel_note" id="cancel_note" class="form-control" rows="3" placeholder="Nhập thêm thông tin về lý do hủy đơn..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-2"></i>Xác nhận hủy đơn
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openCancelModal(orderId, orderCode) {
    document.getElementById('orderCode').value = orderCode;
    document.getElementById('cancelOrderForm').action = '{{ route("client.orders.cancel", ":orderId") }}'.replace(':orderId', orderId);
    
    // Reset form
    document.getElementById('cancel_reason').value = '';
    document.getElementById('cancel_note').value = '';
    
    // Show modal
    var modal = new bootstrap.Modal(document.getElementById('cancelOrderModal'));
    modal.show();
}

// Xử lý khi chọn "Khác" trong lý do hủy
document.getElementById('cancel_reason').addEventListener('change', function() {
    if (this.value === 'Khác') {
        this.style.display = 'none';
        var otherReasonInput = document.createElement('input');
        otherReasonInput.type = 'text';
        otherReasonInput.name = 'cancel_reason';
        otherReasonInput.className = 'form-control';
        otherReasonInput.placeholder = 'Nhập lý do hủy đơn...';
        otherReasonInput.required = true;
        this.parentNode.appendChild(otherReasonInput);
        otherReasonInput.focus();
    }
});

// Reset modal khi đóng
document.getElementById('cancelOrderModal').addEventListener('hidden.bs.modal', function() {
    var select = document.getElementById('cancel_reason');
    select.style.display = 'block';
    select.value = '';
    
    // Xóa input "Khác" nếu có
    var otherInput = select.parentNode.querySelector('input[name="cancel_reason"]');
    if (otherInput) {
        otherInput.remove();
    }
    
    document.getElementById('cancel_note').value = '';
});
</script>
@endsection
