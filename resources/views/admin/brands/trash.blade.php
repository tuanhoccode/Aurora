@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold text-gray-800">Thùng rác thương hiệu</h1>
            <p class="text-muted mt-1">Quản lý các thương hiệu đã bị xóa</p>
        </div>
        <div>
            <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-primary shadow-sm rounded-pill px-4">
                <i class="bi bi-arrow-left me-1"></i> Quay lại
            </a>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm rounded" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Main Card --}}
    <div class="card shadow-sm rounded-3 border-0">
        <div class="card-body p-4">
            {{-- Search and Filter Form --}}
            <form action="{{ route('admin.brands.trash') }}" method="GET" class="d-flex gap-2">
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

            {{-- Brands Table --}}
            @if ($brands->count())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0" style="width: 60px">
                                    <a href="{{ route('admin.brands.trash', array_merge(request()->query(), ['sort_by' => 'id', 'sort_dir' => ($sortBy == 'id' && $sortDir == 'asc') ? 'desc' : 'asc'])) }}"
                                       class="text-decoration-none text-dark d-flex align-items-center">
                                        ID
                                        @if ($sortBy == 'id')
                                            <i class="bi bi-arrow-{{ $sortDir == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="border-0">
                                    <a href="{{ route('admin.brands.trash', array_merge(request()->query(), ['sort_by' => 'name', 'sort_dir' => ($sortBy == 'name' && $sortDir == 'asc') ? 'desc' : 'asc'])) }}"
                                       class="text-decoration-none text-dark d-flex align-items-center">
                                        Tên thương hiệu
                                        @if ($sortBy == 'name')
                                            <i class="bi bi-arrow-{{ $sortDir == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="border-0" style="width: 120px">Logo</th>
                                <th class="border-0" style="width: 120px">
                                    <a href="{{ route('admin.brands.trash', array_merge(request()->query(), ['sort_by' => 'is_active', 'sort_dir' => ($sortBy == 'is_active' && $sortDir == 'asc') ? 'desc' : 'asc'])) }}"
                                       class="text-decoration-none text-dark d-flex align-items-center">
                                        Trạng thái
                                        @if ($sortBy == 'is_active')
                                            <i class="bi bi-arrow-{{ $sortDir == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="border-0" style="width: 150px">
                                    <a href="{{ route('admin.brands.trash', array_merge(request()->query(), ['sort_by' => 'deleted_at', 'sort_dir' => ($sortBy == 'deleted_at' && $sortDir == 'asc') ? 'desc' : 'asc'])) }}"
                                       class="text-decoration-none text-dark d-flex align-items-center">
                                        Ngày xóa
                                        @if ($sortBy == 'deleted_at')
                                            <i class="bi bi-arrow-{{ $sortDir == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="border-0 text-end" style="width: 200px">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($brands as $brand)
                                <tr class="position-relative">
                                    <td class="text-muted">{{ $brand->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="fw-medium">{{ $brand->name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if ($brand->logo)
                                            @php
                                                $logoPath = 'storage/' . $brand->logo;
                                                $logoExists = file_exists(public_path($logoPath));
                                            @endphp
                                            @if ($logoExists)
                                                <img src="{{ asset($logoPath) }}" 
                                                     alt="{{ $brand->name }}" 
                                                     class="img-thumbnail rounded-3" 
                                                     style="max-width:80px; max-height:80px; object-fit:contain; transition: transform 0.2s;"
                                                     onmouseover="this.style.transform='scale(1.1)'"
                                                     onmouseout="this.style.transform='scale(1)'">
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
                                        <span class="badge rounded-pill {{ $brand->is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} px-3 py-2">
                                            <i class="bi bi-circle-fill me-1 small"></i>
                                            {{ $brand->is_active ? 'Đang hoạt động' : 'Không hoạt động' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="text-muted" data-bs-toggle="tooltip" title="{{ $brand->deleted_at->format('H:i:s d/m/Y') }}">
                                            {{ $brand->deleted_at->format('d/m/Y') }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-end gap-2">
                                            <button type="button" 
                                                    class="btn btn-success btn-sm rounded-pill px-3" 
                                                    onclick="confirmRestore('{{ $brand->id }}', '{{ $brand->name }}')"
                                                    data-bs-toggle="tooltip" 
                                                    title="Khôi phục">
                                                <i class="bi bi-arrow-counterclockwise"></i>
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-danger btn-sm rounded-pill px-3" 
                                                    onclick="confirmForceDelete('{{ $brand->id }}', '{{ $brand->name }}')"
                                                    data-bs-toggle="tooltip" 
                                                    title="Xóa vĩnh viễn">
                                                <i class="bi bi-x-octagon"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination and Per Page --}}
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="d-flex align-items-center">
                        <label for="perPage" class="text-muted me-2">Hiển thị:</label>
                        <select name="per_page" id="perPage" class="form-select form-select-sm" style="width: auto" onchange="this.form.submit()">
                            @foreach([10, 25, 50, 100] as $value)
                                <option value="{{ $value }}" {{ request('per_page', 10) == $value ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                        <span class="text-muted ms-2">mục</span>
                    </div>
                    <div>
                        {{ $brands->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-trash display-1 text-muted"></i>
                    <p class="text-muted mt-3">Không có thương hiệu nào trong thùng rác.</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Restore Confirmation Modal --}}
<div class="modal fade" id="restoreModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Xác nhận khôi phục</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="bi bi-arrow-counterclockwise text-success display-4"></i>
                </div>
                <p class="text-center mb-0">
                    Bạn có chắc chắn muốn khôi phục thương hiệu "<span id="restoreBrandName" class="fw-bold"></span>"?
                </p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                <form id="restoreForm" method="POST" style="display: inline;">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-success rounded-pill px-4">Khôi phục</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Force Delete Confirmation Modal --}}
<div class="modal fade" id="forceDeleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Xác nhận xóa vĩnh viễn</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="bi bi-exclamation-triangle text-danger display-4"></i>
                </div>
                <div class="alert alert-danger mb-0">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Cảnh báo: Hành động này không thể hoàn tác!
                </div>
                <p class="text-center mt-3 mb-0">
                    Bạn có chắc chắn muốn xóa vĩnh viễn thương hiệu "<span id="forceDeleteBrandName" class="fw-bold"></span>"?
                </p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                <form id="forceDeleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger rounded-pill px-4">Xóa vĩnh viễn</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })

    function confirmRestore(id, name) {
        document.getElementById('restoreBrandName').textContent = name;
        document.getElementById('restoreForm').action = `/admin/brands/${id}/restore`;
        new bootstrap.Modal(document.getElementById('restoreModal')).show();
    }

    function confirmForceDelete(id, name) {
        document.getElementById('forceDeleteBrandName').textContent = name;
        document.getElementById('forceDeleteForm').action = `/admin/brands/force-delete/${id}`;
        new bootstrap.Modal(document.getElementById('forceDeleteModal')).show();
    }
</script>
@endpush
@endsection