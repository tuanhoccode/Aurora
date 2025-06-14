@extends('admin.layouts.app')

@section('title', 'Cập nhật sản phẩm')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="card bg-light-subtle border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-2">Cập nhật sản phẩm</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}" class="text-decoration-none">Sản phẩm</a></li>
                            <li class="breadcrumb-item active">Cập nhật</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
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
                            <label class="form-label fw-medium">Loại sản phẩm <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" name="type" id="productType" required>
                                <option value="simple" {{ old('type', $product->type) === 'simple' ? 'selected' : '' }}>Sản phẩm đơn giản</option>
                                <option value="digital" {{ old('type', $product->type) === 'digital' ? 'selected' : '' }}>Sản phẩm số</option>
                                <option value="variant" {{ old('type', $product->type) === 'variant' ? 'selected' : '' }}>Sản phẩm biến thể</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Variant Section -->
                        <div id="variantSection" class="mb-4" style="display: none;">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Biến thể sản phẩm</h6>
                                <a href="{{ route('admin.products.variants.create', $product) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-plus me-1"></i>
                                    Thêm biến thể
                                </a>
                            </div>
                            
                            @if($product->variants->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>SKU</th>
                                                <th>Giá</th>
                                                <th>Giá KM</th>
                                                <th>Kho</th>
                                                <th>Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($product->variants as $variant)
                                                <tr>
                                                    <td>{{ $variant->sku }}</td>
                                                    <td>{{ number_format($variant->regular_price) }}đ</td>
                                                    <td>{{ $variant->sale_price ? number_format($variant->sale_price) . 'đ' : '-' }}</td>
                                                    <td>
                                                        @if($variant->manage_stock)
                                                            <span class="badge bg-{{ $variant->stock_status === 'in_stock' ? 'success' : ($variant->stock_status === 'out_of_stock' ? 'danger' : 'warning') }}">
                                                                {{ $variant->stock_status === 'in_stock' ? 'Còn hàng' : ($variant->stock_status === 'out_of_stock' ? 'Hết hàng' : 'Đặt trước') }}
                                                            </span>
                                                        @else
                                                            <span class="badge bg-secondary">Không quản lý</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="{{ route('admin.products.variants.edit', ['product' => $product->id, 'variant' => $variant->id]) }}" 
                                                               class="btn btn-outline-primary">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <button type="button" class="btn btn-outline-danger" 
                                                                    onclick="if(confirm('Bạn có chắc muốn xóa biến thể này?')) { 
                                                                        document.getElementById('delete-variant-{{ $variant->id }}').submit(); 
                                                                    }">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                        <form id="delete-variant-{{ $variant->id }}" 
                                                              action="{{ route('admin.products.variants.destroy', ['product' => $product->id, 'variant' => $variant->id]) }}" 
                                                              method="POST" style="display: none;">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-light border text-center">
                                    Chưa có biến thể nào được tạo
                                </div>
                            @endif
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-medium">Tên sản phẩm <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                name="name" value="{{ old('name', $product->name) }}" required placeholder="Nhập tên sản phẩm">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Danh mục <span class="text-danger">*</span></label>
                                        <select class="form-select select2 @error('categories') is-invalid @enderror" 
                                            name="categories[]" multiple required>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" 
                                                    {{ in_array($category->id, old('categories', $product->categories->pluck('id')->toArray())) ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('categories')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Thương hiệu</label>
                                        <select class="form-select @error('brand_id') is-invalid @enderror" name="brand_id">
                                            <option value="">Chọn thương hiệu</option>
                                            @foreach($brands as $brand)
                                                <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                                    {{ $brand->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('brand_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Trạng thái</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" 
                                        {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label">Đang hoạt động</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Trạng thái khuyến mãi</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_sale" 
                                        {{ old('is_sale', $product->is_sale) ? 'checked' : '' }}>
                                    <label class="form-check-label">Đang khuyến mãi</label>
                                </div>
                            </div>
                        </div>

                        @if($product->type === 'simple')
                            <div class="mb-4">
                                <label class="form-label fw-medium">Số lượng tồn kho</label>
                                <input type="number" class="form-control @error('stock') is-invalid @enderror" 
                                    name="stock" value="{{ old('stock', $product->stock) }}" min="0">
                                @error('stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        <div class="mb-4">
                            <label class="form-label fw-medium">Giá bán</label>
                            <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                name="price" value="{{ old('price', $product->price) }}" step="0.01" min="0" required>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-medium">Giá khuyến mãi</label>
                            <input type="number" class="form-control @error('sale_price') is-invalid @enderror" 
                                name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" step="0.01" min="0">
                            @error('sale_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-medium">Mô tả ngắn</label>
                            <textarea class="form-control" name="short_description" rows="3"
                                placeholder="Nhập mô tả ngắn gọn về sản phẩm">{{ old('short_description', $product->short_description) }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-medium">Mô tả chi tiết</label>
                            <textarea name="description" id="description" class="form-control" rows="10">{{ old('description', $product->description) }}</textarea>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-medium">Hình ảnh sản phẩm</label>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="border rounded p-3 text-center position-relative bg-light">
                                        @if($product->thumbnail)
                                            <img src="{{ asset('storage/' . $product->thumbnail) }}" alt="Current thumbnail" 
                                                class="img-fluid mb-2" style="max-height: 150px;">
                                        @endif
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
                            <input class="form-check-input" type="checkbox" name="is_active" 
                                {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
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
                                <input type="text" class="form-control border-start-0" name="sku" 
                                    value="{{ old('sku', $product->sku) }}" placeholder="Ví dụ: SP001">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Giá bán <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">VNĐ</span>
                                <input type="number" class="form-control border-start-0 @error('price') is-invalid @enderror" 
                                    name="price" value="{{ old('price', $product->price) }}" required min="0" step="1000">
                            </div>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Giá khuyến mãi</label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('sale_price') is-invalid @enderror" 
                                       name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" 
                                       min="0" step="1000">
                                <span class="input-group-text">đ</span>
                            </div>
                            @error('sale_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Lưu sản phẩm
            </button>
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
<script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#description'))
        .catch(error => {
            console.error(error);
        });

    // Initialize Select2 for multiple category selection
    $('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Chọn danh mục'
    });

    // Handle variant section visibility
    $('#productType').change(function() {
        const isVariant = $(this).val() === 'variant';
        $('#variantSection').toggle(isVariant);
        
        // Hide stock input if product type is variant
        if (isVariant) {
            $('input[name="stock"]').closest('.col-md-6').hide();
        } else {
            $('input[name="stock"]').closest('.col-md-6').show();
        }
    });

    // Trigger change event on page load to set initial state
    $('#productType').trigger('change');
</script>
@endpush

@endsection