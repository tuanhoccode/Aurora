@extends('client.layouts.default')

@section('title', 'Danh sách yêu thích - Aurora')

@section('content')
  <!-- BREADCRUMB -->
  <section class="breadcrumb__area include-bg pt-30 pb-10">
    <!-- Giữ nguyên nội dung breadcrumb nếu có -->
  </section>

  <!-- DANH SÁCH YÊU THÍCH -->
  <section class="tp-cart-area pb-120">
    <div class="container">

      <!-- Tiêu đề -->
      <div class="row mb-5">
        <div class="col text-center">
          <h2 class="fw-bold">🧡 Danh Sách Yêu Thích Của Bạn</h2>
          <p class="text-muted">Các sản phẩm bạn đã thêm vào danh sách yêu thích.</p>
        </div>
      </div>

      <div class="row">
        @forelse($wishlists as $wishlist)
          <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
            <div class="wishlist-card border rounded p-3 position-relative h-100 d-flex flex-column shadow-sm">
              <a href="{{ route('client.product.show', ['slug' => $wishlist->product->slug]) }}"
                class="text-center mb-3 d-block">
                <img src="{{ asset('storage/' . $wishlist->product->thumbnail) }}"
                  alt="{{ $wishlist->product->name }}"
                  style="max-height: 180px; object-fit: contain; width: 100%;">
              </a>
              <h6 class="text-center flex-grow-1 mb-2">
                <a href="{{ route('client.product.show', ['slug' => $wishlist->product->slug]) }}"
                  class="text-decoration-none text-dark">
                  {{ $wishlist->product->name }}
                </a>
              </h6>

              <!-- Giá tiền -->
              @php
                $product = $wishlist->product;
                $now = now();
                $displayHtml = '';

                if ($product->type === 'variant' && $product->variants->count() > 0) {
                    // Tính giá hiện tại của từng variant theo khung thời gian sale
                    $prices = $product->variants->map(function($variant) use ($now) {
                        $price = $variant->regular_price;
                        if ($variant->sale_price
                            && $variant->sale_price > 0
                            && (!$variant->sale_starts_at || $variant->sale_starts_at <= $now)
                            && (!$variant->sale_ends_at || $variant->sale_ends_at >= $now)
                            && $variant->sale_price < $variant->regular_price) {
                            $price = $variant->sale_price;
                        }
                        return (float) $price;
                    })->filter(fn($p) => $p > 0);

                    $minPrice = $prices->count() ? $prices->min() : 0;
                    $maxPrice = $prices->count() ? $prices->max() : 0;

                    if ($minPrice > 0) {
                        if ($minPrice == $maxPrice) {
                            $displayHtml = '<span class="text-danger fs-4">' . number_format($minPrice, 0, ',', '.') . '₫</span>';
                        } else {
                            $displayHtml = '<span class="text-danger fs-4">' . number_format($minPrice, 0, ',', '.') . '₫ - ' . number_format($maxPrice, 0, ',', '.') . '₫</span>';
                        }
                    }
                } else {
                    // Sản phẩm thường
                    $current = $product->sale_price
                        && (!$product->sale_starts_at || $product->sale_starts_at <= $now)
                        && (!$product->sale_ends_at || $product->sale_ends_at >= $now)
                        && $product->sale_price < $product->price
                        ? $product->sale_price
                        : $product->price;
                    $original = ($current != $product->price && $product->price > 0) ? $product->price : null;

                    if ($current > 0) {
                        if ($original) {
                            $displayHtml = '<span class="text-muted text-decoration-line-through me-2">' . number_format($original, 0, ',', '.') . '₫</span>';
                        }
                        $displayHtml .= '<span class="text-danger fs-4">' . number_format($current, 0, ',', '.') . '₫</span>';
                    }
                }
              @endphp

              <p class="text-center mt-2 fw-semibold fs-5">
                {!! $displayHtml !== '' ? $displayHtml : '<span class="text-muted">Đang cập nhật</span>' !!}
              </p>

              <form action="{{ route('wishlist.destroy', $wishlist->id) }}" method="POST" class="mt-auto text-center">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-outline-danger w-100"
                  onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?')">🗑 Xóa khỏi yêu thích</button>
              </form>
            </div>
          </div>
        @empty
          <div class="col-12 text-center">
            <p class="text-muted">Danh sách yêu thích của bạn đang trống.</p>
          </div>
        @endforelse
      </div>

      <!-- Phân trang -->
      @if($wishlists->hasPages())
        <div class="row">
          <div class="col d-flex justify-content-center">
            {{ $wishlists->links() }}
          </div>
        </div>
      @endif

      <!-- Nút đi đến giỏ hàng -->
      <div class="row mt-5">
        <div class="col text-center">
          <a href="{{ route('shop') }}" class="tp-cart-update-btn btn btn-primary px-4 py-2">
            Đi đến cửa hàng
          </a>
        </div>
      </div>

    </div>
  </section>
@endsection
