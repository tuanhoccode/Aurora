@extends('admin.layouts.app')

@section('title', 'Quản lý Người dùng')

@section('content')
    <div class="container-fluid px-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Quản lý Người dùng</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Người dùng</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Thêm người dùng
                </a>
            </div>
        </div>

        <!-- User List Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Danh sách người dùng</h5>
            </div>
            <div class="card-body">
                <!-- User Table -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                {{-- <th width="40">
                                    <input type="checkbox" class="form-check-input select-all">
                                </th> --}}
                                {{-- <th width="60">ID</th> --}}
                                <th>Tên người dùng</th>
                                <th>Email</th>
                                <th>Vai trò</th>
                                <th>Ngày đăng ký</th>
                                <th>Trạng thái</th>
                                <th width="100" class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    {{-- <td>
                                        <input type="checkbox" class="form-check-input select-user" name="selected_users[]"
                                            value="{{ $user->id }}">
                                    </td> --}}
                                    {{-- <td>{{ $user->id }}</td> --}}
                                    <td class="fw-medium">
                                        <a href="{{ route('admin.users.show', $user->id) }}"
                                            class="text-decoration-none text-primary">
                                            {{ $user->fullname }}
                                        </a>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @php
                                            $roleClass = match ($user->role) {
                                                'admin' => 'bg-primary text-white',
                                                'employee' => 'bg-warning text-dark',
                                                default => 'bg-secondary text-white',
                                            };
                                            $roleLabel = match ($user->role) {
                                                'admin' => 'Admin',
                                                'employee' => 'Employee',
                                                default => 'Customer',
                                            };
                                        @endphp
                                        <span class="badge {{ $roleClass }}">{{ $roleLabel }}</span>
                                    </td>
                                    <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        @if ($user->status == 'active')
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">InActive</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light" type="button"
                                                id="actionDropdown{{ $user->id }}" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="fas fa-ellipsis-h"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end"
                                                aria-labelledby="actionDropdown{{ $user->id }}">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.users.edit', $user->id) }}">
                                                        <i class="fas fa-edit me-2"></i>Chỉnh sửa
                                                    </a>
                                                </li>
                                                <li>
                                                    <form
                                                        action="{{ route('admin.users.destroy', ['user' => $user->id, 'page' => request('page')]) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Bạn có chắc muốn xóa người dùng này?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="page" value="{{ request('page') }}">
                                                        <button class="dropdown-item text-danger" type="submit">
                                                            <i class="fas fa-trash me-2"></i>Xóa
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Hiển thị {{ $users->firstItem() }} đến {{ $users->lastItem() }} trong tổng số {{ $users->total() }}
                        người dùng
                    </div>
                    <div>
                        {{ $users->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection