@extends('admin.layouts.app')

@section('title', 'Thêm sản phẩm mới')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="card bg-light-subtle border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-2">Thêm sản phẩm mới</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}" class="text-decoration-none">Sản phẩm</a></li>
                            <li class="breadcrumb-item active">Thêm mới</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Quay lại
                    </a>
                    <button type="submit" form="productForm" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Lưu sản phẩm
                    </button>
                </div>
            </div>
        </div>
    </div>

    <form id="productForm" action="" method="POST" enctype="multipart/form-data">
        @csrf
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
                        <div class="mb-4">
                            <label class="form-label fw-medium">Tên sản phẩm <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                name="name" value="{{ old('name') }}" required placeholder="Nhập tên sản phẩm">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Danh mục <span class="text-danger">*</span></label>
                                <select class="form-select @error('category_id') is-invalid @enderror" name="category_id" required>
                                    <option value="">Chọn danh mục</option>
                                    <option value="1">Điện thoại</option>
                                    <option value="2">Laptop</option>
                                    <option value="3">Máy tính bảng</option>
                                    <option value="4">Phụ kiện</option>
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Thương hiệu</label>
                                <select class="form-select" name="brand_id">
                                    <option value="">Chọn thương hiệu</option>
                                    <option value="1">Apple</option>
                                    <option value="2">Samsung</option>
                                    <option value="3">Xiaomi</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-medium">Mô tả ngắn</label>
                            <textarea class="form-control" name="short_description" rows="3"
                                placeholder="Nhập mô tả ngắn gọn về sản phẩm">{{ old('short_description') }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-medium">Mô tả chi tiết</label>
                            <textarea name="description" id="description" class="form-control" rows="10">{{ old('description') }}</textarea>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-medium">Hình ảnh sản phẩm</label>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="border rounded p-3 text-center position-relative bg-light">
                                        <input type="file" class="form-control mb-2" name="thumbnail" accept="image/*">
                                        <small class="text-muted d-block">Ảnh đại diện sản phẩm</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="border rounded p-3 text-center position-relative bg-light">
                                        <input type="file" class="form-control mb-2" name="gallery[]" accept="image/*" multiple>
                                        <small class="text-muted d-block">Thư viện ảnh (tối đa 5 ảnh)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Status -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header border-0 bg-white py-3">
                        <h5 class="card-title mb-0 text-primary">
                            <i class="fas fa-toggle-on me-2"></i>Trạng thái
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="status" checked>
                            <label class="form-check-label fw-medium">Đang kinh doanh</label>
                        </div>
                    </div>
                </div>

                <!-- Pricing -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header border-0 bg-white py-3">
                        <h5 class="card-title mb-0 text-primary">
                            <i class="fas fa-tag me-2"></i>Giá & Kho hàng
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-medium">Mã sản phẩm (SKU)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">SKU</span>
                                <input type="text" class="form-control border-start-0" name="sku" value="{{ old('sku') }}"
                                    placeholder="Ví dụ: SP001">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Giá gốc <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">$</span>
                                <input type="number" class="form-control border-start-0 @error('regular_price') is-invalid @enderror" 
                                    name="regular_price" value="{{ old('regular_price') }}" required step="0.01">
                            </div>
                            @error('regular_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Giá khuyến mãi</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">$</span>
                                <input type="number" class="form-control border-start-0" name="sale_price" 
                                    value="{{ old('sale_price') }}" step="0.01">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Số lượng tồn kho <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('stock_quantity') is-invalid @enderror" 
                                name="stock_quantity" value="{{ old('stock_quantity') }}" required>
                            @error('stock_quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                            <label class="form-label fw-medium">Kích thước</label>
                            <select class="form-select" name="sizes[]" multiple>
                                <option>S</option>
                                <option>M</option>
                                <option>L</option>
                                <option>XL</option>
                            </select>
                            <small class="text-muted">Giữ Ctrl để chọn nhiều</small>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-medium">Màu sắc</label>
                            <input type="text" class="form-control" name="colors" value="{{ old('colors') }}"
                                placeholder="Ví dụ: Đen, Trắng, Xanh">
                            <small class="text-muted">Các màu cách nhau bởi dấu phẩy</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
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
    .form-check-input:checked {
        background-color: #435ebe;
        border-color: #435ebe;
    }
    .breadcrumb-item a {
        color: #435ebe;
    }
    .breadcrumb-item a:hover {
        color: #364b96;
    }
    .form-control:focus,
    .form-select:focus {
        border-color: #435ebe;
        box-shadow: 0 0 0 0.2rem rgba(67, 94, 190, 0.15);
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
    // Initialize CKEditor
    ClassicEditor
        .create(document.querySelector('#description'))
        .catch(error => {
            console.error(error);
        });

    // Form validation
    document.getElementById('productForm').addEventListener('submit', function(e) {
        var requiredFields = this.querySelectorAll('[required]');
        var isValid = true;

        requiredFields.forEach(function(field) {
            if (!field.value) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert('Vui lòng điền đầy đủ thông tin bắt buộc');
        }
    });
</script>
@endpush

@endsection

<<<<<<< HEAD


