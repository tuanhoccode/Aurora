@extends('admin.layouts.app')

@section('title', 'Thùng rác sản phẩm')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold text-gray-800">Thùng rác sản phẩm</h1>
            <p class="text-muted mt-1">Danh sách sản phẩm đã xóa tạm thời</p>
        </div>
        <div>
            <a href="{{ route('admin.products.index') }}" class="btn btn-light rounded-pill shadow-sm"><i class="fas fa-arrow-left me-1"></i> Quay lại danh sách</a>
        </div>
    </div>
    {{-- Alert, table, badge, modal xác nhận xóa, ... --}}

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm rounded" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Bulk Actions --}}
    <div class="mb-3 bulk-actions" style="display: none;">
        <div class="btn-group">
            <button type="button" class="btn btn-success" onclick="bulkRestore()">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Khôi phục
                <span class="badge bg-white text-success ms-1 selected-count">0</span>
            </button>
            <button type="button" class="btn btn-danger" onclick="confirmBulkDelete()">
                <i class="bi bi-trash me-1"></i> Xóa vĩnh viễn
                <span class="badge bg-white text-danger ms-1 selected-count">0</span>
            </button>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="card shadow-sm rounded-3 border-0">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="40">
                                <input type="checkbox" class="form-check-input" id="selectAll">
                            </th>
                            <th width="60">ID</th>
                            <th width="80">Ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Loại</th>
                            <th>Mã SP</th>
                            <th>Danh mục</th>
                            <th>Thương hiệu</th>
                            <th>Giá bán</th>
                            <th>Tồn kho</th>
                            <th>Ngày xóa</th>
                            <th width="120">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trashedProducts as $product)
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input product-checkbox" value="{{ $product->id }}">
                            </td>
                            <td>{{ $product->id }}</td>
                            <td>
                                @if($product->thumbnail)
                                    <img src="{{ asset('storage/' . $product->thumbnail) }}" 
                                         class="rounded shadow-sm" 
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <img src="https://via.placeholder.com/50" 
                                         class="rounded shadow-sm" 
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                @endif
                            </td>
                            <td>
                                <div class="fw-medium text-primary">{{ $product->name }}</div>
                                <small class="text-muted d-block">{{ Str::limit($product->short_description, 100) }}</small>
                            </td>
                            <td>
                                <span class="badge {{ $product->type === 'digital' ? 'bg-info' : 'bg-secondary' }}">
                                    {{ $product->type === 'digital' ? 'Sản phẩm số' : 'Sản phẩm đơn giản' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $product->sku }}</span>
                            </td>
                            <td>
                                @foreach($product->categories as $category)
                                    <span class="badge bg-info text-white mb-1">{{ $category->name }}</span>
                                @endforeach
                            </td>
                            <td>
                                @if($product->brand)
                                    <span class="badge bg-primary text-white">{{ $product->brand->name }}</span>
                                @else
                                    <span class="badge bg-light text-muted">Không có</span>
                                @endif
                            </td>
                            <td>
                                @if($product->is_sale)
                                    <div class="text-decoration-line-through text-muted small">{{ number_format($product->price) }}đ</div>
                                    <div class="text-danger fw-bold">{{ number_format($product->sale_price) }}đ</div>
                                    <small class="text-success">-{{ number_format((($product->price - $product->sale_price) / $product->price) * 100) }}%</small>
                                @else
                                    <div class="fw-bold">{{ number_format($product->price) }}đ</div>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="badge {{ $product->stock > 10 ? 'bg-success' : ($product->stock > 0 ? 'bg-warning' : 'bg-danger') }} me-2">
                                        {{ number_format($product->stock) }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                {{ $product->deleted_at->format('d/m/Y H:i') }}
                            </td>
                            <td>
                                <div class="btn-group">
                                    <form id="restoreForm{{ $product->id }}" action="{{ route('admin.products.restore', $product->id) }}" method="POST" class="d-inline">
                                        @method('PUT')
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="Khôi phục">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.products.force-delete', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa vĩnh viễn sản phẩm này?')">
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

            @if($trashedProducts->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Hiển thị {{ $trashedProducts->firstItem() ?? 0 }} đến {{ $trashedProducts->lastItem() ?? 0 }} 
                    trong tổng số {{ $trashedProducts->total() ?? 0 }} sản phẩm
                </div>
                {{ $trashedProducts->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Bulk Delete Modal --}}
<div class="modal fade" id="bulkDeleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận xóa vĩnh viễn</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa vĩnh viễn <span class="selected-count">0</span> sản phẩm đã chọn?</p>
                <p class="text-danger mb-0">Lưu ý: Hành động này không thể khôi phục lại!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" onclick="bulkForceDelete()">Xóa vĩnh viễn</button>
            </div>
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
    const productCheckboxes = document.querySelectorAll('.product-checkbox');
    
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            productCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActions();
        });
    }

    // Individual checkboxes
    productCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });
});

function updateBulkActions() {
    const checkedCount = document.querySelectorAll('.product-checkbox:checked').length;
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
    const totalCheckboxes = document.querySelectorAll('.product-checkbox').length;
    if (selectAllCheckbox) {
        selectAllCheckbox.checked = checkedCount > 0 && checkedCount === totalCheckboxes;
        selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < totalCheckboxes;
    }
}

function getSelectedIds() {
    return Array.from(document.querySelectorAll('.product-checkbox:checked')).map(cb => cb.value);
}

function bulkRestore() {
    const ids = getSelectedIds();
    if (ids.length === 0) return;

    if (confirm(`Bạn có chắc muốn khôi phục ${ids.length} sản phẩm đã chọn?`)) {
        fetch('{{ route("admin.products.bulk-restore") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ ids })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.error || 'Có lỗi xảy ra');
            }
        })
        .catch(error => {
            alert('Có lỗi xảy ra: ' + error);
        });
    }
}

function confirmBulkDelete() {
    const count = document.querySelectorAll('.product-checkbox:checked').length;
    if (count === 0) return;

    const selectedCountElements = document.querySelectorAll('.selected-count');
    selectedCountElements.forEach(element => {
        element.textContent = count;
    });
    
    const modal = new bootstrap.Modal(document.getElementById('bulkDeleteModal'));
    modal.show();
}

function bulkForceDelete() {
    const ids = getSelectedIds();
    if (ids.length === 0) return;

    fetch('{{ route("admin.products.bulk-force-delete") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ ids })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.error || 'Có lỗi xảy ra');
        }
    })
    .catch(error => {
        alert('Có lỗi xảy ra: ' + error);
    });
}

function handleRestore(productId) {
    if (confirm('Bạn có chắc muốn khôi phục sản phẩm này?')) {
        const form = document.getElementById('restoreForm' + productId);
        if (form) {
            form.submit();
        }
    }
}
</script>
@endpush

@endsection 