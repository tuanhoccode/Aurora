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
                                            <input class="form-check-input" type="checkbox" name="on_sale" value="1" id="on_sale" {{ request('on_sale') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="on_sale">Đang giảm giá</label>
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
                                            <input class="form-check-input" type="checkbox" name="brands[]" value="{{ $brand->id }}" id="brand_{{ $brand->id }}" {{ collect(request('brands'))->contains($brand->id) ? 'checked' : '' }}>
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
                                            <input class="form-check-input" type="checkbox" name="prices[]" value="{{ $i }}" id="price_range_{{ $i }}" {{ collect(request('prices'))->contains($i) ? 'checked' : '' }}>
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
                                <div class="tp-product-action-2 tp-product-action-blackStyle">
                                    <div class="tp-product-action-item-2 d-flex flex-column">
                                        <form method="POST" action="{{ route('shopping-cart.add') }}" class="add-to-cart-form">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <input type="hidden" name="product_variant_id" value="{{ $product->default_variant_id ?? '' }}">
                                            <input type="hidden" name="quantity" value="1">
                                            <input type="hidden" name="price" value="{{ $product->price }}">
                                            <button type="submit" class="tp-product-action-btn-2 tp-product-add-cart-btn" @if($isOutOfStock) disabled style="opacity:0.5;cursor:not-allowed;" @endif>
                                                <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M3.34706 4.53799L3.85961 10.6239C3.89701 11.0923 4.28036 11.4436 4.74871 11.4436H4.75212H14.0265H14.0282C14.4711 11.4436 14.8493 11.1144 14.9122 10.6774L15.7197 5.11162C15.7384 4.97924 15.7053 4.84687 15.6245 4.73995C15.5446 4.63218 15.4273 4.5626 15.2947 4.54393C15.1171 4.55072 7.74498 4.54054 3.34706 4.53799ZM4.74722 12.7162C3.62777 12.7162 2.68001 11.8438 2.58906 10.728L1.81046 1.4837L0.529505 1.26308C0.181854 1.20198 -0.0501969 0.873587 0.00930333 0.526523C0.0705036 0.17946 0.406255 -0.0462578 0.746256 0.00805037L2.51426 0.313534C2.79901 0.363599 3.01576 0.5995 3.04042 0.888012L3.24017 3.26484C15.3748 3.26993 15.4139 3.27587 15.4726 3.28266C15.946 3.3514 16.3625 3.59833 16.6464 3.97849C16.9303 4.35779 17.0493 4.82535 16.9813 5.29376L16.1747 10.8586C16.0225 11.9177 15.1011 12.7162 14.0301 12.7162H14.0259H4.75402H4.74722Z" fill="currentColor" />
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12.6629 7.67446H10.3067C9.95394 7.67446 9.66919 7.38934 9.66919 7.03804C9.66919 6.68673 9.95394 6.40161 10.3067 6.40161H12.6629C13.0148 6.40161 13.3004 6.68673 13.3004 7.03804C13.3004 7.38934 13.0148 7.67446 12.6629 7.67446Z" fill="currentColor" />
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M4.38171 15.0212C4.63756 15.0212 4.84411 15.2278 4.84411 15.4836C4.84411 15.7395 4.63756 15.9469 4.38171 15.9469C4.12501 15.9469 3.91846 15.7395 3.91846 15.4836C3.91846 15.2278 4.12501 15.0212 4.38171 15.0212Z" fill="currentColor" />
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M4.38082 15.3091C4.28477 15.3091 4.20657 15.3873 4.20657 15.4833C4.20657 15.6763 4.55592 15.6763 4.55592 15.4833C4.55592 15.3873 4.47687 15.3091 4.38082 15.3091ZM4.38067 16.5815C3.77376 16.5815 3.28076 16.0884 3.28076 15.4826C3.28076 14.8767 3.77376 14.3845 4.38067 14.3845C4.98757 14.3845 5.48142 14.8767 5.48142 15.4826C5.48142 16.0884 4.98757 16.5815 4.38067 16.5815Z" fill="currentColor" />
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M13.9701 15.0212C14.2259 15.0212 14.4333 15.2278 14.4333 15.4836C14.4333 15.7395 14.2259 15.9469 13.9701 15.9469C13.7134 15.9469 13.5068 15.7395 13.5068 15.4836C13.5068 15.2278 13.7134 15.0212 13.9701 15.0212Z" fill="currentColor" />
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M13.9692 15.3092C13.874 15.3092 13.7958 15.3874 13.7958 15.4835C13.7966 15.6781 14.1451 15.6764 14.1443 15.4835C14.1443 15.3874 14.0652 15.3092 13.9692 15.3092ZM13.969 16.5815C13.3621 16.5815 12.8691 16.0884 12.8691 15.4826C12.8691 14.8767 13.3621 14.3845 13.969 14.3845C14.5768 14.3845 15.0706 14.8767 15.0706 15.4826C15.0706 16.0884 14.5768 16.5815 13.969 16.5815Z" fill="currentColor" />
                                                </svg>
                                                <span class="tp-product-tooltip tp-product-tooltip-right">Thêm vào giỏ</span>
                                            </button>
                                        </form>
                                        <button type="button" class="tp-product-action-btn-2 tp-product-quick-view-btn" data-bs-toggle="modal" data-bs-target="#producQuickViewModal">
                                            <svg width="18" height="15" viewBox="0 0 18 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M8.99948 5.06828C7.80247 5.06828 6.82956 6.04044 6.82956 7.23542C6.82956 8.42951 7.80247 9.40077 8.99948 9.40077C10.1965 9.40077 11.1703 8.42951 11.1703 7.23542C11.1703 6.04044 10.1965 5.06828 8.99948 5.06828ZM8.99942 10.7482C7.0581 10.7482 5.47949 9.17221 5.47949 7.23508C5.47949 5.29705 7.0581 3.72021 8.99942 3.72021C10.9407 3.72021 12.5202 5.29705 12.5202 7.23508C12.5202 9.17221 10.9407 10.7482 8.99942 10.7482Z" fill="currentColor" />
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M1.41273 7.2346C3.08674 10.9265 5.90646 13.1215 8.99978 13.1224C12.0931 13.1215 14.9128 10.9265 16.5868 7.2346C14.9128 3.54363 12.0931 1.34863 8.99978 1.34773C5.90736 1.34863 3.08674 3.54363 1.41273 7.2346ZM9.00164 14.4703H8.99804H8.99714C5.27471 14.4676 1.93209 11.8629 0.0546754 7.50073C-0.0182251 7.33091 -0.0182251 7.13864 0.0546754 6.96883C1.93209 2.60759 5.27561 0.00288103 8.99714 0.000185582C8.99894 -0.000712902 8.99894 -0.000712902 8.99984 0.000185582C9.00164 -0.000712902 9.00164 -0.000712902 9.00254 0.000185582C12.725 0.00288103 16.0676 2.60759 17.945 6.96883C18.0188 7.13864 18.0188 7.33091 17.945 7.50073C16.0685 11.8629 12.725 14.4676 9.00254 14.4703H9.00164Z" fill="currentColor" />
                                            </svg>
                                            <span class="tp-product-tooltip tp-product-tooltip-right">Xem nhanh</span>
                                        </button>
                                        <button type="button" class="tp-product-action-btn-2 tp-product-add-to-wishlist-btn">
                                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M1.60355 7.98635C2.83622 11.8048 7.7062 14.8923 9.0004 15.6565C10.299 14.8844 15.2042 11.7628 16.3973 7.98985C17.1806 5.55102 16.4535 2.46177 13.5644 1.53473C12.1647 1.08741 10.532 1.35966 9.40484 2.22804C9.16921 2.40837 8.84214 2.41187 8.60476 2.23329C7.41078 1.33952 5.85105 1.07778 4.42936 1.53473C1.54465 2.4609 0.820172 5.55014 1.60355 7.98635ZM9.00138 17.0711C8.89236 17.0711 8.78421 17.0448 8.68574 16.9914C8.41055 16.8417 1.92808 13.2841 0.348132 8.3872C0.347252 8.3872 0.347252 8.38633 0.347252 8.38633C-0.644504 5.30321 0.459792 1.42874 4.02502 0.284605C5.69904 -0.254635 7.52342 -0.0174044 8.99874 0.909632C10.4283 0.00973263 12.3275 -0.238878 13.9681 0.284605C17.5368 1.43049 18.6446 5.30408 17.6538 8.38633C16.1248 13.2272 9.59485 16.8382 9.3179 16.9896C9.21943 17.0439 9.1104 17.0711 9.00138 17.0711Z" fill="currentColor" />
                                            </svg>
                                            <span class="tp-product-tooltip tp-product-tooltip-right">Thêm vào yêu thích</span>
                                        </button>
                                    </div>
                                </div>
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