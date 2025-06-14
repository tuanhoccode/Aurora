@extends('admin.layouts.app')

@section('title', 'Tạo biến thể sản phẩm')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tạo biến thể cho sản phẩm: {{ $product->name }}</h3>
                </div>
                <div class="card-body">
                    <!-- Hiển thị thông báo lỗi và thành công -->
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.products.variants.store', $product) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Attributes Section -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Thuộc tính sản phẩm</h6>
                            </div>
                            <div class="card-body">
                                <!-- SKU Section -->
                                <div class="mb-4">
                                    <label class="form-label fw-medium">Mã SKU <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">SKU</span>
                                        <input type="text" 
                                               class="form-control @error('sku') is-invalid @enderror" 
                                               name="sku" 
                                               id="sku"
                                               value="{{ old('sku') }}"
                                               readonly>
                                        <button type="button" class="btn btn-outline-secondary" id="generate-sku">
                                            <i class="fas fa-sync"></i> Tạo mới
                                        </button>
                                    </div>
                                    @error('sku')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                @foreach($attributes as $attribute)
                                    @if(strtolower($attribute->name) === 'size' || 
                                        strtolower($attribute->name) === 'color' || 
                                        strtolower($attribute->name) === 'màu sắc' || 
                                        strtolower($attribute->name) === 'kích thước')
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
                                                                   {{ in_array($value->id, old('attribute_values', [])) ? 'checked' : '' }}>
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

                        <!-- Image Section -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Hình ảnh</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-4">
                                    <label class="form-label fw-medium">Hình ảnh biến thể</label>
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
                                                   value="{{ old('regular_price') }}"
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
                                                   value="{{ old('sale_price') }}"
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
                            <input type="number" name="stock" id="stock" class="form-control @error('stock') is-invalid @enderror" value="{{ old('stock', 0) }}" min="0">
                            @error('stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="card-footer bg-white">
                            <button type="submit" class="btn btn-primary">Tạo biến thể</button>
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
document.addEventListener('DOMContentLoaded', function() {
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

    // Danh sách các từ tiếng Việt và tiếng Anh tương ứng
    const vietnameseToEnglish = {
        'áo': 'shirt',
        'quần': 'pants',
        'giày': 'shoes',
        'mũ': 'hat',
        'túi': 'bag',
        'áo': 'shirt',
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
        'giày thể thao': 'sneakers',
        'mũ lưỡi trai': 'cap',
        'mũ nón': 'hat',
        'mũ bảo hiểm': 'helmet',
        'túi xách': 'bag',
        'túi đeo': 'pouch',
        'túi du lịch': 'travelbag',
        'túi đeo chéo': 'crossbag'
    };

    // Chuyển đổi tên sản phẩm từ tiếng Việt sang tiếng Anh và lấy 2 chữ cái đầu
    function getEnglishProductCode(productName) {
        // Chuyển đổi các từ tiếng Việt sang tiếng Anh
        let englishName = productName.toLowerCase();
        Object.entries(vietnameseToEnglish).forEach(([vietnamese, english]) => {
            englishName = englishName.replace(new RegExp(vietnamese, 'gi'), english);
        });
        
        // Lấy 2 chữ cái đầu của từ đầu tiên
        const firstWord = englishName.split(' ')[0];
        return firstWord.substring(0, 2).toUpperCase();
    }

    // Lấy 2 chữ cái đầu của tên sản phẩm đã chuyển đổi
    const productName = '{{ $product->name }}';
    const productCode = getEnglishProductCode(productName);
    
    // Cập nhật SKU khi chọn thuộc tính
    function updateSKU() {
        // Tìm size và color đã chọn
        let size = null;
        let color = null;
        
        document.querySelectorAll('.attribute-value:checked').forEach(checkbox => {
            const type = checkbox.dataset.type;
            const value = checkbox.dataset.value;
            
            if (type === 'size' || type === 'kích thước') {
                size = value.toUpperCase();
            } else if (type === 'color' || type === 'màu sắc') {
                color = colorCodes[value.toLowerCase()] || value.toUpperCase().substring(0, 2);
            }
        });

        // Nếu đã chọn cả size và color
        if (size && color) {
            const sku = `${productCode}-${size}-${color}`;
            document.getElementById('sku').value = sku;
        } else {
            document.getElementById('sku').value = '';
        }
    }

    // Xử lý khi chọn thuộc tính
    document.querySelectorAll('.attribute-value').forEach(checkbox => {
        checkbox.addEventListener('change', updateSKU);
    });

    // Xử lý nút tạo mới SKU
    document.getElementById('generate-sku').addEventListener('click', () => {
        const sku = document.getElementById('sku');
        const currentSku = sku.value;
        
        if (currentSku) {
            const parts = currentSku.split('-');
            const counter = parseInt(parts[parts.length - 1]) || 0;
            const newSku = `${parts[0]}-${parts[1]}-${parts[2]}-${counter + 1}`;
            sku.value = newSku;
        }
    });

    // Gọi hàm cập nhật lần đầu
    updateSKU();
});
</script>
@endpush
@endsection 