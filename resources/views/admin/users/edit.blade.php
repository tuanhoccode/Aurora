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

        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Vai trò -->
            <div class="mb-3">
                <label for="role" class="form-label">Vai trò</label>
                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                    <option value="customer" {{ old('role', $user->role) == 'customer' ? 'selected' : '' }}>Khách hàng
                    </option>
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

            <!-- Lý do khóa (ẩn/hiện theo trạng thái) -->
            <div class="mb-3" id="reasonLockContainer"
                style="display: {{ old('status', $user->status) == 'inactive' ? 'block' : 'none' }};">
                <label for="reason_lock" class="form-label">Lý do khóa tài khoản</label>
                <textarea class="form-control @error('reason_lock') is-invalid @enderror" id="reason_lock"
                    name="reason_lock">{{ old('reason_lock', $user->status == 'inactive' ? $user->reason_lock : '') }}</textarea>
                @error('reason_lock')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <!-- Đổi mật khẩu -->
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="is_change_password" name="is_change_password" value="1"
                    {{ old('is_change_password') ? 'checked' : '' }} onclick="togglePasswordFields()">
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

        document.addEventListener('DOMContentLoaded', function () {
            const statusSelect = document.getElementById('status');
            const reasonContainer = document.getElementById('reasonLockContainer');
            const reasonInput = document.getElementById('reason_lock');

            statusSelect.addEventListener('change', function () {
                if (this.value === 'inactive') {
                    reasonContainer.style.display = 'block';
                } else {
                    reasonContainer.style.display = 'none';
                    reasonInput.value = ''; // Xóa nội dung nếu chuyển sang trạng thái active
                }
            });
        });
    </script>
@endsection