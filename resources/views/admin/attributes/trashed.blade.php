@extends('admin.layouts.app')


@section('content')
<div class="container-fluid py-4">
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold text-gray-800">Thùng rác thuộc tính</h1>
            <p class="text-muted mt-1">Quản lý các thuộc tính đã bị xóa</p>
        </div>
        <div>
            <a href="{{ route('admin.attributes.index') }}" class="btn btn-outline-primary shadow-sm rounded-pill px-4">
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
            <div class="row mb-4">
                <div class="col-md-6">
                    <form action="{{ route('admin.attributes.trashed') }}" method="GET" class="d-flex gap-2">
                        <div class="input-group">
                            <input type="text"
                                   name="search"
                                   class="form-control"
                                   placeholder="Tìm kiếm thuộc tính..."
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
                            class="btn btn-success rounded-pill px-4 bulk-restore-btn me-2"
                            style="display: none;">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                        Khôi phục đã chọn (<span class="selected-count">0</span>)
                    </button>
                    <button type="button"
                            class="btn btn-danger rounded-pill px-4 bulk-delete-btn"
                            style="display: none;">
                        <i class="bi bi-trash me-1"></i>
                        Xóa vĩnh viễn đã chọn (<span class="selected-count">0</span>)
                    </button>
                </div>
            </div>


            {{-- Attributes Table --}}
            @if ($attributes->count())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0" style="width: 40px">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="selectAll">
                                    </div>
                                </th>
                                <th class="border-0" style="width: 60px">
                                    <a href="{{ route('admin.attributes.trashed', array_merge(request()->query(), ['sort_by' => 'id', 'sort_dir' => ($sortBy == 'id' && $sortDir == 'asc') ? 'desc' : 'asc'])) }}"
                                       class="text-decoration-none text-dark d-flex align-items-center">
                                        ID
                                        @if ($sortBy == 'id')
                                            <i class="bi bi-arrow-{{ $sortDir == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="border-0">
                                    <a href="{{ route('admin.attributes.trashed', array_merge(request()->query(), ['sort_by' => 'name', 'sort_dir' => ($sortBy == 'name' && $sortDir == 'asc') ? 'desc' : 'asc'])) }}"
                                       class="text-decoration-none text-dark d-flex align-items-center">
                                        Tên thuộc tính
                                        @if ($sortBy == 'name')
                                            <i class="bi bi-arrow-{{ $sortDir == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="border-0" style="width: 150px">
                                    <a href="{{ route('admin.attributes.trashed', array_merge(request()->query(), ['sort_by' => 'is_variant', 'sort_dir' => ($sortBy == 'is_variant' && $sortDir == 'asc') ? 'desc' : 'asc'])) }}"
                                       class="text-decoration-none text-dark d-flex align-items-center">
                                        Biến thể
                                        @if ($sortBy == 'is_variant')
                                            <i class="bi bi-arrow-{{ $sortDir == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="border-0" style="width: 120px">
                                    <a href="{{ route('admin.attributes.trashed', array_merge(request()->query(), ['sort_by' => 'is_active', 'sort_dir' => ($sortBy == 'is_active' && $sortDir == 'asc') ? 'desc' : 'asc'])) }}"
                                       class="text-decoration-none text-dark d-flex align-items-center">
                                        Trạng thái
                                        @if ($sortBy == 'is_active')
                                            <i class="bi bi-arrow-{{ $sortDir == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="border-0" style="width: 150px">
                                    <a href="{{ route('admin.attributes.trashed', array_merge(request()->query(), ['sort_by' => 'deleted_at', 'sort_dir' => ($sortBy == 'deleted_at' && $sortDir == 'asc') ? 'desc' : 'asc'])) }}"
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
                            @foreach ($attributes as $attribute)
                                <tr class="position-relative">
                                    <td>
                                        <div class="form-check">
                                            <input type="checkbox"
                                                   class="form-check-input attribute-checkbox"
                                                   value="{{ $attribute->id }}"
                                                   data-name="{{ $attribute->name }}">
                                        </div>
                                    </td>
                                    <td class="text-muted">{{ $attribute->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="fw-medium">{{ $attribute->name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill {{ $attribute->is_variant ? 'bg-info-subtle text-info' : 'bg-secondary-subtle text-secondary' }} px-3 py-2">
                                            <i class="bi bi-circle-fill me-1 small"></i>
                                            {{ $attribute->is_variant ? 'Là biến thể' : 'Không phải biến thể' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill {{ $attribute->is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} px-3 py-2">
                                            <i class="bi bi-circle-fill me-1 small"></i>
                                            {{ $attribute->is_active ? 'Đang hoạt động' : 'Không hoạt động' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="text-muted" data-bs-toggle="tooltip" title="{{ $attribute->deleted_at->format('H:i:s d/m/Y') }}">
                                            {{ $attribute->deleted_at->format('d/m/Y') }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-end gap-2">
                                            <button type="button"
                                                    class="btn btn-success btn-sm rounded-circle"
                                                    onclick="confirmRestore('{{ $attribute->id }}', '{{ $attribute->name }}')"
                                                    data-bs-toggle="tooltip"
                                                    title="Khôi phục"
                                                    style="width: 32px; height: 32px;">
                                                <i class="bi bi-arrow-counterclockwise"></i>
                                            </button>
                                            <button type="button"
                                                    class="btn btn-danger btn-sm rounded-circle"
                                                    onclick="confirmForceDelete('{{ $attribute->id }}', '{{ $attribute->name }}')"
                                                    data-bs-toggle="tooltip"
                                                    title="Xóa vĩnh viễn"
                                                    style="width: 32px; height: 32px;">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-trash display-1 text-muted"></i>
                    <p class="text-muted mt-3">Không có thuộc tính nào trong thùng rác.</p>
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
                    Bạn có chắc chắn muốn khôi phục thuộc tính "<span id="restoreAttributeName" class="fw-bold"></span>"?
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
                    Bạn có chắc chắn muốn xóa vĩnh viễn thuộc tính "<span id="forceDeleteAttributeName" class="fw-bold"></span>"?
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


{{-- Bulk Restore Confirmation Modal --}}
<div class="modal fade" id="bulkRestoreModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Xác nhận khôi phục hàng loạt</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="bi bi-arrow-counterclockwise text-success display-4"></i>
                </div>
                <p class="text-center mb-0">
                    Bạn có chắc chắn muốn khôi phục <span id="bulkRestoreCount" class="fw-bold"></span> thuộc tính đã chọn?
                </p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-success rounded-pill px-4" onclick="submitBulkRestore()">Khôi phục</button>
            </div>
        </div>
    </div>
</div>


{{-- Bulk Force Delete Confirmation Modal --}}
<div class="modal fade" id="bulkForceDeleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Xác nhận xóa vĩnh viễn hàng loạt</h5>
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
                    Bạn có chắc chắn muốn xóa vĩnh viễn <span id="bulkForceDeleteCount" class="fw-bold"></span> thuộc tính đã chọn?
                </p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger rounded-pill px-4" onclick="submitBulkForceDelete()">Xóa vĩnh viễn</button>
            </div>
        </div>
    </div>
</div>


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const bulkRestoreBtn = document.querySelector('.bulk-restore-btn');
        const bulkDeleteBtn = document.querySelector('.bulk-delete-btn');
        const selectAllCheckbox = document.getElementById('selectAll');
        const attributeCheckboxes = document.querySelectorAll('.attribute-checkbox');
        let selectedAttributes = new Set();


        // Update UI based on selection
        function updateBulkButtons() {
            const count = selectedAttributes.size;
            document.querySelectorAll('.selected-count').forEach(el => {
                el.textContent = count;
            });
            bulkRestoreBtn.style.display = count > 0 ? 'inline-block' : 'none';
            bulkDeleteBtn.style.display = count > 0 ? 'inline-block' : 'none';
        }


        // Handle individual checkbox changes
        attributeCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    selectedAttributes.add(this.value);
                } else {
                    selectedAttributes.delete(this.value);
                    selectAllCheckbox.checked = false;
                }
                updateBulkButtons();
            });
        });


        // Handle select all checkbox
        selectAllCheckbox.addEventListener('change', function() {
            attributeCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
                if (this.checked) {
                    selectedAttributes.add(checkbox.value);
                } else {
                    selectedAttributes.delete(checkbox.value);
                }
            });
            updateBulkButtons();
        });


        // Single restore confirmation
        window.confirmRestore = function(id, name) {
            document.getElementById('restoreAttributeName').textContent = name;
            document.getElementById('restoreForm').action = `/admin/attributes/${id}/restore`;
            new bootstrap.Modal(document.getElementById('restoreModal')).show();
        };


        // Single force delete confirmation
        window.confirmForceDelete = function(id, name) {
            document.getElementById('forceDeleteAttributeName').textContent = name;
            document.getElementById('forceDeleteForm').action = `/admin/attributes/${id}/force`;
            new bootstrap.Modal(document.getElementById('forceDeleteModal')).show();
        };


        // Show bulk restore confirmation modal
        bulkRestoreBtn.addEventListener('click', function() {
            document.getElementById('bulkRestoreCount').textContent = selectedAttributes.size;
            new bootstrap.Modal(document.getElementById('bulkRestoreModal')).show();
        });


        // Show bulk force delete confirmation modal
        bulkDeleteBtn.addEventListener('click', function() {
            document.getElementById('bulkForceDeleteCount').textContent = selectedAttributes.size;
            new bootstrap.Modal(document.getElementById('bulkForceDeleteModal')).show();
        });


        // Submit bulk restore
        window.submitBulkRestore = function() {
            const ids = Array.from(selectedAttributes);
            fetch('{{ route("admin.attributes.bulk-restore") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ ids: ids })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.error || 'Có lỗi xảy ra khi khôi phục thuộc tính');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi khôi phục thuộc tính');
            });
        };


        // Submit bulk force delete
        window.submitBulkForceDelete = function() {
            const ids = Array.from(selectedAttributes);
            fetch('{{ route("admin.attributes.bulk-force-delete") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ ids: ids })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.error || 'Có lỗi xảy ra khi xóa vĩnh viễn thuộc tính');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi xóa vĩnh viễn thuộc tính');
            });
        };
    });
</script>
@endpush
@endsection
