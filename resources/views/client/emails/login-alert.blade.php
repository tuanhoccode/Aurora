<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông báo đăng nhập bất thường</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light py-5">
    <div class="container">
        <div class="mx-auto col-md-8 col-lg-6">
            <div class="card shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h4 class="text-danger text-center mb-4">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        Cảnh báo đăng nhập bất thường
                    </h4>
                    <p class="mb-3">Xin chào <strong>{{$user->fullname}}</strong>,</p>
                    <p class="mb-3">Chúng tôi vừa phát hiện một lần đăng nhập từ thiết bị hoặc vị trí chưa từng thấy trước đó.</p>
                    <div class="border rounded p-3 bg-light mb-4">
                        <p class="mb-1"><strong>- IP:</strong>{{$ip}}</p>
                        <p class="mb-1"><strong>- Thiết bị:</strong>{{$agent}}</p>
                        <p class="mb-1"><strong>- Thời gian:</strong>{{now()->format('H:i:s d/m/Y')}}</p>
                    </div>
                    <p class="mb-3">Nếu đây là bạn, bạn có thể bỏ qua thông báo này.</p>
                    <p class="mb-3">Nếu không phải bạn, hãy đổi mật khẩu ngay để bảo vệ tài khoản.</p>
                    <div class="text-center">
                        <a href="{{route('loginHistory')}}" class="btn btn-primary px-4">Xem lịch sử đăng nhập</a>
                    </div>
                    <div class="card-footer text-muted text-center small">
                        Email này được gửi tự động. Vui lòng không trả lời
                    </div>
                </div>
            </div>
        </div>


</body>

</html>