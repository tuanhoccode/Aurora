@extends('admin.layouts.app')

@section('title', 'Quản lý danh mục bài viết')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Quản lý danh mục bài viết</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.blog.categories.trash') }}" class="btn btn-outline-secondary">
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
                                            <li><span class="dropdown-item-text text-muted">Vui lòng vào thùng rác để thao tác</span></li>
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
                                         
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a href="#" class="dropdown-item text-danger" 
                                                    onclick="event.preventDefault(); if(confirm('Bạn có chắc chắn muốn chuyển danh mục vào thùng rác?')) { document.getElementById('delete-form-{{ $category->id }}').submit(); }">
                                                    <i class="bi bi-trash me-2"></i>Xóa
                                                </a>
                                                <form id="delete-form-{{ $category->id }}" 
                                                    action="{{ route('admin.blog.categories.destroy', $category) }}" 
                                                    method="POST" 
                                                    style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
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
    // Xử lý xóa danh mục (soft delete)
    function deleteCategory(id, isPermanent = false, event) {
        event.preventDefault();
        
        if (confirm(isPermanent 
            ? 'Bạn có chắc chắn muốn xóa vĩnh viễn danh mục này? Hành động này không thể hoàn tác!'
            : 'Bạn có chắc chắn muốn chuyển danh mục vào thùng rác?')) {
            
            const url = '{{ url("admin/blog/categories") }}/' + id;
            const token = '{{ csrf_token() }}';
            
            // Gửi yêu cầu xóa bằng AJAX
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: token,
                    _method: 'DELETE'
                },
                success: function(response) {
                    if (response.success) {
                        // Ẩn dòng đã xóa
                        $(`#category-row-${id}`).fadeOut(300, function() {
                            $(this).remove();
                            // Kiểm tra nếu không còn dòng nào thì hiển thị thông báo
                            if ($('tbody tr').length === 0) {
                                $('tbody').html('<tr><td colspan="7" class="text-center py-4"><div class="text-muted"><i class="fas fa-inbox fa-2x mb-2"></i><p>Không có danh mục nào</p></div></td></tr>');
                            }
                        });
                        
                        showToast('success', response.message || 'Đã xóa danh mục thành công');
                    } else {
                        showToast('error', response.message || 'Có lỗi xảy ra khi xóa danh mục');
                    }
                },
                error: function(xhr) {
                    const errorMessage = xhr.responseJSON?.message || 'Có lỗi xảy ra khi kết nối đến máy chủ';
                    showToast('error', errorMessage);
                }
            });
        }
    }
    
    
    // Hàm hiển thị thông báo
    function showToast(type, message) {
        const toast = $(`
            <div class="toast align-items-center text-white bg-${type} border-0 position-fixed bottom-0 end-0 m-3" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `);
        
        $('body').append(toast);
        const bsToast = new bootstrap.Toast(toast[0]);
        bsToast.show();
        
        // Tự động ẩn thông báo sau 3 giây
        setTimeout(() => {
            bsToast.hide();
            toast.remove();
        }, 3000);
    }
    
    // Hàm làm mới bảng
    function refreshTable(callback) {
        const $tableBody = $('table tbody');
        const $pagination = $('.pagination');
        const currentUrl = window.location.href;
        
        // Hiển thị loading
        $tableBody.html(`
            <tr>
                <td colspan="7" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Đang tải...</span>
                    </div>
                    <p class="mt-2 mb-0">Đang tải dữ liệu...</p>
                </td>
            </tr>
        `);
        
        // Lấy lại dữ liệu từ server
        $.get(currentUrl, function(response) {
            // Trích xuất nội dung bảng mới từ response
            const newContent = $(response).find('table tbody').html();
            const newPagination = $(response).find('.pagination').html() || '';
            
            // Cập nhật nội dung bảng
            $tableBody.html(newContent || `
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <div class="text-muted">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <p>Không có danh mục nào</p>
                        </div>
                    </td>
                </tr>
            `);
            
            // Cập nhật phân trang
            if (newPagination) {
                $pagination.html(newPagination);
            } else {
                $pagination.empty();
            }
            
            // Gọi callback nếu có
            if (typeof callback === 'function') {
                callback();
            }
        }).fail(function() {
            showToast('error', 'Không thể tải lại dữ liệu');
            $tableBody.html(`
                <tr>
                    <td colspan="7" class="text-center py-4 text-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Không thể tải dữ liệu. Vui lòng thử lại.
                    </td>
                </tr>
            `);
        });
    }
    
    // Hàm kiểm tra và hiển thị bulk actions
    function toggleBulkActions() {
        const anyChecked = $('input[name="ids[]"]:checked').length > 0;
        $('.bulk-actions').toggle(anyChecked);
        
        // Kiểm tra nếu tất cả các checkbox đều được chọn
        const allChecked = $('input[name="ids[]"]').length === $('input[name="ids[]"]:checked').length;
        $('#selectAll').prop('checked', allChecked);
    }
    
    // Xử lý bulk actions
    $(document).ready(function() {
        // Select all checkbox
        $('#selectAll').change(function() {
            $('input[name="ids[]"]').prop('checked', this.checked);
            toggleBulkActions();
        });
        
        // Toggle bulk actions khi chọn các checkbox
        $(document).on('change', 'input[name="ids[]"]', function() {
            toggleBulkActions();
        });
        
        // Hủy chọn tất cả
        $('.cancel-bulk').click(function() {
            $('.form-check-input').prop('checked', false);
            $('.bulk-actions').hide();
        });
        
        // Xử lý bulk actions
        $('.bulk-action').click(function() {
            const action = $(this).data('action');
            const ids = [];
            
            $('.form-check-input:checked').not('#selectAll').each(function() {
                ids.push($(this).val());
            });
            
            if (ids.length === 0) {
                showToast('warning', 'Vui lòng chọn ít nhất một danh mục');
                return;
            }
            
            let confirmMessage = '';
            let url = '';
            
            switch (action) {
                case 'activate':
                    confirmMessage = 'Bạn có chắc chắn muốn kích hoạt ' + ids.length + ' danh mục đã chọn?';
                    url = '{{ route("admin.blog.categories.bulk-activate") }}';
                    break;
                case 'deactivate':
                    confirmMessage = 'Bạn có chắc chắn muốn tắt ' + ids.length + ' danh mục đã chọn?';
                    url = '{{ route("admin.blog.categories.bulk-deactivate") }}';
                    break;
                case 'delete':
                    confirmMessage = 'Bạn có chắc chắn muốn xóa ' + ids.length + ' danh mục đã chọn?';
                    url = '{{ route("admin.blog.categories.bulk-destroy") }}';
                    break;
            }
            
            if (!confirm(confirmMessage)) {
                return;
            }
            
            // Hiển thị loading
            const $submitButton = $(this);
            const originalText = $submitButton.html();
            $submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang xử lý...');
            
            // Xác định method dựa trên action
            const method = (action === 'delete' || action === 'destroy') ? 'DELETE' : 'POST';
            
            $.ajax({
                url: url,
                type: method,
                data: {
                    _token: '{{ csrf_token() }}',
                    ids: ids
                },
                success: function(response) {
                    if (response.success) {
                        // Làm mới bảng bằng cách gọi lại dữ liệu
                        refreshTable(function() {
                            showToast('success', response.message || 'Thao tác thành công');
                        });
                    } else {
                        showToast('error', response.message || 'Có lỗi xảy ra');
                    }
                },
                error: function(xhr) {
                    const errorMessage = xhr.responseJSON?.message || 'Có lỗi xảy ra khi kết nối đến máy chủ';
                    showToast('error', errorMessage);
                },
                complete: function() {
                    $submitButton.prop('disabled', false).html(originalText);
                    // Ẩn bulk actions
                    $('.bulk-actions').hide();
                    // Bỏ chọn tất cả checkbox
                    $('.form-check-input').prop('checked', false);
                }
            });
        });
    });
</script>
@endpush
