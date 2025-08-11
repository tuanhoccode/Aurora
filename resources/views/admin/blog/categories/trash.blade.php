@extends('admin.layouts.app')

@section('title', 'Thùng rác - Danh mục bài viết')

@push('styles')
<style>
    .category-icon {
        font-size: 1.25rem;
        margin-right: 0.5rem;
    }
    .deleted-at {
        color: #dc3545;
        font-size: 0.875rem;
    }
</style>
@endpush

@section('content')
<div class="content">
    <nav class="mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.blog.categories.index') }}">Danh mục bài viết</a></li>
            <li class="breadcrumb-item active">Thùng rác</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="fas fa-trash-alt me-2"></i>Thùng rác
        </h2>
        <div>
            <a href="{{ route('admin.blog.categories.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách
            </a>
            
            @if($trashedCount > 0)
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#emptyTrashModal">
                <i class="fas fa-trash-alt me-1"></i> Làm trống thùng rác
            </button>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @if($categories->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="40">#</th>
                                <th>Tên danh mục</th>
                                <th>Đường dẫn</th>
                                <th>Đã xóa lúc</th>
                                <th class="text-end">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $category)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($category->icon)
                                            <i class="{{ $category->icon }} category-icon"></i>
                                        @else
                                            <i class="fas fa-folder category-icon text-muted"></i>
                                        @endif
                                        <div>
                                            <div>{{ $category->name }}</div>
                                            @if($category->posts_count > 0)
                                                <span class="badge bg-light text-dark">{{ $category->posts_count }} bài viết</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <code>/blog/category/{{ $category->slug }}</code>
                                </td>
                                <td>
                                    <span class="deleted-at">
                                        <i class="far fa-clock me-1"></i>
                                        {{ $category->deleted_at->diffForHumans() }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group" role="group">
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-success restore-category" 
                                                data-id="{{ $category->id }}"
                                                data-bs-toggle="tooltip" 
                                                title="Khôi phục">
                                            <i class="fas fa-trash-restore"></i> Khôi phục
                                        </button>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger force-delete-category" 
                                                data-id="{{ $category->id }}"
                                                data-name="{{ $category->name }}"
                                                data-bs-toggle="tooltip" 
                                                title="Xóa vĩnh viễn">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Hiển thị {{ $categories->firstItem() }} đến {{ $categories->lastItem() }} trong tổng số {{ $categories->total() }} mục
                    </div>
                    {{ $categories->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-trash-alt fa-4x text-muted"></i>
                    </div>
                    <h4 class="text-muted">Thùng rác trống</h4>
                    <p class="text-muted">Không có danh mục nào trong thùng rác</p>
                    <a href="{{ route('admin.blog.categories.index') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal xác nhận làm trống thùng rác -->
@if($trashedCount > 0)
<div class="modal fade" id="emptyTrashModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>Xác nhận xóa vĩnh viễn
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa vĩnh viễn tất cả ({{ $trashedCount }}) danh mục trong thùng rác?</p>
                <p class="text-danger mb-0"><strong>Cảnh báo:</strong> Hành động này không thể hoàn tác. Tất cả danh mục và dữ liệu liên quan sẽ bị xóa vĩnh viễn.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Hủy bỏ
                </button>
                <form action="{{ route('admin.blog.categories.empty-trash') }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-1"></i> Xóa vĩnh viễn tất cả
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Form ẩn để xử lý khôi phục -->
<form id="restoreForm" method="POST" style="display: none;">
    @csrf
    @method('PATCH')
</form>

<!-- Form ẩn để xử lý xóa vĩnh viễn -->
<form id="forceDeleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Khôi phục danh mục
        $('.restore-category').click(function() {
            const categoryId = $(this).data('id');
            const form = $('#restoreForm');
            form.attr('action', `/admin/blog/categories/${categoryId}/restore`);
            
            Swal.fire({
                title: 'Xác nhận khôi phục',
                text: 'Bạn có chắc chắn muốn khôi phục danh mục này?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Khôi phục',
                cancelButtonText: 'Hủy',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        // Xóa vĩnh viễn danh mục
        $('.force-delete-category').click(function() {
            const categoryId = $(this).data('id');
            const categoryName = $(this).data('name');
            const form = $('#forceDeleteForm');
            form.attr('action', `/admin/blog/categories/${categoryId}/force-delete`);
            
            Swal.fire({
                title: 'Xóa vĩnh viễn danh mục',
                html: `Bạn có chắc chắn muốn xóa vĩnh viễn danh mục <strong>${categoryName}</strong>?<br><br><span class="text-danger">Hành động này không thể hoàn tác!</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Xóa vĩnh viễn',
                cancelButtonText: 'Hủy',
                confirmButtonColor: '#dc3545',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
        
        // Kích hoạt tooltip
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>
@endpush
