@extends('admin.layouts.app')

@section('title', 'Quản lý thương hiệu')

@section('content')
    <div class="container-fluid py-4">
        {{-- Header Section --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 fw-bold text-gray-800">Danh sách thương hiệu</h1>
                <p class="text-muted mt-1">Quản lý thông tin các thương hiệu trong hệ thống</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.brands.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4">
                    <i class="bi bi-plus-circle me-1"></i> Thêm mới
                </a>
                <a href="{{ route('admin.brands.trash') }}" class="btn btn-outline-danger shadow-sm rounded-pill px-4">
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
                {{-- Chú thích cảnh báo không thể xóa thương hiệu có sản phẩm liên kết --}}
                <div class="alert alert-warning py-2 small mb-3">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    Những thương hiệu có <span class="text-warning"><i class="bi bi-exclamation-triangle-fill"></i></span> không thể xóa vì còn sản phẩm liên kết.
                </div>
                {{-- Search and Filter Form --}}
                <div class="row mb-4">
                    <div class="col-md-6">
                        <form action="{{ route('admin.brands.index') }}" method="GET" class="d-flex gap-2">
                            <div class="input-group">
                                <input type="text" 
                                       name="search" 
                                       class="form-control"
                                       placeholder="Tìm kiếm thương hiệu..." 
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
                        <!-- Các nút bulk action giữ nguyên -->
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

                @if ($brands->count())
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
                                        <a href="{{ route('admin.brands.index', array_merge(request()->query(), ['sort_by' => 'name', 'sort_dir' => ($sortBy == 'name' && $sortDir == 'asc') ? 'desc' : 'asc'])) }}"
                                           class="text-decoration-none text-dark d-flex align-items-center">
                                            Tên thương hiệu
                                            @if ($sortBy == 'name')
                                                <i class="bi bi-arrow-{{ $sortDir == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="border-0" style="width: 120px">Logo</th>
                                    <th class="border-0" style="width: 120px">
                                        <a href="{{ route('admin.brands.index', array_merge(request()->query(), ['sort_by' => 'is_active', 'sort_dir' => ($sortBy == 'is_active' && $sortDir == 'asc') ? 'desc' : 'asc'])) }}"
                                           class="text-decoration-none text-dark d-flex align-items-center">
                                            Trạng thái
                                            @if ($sortBy == 'is_active')
                                                <i class="bi bi-arrow-{{ $sortDir == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="border-0" style="width: 100px">Hiển thị</th>
                                    <th class="border-0" style="width: 120px">
                                        <a href="{{ route('admin.brands.index', array_merge(request()->query(), ['sort_by' => 'created_at', 'sort_dir' => ($sortBy == 'created_at' && $sortDir == 'asc') ? 'desc' : 'asc'])) }}"
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
                                @foreach ($brands as $brand)
                                    <tr class="position-relative">
                                        <td>
                                            <div class="form-check">
                                                <input type="checkbox" 
                                                       class="form-check-input brand-checkbox" 
                                                       value="{{ $brand->id }}"
                                                       data-name="{{ $brand->name }}"
                                                       data-status="{{ $brand->is_active }}">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="fw-medium">{{ $brand->name }}</span>
                                                @if($brand->products()->count() > 0)
                                                    <span class="ms-1" data-bs-toggle="tooltip" title="Không thể xóa vì còn sản phẩm liên kết">
                                                        <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                                                    </span>
                                                    <a href="{{ route('admin.products.index', ['brand' => $brand->id]) }}" class="btn btn-link btn-sm ms-2 px-1 py-0" style="font-size:13px;" title="Xem sản phẩm liên kết">
                                                        <i class="bi bi-box-seam"></i> Sản phẩm ({{ $brand->products()->count() }})
                                                    </a>
                                                @endif
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
                                            <button type="button"
                                                class="badge rounded-pill toggle-active-btn {{ $brand->is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} px-3 py-2 border-0"
                                                data-id="{{ $brand->id }}"
                                                style="cursor:pointer;">
                                                <i class="bi bi-circle-fill me-1 small"></i>
                                                {{ $brand->is_active ? 'Đang hoạt động' : 'Không hoạt động' }}
                                            </button>
                                        </td>
                                        <td class="text-center">
                                            @if($brand->is_active)
                                                <button type="button"
                                                    class="badge rounded-pill toggle-visible-btn {{ $brand->is_visible ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} px-3 py-2 border-0"
                                                    data-id="{{ $brand->id }}"
                                                    style="cursor:pointer;">
                                                    {{ $brand->is_visible ? 'Hiển thị' : 'Ẩn' }}
                                                </button>
                                            @else
                                                <span class="badge rounded-pill bg-secondary-subtle text-secondary px-3 py-2 border-0 opacity-50" style="cursor:not-allowed;">Ẩn</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="text-muted" data-bs-toggle="tooltip" title="{{ $brand->created_at->format('H:i:s d/m/Y') }}">
                                                {{ $brand->created_at->format('d/m/Y') }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="dropdown">
                                                <button class="btn btn-link text-dark p-0 m-0" type="button" id="dropdownMenu{{ $brand->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="bi bi-three-dots fs-4"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow rounded-3 py-2" aria-labelledby="dropdownMenu{{ $brand->id }}" style="min-width: 180px;">
                                                    <li>
                                                        <a href="{{ route('admin.brands.show', $brand->id) }}" class="dropdown-item d-flex align-items-center gap-2">
                                                            <i class="bi bi-eye text-primary"></i> <span>Xem chi tiết</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('admin.brands.edit', $brand->id) }}" class="dropdown-item d-flex align-items-center gap-2">
                                                            <i class="bi bi-pencil-square text-warning"></i> <span>Chỉnh sửa</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        @if($brand->products()->count() > 0)
                                                            <button type="button" class="dropdown-item d-flex align-items-center gap-2 text-muted" disabled>
                                                                <i class="bi bi-trash"></i> <span>Xóa</span>
                                                            </button>
                                                        @else
                                                            <button type="button" class="dropdown-item d-flex align-items-center gap-2 text-danger" onclick="confirmDelete('{{ $brand->id }}', '{{ $brand->name }}')">
                                                                <i class="bi bi-trash"></i> <span>Xóa</span>
                                                            </button>
                                                        @endif
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-box display-1 text-muted"></i>
                        <p class="text-muted mt-3">Không tìm thấy thương hiệu nào.</p>
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
                        Bạn có chắc chắn muốn xóa thương hiệu "<span id="deleteBrandName" class="fw-bold"></span>"?
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
                        Bạn có chắc chắn muốn xóa <span id="bulkDeleteCount" class="fw-bold"></span> thương hiệu đã chọn?
                    </p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-danger rounded-pill px-4" onclick="submitBulkDelete()">Xóa</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Bulk Toggle Status Confirmation Modal --}}
    <div class="modal fade" id="bulkToggleModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Xác nhận thay đổi trạng thái</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <i class="bi bi-toggle-on text-success display-4"></i>
                    </div>
                    <p class="text-center mb-0">
                        Bạn có chắc chắn muốn thay đổi trạng thái của <span id="bulkToggleCount" class="fw-bold"></span> thương hiệu đã chọn?
                    </p>
                    <input type="hidden" id="bulkToggleStatus" value="1">
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-success rounded-pill px-4" onclick="submitBulkToggle()">Xác nhận</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Bulk selection handling
            let selectedBrands = new Set();
            const bulkToggleBtns = document.querySelectorAll('.bulk-toggle-btn');
            const bulkDeleteBtn = document.querySelector('.bulk-delete-btn');
            const selectAllCheckbox = document.getElementById('selectAll');
            const brandCheckboxes = document.querySelectorAll('.brand-checkbox');

            // Update UI based on selection
            function updateBulkButtons() {
                const count = selectedBrands.size;
                document.querySelectorAll('.selected-count').forEach(el => {
                    el.textContent = count;
                });
                bulkToggleBtns.forEach(btn => {
                    btn.style.display = count > 0 ? 'inline-block' : 'none';
                });
                bulkDeleteBtn.style.display = count > 0 ? 'inline-block' : 'none';
            }

            // Handle individual checkbox changes
            brandCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        selectedBrands.add(this.value);
                    } else {
                        selectedBrands.delete(this.value);
                        selectAllCheckbox.checked = false;
                    }
                    updateBulkButtons();
                });
            });

            // Handle select all checkbox
            selectAllCheckbox.addEventListener('change', function() {
                brandCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                    if (this.checked) {
                        selectedBrands.add(checkbox.value);
                    } else {
                        selectedBrands.delete(checkbox.value);
                    }
                });
                updateBulkButtons();
            });

            // Bulk toggle status
            window.bulkToggleStatus = function(status) {
                const ids = Array.from(selectedBrands);
                
                fetch('{{ route("admin.brands.bulk-toggle-status") }}', {
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
                if (selectedBrands.size === 0) {
                    alert('Vui lòng chọn ít nhất một thương hiệu');
                    return;
                }
                document.getElementById('bulkDeleteCount').textContent = selectedBrands.size;
                new bootstrap.Modal(document.getElementById('bulkDeleteModal')).show();
            });

            // Submit bulk delete
            window.submitBulkDelete = function() {
                fetch('{{ route("admin.brands.bulk-delete") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ ids: Array.from(selectedBrands) })
                })
                .then(response => response.json())
                .then(data => {
                    // Nếu có thương hiệu không xóa được
                    if (data.error) {
                        alert(data.error);
                    }
                    // Nếu có thương hiệu đã xóa thành công, xóa khỏi DOM
                    if (data.deleted_ids && Array.isArray(data.deleted_ids)) {
                        data.deleted_ids.forEach(function(id) {
                            // Tìm dòng chứa checkbox có value = id
                            const row = document.querySelector('input.brand-checkbox[value="' + id + '"]')?.closest('tr');
                            if (row) row.remove();
                            selectedBrands.delete(id);
                        });
                        updateBulkButtons();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Đã có lỗi xảy ra');
                });
            };

            // Xác nhận xóa một thương hiệu
            window.confirmDelete = function(id, name) {
                document.getElementById('deleteBrandName').textContent = name;
                const form = document.getElementById('deleteForm');
                form.action = `{{ url('admin/brands') }}/${id}`;
                new bootstrap.Modal(document.getElementById('deleteModal')).show();
            };

            document.querySelectorAll('.toggle-visible-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const brandId = this.getAttribute('data-id');
                    const button = this;
                    fetch(`/admin/brands/${brandId}/toggle-visible`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ _method: 'PUT' })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            if (data.is_visible) {
                                button.classList.remove('bg-secondary-subtle', 'text-secondary');
                                button.classList.add('bg-success-subtle', 'text-success');
                                button.textContent = 'Hiển thị';
                            } else {
                                button.classList.remove('bg-success-subtle', 'text-success');
                                button.classList.add('bg-secondary-subtle', 'text-secondary');
                                button.textContent = 'Ẩn';
                            }
                        }
                    });
                });
            });

            document.querySelectorAll('.toggle-active-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const brandId = this.getAttribute('data-id');
                    const button = this;
                    fetch(`/admin/brands/${brandId}/toggle-active`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ _method: 'PUT' })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Cập nhật badge trạng thái
                            if (data.is_active) {
                                button.classList.remove('bg-danger-subtle', 'text-danger');
                                button.classList.add('bg-success-subtle', 'text-success');
                                button.textContent = '';
                                button.innerHTML = '<i class="bi bi-circle-fill me-1 small"></i>Đang hoạt động';
                            } else {
                                button.classList.remove('bg-success-subtle', 'text-success');
                                button.classList.add('bg-danger-subtle', 'text-danger');
                                button.textContent = '';
                                button.innerHTML = '<i class="bi bi-circle-fill me-1 small"></i>Không hoạt động';
                            }
                            // Cập nhật badge hiển thị nếu có
                            const row = button.closest('tr');
                            const visibleBtn = row.querySelector('.toggle-visible-btn');
                            const visibleSpan = row.querySelector('.badge.bg-secondary-subtle');
                            if (data.is_active) {
                                if (visibleBtn) {
                                    visibleBtn.disabled = false;
                                    visibleBtn.removeAttribute('disabled');
                                    visibleBtn.style.cursor = 'pointer';
                                }
                            } else {
                                if (visibleBtn) {
                                    visibleBtn.disabled = true;
                                    visibleBtn.setAttribute('disabled', 'disabled');
                                    visibleBtn.style.cursor = 'not-allowed';
                                    visibleBtn.classList.remove('bg-success-subtle', 'text-success');
                                    visibleBtn.classList.add('bg-secondary-subtle', 'text-secondary');
                                    visibleBtn.textContent = 'Ẩn';
                                }
                            }
                        }
                    });
                });
            });
        });
    </script>
    @endpush
@endsection