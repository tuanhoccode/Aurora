@extends('admin.layouts.app')

@section('title', 'Thùng rác - Sản phẩm')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold text-gray-800">Thùng rác bình luận</h1>
            <p class="mb-0 text-muted">Quản lý các bình luận đã xóa tạm thời</p>
        </div>
        <div>
            <a href="{{ route('admin.reviews.comments') }}" class="btn btn-light rounded-pill shadow-sm"><i class="bi bi-arrow-left me-1"></i> Quay lại</a>
        </div>
    </div>
    <div class="card shadow-sm rounded-3 border-0">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Người dùng</th>
                            <th>Sản phẩm</th>
                            <th>Nội dung</th>
                            <th>Trạng thái</th>
                            <th>Lý do</th>
                            <th>Thời gian xóa</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trashComments as $comment)
                        <tr>

                            <td>
                                <div class="fw-medium text-primary">{{ $comment->user ? $comment->user->fullname : 'N/A'  }}</div>
                                <!-- <small class="text-muted d-block">{{ Str::limit($comment->short_description, 100) }}</small> -->
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $comment->product ? $comment->product->name : 'N/A' }}</span>
                            </td>
                            <td>
                                {{$comment->content}}
                            </td>
                            <td>
                                @if ($comment->is_active === 1)
                                    <span class="badge d-inline-block text-center bg-success w-10" style="min-width: 110px;">
                                    <i class="fas fa-check-circle me-1"></i> Đã duyệt
                                    </span>
                                @elseif ($comment->is_active === 0 && $comment->reason)
                                    <span class="badge d-inline-block text-center bg-danger w-10" style="min-width: 110px;">
                                        <i class="fas fa-times-circle me-1"></i> Không duyệt
                                    </span>
                                @else
                                    <span class="badge d-inline-block text-center bg-warning text-dark w-10" style="min-width: 110px;">
                                        <i class="fas fa-clock me-1"></i> Chờ duyệt
                                    </span>
                                @endif
                            </td>
                            <td>
                                {{$comment->reason}}
                            </td>
                            <td>
                                {{ $comment->deleted_at ? $comment->deleted_at->format('d/m/Y H:i') : '' }}
                            </td>
                            <td>
                                <div class="btn-group">
                                    <form id="restoreForm{{ $comment->id }}" action="{{ route('admin.reviews.restore', $comment->id) }}" method="POST" class="d-inline">
                                        @method('PUT')
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="Khôi phục">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.reviews.forceDelete', $comment->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa vĩnh viễn sản phẩm này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Xóa vĩnh viễn">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" class="text-center py-4">
                                <div class="text-muted">Không có sản phẩm nào trong thùng rác</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($trashComments->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Hiển thị {{ $trashComments->firstItem() ?? 0 }} đến {{ $trashComments->lastItem() ?? 0 }} trong tổng số {{ $trashComments->total() ?? 0 }} bình luận
                </div>
                <div>
                    {{ $trashComments->links('pagination::bootstrap-5') }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>



@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Select all checkbox
    const selectAllCheckbox = document.getElementById('selectAll');
    const commentCheckboxes = document.querySelectorAll('.comment-checkbox');
    
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            commentCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActions();
        });
    }

    // Individual checkboxes
    commentCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });
});

function updateBulkActions() {
    const checkedCount = document.querySelectorAll('.comment-checkbox:checked').length;
    const bulkActions = document.querySelector('.bulk-actions');
    const selectedCountElements = document.querySelectorAll('.selected-count');
    
    if (bulkActions) {
        if (checkedCount > 0) {
            bulkActions.style.display = 'block';
            selectedCountElements.forEach(element => {
                element.textContent = checkedCount;
            });
        } else {
            bulkActions.style.display = 'none';
        }
    }

    // Update "Select All" checkbox state
    const selectAllCheckbox = document.getElementById('selectAll');
    const totalCheckboxes = document.querySelectorAll('.comment-checkbox').length;
    if (selectAllCheckbox) {
        selectAllCheckbox.checked = checkedCount > 0 && checkedCount === totalCheckboxes;
        selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < totalCheckboxes;
    }
}

function getSelectedIds() {
    return Array.from(document.querySelectorAll('.comment-checkbox:checked')).map(cb => cb.value);
}

function bulkRestore() {
    const ids = getSelectedIds();
    if (ids.length === 0) return;

    if (confirm(`Bạn có chắc muốn khôi phục ${ids.length} sản phẩm đã chọn?`)) {
        fetch('{{ route("admin.reviews.bulkRestore") }}', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: JSON.stringify({ ids })
    })
    .then(response => {
        if (!response.ok) throw new Error("HTTP status " + response.status);
        return response.json();
    })
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.error || 'Có lỗi xảy ra');
        }
    })
    .catch(error => {
        console.error('Lỗi chi tiết:', error);
        alert('Có lỗi xảy ra: ' + error.message);
    });

    }
}

function confirmBulkDelete() {
    const count = document.querySelectorAll('.comment-checkbox:checked').length;
    if (count === 0) return;

    const selectedCountElements = document.querySelectorAll('.selected-count');
    selectedCountElements.forEach(element => {
        element.textContent = count;
    });
    
    const modal = new bootstrap.Modal(document.getElementById('bulkDeleteModal'));
    modal.show();
}




</script>
@endpush

@endsection 