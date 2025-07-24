@extends('admin.layouts.app')

@section('title', 'Quản lý sản phẩm')

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
                @if ($trashedCount > 0)
                    <span class="badge bg-danger ms-1">{{ $trashedCount }}</span>
                @endif
            </a>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm rounded" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm rounded-3 border-0">
        <div class="card-body p-4">
            {{-- Search and Filter Form --}}
            <div class="row mb-4">
                <div class="col-md-6">
                    <form action="{{ route('admin.products.index') }}" method="GET" class="d-flex gap-2">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm sản phẩm..." value="{{ request('search') }}">
                        </div>
                        <select name="status" class="form-select" style="width: auto" onchange="this.form.submit()">
                            <option value="">Tất cả trạng thái</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Đang kinh doanh</option>
                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Ngừng kinh doanh</option>
                        </select>
                    </form>
                </div>
                <div class="col-md-6 text-end">
                    {{-- Bulk Actions --}}
                    <button type="button" class="btn btn-success rounded-pill px-4 bulk-toggle-btn me-2" style="display: none;" onclick="bulkToggleStatus(1)" data-bs-toggle="tooltip" title="Kích hoạt đã chọn">
                        <i class="bi bi-check-circle me-1"></i>
                        <i class="bi bi-toggle-on"></i>
                        <span class="badge bg-white text-success ms-2 selected-count">0</span>
                    </button>
                    <button type="button" class="btn btn-secondary rounded-pill px-4 bulk-toggle-btn me-2" style="display: none;" onclick="bulkToggleStatus(0)" data-bs-toggle="tooltip" title="Vô hiệu đã chọn">
                        <i class="bi bi-x-circle me-1"></i>
                        <i class="bi bi-toggle-off"></i>
                        <span class="badge bg-white text-secondary ms-2 selected-count">0</span>
                    </button>
                    <button type="button" class="btn btn-danger rounded-pill px-4 bulk-delete-btn" style="display: none;" data-bs-toggle="tooltip" title="Xóa đã chọn">
                        <i class="bi bi-trash me-1"></i>
                        <i class="bi bi-check2-square"></i>
                        <span class="badge bg-white text-danger ms-2 selected-count">0</span>
                    </button>
                </div>
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 40px">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                </div>
                            </th>
                            <th style="width: 60px">Ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Mã SP</th>
                            <th>Giá bán</th>
                            <th>Danh mục</th>
                            <th>Thương hiệu</th>
                            <th>Tồn kho</th>
                            <th>Loại</th>
                            <th>Trạng thái</th>
                            <th style="width: 100px">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input product-checkbox" value="{{ $product->id }}" id="product-{{ $product->id }}">
                                </div>
                            </td>
                            <td>
                                @if($product->thumbnail)
                                    <img src="{{ asset('storage/' . $product->thumbnail) }}" class="rounded shadow-sm" style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <div class="text-muted small text-center" style="width:50px;height:50px;display:flex;align-items:center;justify-content:center;background:#f3f3f3;border-radius:8px;">
                                        <i class="bi bi-image" style="font-size:1.5rem;"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold text-primary">
                                    @if($product->slug)
                                        <a href="{{ route('client.product.show', $product->slug) }}" target="_blank" class="text-decoration-none text-primary">
                                            {{ $product->name }}
                                        </a>
                                    @else
                                        {{ $product->name }}
                                    @endif
                                </div>
                                <div class="text-muted small">{!! Str::limit($product->short_description, 40) !!}</div>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $product->sku }}</span></td>
                            <td>
                                @if ($product->type === 'variant' && $product->variants->count() > 0)
                                    @php $variant = $product->variants->first(); @endphp
                                    @if ($variant->sale_price > 0 && $variant->sale_price < $variant->regular_price)
                                        <span class="text-decoration-line-through text-muted">{{ number_format($variant->regular_price) }}đ</span>
                                        <span class="text-danger fw-bold ms-1">{{ number_format($variant->sale_price) }}đ</span>
                                        <span class="badge bg-danger ms-1">-{{ number_format((($variant->regular_price - $variant->sale_price) / $variant->regular_price) * 100, 1) }}%</span>
                                    @else
                                        <span>{{ number_format($variant->regular_price) }}đ</span>
                                    @endif
                                @else
                                    @if ($product->is_sale && $product->sale_price < $product->price)
                                        <span class="text-decoration-line-through text-muted">{{ number_format($product->price) }}đ</span>
                                        <span class="text-danger fw-bold ms-1">{{ number_format($product->sale_price) }}đ</span>
                                        <span class="badge bg-danger ms-1">-{{ number_format((($product->price - $product->sale_price) / $product->price) * 100, 1) }}%</span>
                                    @else
                                        <span>{{ number_format($product->price) }}đ</span>
                                    @endif
                                @endif
                            </td>
                            <td>
                                @foreach ($product->categories as $category)
                                    <span class="badge bg-info text-dark me-1">{{ $category->name }}</span>
                                @endforeach
                            </td>
                            <td>
                                @if ($product->brand)
                                    <span class="badge bg-secondary">{{ $product->brand->name }}</span>
                                @else
                                    <span class="badge bg-light text-dark border">Không có</span>
                                @endif
                            </td>
                            <td>
                                @if ($product->type === 'variant')
                                    @php $totalStock = $product->variants->sum('stock'); @endphp
                                    @if ($totalStock > 0)
                                        <span class="badge bg-success"><i class="bi bi-check-circle"></i> {{ number_format($totalStock) }}</span>
                                    @else
                                        <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Hết hàng</span>
                                    @endif
                                @else
                                    @if ($product->stock > 0)
                                        <span class="badge bg-success"><i class="bi bi-check-circle"></i> {{ number_format($product->stock) }}</span>
                                    @else
                                        <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Hết hàng</span>
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if ($product->type === 'variant')
                                    <span class="badge bg-primary"><i class="bi bi-boxes"></i> Biến thể</span>
                                @else
                                    <span class="badge bg-info"><i class="bi bi-box"></i> Đơn giản</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge rounded-pill {{ $product->is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} px-3 py-2">
                                    <i class="bi bi-circle-fill me-1 small"></i>
                                    {{ $product->is_active ? 'Đang kinh doanh' : 'Ngừng kinh doanh' }}
                                </span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-link text-dark p-0 m-0" type="button" id="dropdownMenu{{ $product->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-three-dots fs-4"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow rounded-3 py-2" aria-labelledby="dropdownMenu{{ $product->id }}" style="min-width: 180px;">
                                        <li>
                                            <a href="{{ route('admin.products.show', $product->id) }}" class="dropdown-item d-flex align-items-center gap-2">
                                                <i class="bi bi-eye text-primary"></i> <span>Xem chi tiết</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.products.edit', $product->id) }}" class="dropdown-item d-flex align-items-center gap-2">
                                                <i class="bi bi-pencil-square text-warning"></i> <span>Chỉnh sửa</span>
                                            </a>
                                        </li>
                                        <li>
                                            <button type="button" class="dropdown-item d-flex align-items-center gap-2 text-danger delete-product" data-id="{{ $product->id }}" data-name="{{ $product->name }}">
                                                <i class="bi bi-trash"></i> <span>Xóa</span>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" class="text-center py-4">
                                <div class="text-muted mb-1"><i class="bi bi-inbox fa-2x"></i></div>
                                Không có sản phẩm nào
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Hiển thị {{ $products->firstItem() }} đến {{ $products->lastItem() }} trong tổng số {{ $products->total() }} sản phẩm
                </div>
                <div>
                    {{ $products->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Xác nhận xóa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <i class="bi bi-exclamation-triangle text-danger display-4"></i>
                    </div>
                    <p class="text-center mb-0">
                        Bạn có chắc chắn muốn xóa sản phẩm "<span id="deleteProductName" class="fw-bold"></span>"?
                    </p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                    <form id="deleteForm" action="" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger rounded-pill px-4">Xóa</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Bulk Delete Confirmation Modal --}}
    <div class="modal fade" id="bulkDeleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Xác nhận xóa hàng loạt</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <i class="bi bi-exclamation-triangle text-danger display-4"></i>
                    </div>
                    <p class="text-center mb-0">
                        Bạn có chắc chắn muốn xóa <span id="bulkDeleteCount" class="fw-bold"></span> sản phẩm đã chọn?
                    </p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-danger rounded-pill px-4" onclick="submitBulkDelete()">Xóa</button>
                </div>
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
                let data = {
                    ids
                };

                switch (action) {
                    case 'activate':
                    case 'deactivate':
                        url = '{{ route('admin.products.bulk-toggle-status') }}';
                        data.status = action === 'activate' ? 1 : 0;
                        break;
                    case 'delete':
                        url = '{{ route('admin.products.bulk-delete') }}';
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
                    url: '{{ route('admin.products.bulk-toggle-status') }}',
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
            $('.delete-product').click(function(e) {
                if ($(this).data('locked')) {
                    alert('Sản phẩm đang có trong đơn hàng giao thành công và không thể xóa.');
                    e.preventDefault();
                    return false;
                }
                const id = $(this).data('id');
                const name = $(this).data('name');

                $('#deleteProductName').text(name);
                $('#deleteForm').attr('action', `/admin/products/${id}`);
                $('#deleteModal').modal('show');
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        .disabled-link {
            pointer-events: none;
            opacity: 0.6;
        }
    </style>
@endpush
