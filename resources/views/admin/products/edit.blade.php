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
                    <button type="submit" form="productForm" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Lưu sản phẩm
                    </button>
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

    <form id="productForm" action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
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
                                <option value="variant" {{ old('type', $product->type) === 'variant' ? 'selected' : '' }}>Sản phẩm có biến thể</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
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
                                <select class="form-select @error('category_id') is-invalid @enderror" name="category_id" required>
                                    <option value="">Chọn danh mục</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Thương hiệu</label>
                                <select class="form-select" name="brand_id">
                                    <option value="">Chọn thương hiệu</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
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

                <!-- Variants -->
                <div class="card border-0 shadow-sm mb-4" id="variantSection" style="display: {{ old('type', $product->type) === 'variant' ? 'block' : 'none' }};">
                    <div class="card-header border-0 bg-white py-3">
                        <h5 class="card-title mb-0 text-primary">
                            <i class="fas fa-cubes me-2"></i>Thuộc tính biến thể
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label class="form-label fw-medium">Chọn thuộc tính cho biến thể</label>
                            <div class="variant-attributes">
                                @foreach($attributes as $attribute)
                                    @php
                                        $selectedValues = [];
                                        $productAttribute = $product->attributes->where('id', $attribute->id)->first();
                                        if ($productAttribute) {
                                            $selectedValues = json_decode($productAttribute->pivot->values ?? '[]', true);
                                        }
                                    @endphp

                                    <div class="form-check mb-2">
                                        <input class="form-check-input variant-attribute" type="checkbox" 
                                            name="variant_attributes[]" value="{{ $attribute->id }}" 
                                            id="attribute{{ $attribute->id }}"
                                            data-attribute-type="{{ $attribute->type }}"
                                            {{ $productAttribute ? 'checked' : '' }}>
                                        <label class="form-check-label" for="attribute{{ $attribute->id }}">
                                            {{ $attribute->name }}
                                        </label>
                                    </div>
                                    
                                    @if($attribute->type === 'select')
                                        <div class="attribute-values ms-4 mb-3" id="attribute{{ $attribute->id }}_values" 
                                            style="display: {{ $productAttribute ? 'block' : 'none' }};">
                                            <select class="form-select attribute-value-select" 
                                                name="attribute_values[{{ $attribute->id }}][]" 
                                                multiple 
                                                data-placeholder="Chọn giá trị cho {{ $attribute->name }}">
                                                @foreach($attribute->values as $value)
                                                    <option value="{{ $value->id }}" 
                                                        {{ in_array($value->id, $selectedValues) ? 'selected' : '' }}>
                                                        {{ $value->value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @else
                                        <div class="attribute-values ms-4 mb-3" id="attribute{{ $attribute->id }}_values" 
                                            style="display: {{ $productAttribute ? 'block' : 'none' }};">
                                            <input type="text" class="form-control" 
                                                name="attribute_values[{{ $attribute->id }}]" 
                                                value="{{ implode(', ', $selectedValues) }}"
                                                placeholder="Nhập các giá trị cho {{ $attribute->name }}, phân cách bằng dấu phẩy">
                                        </div>
                                    @endif
                                @endforeach
                            </div>
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
                            <label class="form-label fw-medium">Giá gốc <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">$</span>
                                <input type="number" class="form-control border-start-0 @error('price') is-invalid @enderror" 
                                    name="price" value="{{ old('price', $product->price) }}" required step="0.01">
                            </div>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Giá khuyến mãi</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">$</span>
                                <input type="number" class="form-control border-start-0 @error('sale_price') is-invalid @enderror" 
                                    name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" step="0.01">
                            </div>
                            @error('sale_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Giá khuyến mãi phải nhỏ hơn giá gốc</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Số lượng tồn kho <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('stock') is-invalid @enderror" 
                                name="stock" value="{{ old('stock', $product->stock) }}" required>
                            @error('stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize CKEditor
        ClassicEditor
            .create(document.querySelector('#description'))
            .catch(error => {
                console.error(error);
            });

        // Initialize Select2 for attribute values
        $('.attribute-value-select').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: function() {
                return $(this).data('placeholder');
            }
        });

        // Handle product type selection
        const productType = document.getElementById('productType');
        const variantSection = document.getElementById('variantSection');

        if (productType && variantSection) {
            productType.addEventListener('change', function() {
                if (this.value === 'variant') {
                    variantSection.style.display = 'block';
                } else {
                    variantSection.style.display = 'none';
                    // Reset các checkbox và giá trị
                    document.querySelectorAll('.variant-attribute').forEach(checkbox => {
                        checkbox.checked = false;
                        const valuesContainer = document.getElementById(checkbox.id + '_values');
                        if (valuesContainer) {
                            valuesContainer.style.display = 'none';
                            // Reset Select2 hoặc input
                            const select = valuesContainer.querySelector('select');
                            if (select) {
                                $(select).val(null).trigger('change');
                            } else {
                                const input = valuesContainer.querySelector('input');
                                if (input) {
                                    input.value = '';
                                }
                            }
                        }
                    });
                }
            });
        }

        // Handle variant attribute selection
        document.querySelectorAll('.variant-attribute').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const valuesContainer = document.getElementById(this.id + '_values');
                if (valuesContainer) {
                    if (this.checked) {
                        valuesContainer.style.display = 'block';
                    } else {
                        valuesContainer.style.display = 'none';
                        // Reset giá trị khi bỏ chọn
                        const select = valuesContainer.querySelector('select');
                        if (select) {
                            $(select).val(null).trigger('change');
                        } else {
                            const input = valuesContainer.querySelector('input');
                            if (input) {
                                input.value = '';
                            }
                        }
                    }
                }
            });
        });

        // Form validation
        const form = document.getElementById('productForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                let isValid = true;
                const price = parseFloat(this.querySelector('[name="price"]').value) || 0;
                const salePrice = parseFloat(this.querySelector('[name="sale_price"]').value) || 0;

                if (salePrice > 0 && salePrice >= price) {
                    alert('Giá khuyến mãi phải nhỏ hơn giá gốc');
                    isValid = false;
                }

                // Validate variants if product type is variant
                if (productType.value === 'variant') {
                    const hasSelectedAttributes = document.querySelectorAll('.variant-attribute:checked').length > 0;
                    if (!hasSelectedAttributes) {
                        alert('Vui lòng chọn ít nhất một thuộc tính cho biến thể');
                        isValid = false;
                    }
                }

                if (!isValid) {
                    e.preventDefault();
                }
            });
        }
    });
</script>
@endpush

@endsection