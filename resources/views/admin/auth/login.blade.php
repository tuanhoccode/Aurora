<!DOCTYPE html>
<html lang="en" class="h-100">


<!-- Mirrored from techzaa.in/larkon/admin/auth-signin.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 10 Jan 2025 15:42:45 GMT -->

<head>
    <!-- Title Meta -->
    <meta charset="utf-8" />
    <title>Đăng nhập | Larkon - Responsive Admin Dashboard Template</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A fully responsive premium admin dashboard template" />
    <meta name="author" content="Techzaa" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!-- Vendor css (Require in all Page) -->
    <link href="{{asset('assets1/css/vendor.min.css')}}" rel="stylesheet" type="text/css" />

    <!-- Icons css (Require in all Page) -->
    <link href="{{asset('assets1/css/icons.min.css')}}" rel="stylesheet" type="text/css" />

    <!-- App css (Require in all Page) -->
    <link href="{{asset('assets1/css/app.min.css')}}" rel="stylesheet" type="text/css" />

    <!-- Theme Config js (Require in all Page) -->
    <script src="{{asset('assets1/js/config.js')}}"></script>
    <style>
        .error {
            color: #dc3545;
            /* Màu đỏ Bootstrap */
            font-size: 0.9rem;
            margin-top: 5px;
        }
    </style>
</head>

<body class="h-100">
    <div class="d-flex flex-column h-100 p-3">
        <div class="d-flex flex-column flex-grow-1">
            <div class="row h-100">
                <div class="col-xxl-7">
                    <div class="row justify-content-center h-100">
                        <div class="col-lg-6 py-lg-5">
                            <div class="d-flex flex-column h-100 justify-content-center">
                                <div class="auth-logo mb-4">
                                    <a href="index.html" class="logo-dark">
                                        <img src="{{asset('assets1/images/logo-dark.png')}}" height="24" alt="logo dark">
                                    </a>

                                    <a href="index.html" class="logo-light">
                                        <img src="{{asset('assets1/images/logo-light.png')}}" height="24" alt="logo light">
                                    </a>
                                </div>

                                <h2 class="fw-bold fs-24">Đăng Nhập</h2>

                                <p class="text-muted mt-1 mb-4">Nhập địa chỉ email và mật khẩu của bạn để truy cập vào bảng quản trị.</p>

                                <div class="mb-5">
                                    <form action="{{route('admin.login')}}" class="authentication-form" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label" for="example-email">Email</label>
                                            <input type="email" id="email" name="email" class="form-control bg-" placeholder="Enter your email" value="{{old('email')}}">
                                            @error('email')
                                            <div class="error">{{$message}}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <a href="auth-password.html" class="float-end text-muted text-unline-dashed ms-1">Quên mật khẩu</a>
                                            <label class="form-label" for="password">Mật khẩu</label>
                                            <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" value="{{old('password')}}">
                                            @error('password')
                                            <div class="error">{{$message}}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="checkbox-signin">
                                                <label class="form-check-label" for="checkbox-signin">Remember me</label>
                                            </div>
                                        </div>

                                        <div class="mb-1 text-center d-grid">
                                            <button class="btn btn-soft-primary" type="submit">Đăng nhập</button>
                                        </div>
                                    </form>

                                    <p class="mt-3 fw-semibold no-span">OR sign with</p>

                                    <div class="d-grid gap-2">
                                        <a href="javascript:void(0);" class="btn btn-soft-dark"><i class="bx bxl-google fs-20 me-1"></i> Sign in with Google</a>
                                        <a href="javascript:void(0);" class="btn btn-soft-primary"><i class="bx bxl-facebook fs-20 me-1"></i> Sign in with Facebook</a>
                                    </div>
                                </div>

                                <p class="text-danger text-center">Don't have an account? <a href="auth-signup.html" class="text-dark fw-bold ms-1">Sign Up</a></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-5 d-none d-xxl-flex">
                    <div class="card h-100 mb-0 overflow-hidden">
                        <div class="d-flex flex-column h-100">
                            <img src="{{asset('assets1/images/small/img-1.jpg')}}" alt="" class="w-100 h-100">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vendor Javascript (Require in all Page) -->
    <script src="{{asset('assets1/js/vendor.js')}}"></script>

    <!-- App Javascript (Require in all Page) -->
    <script src="{{asset('assets1/js/app.js')}}"></script>

</body>


<!-- Mirrored from techzaa.in/larkon/admin/auth-signin.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 10 Jan 2025 15:42:45 GMT -->

</html>