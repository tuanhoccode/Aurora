@extends('client.layouts.default')
@section('title', 'Lỗi 404')
@section('content')


<!-- error area start -->
<section class="tp-error-area pt-110 pb-110">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8 col-md-10">
                <div class="tp-error-content text-center">
                    <div class="tp-error-thumb">
                        <img src="assets/img/error/error.png" alt="">
                    </div>

                    <h3 class="tp-error-title">Rất tiếc! Không tìm thấy trang</h3>
                    <p>Rất tiếc! Có vẻ như trang bạn đang tìm kiếm không được tìm thấy.</p>

                    <a href="{{route('home')}}" class="tp-error-btn">Quay lại Trang chủ</a>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- error area end -->

@endsection