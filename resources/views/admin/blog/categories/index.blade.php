@extends('admin.layouts.app')

@section('title', 'Quản lý danh mục bài viết')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Quản lý danh mục bài viết</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.blog.categories.index', ['is_active' => 'trashed']) }}" class="btn btn-outline-secondary">
            <i class="bi bi-trash"></i>
            Thùng rác @if($stats['trashed'] > 0)
                <span class="badge bg-danger">{{ $stats['trashed'] }}</span>
            @endif
        </a>
        <a href="{{ route('admin.blog.categories.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i>
            Thêm danh mục
        </a>
    </div>
</div>

{{-- Bulk Actions --}}
<div class="bulk-actions bg-light rounded-3 p-3 mb-3" style="display: none;">
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-success bulk-action" data-action="activate">
            <i class="bi bi-check-circle"></i>
            Kích hoạt
        </button>
        <button type="button" class="btn btn-warning bulk-action" data-action="deactivate">
            <i class="bi bi-eye-slash"></i>
            Tạm ẩn
        </button>
        <button type="button" class="btn btn-danger bulk-action" data-action="delete">
            <i class="bi bi-trash"></i>
            Xóa
        </button>
        <button type="button" class="btn btn-light ms-auto cancel-bulk">
            <i class="bi bi-x-lg"></i>
            Hủy
        </button>
    </div>
</div>

{{-- Search --}}
<div class="card shadow-sm rounded-3 border-0 mb-3">
    <div class="card-body">
        <form action="{{ route('admin.blog.categories.index') }}" method="GET">
            <div class="input-group">
                <input type="text" class="form-control form-control-lg" name="search" value="{{ request('search') }}"
                    placeholder="Nhập từ khóa tìm kiếm...">
                <button class="btn btn-primary px-4" type="submit">
                    <i class="bi bi-search me-1"></i> Tìm kiếm
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.blog.categories.index', request()->except('search')) }}" class="btn btn-light" title="Xóa tìm kiếm">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Filters --}}
<div class="card shadow-sm rounded-3 border-0 mb-3">
    <div class="card-body">
        <form action="{{ route('admin.blog.categories.index') }}" method="GET" class="row g-3">
            <!-- Giữ lại tham số tìm kiếm nếu có -->
            @if(request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
            
            <div class="col-md-5">
                <select name="status" class="form-select">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Hoạt động</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
                </select>
            </div>
            
            <div class="col-md-5">
                <select name="parent_id" class="form-select">
                    <option value="">-- Tất cả danh mục cha --</option>
                    @foreach($parentCategories as $parent)
                        <option value="{{ $parent->id }}" {{ request('parent_id') == $parent->id ? 'selected' : '' }}>
                            {{ $parent->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-outline-primary flex-grow-1">
                    <i class="bi bi-funnel me-1"></i> Lọc
                </button>
                @if(request()->hasAny(['status', 'parent_id']))
                    <a href="{{ route('admin.blog.categories.index', ['search' => request('search')]) }}" class="btn btn-outline-secondary" title="Đặt lại bộ lọc">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Categories Table --}}
<div class="card shadow-sm rounded-3 border-0">
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="40">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="selectAll">
                            </div>
                        </th>
                        <th>Tên danh mục</th>
                        <th>Danh mục cha</th>
                        <th class="text-center">Bài viết</th>
                        <th class="text-center">Trạng thái</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="ids[]" value="{{ $category->id }}">
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                                        <i class="fas fa-folder text-secondary"></i>
                                    </div>
                                    <div class="fw-semibold">{{ $category->name }}</div>
                                </div>
                            </td>
                            <td>
                                @if($category->parent)
                                    <a href="{{ route('admin.blog.categories.show', $category->parent_id) }}" class="text-primary">
                                        <i class="{{ $category->parent->icon ?? 'fas fa-folder' }} me-1"></i>
                                        {{ $category->parent->name }}
                                    </a>
                                @else
                                    <span class="text-muted">Không có</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary rounded-pill">{{ $category->posts_count }}</span>
                            </td>
                            <td class="text-center">
                                @if($category->trashed())
                                    <span class="badge bg-danger">Đã xóa</span>
                                @else
                                    <span class="badge bg-{{ $category->is_active ? 'success' : 'secondary' }}">
                                        {{ $category->is_active ? 'Hoạt động' : 'Không hoạt động' }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="dropdown d-inline-block">
                                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        @if($category->trashed())
                                            <li>
                                                <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('restore-form-{{ $category->id }}').submit();">
                                                    <i class="bi bi-arrow-counterclockwise me-2"></i>Khôi phục
                                                </a>
                                                <form id="restore-form-{{ $category->id }}" 
                                                    action="{{ route('admin.blog.categories.restore', $category) }}" 
                                                    method="POST" 
                                                    style="display: none;">
                                                    @csrf
                                                    @method('PATCH')
                                                </form>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal" 
                                                   data-id="{{ $category->id }}" 
                                                   data-name="{{ $category->name }}"
                                                   data-permanent="true">
                                                    <i class="bi bi-trash me-2"></i>Xóa vĩnh viễn
                                                </a>
                                            </li>
                                        @else
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.blog.categories.show', $category) }}">
                                                    <i class="bi bi-eye me-2"></i>Xem chi tiết
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.blog.categories.edit', $category) }}">
                                                    <i class="bi bi-pencil me-2"></i>Chỉnh sửa
                                                </a>
                                            </li>
                                            @if($category->is_active)
                                                <li>
                                                    <a class="dropdown-item text-warning" href="#" onclick="event.preventDefault(); document.getElementById('deactivate-form-{{ $category->id }}').submit();">
                                                        <i class="bi bi-eye-slash me-2"></i>Tạm ẩn
                                                    </a>
                                                    <form id="deactivate-form-{{ $category->id }}" 
                                                        action="{{ route('admin.blog.categories.update', $category) }}" 
                                                        method="POST" 
                                                        style="display: none;">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="is_active" value="0">
                                                    </form>
                                                </li>
                                            @else
                                                <li>
                                                    <a class="dropdown-item text-success" href="#" onclick="event.preventDefault(); document.getElementById('activate-form-{{ $category->id }}').submit();">
                                                        <i class="bi bi-eye me-2"></i>Kích hoạt
                                                    </a>
                                                    <form id="activate-form-{{ $category->id }}" 
                                                        action="{{ route('admin.blog.categories.update', $category) }}" 
                                                        method="POST" 
                                                        style="display: none;">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="is_active" value="1">
                                                    </form>
                                                </li>
                                            @endif
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal" 
                                                   data-id="{{ $category->id }}" 
                                                   data-name="{{ $category->name }}">
                                                    <i class="bi bi-trash me-2"></i>Xóa
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <p class="mb-0">Không có danh mục nào</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
            <div class="mt-3">
                {{ $categories->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa danh mục <strong id="categoryName"></strong>?
                <p class="text-danger mt-2" id="permanentDeleteWarning" style="display: none;">
                    <i class="fas fa-exclamation-triangle me-1"></i> Hành động này sẽ xóa vĩnh viễn danh mục và không thể khôi phục!
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-1"></i> Xác nhận xóa
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Xử lý modal xóa
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const categoryId = button.getAttribute('data-id');
            const categoryName = button.getAttribute('data-name');
            const isPermanent = button.getAttribute('data-permanent') === 'true';
            
            const modalTitle = deleteModal.querySelector('.modal-title');
            const categoryNameElement = deleteModal.querySelector('#categoryName');
            const deleteForm = deleteModal.querySelector('#deleteForm');
            const permanentDeleteWarning = deleteModal.querySelector('#permanentDeleteWarning');
            $('#categoryName').text(name);
            $('#deleteForm').attr('action', url);
            var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        });
        
        // Handle restore button click
        $(document).on('click', '.restore-category', function() {
            var id = $(this).data('id');
            var name = $(this).closest('tr').find('td:eq(1)').text().trim();
            var url = '{{ route("admin.blog.categories.restore", ":id") }}';
            url = url.replace(':id', id);
            
            $('#restoreCategoryName').text(name);
            $('#restoreForm').attr('action', url);
            var restoreModal = new bootstrap.Modal(document.getElementById('restoreModal'));
            restoreModal.show();
        });
        
        // Handle force delete button click
        $(document).on('click', '.force-delete-category', function() {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var url = '{{ route("admin.blog.categories.force-delete", ":id") }}';
            url = url.replace(':id', id);
            
            $('#forceDeleteCategoryName').text(name);
            $('#forceDeleteForm').attr('action', url);
            var forceDeleteModal = new bootstrap.Modal(document.getElementById('forceDeleteModal'));
            forceDeleteModal.show();
        });
    };
    
    // Xử lý bulk actions
    const bulkActions = document.querySelector('.bulk-actions');
    if (bulkActions) {
        const checkboxes = document.querySelectorAll('input[name="ids[]"]');
        const selectAllCheckbox = document.getElementById('selectAll');
        
        // Handle select all checkbox click
        selectAllCheckbox.addEventListener('change', function() {
            checkboxes.forEach(function(checkbox) {
                checkbox.checked = selectAllCheckbox.checked;
            });
        });
        
        // Handle bulk action button click
        const bulkActionButtons = bulkActions.querySelectorAll('.bulk-action');
        bulkActionButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const action = button.getAttribute('data-action');
                const ids = [];
                checkboxes.forEach(function(checkbox) {
                    if (checkbox.checked) {
                        ids.push(checkbox.value);
                    }
                });
                
                // Xử lý bulk action
                switch (action) {
                    case 'activate':
                        // Kích hoạt danh mục
                        break;
                    case 'deactivate':
                        // Tạm ẩn danh mục
                        break;
                    case 'delete':
                        // Xóa danh mục
                        break;
                }
            });
        });
    };
</script>
@endpush
