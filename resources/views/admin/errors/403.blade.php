<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lỗi 403</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">
    <div class="text-center">
        <h1 class="display-1 fw-bold text-danger">403</h1>
        <p class="fs-3">⚠️ <span class="text-dark">Không có trang này trên hệ thống.</span></p>
        <a href="{{ route('showLoginForm') }}" class="btn btn-primary">Về trang chủ</a>
    </div>
</body>
</html>