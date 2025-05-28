@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 fw-bold">Danh sách thương hiệu</h1>
            <div>
                <a href="{{ route('admin.brands.create') }}" class="btn btn-primary shadow-sm rounded">
                    <i class="bi bi-plus-circle"></i> Thêm thương hiệu mới
                </a>
                <a href="{{ route('admin.brands.trash') }}" class="btn btn-danger shadow-sm rounded ms-2">
                    <i class="bi bi-trash3-fill"></i> Thùng rác
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success shadow-sm rounded">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger shadow-sm rounded">{{ session('error') }}</div>
        @endif

        <div class="card shadow-sm rounded">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <form action="{{ route('admin.brands.index') }}" method="GET" class="d-flex gap-2">
                            <input type="text" 
                                   name="search" 
                                   class="form-control" 
                                   placeholder="Tìm kiếm thương hiệu..." 
                                   value="{{ request('search') }}">
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">Tất cả trạng thái</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Đang hoạt động</option>
                                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Không hoạt động</option>
                            </select>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i>
                            </button>
                        </form>
                    </div>
                </div>

                @if ($brands->count())
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 60px">ID</th>
                                    <th>Tên thương hiệu</th>
                                    <th style="width: 120px">Logo</th>
                                    <th style="width: 120px">Trạng thái</th>
                                    <th style="width: 120px">Ngày tạo</th>
                                    <th style="width: 200px">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($brands as $brand)
                                    <tr>
                                        <td>{{ $brand->id }}</td>
                                        <td>{{ $brand->name }}</td>
                                        <td class="text-center">
                                            @if ($brand->logo)
                                                @php
                                                    $logoPath = 'storage/' . $brand->logo;
                                                    $logoExists = file_exists(public_path($logoPath));
                                                @endphp
                                                @if ($logoExists)
                                                    <img src="{{ asset($logoPath) }}" 
                                                         alt="{{ $brand->name }}" 
                                                         class="img-thumbnail" 
                                                         style="max-width:80px; max-height:80px; object-fit:contain;">
                                                @else
                                                    <div class="text-muted small">
                                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                                        Logo không tồn tại
                                                    </div>
                                                @endif
                                            @else
                                                <div class="text-muted small">
                                                    <i class="bi bi-image me-1"></i>
                                                    Không có logo
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $brand->is_active ? 'bg-success' : 'bg-danger' }}">
                                                {{ $brand->is_active ? 'Đang hoạt động' : 'Không hoạt động' }}
                                            </span>
                                        </td>
                                        <td class="text-center">{{ $brand->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.brands.show', $brand->id) }}"
                                                    class="btn btn-info btn-sm" title="Xem chi tiết">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.brands.edit', $brand->id) }}"
                                                    class="btn btn-warning btn-sm" title="Chỉnh sửa">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-sm" 
                                                    onclick="confirmDelete('{{ $brand->id }}', '{{ $brand->name }}')"
                                                    title="Xóa">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        {{ $brands->links() }}
                    </div>
                @else
                    <div class="alert alert-info mb-0">
                        Không tìm thấy thương hiệu nào.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Xác nhận xóa -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Xác nhận xóa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Bạn có chắc chắn muốn xóa thương hiệu "<span id="brandName"></span>"?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Xóa</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function confirmDelete(id, name) {
            document.getElementById('brandName').textContent = name;
            document.getElementById('deleteForm').action = `/admin/brands/${id}`;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }
    </script>
    @endpush
@endsection
