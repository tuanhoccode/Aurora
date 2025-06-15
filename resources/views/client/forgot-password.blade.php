@extends('client.layouts.default')
@section('title', 'Đăng nhập')
@section('content')
<!-- breadcrumb area start -->
 <style>
   .error {
      color: #dc3545;
      /* Màu đỏ Bootstrap */
      font-size: 0.9rem;
      margin-top: 5px;
   }
</style>

<section class="breadcrumb__area include-bg text-center pt-95 pb-50">
    <div class="container">
        <div class="row">
            <div class="col-xxl-12">
                <div class="breadcrumb__content p-relative z-index-1">
                    <h3 class="breadcrumb__title">Quên Mật Khẩu</h3>
                    <div class="breadcrumb__list">
                        <span> Đặt Lại Mật Khẩu</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- breadcrumb area end -->

<!-- login area start -->
<section class="tp-login-area pb-140 p-relative z-index-1 fix">
    <div class="tp-login-shape">
        <img class="tp-login-shape-1" src="assets2/img/login/login-shape-1.png" alt="">
        <img class="tp-login-shape-2" src="assets2/img/login/login-shape-2.png" alt="">
        <img class="tp-login-shape-3" src="assets2/img/login/login-shape-3.png" alt="">
        <img class="tp-login-shape-4" src="assets2/img/login/login-shape-4.png" alt="">
    </div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8">
                <div class="tp-login-wrapper">
                    <div class="tp-login-top text-center mb-30">
                        <h3 class="tp-login-title">Đặt Lại Mật Khẩu</h3>
                        <p>Nhập địa chỉ email của bạn để yêu cầu đặt lại mật khẩu.</p>
                    </div>
                    <form action="{{route('password.email')}}" method="post">
                        @csrf
                        <div class="tp-login-option">
                            <div class="tp-login-input-wrapper">
                                <div class="tp-login-input-box">
                                    <div class="tp-login-input">
                                        <input id="email" name="email" type="email" value="{{old('email')}}" placeholder="aurora@mail.com">
                                    </div>
                                    <div class="tp-login-input-title">
                                        <label for="email">Email của bạn</label>
                                    </div>
                                    @error('email')
                                    <div class="error">{{$message}}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="tp-login-bottom mb-15">
                                <button type="submit" class="tp-login-btn w-100">Gửi thư</button>
                            </div>
                            <div class="tp-login-suggetions d-sm-flex align-items-center justify-content-center">
                                <div class="tp-login-forgot">
                                    <span>Ghi nhớ mật khẩu? <a href="{{route('login')}}"> Đăng nhập</a></span>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- @if (session('success'))
    <script>
        toastr.success("{{ session('success') }}");
    </script>
@elseif (session('error'))
    <script>
        toastr.error("{{ session('error') }}");
    </script>
@endif -->
<!-- login area end -->

@endsection