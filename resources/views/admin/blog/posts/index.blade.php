@extends('admin.layouts.app')

@section('title', 'Quản lý bài viết')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Quản lý bài viết</h1>
        @if(Auth::user()->role === 'admin')
            <div class="d-flex gap-2">
                <a href="{{ route('admin.blog.posts.trash') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-trash"></i>
                    Thùng rác @if($trashedCount > 0)
                        <span class="badge bg-danger">{{ $trashedCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.blog.posts.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i>
                    Thêm bài viết
                </a>
            </div>
        @endif
    </div>

    {{-- Bulk Actions --}}
    <div class="bulk-actions bg-light rounded-3 p-3 mb-3" style="display: none;">
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-success bulk-action" data-action="activate">
                <i class="bi bi-check-circle"></i>
                Hoạt động
            </button>
            <button type="button" class="btn btn-warning bulk-action" data-action="deactivate">
                <i class="bi bi-x-circle"></i>
                Không hoạt động
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
            <form action="{{ route('admin.blog.posts.index') }}" method="GET" class="row g-3">
                <div class="col-12">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}"
                            placeholder="Tìm kiếm bài viết...">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i>
                        </button>
                        @if (request('search'))
                            <a href="{{ route('admin.blog.posts.index') }}" class="btn btn-light">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card shadow-sm rounded-3 border-0 mb-3">
        <div class="card-body">
            <form action="{{ route('admin.blog.posts.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <select class="form-select" name="category">
                        <option value="">Tất cả danh mục</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-8">
                    <div class="d-flex gap-2">
                        <select class="form-select" name="status">
                            <option value="">Tất cả trạng thái</option>
                            <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Đã xuất bản</option>
                            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Bản nháp</option>
                            <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Lên lịch</option>
                        </select>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-filter"></i>
                        </button>
                        @if (request()->hasAny(['category', 'author', 'status']))
                            <a href="{{ route('admin.blog.posts.index') }}" class="btn btn-light">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Posts Table --}}
    <div class="card shadow-sm rounded-3 border-0">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="40">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                </div>
                            </th>
                            <th width="80">Ảnh</th>
                            <th>Tiêu đề</th>
                            <th>Danh mục</th>
                            <th>Tác giả</th>
                            <th>Lượt xem</th>
                            <th>Trạng thái</th>
                            <th>Ngày đăng</th>
                            <th width="120">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($posts as $post)
                            <tr id="post-row-{{ $post->id }}">
                                <td>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input post-checkbox" value="{{ $post->id }}" id="post-{{ $post->id }}">
                                    </div>
                                </td>
                                <td>
                                    <img src="{{ $post->thumbnail ? asset('storage/' . $post->thumbnail) : 'https://via.placeholder.com/50' }}" 
                                         class="rounded shadow-sm" 
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                </td>
                                <td>
                                    <div class="fw-semibold text-primary">
                                        @if($post->slug)
                                            <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="text-decoration-none text-primary">
                                                {{ $post->title }}
                                            </a>
                                        @else
                                            {{ $post->title }}
                                        @endif
                                    </div>
                                    <div class="text-muted small">{{ $post->excerpt ?: Str::limit(strip_tags($post->content), 60) }}</div>
                                </td>
                                <td>
                                    @if($post->category)
                                        <span class="badge bg-info text-dark mb-1">{{ $post->category->name }}</span>
                                    @else
                                        <span class="text-muted">Chưa phân loại</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($post->author && $post->author->avatar)
                                            <img src="{{ asset('storage/' . $post->author->avatar) }}" 
                                                 alt="{{ $post->author->name }}" 
                                                 class="rounded-circle me-2" 
                                                 style="width: 30px; height: 30px; object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2" 
                                                 style="width: 30px; height: 30px; font-size: 12px;">
                                                {{ $post->author ? strtoupper(substr($post->author->name, 0, 1)) : '?' }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-medium">{{ $post->author->name ?? 'Không xác định' }}</div>
                                            <small class="text-muted">{{ $post->author->email ?? '' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ number_format($post->views) }}</td>
                                <td id="post-status-{{ $post->id }}">
                                    @if($post->is_active)
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i> Đang hiển thị
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-eye-slash me-1"></i> Đang ẩn
                                        </span>
                                    @endif
                                    
                                    @if($post->is_featured)
                                        <span class="badge bg-primary mt-1 d-block">
                                            <i class="bi bi-star-fill me-1"></i> Nổi bật
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="small text-muted">Tạo: {{ $post->created_at->format('d/m/Y H:i') }}</div>
                                    @if($post->published_at)
                                        <div class="small text-muted">Đăng: {{ $post->published_at->format('d/m/Y H:i') }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-link text-secondary p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <!-- Xem chi tiết -->
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.blog.posts.show', $post) }}">
                                                    <i class="bi bi-eye me-2"></i>Xem chi tiết
                                                </a>
                                            </li>
                                            
                                            @if(!$post->trashed())
                                                <!-- Chỉnh sửa -->
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.blog.posts.edit', $post) }}">
                                                        <i class="bi bi-pencil me-2"></i>Chỉnh sửa
                                                    </a>
                                                </li>
                                                
                                                <li><hr class="dropdown-divider"></li>
                                                
                                                <!-- Xóa -->
                                                <li>
                                                    <a href="#" class="dropdown-item text-danger" 
                                                       onclick="event.preventDefault(); if(confirm('Bạn có chắc chắn muốn chuyển bài viết vào thùng rác?')) { document.getElementById('delete-form-{{ $post->id }}').submit(); }">
                                                        <i class="bi bi-trash me-2"></i>Xóa
                                                    </a>
                                                    <form id="delete-form-{{ $post->id }}" 
                                                          action="{{ route('admin.blog.posts.destroy', $post) }}" 
                                                          method="POST" 
                                                          style="display: none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </li>
                                            @else
                                                <!-- Khôi phục -->
                                                <li>
                                                    <form action="{{ route('admin.blog.posts.restore', $post->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="bi bi-arrow-counterclockwise me-2"></i>Khôi phục
                                                        </button>
                                                    </form>
                                                </li>
                                                
                                                <li><hr class="dropdown-divider"></li>
                                                
                                                <!-- Xóa vĩnh viễn -->
                                                <li>
                                                    <form action="{{ route('admin.blog.posts.force-delete', $post->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn bài viết này?')">
                                                            <i class="bi bi-trash-fill me-2"></i>Xóa vĩnh viễn
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">Không có bài viết nào</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-3">
                        {{ $posts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa bài viết <strong id="deletePostTitle"></strong>?
                <p class="text-danger mt-2">Hành động này không thể hoàn tác!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa vĩnh viễn</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .post-actions .dropdown-toggle::after {
        display: none;
    }
    .table th {
        white-space: nowrap;
    }
</style>
@endpush

@push('scripts')
<script>
    function deleteItem(id, isTrashed, event) {
        event.preventDefault();
        if (confirm(isTrashed 
            ? 'Bạn có chắc chắn muốn xóa vĩnh viễn bài viết này? Hành động này không thể hoàn tác!'
            : 'Bạn có chắc chắn muốn chuyển bài viết vào thùng rác?')) {
            
            const url = '{{ url("admin/blog/posts") }}/' + id;
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
                        $(`#post-row-${id}`).fadeOut(300, function() {
                            $(this).remove();
                            // Kiểm tra nếu không còn dòng nào thì hiển thị thông báo
                            if ($('tbody tr').length === 1) { // Chỉ còn dòng thông báo
                                $('tbody').html('<tr><td colspan="9" class="text-center">Không có bài viết nào</td></tr>');
                            }
                        });
                    } else {
                        alert('Có lỗi xảy ra: ' + (response.message || 'Vui lòng thử lại sau'));
                    }
                },
                error: function(xhr) {
                    alert('Có lỗi xảy ra: ' + (xhr.responseJSON?.message || 'Vui lòng thử lại sau'));
                }
            });
        }
    }
    
    // Delete modal handler
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const postId = button.getAttribute('data-id');
            const postTitle = button.getAttribute('data-title');
            const form = deleteModal.querySelector('form');
            
            document.getElementById('deletePostTitle').textContent = '"' + postTitle + '"';
            form.action = '{{ url("admin/blog/posts") }}/' + postId;
        });
    }

    // Bulk actions
    $(document).ready(function() {
        // Select all checkbox
        $('#selectAll').change(function() {
            $('.post-checkbox').prop('checked', $(this).prop('checked'));
            toggleBulkActions();
        });

        // Individual checkbox
        $(document).on('change', '.post-checkbox', function() {
            if ($('.post-checkbox:checked').length === $('.post-checkbox').length) {
                $('#selectAll').prop('checked', true);
            } else {
                $('#selectAll').prop('checked', false);
            }
            toggleBulkActions();
        });

        // Toggle bulk actions visibility
        function toggleBulkActions() {
            if ($('.post-checkbox:checked').length > 0) {
                $('.bulk-actions').slideDown();
            } else {
                $('.bulk-actions').slideUp();
            }
        }

        // Cancel bulk actions
        $('.cancel-bulk').click(function() {
            $('.post-checkbox, #selectAll').prop('checked', false);
            $('.bulk-actions').slideUp();
        });

        // Bulk actions
        $('.bulk-action').click(function() {
            const action = $(this).data('action');
            const postIds = [];
            $('.post-checkbox:checked').each(function() {
                postIds.push($(this).val());
            });

            if (postIds.length === 0) {
                alert('Vui lòng chọn ít nhất một bài viết');
                return;
            }

            let confirmMessage = '';
            let formAction = '';
            
            switch(action) {
                case 'activate':
                    confirmMessage = 'Bạn có chắc muốn kích hoạt ' + postIds.length + ' bài viết đã chọn?';
                    formAction = '{{ route("admin.blog.posts.bulk-activate") }}';
                    break;
                case 'deactivate':
                    confirmMessage = 'Bạn có chắc muốn tắt ' + postIds.length + ' bài viết đã chọn?';
                    formAction = '{{ route("admin.blog.posts.bulk-deactivate") }}';
                    break;
                case 'delete':
                    confirmMessage = 'Bạn có chắc muốn xóa ' + postIds.length + ' bài viết đã chọn? Hành động này không thể hoàn tác!';
                    formAction = '{{ route("admin.blog.posts.bulk-delete") }}';
                    break;
            }

            if (confirm(confirmMessage)) {
                // Tạo form ẩn để submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = formAction;
                form.style.display = 'none';
                
                // Thêm CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                
                // Thêm các ID bài viết
                postIds.forEach(function(id) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = id;
                    form.appendChild(input);
                });
                
                // Thêm form vào body và submit
                document.body.appendChild(form);
                form.submit();
            }
        });
    });    
</script>
@endpush
