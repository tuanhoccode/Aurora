@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa biến thể sản phẩm')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Chỉnh sửa biến thể cho sản phẩm: {{ $product->name }}</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.products.variants.update', [$product, $variant]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <!-- Attributes Section -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Thuộc tính sản phẩm</h6>
                            </div>
                            <div class="card-body">
                                @foreach($attributes as $attribute)
                                    @if(strtolower($attribute->name) === 'size' || strtolower($attribute->name) === 'color' || strtolower($attribute->name) === 'màu sắc' || strtolower($attribute->name) === 'kích thước')
                                        <div class="mb-4">
                                            <label class="form-label fw-medium">{{ $attribute->name }}</label>
                                            <div class="row g-2">
                                                @foreach($attribute->values as $value)
                                                    <div class="col-auto">
                                                        <div class="form-check">
                                                            <input type="checkbox" 
                                                                   class="form-check-input attribute-value" 
                                                                   name="attribute_values[]" 
                                                                   value="{{ $value->id }}" 
                                                                   data-value="{{ $value->value }}"
                                                                   data-type="{{ strtolower($attribute->name) }}"
                                                                   id="value-{{ $value->id }}"
                                                                   {{ in_array($value->id, old('attribute_values', $selectedValues)) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="value-{{ $value->id }}">
                                                                {{ $value->value }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            @error('attribute_values')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        {{-- SKU SECTION - giống form tạo --}}
                        <div class="mb-4">
                            <label class="form-label fw-medium">Mã SKU <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">SKU</span>
                                <input type="text" class="form-control @error('sku') is-invalid @enderror" name="sku" id="sku" value="{{ old('sku', $variant->sku) }}" readonly>
                                <button type="button" class="btn btn-outline-secondary" id="generate-sku">
                                    <i class="fas fa-sync"></i> Tạo mới
                                </button>
                            </div>
                            @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Image Section -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Hình ảnh</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-4">
                                    <label class="form-label fw-medium">Hình ảnh biến thể</label>
                                    <div class="mb-3">
                                        @if($variant->img)
                                            <img src="{{ asset($variant->img) }}" alt="Hình ảnh biến thể" class="img-thumbnail mb-2" style="max-width: 200px;">
                                        @endif
                                    </div>
                                    <input type="file" 
                                           class="form-control @error('img') is-invalid @enderror" 
                                           name="img" 
                                           accept="image/*">
                                    @error('img')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Price Section -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Giá</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label fw-medium">Giá gốc <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">VNĐ</span>
                                            <input type="number" 
                                                   class="form-control @error('regular_price') is-invalid @enderror" 
                                                   name="regular_price" 
                                                   value="{{ old('regular_price', $variant->regular_price) }}"
                                                   min="0" 
                                                   step="0.01" 
                                                   required>
                                            @error('regular_price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label fw-medium">Giá khuyến mãi</label>
                                        <div class="input-group">
                                            <span class="input-group-text">VNĐ</span>
                                            <input type="number" 
                                                   class="form-control @error('sale_price') is-invalid @enderror" 
                                                   name="sale_price" 
                                                   value="{{ old('sale_price', $variant->sale_price) }}"
                                                   min="0" 
                                                   step="0.01">
                                            @error('sale_price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label for="stock" class="form-label fw-medium">Số lượng trong kho</label>
                            <input type="number" name="stock" id="stock" class="form-control @error('stock') is-invalid @enderror" value="{{ old('stock', $variant->stock) }}" min="0">
                            @error('stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="card-footer bg-white">
                            <button type="submit" class="btn btn-primary">Cập nhật biến thể</button>
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-secondary">Quay lại</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const colorCodes = {
        'đỏ': 'DO',
        'xanh': 'XA',
        'trắng': 'TR',
        'đen': 'DE',
        'vàng': 'VA',
        'xanh lá': 'XL',
        'xanh dương': 'XD',
        'cam': 'CA',
        'tím': 'TI',
        'nâu': 'NA',
        'hồng': 'HO',
        'xám': 'XA'
    };

    const vietnameseToEnglish = {
        'áo': 'shirt',
        'quần': 'pants',
        'giày': 'shoes',
        'mũ': 'hat',
        'túi': 'bag',
        'áo khoác': 'jacket',
        'áo thun': 'tshirt',
        'áo sơ mi': 'shirt',
        'áo len': 'sweater',
        'áo vest': 'jacket',
        'quần jean': 'jeans',
        'quần dài': 'pants',
        'quần short': 'shorts',
        'quần kaki': 'chinos',
        'quần jogger': 'joggers',
        'quần thể thao': 'sportpants',
        'quần đùi': 'shorts',
        'giày thể thao': 'sneakers',
        'giày da': 'leather',
        'giày cao gót': 'heels',
        'mũ lưỡi trai': 'cap',
        'mũ nón': 'hat',
        'mũ bảo hiểm': 'helmet',
        'túi xách': 'bag',
        'túi đeo': 'pouch',
        'túi du lịch': 'travelbag',
        'túi đeo chéo': 'crossbag'
    };

    function getEnglishProductCode(productName) {
        let english = productName.toLowerCase();
        Object.entries(vietnameseToEnglish).forEach(([vi, en]) => {
            english = english.replace(new RegExp(vi, 'gi'), en);
        });
        return english.split(' ')[0].substring(0, 2).toUpperCase();
    }

    const productCode = getEnglishProductCode('{{ $product->name }}');
    const skuInput    = document.getElementById('sku');

    function updateSKU() {
        let size = null;
        let color = null;
        document.querySelectorAll('.attribute-value:checked').forEach(cb => {
            const value = cb.dataset.value;
            const type  = cb.dataset.type;
            if (type === 'size' || type === 'kích thước') {
                size = value.toUpperCase();
            } else if (type === 'color' || type === 'màu sắc') {
                color = colorCodes[value.toLowerCase()] || value.toUpperCase().substring(0, 2);
            }
        });
        skuInput.value = (size && color) ? `${productCode}-${size}-${color}` : '';
    }

    document.querySelectorAll('.attribute-value').forEach(cb => cb.addEventListener('change', updateSKU));

    document.getElementById('generate-sku').addEventListener('click', () => {
        if (!skuInput.value) return;
        const parts = skuInput.value.split('-');
        const last  = parts[parts.length - 1];
        const num   = parseInt(last);
        if (isNaN(num)) {
            skuInput.value = skuInput.value + '-1';
        } else {
            parts[parts.length - 1] = num + 1;
            skuInput.value = parts.join('-');
        }
    });

    updateSKU();
});
</script>
@endpush
@endsection 