@extends('admin.layouts.app')

@section('title', 'Thêm sản phẩm mới')

@section('content')
<div class="container-fluid px-4">
    <div class="card bg-light border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-2">Thêm sản phẩm mới</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Sản phẩm</a></li>
                            <li class="breadcrumb-item active">Thêm mới</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    <button type="submit" form="productForm" class="btn btn-primary">
                        <i class="fas fa-save"></i> Lưu sản phẩm
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="productForm" action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-8">
                <!-- Thông tin cơ bản -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0">Thông tin cơ bản</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Loại sản phẩm <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" name="type" id="productType" required>
                                <option value="simple">Sản phẩm đơn giản</option>
                                <option value="variant">Sản phẩm có biến thể</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Danh mục <span class="text-danger">*</span></label>
                                <select class="form-select @error('category_id') is-invalid @enderror" name="category_id" required>
                                    <option value="">Chọn danh mục</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Thương hiệu</label>
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

                        <div class="mb-3">
                            <label class="form-label">Mô tả ngắn</label>
                            <textarea class="form-control @error('short_description') is-invalid @enderror" 
                                name="short_description" rows="3">{{ old('short_description') }}</textarea>
                            @error('short_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mô tả chi tiết</label>
                            <textarea id="description" name="description" 
                                class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ảnh đại diện</label>
                                <input type="file" class="form-control @error('thumbnail') is-invalid @enderror" 
                                    name="thumbnail" accept="image/*">
                                @error('thumbnail')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Thư viện ảnh</label>
                                <input type="file" class="form-control @error('gallery') is-invalid @enderror" 
                                    name="gallery[]" multiple accept="image/*">
                                <small class="text-muted">Có thể chọn nhiều ảnh</small>
                                @error('gallery')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Biến thể sản phẩm -->
                <div id="variantSection" class="card shadow-sm mb-4" style="display: none;">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0">Thuộc tính biến thể</h5>
                    </div>
                    <div class="card-body">
                        @if(isset($attributes) && $attributes->count() > 0)
                            <div class="mb-3">
                                <label class="form-label">Chọn thuộc tính cho biến thể</label>
                                @foreach($attributes as $attribute)
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input variant-attribute" 
                                                name="variant_attributes[]" 
                                                value="{{ $attribute->id }}" 
                                                id="attribute{{ $attribute->id }}"
                                                {{ in_array($attribute->id, old('variant_attributes', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="attribute{{ $attribute->id }}">
                                                {{ $attribute->name }}
                                            </label>
                                        </div>

                                        <div class="mt-2 ps-4 variant-values" id="attribute{{ $attribute->id }}_values" 
                                            style="display: {{ in_array($attribute->id, old('variant_attributes', [])) ? 'block' : 'none' }};">
                                            @if($attribute->type === 'select')
                                                <select class="form-select attribute-value-select" 
                                                    name="attribute_values[{{ $attribute->id }}][]" 
                                                    multiple 
                                                    data-placeholder="Chọn giá trị cho {{ $attribute->name }}">
                                                    @foreach($attribute->values as $value)
                                                        <option value="{{ $value->id }}"
                                                            {{ in_array($value->id, old("attribute_values.{$attribute->id}", [])) ? 'selected' : '' }}>
                                                            {{ $value->value }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <input type="text" class="form-control" 
                                                    name="attribute_values[{{ $attribute->id }}]" 
                                                    value="{{ old("attribute_values.{$attribute->id}") }}"
                                                    placeholder="Nhập các giá trị, phân cách bằng dấu phẩy">
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                Chưa có thuộc tính nào. Vui lòng <a href="{{ route('admin.attributes.create') }}" class="alert-link">thêm thuộc tính</a> trước.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Trạng thái -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0">Trạng thái</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch">
                            <input type="checkbox" class="form-check-input @error('is_active') is-invalid @enderror" 
                                name="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label">Đang kinh doanh</label>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Giá & Kho -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0">Giá & Kho hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Mã sản phẩm (SKU)</label>
                            <input type="text" class="form-control @error('sku') is-invalid @enderror" 
                                name="sku" value="{{ old('sku') }}" placeholder="VD: SP001">
                            @error('sku')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Giá gốc <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₫</span>
                                <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                    name="price" value="{{ old('price') }}" required min="0" step="1000">
                            </div>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Giá khuyến mãi</label>
                            <div class="input-group">
                                <span class="input-group-text">₫</span>
                                <input type="number" class="form-control @error('sale_price') is-invalid @enderror" 
                                    name="sale_price" value="{{ old('sale_price') }}" min="0" step="1000">
                            </div>
                            @error('sale_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Để trống nếu không có khuyến mãi</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Số lượng <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('stock') is-invalid @enderror" 
                                name="stock" value="{{ old('stock', 0) }}" required min="0">
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
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    /* Editor styles */
    .ck-editor__editable {
        min-height: 200px;
        max-height: 400px;
    }
    .ck-editor__editable_inline {
        padding: 0 1rem !important;
    }
    .ck.ck-toolbar {
        border-radius: 0.375rem 0.375rem 0 0 !important;
        border-color: #dee2e6 !important;
    }
    .ck.ck-editor__main>.ck-editor__editable {
        border-radius: 0 0 0.375rem 0.375rem !important;
        border-color: #dee2e6 !important;
    }
    .ck.ck-editor__editable:not(.ck-editor__nested-editable).ck-focused {
        border-color: #435ebe !important;
        box-shadow: 0 0 0 0.2rem rgba(67, 94, 190, 0.15) !important;
    }

    /* Select2 styles cho biến thể */
    .variant-values .select2-container--bootstrap-5 .select2-selection {
        min-height: 38px;
        border-color: #dee2e6;
    }
    .variant-values .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered {
        padding: 0 0.375rem;
    }
    .variant-values .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
        background-color: #435ebe;
        color: #fff;
        border: none;
        padding: 2px 8px;
        margin: 2px;
    }
    .variant-values .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove {
        color: #fff;
        margin-right: 5px;
    }
    .variant-values .select2-container--bootstrap-5 .select2-dropdown {
        border-color: #dee2e6;
    }

    /* Fix select2 double arrow */
    .select2-container--bootstrap-5 .select2-selection--single {
        padding-right: 2.25rem !important;
        background-image: none !important;
    }
    
    /* Animation cho variant section */
    #variantSection {
        transition: all 0.3s ease-in-out;
    }
    .variant-values {
        transition: all 0.3s ease-in-out;
    }

    /* Form styles */
    .form-control:focus,
    .form-select:focus {
        border-color: #435ebe;
        box-shadow: 0 0 0 0.2rem rgba(67, 94, 190, 0.15);
    }
    .form-label {
        font-weight: 500;
        color: #566a7f;
    }
    .card {
        box-shadow: 0 2px 6px 0 rgba(67, 89, 113, 0.12);
    }
    .card-header {
        background-color: transparent;
        border-bottom: 1px solid #e9ecef;
    }
    .card-title {
        color: #566a7f;
        font-weight: 500;
    }
    
    /* Normal select styles */
    .form-select {
        background-color: #fff;
        border: 1px solid #dee2e6;
        padding: 0.375rem 2.25rem 0.375rem 0.75rem;
        background-position: right 0.75rem center;
    }
    .form-select:focus {
        border-color: #435ebe;
        box-shadow: 0 0 0 0.2rem rgba(67, 94, 190, 0.15);
    }

    /* Fix textarea duplication */
    .ck-editor + textarea {
        display: none;
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




