@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm rounded">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-bold">Thêm ảnh sản phẩm mới</h5>
                        <a href="{{ route('admin.product-images.all') }}" class="btn btn-secondary btn-sm shadow-sm rounded">
                            <i class="mdi mdi-arrow-left me-1"></i> Quay lại danh sách
                        </a>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success shadow-sm rounded mb-3">{{ session('success') }}</div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger shadow-sm rounded mb-3">{{ session('error') }}</div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-danger shadow-sm rounded mb-3">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                       <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" 
                              action="{{ route('admin.product-images.store-general') }}" 
                              enctype="multipart/form-data"
                              id="createImageForm">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="product_id" class="form-label fw-bold">Chọn sản phẩm</label>
                                    <select class="form-select" id="product_id" name="product_id" required>
                                        <option value="">-- Chọn sản phẩm --</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                                {{ $product->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('product_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="product_variant_id" class="form-label fw-bold">Chọn biến thể (nếu có)</label>
                                    <select class="form-select" id="product_variant_id" name="product_variant_id">
                                        <option value="">-- Không chọn --</option>
                                        @foreach ($products as $product)
                                            @foreach ($product->variants as $variant)
                                                <option value="{{ $variant->id }}" data-product="{{ $product->id }}" 
                                                        {{ old('product_variant_id') == $variant->id ? 'selected' : '' }}>
                                                    {{ $product->name }} - SKU: {{ $variant->sku }}
                                                </option>
                                            @endforeach
                                        @endforeach
                                    </select>
                                    @error('product_variant_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="image" class="form-label fw-bold">Chọn ảnh <span class="text-danger">*</span></label>
                                    <input type="file" 
                                           name="image" 
                                           id="image" 
                                           class="form-control @error('image') is-invalid @enderror"
                                           required>
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div id="imagePreview" class="mt-2 d-none">
                                        <img src="" alt="Preview" class="img-thumbnail" style="max-height: 200px;">
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-plus-circle me-1"></i> Thêm mới
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Image Preview
        $('#image').change(function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview').removeClass('d-none').find('img').attr('src', e.target.result);
                }
                reader.readAsDataURL(file);
            } else {
                $('#imagePreview').addClass('d-none').find('img').attr('src', '');
            }
        });

        // Filter variants based on selected product
        document.addEventListener('DOMContentLoaded', function () {
            const productSelect = document.getElementById('product_id');
            const variantSelect = document.getElementById('product_variant_id');

            function filterVariants() {
                const selectedProductId = productSelect.value;
                Array.from(variantSelect.options).forEach(option => {
                    if (!option.value) {
                        option.hidden = false; // "-- Không chọn --"
                        return;
                    }
                    option.hidden = option.dataset.product !== selectedProductId;
                });
                variantSelect.value = ""; // Reset selected variant
            }

            productSelect.addEventListener('change', filterVariants);
            filterVariants(); // Call once on page load
        });
    });
</script>
@endpush
