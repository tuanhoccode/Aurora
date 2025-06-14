@extends('admin.layouts.app')

@section('title', 'Chi tiết Người dùng')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Chi tiết Người dùng</h1>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h5 class="mb-4">Thông tin chi tiết</h5>
            <table class="table table-bordered">
                <tbody>
                    <tr><th width="200">ID</th><td>{{ $user->id }}</td></tr>
                    <tr><th>Họ và tên</th><td>{{ $user->fullname }}</td></tr>
                    <tr><th>Email</th><td>{{ $user->email }}</td></tr>
                    <tr><th>Số điện thoại</th><td>{{ $user->phone_number }}</td></tr>
                    <tr><th>Ảnh đại diện</th>
                        <td>
                            <img src="{{ $user->avatar ? asset($user->avatar) : 'https://via.placeholder.com/80' }}" 
                                 alt="Avatar" width="80" class="rounded-circle border">
                        </td>
                    </tr>
                    <tr><th>Giới tính</th>
                        <td>
                            @php
                                $gender = match($user->gender) {
                                    'male' => 'Nam',
                                    'female' => 'Nữ',
                                    'other' => 'Khác',
                                    default => 'Không xác định',
                                };
                            @endphp
                            {{ $gender }}
                        </td>
                    </tr>
                    <tr><th>Ngày sinh</th>
                        <td>{{ $user->birthday ? \Carbon\Carbon::parse($user->birthday)->format('d/m/Y') : 'Chưa có' }}</td>
                    </tr>
                    <tr>
                        <th>Vai trò</th>
                        <td>
                            @php
                                $roleClass = match ($user->role) {
                                    'admin' => 'badge bg-primary',
                                    'employee' => 'badge bg-warning text-dark',
                                    default => 'badge bg-secondary',
                                };
                                $roleLabel = match ($user->role) {
                                    'admin' => 'Admin',
                                    'employee' => 'Employee',
                                    default => 'Customer',
                                };
                            @endphp
                            <span class="{{ $roleClass }}">{{ $roleLabel }}</span>
                        </td>
                    </tr>
                    <tr><th>Trạng thái</th>
                        <td>
                            @if ($user->status == 'active')
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                    </tr>
                    <tr><th>Đã đổi mật khẩu?</th>
                        <td>
                            {{ $user->is_change_password ? 'Đã đổi' : 'Chưa đổi' }}
                        </td>
                    </tr>
                    <tr><th>Lý do khóa (nếu có)</th>
                        <td>{{ $user->reason_lock ?? 'Không có' }}</td>
                    </tr>
                    <tr><th>Ngân hàng</th>
                        <td>
                            @if ($user->bank_name)
                                {{ $user->bank_name }} <br>
                                <small>Chủ tài khoản: {{ $user->user_bank_name }}</small><br>
                                <small>Số tài khoản: {{ $user->bank_account }}</small>
                            @else
                                <span class="text-muted">Chưa cập nhật</span>
                            @endif
                        </td>
                    </tr>
                    <tr><th>Ngày đăng ký</th>
                        <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr><th>Ngày cập nhật</th>
                        <td>{{ $user->updated_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="mt-4">
                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary me-2">
                    <i class="fas fa-edit me-1"></i> Chỉnh sửa
                </a>
                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Bạn có chắc muốn xóa người dùng này?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger" type="submit">
                        <i class="fas fa-trash me-1"></i> Xóa
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection