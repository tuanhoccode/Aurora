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

        <!-- Phone Number -->
        <div class="mb-3">
            <label for="phone_number" class="form-label">Số điện thoại</label>
            <input type="text" class="form-control" id="phone_number" name="phone_number" 
                value="{{ old('phone_number', $user->phone_number) }}" required>
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" 
                value="{{ old('email', $user->email) }}">
        </div>

        <!-- Fullname -->
        <div class="mb-3">
            <label for="fullname" class="form-label">Họ và tên</label>
            <input type="text" class="form-control" id="fullname" name="fullname" 
                value="{{ old('fullname', $user->fullname) }}">
        </div>

        <!-- Avatar (file upload + preview current) -->
        <div class="mb-3">
            <label for="avatar" class="form-label">Ảnh đại diện</label>
            <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
            @if($user->avatar)
                <img src="{{ asset($user->avatar) }}" alt="Avatar" style="max-width:150px; margin-top:10px;">
            @endif
        </div>

        <!-- Gender -->
        <div class="mb-3">
            <label for="gender" class="form-label">Giới tính</label>
            <select class="form-select" id="gender" name="gender">
                <option value="" {{ old('gender', $user->gender) == null ? 'selected' : '' }}>Chọn giới tính</option>
                <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Nam</option>
                <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Nữ</option>
                <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Khác</option>
            </select>
        </div>

        <!-- Birthday -->
        <div class="mb-3">
            <label for="birthday" class="form-label">Ngày sinh</label>
            <input type="date" class="form-control" id="birthday" name="birthday" 
                value="{{ old('birthday', $user->birthday ? $user->birthday->format('Y-m-d') : '') }}">
        </div>

        <!-- Role -->
        <div class="mb-3">
            <label for="role" class="form-label">Vai trò</label>
            <select class="form-select" id="role" name="role" required>
                <option value="customer" {{ old('role', $user->role) == 'customer' ? 'selected' : '' }}>Customer</option>
                <option value="employee" {{ old('role', $user->role) == 'employee' ? 'selected' : '' }}>Employee</option>
                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
        </div>

        <!-- Status -->
        <div class="mb-3">
            <label for="status" class="form-label">Trạng thái</label>
            <select class="form-select" id="status" name="status" required>
                <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <!-- Bank Name -->
        <div class="mb-3">
            <label for="bank_name" class="form-label">Tên ngân hàng</label>
            <input type="text" class="form-control" id="bank_name" name="bank_name" 
                value="{{ old('bank_name', $user->bank_name) }}">
        </div>

        <!-- User Bank Name -->
        <div class="mb-3">
            <label for="user_bank_name" class="form-label">Tên người dùng ngân hàng</label>
            <input type="text" class="form-control" id="user_bank_name" name="user_bank_name" 
                value="{{ old('user_bank_name', $user->user_bank_name) }}">
        </div>

        <!-- Bank Account -->
        <div class="mb-3">
            <label for="bank_account" class="form-label">Số tài khoản ngân hàng</label>
            <input type="text" class="form-control" id="bank_account" name="bank_account" 
                value="{{ old('bank_account', $user->bank_account) }}">
        </div>

        <!-- Reason Lock -->
        <div class="mb-3">
            <label for="reason_lock" class="form-label">Lý do khóa tài khoản</label>
            <textarea class="form-control" id="reason_lock" name="reason_lock" rows="3">{{ old('reason_lock', $user->reason_lock) }}</textarea>
        </div>

        <!-- Is Change Password (checkbox) -->
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="is_change_password" name="is_change_password" value="1" {{ old('is_change_password', $user->is_change_password) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_change_password">Đã thay đổi mật khẩu</label>
        </div>

        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary ms-2">Hủy</a>
    </form>
</div>
@endsection