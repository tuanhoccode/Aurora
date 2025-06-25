@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        {{-- Header Section --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 fw-bold text-gray-800">Giá trị thuộc tính: {{ $attribute->name }}</h1>
                <p class="text-muted mt-1">
                    Quản lý các giá trị của thuộc tính {{ $attribute->is_variant ? '(Biến thể)' : '' }}
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.attributes.index') }}" class="btn btn-secondary shadow-sm rounded-pill px-4">
                    <i class="bi bi-arrow-left me-1"></i> Quay lại
                </a>
                <a href="{{ route('admin.attribute_values.create', $attribute->id) }}" class="btn btn-primary shadow-sm rounded-pill px-4">
                    <i class="bi bi-plus-circle me-1"></i> Thêm mới
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
                        <form action="{{ route('admin.attribute_values.index', $attribute->id) }}" method="GET" class="d-flex gap-2">
                            <div class="input-group">
                                <input type="text" 
                                       name="search" 
                                       class="form-control"
                                       placeholder="Tìm kiếm giá trị..." 
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

                @if ($values->count())
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
                                        <a href="{{ route('admin.attribute_values.index', array_merge(['attributeId' => $attribute->id], request()->query(), ['sort_by' => 'id', 'sort_dir' => ($sortBy == 'id' && $sortDir == 'asc') ? 'desc' : 'asc'])) }}"
                                           class="text-decoration-none text-dark d-flex align-items-center">
                                            ID
                                            @if ($sortBy == 'id')
                                                <i class="bi bi-arrow-{{ $sortDir == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="border-0">
                                        <a href="{{ route('admin.attribute_values.index', array_merge(['attributeId' => $attribute->id], request()->query(), ['sort_by' => 'value', 'sort_dir' => ($sortBy == 'value' && $sortDir == 'asc') ? 'desc' : 'asc'])) }}"
                                           class="text-decoration-none text-dark d-flex align-items-center">
                                            Giá trị
                                            @if ($sortBy == 'value')
                                                <i class="bi bi-arrow-{{ $sortDir == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="border-0" style="width: 120px">
                                        <a href="{{ route('admin.attribute_values.index', array_merge(['attributeId' => $attribute->id], request()->query(), ['sort_by' => 'is_active', 'sort_dir' => ($sortBy == 'is_active' && $sortDir == 'asc') ? 'desc' : 'asc'])) }}"
                                           class="text-decoration-none text-dark d-flex align-items-center">
                                            Trạng thái
                                            @if ($sortBy == 'is_active')
                                                <i class="bi bi-arrow-{{ $sortDir == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="border-0" style="width: 120px">
                                        <a href="{{ route('admin.attribute_values.index', array_merge(['attributeId' => $attribute->id], request()->query(), ['sort_by' => 'created_at', 'sort_dir' => ($sortBy == 'created_at' && $sortDir == 'asc') ? 'desc' : 'asc'])) }}"
                                           class="text-decoration-none text-dark d-flex align-items-center">
                                            Ngày tạo
                                            @if ($sortBy == 'created_at')
                                                <i class="bi bi-arrow-{{ $sortDir == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="border-0 text-end" style="width: 120px">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($values as $value)
                                    <tr class="position-relative">
                                        <td>
                                            <div class="form-check">
                                                <input type="checkbox" 
                                                       class="form-check-input value-checkbox" 
                                                       value="{{ $value->id }}"
                                                       data-value="{{ $value->value }}">
                                            </div>
                                        </td>
                                        <td class="text-muted">{{ $value->id }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="fw-medium">{{ $value->value }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill {{ $value->is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} px-3 py-2">
                                                <i class="bi bi-circle-fill me-1 small"></i>
                                                {{ $value->is_active ? 'Đang hoạt động' : 'Không hoạt động' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="text-muted" data-bs-toggle="tooltip" title="{{ $value->created_at->format('H:i:s d/m/Y') }}">
                                                {{ $value->created_at->format('d/m/Y') }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="{{ route('admin.attribute_values.edit', [$attribute->id, $value->id]) }}"
                                                    class="btn btn-warning btn-sm rounded-pill px-3" 
                                                    data-bs-toggle="tooltip" 
                                                    title="Chỉnh sửa">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-danger btn-sm rounded-pill px-3" 
                                                        onclick="confirmDelete('{{ $value->id }}', '{{ $value->value }}')"
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
                    @if ($values->hasPages())
                        <div class="d-flex justify-content-end mt-3">
                            {{ $values->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-box display-1 text-muted"></i>
                        <p class="text-muted mt-3">Không tìm thấy giá trị nào.</p>
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
                        Bạn có chắc chắn muốn xóa giá trị "<span id="deleteValueName" class="fw-bold"></span>"?
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
                        Bạn có chắc chắn muốn xóa <span id="bulkDeleteCount" class="fw-bold"></span> giá trị đã chọn?
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
        const valueCheckboxes = document.querySelectorAll('.value-checkbox');
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
            const valueId = checkbox.value;
            if (checkbox.checked) {
                if (!selectedItems.includes(valueId)) {
                    selectedItems.push(valueId);
                }
            } else {
                selectedItems = selectedItems.filter(id => id !== valueId);
                selectAllCheckbox.checked = false;
            }
            updateUI();
        }

        // Xử lý chọn tất cả
        selectAllCheckbox?.addEventListener('change', function() {
            valueCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
                handleCheckboxChange(checkbox);
            });
        });

        // Xử lý chọn từng checkbox
        valueCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                handleCheckboxChange(this);
            });
        });

        // Xử lý xóa hàng loạt
        bulkDeleteBtn?.addEventListener('click', function() {
            if (selectedItems.length === 0) {
                alert('Vui lòng chọn ít nhất một giá trị');
                return;
            }

            if (confirm('Bạn có chắc chắn muốn xóa các giá trị đã chọn?')) {
                fetch('{{ route('admin.attribute_values.bulk-delete', $attribute->id) }}', {
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
        const selectedItems = Array.from(document.querySelectorAll('.value-checkbox:checked')).map(cb => cb.value);
        
        if (selectedItems.length === 0) {
            alert('Vui lòng chọn ít nhất một giá trị');
            return;
        }

        const statusText = status ? 'kích hoạt' : 'vô hiệu hóa';
        if (confirm(`Bạn có chắc chắn muốn ${statusText} các giá trị đã chọn?`)) {
            fetch('{{ route('admin.attribute_values.bulk-toggle', $attribute->id) }}', {
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

    // Xác nhận xóa một giá trị
    function confirmDelete(id, value) {
        if (confirm(`Bạn có chắc chắn muốn xóa giá trị "${value}"?`)) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ url('admin/attributes') }}/{{ $attribute->id }}/values/${id}`;
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