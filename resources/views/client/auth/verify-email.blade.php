<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác thực Email</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f9fc;
        }

        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            border-radius: 2rem;
        }
    </style>
</head>

<body>

    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="col-md-8 col-lg-6">
            <div class="card p-4">
                <div class="card-body text-center">
                    <h3 class="mb-3">Vui lòng xác minh email của bạn</h3>

                    @if (session('status') == 'verification-link-sent')
                    <div class="alert alert-success">
                        Một liên kết xác thực mới đã được gửi đến địa chỉ email của bạn.
                    </div>
                    @endif

                    <p class="mb-4">
                        Trước khi tiếp tục, vui lòng kiểm tra email của bạn để xác minh. <br>
                        Nếu bạn không nhận được email, bạn có thể yêu cầu gửi lại.
                    </p>

                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100 mb-3">
                           📩 Gửi lại email xác thực
                        </button>
                    </form>

                    <a href="{{ route('showRegister')}}" class="btn btn-outline-secondary w-100">
                        ⬅ Quay lại trang đăng ký
                    </a>

                </div>
            </div>
        </div>
    </div>

</body>

</html>