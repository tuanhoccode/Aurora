<!DOCTYPE html>
<html>
<head>
    <title>Đăng nhập Admin</title>
</head>
<body>
    <h2>Đăng nhập Quản Trị</h2>

    <form method="POST" action="{{ route('admin.login') }}">
        @csrf
        <label>Email:</label><br>
        <input type="email" name="email" ><br><br>

        <label>Mật khẩu:</label><br>
        <input type="text" name="password" ><br><br>

        <button type="submit">Đăng nhập</button>
    </form>

    @if ($errors->any())
        <div style="color:red;">
            {{ $errors->first() }}
        </div>
    @endif
</body>
</html>
