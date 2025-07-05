@extends('client.layouts.default')

@section('content')
<section class="breadcrumb__area include-bg pt-100 pb-50">
    <div class="container">
        <div class="row">
            <div class="col-xxl-12">
                <div class="breadcrumb__content p-relative z-index-1">
                    <h3 class="breadcrumb__title">Sản phẩm thuộc danh mục: {{ $category->name }}</h3>
                    <div class="breadcrumb__list">
                        <span><a href="/">Trang chủ</a></span>
                        <span>{{ $category->name }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container">
    <div class="row">
        <!-- SIDEBAR FILTER -->
        <div class="col-xl-3 col-lg-4 mb-4">
            <div class="tp-shop-sidebar">
                <form method="GET">
                    <input type="hidden" id="min_price" name="min_price" value="{{ request('min_price') }}">
                    <input type="hidden" id="max_price" name="max_price" value="{{ request('max_price') }}">
                    <!-- Status Filter -->
                    <div class="tp-shop-widget mb-35">
                        <h3 class="tp-shop-widget-title">Trạng thái</h3>
                        <div class="tp-shop-widget-content">
                            <div class="tp-shop-widget-checkbox">
                                <ul class="filter-items filter-checkbox">
                                    <li class="d-flex justify-content-between mb-10">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="is_sale" value="1" id="is_sale" {{ request('is_sale') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_sale">Đang giảm giá</label>
                                        </div>
                                    </li>
                                    <li class="d-flex justify-content-between mb-10">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="in_stock" value="1" id="in_stock" {{ request('in_stock') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="in_stock">Còn hàng</label>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Brand Filter -->
                    <div class="tp-shop-widget mb-35">
                        <h3 class="tp-shop-widget-title">Thương hiệu</h3>
                        <div class="tp-shop-widget-content">
                            <div class="tp-shop-widget-checkbox">
                                <ul class="filter-items filter-checkbox">
                                    @foreach($brands as $brand)
                                    <li class="d-flex justify-content-between mb-10">
<div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                name="brand_ids[]" 
                                                value="{{ $brand->id }}" 
                                                id="brand_{{ $brand->id }}"
                                                {{ in_array($brand->id, (array)request('brand_ids', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="brand_{{ $brand->id }}">
                                                {{ $brand->name }}
                                            </label>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Price Filter (Fixed Ranges) -->
                   <!-- Price Filter (Multi-checkbox) -->
<div class="tp-shop-widget mb-35">
    <h3 class="tp-shop-widget-title">Lọc theo giá</h3>
    <div class="tp-shop-widget-content">
        <div class="tp-shop-widget-checkbox">
            <ul class="filter-items filter-checkbox">
                @php
                    $priceRanges = [
                        ['min' => 0, 'max' => 200000, 'label' => '0 đ - 200.000 đ'],
                        ['min' => 200000, 'max' => 500000, 'label' => '200.000 đ - 500.000 đ'],
                        ['min' => 500000, 'max' => 800000, 'label' => '500.000 đ - 800.000 đ'],
                        ['min' => 800000, 'max' => 1000000, 'label' => '800.000 đ - 1.000.000 đ'],
                    ];

                    $selectedRanges = request()->input('price_ranges', []);
                @endphp

                @foreach($priceRanges as $index => $range)
                    @php
                        $value = $range['min'] . '-' . $range['max'];
                    @endphp
                    <li class="mb-2">
                        <div class="form-check">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   name="price_ranges[]" 
                                   value="{{ $value }}" 
                                   id="price_range_{{ $index }}"
                                   {{ in_array($value, $selectedRanges) ? 'checked' : '' }}>
                            <label class="form-check-label" for="price_range_{{ $index }}">
                                {{ $range['label'] }}
                            </label>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>


                    <div class="tp-shop-widget-btn">
                        <button type="submit" class="tp-btn w-100">Lọc sản phẩm</button>
</div>
                </form>
            </div>
        </div>

        <!-- PRODUCT LIST -->
        <div class="col-xl-9 col-lg-8">
            <div class="tp-shop-items-wrapper tp-shop-item-primary">
                <div class="row">
                    @forelse($products as $product)
                        <div class="col-xl-4 col-md-6 col-sm-6 mb-4">
                            <div class="tp-product-item-2 mb-40">
                                <div class="tp-product-thumb-2 p-relative z-index-1 fix w-img">
                                    <a href="{{ route('client.product.show', ['slug' => $product->slug]) }}">
                                        <img src="{{ $product->image_url ?? asset('assets2/img/product/product-1.jpg') }}" alt="{{ $product->name }}">
                                    </a>
                                    <div class="tp-product-action-2 tp-product-action-blackStyle">
                                        <div class="tp-product-action-item-2 d-flex flex-column">
                                            <form method="POST" action="{{ route('shopping-cart.add') }}" class="add-to-cart-form">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                <input type="hidden" name="product_variant_id" value="{{ $product->default_variant_id ?? '' }}">
                                                <input type="hidden" name="quantity" value="1">
                                                <input type="hidden" name="price" value="{{ $product->price }}">
                                                <button type="submit" class="tp-product-action-btn-2 tp-product-add-cart-btn">
                                                    <i class="fa fa-shopping-cart"></i>
                                                    <span class="tp-product-tooltip tp-product-tooltip-right">Thêm vào giỏ</span>
                                                </button>
                                            </form>
                                            <a href="{{ route('client.product.show', ['slug' => $product->slug]) }}" class="tp-product-action-btn-2 tp-product-quick-view-btn">
                                                <i class="fa fa-eye"></i>
                                                <span class="tp-product-tooltip tp-product-tooltip-right">Xem chi tiết</span>
                                            </a>
                                            <button type="button" class="tp-product-action-btn-2 tp-product-add-to-wishlist-btn">
                                                <i class="fa fa-heart"></i>
                                                <span class="tp-product-tooltip tp-product-tooltip-right">Yêu thích</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="tp-product-content-2 pt-15">
                                    <div class="tp-product-tag-2">
                                        <a href="#">{{ $category->name }}</a>
                                    </div>
                                    <h3 class="tp-product-title-2">
                                        <a href="#">{{ $product->name }}</a>
                                    </h3>
                                    <div class="tp-product-price-wrapper-2">
                                        @if($product->is_on_sale)
                                            <span class="tp-product-price-2 new-price">{{ number_format($product->sale_price, 0, ',', '.') }} đ</span>
                                            <span class="tp-product-price-2 old-price">{{ number_format($product->price, 0, ',', '.') }} đ</span>
                                        @else
                                            <span class="tp-product-price-2 new-price">{{ number_format($product->price, 0, ',', '.') }} đ</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-warning">Không có sản phẩm nào phù hợp.</div>
                        </div>
                    @endforelse
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {{ $products->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<style>
/* XÓA TOÀN BỘ CSS LIÊN QUAN ĐẾN SLIDER LỌC GIÁ CŨ */
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
// Khi chọn radio, tự động set min_price và max_price rồi submit form
$(document).ready(function() {
    $('input[name="price_range"]').on('change', function() {
        var val = $(this).val().split('-');
        $('#min_price').val(val[0]);
        $('#max_price').val(val[1]);
        $(this).closest('form').submit();
    });
});
</script>
@endpush