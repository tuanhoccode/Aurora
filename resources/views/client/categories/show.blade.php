@extends('client.layouts.default')

@section('content')
<style>
    .d-none-by-js { display: none !important; }
    .tp-product-item-2 { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
    .sidebar-filter h5 { font-size: 1.1rem; margin-bottom: 0.5rem; margin-top: 1.5rem; }
    .sidebar-filter .form-check { margin-bottom: 0.5rem; }
    .sidebar-filter { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); padding: 1.5rem 1rem; }
    
    .product-badge-outofstock {
      position: absolute;
      top: 10px; left: 10px;
      background: #d90429;
      color: #fff;
      padding: 4px 12px;
      border-radius: 4px;
      font-size: 0.95rem;
      font-weight: bold;
      z-index: 20;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .product-out-of-stock-overlay {
      position: absolute;
      top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(255,255,255,0.8);
      z-index: 10;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      color: #d90429;
      font-size: 1.1rem;
      text-align: center;
      border-radius: 8px;
      gap: 8px;
    }
    .tp-product-thumb-2.out-of-stock img {
      filter: grayscale(1) brightness(0.85);
      opacity: 0.7;
    }
    .tp-product-thumb-2.out-of-stock {
      pointer-events: none;
    }
    .tp-product-thumb-2.out-of-stock .tp-product-action-2 {
      display: none;
    }
    .tp-product-thumb-2 img {
      max-width: 100%;
      height: auto;
      display: block;
      margin: 0 auto;
    }
</style>
<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4 mt-2" style="font-weight:700;font-size:2.2rem;line-height:1.2;">Hiển thị danh mục: {{ $category->name }}</h2>
        </div>
    </div>
    <div class="row align-items-start">
        <!-- SIDEBAR FILTER -->
        <div class="col-xl-3 col-lg-3 mb-4">
            <div class="tp-shop-sidebar">
                <form method="GET" action="{{ route('shop') }}">
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
                                            <input class="form-check-input" type="checkbox" name="brand_ids[]" value="{{ $brand->id }}" id="brand_{{ $brand->id }}" {{ collect(request('brand_ids'))->contains($brand->id) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="brand_{{ $brand->id }}">{{ $brand->name }}</label>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
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
                                    @endphp
                                    @foreach($priceRanges as $i => $range)
                                    <li class="mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="price_ranges[]" value="{{ $range['min'] }}-{{ $range['max'] }}" id="price_range_{{ $i }}" {{ collect(request('price_ranges'))->contains($range['min'].'-'.$range['max']) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="price_range_{{ $i }}">{{ $range['label'] }}</label>
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
        <div class="col-xl-9 col-lg-9">
            <div class="row tp-shop-items-wrapper tp-shop-item-primary">
                @php $defaultShow = 9; @endphp
                @foreach ($products as $index => $product)
                    @php
                        $isOutOfStock = $product->type === 'variant'
                            ? ($product->variants->sum('stock') <= 0)
                            : (($product->stock ?? 0) <= 0);
                    @endphp
                    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 product-item{{ $index >= $defaultShow ? ' d-none-by-js' : '' }}">
                        <div class="tp-product-item-2 mb-40">
                            <div class="tp-product-thumb-2 p-relative z-index-1 fix w-img {{ $isOutOfStock ? 'out-of-stock' : '' }}">
                                <a href="{{ $product->slug ? route('client.product.show', ['slug' => $product->slug]) : '#' }}">
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                                </a>
                                @if($isOutOfStock)
                                    <div class="product-badge-outofstock">Hết hàng</div>
                                    <div class="product-out-of-stock-overlay">
                                        <i class="fa-solid fa-exclamation-triangle"></i>
                                        <span>Hết hàng</span>
                                    </div>
                                @endif
                            </div>
                            <div class="tp-product-content-2 pt-15">
                                <div class="tp-product-tag-2">
                                    <a href="#">{{ $product->brand->name ?? 'Không có thương hiệu' }}</a>
                                </div>
                                <h3 class="tp-product-title-2">
                                    <a href="{{ $product->slug ? route('client.product.show', ['slug' => $product->slug]) : '#' }}">{{ $product->name }}</a>
                                </h3>
                                <div class="tp-product-rating-icon tp-product-rating-icon-2">
                                    @for ($i = 0; $i < 5; $i++)
                                        <span><i class="fa-solid fa-star"></i></span>
                                    @endfor
                                </div>
                                <div class="tp-product-price-wrapper-2">
                                    <span class="tp-product-price-2 new-price">${{ number_format($product->price, 2) }}</span>
                                    @if ($product->original_price && $product->original_price > $product->price)
                                        <span class="tp-product-price-2 old-price">${{ number_format($product->original_price, 2) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="text-center my-4">
                <div id="showing-info" class="mb-2">
                    Đang hiển thị <span id="showing-count">{{ min($defaultShow, $products->count()) }}</span> trong số {{ $products->count() }} sản phẩm
                </div>
                @if($products->count() > $defaultShow)
                    <button id="show-all-btn" class="btn btn-dark">
                        <b>Xem thêm</b> <i class="fa fa-arrow-up-right-from-square"></i>
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const defaultShow = {{ $defaultShow }};
    const total = {{ $products->count() }};
    const items = document.querySelectorAll('.product-item');
    const showAllBtn = document.getElementById('show-all-btn');
    const showingCount = document.getElementById('showing-count');
    let currentVisible = defaultShow;

    if (showAllBtn) {
        showAllBtn.addEventListener('click', function() {
            let nextVisible = currentVisible + defaultShow;
            for (let i = currentVisible; i < nextVisible && i < total; i++) {
                items[i].classList.remove('d-none-by-js');
            }
            currentVisible += defaultShow;
            showingCount.textContent = Math.min(currentVisible, total);
            if (currentVisible >= total) {
                showAllBtn.style.display = 'none';
            }
        });
    }
</script>
@endsection 