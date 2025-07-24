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
                            <!-- Ảnh mặc định -->
                            <div class="mb-3">
                                <label class="fw-bold">Ảnh mặc định</label>
                                @if(!empty($variant->img))
                                    <div class="position-relative mb-2">
                                        <img src="{{ asset('storage/' . $variant->img) }}" alt="Ảnh mặc định" class="img-thumbnail" style="max-width: 150px; max-height: 150px; object-fit: cover;">
                                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0" onclick="deleteDefaultImage()">x</button>
                                    </div>
                                @endif
                                <input type="file" class="form-control @error('img') is-invalid @enderror" name="img" id="defaultImageInput" accept="image/*">
                                @error('img')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <!-- Thư viện ảnh biến thể -->
                            <div id="gallery-upload-wrapper" style="display: block;">
                                <label class="fw-bold" for="images">Thư viện ảnh biến thể (có thể chọn nhiều)</label>
                                <input type="file" class="form-control @error('images') is-invalid @enderror" name="images[]" id="imageInput" accept="image/*" multiple>
                                @error('images')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <!-- Xem trước ảnh mới upload -->
                            <div id="imagePreview" class="mt-2 d-flex flex-wrap gap-2"></div>
                            <!-- Hiển thị danh sách ảnh hiện có -->
                            @if($images->isNotEmpty())
                                <div class="mt-2">
                                    <strong>Ảnh hiện có:</strong>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($images as $image)
                                            <div class="position-relative" id="image-{{ $image->id }}">
                                                <img src="{{ asset('storage/' . $image->url) }}" class="img-thumbnail" style="max-width: 150px; max-height: 150px; object-fit: cover;">
                                                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0" onclick="deleteImage({{ $image->id }})">x</button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
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
        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-secondary mt-3">Quay lại</a>
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
        document.getElementById('sku').value = sku;
    }

    document.querySelectorAll('.attribute-radio').forEach(cb => {
        cb.addEventListener('change', updateSKU);
    });

    // Gọi lần đầu để đồng bộ khi load trang
    updateSKU();

    // Xử lý xem trước ảnh mặc định
    const defaultImageInput = document.getElementById('defaultImageInput');
    defaultImageInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const imgContainer = document.createElement('div');
                imgContainer.className = 'position-relative mb-2';
                imgContainer.innerHTML = `
                    <img src="${e.target.result}" class="img-thumbnail" style="max-width: 150px; max-height: 150px; object-fit: cover;">
                    <button type="button" class="btn btn-warning btn-sm position-absolute top-0 end-0" onclick="this.parentElement.remove(); document.getElementById('defaultImageInput').value=''">x</button>
                `;
                defaultImageInput.parentElement.insertBefore(imgContainer, defaultImageInput);
            };
            reader.readAsDataURL(file);
        }
    });

    // Xử lý xem trước ảnh trong thư viện
    const imageInput = document.getElementById('imageInput');
    const imagePreview = document.getElementById('imagePreview');

    imageInput.addEventListener('change', function() {
        imagePreview.innerHTML = ''; // Xóa preview cũ
        const files = this.files;

        for (let file of files) {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imgContainer = document.createElement('div');
                    imgContainer.className = 'position-relative';
                    imgContainer.innerHTML = `
                        <img src="${e.target.result}" class="img-thumbnail" style="max-width: 150px; max-height: 150px; object-fit: cover;">
                        <button type="button" class="btn btn-warning btn-sm position-absolute top-0 end-0" onclick="this.parentElement.remove()">x</button>
                    `;
                    imagePreview.appendChild(imgContainer);
                };
                reader.readAsDataURL(file);
            }
        }
    });

    // Xử lý xóa ảnh mặc định
    function deleteDefaultImage() {
        if (confirm('Bạn có chắc chắn muốn xóa ảnh mặc định này?')) {
            fetch('{{ route("admin.products.variants.delete-default-image", ["product" => $product->id, "variant" => $variant->id]) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ variant_id: {{ $variant->id }} })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Xóa ảnh mặc định thành công!');
                    document.querySelector('.mb-2.position-relative').remove();
                } else {
                    alert('Xóa ảnh mặc định thất bại!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Đã có lỗi xảy ra khi xóa ảnh mặc định!');
            });
        }
    }

    // Xử lý xóa ảnh trong thư viện
    function deleteImage(imageId) {
        if (confirm('Bạn có chắc chắn muốn xóa ảnh này?')) {
            fetch('{{ route("admin.products.variants.delete-image", ["product" => $product->id]) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ image_id: imageId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Xóa ảnh thành công!');
                    document.getElementById(`image-${imageId}`).remove();
                } else {
                    alert('Xóa ảnh thất bại!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Đã có lỗi xảy ra khi xóa ảnh!');
            });
        }
    }
});
</script>
@endpush
@endsection