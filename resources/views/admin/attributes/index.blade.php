@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        {{-- Header Section --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 fw-bold text-gray-800">Danh sách thuộc tính</h1>
                <p class="text-muted mt-1">Quản lý thông tin các thuộc tính sản phẩm trong hệ thống</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.attributes.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4">
                    <i class="bi bi-plus-circle me-1"></i> Thêm mới
                </a>
                <a href="{{ route('admin.attributes.trashed') }}" class="btn btn-outline-danger shadow-sm rounded-pill px-4">
                    <i class="bi bi-trash3 me-1"></i> Thùng rác
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
                <div class="row mb-4">
                    <div class="col-md-6">
                        <form action="{{ route('admin.attributes.index') }}" method="GET" class="d-flex gap-2">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control"
                                    placeholder="Tìm kiếm thuộc tính..." value="{{ request('search') }}">
                                <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                            </div>
                            <select name="status" class="form-select" style="width: auto" onchange="this.form.submit()">
                                <option value="">Tất cả trạng thái</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Đang hoạt động
                                </option>
                                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Không hoạt động
                                </option>
                            </select>
                        </form>
                    </div>
                </div>

                @if ($attributes->count())
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0">
                                        <a href="{{ route('admin.attributes.index', array_merge(request()->query(), ['sort_by' => 'name', 'sort_dir' => $sortBy == 'name' && $sortDir == 'asc' ? 'desc' : 'asc'])) }}"
                                            class="text-decoration-none text-dark d-flex align-items-center">
                                            Tên thuộc tính
                                            @if ($sortBy == 'name')
                                                <i class="bi bi-arrow-{{ $sortDir == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="border-0" style="width: 150px">
                                        <a href="{{ route('admin.attributes.index', array_merge(request()->query(), ['sort_by' => 'is_variant', 'sort_dir' => $sortBy == 'is_variant' && $sortDir == 'asc' ? 'desc' : 'asc'])) }}"
                                            class="text-decoration-none text-dark d-flex align-items-center">
                                            Biến thể
                                            @if ($sortBy == 'is_variant')
                                                <i class="bi bi-arrow-{{ $sortDir == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="border-0" style="width: 120px">
                                        <a href="{{ route('admin.attributes.index', array_merge(request()->query(), ['sort_by' => 'attribute_values_count', 'sort_dir' => $sortBy == 'attribute_values_count' && $sortDir == 'asc' ? 'desc' : 'asc'])) }}"
                                            class="text-decoration-none text-dark d-flex align-items-center">
                                            Số lượng giá trị
                                            @if ($sortBy == 'attribute_values_count')
                                                <i class="bi bi-arrow-{{ $sortDir == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="border-0" style="width: 120px">
                                        <a href="{{ route('admin.attributes.index', array_merge(request()->query(), ['sort_by' => 'is_active', 'sort_dir' => $sortBy == 'is_active' && $sortDir == 'asc' ? 'desc' : 'asc'])) }}"
                                            class="text-decoration-none text-dark d-flex align-items-center">
                                            Trạng thái
                                            @if ($sortBy == 'is_active')
                                                <i class="bi bi-arrow-{{ $sortDir == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="border-0" style="width: 120px">
                                        <a href="{{ route('admin.attributes.index', array_merge(request()->query(), ['sort_by' => 'created_at', 'sort_dir' => $sortBy == 'created_at' && $sortDir == 'asc' ? 'desc' : 'asc'])) }}"
                                            class="text-decoration-none text-dark d-flex align-items-center">
                                            Ngày tạo
                                            @if ($sortBy == 'created_at')
                                                <i class="bi bi-arrow-{{ $sortDir == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="border-0 text-end" style="width: 100px">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($attributes as $attribute)
                                    <tr class="position-relative">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="fw-medium">{{ $attribute->name }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge rounded-pill {{ $attribute->is_variant ? 'bg-info-subtle text-info' : 'bg-secondary-subtle text-secondary' }} px-3 py-2">
                                                <i class="bi bi-circle-fill me-1 small"></i>
                                                {{ $attribute->is_variant ? 'Là biến thể' : 'Không phải biến thể' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill bg-primary-subtle text-primary px-3 py-2">
                                                {{ $attribute->attribute_values_count }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge rounded-pill {{ $attribute->is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} px-3 py-2">
                                                <i class="bi bi-circle-fill me-1 small"></i>
                                                {{ $attribute->is_active ? 'Đang hoạt động' : 'Không hoạt động' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="text-muted" data-bs-toggle="tooltip"
                                                title="{{ $attribute->created_at->format('H:i:s d/m/Y') }}">
                                                {{ $attribute->created_at->format('d/m/Y') }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-end">
                                                <div class="dropdown">
                                                    <button class="btn btn-secondary btn-sm rounded-pill" type="button"
                                                        id="dropdownMenuButton{{ $attribute->id }}"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="bi bi-three-dots"></i>
                                                    </button>
                                                    <ul class="dropdown-menu"
                                                        aria-labelledby="dropdownMenuButton{{ $attribute->id }}">
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('admin.attribute_values.index', $attribute->id) }}">
                                                                <i class="bi bi-list-check me-2"></i> Quản lý giá trị
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('admin.attributes.edit', $attribute->id) }}">
                                                                <i class="bi bi-pencil-square me-2"></i> Chỉnh sửa
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item text-danger" href="#"
                                                                onclick="confirmDelete('{{ $attribute->id }}', '{{ $attribute->name }}')">
                                                                <i class="bi bi-trash me-2"></i> Xóa
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if ($attributes->hasPages())
                        <div class="d-flex justify-content-end mt-3">
                            {{ $attributes->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-box display-1 text-muted"></i>
                        <p class="text-muted mt-3">Không tìm thấy thuộc tính nào.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Xác nhận xóa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <i class="bi bi-exclamation-triangle text-danger display-4"></i>
                    </div>
                    <p class="text-center mb-0">
                        Bạn có chắc chắn muốn xóa thuộc tính "<span id="deleteAttributeName" class="fw-bold"></span>"?
                    </p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger rounded-pill px-4">Xóa</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // Xác nhận xóa một thuộc tính
        function confirmDelete(id, name) {
            // Update modal content
            document.getElementById('deleteAttributeName').textContent = name;
            document.getElementById('deleteForm').action = `{{ url('admin/attributes') }}/${id}`;
            // Show modal
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }
    </script>
@endpush
