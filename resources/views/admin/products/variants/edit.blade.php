@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa biến thể sản phẩm')

@section('content')
<div class="container">
    <h3 class="mb-4">Chỉnh sửa biến thể cho sản phẩm: <span class="text-primary">{{ $product->name }}</span></h3>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('admin.products.variants.update', [$product->id, $variant->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card mb-3 variant-card">
            <div class="card-header">
                <strong>Biến thể hiện tại:</strong>
                @foreach($variant->attributeValues as $attributeValue)
                    <span class="badge bg-light text-dark me-2">
                        {{ $attributeValue->attribute->name }}: {{ $attributeValue->value }}
                    </span>
                @endforeach
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Chọn thuộc tính cho biến thể:</strong>
                    <div class="row">
                        @foreach($attributes as $attribute)
                            <div class="col-md-4 mb-2">
                                <div class="fw-bold mb-1">{{ $attribute->name }}</div>
                                @foreach($attribute->values as $value)
                                    <div class="form-check">
                                        <input type="radio" class="form-check-input attribute-radio" 
                                            name="attribute_values[{{ $attribute->id }}]" 
                                            value="{{ $value->id }}" 
                                            data-attribute-name="{{ strtolower($attribute->name) }}"
                                            data-value="{{ $value->value }}"
                                            id="attr-{{ $attribute->id }}-{{ $value->id }}"
                                            {{ in_array($value->id, $selectedValues) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="attr-{{ $attribute->id }}-{{ $value->id }}">
                                            {{ $value->value }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="fw-bold" for="img">Ảnh sản phẩm</label>
                            <div>
                                @if(!empty($variant->img))
                                    <img src="{{ asset('storage/' . $variant->img) }}" alt="Ảnh sản phẩm" class="img-thumbnail mb-2" style="max-width: 150px;">
                                @endif
                                <input type="file" class="form-control" id="img" name="img">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="fw-bold" for="sku">SKU</label>
                            <input type="text" class="form-control" id="sku" name="sku" value="{{ old('sku', $variant->sku ?? '') }}">
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold" for="stock">Tồn kho</label>
                            <input type="number" class="form-control" id="stock" name="stock" value="{{ old('stock', $variant->stock ?? '') }}">
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold" for="regular_price">Giá gốc</label>
                            <input type="number" class="form-control" id="regular_price" name="regular_price"
                                value="{{ old('regular_price', ($variant->regular_price ?? '')) }}" min="0" step="1">
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold" for="sale_price">Giá khuyến mãi</label>
                            <input type="number" class="form-control" id="sale_price" name="sale_price"
                                value="{{ old('sale_price', ($variant->sale_price ?? '')) }}" min="0" step="1">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-success mt-3">Cập nhật biến thể</button>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Bảng mã màu sắc
    const colorMap = {
        'Đỏ': 'DO',
        'Vàng': 'VANG',
        'Đen': 'DEN',
        'Trắng': 'TRANG',
        'Xám': 'XAM',
        'Xanh': 'XA',
        // ... bổ sung nếu có thêm màu
    };
    function removeVietnameseTones(str) {
        return str.normalize('NFD').replace(/\p{Diacritic}/gu, '').replace(/đ/g, 'd').replace(/Đ/g, 'D');
    }
    function getProductCode(name) {
        let en = name.normalize('NFD').replace(/\p{Diacritic}/gu, '').replace(/[^a-zA-Z ]/g, '').toLowerCase();
        let words = en.split(' ').filter(Boolean);
        return words.length > 0 ? words[0].substring(0,2).toUpperCase() : 'PR';
    }
    const productCode = getProductCode(@json($product->name));

    function updateSKU() {
        let sku = productCode;
        document.querySelectorAll('.attribute-radio:checked').forEach(cb => {
            let attr = cb.getAttribute('data-attribute-name');
            let value = cb.getAttribute('data-value');
            if (attr.includes('màu') || attr.includes('color')) {
                let colorCode = colorMap[value] || removeVietnameseTones(value).substring(0,2).toUpperCase();
                sku += '-' + colorCode;
            } else if (attr.includes('size') || attr.includes('kích')) {
                sku += '-' + removeVietnameseTones(value).toUpperCase();
            }
        });
        document.getElementById('skuInput').value = sku;
    }
    document.querySelectorAll('.attribute-radio').forEach(cb => {
        cb.addEventListener('change', updateSKU);
    });
    // Gọi lần đầu để đồng bộ khi load trang
});
</script>
@endpush
@endsection