@extends('client.layouts.default')

@section('title', 'Danh sách yêu thích - Aurora')

@section('content')
   <!-- BREADCRUMB -->
   <section class="breadcrumb__area include-bg pt-95 pb-50">
      <!-- ... không đổi ... -->
   </section>

   <!-- DANH SÁCH YÊU THÍCH -->
   <section class="tp-cart-area pb-120">
      <div class="container">
        <div class="row">
          @forelse($wishlists as $wishlist)
           <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
            <div class="wishlist-card border rounded p-3 position-relative h-100 d-flex flex-column">
               <a href="{{ route('client.product.show', ['slug' => $wishlist->product->slug]) }}"
                class="text-center mb-3">
                <img src="{{ asset('storage/' . $wishlist->product->thumbnail) }}"
                  alt="{{ $wishlist->product->name }}" style="max-height: 180px; object-fit: contain; width: 100%;">
               </a>
               <h6 class="text-center flex-grow-1">
                <a href="{{ route('client.product.show', ['slug' => $wishlist->product->slug]) }}"
                  class="text-decoration-none text-dark">
                  {{ $wishlist->product->name }}
                </a>
               </h6>
               <form action="{{ route('wishlist.destroy', $wishlist->id) }}" method="POST" class="mt-3 text-center">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-danger w-100"
                  onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?')">Xóa</button>
               </form>
            </div>
           </div>
         @empty
           <div class="col-12 text-center">
            <p>Danh sách yêu thích của bạn đang trống.</p>
           </div>
         @endforelse
        </div>

        <!-- Phân trang -->
        <div class="row">
          <div class="col d-flex justify-content-center">
            {{ $wishlists->links() }}
          </div>
        </div>

        <div class="row mt-4">
          <div class="col text-center">
            <a href="{{ route('shopping-cart.index') }}" class="tp-cart-update-btn btn btn-primary px-4 py-2">Đi đến giỏ
               hàng</a>
          </div>
        </div>
      </div>
   </section>
@endsection