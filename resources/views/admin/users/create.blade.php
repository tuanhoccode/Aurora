@extends('admin.layouts.app')

@section('title', 'Thêm Người dùng')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Thêm Người dùng</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Người dùng</a></li>
                    <li class="breadcrumb-item active">Thêm</li>
                </ol>
            </nav>
        </div>
    </div>

    <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data" id="userForm">
        @csrf
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Thông tin người dùng</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Họ và tên</label>
                        <input type="text" name="fullname" class="form-control" value="{{ old('fullname') }}">
                        <div class="error-text text-danger small" id="fullname-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                        <div class="error-text text-danger small" id="email-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number') }}">
                        <div class="error-text text-danger small" id="phone-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Mật khẩu</label>
                        <input type="password" name="password" class="form-control" autocomplete="new-password">
                        <div class="error-text text-danger small" id="password-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Xác nhận mật khẩu</label>
                        <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
                        <div class="error-text text-danger small" id="confirm-password-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Giới tính</label>
                        <select name="gender" class="form-select">
                            <option value="">-- Chọn --</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Nam</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Nữ</option>
                            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Khác</option>
                        </select>
                        <div class="error-text text-danger small" id="gender-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Ngày sinh</label>
                        <input type="date" name="birthday" class="form-control" value="{{ old('birthday') }}">
                        <div class="error-text text-danger small" id="birthday-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Vai trò</label>
                        <select name="role" class="form-select">
                            <option value="customer" {{ old('role') == 'customer' ? 'selected' : '' }}>Khách hàng</option>
                            <option value="employee" {{ old('role') == 'employee' ? 'selected' : '' }}>Nhân viên</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Quản trị</option>
                        </select>
                        <div class="error-text text-danger small" id="role-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Trạng thái</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Khóa</option>
                        </select>
                        <div class="error-text text-danger small" id="status-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Ảnh đại diện</label>
                        <input type="file" name="avatar" class="form-control">
                        <div class="error-text text-danger small" id="avatar-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Tên ngân hàng</label>
                        <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name') }}">
                        <div class="error-text text-danger small" id="bank-name-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Tên tài khoản ngân hàng</label>
                        <input type="text" name="user_bank_name" class="form-control" value="{{ old('user_bank_name') }}">
                        <div class="error-text text-danger small" id="user-bank-name-error"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Số tài khoản ngân hàng</label>
                        <input type="text" name="bank_account" class="form-control" value="{{ old('bank_account') }}" autocomplete="off">
                        <div class="error-text text-danger small" id="bank-account-error"></div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white text-end">
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Hủy</a>
                <button type="submit" class="btn btn-primary">Lưu người dùng</button>
            </div>
        </div>
    </form>
</div>

<script>
document.getElementById('userForm').addEventListener('submit', function(e) {
    e.preventDefault();

    // Reset lỗi
    document.querySelectorAll('.error-text').forEach(error => error.textContent = '');

    let isValid = true;

    // Kiểm tra Họ và tên
    let fullname = document.querySelector('input[name="fullname"]').value;
    if (!fullname.trim()) {
        document.getElementById('fullname-error').textContent = 'Họ và tên không được để trống!';
        isValid = false;
    }

    // Kiểm tra Email
    let email = document.querySelector('input[name="email"]').value;
    if (!email.trim()) {
        document.getElementById('email-error').textContent = 'Email không được để trống!';
        isValid = false;
    } else if (!email.match(/^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/)) {
        document.getElementById('email-error').textContent = 'Email không hợp lệ!';
        isValid = false;
    }

    // Kiểm tra Số điện thoại
    let phone = document.querySelector('input[name="phone_number"]').value;
    if (!phone.trim()) {
        document.getElementById('phone-error').textContent = 'Số điện thoại không được để trống!';
        isValid = false;
    } else if (!/^\d{10}$/.test(phone)) {
        document.getElementById('phone-error').textContent = 'Số điện thoại phải là 10 chữ số!';
        isValid = false;
    }

    // Kiểm tra Mật khẩu
    let password = document.querySelector('input[name="password"]').value;
    if (!password.trim()) {
        document.getElementById('password-error').textContent = 'Mật khẩu không được để trống!';
        isValid = false;
    } else if (password.length < 6) {
        document.getElementById('password-error').textContent = 'Mật khẩu phải có ít nhất 6 ký tự!';
        isValid = false;
    }

    // Kiểm tra Xác nhận mật khẩu
    let confirmPassword = document.querySelector('input[name="password_confirmation"]').value;
    if (!confirmPassword.trim()) {
        document.getElementById('confirm-password-error').textContent = 'Xác nhận mật khẩu không được để trống!';
        isValid = false;
    } else if (password !== confirmPassword) {
        document.getElementById('confirm-password-error').textContent = 'Mật khẩu và xác nhận mật khẩu không khớp!';
        isValid = false;
    }

    // Kiểm tra Giới tính
    let gender = document.querySelector('select[name="gender"]').value;
    if (!gender) {
        document.getElementById('gender-error').textContent = 'Vui lòng chọn giới tính!';
        isValid = false;
    }

    // Kiểm tra Ngày sinh
    let birthday = document.querySelector('input[name="birthday"]').value;
    if (!birthday) {
        document.getElementById('birthday-error').textContent = 'Ngày sinh không được để trống!';
        isValid = false;
    }

    // Kiểm tra Vai trò
    let role = document.querySelector('select[name="role"]').value;
    if (!role) {
        document.getElementById('role-error').textContent = 'Vui lòng chọn vai trò!';
        isValid = false;
    }

    // Kiểm tra Trạng thái
    let status = document.querySelector('select[name="status"]').value;
    if (!status) {
        document.getElementById('status-error').textContent = 'Vui lòng chọn trạng thái!';
        isValid = false;
    }

    // Kiểm tra Tên ngân hàng
    let bankName = document.querySelector('input[name="bank_name"]').value;
    if (!bankName.trim()) {
        document.getElementById('bank-name-error').textContent = 'Tên ngân hàng không được để trống!';
        isValid = false;
    }

    // Kiểm tra Tên tài khoản ngân hàng
    let userBankName = document.querySelector('input[name="user_bank_name"]').value;
    if (!userBankName.trim()) {
        document.getElementById('user-bank-name-error').textContent = 'Tên tài khoản ngân hàng không được để trống!';
        isValid = false;
    }

    // Kiểm tra Số tài khoản ngân hàng
    let bankAccount = document.querySelector('input[name="bank_account"]').value;
    if (!bankAccount.trim()) {
        document.getElementById('bank-account-error').textContent = 'Số tài khoản ngân hàng không được để trống!';
        isValid = false;
    } else if (!/^\d{10,}$/.test(bankAccount)) {
        document.getElementById('bank-account-error').textContent = 'Số tài khoản ngân hàng phải là số và ít nhất 10 chữ số!';
        isValid = false;
    }

    // Nếu tất cả hợp lệ, gửi form
    if (isValid) {
        this.submit();
    }
});
</script>
@endsection