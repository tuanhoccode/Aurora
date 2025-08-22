@extends('admin.layouts.app')

@section('title', 'Thùng rác - Danh mục bài viết')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Thùng rác danh mục bài viết</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.blog.categories.index') }}">Quản lý danh mục</a></li>
                <li class="breadcrumb-item active">Thùng rác</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="{{ route('admin.blog.categories.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách
        </a>
    </div>
</div>

<div class="card shadow-sm rounded-3 border-0">
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($categories->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tên danh mục</th>
                            <th>Danh mục cha</th>
                            <th>Bài viết</th>
                            <th>Trạng thái</th>
                            <th>Đã xóa lúc</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($category->icon)
                                        <i class="{{ $category->icon }} me-2"></i>
                                    @else
                                        <i class="fas fa-folder me-2 text-muted"></i>
                                    @endif
                                    <div>
                                        <div class="fw-medium">{{ $category->name }}</div>
                                        <div class="small text-muted">
                                            {{ $category->slug }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($category->parent)
                                    <a href="{{ route('admin.blog.categories.show', $category->parent) }}" class="text-decoration-none">
                                        {{ $category->parent->name }}
                                    </a>
                                @else
                                    <span class="text-muted">Không có</span>
                                @endif
                            </td>
                            <td>
                                @if($category->posts_count > 0)
                                    <span class="badge bg-info text-dark">{{ $category->posts_count }} bài viết</span>
                                @else
                                    <span class="text-muted">Không có</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary">Đã xóa</span>
                            </td>
                            <td>
                                <div class="small">{{ $category->deleted_at->format('d/m/Y H:i') }}</div>
                                <div class="small text-muted">
                                    {{ $category->deleted_at->diffForHumans() }}
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="dropdown">
                                    <button class="btn btn-link text-secondary p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <button type="button" class="dropdown-item text-success restore-category" data-id="{{ $category->id }}">
                                                <i class="bi bi-arrow-counterclockwise me-2"></i>Khôi phục
                                            </button>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <button type="button" class="dropdown-item text-danger delete-forever" data-id="{{ $category->id }}">
                                                <i class="bi bi-trash me-2"></i>Xóa vĩnh viễn
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $categories->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="bi bi-trash display-1 text-muted"></i>
                </div>
                <h5 class="text-muted">Thùng rác trống</h5>
                <p class="text-muted">Không có danh mục nào trong thùng rác</p>
                <a href="{{ route('admin.blog.categories.index') }}" class="btn btn-primary mt-3">
                    <i class="bi bi-arrow-left"></i>
                    Quay lại danh sách danh mục
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Empty Trash Confirmation Modal -->
@if($trashedCount > 0)
<div class="modal fade" id="emptyTrashModal" tabindex="-1" aria-labelledby="emptyTrashModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="emptyTrashModalLabel">Xác nhận làm trống thùng rác</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa vĩnh viễn tất cả danh mục trong thùng rác? Hành động này không thể hoàn tác!
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form action="{{ route('admin.blog.categories.trash.empty') }}" method="POST" id="emptyTrashForm">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa vĩnh viễn tất cả</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteForeverModal" tabindex="-1" aria-labelledby="deleteForeverModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteForeverModalLabel">Xác nhận xóa vĩnh viễn</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa vĩnh viễn danh mục này? Hành động này không thể hoàn tác!
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form id="deleteForeverForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa vĩnh viễn</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Hidden form for restore -->
<form id="restoreForm" method="POST" style="display: none;">
    @csrf
    @method('PATCH')
</form>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check/Uncheck all
        const checkAll = document.getElementById('checkAll');
        if (checkAll) {
            checkAll.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.category-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = checkAll.checked;
                });
            });
        }

        // Empty trash button
        const emptyTrashBtn = document.getElementById('emptyTrashBtn');
        if (emptyTrashBtn) {
            emptyTrashBtn.addEventListener('click', function() {
                const emptyTrashModal = new bootstrap.Modal(document.getElementById('emptyTrashModal'));
                emptyTrashModal.show();
            });
        }

        // Restore category
        const restoreButtons = document.querySelectorAll('.restore-category');
        restoreButtons.forEach(button => {
            button.addEventListener('click', function() {
                const categoryId = this.getAttribute('data-id');
                if (confirm('Bạn có chắc chắn muốn khôi phục danh mục này?')) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/admin/blog/categories/${categoryId}/restore`;
                    
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'PATCH';
                    
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;
                    
                    form.appendChild(methodInput);
                    form.appendChild(csrfInput);
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });

        // Delete forever
        const deleteForeverButtons = document.querySelectorAll('.delete-forever');
        const deleteForeverForm = document.getElementById('deleteForeverForm');
        
        deleteForeverButtons.forEach(button => {
            button.addEventListener('click', function() {
                const categoryId = this.getAttribute('data-id');
                deleteForeverForm.action = `/admin/blog/categories/${categoryId}/force-delete`;
                
                const deleteForeverModal = new bootstrap.Modal(document.getElementById('deleteForeverModal'));
                deleteForeverModal.show();
            });
        });
    });
</script>
@endpush
