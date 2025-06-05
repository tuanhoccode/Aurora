@extends('admin.layouts.app')

@section('title', 'Quản lý Sản phẩm')

@section('content')
<div class="container-fluid py-4">
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold text-gray-800">Danh sách sản phẩm</h1>
            <p class="text-muted mt-1">Quản lý thông tin các sản phẩm trong hệ thống</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4">
                <i class="bi bi-plus-circle me-1"></i> Thêm mới
            </a>
            <a href="{{ route('admin.products.trash') }}" class="btn btn-outline-danger shadow-sm rounded-pill px-4">
                <i class="bi bi-trash3 me-1"></i> Thùng rác
                @if($trashedCount > 0)
                    <span class="badge bg-danger ms-1">{{ $trashedCount }}</span>
                @endif
            </a>
        </div>
    </div>

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

    {{-- Stats Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary bg-gradient text-white h-100 hover-shadow rounded-3 border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Tổng sản phẩm</h6>
                            <h2 class="mb-0 display-6">{{ number_format($totalProducts) }}</h2>
                        </div>
                        <div class="icon-box bg-white bg-opacity-25 rounded-3 p-3">
                            <i class="bi bi-box fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success bg-gradient text-white h-100 hover-shadow rounded-3 border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Đang kinh doanh</h6>
                            <h2 class="mb-0 display-6">{{ number_format($activeProducts) }}</h2>
                            <small>Tồn kho: {{ number_format($totalStock) }} sản phẩm</small>
                        </div>
                        <div class="icon-box bg-white bg-opacity-25 rounded-3 p-3">
                            <i class="bi bi-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning bg-gradient text-white h-100 hover-shadow rounded-3 border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Sắp hết hàng</h6>
                            <h2 class="mb-0 display-6">{{ number_format($lowStockProducts) }}</h2>
                            <small>Tồn < 10 sản phẩm</small>
                        </div>
                        <div class="icon-box bg-white bg-opacity-25 rounded-3 p-3">
                            <i class="bi bi-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger bg-gradient text-white h-100 hover-shadow rounded-3 border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Hết hàng</h6>
                            <h2 class="mb-0 display-6">{{ number_format($outOfStockProducts) }}</h2>
                        </div>
                        <div class="icon-box bg-white bg-opacity-25 rounded-3 p-3">
                            <i class="bi bi-x-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bulk Actions --}}
    <div class="mb-3 bulk-actions" style="display: none;">
        <div class="btn-group">
            <button type="button" class="btn btn-success" onclick="bulkToggleStatus(1)">
                <i class="bi bi-check-circle me-1"></i> Kích hoạt
                <span class="badge bg-white text-success ms-1 selected-count">0</span>
            </button>
            <button type="button" class="btn btn-secondary" onclick="bulkToggleStatus(0)">
                <i class="bi bi-x-circle me-1"></i> Vô hiệu
                <span class="badge bg-white text-secondary ms-1 selected-count">0</span>
            </button>
            <button type="button" class="btn btn-danger" onclick="confirmBulkDelete()">
                <i class="bi bi-trash me-1"></i> Xóa
                <span class="badge bg-white text-danger ms-1 selected-count">0</span>
            </button>
        </div>
    </div>

    {{-- Search & Filters --}}
    <div class="card shadow-sm rounded-3 border-0 mb-3">
        <div class="card-body">
            <form action="{{ route('admin.products.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}" 
                               placeholder="Tìm theo tên, mã sản phẩm...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="category">
                        <option value="">Tất cả danh mục</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="status">
                        <option value="">Tất cả trạng thái</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Đang kinh doanh</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Ngừng kinh doanh</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-funnel me-1"></i> Lọc
                        </button>
                        @if(request()->hasAny(['search', 'category', 'status']))
                            <a href="{{ route('admin.products.index') }}" class="btn btn-light">
                                <i class="bi bi-x-circle"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Main Products Table --}}
    <div class="card shadow-sm rounded-3 border-0">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="40">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                    <label class="form-check-label" for="selectAll"></label>
                                </div>
                            </th>
                            <th width="60">ID</th>
                            <th width="80">Ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Mã SP</th>
                            <th>Danh mục</th>
                            <th>Thương hiệu</th>
                            <th>Giá bán</th>
                            <th>Tồn kho</th>
                            <th>Trạng thái</th>
                            <th width="120">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input product-checkbox" value="{{ $product->id }}" id="product-{{ $product->id }}">
                                    <label class="form-check-label" for="product-{{ $product->id }}"></label>
                                </div>
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
                            </td>
                            
                            <td>
                                <span class="badge bg-light text-dark border">{{ $product->sku }}</span>
                            </td>
                            <td>
                                <span class="badge bg-info text-white">{{ $product->category->name }}</span>
                            </td>
                            <td>
                                <span class="badge bg-primary text-white">{{ $product->brand->name }}</span>
                            </td>
                            <td>
                                @if($product->sale_price)
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
                                    <div class="progress flex-grow-1" style="height: 5px; width: 50px">
                                        <div class="progress-bar {{ $product->stock > 10 ? 'bg-success' : ($product->stock > 0 ? 'bg-warning' : 'bg-danger') }}" 
                                             style="width: {{ min(($product->stock / 100) * 100, 100) }}%">
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $product->is_active ? 'Đang kinh doanh' : 'Ngừng kinh doanh' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="{{ route('admin.products.edit', $product->id) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="Chỉnh sửa">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="{{ route('admin.products.show', $product->id) }}" 
                                       class="btn btn-sm btn-outline-info" 
                                       title="Chi tiết">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product->id) }}" 
                                          method="POST" 
                                          class="d-inline" 
                                          onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-outline-danger" 
                                                title="Xóa">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <div class="text-muted">
                                    @if(request()->hasAny(['search', 'category', 'status']))
                                        Không tìm thấy sản phẩm nào phù hợp với điều kiện lọc
                                    @else
                                        Chưa có sản phẩm nào
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Hiển thị {{ $products->firstItem() ?? 0 }} đến {{ $products->lastItem() ?? 0 }} 
                    trong tổng số {{ $products->total() ?? 0 }} sản phẩm
                </div>
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>


{{-- Bulk Delete Modal --}}
<div class="modal fade" id="bulkDeleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa <span class="selected-count">0</span> sản phẩm đã chọn?</p>
                <p class="text-muted mb-0">Các sản phẩm sẽ được chuyển vào thùng rác và có thể khôi phục lại sau.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" onclick="bulkDelete()">Xóa</button>
            </div>
        </div>
    </div>
</div>



@push('styles')
<style>
    .hover-shadow {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .hover-shadow:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important;
    }
    .icon-box {
        line-height: 1;
        opacity: 0.8;
    }
    .bulk-actions {
        animation: slideDown 0.3s ease-out;
    }
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endpush

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

function bulkToggleStatus(status) {
    const ids = getSelectedIds();
    if (ids.length === 0) return;

    fetch('{{ route("admin.products.bulk-toggle-status") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ ids, status })
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

function bulkDelete() {
    const ids = getSelectedIds();
    if (ids.length === 0) return;

    fetch('{{ route("admin.products.bulk-delete") }}', {
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
</script>
@endpush

@endsection