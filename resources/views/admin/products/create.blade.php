@extends('admin.layouts.app')

@section('title', 'Thêm sản phẩm mới')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0 text-gray-800">Thêm sản phẩm mới</h1>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                            Quay lại
                        </a>
                        <div id="saveButtons">
                            <button type="submit" class="btn btn-primary" id="normalSave">
                                <i class="fas fa-save"></i>
                                Lưu sản phẩm
                            </button>
                            <button type="submit" class="btn btn-primary d-none" id="variantSave" name="redirect_to_variant" value="1">
                                <i class="fas fa-save"></i>
                                Lưu và tạo biến thể
                            </button>
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

                <div class="row">
                    <!-- Main Content -->
                    <div class="col-lg-8">
                        <!-- Basic Info -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header border-0 bg-white py-3">
                                <h5 class="card-title mb-0 text-primary">
                                    <i class="fas fa-info-circle me-2"></i>Thông tin cơ bản
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-4">
                                    <label class="form-label fw-medium">Loại sản phẩm <span class="text-danger">*</span></label>
                                    <select class="form-select @error('type') is-invalid @enderror" name="type" id="productType">
                                        <option value="simple" {{ old('type') === 'simple' ? 'selected' : '' }}>Sản phẩm đơn giản</option>
                                        <option value="digital" {{ old('type') === 'digital' ? 'selected' : '' }}>Sản phẩm số</option>
                                        <option value="variant" {{ old('type') === 'variant' ? 'selected' : '' }}>Sản phẩm biến thể</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Variant Button -->
                                <div id="variantSection" class="mb-4" style="display: none;">
                                    <a href="{{ route('admin.products.variants.create', ['product' => ':product_id']) }}" class="btn btn-outline-primary" id="createVariantBtn" disabled>
                                        <i class="fas fa-plus me-1"></i>
                                        Tạo biến thể sản phẩm
                                    </a>
                                    <small class="text-muted d-block mt-2">
                                        * Lưu sản phẩm trước khi tạo biến thể
                                    </small>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-medium">Tên sản phẩm <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                        name="name" value="{{ old('name') }}" placeholder="Nhập tên sản phẩm">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Danh mục <span class="text-danger">*</span></label>
                                        <select class="form-select select2 @error('categories') is-invalid @enderror" 
                                            name="categories[]" multiple>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ in_array($category->id, old('categories', [])) ? 'selected' : '' }}>
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
                                                <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                                    {{ $brand->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('brand_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-medium">Mô tả ngắn</label>
                                    <textarea class="form-control @error('short_description') is-invalid @enderror"
                                        name="short_description" rows="2"
                                        placeholder="Nhập mô tả ngắn">{{ old('short_description') }}</textarea>
                                    @error('short_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-medium">Mô tả chi tiết</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                        name="description" id="description"
                                        rows="5">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Media -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header border-0 bg-white py-3">
                                <h5 class="card-title mb-0 text-primary">
                                    <i class="fas fa-images me-2"></i>Hình ảnh
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-4">
                                    <label class="form-label fw-medium">Ảnh đại diện <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control @error('thumbnail') is-invalid @enderror" 
                                        name="thumbnail" accept="image/*">
                                    @error('thumbnail')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="col-lg-4">
                        <!-- Pricing -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header border-0 bg-white py-3">
                                <h5 class="card-title mb-0 text-primary">
                                    <i class="fas fa-tag me-2"></i>Giá & Khuyến mãi
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">SKU</label>
                                            <input type="text" class="form-control @error('sku') is-invalid @enderror"
                                                name="sku" value="{{ old('sku') }}">
                                            @error('sku')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Giá <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                                name="price" value="{{ old('price', 0) }}" min="0" step="1">
                                            @error('price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="is_sale" id="isSale"
                                            {{ old('is_sale') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="isSale">Đang giảm giá</label>
                                    </div>
                                </div>

                                <div class="mb-3 sale-price-field" style="{{ old('is_sale') ? '' : 'display: none;' }}">
                                    <label class="form-label">Giá khuyến mãi</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control @error('sale_price') is-invalid @enderror"
                                               name="sale_price" value="{{ old('sale_price') }}"
                                               min="0" step="1000">
                                        <span class="input-group-text">đ</span>
                                    </div>
                                    @error('sale_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Tồn kho</label>
                                    <input type="number" class="form-control @error('stock') is-invalid @enderror"
                                           name="stock" value="{{ old('stock', 0) }}"
                                           min="0">
                                    @error('stock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">
                                        Chỉ áp dụng cho sản phẩm đơn giản và sản phẩm số
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="card shadow-sm">
                            <div class="card-header border-0 bg-white py-3">
                                <h5 class="card-title mb-0 text-primary">
                                    <i class="fas fa-toggle-on me-2"></i>Trạng thái
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" name="is_active" id="isActive"
                                        {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="isActive">Kích hoạt</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/27.1.0/classic/ckeditor.js"></script>
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

    // Handle sale price field visibility
    $('#isSale').change(function() {
        $('.sale-price-field').toggle(this.checked);
    });

    // Handle variant section visibility
    $('#productType').change(function() {
        const isVariant = $(this).val() === 'variant';
        if (isVariant) {
            $('#normalSave').addClass('d-none');
            $('#variantSave').removeClass('d-none');
            $('input[name="stock"]').closest('.col-md-6').hide();
        } else {
            $('#normalSave').removeClass('d-none');
            $('#variantSave').addClass('d-none');
            $('input[name="stock"]').closest('.col-md-6').show();
        }
    });

    // Trigger change event on page load to set initial state
    $('#productType').trigger('change');
</script>
@endpush

@endsection
