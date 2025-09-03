@extends('admin.layouts.app')

@section('title', 'Quản lý hoàn tiền')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 fw-bold text-gray-800">Danh sách yêu cầu hoàn tiền</h1>
                <p class="text-muted mt-1">Quản lý các yêu cầu hoàn tiền trong hệ thống</p>
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



        <!-- Thống kê nhanh -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Chờ Xử Lý</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $stats['pending'] ?? 0 }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Đang Nhận Hàng</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $stats['receiving'] ?? 0 }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-truck fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Hoàn Thành</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $stats['completed'] ?? 0 }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Từ Chối/Hủy</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ ($stats['rejected'] ?? 0) + ($stats['failed'] ?? 0) + ($stats['cancel'] ?? 0) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm rounded-3 border-0">
            <div class="card-body p-4">
                <div class="row mb-4">
                    <div class="col-md-12">
                        <form action="{{ route('admin.refunds.index') }}" method="GET" class="d-flex gap-2">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm mã đơn hàng, tên khách hàng..."
                                    value="{{ request('search') }}">
                                <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
                <ul class="nav nav-tabs mb-3">
                    <li class="nav-item">
                        <a class="nav-link {{ request('status') == '' ? 'active' : '' }}"
                            href="{{ route('admin.refunds.index') }}">
                            <i class="fas fa-list me-1"></i> Tất cả ({{ $refunds->total() }})
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request('status') == 'pending' ? 'active' : '' }}"
                            href="{{ route('admin.refunds.index', ['status' => 'pending']) }}">
                            <i class="fas fa-hourglass-half me-1"></i> Chờ xử lý ({{ $stats['pending'] ?? 0 }})
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request('status') == 'receiving' ? 'active' : '' }}"
                            href="{{ route('admin.refunds.index', ['status' => 'receiving']) }}">
                            <i class="fas fa-truck-loading me-1"></i> Đang nhận hàng ({{ $stats['receiving'] ?? 0 }})
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request('status') == 'completed' ? 'active' : '' }}"
                            href="{{ route('admin.refunds.index', ['status' => 'completed']) }}">
                            <i class="fas fa-check-circle me-1"></i> Hoàn thành ({{ $stats['completed'] ?? 0 }})
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request('status') == 'rejected' ? 'active' : '' }}"
                            href="{{ route('admin.refunds.index', ['status' => 'rejected']) }}">
                            <i class="fas fa-times-circle me-1"></i> Từ chối ({{ $stats['rejected'] ?? 0 }})
                        </a>
                    </li>
                </ul>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th><i class="fas fa-shopping-cart me-1"></i> MÃ ĐƠN HÀNG</th>
                                <th><i class="fas fa-user me-1"></i> KHÁCH HÀNG</th>
                                <th><i class="fas fa-coins me-1"></i> SỐ TIỀN</th>
                                <th><i class="fas fa-exclamation-triangle me-1"></i> LÝ DO</th>
                                <th><i class="fas fa-check-square me-1"></i> TRẠNG THÁI</th>
                                <th><i class="fas fa-calendar-alt me-1"></i> NGÀY TẠO</th>
                                <th><i class="fas fa-cog me-1"></i> HÀNH ĐỘNG</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($refunds->count() > 0)
                                @foreach ($refunds as $refund)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $refund->order_id) }}"
                                               class="text-primary fw-bold text-decoration-none">
                                                {{ $refund->order ? $refund->order->code : 'N/A' }}
                                            </a>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $refund->user ? $refund->user->fullname : 'N/A' }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $refund->user ? $refund->user->email : 'N/A' }}</small>
                                                <br>
                                                <small class="text-muted">{{ $refund->user ? $refund->user->phone_number : 'N/A' }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <strong class="text-danger">{{ number_format($refund->total_amount, 0, ',', '.') }} ₫</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-info text-white">
                                                {{ \App\Http\Controllers\RefundController::getReasonText($refund->reason) }}
                                            </span>
                                            @if($refund->reason_image)
                                                <br><small class="text-success"><i class="fas fa-image"></i> Có ảnh</small>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $statusClasses = [
                                                    'pending' => 'bg-warning text-dark',
                                                    'receiving' => 'bg-info text-white',
                                                    'completed' => 'bg-success text-white',
                                                    'rejected' => 'bg-danger text-white',
                                                    'failed' => 'bg-danger text-white',
                                                    'cancel' => 'bg-secondary text-white'
                                                ];
                                            @endphp
                                            <span class="badge rounded-pill {{ $statusClasses[$refund->status] ?? 'bg-secondary text-white' }}">
                                                {{ \App\Http\Controllers\RefundController::getReasonText($refund->status) }}
                                            </span>
                                            @if($refund->is_send_money)
                                                <br><small class="text-success"><i class="fas fa-check"></i> Đã chuyển tiền</small>
                                            @endif
                                            @if($refund->order && $refund->order->payment_id == 2)
                                                <br><small class="text-info"><i class="fab fa-cc-visa"></i> VNPay</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($refund->created_at)
                                                {{ $refund->created_at->format('d/m/Y, H:i') }}
                                                <br>
                                                <small class="text-muted">{{ $refund->created_at->diffForHumans() }}</small>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.refunds.show', $refund->id) }}"
                                                   class="btn btn-primary btn-sm" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                @if($refund->status == 'pending')

                                                @elseif($refund->status == 'receiving')
                                                    <button type="button" class="btn btn-success btn-sm"
                                                            onclick="quickComplete({{ $refund->id }})" title="Hoàn thành">
                                                        <i class="fas fa-check-circle"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <p>Không có yêu cầu hoàn tiền nào</p>

                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Phân trang -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Hiển thị {{ $refunds->firstItem() ?? 0 }} đến {{ $refunds->lastItem() ?? 0 }}
                        trong tổng số {{ $refunds->total() }} kết quả
                    </div>
                    <div>
                        {{ $refunds->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Modal xác nhận nhanh -->
<div class="modal fade" id="quickActionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Xác nhận hành động</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="modalMessage">Bạn có chắc chắn muốn thực hiện hành động này?</p>
                <form id="quickActionForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" id="actionStatus">
                    <div class="form-group">
                        <label for="admin_reason">Lý do:</label>
                        <textarea name="admin_reason" id="admin_reason" class="form-control" rows="3" placeholder="Nhập lý do (bắt buộc khi từ chối)"></textarea>
                        <small class="form-text text-muted">Lý do này sẽ được gửi đến khách hàng qua email</small>
                    </div>
                    <div class="form-group">
                        <label for="is_send_money">Trạng thái chuyển tiền:</label>
                        <select name="is_send_money" id="is_send_money" class="form-control">
                            <option value="0">Chưa chuyển</option>
                            <option value="1">Đã chuyển</option>
                        </select>
                        <small class="form-text text-muted">Chọn "Đã chuyển" khi hoàn thành yêu cầu</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="confirmAction">Xác nhận</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function quickApprove(refundId) {
    $('#modalTitle').text('Chấp nhận yêu cầu hoàn tiền');
    $('#modalMessage').text('Bạn có chắc chắn muốn chấp nhận yêu cầu hoàn tiền này?');
    $('#actionStatus').val('receiving');
    $('#quickActionForm').attr('action', '{{ route("admin.refunds.update", ":id") }}'.replace(':id', refundId));
    $('#quickActionModal').modal('show');
}

function quickReject(refundId) {
    $('#modalTitle').text('Từ chối yêu cầu hoàn tiền');
    $('#modalMessage').text('Bạn có chắc chắn muốn từ chối yêu cầu hoàn tiền này?');
    $('#actionStatus').val('rejected');
    $('#quickActionForm').attr('action', '{{ route("admin.refunds.update", ":id") }}'.replace(':id', refundId));
    $('#admin_reason').attr('required', true).attr('placeholder', 'Vui lòng nhập lý do từ chối...');
    $('#quickActionModal').modal('show');
}

function quickComplete(refundId) {
    $('#modalTitle').text('Hoàn thành yêu cầu hoàn tiền');
    $('#modalMessage').text('Bạn có chắc chắn muốn hoàn thành yêu cầu hoàn tiền này?');
    $('#actionStatus').val('completed');
    $('#quickActionForm').attr('action', '{{ route("admin.refunds.update", ":id") }}'.replace(':id', refundId));
    $('#is_send_money').val('1');
    $('#quickActionModal').modal('show');
}

$('#confirmAction').click(function() {
    const form = $('#quickActionForm');
    const formData = new FormData(form[0]);
    const $button = $(this);

    // Disable button to prevent double click
    $button.prop('disabled', true).text('Đang xử lý...');

    $.ajax({
        url: form.attr('action'),
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        success: function(response) {
            $('#quickActionModal').modal('hide');
            toastr.success('Cập nhật trạng thái thành công!');
            setTimeout(() => window.location.reload(), 1500);
        },
        error: function(xhr) {
            let errorMessage = 'Có lỗi xảy ra!';

            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                const errors = xhr.responseJSON.errors;
                errorMessage = Object.values(errors).flat().join(', ');
            }

            toastr.error(errorMessage);
        },
        complete: function() {
            $button.prop('disabled', false).text('Xác nhận');
        }
    });
});

// Reset form khi đóng modal
$('#quickActionModal').on('hidden.bs.modal', function() {
    $('#admin_reason').removeAttr('required').removeAttr('placeholder').val('');
    $('#is_send_money').val('0');
});

// Validation trước khi submit
$('#quickActionForm').on('submit', function(e) {
    e.preventDefault();

    const status = $('#actionStatus').val();
    const adminReason = $('#admin_reason').val().trim();

    if (status === 'rejected' && !adminReason) {
        toastr.error('Vui lòng nhập lý do từ chối!');
        $('#admin_reason').focus();
        return false;
    }

    // Nếu validation pass, trigger click button
    $('#confirmAction').click();
});
</script>
@endsection
