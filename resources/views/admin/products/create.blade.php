@extends('admin.layouts.app')

@section('title', 'Thêm sản phẩm mới')

@section('content')
<div class="container-fluid">
    <form id="productForm" action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-4">
            <div class="col-lg-8">
                <!-- Thông tin cơ bản -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0">Thông tin cơ bản</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Loại sản phẩm <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" name="type" id="productType" required>
                                <option value="simple" {{ old('type', 'simple') === 'simple' ? 'selected' : '' }}>Sản phẩm đơn giản</option>
                                <option value="variant" {{ old('type') === 'variant' ? 'selected' : '' }}>Sản phẩm có biến thể</option>
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

                        <div class="mb-3">
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

                        <div class="mb-3">
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
                            <textarea id="description" class="form-control @error('description') is-invalid @enderror" 
                                name="description">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Giá và tồn kho cho sản phẩm đơn giản -->
                <div id="simpleProductSection">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title mb-0">Giá & Tồn kho</h5>
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
                                            name="price" value="{{ old('price', 0) }}" min="0" step="1" required>
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Giá khuyến mãi</label>
                                        <input type="number" class="form-control @error('sale_price') is-invalid @enderror" 
                                            name="sale_price" value="{{ old('sale_price') }}" min="0">
                                        @error('sale_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Tồn kho <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('stock') is-invalid @enderror" 
                                            name="stock" value="{{ old('stock', 0) }}" min="0" required>
                                        @error('stock')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Biến thể sản phẩm -->
                <div id="variantSection" style="display: none;">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Biến thể sản phẩm</h5>
                            <button type="button" class="btn btn-primary btn-sm" id="generateVariantsBtn">
                                <i class="fas fa-sync-alt me-1"></i>Tạo biến thể
                            </button>
                        </div>
                        <div class="card-body">
                            <!-- Phần chọn thuộc tính -->
                            <div class="mb-4">
                                <label class="form-label fw-medium">Chọn thuộc tính cho biến thể</label>
                                <div class="attribute-list">
                                    @foreach($attributes as $attribute)
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input type="checkbox" 
                                                    class="form-check-input variant-attribute" 
                                                    name="variant_attributes[]" 
                                                    value="{{ $attribute->id }}" 
                                                    id="attribute{{ $attribute->id }}"
                                                    data-attribute-name="{{ $attribute->name }}"
                                                    {{ in_array($attribute->id, old('variant_attributes', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="attribute{{ $attribute->id }}">
                                                    {{ $attribute->name }}
                                                </label>
                                            </div>
                                            
                                            <div class="mt-2 ps-4 variant-values" id="attribute{{ $attribute->id }}_values" 
                                                style="{{ in_array($attribute->id, old('variant_attributes', [])) ? '' : 'display: none;' }}">
                                                @if($attribute->type === 'select')
                                                    <select class="form-select attribute-value-select" 
                                                        name="attribute_values[{{ $attribute->id }}][]" 
                                                        multiple 
                                                        data-attribute-id="{{ $attribute->id }}"
                                                        data-placeholder="Chọn giá trị cho {{ $attribute->name }}">
                                                        @foreach($attribute->values as $value)
                                                            <option value="{{ $value->id }}" 
                                                                {{ in_array($value->id, old("attribute_values.{$attribute->id}", [])) ? 'selected' : '' }}>
                                                                {{ $value->value }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <input type="text" 
                                                        class="form-control" 
                                                        name="attribute_values[{{ $attribute->id }}]" 
                                                        data-attribute-id="{{ $attribute->id }}"
                                                        value="{{ old("attribute_values.{$attribute->id}") }}"
                                                        placeholder="Nhập các giá trị, phân cách bằng dấu phẩy">
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Bảng biến thể -->
                            <div class="variants-table-wrapper" style="display: none;">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 40px;">
                                                    <input type="checkbox" class="form-check-input" id="checkAllVariants">
                                                </th>
                                                <th>Biến thể</th>
                                                <th style="width: 150px;">SKU</th>
                                                <th style="width: 150px;">Giá</th>
                                                <th style="width: 150px;">Giá KM</th>
                                                <th style="width: 120px;">Tồn kho</th>
                                                <th style="width: 100px;">Trạng thái</th>
                                            </tr>
                                        </thead>
                                        <tbody id="variantsTableBody">
                                            <!-- Các hàng biến thể sẽ được thêm vào đây bằng JavaScript -->
                                        </tbody>
                                    </table>
                                </div>

                                <div class="bulk-actions mt-3" style="display: none;">
                                    <div class="row g-2">
                                        <div class="col-auto">
                                            <select class="form-select" id="bulkAction">
                                                <option value="">Hành động</option>
                                                <option value="delete">Xóa biến thể</option>
                                                <option value="price">Cập nhật giá</option>
                                                <option value="stock">Cập nhật tồn kho</option>
                                            </select>
                                        </div>
                                        <div class="col-auto">
                                            <button type="button" class="btn btn-secondary" id="applyBulkAction">Áp dụng</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hình ảnh -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0">Hình ảnh</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Ảnh đại diện</label>
                            <input type="file" class="form-control @error('thumbnail') is-invalid @enderror" 
                                name="thumbnail" accept="image/*">
                            @error('thumbnail')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Thư viện ảnh</label>
                            <input type="file" class="form-control @error('gallery.*') is-invalid @enderror" 
                                name="gallery[]" accept="image/*" multiple>
                            @error('gallery.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Trạng thái -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0">Trạng thái</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" checked>
                            <label class="form-check-label">Đang kinh doanh</label>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-1"></i>Lưu sản phẩm
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
.variants-table-wrapper {
    margin-top: 20px;
}
.variant-row.disabled {
    opacity: 0.6;
    pointer-events: none;
}
.bulk-edit-input {
    width: 120px !important;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/27.1.0/classic/ckeditor.js"></script>

<script>
    $(document).ready(function() {
        // Initialize CKEditor
        let editor = null;
        if (!editor) {
            ClassicEditor
                .create(document.querySelector('#description'))
                .then(newEditor => {
                    editor = newEditor;
                })
                .catch(error => {
                    console.error(error);
                });
        }

        // Initialize Select2 for attribute values
        $('.attribute-value-select').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: function() {
                return $(this).data('placeholder');
            }
        });

        // Show/hide sections based on product type
        const toggleSections = () => {
            const type = $('#productType').val();
            if (type === 'simple') {
                $('#simpleProductSection').show();
                $('#variantSection').hide();
            } else {
                $('#simpleProductSection').hide();
                $('#variantSection').show();
            }
        };

        // Initial toggle
        toggleSections();

        // Toggle on change
        $('#productType').change(toggleSections);

        // Handle attribute checkbox change
        $('.variant-attribute').change(function() {
            const valuesDiv = $(`#${this.id}_values`);
            if (this.checked) {
                valuesDiv.show();
            } else {
                valuesDiv.hide();
                // Clear values
                const select = valuesDiv.find('select');
                if (select.length) {
                    select.val(null).trigger('change');
                } else {
                    valuesDiv.find('input').val('');
                }
            }
        });

        // Generate variants
        $('#generateVariantsBtn').click(function() {
            const selectedAttributes = [];
            
            // Collect selected attributes and their values
            $('.variant-attribute:checked').each(function() {
                const attributeId = $(this).val();
                const attributeName = $(this).data('attribute-name');
                const valuesContainer = $(`#attribute${attributeId}_values`);
                let values = [];

                if (valuesContainer.find('select').length) {
                    // For select type
                    values = valuesContainer.find('select').select2('data').map(item => ({
                        id: item.id,
                        value: item.text
                    }));
                } else {
                    // For text type
                    const textValues = valuesContainer.find('input').val().split(',').map(v => v.trim()).filter(v => v);
                    values = textValues.map(value => ({
                        id: value,
                        value: value
                    }));
                }

                if (values.length) {
                    selectedAttributes.push({
                        id: attributeId,
                        name: attributeName,
                        values: values
                    });
                }
            });

            if (!selectedAttributes.length) {
                alert('Vui lòng chọn ít nhất một thuộc tính và giá trị cho biến thể.');
                return;
            }

            // Generate combinations
            const combinations = generateCombinations(selectedAttributes);
            
            // Generate variants table
            const variantsTable = generateVariantsTable(combinations);
            
            // Show variants table
            $('.variants-table-wrapper').show();
            $('.variants-table-wrapper .table-responsive').html(variantsTable);

            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();

            // Handle bulk actions
            initBulkActions();
        });

        // Generate all possible combinations of attribute values
        function generateCombinations(attributes) {
            if (attributes.length === 0) return [];
            if (attributes.length === 1) {
                return attributes[0].values.map(value => ({
                    [attributes[0].name]: value
                }));
            }

            const [first, ...rest] = attributes;
            const combinations = generateCombinations(rest);
            const result = [];

            first.values.forEach(value => {
                if (combinations.length === 0) {
                    result.push({ [first.name]: value });
                } else {
                    combinations.forEach(combination => {
                        result.push({
                            [first.name]: value,
                            ...combination
                        });
                    });
                }
            });

            return result;
        }

        // Generate variants table HTML
        function generateVariantsTable(combinations) {
            let html = `
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40px;">
                                <input type="checkbox" class="form-check-input" id="checkAllVariants">
                            </th>
                            ${Object.keys(combinations[0]).map(attr => `<th>${attr}</th>`).join('')}
                            <th>SKU</th>
                            <th>Giá</th>
                            <th>Giá KM</th>
                            <th>Tồn kho</th>
                            <th style="width: 40px;">Hiện</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            combinations.forEach((combination, index) => {
                const variantName = Object.values(combination).map(v => v.value).join('-');
                html += `
                    <tr>
                        <td>
                            <input type="checkbox" class="form-check-input variant-checkbox">
                        </td>
                        ${Object.entries(combination).map(([attr, value]) => `
                            <td>
                                ${value.value}
                                <input type="hidden" name="variants[${index}][attributes][${value.id}]" value="${value.id}">
                            </td>
                        `).join('')}
                        <td>
                            <input type="text" class="form-control form-control-sm" 
                                name="variants[${index}][sku]" 
                                value="PRD-${variantName.toUpperCase()}"
                                required>
                        </td>
                        <td>
                            <input type="number" class="form-control form-control-sm" 
                                name="variants[${index}][price]" 
                                value="0" min="0" required>
                        </td>
                        <td>
                            <input type="number" class="form-control form-control-sm" 
                                name="variants[${index}][sale_price]" 
                                value="" min="0">
                        </td>
                        <td>
                            <input type="number" class="form-control form-control-sm" 
                                name="variants[${index}][stock]" 
                                value="0" min="0" required>
                        </td>
                        <td>
                            <div class="form-check d-flex justify-content-center">
                                <input type="checkbox" class="form-check-input" 
                                    name="variants[${index}][is_active]" 
                                    value="1" checked>
                            </div>
                        </td>
                    </tr>
                `;
            });

            html += `
                    </tbody>
                </table>
                <div class="bulk-actions mt-3" style="display: none;">
                    <div class="row g-2">
                        <div class="col-auto">
                            <select class="form-select form-select-sm bulk-action-select">
                                <option value="">Chọn thao tác</option>
                                <option value="delete">Xóa đã chọn</option>
                                <option value="price">Cập nhật giá</option>
                                <option value="stock">Cập nhật tồn kho</option>
                            </select>
                        </div>
                        <div class="col-auto bulk-action-value" style="display: none;">
                            <input type="number" class="form-control form-control-sm" min="0">
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-sm btn-secondary apply-bulk-action">
                                Áp dụng
                            </button>
                        </div>
                    </div>
                </div>
            `;

            return html;
        }

        // Initialize bulk actions
        function initBulkActions() {
            // Check all variants
            $('#checkAllVariants').change(function() {
                $('.variant-checkbox').prop('checked', this.checked);
                toggleBulkActions();
            });

            // Individual checkbox change
            $(document).on('change', '.variant-checkbox', function() {
                toggleBulkActions();
            });

            // Show/hide bulk actions
            function toggleBulkActions() {
                const checkedCount = $('.variant-checkbox:checked').length;
                $('.bulk-actions')[checkedCount ? 'show' : 'hide']();
            }

            // Show/hide value input based on action
            $('.bulk-action-select').change(function() {
                const action = $(this).val();
                $('.bulk-action-value')[action === 'price' || action === 'stock' ? 'show' : 'hide']();
            });

            // Apply bulk action
            $('.apply-bulk-action').click(function() {
                const action = $('.bulk-action-select').val();
                const value = $('.bulk-action-value input').val();

                if (!action) {
                    alert('Vui lòng chọn thao tác');
                    return;
                }

                $('.variant-checkbox:checked').each(function() {
                    const row = $(this).closest('tr');
                    switch (action) {
                        case 'delete':
                            row.remove();
                            break;
                        case 'price':
                            if (!value) {
                                alert('Vui lòng nhập giá');
                                return;
                            }
                            row.find('input[name$="[price]"]').val(value);
                            break;
                        case 'stock':
                            if (!value) {
                                alert('Vui lòng nhập số lượng tồn kho');
                                return;
                            }
                            row.find('input[name$="[stock]"]').val(value);
                            break;
                    }
                });

                // Reset bulk actions
                $('.bulk-action-select').val('');
                $('.bulk-action-value').hide().find('input').val('');
                $('#checkAllVariants').prop('checked', false);
                toggleBulkActions();
            });
        }

        // Form validation
        $('#productForm').submit(function(e) {
            const type = $('#productType').val();
            
            if (type === 'variant') {
                // Check if any attribute is selected
                if (!$('.variant-attribute:checked').length) {
                    e.preventDefault();
                    alert('Vui lòng chọn ít nhất một thuộc tính cho biến thể.');
                    return;
                }

                // Check if variants table exists
                if (!$('.variants-table-wrapper table tbody tr').length) {
                    e.preventDefault();
                    alert('Vui lòng tạo ít nhất một biến thể cho sản phẩm.');
                    return;
                }
            }
        });
    });
</script>
@endpush




