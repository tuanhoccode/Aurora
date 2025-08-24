@extends('client.layouts.default')




@section('content')
<style>
    .d-none-by-js { display: none !important; }
   
    /* Cải thiện layout sản phẩm */
    .tp-product-item-2 {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        height: 100%;
        display: flex;
        flex-direction: column;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
   
    .tp-product-item-2:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    }
   
    /* Cố định kích thước ảnh sản phẩm */
    .tp-product-thumb-2 {
        position: relative;
        width: 100%;
        height: 280px;
        overflow: hidden;
        border-radius: 8px 8px 0 0;
    }
   
    .tp-product-thumb-2 img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
   
    .tp-product-item-2:hover .tp-product-thumb-2 img {
        transform: scale(1.05);
    }
   
    /* Cố định kích thước content */
    .tp-product-content-2 {
        padding: 15px;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
   
    /* Giới hạn độ dài tên sản phẩm */
    .tp-product-title-2 {
        margin: 8px 0;
        line-height: 1.4;
        height: 2.8em; /* 2 dòng */
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        text-overflow: ellipsis;
    }
   
    .tp-product-title-2 a {
        color: #333;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.95rem;
    }
   
    .tp-product-title-2 a:hover {
        color: #007bff;
    }
   
    /* Cố định kích thước brand tag */
    .tp-product-tag-2 {
        margin-bottom: 8px;
        height: 20px;
        overflow: hidden;
    }
   
    .tp-product-tag-2 a {
        font-size: 0.8rem;
        color: #666;
        text-decoration: none;
        background: #f8f9fa;
        padding: 2px 8px;
        border-radius: 4px;
        display: inline-block;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
   
    /* Cố định kích thước rating */
    .tp-product-rating-icon {
        margin: 8px 0;
        height: 24px;
        display: flex;
        align-items: center;
    }
   
    .tp-product-rating-icon p {
        margin: 0;
        font-size: 0.85rem;
        color: #999;
    }
   
    .review-item {
        padding-bottom: 0 !important;
    }
   
    .review-item .text-warning {
        font-size: 0.9rem;
    }
   
    /* Cố định kích thước price */
    .tp-product-price-wrapper-2 {
        margin-top: auto;
        padding-top: 8px;
        border-top: 1px solid #f0f0f0;
    }
   
    .tp-product-price-2.new-price {
        font-size: 1.1rem;
        font-weight: bold;
        color: #333;
    }
   
    .tp-product-price-2.old-price {
        font-size: 0.9rem;
        color: #999;
        text-decoration: line-through;
        margin-left: 8px;
    }
   
    /* Cải thiện sidebar */
    .sidebar-filter h5 {
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
        margin-top: 1.5rem;
    }
   
    .sidebar-filter .form-check {
        margin-bottom: 0.5rem;
    }
   
    .sidebar-filter {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        padding: 1.5rem 1rem;
        position: sticky;
        top: 20px;
    }
   
    /* Badge hết hàng */
    .product-badge-outofstock {
        position: absolute;
        top: 10px;
        left: 10px;
        background: #d90429;
        color: #fff;
        padding: 4px 12px;
        border-radius: 4px;
        font-size: 0.85rem;
        font-weight: bold;
        z-index: 20;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
   
    .product-out-of-stock-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255,255,255,0.8);
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: #d90429;
        font-size: 1rem;
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
   
    /* Responsive */
    @media (max-width: 768px) {
        .tp-product-thumb-2 {
            height: 220px;
        }
       
        .tp-product-title-2 {
            font-size: 0.9rem;
            height: 2.6em;
        }
       
        .tp-product-price-2.new-price {
            font-size: 1rem;
        }
    }
   
    @media (max-width: 576px) {
        .tp-product-thumb-2 {
            height: 200px;
        }
       
        .tp-product-content-2 {
            padding: 12px;
        }
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
                <form method="GET" action="{{ route('client.categories.show', $category->id) }}">
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


                    <!-- Price Filter -->
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


                    <!-- Brand Filter -->
                    <div class="tp-shop-widget mb-35">
                        <h3 class="tp-shop-widget-title">Thương hiệu</h3>
                        <div class="tp-shop-widget-content">
                            <div class="tp-shop-widget-checkbox">
                                <ul class="filter-items filter-checkbox">
                                    @foreach($brands as $brand)
                                    <li class="d-flex justify-content-between mb-10">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="brands[]" value="{{ $brand->id }}" id="brand_{{ $brand->id }}" {{ collect(request('brands'))->contains($brand->id) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="brand_{{ $brand->id }}">{{ $brand->name }}</label>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>


                    <!-- Variant Attributes Filter -->
                    @if(isset($variantAttributes) && $variantAttributes->count() > 0)
                        @foreach($variantAttributes as $attribute)
                            @php
                                $isColorAttribute = in_array(strtolower(trim($attribute->name)), ['màu sắc', 'màu', 'color', 'mau', 'mau sac']);
                            @endphp
                            <div class="tp-shop-widget mb-35">
                                <h3 class="tp-shop-widget-title">{{ $attribute->name }}</h3>
                                <div class="tp-shop-widget-content">
                                    @if($isColorAttribute)
                                        <div class="color-filter">
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach($attribute->attributeValues as $value)
                                                    @php
                                                        $isActive = is_array(request('variant_attributes.'.$attribute->id)) && in_array($value->id, request('variant_attributes.'.$attribute->id, []));
                                                        $colorCode = $value->color_code ?: '#cccccc';
                                                        $colorStyle = "background-color: $colorCode; width: 30px; height: 30px; border-radius: 50%; border: 1px solid #ddd;";
                                                        if (strpos($colorCode, 'http') === 0) {
                                                            $colorStyle = "background-image: url('$colorCode'); background-size: cover; background-position: center; width: 30px; height: 30px; border-radius: 50%; border: 1px solid #ddd;";
                                                        }
                                                    @endphp
                                                    <div class="color-option {{ $isActive ? 'active' : '' }}"
                                                         data-attribute-id="{{ $attribute->id }}"
                                                         data-value-id="{{ $value->id }}"
                                                         title="{{ $value->value }}"
                                                         style="cursor: pointer; display: inline-block; margin: 3px; border: 2px solid {{ $isActive ? '#000' : '#ddd' }}; border-radius: 50%;">
                                                        <div class="color-swatch" style="{{ $colorStyle }}">
                                                            <input type="checkbox"
                                                                   name="variant_attributes[{{ $attribute->id }}][]"
                                                                   value="{{ $value->id }}"
                                                                   id="variant_attr_{{ $attribute->id }}_{{ $value->id }}"
                                                                   style="display: none;"
                                                                   {{ $isActive ? 'checked' : '' }}>
                                                            <span class="checkmark" style="display: {{ $isActive ? 'block' : 'none' }}; color: white; text-align: center; line-height: 26px;">✓</span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <div class="tp-shop-widget-checkbox">
                                            <ul class="filter-items filter-checkbox">
                                                @foreach($attribute->attributeValues as $value)
                                                    <li class="d-flex justify-content-between mb-2">
                                                        <div class="form-check">
                                                            <input
                                                                class="form-check-input"
                                                                type="checkbox"
                                                                name="variant_attributes[{{ $attribute->id }}][]"
                                                                value="{{ $value->id }}"
                                                                id="variant_attr_{{ $attribute->id }}_{{ $value->id }}"
                                                                {{ is_array(request('variant_attributes.'.$attribute->id)) && in_array($value->id, request('variant_attributes.'.$attribute->id, [])) ? 'checked' : '' }}
                                                            >
                                                            <label class="form-check-label" for="variant_attr_{{ $attribute->id }}_{{ $value->id }}">
                                                                {{ $value->value }}
                                                            </label>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif


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
                    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 product-item{{ $index >= $defaultShow ? ' d-none-by-js' : '' }}" style="margin-bottom: 30px;">
                        <div class="tp-product-item-2">
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
                            <div class="tp-product-content-2">
                                <div class="tp-product-tag-2">
                                    <a href="#" title="{{ $product->brand->name ?? 'Không có thương hiệu' }}">
                                        {{ $product->brand->name ?? 'Không có thương hiệu' }}
                                    </a>
                                </div>
                                <h3 class="tp-product-title-2">
                                    <a href="{{ $product->slug ? route('client.product.show', ['slug' => $product->slug]) : '#' }}" title="{{ $product->name }}">
                                        {{ $product->name }}
                                    </a>
                                </h3>
                                @php
                                   $validReviews = $product->reviews
                                       ->where('is_active', 1)
                                       ->where('review_id', null)
                                       ->where('rating', '>', 0);




                                   $avg = $validReviews->count() > 0 ? round($validReviews->avg('rating')) : 0;
                                @endphp




                                <div class="tp-product-rating-icon tp-product-rating-icon-2">
                                    @if ($validReviews->count() > 0)
                                        <div class="review-item">
                                            <div class="text-warning">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    @if ($i <= $avg)
                                                        ★
                                                    @else
                                                        ☆
                                                    @endif
                                                @endfor
                                            </div>
                                        </div>
                                    @else
                                        <p>Chưa có đánh giá.</p>
                                    @endif
                                </div>
                                {{-- Debug info --}}
                                @if(config('app.debug') && $product->type === 'variant')
                                    <div class="debug-info" style="font-size: 10px; color: #999; margin-bottom: 5px;">
                                        Debug: Type={{ $product->type }}, Variants={{ $product->variants->count() }},
                                        @if($product->variants->count() > 0)
                                            First variant: {{ $product->variants->first()->regular_price ?? 'N/A' }}
                                        @else
                                            No variants
                                        @endif
                                    </div>
                                @endif
                               
                                <div class="tp-product-price-wrapper-2">
                                    @php
                                        if ($product->type === 'variant' && $product->variants->count() > 0) {
                                            // Debug: Kiểm tra dữ liệu biến thể
                                            // {{-- dd($product->variants->toArray()) --}}
                                           
                                            // Lấy ngẫu nhiên một biến thể để hiển thị giá
                                            $randomVariant = $product->variants->random();
                                           
                                            // Lấy giá từ biến thể ngẫu nhiên
                                            $displayPrice = $randomVariant->sale_price > 0 ? $randomVariant->sale_price : $randomVariant->regular_price;
                                            $displayOriginalPrice = $randomVariant->sale_price > 0 ? $randomVariant->regular_price : null;
                                           
                                            // Đảm bảo giá không phải 0
                                            if ($displayPrice <= 0) {
                                                // Nếu giá vẫn 0, lấy giá từ biến thể đầu tiên
                                                $firstVariant = $product->variants->first();
                                                $displayPrice = $firstVariant->sale_price > 0 ? $firstVariant->sale_price : $firstVariant->regular_price;
                                                $displayOriginalPrice = $firstVariant->sale_price > 0 ? $firstVariant->regular_price : null;
                                            }
                                           
                                            // Nếu vẫn 0, lấy giá cao nhất từ tất cả biến thể
                                            if ($displayPrice <= 0) {
                                                $maxPrice = $product->variants->max('regular_price');
                                                $displayPrice = $maxPrice > 0 ? $maxPrice : 0;
                                                $displayOriginalPrice = null;
                                            }
                                           
                                            // Debug: Log giá trị để kiểm tra
                                            // \Log::info("Product: {$product->name}, Type: {$product->type}, Variants count: {$product->variants->count()}, Display price: {$displayPrice}");
                                        } else {
                                            // Sản phẩm thường
                                            $displayPrice = $product->sale_price > 0 ? $product->sale_price : $product->price;
                                            $displayOriginalPrice = $product->sale_price > 0 ? $product->price : null;
                                        }
                                    @endphp
                                   
                                    <span class="tp-product-price-2 new-price">
                                        {{ number_format($displayPrice, 0, ',', '.') }} <span style="color: red;">đ</span>
                                    </span>
                                    @if ($displayOriginalPrice && $displayOriginalPrice > $displayPrice)
                                        <span class="tp-product-price-2 old-price">
                                            {{ number_format($displayOriginalPrice, 0, ',', '.') }} <span style="color: red;">đ</span>
                                        </span>
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
    document.addEventListener('DOMContentLoaded', function() {
        // Hàm cập nhật tên checkbox
        function updateCheckboxNames() {
            document.querySelectorAll('.color-option').forEach(option => {
                const checkbox = option.querySelector('input[type="checkbox"]');
                const attributeId = option.dataset.attributeId;
                if (checkbox && attributeId) {
                    checkbox.name = `variant_attributes[${attributeId}][]`;
                }
            });
        }


        // Xử lý sự kiện click vào ô màu
        document.querySelectorAll('.color-option').forEach(option => {
            option.addEventListener('click', function(e) {
                e.preventDefault();
                const checkbox = this.querySelector('input[type="checkbox"]');
                const isActive = this.classList.contains('active');
                const checkmark = this.querySelector('.checkmark');
               
                // Toggle trạng thái active
                if (isActive) {
                    this.classList.remove('active');
                    checkbox.checked = false;
                    this.style.borderColor = '#ddd';
                    if (checkmark) checkmark.style.display = 'none';
                } else {
                    this.classList.add('active');
                    checkbox.checked = true;
                    this.style.borderColor = '#000';
                    if (checkmark) checkmark.style.display = 'block';
                }
               
                updateCheckboxNames();
            });
        });
       
        // Khởi tạo trạng thái ban đầu cho các ô màu
        document.querySelectorAll('.color-option').forEach(option => {
            const checkbox = option.querySelector('input[type="checkbox"]');
            const checkmark = option.querySelector('.checkmark');
            if (checkbox && checkbox.checked) {
                option.classList.add('active');
                option.style.borderColor = '#000';
                if (checkmark) checkmark.style.display = 'block';
            }
        });
       
        // Gọi hàm cập nhật tên checkbox khi tải trang
        updateCheckboxNames();
       
        // Xử lý sự kiện submit form
        const filterForm = document.querySelector('.tp-shop-sidebar form');
        if (filterForm) {
            filterForm.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang lọc...';
                }
            });
        }
    });
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

