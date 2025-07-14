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

                    <form action="{{ route('admin.products.variants.store', $product) }}" method="POST" enctype="multipart/form-data" id="create-variants-form">
                        @csrf

                        <!-- Attributes Selection -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Chọn thuộc tính</h6>
                            </div>
                            <div class="card-body">
                                @if($attributes->isEmpty())
                                    <div class="text-danger">Chưa có thuộc tính nào được định nghĩa.</div>
                                @else
                                    <div class="row">
                                        @foreach($attributes as $attribute)
                                            <div class="col-md-6 mb-3">
                                                <h6>{{ $attribute->name }}</h6>
                                                @foreach($attribute->values as $value)
                                                    <div class="form-check">
                                                        <input type="checkbox" 
                                                               class="form-check-input attribute-checkbox" 
                                                               value="{{ $value->id }}" 
                                                               data-value="{{ $value->value }}"
                                                               data-attribute-id="{{ $attribute->id }}"
                                                               data-attribute-name="{{ strtolower($attribute->name) }}"
                                                               id="attribute-{{ $attribute->id }}-{{ $value->id }}">
                                                        <label class="form-check-label" for="attribute-{{ $attribute->id }}-{{ $value->id }}">
                                                            {{ $value->value }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </div>
                                    <button type="button" class="btn btn-primary btn-sm mt-3" id="generateVariants">
                                        <i class="fas fa-plus me-1"></i> Tạo các cặp biến thể
                                    </button>
                                @endif
                            </div>
                        </div>

                        <!-- Variants Section -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Biến thể sản phẩm</h6>
                            </div>
                            <div class="card-body">
                                <div id="variantContainer">
                                    <!-- Các biến thể sẽ được thêm động bằng JavaScript -->
                                </div>
                                @error('variants')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
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
    console.log('DOMContentLoaded triggered'); // Debug

    const colorCodes = {
        'đỏ': 'DO', 'xanh': 'XA', 'trắng': 'TR', 'đen': 'DE', 'vàng': 'VA',
        'xanh lá': 'XL', 'xanh dương': 'XD', 'cam': 'CA', 'tím': 'TI',
        'nâu': 'NA', 'hồng': 'HO', 'xám': 'XA'
    };

    const vietnameseToEnglish = {
        'áo': 'shirt', 'quần': 'pants', 'giày': 'shoes', 'mũ': 'hat', 'túi': 'bag',
        'áo khoác': 'jacket', 'áo thun': 'tshirt', 'áo sơ mi': 'shirt', 'áo len': 'sweater',
        'áo vest': 'jacket', 'quần jean': 'jeans', 'quần dài': 'pants', 'quần short': 'shorts',
        'quần kaki': 'chinos', 'quần jogger': 'joggers', 'quần thể thao': 'sportpants',
        'quần đùi': 'shorts', 'giày thể thao': 'sneakers', 'giày da': 'leather',
        'giày cao gót': 'heels', 'mũ lưỡi trai': 'cap', 'mũ nón': 'hat',
        'mũ bảo hiểm': 'helmet', 'túi xách': 'bag', 'túi đeo': 'pouch',
        'túi du lịch': 'travelbag', 'túi đeo chéo': 'crossbag'
    };

    function getEnglishProductCode(productName) {
        let englishName = productName.toLowerCase();
        Object.entries(vietnameseToEnglish).forEach(([vietnamese, english]) => {
            englishName = englishName.replace(new RegExp(vietnamese, 'gi'), english);
        });
        const firstWord = englishName.split(' ')[0];
        return firstWord.substring(0, 2).toUpperCase();
    }

    const productName = '{{ $product->name }}';
    const productCode = getEnglishProductCode(productName);
    let variantCount = 0;

    // Hàm tạo tổ hợp từ các mảng giá trị
    function cartesianProduct(arrays) {
        return arrays.reduce((acc, curr) => {
            return acc.flatMap(x => curr.map(y => [...x, y]));
        }, [[]]);
    }

    function generateVariants() {
        console.log('Generating variants'); // Debug
        const attributeCheckboxes = document.querySelectorAll('.attribute-checkbox:checked');
        const variantContainer = document.getElementById('variantContainer');

        if (!variantContainer) {
            console.error('variantContainer not found');
            return;
        }

        if (attributeCheckboxes.length === 0) {
            alert('Vui lòng chọn ít nhất một giá trị thuộc tính.');
            return;
        }

        // Nhóm các giá trị theo thuộc tính
        const attributes = {};
        attributeCheckboxes.forEach(checkbox => {
            const attributeId = checkbox.dataset.attributeId;
            if (!attributes[attributeId]) {
                attributes[attributeId] = [];
            }
            attributes[attributeId].push({
                id: checkbox.value,
                value: checkbox.dataset.value,
                name: checkbox.dataset.attributeName
            });
        });

        // Tạo tổ hợp các giá trị thuộc tính
        const attributeValues = Object.values(attributes);
        if (attributeValues.length === 0) {
            return;
        }
        const combinations = cartesianProduct(attributeValues);

        // Xóa các biến thể hiện có
        variantContainer.innerHTML = '';
        variantCount = 0;

        // Tạo HTML cho mỗi tổ hợp
        combinations.forEach(combination => {
            variantCount++;
            let skuParts = [productCode];
            let variantLabel = [];

            // Tạo nhãn từ tổ hợp (không tự động tạo SKU nữa)
            combination.forEach(attr => {
                variantLabel.push(attr.value);
            });

            const label = variantLabel.join(' - ');
            // Để trống SKU để hệ thống tự tạo theo format SKU-TWCTE

            console.log(`Variant ${variantCount} generation:`, {
                productCode,
                label
            });

            const variantHtml = `
                <div class="variant-item mb-3 p-3 border rounded" data-index="${variantCount}">
                    <h6>Biến thể ${variantCount}: ${label}</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Mã SKU <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">SKU</span>
                                <input type="text" 
                                       class="form-control" 
                                       name="variants[${variantCount}][sku]" 
                                       id="sku-${variantCount}" 
                                       value="" 
                                       placeholder="Để trống để tự tạo">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Số lượng trong kho <span class="text-danger">*</span></label>
                            <input type="number" 
                                   class="form-control" 
                                   name="variants[${variantCount}][stock]" 
                                   value="0" 
                                   min="0">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Giá gốc <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">VNĐ</span>
                                <input type="number" 
                                       class="form-control" 
                                       name="variants[${variantCount}][regular_price]" 
                                       min="0" 
                                       step="0.01" >
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Giá khuyến mãi</label>
                            <div class="input-group">
                                <span class="input-group-text">VNĐ</span>
                                <input type="number" 
                                       class="form-control" 
                                       name="variants[${variantCount}][sale_price]" 
                                       min="0" 
                                       step="0.01">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Hình ảnh biến thể</label>
                        <input type="file" 
                               class="form-control" 
                               name="variants[${variantCount}][img]" 
                               accept="image/*">
                    </div>
                    ${combination.map(attr => `
                        <input type="hidden" 
                               name="variants[${variantCount}][attribute_values][]" 
                               value="${attr.id}">
                    `).join('')}
                    <button type="button" class="btn btn-danger btn-sm remove-variant">Xóa biến thể</button>
                </div>
            `;

            variantContainer.insertAdjacentHTML('beforeend', variantHtml);
            console.log(`Variant ${variantCount} added: ${label}`); // Debug
            console.log(`Attribute values for variant ${variantCount}:`, combination.map(attr => ({id: attr.id, value: attr.value}))); // Debug
        });
    }

    // Gắn sự kiện cho nút Tạo các cặp biến thể
    const generateVariantsBtn = document.getElementById('generateVariants');
    if (generateVariantsBtn) {
        generateVariantsBtn.addEventListener('click', function() {
            console.log('Generate variants button clicked'); // Debug
            generateVariants();
        });
    } else {
        console.error('generateVariants button not found');
    }

    // Gắn sự kiện xóa biến thể
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-variant')) {
            console.log('Remove variant button clicked'); // Debug
            e.target.closest('.variant-item').remove();
        }
    });

    // Form validation before submit
    document.getElementById('create-variants-form').addEventListener('submit', function(e) {
        const variantItems = document.querySelectorAll('.variant-item');
        
        if (variantItems.length === 0) {
            e.preventDefault();
            alert('Vui lòng tạo ít nhất một biến thể trước khi lưu.');
            return false;
        }

        // Check if all required fields are filled
        let isValid = true;
        variantItems.forEach((item, index) => {
            const stock = item.querySelector('input[name*="[stock]"]').value;
            const regularPrice = item.querySelector('input[name*="[regular_price]"]').value;
            const attributeValues = item.querySelectorAll('input[name*="[attribute_values][]"]');

            if (!stock || !regularPrice || attributeValues.length === 0) {
                isValid = false;
                console.log(`Variant ${index + 1} has missing required fields`);
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert('Vui lòng điền đầy đủ thông tin cho tất cả biến thể.');
            return false;
        }

        // Log form data before submit
        console.log('Form data before submit:', {
            variants: Array.from(variantItems).map((item, index) => {
                const sku = item.querySelector('input[name*="[sku]"]').value;
                const stock = item.querySelector('input[name*="[stock]"]').value;
                const regularPrice = item.querySelector('input[name*="[regular_price]"]').value;
                const salePrice = item.querySelector('input[name*="[sale_price]"]').value;
                const attributeValues = Array.from(item.querySelectorAll('input[name*="[attribute_values][]"]')).map(input => input.value);
                
                return {
                    sku,
                    stock,
                    regular_price: regularPrice,
                    sale_price: salePrice,
                    attribute_values: attributeValues
                };
            })
        });
    });
});
</script>
@endpush
@endsection