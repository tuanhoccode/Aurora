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


    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
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
                            <select class="form-select @error('type') is-invalid @enderror" name="type" id="productType" >
                                <option value="simple" {{ old('type', $product->type) === 'simple' ? 'selected' : '' }}>Sản phẩm đơn giản</option>
                                <option value="digital" {{ old('type', $product->type) === 'digital' ? 'selected' : '' }}>Sản phẩm số</option>
                                <option value="variant" {{ old('type', $product->type) === 'variant' ? 'selected' : '' }}>Sản phẩm biến thể</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>


                        <!-- Variant Section -->
                        <div id="variantSection" class="mb-4" style="display: none;">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Biến thể sản phẩm</h6>
                                <a href="{{ route('admin.products.variants.create', $product) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-plus me-1"></i>
                                    Thêm biến thể
                                </a>
                            </div>
                           
                            @if($product->variants->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>SKU</th>
                                                <th>Giá</th>
                                                <th>Giá KM</th>
                                                <th>Kho</th>
                                                <th>Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($product->variants as $variant)
                                                <tr>
                                                    <td>{{ $variant->sku }}</td>
                                                    <td>{{ number_format($variant->regular_price) }}đ</td>
                                                    <td>{{ $variant->sale_price ? number_format($variant->sale_price) . 'đ' : '-' }}</td>
                                                    <td>{{ $variant->stock }}</td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="{{ route('admin.products.variants.edit', ['product' => $product->id, 'variant' => $variant->id]) }}"
                                                               class="btn btn-outline-primary">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <button type="button" class="btn btn-outline-danger"
                                                                    onclick="deleteVariant({{ $variant->id }}, '{{ $variant->sku }}')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-light border text-center">
                                    Chưa có biến thể nào được tạo
                                </div>
                            @endif
                        </div>


                        <div class="mb-4">
                            <label class="form-label fw-medium">Tên sản phẩm <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror"
                                name="name" value="{{ old('name', $product->name) }}"  placeholder="Nhập tên sản phẩm">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Danh mục <span class="text-danger">*</span></label>
                                <select class="form-select select2 @error('categories') is-invalid @enderror"
                                    name="categories[]" multiple>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ in_array($category->id, old('categories', $product->categories->pluck('id')->toArray())) ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('categories')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Thương hiệu</label>
                                <select class="form-select @error('brand_id') is-invalid @enderror" name="brand_id">
                                    <option value="">Chọn thương hiệu</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('brand_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>


                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Trạng thái</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                        {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label">Đang hoạt động</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Trạng thái khuyến mãi</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_sale"
                                        {{ old('is_sale', $product->is_sale) ? 'checked' : '' }}>
                                    <label class="form-check-label">Đang khuyến mãi</label>
                                </div>
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
                                        <label class="form-label mb-0 fw-medium">Ảnh đại diện</label>
                                        <div class="mb-3">
                                            @if($product->thumbnail)
                                                <img src="{{ Storage::url($product->thumbnail) }}" 
                                                     class="img-fluid rounded mb-3" 
                                                     style="max-height: 200px; object-fit: cover;">
                                            @endif
                                            <input type="file" class="form-control @error('thumbnail') is-invalid @enderror" 
                                                name="thumbnail" accept="image/*">
                                            @error('thumbnail')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="border rounded p-3 text-center position-relative bg-light">
                                        <label class="form-label mb-0 fw-medium">Hình ảnh phụ</label>
                                        <div class="mb-3">
                                            <div class="d-flex flex-wrap gap-2">
                                                @if($product->gallery)
                                                    @foreach(json_decode($product->gallery) as $image)
                                                        <div class="position-relative">
                                                            <img src="{{ Storage::url($image) }}" 
                                                                 class="img-thumbnail" 
                                                                 style="max-width: 100px; max-height: 100px; object-fit: cover;">
                                                            <button type="button" 
                                                                    class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 delete-gallery-image" 
                                                                    data-path="{{ $image }}">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                            <input type="file" class="form-control image-upload" 
                                                name="gallery_images[]" accept="image/*" multiple>
                                            <div class="preview-images d-flex flex-wrap gap-2 mt-2"></div>
                                        </div>
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
                            <input class="form-check-input" type="checkbox" name="is_sale" value="1"
                                {{ old('is_sale', $product->is_sale) ? 'checked' : '' }}>
                            <label class="form-check-label fw-medium">Đang khuyến mãi</label>
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
                                    value="{{ old('sku', $product->sku) }}" readonly>
                            </div>
                            <small class="text-muted">SKU không thể thay đổi</small>
                        </div>


                        <div class="mb-3">
                            <label class="form-label fw-medium">Giá bán <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">VNĐ</span>
                                <input type="number" class="form-control border-start-0 @error('price') is-invalid @enderror"
                                    name="price" value="{{ old('price', $product->price) }}"  min="0" step="1000">
                            </div>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="mb-3">
                            <label class="form-label">Giá khuyến mãi</label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('sale_price') is-invalid @enderror"
                                       name="sale_price" value="{{ old('sale_price', $product->sale_price) }}"
                                       min="0" step="1000">
                                <span class="input-group-text">đ</span>
                            </div>
                            @error('sale_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        <!-- Stock field - only show for non-variant products -->
                        <div class="mb-3" id="stockField">
                            <label class="form-label fw-medium">Số lượng trong kho</label>
                            <input type="number" class="form-control @error('stock') is-invalid @enderror"
                                   name="stock" value="{{ old('stock', $product->stock) }}" min="0">
                            @error('stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Lưu sản phẩm
            </button>
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
    .preview-images img {
        max-width: 100px;
        max-height: 100px;
        object-fit: cover;
    }
    .remove-image {
        position: relative;
        top: -20px;
        left: -5px;
    }
</style>
@endpush


@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#description'))
        .catch(error => {
            console.error(error);
        });


    // Initialize Select2 for multiple category selection
    $('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Chọn danh mục'
    });


    // Handle variant section visibility
    $('#productType').change(function() {
        const isVariant = $(this).val() === 'variant';
        $('#variantSection').toggle(isVariant);
       
        // Hide stock input if product type is variant
        if (isVariant) {
            $('#stockField').hide();
        } else {
            $('#stockField').show();
        }
    });


    // Trigger change event on page load to set initial state
    $('#productType').trigger('change');


    // Function to delete variant safely
    function deleteVariant(variantId, variantSku) {
        if (confirm(`Bạn có chắc muốn xóa biến thể "${variantSku}"?`)) {
            // Create a temporary form for deletion
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/products/{{ $product->id }}/variants/${variantId}`;
            form.style.display = 'none';
           
            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.appendChild(csrfToken);
           
            // Add method override
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            form.appendChild(methodField);
           
            // Submit the form
            document.body.appendChild(form);
            form.submit();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Preview gallery images
        const imageUpload = document.querySelector('.image-upload');
        const previewContainer = document.querySelector('.preview-images');

        imageUpload?.addEventListener('change', function(e) {
            previewContainer.innerHTML = '';
            const files = e.target.files;
            
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'img-thumbnail';
                        
                        const removeBtn = document.createElement('button');
                        removeBtn.className = 'btn btn-danger btn-sm remove-image';
                        removeBtn.innerHTML = '<i class="fas fa-times"></i>';
                        removeBtn.onclick = function() {
                            previewContainer.removeChild(img);
                            previewContainer.removeChild(removeBtn);
                        };
                        
                        previewContainer.appendChild(img);
                        previewContainer.appendChild(removeBtn);
                    };
                    reader.readAsDataURL(file);
                }
            }
        });

        // Delete gallery image
        document.querySelectorAll('.delete-gallery-image').forEach(button => {
            button.addEventListener('click', function() {
                const path = this.dataset.path;
                if (confirm('Bạn có chắc chắn muốn xóa hình ảnh này không?')) {
                    // Gửi request để xóa hình ảnh
                    fetch(`{{ route('admin.products.delete-gallery-image', $product->id) }}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ path: path })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Xóa hình ảnh khỏi giao diện
                            this.closest('.position-relative').remove();
                        } else {
                            alert('Có lỗi xảy ra khi xóa hình ảnh');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Có lỗi xảy ra khi xóa hình ảnh');
                    });
                }
            });
        });
    });
</script>
@endpush


@endsection
