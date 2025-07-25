@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold text-gray-800">Danh sách danh mục</h1>
            <p class="text-muted mt-1">Quản lý thông tin các danh mục trong hệ thống</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4">
                <i class="bi bi-plus-circle me-1"></i> Thêm mới
            </a>
            <a href="{{ route('admin.categories.trash') }}" class="btn btn-outline-danger shadow-sm rounded-pill px-4">
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
                    <form action="{{ route('admin.categories.index') }}" method="GET" class="d-flex gap-2">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm danh mục..."
                                value="{{ request('search') }}">
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
                <div class="col-md-6 text-end">
                    <button type="button" class="btn btn-success rounded-pill px-4 bulk-toggle-btn me-2"
                        style="display: none;" onclick="bulkToggleStatus(1)" data-bs-toggle="tooltip"
                        title="Kích hoạt đã chọn">
                        <i class="bi bi-check-circle me-1"></i>
                        <i class="bi bi-toggle-on"></i>
                        <span class="badge bg-white text-success ms-2 selected-count">0</span>
                    </button>
                    <button type="button" class="btn btn-secondary rounded-pill px-4 bulk-toggle-btn me-2"
                        style="display: none;" onclick="bulkToggleStatus(0)" data-bs-toggle="tooltip"
                        title="Vô hiệu đã chọn">
                        <i class="bi bi-x-circle me-1"></i>
                        <i class="bi bi-toggle-off"></i>
                        <span class="badge bg-white text-secondary ms-2 selected-count">0</span>
                    </button>
                    <button type="button" class="btn btn-danger rounded-pill px-4 bulk-delete-btn"
                        style="display: none;" data-bs-toggle="tooltip" title="Xóa đã chọn">
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
                                {{-- <th class="border-0" style="width: 60px">
                                    <a href="{{ route('admin.categories.index', array_merge(request()->query(), ['sort_by' => 'id', 'sort_dir' => ($sortBy == 'id' && $sortDir == 'asc') ? 'desc' : 'asc'])) }}"
                                        class="text-decoration-none text-dark d-flex align-items-center">
                                        ID
                                        @if ($sortBy == 'id')
                                            <i class="bi bi-arrow-{{ $sortDir == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                        @endif
                                    </a>
                                </th> --}}
                                <th class="border-0">
                                    <a href="{{ route('admin.categories.index', array_merge(request()->query(), ['sort_by' => 'name', 'sort_dir' => $sortBy == 'name' && $sortDir == 'asc' ? 'desc' : 'asc'])) }}"
                                        class="text-decoration-none text-dark d-flex align-items-center">
                                        Tên danh mục
                                        @if ($sortBy == 'name')
                                            <i class="bi bi-arrow-{{ $sortDir == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="border-0" style="width: 120px">Ảnh</th>
                                <th class="border-0" style="width: 200px">Danh mục cha</th>
                                <th class="border-0">Sản phẩm</th>
                                <th class="border-0" style="width: 120px">
                                    <a href="{{ route('admin.categories.index', array_merge(request()->query(), ['sort_by' => 'is_active', 'sort_dir' => $sortBy == 'is_active' && $sortDir == 'asc' ? 'desc' : 'asc'])) }}"
                                        class="text-decoration-none text-dark d-flex align-items-center">
                                        Trạng thái
                                        @if ($sortBy == 'is_active')
                                            <i class="bi bi-arrow-{{ $sortDir == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="border-0" style="width: 120px">
                                    <a href="{{ route('admin.categories.index', array_merge(request()->query(), ['sort_by' => 'created_at', 'sort_dir' => $sortBy == 'created_at' && $sortDir == 'asc' ? 'desc' : 'asc'])) }}"
                                        class="text-decoration-none text-dark d-flex align-items-center">
                                        Ngày tạo
                                        @if ($sortBy == 'created_at')
                                            <i class="bi bi-arrow-{{ $sortDir == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="border-0 text-end" style="width: 200px">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $category)
                                <tr class="position-relative">
                                    <td>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input category-checkbox"
                                                value="{{ $category->id }}" data-name="{{ $category->name }}">
                                        </div>
                                    </td>
                                    {{-- <td class="text-muted">{{ $category->id }}</td> --}}
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="fw-medium">{{ $category->name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if ($category->icon)
                                            @php
                                                $iconPath = 'storage/' . $category->icon;
                                                $iconExists = file_exists(public_path($iconPath));
                                            @endphp
                                            @if ($iconExists)
                                                <img src="{{ asset($iconPath) }}" alt="{{ $category->name }}"
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
                                        @if ($category->parent)
                                            <span class="badge bg-info rounded-pill px-3 py-2">
                                                {{ $category->parent->name }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary rounded-pill px-3 py-2">
                                                Danh mục gốc
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ $category->products_count }}</td>
                                    <td class="text-center">
                                        <span
                                            class="badge rounded-pill {{ $category->is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} px-3 py-2">
                                            <i class="bi bi-circle-fill me-1 small"></i>
                                            {{ $category->is_active ? 'Đang hoạt động' : 'Không hoạt động' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="text-muted" data-bs-toggle="tooltip"
                                            title="{{ $category->created_at->format('H:i:s d/m/Y') }}">
                                            {{ $category->created_at->format('d/m/Y') }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="dropdown">
                                            <button class="btn btn-link p-0 text-dark" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis-h"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm rounded">
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.categories.show', $category->id) }}">
                                                        <i class="fas fa-eye me-1"></i> Xem
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.categories.edit', $category->id) }}">
                                                        <i class="fas fa-edit me-1"></i> Sửa
                                                    </a>
                                                </li>
                                                <li>
                                                    <form
                                                        action="{{ route('admin.categories.destroy', $category->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="fas fa-trash-alt me-1"></i> Xóa
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
                <form id="deleteForm" method="POST" style="display: inline;">
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
                <button type="button" class="btn btn-danger rounded-pill px-4"
                    onclick="submitBulkDelete()">Xóa</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        const selectAllCheckbox = document.getElementById('selectAll');
        const categoryCheckboxes = document.querySelectorAll('.category-checkbox');
        const bulkDeleteBtn = document.querySelector('.bulk-delete-btn');
        const bulkToggleBtns = document.querySelectorAll('.bulk-toggle-btn');
        const selectedCounts = document.querySelectorAll('.selected-count');
        let selectedItems = [];

        function updateUI() {
            const hasSelected = selectedItems.length > 0;
            bulkDeleteBtn.style.display = hasSelected ? 'inline-block' : 'none';
            bulkToggleBtns.forEach(btn => {
                btn.style.display = hasSelected ? 'inline-block' : 'none';
            });
            selectedCounts.forEach(count => {
                count.textContent = selectedItems.length;
            });
        }

        function handleCheckboxChange(checkbox) {
            const categoryId = checkbox.value;
            if (checkbox.checked) {
                if (!selectedItems.includes(categoryId)) {
                    selectedItems.push(categoryId);
                }
            } else {
                selectedItems = selectedItems.filter(id => id !== categoryId);
                selectAllCheckbox.checked = false;
            }
            updateUI();
        }

        selectAllCheckbox?.addEventListener('change', function() {
            categoryCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
                handleCheckboxChange(checkbox);
            });
        });

        categoryCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                handleCheckboxChange(this);
            });
        });

        bulkDeleteBtn?.addEventListener('click', function() {
            if (selectedItems.length === 0) {
                Swal.fire('Thông báo', 'Vui lòng chọn ít nhất một danh mục', 'warning');
                return;
            }

            Swal.fire({
                title: 'Xác nhận xóa',
                text: 'Bạn có chắc chắn muốn xóa các danh mục đã chọn?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy',
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route('admin.categories.bulk-delete') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                ids: selectedItems
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Thành công', data.message || 'Đã xóa danh mục',
                                        'success')
                                    .then(() => window.location.reload());
                            } else {
                                Swal.fire('Lỗi', data.error || 'Không thể xóa danh mục',
                                    'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire('Lỗi', 'Đã có lỗi xảy ra khi xóa danh mục', 'error');
                        });
                }
            });
        });
    });

    function bulkToggleStatus(status) {
        const selectedItems = Array.from(document.querySelectorAll('.category-checkbox:checked')).map(cb => cb.value);
        if (selectedItems.length === 0) {
            Swal.fire('Thông báo', 'Vui lòng chọn ít nhất một danh mục', 'warning');
            return;
        }

        const statusText = status ? 'kích hoạt' : 'vô hiệu hóa';

        Swal.fire({
            title: 'Xác nhận',
            text: `Bạn có chắc chắn muốn ${statusText} các danh mục đã chọn?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Xác nhận',
            cancelButtonText: 'Hủy',
        }).then(result => {
            if (result.isConfirmed) {
                fetch('{{ route('admin.categories.bulk-toggle') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            ids: selectedItems,
                            status: status
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Thành công', data.message || 'Cập nhật trạng thái thành công',
                                    'success')
                                .then(() => window.location.reload());
                        } else {
                            Swal.fire('Lỗi', data.error || 'Không thể cập nhật trạng thái', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Lỗi', 'Đã có lỗi xảy ra khi cập nhật', 'error');
                    });
            }
        });
    }

    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Xác nhận xóa',
            text: `Bạn có chắc chắn muốn xóa danh mục "${name}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Xóa',
            cancelButtonText: 'Hủy'
        }).then(result => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ url('admin/categories') }}/${id}`;
                form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
@endpush
