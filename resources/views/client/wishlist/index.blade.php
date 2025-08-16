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
          <h2 class="fw-bold">🧡 Danh sách sản phẩm yêu thích của bạn</h2>
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
                $price = null;
                $originalPrice = null;

                if ($product->variants->count() > 0) {
                    $validVariants = $product->variants->filter(function ($variant) {
                        return ($variant->sale_price ?? 0) > 0 || ($variant->price ?? 0) > 0;
                    });

                    if ($validVariants->isNotEmpty()) {
                        $minSale = $validVariants->where('sale_price', '>', 0)->min('sale_price');
                        $minPrice = $validVariants->min('price');

                        $price = $minSale ?? $minPrice;
                        $originalPrice = $minSale ? $minPrice : null;
                    }
                } else {
                    $price = $product->sale_price > 0 ? $product->sale_price : $product->price;
                    $originalPrice = $product->sale_price > 0 ? $product->price : null;
                }
              @endphp

              <p class="text-center mt-2 fw-semibold fs-5">
                @if ($price > 0)
                  @if ($originalPrice)
                    <span class="text-muted text-decoration-line-through me-2">
                      {{ number_format($originalPrice, 0, ',', '.') }}₫
                    </span>
                  @endif
                  <span class="text-danger fs-4">{{ number_format($price, 0, ',', '.') }}₫</span>
                @else
                  <span class="text-muted">Liên hệ</span>
                @endif
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
