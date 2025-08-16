@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        {{-- Header Section --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 fw-bold text-gray-800">Danh sách danh mục</h1>
                <p class="text-muted mt-1">Quản lý thông tin các danh mục trong hệ thống</p>
            </div>
            @if(Auth::user()->role === 'admin')
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4">
                        <i class="bi bi-plus-circle me-1"></i> Thêm mới
                    </a>
                    <a href="{{ route('admin.categories.trash') }}" class="btn btn-outline-danger shadow-sm rounded-pill px-4">
                        <i class="bi bi-trash3 me-1"></i> Thùng rác
                    </a>
                </div>
            @endif
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
                {{-- Chú thích cảnh báo không thể xóa danh mục có sản phẩm liên kết --}}
                <div class="alert alert-warning py-2 small mb-3">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    Những danh mục có <span class="text-warning"><i class="bi bi-exclamation-triangle-fill"></i></span> không thể xóa vì còn sản phẩm liên kết.
                </div>
                {{-- Search and Filter Form --}}
                <div class="row mb-4">
                    <div class="col-md-6">
                        <form action="{{ route('admin.categories.index') }}" method="GET" class="d-flex gap-2">
                            <div class="input-group">
                                <input type="text"
                                       name="search"
                                       class="form-control"
                                       placeholder="Tìm kiếm danh mục..."
                                       value="{{ request('search') }}">
                            </div>
                            <select name="status" class="form-select" style="width: auto" onchange="this.form.submit()">
                                <option value="">Tất cả trạng thái</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Đang hoạt động</option>
                                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Không hoạt động</option>
                            </select>
                        </form>
                    </div>
                    <div class="col-md-6 text-end">
                        <button type="button"
                                class="btn btn-success rounded-pill px-4 bulk-toggle-btn me-2"
                                style="display: none;"
                                onclick="bulkToggleStatus(1)"
                                data-bs-toggle="tooltip"
                                title="Kích hoạt đã chọn">
                            <i class="bi bi-check-circle me-1"></i>
                            <i class="bi bi-toggle-on"></i>
                            <span class="badge bg-white text-success ms-2 selected-count">0</span>
                        </button>
                        <button type="button"
                                class="btn btn-secondary rounded-pill px-4 bulk-toggle-btn me-2"
                                style="display: none;"
                                onclick="bulkToggleStatus(0)"
                                data-bs-toggle="tooltip"
                                title="Vô hiệu đã chọn">
                            <i class="bi bi-x-circle me-1"></i>
                            <i class="bi bi-toggle-off"></i>
                            <span class="badge bg-white text-secondary ms-2 selected-count">0</span>
                        </button>
                        <button type="button"
                                class="btn btn-danger rounded-pill px-4 bulk-delete-btn"
                                style="display: none;"
                                data-bs-toggle="tooltip"
                                title="Xóa đã chọn">
                            <i class="bi bi-trash me-1"></i>
                            <i class="bi bi-check2-square"></i>
                            <span class="badge bg-white text-danger ms-2 selected-count">0</span>
                        </button>
                    </div>
                </div>

                @if ($categories->count())
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0" style="width: 40px">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="selectAll">
                                        </div>
                                    </th>
                                    <th class="border-0">
                                        <a href="{{ route('admin.categories.index', array_merge(request()->query(), ['sort_by' => 'name', 'sort_dir' => ($sortBy == 'name' && $sortDir == 'asc') ? 'desc' : 'asc'])) }}"
                                           class="text-decoration-none text-dark d-flex align-items-center">
                                            Tên danh mục
                                            @if ($sortBy == 'name')
                                                <i class="bi bi-arrow-{{ $sortDir == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="border-0" style="width: 120px">Ảnh</th>
                                    <th class="border-0" style="width: 200px">Danh mục cha</th>
                                    <th class="border-0" style="width: 120px">
                                        <a href="{{ route('admin.categories.index', array_merge(request()->query(), ['sort_by' => 'is_active', 'sort_dir' => ($sortBy == 'is_active' && $sortDir == 'asc') ? 'desc' : 'asc'])) }}"
                                           class="text-decoration-none text-dark d-flex align-items-center">
                                            Trạng thái
                                            @if ($sortBy == 'is_active')
                                                <i class="bi bi-arrow-{{ $sortDir == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="border-0" style="width: 120px">
                                        <a href="{{ route('admin.categories.index', array_merge(request()->query(), ['sort_by' => 'created_at', 'sort_dir' => ($sortBy == 'created_at' && $sortDir == 'asc') ? 'desc' : 'asc'])) }}"
                                           class="text-decoration-none text-dark d-flex align-items-center">
                                            Ngày tạo
                                            @if ($sortBy == 'created_at')
                                                <i class="bi bi-arrow-{{ $sortDir == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="border-0 text-end" style="width: 60px">&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categories as $category)
                                    <tr class="position-relative">
                                        <td>
                                            <div class="form-check">
                                                <input type="checkbox"
                                                       class="form-check-input category-checkbox"
                                                       value="{{ $category->id }}"
                                                       data-name="{{ $category->name }}"
                                                       data-status="{{ $category->is_active }}">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="fw-medium">{{ $category->name }}</span>
                                                @if($category->products_count > 0)
                                                    <span class="ms-1" data-bs-toggle="tooltip" title="Không thể xóa vì còn sản phẩm liên kết">
                                                        <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                                                    </span>
                                                    <a href="{{ route('admin.products.index', ['category' => $category->id]) }}" class="btn btn-link btn-sm ms-2 px-1 py-0" style="font-size:13px;" title="Xem sản phẩm liên kết">
                                                        <i class="bi bi-box-seam"></i> Sản phẩm ({{ $category->products_count }})
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if ($category->icon)
                                                @php
                                                    $iconPath = 'storage/' . $category->icon;
                                                    $iconExists = file_exists(public_path($iconPath));
                                                @endphp
                                                @if ($iconExists)
                                                    <img src="{{ asset($iconPath) }}"
                                                         alt="{{ $category->name }}"
                                                         class="img-thumbnail rounded-3"
                                                         style="max-width:80px; max-height:80px; object-fit:contain; transition: transform 0.2s;"
                                                         onmouseover="this.style.transform='scale(1.1)'"
                                                         onmouseout="this.style.transform='scale(1)'">
                                                @else
                                                    <div class="text-muted small">
                                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                                        Ảnh không tồn tại
                                                    </div>
                                                @endif
                                            @else
                                                <div class="text-muted small">
                                                    <i class="bi bi-image me-1"></i>
                                                    Không có ảnh
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            @if($category->parent)
                                                <span class="badge bg-info rounded-pill px-3 py-2">
                                                    {{ $category->parent->name }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary rounded-pill px-3 py-2">
                                                    Danh mục gốc
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill {{ $category->is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} px-3 py-2">
                                                <i class="bi bi-circle-fill me-1 small"></i>
                                                {{ $category->is_active ? 'Đang hoạt động' : 'Không hoạt động' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="text-muted" data-bs-toggle="tooltip" title="{{ $category->created_at->format('H:i:s d/m/Y') }}">
                                                {{ $category->created_at->format('d/m/Y') }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="dropdown">
                                                <button class="btn btn-link text-dark p-0 m-0" type="button" id="dropdownMenu{{ $category->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="bi bi-three-dots fs-4"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow rounded-3 py-2" aria-labelledby="dropdownMenu{{ $category->id }}" style="min-width: 180px;">
                                                    <li>
                                                        <a href="{{ route('admin.categories.show', $category->id) }}" class="dropdown-item d-flex align-items-center gap-2">
                                                            <i class="bi bi-eye text-primary"></i> <span>Xem chi tiết</span>
                                                        </a>
                                                    </li>
                                                    @if(Auth::user()->role === 'admin')
                                                        <li>
                                                            <a href="{{ route('admin.categories.edit', $category->id) }}" class="dropdown-item d-flex align-items-center gap-2">
                                                                <i class="bi bi-pencil-square text-warning"></i> <span>Chỉnh sửa</span>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            @if($category->products_count > 0)
                                                            <button type="button" class="dropdown-item d-flex align-items-center gap-2 text-muted" disabled>
                                                                <i class="bi bi-trash"></i> <span>Xóa</span>
                                                            </button>
                                                            @else
                                                            <button type="button" class="dropdown-item d-flex align-items-center gap-2 text-danger" onclick="confirmDelete('{{ $category->id }}', '{{ $category->name }}')">
                                                                <i class="bi bi-trash"></i> <span>Xóa</span>
                                                            </button>
                                                            @endif
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if ($categories->hasPages())
                        <div class="d-flex justify-content-end mt-3">
                            {{ $categories->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-box display-1 text-muted"></i>
                        <p class="text-muted mt-3">Không tìm thấy danh mục nào.</p>
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
                        Bạn có chắc chắn muốn xóa danh mục "<span id="deleteCategoryName" class="fw-bold"></span>"?
                    </p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger rounded-pill px-4">Xóa</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Bulk Delete Confirmation Modal --}}
    <div class="modal fade" id="bulkDeleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Xác nhận xóa hàng loạt</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <i class="bi bi-exclamation-triangle text-danger display-4"></i>
                    </div>
                    <p class="text-center mb-0">
                        Bạn có chắc chắn muốn xóa <span id="bulkDeleteCount" class="fw-bold"></span> danh mục đã chọn?
                    </p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-danger rounded-pill px-4" onclick="submitBulkDelete()">Xóa</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Bulk selection handling
        let selectedCategories = new Set();
        const bulkToggleBtns = document.querySelectorAll('.bulk-toggle-btn');
        const bulkDeleteBtn = document.querySelector('.bulk-delete-btn');
        const selectAllCheckbox = document.getElementById('selectAll');
        const categoryCheckboxes = document.querySelectorAll('.category-checkbox');

        // Update UI based on selection
        function updateBulkButtons() {
            const count = selectedCategories.size;
            document.querySelectorAll('.selected-count').forEach(el => {
                el.textContent = count;
            });
            bulkToggleBtns.forEach(btn => {
                btn.style.display = count > 0 ? 'inline-block' : 'none';
            });
            bulkDeleteBtn.style.display = count > 0 ? 'inline-block' : 'none';
        }

        // Handle individual checkbox changes
        categoryCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    selectedCategories.add(this.value);
                } else {
                    selectedCategories.delete(this.value);
                    selectAllCheckbox.checked = false;
                }
                updateBulkButtons();
            });
        });

        // Handle select all checkbox
        selectAllCheckbox.addEventListener('change', function() {
            categoryCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
                if (this.checked) {
                    selectedCategories.add(checkbox.value);
                } else {
                    selectedCategories.delete(checkbox.value);
                }
            });
            updateBulkButtons();
        });

        // Bulk toggle status
        window.bulkToggleStatus = function(status) {
            const ids = Array.from(selectedCategories);
            
            fetch('{{ route("admin.categories.bulk-toggle") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ 
                    ids: ids,
                    status: status 
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.error || 'Có lỗi xảy ra khi cập nhật trạng thái');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi cập nhật trạng thái');
            });
        };

        // Show bulk delete modal
        bulkDeleteBtn.addEventListener('click', function() {
            if (selectedCategories.size === 0) {
                alert('Vui lòng chọn ít nhất một danh mục');
                return;
            }
            document.getElementById('bulkDeleteCount').textContent = selectedCategories.size;
            new bootstrap.Modal(document.getElementById('bulkDeleteModal')).show();
        });

        // Submit bulk delete
        window.submitBulkDelete = function() {
            fetch('{{ route("admin.categories.bulk-delete") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ ids: Array.from(selectedCategories) })
            })
            .then(response => response.json())
            .then(data => {
                // Nếu có danh mục không xóa được
                if (data.warning) {
                    alert(data.warning);
                }
                // Nếu có danh mục đã xóa thành công, xóa khỏi DOM
                if (data.deleted_ids && Array.isArray(data.deleted_ids)) {
                    data.deleted_ids.forEach(function(id) {
                        // Tìm dòng chứa checkbox có value = id
                        const row = document.querySelector('input.category-checkbox[value="' + id + '"]')?.closest('tr');
                        if (row) row.remove();
                        selectedCategories.delete(id);
                    });
                    updateBulkButtons();
                }
                // Nếu có thông báo thành công
                if (data.success) {
                    alert(data.success);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Đã có lỗi xảy ra');
            });
        };

        // Xác nhận xóa một danh mục
        window.confirmDelete = function(id, name) {
            document.getElementById('deleteCategoryName').textContent = name;
            const form = document.getElementById('deleteForm');
            form.action = `{{ url('admin/categories') }}/${id}`;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        };
    });
</script>
@endpush
