// ...existing code...

<tbody>
  @foreach($product->variants as $variant)
    {{-- ...render biến thể cũ như hiện tại... --}}
  @endforeach

  {{-- Thêm đoạn này để render lại biến thể mới từ old('variants') nếu có --}}
  @if(old('variants') && count(old('variants')) > 0)
    @foreach(old('variants') as $i => $variant)
      <tr>
        <td>
          @if(isset($variant['attributes']))
            @foreach($variant['attributes'] as $attrId => $valueId)
              <input type="hidden" name="variants[{{ $i }}][attributes][{{ $attrId }}]" value="{{ $valueId }}">
              @php
                $attr = $attributes->firstWhere('id', $attrId);
                $value = $attr ? $attr->values->firstWhere('id', $valueId) : null;
              @endphp
              @if($attr && $value)
                <span class="badge bg-secondary me-1">{{ $attr->name }}: {{ $value->value }}</span>
              @endif
            @endforeach
          @endif
        </td>
        <td>
          <input type="text" class="form-control" name="variants[{{ $i }}][sku]" value="{{ $variant['sku'] ?? '' }}" readonly>
        </td>
        <td>
          <input type="number" class="form-control variant-price" name="variants[{{ $i }}][price]" value="{{ $variant['price'] ?? '' }}" min="0" placeholder="Giá gốc">
        </td>
        <td>
          <input type="number" class="form-control variant-sale-price" name="variants[{{ $i }}][sale_price]" value="{{ $variant['sale_price'] ?? '' }}" min="0" placeholder="Giá khuyến mãi">
          <small class="text-muted discount-percentage" style="display:none;"></small>
        </td>
        <td>
          <input type="number" class="form-control" name="variants[{{ $i }}][stock]" value="{{ $variant['stock'] ?? '' }}" min="0" max="100" placeholder="Tồn kho (tối đa 100)">
        </td>
        <td>
          <input type="file" class="form-control mb-2" name="variants[{{ $i }}][image]" accept="image/*">
          <div class="variant-gallery-upload">
            <label class="form-label small text-muted mb-1 d-block">Thư viện ảnh</label>
            <input type="file" class="form-control variant-gallery-input" 
                   data-variant-index="{{ $i }}" 
                   name="variants[{{ $i }}][gallery][]" 
                   multiple 
                   accept="image/*">
            <div class="variant-gallery-preview mt-2 d-flex flex-wrap gap-2" id="variant-gallery-{{ $i }}">
              <!-- Ảnh sẽ được hiển thị ở đây -->
            </div>
          </div>
        </td>
        <td class="text-center">
          <button type="button" class="btn btn-sm btn-danger remove-variant-row"><i class="fas fa-trash"></i></button>
        </td>
      </tr>
    @endforeach
  @endif
</tbody>

// ...existing code...@extends('admin.layouts.app')

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
                    <span class="badge bg-secondary me-2">
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

@push('styles')
<style>
/* Custom style for variant attribute badges */
.badge.bg-secondary {
  background-color: #e4e4e4 !important;
  color: #333 !important;
}
</style>
@endpush
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
        document.getElementById('sku').value = sku;
    }
    // Khôi phục dữ liệu từ session khi có lỗi validate
    function restoreAttributeSelections() {
        @if(old('attribute_values'))
            let oldAttributeValues = @json(old('attribute_values'));
            if (oldAttributeValues) {
                Object.keys(oldAttributeValues).forEach(function(attrId) {
                    let valueId = oldAttributeValues[attrId];
                    let radio = document.querySelector(`input[name="attribute_values[${attrId}]"][value="${valueId}"]`);
                    if (radio) {
                        radio.checked = true;
                    }
                });
                // Cập nhật SKU sau khi khôi phục
                updateSKU();
            }
        @endif
    }

    // Gọi hàm khôi phục khi trang load
    restoreAttributeSelections();

    document.querySelectorAll('.attribute-radio').forEach(cb => {
        cb.addEventListener('change', updateSKU);
    });
    // Gọi lần đầu để đồng bộ khi load trang
    updateSKU();
});
</script>
@endpush
@endsection