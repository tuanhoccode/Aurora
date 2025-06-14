@extends('admin.layouts.app')

@section('title', 'Quản lý sản phẩm')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Quản lý sản phẩm</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.products.trash') }}" class="btn btn-outline-secondary">
                <i class="bi bi-trash"></i>
                Thùng rác @if($trashedCount > 0)<span class="badge bg-danger">{{ $trashedCount }}</span>@endif
            </a>
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i>
                Thêm sản phẩm
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-4 col-md-6">
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
        <div class="col-xl-4 col-md-6">
            <div class="card bg-success bg-gradient text-white h-100 hover-shadow rounded-3 border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Đang kinh doanh</h6>
                            <h2 class="mb-0 display-6">{{ number_format($activeProducts) }}</h2>
                        </div>
                        <div class="icon-box bg-white bg-opacity-25 rounded-3 p-3">
                            <i class="bi bi-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card bg-warning bg-gradient text-white h-100 hover-shadow rounded-3 border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Đang giảm giá</h6>
                            <h2 class="mb-0 display-6">{{ number_format($saleProducts) }}</h2>
                        </div>
                        <div class="icon-box bg-white bg-opacity-25 rounded-3 p-3">
                            <i class="bi bi-tag fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bulk Actions --}}
    <div class="bulk-actions bg-light rounded-3 p-3 mb-3" style="display: none;">
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-success bulk-action" data-action="activate">
                <i class="bi bi-check-circle"></i>
                Kích hoạt
            </button>
            <button type="button" class="btn btn-warning bulk-action" data-action="deactivate">
                <i class="bi bi-x-circle"></i>
                Vô hiệu
            </button>
            <button type="button" class="btn btn-danger bulk-action" data-action="delete">
                <i class="bi bi-trash"></i>
                Xóa
            </button>
            <button type="button" class="btn btn-light ms-auto cancel-bulk">
                <i class="bi bi-x-lg"></i>
                Hủy
            </button>
        </div>
    </div>

    {{-- Filters --}}
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
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="bi bi-search"></i>
                            Lọc
                        </button>
                        @if(request()->hasAny(['search', 'category', 'status']))
                            <a href="{{ route('admin.products.index') }}" class="btn btn-light">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Products Table --}}
    <div class="card shadow-sm rounded-3 border-0">
        <div class="card-body">
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
                            <th width="100">Thao tác</th>
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
                                <small class="text-muted">{{ $product->short_description }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $product->sku }}</span>
                            </td>
                            <td>
                                @foreach($product->categories as $category)
                                    <span class="badge bg-info">{{ $category->name }}</span>
                                @endforeach
                            </td>
                            <td>
                                @if($product->brand)
                                    <span class="badge bg-secondary">{{ $product->brand->name }}</span>
                                @else
                                    <span class="badge bg-light text-dark border">Không có</span>
                                @endif
                            </td>
                            <td>
                                @if($product->is_sale)
                                    <div class="text-decoration-line-through text-muted">
                                        {{ number_format($product->price) }}đ
                                    </div>
                                    <div class="fw-medium text-danger">
                                        {{ number_format($product->sale_price) }}đ
                                    </div>
                                    <small class="text-success">
                                        -{{ number_format((($product->price - $product->sale_price) / $product->price) * 100, 1) }}%
                                    </small>
                                @else
                                    <div class="fw-medium">
                                        {{ number_format($product->price) }}đ
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($product->type === 'variant')
                                    <span class="badge bg-info">Có biến thể</span>
                                @else
                                    @if($product->stock > 0)
                                        <span class="badge bg-success">{{ number_format($product->stock) }}</span>
                                    @else
                                        <span class="badge bg-danger">Hết hàng</span>
                                    @endif
                                @endif
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input toggle-status" 
                                           data-id="{{ $product->id }}"
                                           {{ $product->is_active ? 'checked' : '' }}>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.products.show', $product->id) }}" 
                                       class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.products.edit', $product->id) }}" 
                                       class="btn btn-sm btn-primary">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger delete-product" 
                                            data-id="{{ $product->id }}"
                                            data-name="{{ $product->name }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <div class="text-muted mb-1">
                                    <i class="bi bi-inbox fa-2x"></i>
                                </div>
                                Không có sản phẩm nào
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($products->hasPages())
                <hr class="my-0">
                <div class="p-3">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Delete Modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Xác nhận xóa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn xóa sản phẩm <strong id="deleteProductName"></strong>?</p>
                    <p class="mb-0 text-danger">Hành động này không thể hoàn tác!</p>
                </div>
                <div class="modal-footer">
                    <form id="deleteForm" action="" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i>
                            Xóa
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Select All
        $('#selectAll').change(function() {
            $('.product-checkbox').prop('checked', $(this).prop('checked'));
            toggleBulkActions();
        });

        // Individual checkbox
        $('.product-checkbox').change(function() {
            toggleBulkActions();
        });

        // Toggle bulk actions
        function toggleBulkActions() {
            if ($('.product-checkbox:checked').length > 0) {
                $('.bulk-actions').slideDown();
            } else {
                $('.bulk-actions').slideUp();
            }
        }

        // Cancel bulk actions
        $('.cancel-bulk').click(function() {
            $('.product-checkbox, #selectAll').prop('checked', false);
            toggleBulkActions();
        });

        // Bulk actions
        $('.bulk-action').click(function() {
            const action = $(this).data('action');
            const ids = $('.product-checkbox:checked').map(function() {
                return $(this).val();
            }).get();

            if (action === 'delete') {
                if (!confirm('Bạn có chắc chắn muốn xóa các sản phẩm đã chọn?')) {
                    return;
                }
            }

            let url = '';
            let method = 'POST';
            let data = { ids };

            switch (action) {
                case 'activate':
                case 'deactivate':
                    url = '{{ route("admin.products.bulk-toggle-status") }}';
                    data.status = action === 'activate' ? 1 : 0;
                    break;
                case 'delete':
                    url = '{{ route("admin.products.bulk-delete") }}';
                    break;
            }

            // Send request
            $.ajax({
                url: url,
                method: method,
                data: data,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    }
                },
                error: function(xhr) {
                    alert('Đã có lỗi xảy ra. Vui lòng thử lại sau.');
                }
            });
        });

        // Toggle status
        $('.toggle-status').change(function() {
            const id = $(this).data('id');
            const status = $(this).prop('checked') ? 1 : 0;

            $.ajax({
                url: '{{ route("admin.products.bulk-toggle-status") }}',
                method: 'POST',
                data: {
                    ids: [id],
                    status: status
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                error: function(xhr) {
                    alert('Đã có lỗi xảy ra. Vui lòng thử lại sau.');
                    // Revert the checkbox
                    $(this).prop('checked', !status);
                }
            });
        });

        // Delete confirmation
        $('.delete-product').click(function() {
            const id = $(this).data('id');
            const name = $(this).data('name');
            
            $('#deleteProductName').text(name);
            $('#deleteForm').attr('action', `/admin/products/${id}`);
            $('#deleteModal').modal('show');
        });
    });
</script>
@endpush