<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f4f4f4; padding: 30px;">
    <div style="max-width: 600px; margin: auto; background:white; border-radius: 8px; padding: 20px;">
        <p>Aurora xin chào {{$user->fullname ?? 'Quý khách'}}</p>
        <p>Bạn vừa yêu cầu đặt lại mật kẩu cho tài khoảng của mình </p>
        <p>Vui lòng nhấn vào liên kết dưới đây để đặt lại mật khẩu:</p>
        <p>
            <a href="{{ $resetUrl }}" style="display: inline-block; padding: 12px 20px; background-color: #007bff; color: #fff; text-decoration: none; border-radius: 6px;">
                Đặt lại mật khẩu
            </a>
        </p>
        <p>Liên kết này sẽ hết hạn sau 60 phút.</p>
        <p>Nếu bạn không yêu cầu vui lòng bỏ qua email này.</p>
    </div>
</body>
</html>