@extends('admin.layouts.app')

@section('title', 'Chi tiết sản phẩm')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="card bg-light-subtle border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-2">Chi tiết sản phẩm</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}" class="text-decoration-none">Sản phẩm</a></li>
                            <li class="breadcrumb-item active">Chi tiết</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Quay lại
                    </a>
                    <a href="#" class="btn btn-primary">
                        <i class="fas fa-edit me-1"></i> Chỉnh sửa
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Basic Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header border-0 bg-white py-3">
                    <h5 class="card-title mb-0 text-primary">
                        <i class="fas fa-info-circle me-2"></i>Thông tin cơ bản
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center mb-4">
                        <div class="col-auto">
                            <img src="https://via.placeholder.com/150" 
                                alt="Ảnh sản phẩm" class="rounded shadow-sm" 
                                style="width: 150px; height: 150px; object-fit: cover;">
                        </div>
                        <div class="col">
                            <h4 class="mb-2">Áo Sơ Mi Nam Dài Tay Linen Cao Cấp</h4>
                            <p class="text-muted mb-2">Áo sơ mi nam chất liệu linen cao cấp, form regular fit thanh lịch</p>
                            <div class="d-flex gap-2">
                                <span class="badge bg-success">Đang kinh doanh</span>
                                <span class="badge bg-warning text-dark">Đang giảm giá</span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Mã sản phẩm (SKU)</label>
                            <p class="fw-medium mb-0">SM-LIN-001</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Danh mục</label>
                            <p class="fw-medium mb-0">Áo sơ mi nam</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Thương hiệu</label>
                            <p class="fw-medium mb-0">OWEN</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Ngày tạo</label>
                            <p class="fw-medium mb-0">15/03/2024 08:30</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="card border-0 shadow-sm">
                <div class="card-header border-0 bg-white py-3">
                    <h5 class="card-title mb-0 text-primary">
                        <i class="fas fa-file-alt me-2"></i>Mô tả chi tiết
                    </h5>
                </div>
                <div class="card-body">
                    <div class="border rounded p-3 bg-light">
                        <h5>Áo Sơ Mi Nam Dài Tay Linen Cao Cấp</h5>
                        <p>Sản phẩm cao cấp với nhiều ưu điểm nổi bật:</p>
                        <ul>
                            <li>Chất liệu: 100% Linen cao cấp</li>
                            <li>Form dáng: Regular fit thoải mái</li>
                            <li>Đường may tỉ mỉ, chắc chắn</li>
                            <li>Thiết kế basic dễ phối đồ</li>
                            <li>Phù hợp: Công sở, Dự tiệc, Đi chơi</li>
                        </ul>
                        <p><strong>Hướng dẫn bảo quản:</strong></p>
                        <ul>
                            <li>Giặt máy ở nhiệt độ thường</li>
                            <li>Không dùng chất tẩy</li>
                            <li>Ủi ở nhiệt độ thấp</li>
                            <li>Phơi trong bóng râm</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Pricing -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header border-0 bg-white py-3">
                    <h5 class="card-title mb-0 text-primary">
                        <i class="fas fa-tag me-2"></i>Giá & Kho hàng
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">Giá gốc</label>
                        <p class="h4 mb-0">599.000đ</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Giá khuyến mãi</label>
                        <p class="h4 text-danger mb-0">499.000đ</p>
                    </div>

                    <div class="mb-0">
                        <label class="form-label text-muted">Số lượng tồn kho</label>
                        <p class="h4 mb-0">150</p>
                    </div>
                </div>
            </div>

            <!-- Attributes -->
            <div class="card border-0 shadow-sm">
                <div class="card-header border-0 bg-white py-3">
                    <h5 class="card-title mb-0 text-primary">
                        <i class="fas fa-list me-2"></i>Thuộc tính
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">Kích thước</label>
                        <div class="d-flex flex-wrap gap-1">
                            <span class="badge bg-light text-dark border">S</span>
                            <span class="badge bg-light text-dark border">M</span>
                            <span class="badge bg-light text-dark border">L</span>
                            <span class="badge bg-light text-dark border">XL</span>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label text-muted">Màu sắc</label>
                        <div class="d-flex flex-wrap gap-1">
                            <span class="badge bg-light text-dark border">Trắng</span>
                            <span class="badge bg-light text-dark border">Xanh nhạt</span>
                            <span class="badge bg-light text-dark border">Be</span>
                            <span class="badge bg-light text-dark border">Ghi</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .form-label {
        color: #444;
    }
    .card {
        transition: all 0.2s ease;
    }
    .card:hover {
        transform: translateY(-2px);
    }
    .text-primary {
        color: #435ebe !important;
    }
    .btn-primary {
        background: #435ebe;
        border-color: #435ebe;
    }
    .btn-primary:hover {
        background: #364b96;
        border-color: #364b96;
    }
    .breadcrumb-item a {
        color: #435ebe;
    }
    .breadcrumb-item a:hover {
        color: #364b96;
    }
</style>
@endpush

<<<<<<< HEAD
@endsection
=======
@endsection
>>>>>>> e8e389e42114628c2ec091e6ab6b4822ed692086
