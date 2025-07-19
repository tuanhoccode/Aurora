@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa Người dùng')

@section('content')
<div class="container-fluid px-4">
    <h1 class="h3 mb-4">Chỉnh sửa Người dùng</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Họ và tên -->
        <div class="mb-3">
            <label for="fullname" class="form-label">Họ và tên</label>
            <input type="text" class="form-control @error('fullname') is-invalid @enderror" id="fullname" name="fullname" value="{{ old('fullname', $user->fullname ?? '') }}" required>
            @error('fullname')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email ?? '') }}" required>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <!-- Số điện thoại -->
        <div class="mb-3">
            <label for="phone_number" class="form-label">Số điện thoại</label>
            <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ old('phone_number', $user->phone_number ?? '') }}">
            @error('phone_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <!-- Ảnh đại diện -->
        <div class="mb-3">
            <label for="avatar" class="form-label">Ảnh đại diện</label>
            <input type="file" class="form-control @error('avatar') is-invalid @enderror" id="avatar" name="avatar" accept="image/*">
            @error('avatar')<div class="invalid-feedback">{{ $message }}</div>@enderror
            @if($user->avatar)
                <img src="{{ asset($user->avatar) }}" alt="Avatar" style="max-width:150px; margin-top:10px;">
            @endif
        </div>

        <!-- Giới tính -->
        <div class="mb-3">
            <label for="gender" class="form-label">Giới tính</label>
            <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                <option value="">Chọn giới tính</option>
                <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Nam</option>
                <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Nữ</option>
                <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Khác</option>
            </select>
            @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <!-- Ngày sinh -->
        <div class="mb-3">
            <label for="birthday" class="form-label">Ngày sinh</label>
            <input type="date" class="form-control @error('birthday') is-invalid @enderror" id="birthday" name="birthday" value="{{ old('birthday', optional($user->birthday)->format('Y-m-d')) }}">
            @error('birthday')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <!-- Vai trò -->
        <div class="mb-3">
            <label for="role" class="form-label">Vai trò</label>
            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                <option value="customer" {{ old('role', $user->role) == 'customer' ? 'selected' : '' }}>Khách hàng</option>
                <option value="employee" {{ old('role', $user->role) == 'employee' ? 'selected' : '' }}>Nhân viên</option>
                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Quản trị viên</option>
            </select>
            @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <!-- Trạng thái -->
        <div class="mb-3">
            <label for="status" class="form-label">Trạng thái</label>
            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Hoạt động</option>
                <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Khóa</option>
            </select>
            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <!-- Ngân hàng -->
        <div class="mb-3">
            <label for="bank_name" class="form-label">Tên ngân hàng</label>
            <input type="text" class="form-control @error('bank_name') is-invalid @enderror" id="bank_name" name="bank_name" value="{{ old('bank_name', $user->bank_name) }}">
            @error('bank_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="user_bank_name" class="form-label">Tên tài khoản ngân hàng</label>
            <input type="text" class="form-control @error('user_bank_name') is-invalid @enderror" id="user_bank_name" name="user_bank_name" value="{{ old('user_bank_name', $user->user_bank_name) }}">
            @error('user_bank_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="bank_account" class="form-label">Số tài khoản ngân hàng</label>
            <input type="text" class="form-control @error('bank_account') is-invalid @enderror" id="bank_account" name="bank_account" value="{{ old('bank_account', $user->bank_account) }}">
            @error('bank_account')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <!-- Địa chỉ -->
        <div class="mb-3">
            <label for="address" class="form-label">Địa chỉ</label>
            <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', optional($user->address)->address) }}">
            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="address_name" class="form-label">Tên người nhận</label>
            <input type="text" class="form-control @error('address_name') is-invalid @enderror" id="address_name" name="address_name" value="{{ old('address_name', optional($user->address)->fullname) }}">
            @error('address_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="address_phone" class="form-label">SĐT người nhận</label>
            <input type="text" class="form-control @error('address_phone') is-invalid @enderror" id="address_phone" name="address_phone" value="{{ old('address_phone', optional($user->address)->phone_number) }}">
            @error('address_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <!-- Lý do khóa -->
        <div class="mb-3">
            <label for="reason_lock" class="form-label">Lý do khóa tài khoản</label>
            <textarea class="form-control @error('reason_lock') is-invalid @enderror" id="reason_lock" name="reason_lock">{{ old('reason_lock', $user->reason_lock) }}</textarea>
            @error('reason_lock')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <!-- Đổi mật khẩu -->
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="is_change_password" name="is_change_password" value="1" {{ old('is_change_password') ? 'checked' : '' }} onclick="togglePasswordFields()">
            <label class="form-check-label" for="is_change_password">Thay đổi mật khẩu</label>
        </div>

        <div id="passwordFields" style="display: {{ old('is_change_password') ? 'block' : 'none' }};">
            <div class="mb-3">
                <label class="form-label">Mật khẩu mới</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Xác nhận mật khẩu mới</label>
                <input type="password" name="password_confirmation" class="form-control">
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary ms-2">Hủy</a>
    </form>
</div>

<script>
    function togglePasswordFields() {
        const checkbox = document.getElementById('is_change_password');
        const passwordFields = document.getElementById('passwordFields');
        passwordFields.style.display = checkbox.checked ? 'block' : 'none';
    }
</script>
@endsection
