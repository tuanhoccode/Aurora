@extends('admin.layouts.app')

<<<<<<< HEAD
@section('title', 'Quản lý Sản phẩm')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Quản lý Sản phẩm</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Sản phẩm</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#lowStockModal">
                <i class="fas fa-exclamation-triangle me-1"></i> Sắp hết hàng
                <span class="badge bg-white text-warning ms-1">7</span>
            </button>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#bestSellingModal">
                <i class="fas fa-chart-line me-1"></i> Bán chạy
                <span class="badge bg-white text-success ms-1">5</span>
            </button>
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Thêm sản phẩm
            </a>
            <div class="dropdown">
                <button class="btn btn-light" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-file-export me-2"></i>Xuất Excel</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-file-import me-2"></i>Nhập Excel</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-trash me-2"></i>Xóa đã chọn</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary bg-gradient text-white h-100 hover-shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Tổng sản phẩm</h6>
                            <h2 class="mb-0 display-6">123</h2>
                        </div>
                        <div class="icon-box bg-white bg-opacity-25 rounded-3 p-3">
                            <i class="fas fa-box fa-2x"></i>
                        </div>
                    </div>
                    <div class="mt-3 small">
                        <span class="text-white"><i class="fas fa-arrow-up me-1"></i>12% </span>
                        <span class="text-white-50">so với tháng trước</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success bg-gradient text-white h-100 hover-shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Đang kinh doanh</h6>
                            <h2 class="mb-0 display-6">95</h2>
                        </div>
                        <div class="icon-box bg-white bg-opacity-25 rounded-3 p-3">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                    <div class="mt-3 small">
                        <span class="text-white"><i class="fas fa-arrow-up me-1"></i>5% </span>
                        <span class="text-white-50">so với tháng trước</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning bg-gradient text-white h-100 hover-shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Sắp hết hàng</h6>
                            <h2 class="mb-0 display-6">7</h2>
                        </div>
                        <div class="icon-box bg-white bg-opacity-25 rounded-3 p-3">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                    <div class="mt-3 small">
                        <span class="text-white"><i class="fas fa-arrow-down me-1"></i>3% </span>
                        <span class="text-white-50">so với tháng trước</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger bg-gradient text-white h-100 hover-shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Hết hàng</h6>
                            <h2 class="mb-0 display-6">3</h2>
                        </div>
                        <div class="icon-box bg-white bg-opacity-25 rounded-3 p-3">
                            <i class="fas fa-times-circle fa-2x"></i>
                        </div>
                    </div>
                    <div class="mt-3 small">
                        <span class="text-white"><i class="fas fa-arrow-down me-1"></i>2% </span>
                        <span class="text-white-50">so với tháng trước</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product List Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Danh sách sản phẩm</h5>
            <div class="btn-group">
                <button type="button" class="btn btn-light active" data-bs-toggle="tooltip" title="Dạng bảng">
                    <i class="fas fa-list"></i>
                </button>
                <button type="button" class="btn btn-light" data-bs-toggle="tooltip" title="Dạng lưới">
                    <i class="fas fa-th"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Filter -->
            <form action="#" method="GET" class="mb-4">
                <div class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0" placeholder="Tìm theo tên hoặc mã sản phẩm...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select">
                            <option value="">Tất cả danh mục</option>
                            <option>Áo sơ mi nam</option>
                            <option>Áo thun nam</option>
                            <option>Quần jean nam</option>
                            <option>Quần tây nam</option>
                            <option>Áo khoác nam</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select">
                            <option value="">Tất cả trạng thái</option>
                            <option value="active">Đang kinh doanh</option>
                            <option value="inactive">Ngừng kinh doanh</option>
                            <option value="out_of_stock">Hết hàng</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="fas fa-filter me-1"></i> Lọc
                        </button>
                        <button type="reset" class="btn btn-light" data-bs-toggle="tooltip" title="Đặt lại">
                            <i class="fas fa-redo"></i>
                        </button>
                    </div>
                </div>
            </form>

            <!-- Bulk Actions -->
            <div class="bulk-actions mb-3 d-none">
                <div class="alert alert-light d-flex justify-content-between align-items-center mb-0">
                    <div>
                        <input type="checkbox" class="form-check-input me-2" id="selectAll">
                        <span class="selected-count">0 sản phẩm được chọn</span>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-light">
                            <i class="fas fa-edit me-1"></i> Sửa hàng loạt
                        </button>
                        <button type="button" class="btn btn-light text-danger">
                            <i class="fas fa-trash me-1"></i> Xóa đã chọn
                        </button>
                    </div>
                </div>
            </div>

            <!-- Product Table -->
=======
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
                        @if(request('search'))
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
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <select class="form-select" name="brand">
                        <option value="">Tất cả thương hiệu</option>
                        @foreach($brands as $brand)
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
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Đang kinh doanh</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Ngừng kinh doanh</option>
                        </select>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-filter"></i>
                        </button>
                        @if(request()->hasAny(['category', 'brand', 'status']))
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
>>>>>>> origin/main
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="40">
<<<<<<< HEAD
                                <input type="checkbox" class="form-check-input">
=======
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                    <label class="form-check-label" for="selectAll"></label>
                                </div>
>>>>>>> origin/main
                            </th>
                            <th width="60">ID</th>
                            <th width="80">Ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Mã SP</th>
                            <th>Danh mục</th>
<<<<<<< HEAD
                            <th>Giá bán</th>
                            <th>Tồn kho</th>
=======
                            <th>Thương hiệu</th>
                            <th>Giá bán</th>
                            <th>Tồn kho</th>
                            <th>Loại SP</th>
>>>>>>> origin/main
                            <th>Trạng thái</th>
                            <th width="100">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
<<<<<<< HEAD
                        @if ($products->count())
                            @foreach ($products as $product)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input" value="{{ $product->id }}">
                                    </td>
                                    <td>{{ $product->id }}</td>
                                    <td>
                                        @if ($product->thumbnail)
                                            <img src="{{ asset('storage/' . $product->thumbnail) }}" 
                                                 alt="{{ $product->name }}" 
                                                 class="rounded shadow-sm" 
                                                 style="width: 50px; height: 50px; object-fit: cover;"
                                                 data-bs-toggle="tooltip" title="Xem ảnh lớn">
                                        @else
                                            <div class="text-muted small text-center">
                                                <i class="bi bi-image me-1"></i>
                                                Không ảnh
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-medium text-primary">{{ $product->name }}</div>
                                        @if ($product->short_description)
                                            <small class="text-muted d-block">{{ Str::limit($product->short_description, 80) }}</small>
                                        @endif
                                        <div class="mt-1">
                                            @if ($product->status == 'new')
                                                <span class="badge bg-info">Mới</span>
                                            @endif
                                            @if ($product->status == 'sale')
                                                <span class="badge bg-danger">Giảm giá</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $product->sku }}</span>
                                    </td>
                                    <td>
                                        @if ($product->category)
                                            <span class="badge bg-light text-dark">{{ $product->category->name }}</span>
                                        @else
                                            <span class="badge bg-secondary text-white">Chưa phân loại</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($product->compare_price > 0 && $product->compare_price > $product->price)
                                            <div class="text-decoration-line-through text-muted small">{{ number_format($product->compare_price, 0, ',', '.') }}đ</div>
                                        @endif
                                        <div class="text-danger fw-bold">{{ number_format($product->price, 0, ',', '.') }}đ</div>
                                        @if ($product->compare_price > 0 && $product->compare_price > $product->price)
                                        @php
                                            $discount = (($product->compare_price - $product->price) / $product->compare_price) * 100;
                                        @endphp
                                        <small class="text-success">-{{ round($discount) }}%</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="badge {{ $product->stock > 10 ? 'bg-success' : ($product->stock > 0 ? 'bg-warning' : 'bg-danger') }} me-2">{{ $product->stock }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $product->status == 'published' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $product->status == 'published' ? 'Đang kinh doanh' : 'Ngừng kinh doanh' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Xem chi tiết">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Chỉnh sửa">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Xóa sản phẩm này">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="10" class="text-center text-muted">
                                    Không tìm thấy sản phẩm nào.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Low Stock Modal -->
<div class="modal fade" id="lowStockModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sản phẩm sắp hết hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Tồn kho</th>
                                <th>Đã bán</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://via.placeholder.com/40" class="rounded me-2">
                                        <div>
                                            <div class="fw-medium">Quần Jean Nam Slim Fit</div>
                                            <small class="text-muted">SKU: QJ-SLIM-002</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-warning">8</span>
                                </td>
                                <td>85</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-light">
                                        <i class="fas fa-plus me-1"></i> Nhập hàng
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://via.placeholder.com/40" class="rounded me-2">
                                        <div>
                                            <div class="fw-medium">Áo Thun Nam Cotton Basic</div>
                                            <small class="text-muted">SKU: ATN-BSC-003</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-warning">5</span>
                                </td>
                                <td>92</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-light">
                                        <i class="fas fa-plus me-1"></i> Nhập hàng
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary">Xuất báo cáo</button>
            </div>
        </div>
    </div>
</div>

<!-- Best Selling Modal -->
<div class="modal fade" id="bestSellingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sản phẩm bán chạy</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Đã bán</th>
                                <th>Doanh thu</th>
                                <th>Tồn kho</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://via.placeholder.com/40" class="rounded me-2">
                                        <div>
                                            <div class="fw-medium">Áo Sơ Mi Nam Dài Tay Linen Cao Cấp</div>
                                            <small class="text-muted">SKU: SM-LIN-001</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-medium">120</div>
                                    <small class="text-success">
                                        <i class="fas fa-arrow-up"></i> 15%
                                    </small>
                                </td>
                                <td>
                                    <div class="fw-medium">59.880.000đ</div>
                                    <small class="text-success">
                                        <i class="fas fa-arrow-up"></i> 12%
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-success">150</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://via.placeholder.com/40" class="rounded me-2">
                                        <div>
                                            <div class="fw-medium">Áo Thun Nam Cotton Basic</div>
                                            <small class="text-muted">SKU: ATN-BSC-003</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-medium">92</div>
                                    <small class="text-success">
                                        <i class="fas fa-arrow-up"></i> 8%
                                    </small>
                                </td>
                                <td>
                                    <div class="fw-medium">18.400.000đ</div>
                                    <small class="text-success">
                                        <i class="fas fa-arrow-up"></i> 5%
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-warning">5</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary">Xuất báo cáo</button>
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
$(document).ready(function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Bulk selection
    $('#selectAll').change(function() {
        $('tbody input[type="checkbox"]').prop('checked', $(this).prop('checked'));
        updateBulkActions();
    });

    $('tbody input[type="checkbox"]').change(function() {
        updateBulkActions();
    });

    function updateBulkActions() {
        var checkedCount = $('tbody input[type="checkbox"]:checked').length;
        if (checkedCount > 0) {
            $('.bulk-actions').removeClass('d-none');
            $('.selected-count').text(checkedCount + ' sản phẩm được chọn');
        } else {
            $('.bulk-actions').addClass('d-none');
        }
    }

    // Status toggle
    $('.form-check-input').change(function() {
        var status = $(this).prop('checked') ? 'active' : 'inactive';
        // Add your status update logic here
    });

    // Product image preview
    $('td img').click(function() {
        // Add your image preview logic here
    });
});
</script>
@endpush

@endsection
=======
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
                                    @php
                                        $totalStock = $product->variants->sum('stock');
                                    @endphp
                                    @if($totalStock > 0)
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle"></i>
                                            {{ number_format($totalStock) }} sản phẩm
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle"></i> Hết hàng
                                        </span>
                                    @endif
                                @else
                                    @if($product->stock > 0)
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle"></i>
                                            {{ number_format($product->stock) }} sản phẩm
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle"></i> Hết hàng
                                        </span>
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if($product->type === 'variant')
                                    <span class="badge bg-primary">
                                        <i class="bi bi-boxes"></i> Biến thể
                                    </span>
                                @else
                                    <span class="badge bg-info">
                                        <i class="bi bi-box"></i> Đơn giản
                                    </span>
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
                            <td colspan="12" class="text-center py-4">
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
>>>>>>> origin/main
