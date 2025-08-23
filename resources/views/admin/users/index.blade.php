@extends('admin.layouts.app')

@section('title', 'Quản lý Người dùng')

@section('content')
    <div class="container-fluid px-4">
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

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Danh sách người dùng</h5>
                <form action="{{ route('admin.users.index') }}" method="GET" class="d-flex w-100 ms-3"
                    style="max-width: 500px;">
                    <div class="input-group w-100">
                        <input type="text" name="search" class="form-control" placeholder="Tìm kiếm người dùng..."
                            value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
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
                                        <span class="badge {{ $roleClass }} role-editable" data-id="{{ $user->id }}"
                                            data-role="{{ $user->role }}" style="cursor: pointer;">
                                            {{ $roleLabel }}
                                        </span>
                                    </td>
                                    <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <span
                                            class="badge {{ $user->status === 'active' ? 'bg-success' : 'bg-danger' }} status-editable"
                                            data-id="{{ $user->id }}" data-status="{{ $user->status }}"
                                            style="cursor: pointer;">
                                            {{ $user->status === 'active' ? 'Active' : 'Inactive' }}
                                        </span>
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
                                                    {{-- <form
                                                        action="{{ route('admin.users.destroy', ['user' => $user->id, 'page' => request('page')]) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Bạn có chắc muốn xóa người dùng này?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="page" value="{{ request('page') }}">
                                                        <button class="dropdown-item text-danger" type="submit">
                                                            <i class="fas fa-trash me-2"></i>Xóa
                                                        </button>
                                                    </form> --}}
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

@push('scripts')
    <!-- Thêm toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        $(document).ready(function () {
            // --- Toastr thông báo từ session ---
            @if (session('success'))
                toastr.success("{{ session('success') }}");
            @endif
            @if (session('error'))
                toastr.error("{{ session('error') }}");
            @endif

            // --- Status Editable ---
            $(document).on('click', '.status-editable', function () {
                let $span = $(this);
                let oldStatus = $span.data('status');
                let userId = $span.data('id');

                let select = `
                        <select class="form-select form-select-sm status-select" 
                                data-id="${userId}"
                                data-old-status="${oldStatus}"
                                style="min-width:100px;">
                            <option value="active" ${oldStatus === 'active' ? 'selected' : ''}>Active</option>
                            <option value="inactive" ${oldStatus === 'inactive' ? 'selected' : ''}>Inactive</option>
                        </select>
                    `;
                $span.replaceWith(select);
                select = $(`select[data-id=${userId}]`);
                select.focus();
            });

            $(document).on('change', '.status-select', function () {
                let $select = $(this);
                let newStatus = $select.val();
                let oldStatus = $select.data('old-status');
                let userId = $select.data('id');

                $.ajax({
                    url: `/admin/users/${userId}/change-status`,
                    method: 'PATCH',
                    data: { status: newStatus, _token: '{{ csrf_token() }}' },
                    success: function () {
                        let badgeClass = newStatus === 'active' ? 'bg-success' : 'bg-danger';
                        let badgeText = newStatus === 'active' ? 'Active' : 'Inactive';
                        let span = `
                                <span class="badge ${badgeClass} status-editable"
                                      data-id="${userId}"
                                      data-status="${newStatus}"
                                      style="cursor: pointer;">
                                    ${badgeText}
                                </span>
                            `;
                        $select.replaceWith(span);
                        toastr.success('Cập nhật trạng thái thành công.');
                    },
                    error: function () {
                        toastr.error('Cập nhật trạng thái thất bại.');
                        let badgeClass = oldStatus === 'active' ? 'bg-success' : 'bg-danger';
                        let badgeText = oldStatus === 'active' ? 'Active' : 'Inactive';
                        let span = `
                                <span class="badge ${badgeClass} status-editable"
                                      data-id="${userId}"
                                      data-status="${oldStatus}"
                                      style="cursor: pointer;">
                                    ${badgeText}
                                </span>
                            `;
                        $select.replaceWith(span);
                    }
                });
            });

            // --- Role Editable ---
            $(document).on('click', '.role-editable', function () {
                let $span = $(this);
                let oldRole = $span.data('role');
                let userId = $span.data('id');

                let select = `
                        <select class="form-select form-select-sm role-select" 
                                data-id="${userId}"
                                data-old-role="${oldRole}"
                                style="min-width:120px;">
                            <option value="customer" ${oldRole === 'customer' ? 'selected' : ''}>Customer</option>
                            <option value="employee" ${oldRole === 'employee' ? 'selected' : ''}>Employee</option>
                            <option value="admin" ${oldRole === 'admin' ? 'selected' : ''}>Admin</option>
                        </select>
                    `;
                $span.replaceWith(select);
                select = $(`select[data-id=${userId}]`);
                select.focus();
            });

            $(document).on('change', '.role-select', function () {
                let $select = $(this);
                let newRole = $select.val();
                let oldRole = $select.data('old-role');
                let userId = $select.data('id');

                $.ajax({
                    url: `/admin/users/${userId}/change-role`,
                    method: 'PATCH',
                    data: { role: newRole, _token: '{{ csrf_token() }}' },
                    success: function () {
                        let roleClass = newRole === 'admin' ? 'bg-primary text-white' :
                            newRole === 'employee' ? 'bg-warning text-dark' :
                                'bg-secondary text-white';
                        let roleLabel = newRole.charAt(0).toUpperCase() + newRole.slice(1);
                        let span = `
                                <span class="badge ${roleClass} role-editable"
                                      data-id="${userId}"
                                      data-role="${newRole}"
                                      style="cursor: pointer;">
                                    ${roleLabel}
                                </span>
                            `;
                        $select.replaceWith(span);
                        toastr.success('Cập nhật vai trò thành công.');
                    },
                    error: function () {
                        toastr.error('Cập nhật vai trò thất bại.');
                        let roleClass = oldRole === 'admin' ? 'bg-primary text-white' :
                            oldRole === 'employee' ? 'bg-warning text-dark' :
                                'bg-secondary text-white';
                        let roleLabel = oldRole.charAt(0).toUpperCase() + oldRole.slice(1);
                        let span = `
                                <span class="badge ${roleClass} role-editable"
                                      data-id="${userId}"
                                      data-role="${oldRole}"
                                      style="cursor: pointer;">
                                    ${roleLabel}
                                </span>
                            `;
                        $select.replaceWith(span);
                    }
                });
            });
        });
    </script>
@endpush