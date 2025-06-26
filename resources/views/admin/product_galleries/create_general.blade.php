@extends('admin.layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4 fw-bold">Thêm ảnh phụ sản phẩm</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

<form action="{{ route('admin.product-images.store-general') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Chọn sản phẩm --}}
        <div class="mb-3">
            <label for="product_id" class="form-label">Chọn sản phẩm</label>
            <select class="form-select" id="product_id" name="product_id" required>
                <option value="">-- Chọn sản phẩm --</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Chọn biến thể nếu có --}}
        <div class="mb-3">
            <label for="product_variant_id" class="form-label">Chọn biến thể (nếu có)</label>
            <select class="form-select" id="product_variant_id" name="product_variant_id">
                <option value="">-- Không chọn --</option>
                @foreach ($products as $product)
                    @foreach ($product->variants as $variant)
                        <option value="{{ $variant->id }}" data-product="{{ $product->id }}">
                            {{ $product->name }} - SKU: {{ $variant->sku }}
                        </option>
                    @endforeach
                @endforeach
            </select>
        </div>

        {{-- Chọn ảnh --}}
        <div class="mb-3">
            <label for="image" class="form-label">Chọn ảnh</label>
            <input type="file" class="form-control" id="image" name="image" required>
        </div>

        <button type="submit" class="btn btn-primary">Thêm ảnh</button>
    </form>
</div>

{{-- Script lọc biến thể theo sản phẩm --}}
<script>
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
            variantSelect.value = ""; // Reset chọn biến thể
        }

        productSelect.addEventListener('change', filterVariants);
        filterVariants(); // Gọi 1 lần đầu tiên
    });
</script>
@endsection
