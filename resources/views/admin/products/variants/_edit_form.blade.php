<form action="{{ route('admin.products.variants.update', [$product->id, $variant->id]) }}" method="POST" enctype="multipart/form-data" id="modalEditVariantForm">
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
                                        id="modal-attr-{{ $attribute->id }}-{{ $value->id }}"
                                        {{ in_array($value->id, $selectedValues) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="modal-attr-{{ $attribute->id }}-{{ $value->id }}">
                                        {{ $value->value }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="row g-2">
                <div class="col-md-3">
                    <label class="form-label">SKU</label>
                    <input type="text" name="sku" id="modalSkuInput" class="form-control" value="{{ old('sku', $variant->sku) }}" placeholder="Để trống để tự tạo">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tồn kho</label>
                    <input type="number" name="stock" class="form-control" min="0" value="{{ old('stock', $variant->stock) }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Giá gốc</label>
                    <input type="number" name="regular_price" class="form-control" min="0" value="{{ old('regular_price', $variant->regular_price) }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Giá KM</label>
                    <input type="number" name="sale_price" class="form-control" min="0" value="{{ old('sale_price', $variant->sale_price) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Ảnh</label>
                    <input type="file" name="img" class="form-control">
                    @if($variant->img)
                        <img src="{{ asset('storage/' . $variant->img) }}" class="img-thumbnail mt-2" style="max-width: 100px;">
                    @endif
                </div>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-success mt-3">Cập nhật biến thể</button>
</form>
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
        document.getElementById('modalSkuInput').value = sku;
    }
    document.querySelectorAll('.attribute-radio').forEach(cb => {
        cb.addEventListener('change', updateSKU);
    });
    // Gọi lần đầu để đồng bộ khi load trang
    updateSKU();
});
</script> 