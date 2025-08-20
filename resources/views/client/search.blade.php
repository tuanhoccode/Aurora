@extends('client.layouts.default')
@section('title', 'Kết quả tìm kiếm')
@section('content')
<style>
  .search-title-row {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    flex-wrap: wrap;
    margin-bottom: 0.5rem;
  }
  .search-title {
    font-size: 2rem;
    font-weight: 700;
    color: #111;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  .search-divider {
    border: none;
    border-top: 1.5px solid #e5e5e5;
    margin: 0.5rem 0 1.5rem 0;
  }
  .search-category-list {
    padding-left: 0;
    margin-bottom: 1.5rem;
    list-style: none;
  }
  .search-category-list li {
    margin-bottom: 0.5rem;
    font-size: 1.08rem;
    display: flex;
    align-items: center;
    gap: 6px;
  }
  .search-category-list li i {
    color: #d90429;
    font-size: 1.1rem;
  }
  .search-empty {
    text-align: center;
    color: #888;
    font-size: 1.15rem;
    margin: 2.5rem 0 2rem 0;
  }
  .search-breadcrumb {
    font-size: 1rem;
    color: #888;
    margin-bottom: 0.7rem;
  }
  .search-breadcrumb a {
    color: #888;
    text-decoration: none;
  }
  .search-breadcrumb span {
    margin: 0 4px;
  }
  .search-count {
    color: #888;
    font-size: 1.08rem;
    font-weight: 400;
    margin-left: 1rem;
    white-space: nowrap;
  }
</style>
<div class="container py-5">
    <div class="search-breadcrumb">
      <a href="/">Trang chủ</a> <span>/</span> <span>Tìm kiếm</span>
    </div>
    <div class="search-title-row">
      <div class="search-title text-capitalize fw-normal text-dark" style="color: #1E3A8A;">Kết quả tìm kiếm: {{ mb_strtoupper($query) }}</div>
      @php
        $inStockCount = $products->filter(function($product) {
          return $product->type === 'variant'
            ? ($product->variants->sum('stock') > 0)
            : (($product->stock ?? 0) > 0);
        })->count();
      @endphp
      <div class="search-count fw-normal text-dark" style="font-size: 1.1rem;">{{ $inStockCount }} sản phẩm</div>
    </div>
    <hr class="search-divider">
    <div class="d-flex justify-content-end align-items-center mb-3">
      <form method="GET" action="" class="d-flex align-items-center" style="gap:8px;">
        <input type="hidden" name="query" value="{{ $query }}">
        <label for="sort" class="mb-0" style="font-weight:500; color:#444;">Sắp xếp:</label>
        <select name="sort" id="sort" class="form-select form-select-sm" style="width:auto; min-width:160px;" onchange="this.form.submit()">
          <option value="newest" {{ request('sort','newest')=='newest' ? 'selected' : '' }}>Mới nhất</option>
          <option value="price_desc" {{ request('sort')=='price_desc' ? 'selected' : '' }}>Giá cao đến thấp</option>
          <option value="price_asc" {{ request('sort')=='price_asc' ? 'selected' : '' }}>Giá thấp đến cao</option>
        </select>
      </form>
    </div>
    <h4 class="mt-4 mb-3 text-dark fw-normal">Sản Phẩm Phù Hợp</h4>
    <div class="row justify-content-center">
        @php $found = false; @endphp
        @php
            $matchedProduct = null;
            foreach ($products as $product) {
                if (mb_strtolower(trim($product->name)) === mb_strtolower(trim($query))) {
                    $matchedProduct = $product;
                    break;
                }
            }
        @endphp
        @if($matchedProduct && $matchedProduct->slug)
            <script>
                window.location.href = "{{ route('client.product.show', ['slug' => $matchedProduct->slug]) }}";
            </script>
        @endif
        @foreach($products as $product)
            @php
                $isOutOfStock = $product->type === 'variant'
                    ? ($product->variants->sum('stock') <= 0)
                    : (($product->stock ?? 0) <= 0);
            @endphp
            @if(!$isOutOfStock)
                @php $found = true; @endphp
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-4 d-flex">
                    <div class="tp-product-item-2 mb-40 search-result w-100">
                        <div class="tp-product-thumb-2 p-relative z-index-1 fix w-img @if($isOutOfStock) out-of-stock @endif">
                            @if($product->slug)
                            <a href="{{ route('client.product.show', ['slug' => $product->slug]) }}">
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                                @if($isOutOfStock)
                                    <span class="product-badge-outofstock">Hết hàng</span>
                                @endif
                            </a>
                            @else
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                            @endif
                            @if($isOutOfStock)
                                <div class="product-out-of-stock-overlay">
                                    <i class="fa fa-exclamation-circle" style="font-size:2rem;"></i>
                                    Sản phẩm đã hết hàng
                                </div>
                            @endif
                        </div>
                        <div class="tp-product-content-2 pt-15">
                            <div class="tp-product-tag-2">
                                <a href="#">{{ $product->brand->name ?? 'Không có thương hiệu' }}</a>
                            </div>
                            <h3 class="tp-product-title-2">
                                @if($product->slug)
                                    <a href="{{ route('client.product.show', ['slug' => $product->slug]) }}">{{ $product->name }}</a>
                                @else
                                    {{ $product->name }}
                                @endif
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
                                    <div class="review-item pb-3">
                                        <div class="text-warning mb-1">
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
                            <div class="tp-product-price-wrapper-2">
                                <span
                                    class="tp-product-price-2 new-price">{{ number_format($product->price, 0, ',', '.') }} <span style="color: red;">đ</span></span>
                                  @if ($product->original_price && $product->original_price > $product->price)
                                <span
                                    class="tp-product-price-2 old-price">{{ number_format($product->original_price, 0, ',', '.') }} <span style="color: red;">đ</span></span>
                                  @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
        @if(!$found)
            <div class="col-12 search-empty">
                <i class="fa fa-search"></i> Không tìm thấy sản phẩm phù hợp.
            </div>
        @endif
    </div>
</div>
@endsection 