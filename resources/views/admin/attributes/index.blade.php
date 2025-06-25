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
                                        <a href="{{ route('admin.attributes.index', array_merge(request()->query(), ['sort_by' => 'id', 'sort_dir' => ($sortBy == 'id' && $sortDir == 'asc') ? 'desc' : 'asc'])) }}"
                                           class="text-decoration-none text-dark d-flex align-items-center">
                                            ID
                                            @if ($sortBy == 'id')
                                                <i class="bi bi-arrow-{{ $sortDir == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="border-0">
                                        <a href="{{ route('admin.attributes.index', array_merge(request()->query(), ['sort_by' => 'name', 'sort_dir' => ($sortBy == 'name' && $sortDir == 'asc') ? 'desc' : 'asc'])) }}"
                                           class="text-decoration-none text-dark d-flex align-items-center">
                                            Tên thuộc tính
                                            @if ($sortBy == 'name')
                                                <i class="bi bi-arrow-{{ $sortDir == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="border-0" style="width: 150px">
                                        <a href="{{ route('admin.attributes.index', array_merge(request()->query(), ['sort_by' => 'is_variant', 'sort_dir' => ($sortBy == 'is_variant' && $sortDir == 'asc') ? 'desc' : 'asc'])) }}"
                                           class="text-decoration-none text-dark d-flex align-items-center">
                                            Biến thể
                                            @if ($sortBy == 'is_variant')
                                                <i class="bi bi-arrow-{{ $sortDir == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="border-0" style="width: 120px">
                                        <a href="{{ route('admin.attributes.index', array_merge(request()->query(), ['sort_by' => 'is_active', 'sort_dir' => ($sortBy == 'is_active' && $sortDir == 'asc') ? 'desc' : 'asc'])) }}"
                                           class="text-decoration-none text-dark d-flex align-items-center">
                                            Trạng thái
                                            @if ($sortBy == 'is_active')
                                                <i class="bi bi-arrow-{{ $sortDir == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="border-0" style="width: 120px">
                                        <a href="{{ route('admin.attributes.index', array_merge(request()->query(), ['sort_by' => 'created_at', 'sort_dir' => ($sortBy == 'created_at' && $sortDir == 'asc') ? 'desc' : 'asc'])) }}"
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
                                            <span class="text-muted" data-bs-toggle="tooltip" title="{{ $attribute->created_at->format('H:i:s d/m/Y') }}">
                                                {{ $attribute->created_at->format('d/m/Y') }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="{{ route('admin.attribute_values.index', $attribute->id) }}"
                                                    class="btn btn-info btn-sm rounded-pill px-3"
                                                    data-bs-toggle="tooltip"
                                                    title="Quản lý giá trị">
                                                    <i class="bi bi-list-check"></i>
                                                </a>
                                                <a href="{{ route('admin.attributes.edit', $attribute->id) }}"
                                                    class="btn btn-warning btn-sm rounded-pill px-3"
                                                    data-bs-toggle="tooltip"
                                                    title="Chỉnh sửa">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <button type="button"
                                                        class="btn btn-danger btn-sm rounded-pill px-3"
                                                        onclick="confirmDelete('{{ $attribute->id }}', '{{ $attribute->name }}')"
                                                        data-bs-toggle="tooltip"
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
                        Bạn có chắc chắn muốn xóa <span id="bulkDeleteCount" class="fw-bold"></span> thuộc tính đã chọn?
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });


        const selectAllCheckbox = document.getElementById('selectAll');
        const attributeCheckboxes = document.querySelectorAll('.attribute-checkbox');
        const bulkDeleteBtn = document.querySelector('.bulk-delete-btn');
        const bulkToggleBtns = document.querySelectorAll('.bulk-toggle-btn');
        const selectedCounts = document.querySelectorAll('.selected-count');
        let selectedItems = [];


        // Cập nhật UI khi có checkbox được chọn
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


        // Xử lý khi checkbox được chọn
        function handleCheckboxChange(checkbox) {
            const attributeId = checkbox.value;
            if (checkbox.checked) {
                if (!selectedItems.includes(attributeId)) {
                    selectedItems.push(attributeId);
                }
            } else {
                selectedItems = selectedItems.filter(id => id !== attributeId);
                selectAllCheckbox.checked = false;
            }
            updateUI();
        }


        // Xử lý chọn tất cả
        selectAllCheckbox?.addEventListener('change', function() {
            attributeCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
                handleCheckboxChange(checkbox);
            });
        });


        // Xử lý chọn từng checkbox
        attributeCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                handleCheckboxChange(this);
            });
        });


        // Xử lý xóa hàng loạt
        bulkDeleteBtn?.addEventListener('click', function() {
            if (selectedItems.length === 0) {
                alert('Vui lòng chọn ít nhất một thuộc tính');
                return;
            }


            if (confirm('Bạn có chắc chắn muốn xóa các thuộc tính đã chọn?')) {
                fetch('{{ route('admin.attributes.bulk-delete') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ ids: selectedItems })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert(data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Đã có lỗi xảy ra');
                });
            }
        });
    });


    // Xử lý thay đổi trạng thái hàng loạt
    function bulkToggleStatus(status) {
        const selectedItems = Array.from(document.querySelectorAll('.attribute-checkbox:checked')).map(cb => cb.value);
       
        if (selectedItems.length === 0) {
            alert('Vui lòng chọn ít nhất một thuộc tính');
            return;
        }


        const statusText = status ? 'kích hoạt' : 'vô hiệu hóa';
        if (confirm(`Bạn có chắc chắn muốn ${statusText} các thuộc tính đã chọn?`)) {
            fetch('{{ route('admin.attributes.bulk-toggle') }}', {
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
                    window.location.reload();
                } else {
                    alert(data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Đã có lỗi xảy ra');
            });
        }
    }


    // Xác nhận xóa một thuộc tính
    function confirmDelete(id, name) {
        if (confirm(`Bạn có chắc chắn muốn xóa thuộc tính "${name}"?`)) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ url('admin/attributes') }}/${id}`;
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endpush

