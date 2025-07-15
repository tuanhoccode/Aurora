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

    <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data" autocomplete="off" novalidate>

        @csrf
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Thông tin người dùng</h5>
            </div>
            <div class="card-body row g-3">
                {{-- Họ và tên --}}
                <div class="col-md-6">
                    <label class="form-label">Họ và tên</label>
                    <input type="text" name="fullname" value="{{ old('fullname') }}" class="form-control @error('fullname') is-invalid @enderror">
                    @error('fullname')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Số điện thoại --}}
                <div class="col-md-6">
                    <label class="form-label">Số điện thoại</label>
                    <input type="text" name="phone_number" value="{{ old('phone_number') }}" class="form-control @error('phone_number') is-invalid @enderror">
                    @error('phone_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Ngày sinh --}}
                <div class="col-md-6">
                    <label class="form-label">Ngày sinh</label>
                    <input type="date" name="birthday" value="{{ old('birthday') }}" class="form-control @error('birthday') is-invalid @enderror">
                    @error('birthday')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Mật khẩu --}}
                <div class="col-md-6">
                    <label class="form-label">Mật khẩu</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Xác nhận mật khẩu --}}
                <div class="col-md-6">
                    <label class="form-label">Xác nhận mật khẩu</label>
                    <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
                </div>

                {{-- Giới tính --}}
                <div class="col-md-6">
                    <label class="form-label">Giới tính</label>
                    <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                        <option value="">-- Chọn --</option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Nam</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Nữ</option>
                        <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Khác</option>
                    </select>
                    @error('gender')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Vai trò --}}
                <div class="col-md-6">
                    <label class="form-label">Vai trò</label>
                    <select name="role" class="form-select @error('role') is-invalid @enderror">
                        <option value="customer" {{ old('role') == 'customer' ? 'selected' : '' }}>Khách hàng</option>
                        <option value="employee" {{ old('role') == 'employee' ? 'selected' : '' }}>Nhân viên</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Quản trị</option>
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Trạng thái --}}
                <div class="col-md-6">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror">
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Khóa</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Tên ngân hàng --}}
                <div class="col-md-6">
                    <label class="form-label">Tên ngân hàng</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name') }}" class="form-control @error('bank_name') is-invalid @enderror">
                    @error('bank_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Tên tài khoản ngân hàng --}}
                <div class="col-md-6">
                    <label class="form-label">Tên tài khoản ngân hàng</label>
                    <input type="text" name="user_bank_name" value="{{ old('user_bank_name') }}" class="form-control @error('user_bank_name') is-invalid @enderror">
                    @error('user_bank_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Số tài khoản ngân hàng --}}
                <div class="col-md-6">
                    <label class="form-label">Số tài khoản ngân hàng</label>
                    <input type="number" name="bank_account" value="{{ old('bank_account') }}" class="form-control @error('bank_account') is-invalid @enderror">
                    @error('bank_account')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Địa chỉ --}}
                <div class="col-md-12">
                    <label class="form-label">Địa chỉ</label>
                    <input type="text" name="address" value="{{ old('address') }}" class="form-control @error('address') is-invalid @enderror">
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Tên người nhận --}}
                <div class="col-md-6">
                    <label class="form-label">Tên người nhận</label>
                    <input type="text" name="address_name" value="{{ old('address_name') }}" class="form-control @error('address_name') is-invalid @enderror">
                    @error('address_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- SĐT người nhận --}}
                <div class="col-md-6">
                    <label class="form-label">SĐT người nhận</label>
                    <input type="text" name="address_phone" value="{{ old('address_phone') }}" class="form-control @error('address_phone') is-invalid @enderror">
                    @error('address_phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Ảnh đại diện --}}
                <div class="col-md-6">
                    <label class="form-label">Ảnh đại diện</label>
                    <input type="file" name="avatar" class="form-control @error('avatar') is-invalid @enderror" autocomplete="off">
                    @error('avatar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="card-footer bg-white text-end">
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Hủy</a>
                <button type="submit" class="btn btn-primary">Lưu người dùng</button>
            </div>
        </div>
    </form>
</div>
@endsection
