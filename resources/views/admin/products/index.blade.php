@extends('admin.layouts.app')

@section('title', 'Quản lý sản phẩm')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Quản lý sản phẩm</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.products.trash') }}" class="btn btn-outline-secondary">
                <i class="bi bi-trash"></i>
                Thùng rác @if ($trashedCount > 0)
                    <span class="badge bg-danger">{{ $trashedCount }}</span>
                @endif
            </a>
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i>
                Thêm sản phẩm
            </a>
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

    {{-- Search --}}
    <div class="card shadow-sm rounded-3 border-0 mb-3">
        <div class="card-body">
            <form action="{{ route('admin.products.index') }}" method="GET" class="row g-3">
                <div class="col-12">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}"
                            placeholder="Tìm kiếm sản phẩm...">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i>
                        </button>
                        @if (request('search'))
                            <a href="{{ route('admin.products.index') }}" class="btn btn-light">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card shadow-sm rounded-3 border-0 mb-3">
        <div class="card-body">
            <form action="{{ route('admin.products.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <select class="form-select" name="category">
                        <option value="">Tất cả danh mục</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <select class="form-select" name="brand">
                        <option value="">Tất cả thương hiệu</option>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}" {{ request('brand') == $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <select class="form-select" name="status">
                            <option value="">Tất cả trạng thái</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Đang kinh doanh
                            </option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Ngừng kinh doanh
                            </option>
                        </select>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-filter"></i>
                        </button>
                        @if (request()->hasAny(['category', 'brand', 'status']))
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
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="40">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                </div>
                            </th>
                            <th width="80">Ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Mã SP</th>
                            <th>Giá bán</th>
                            <th>Danh mục</th>
                            <th>Thương hiệu</th>
                            <th>Tồn kho</th>
                            <th>Loại</th>
                            <th>Trạng thái</th>
                            <th width="100">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        @php
                          $hasLockedVariant = $product->variants->contains(function($variant) {
                            return $variant->orderItems()->whereHas('order.currentStatus', function($q) {
                              $q->where('order_status_id', 4)->where('is_current', 1);
                            })->exists();
                          });
                        @endphp
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input product-checkbox" value="{{ $product->id }}" id="product-{{ $product->id }}">
                                </div>
                            </td>
                            <td>
                                <img src="{{ $product->thumbnail ? asset('storage/' . $product->thumbnail) : 'https://via.placeholder.com/50' }}" class="rounded shadow-sm" style="width: 50px; height: 50px; object-fit: cover;">
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
                                <div class="text-muted small">{{ Str::limit($product->short_description, 40) }}</div>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $product->sku }}</span></td>
                            <td>
                                @if ($product->is_sale && $product->sale_price < $product->price)
                                    <span class="text-decoration-line-through text-muted">{{ number_format($product->price) }}đ</span>
                                    <span class="text-danger fw-bold ms-1">{{ number_format($product->sale_price) }}đ</span>
                                    <span class="badge bg-danger ms-1">-{{ number_format((($product->price - $product->sale_price) / $product->price) * 100, 1) }}%</span>
                                @else
                                    <span>{{ number_format($product->price) }}đ</span>
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
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input toggle-status" data-id="{{ $product->id }}" {{ $product->is_active ? 'checked' : '' }}>
                                </div>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-link text-secondary p-0" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-three-dots fs-5"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('admin.products.show', $product->id) }}"><i class="bi bi-eye me-2"></i>Xem</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.products.edit', $product->id) }}"><i class="bi bi-pencil-square me-2"></i>Chỉnh sửa</a></li>
                                        <li><a class="dropdown-item text-danger delete-product @if($hasLockedVariant) locked-delete @endif" href="#" data-id="{{ $product->id }}" data-name="{{ $product->name }}" @if($hasLockedVariant) data-locked="1" @endif><i class="bi bi-trash me-2"></i>Xóa</a></li>
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
