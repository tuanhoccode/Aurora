@extends('admin.layouts.app')

@section('title', 'Thùng rác - Sản phẩm')

@section('content')
<div class="container-fluid py-4">
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Thùng rác sản phẩm</h1>
            <p class="mb-0 text-muted">Quản lý các sản phẩm đã xóa tạm thời</p>
        </div>
        <div>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Quay lại
            </a>
        </div>
    </div>

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
                            <th>Giá bán</th>
                            <th>Tồn kho</th>
                            <th>Biến thể</th>
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
                                <span class="badge {{ $product->type === 'variant' ? 'bg-info' : 'bg-secondary' }}">
                                    {{ $product->type === 'variant' ? 'Có biến thể' : 'Đơn giản' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $product->sku }}</span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">{{ $product->category->name }}</span>
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
                                </div>
                            </td>
                            <td>
                                @if($product->type === 'variant')
                                    @if($product->variants->count() > 0)
                                        <span class="badge bg-success">{{ $product->variants->count() }} biến thể</span>
                                        <button type="button" 
                                                class="btn btn-sm btn-light border ms-1" 
                                                data-bs-toggle="popover" 
                                                data-bs-html="true"
                                                data-bs-content="
                                                    <div class='small'>
                                                        @foreach($product->variants->take(5) as $variant)
                                                            <div class='mb-1'>
                                                                @foreach($variant->attributeValues as $value)
                                                                    <span class='badge bg-light text-dark border'>
                                                                        {{ $value->attribute->name }}: {{ $value->value }}
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        @endforeach
                                                        @if($product->variants->count() > 5)
                                                            <div class='text-muted'>và {{ $product->variants->count() - 5 }} biến thể khác...</div>
                                                        @endif
                                                    </div>
                                                ">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    @else
                                        <span class="badge bg-warning">Chưa có biến thể</span>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">Không có</span>
                                @endif
                            </td>
                            <td>
                                {{ $product->deleted_at->format('d/m/Y H:i') }}
                            </td>
                            <td>
                                <div class="btn-group">
                                    <form action="{{ route('admin.products.restore', $product->id) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Bạn có chắc muốn khôi phục sản phẩm này?')">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" 
                                                class="btn btn-sm btn-success" 
                                                data-bs-toggle="tooltip" 
                                                title="Khôi phục">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.products.force-delete', $product->id) }}" 
                                          method="POST" 
                                          class="d-inline" 
                                          onsubmit="return confirm('Bạn có chắc muốn xóa vĩnh viễn sản phẩm này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-danger" 
                                                data-bs-toggle="tooltip" 
                                                title="Xóa vĩnh viễn">
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
$(document).ready(function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl, {
            trigger: 'click',
            placement: 'left'
        })
    });

    // Close other popovers when opening a new one
    document.addEventListener('click', function(e) {
        if (e.target.getAttribute('data-bs-toggle') === 'popover') {
            var currentPopover = e.target;
            popoverTriggerList.forEach(function(el) {
                if (el !== currentPopover) {
                    var popover = bootstrap.Popover.getInstance(el);
                    if (popover) {
                        popover.hide();
                    }
                }
            });
        }
    });

    // Select all checkbox
    $('#selectAll').change(function() {
        $('.product-checkbox').prop('checked', $(this).prop('checked'));
        updateBulkActions();
    });

    // Individual checkbox
    $('.product-checkbox').change(function() {
        updateBulkActions();
    });
});

function updateBulkActions() {
    var checkedCount = $('.product-checkbox:checked').length;
    if (checkedCount > 0) {
        $('.bulk-actions').show();
        $('.selected-count').text(checkedCount);
    } else {
        $('.bulk-actions').hide();
    }
}

function getSelectedIds() {
    return $('.product-checkbox:checked').map(function() {
        return $(this).val();
    }).get();
}

function bulkRestore() {
    var ids = getSelectedIds();
    if (ids.length === 0) return;

    $.ajax({
        url: '{{ route("admin.products.bulk-restore") }}',
        type: 'POST',
        data: {
            ids: ids,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.error || 'Có lỗi xảy ra');
            }
        },
        error: function(xhr) {
            alert('Có lỗi xảy ra: ' + xhr.responseJSON.error);
        }
    });
}

function confirmBulkDelete() {
    var count = $('.product-checkbox:checked').length;
    if (count === 0) return;

    $('.selected-count').text(count);
    new bootstrap.Modal(document.getElementById('bulkDeleteModal')).show();
}

function bulkForceDelete() {
    var ids = getSelectedIds();
    if (ids.length === 0) return;

    $.ajax({
        url: '{{ route("admin.products.bulk-force-delete") }}',
        type: 'POST',
        data: {
            ids: ids,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            location.reload();
        },
        error: function(xhr) {
            alert('Có lỗi xảy ra: ' + xhr.responseJSON.error);
        }
    });
}
</script>
@endpush

@endsection 