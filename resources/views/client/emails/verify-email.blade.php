<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác thực email Aurora</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f4f4f4; padding: 30px;">
    <div style="max-width: 600px; margin: auto; background:white; border-radius: 8px; padding: 20px;">
        <h2>Aurora xin chào {{$user->fullname ?? 'Quý khách'}}</h2>
        <p>Cảm ơn bạn đã đăng ký tài khoản tại <strong>Aurora</strong>. </p>
        <p>Vui lòng nhấn nút dưới đây để xác thực email và kích hoạt tài khoản: </p>
        <p style="text-align: center;">
            <a href="{{ $url }}" style="display: inline-block; padding: 12px 20px; background-color: #007bff; color: #fff; text-decoration: none; border-radius: 6px;">
                Xác thực Email Ngay
        </p>
        <p>Nếu bạn không tạo tài khoản hãy bỏ qua email này.</p>
        <hr>
        <small>Đây là email tự động, vui lòng không trả lời.</small>
    </div>
</body>
</html>