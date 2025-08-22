@extends('admin.layouts.app')

@section('title', 'Thùng rác bài viết')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Thùng rác bài viết</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.blog.posts.index') }}">Quản lý bài viết</a></li>
            </a>
        </div>
    </div>

    {{-- Bulk Actions --}}
    <div class="bulk-actions bg-light rounded-3 p-3 mb-3" style="display: none;">
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-success bulk-action" data-action="restore">
                <i class="bi bi-arrow-counterclockwise"></i>
                Khôi phục
            </button>
            <button type="button" class="btn btn-danger bulk-action" data-action="force-delete">
                <i class="bi bi-trash"></i>
                Xóa vĩnh viễn
            </button>
            <button type="button" class="btn btn-light ms-auto cancel-bulk">
                <i class="bi bi-x-lg"></i>
                Hủy
            </button>
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

            <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="40">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="checkAll">
                                    </div>
                                </th>
                                <th>Tiêu đề</th>
                                <th>Tác giả</th>
                                <th>Danh mục</th>
                                <th>Lượt xem</th>
                                <th>Trạng thái</th>
                                <th>Ngày xóa</th>
                                <th class="text-end">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trashedPosts as $post)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input post-checkbox" value="{{ $post->id }}" id="post-{{ $post->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($post->featured_image)
                                                <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}" class="rounded me-2" width="60" height="40" style="object-fit: cover;">
                                            @endif
                                            <div>
                                                <div class="fw-medium">{{ $post->title }}</div>
                                                <div class="small text-muted">
                                                    {{ $post->slug }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($post->author)
                                            <div class="d-flex align-items-center">
                                                @if($post->author->avatar)
                                                    <img src="{{ asset('storage/' . $post->author->avatar) }}" 
                                                         alt="{{ $post->author->fullname }}" 
                                                         class="rounded-circle me-2" 
                                                         width="32" 
                                                         height="32" 
                                                         style="object-fit: cover;">
                                                @else
                                                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2" 
                                                         style="width: 32px; height: 32px;">
                                                        {{ substr($post->author->fullname, 0, 1) }}
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="fw-medium">{{ $post->author->fullname }}</div>
                                                    <small class="text-muted">{{ $post->author->email }}</small>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">Không xác định</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($post->category)
                                            <span class="badge bg-info text-dark">{{ $post->category->name }}</span>
                                        @else
                                            <span class="text-muted">Chưa phân loại</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-eye me-1"></i>
                                            <span>{{ number_format($post->views) }}</span>
                                            <small class="text-muted ms-1">lượt xem</small>
                                        </div>
                                    </td>
                                    <td>
                                        @if($post->is_published)
                                            <span class="badge bg-success">Đã xuất bản</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Bản nháp</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="small">{{ $post->deleted_at->format('d/m/Y H:i') }}</div>
                                        <div class="small text-muted">
                                            {{ $post->deleted_at->diffForHumans() }}
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <div class="dropdown">
                                            <button class="btn btn-link text-secondary p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="bi bi-three-dots"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <button type="button" class="dropdown-item text-success restore-post" data-id="{{ $post->id }}">
                                                        <i class="bi bi-arrow-counterclockwise me-2"></i>Khôi phục
                                                    </button>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <button type="button" class="dropdown-item text-danger delete-forever" data-id="{{ $post->id }}">
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
                    {{ $trashedPosts->links() }}
                </div>
            @if($trashedPosts->isEmpty())
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="bi bi-trash display-1 text-muted"></i>
                    </div>
                    <h5 class="text-muted">Thùng rác trống</h5>
                    <p class="text-muted">Không có bài viết nào trong thùng rác</p>
                    <a href="{{ route('admin.blog.posts.index') }}" class="btn btn-primary mt-3">
                        <i class="bi bi-arrow-left"></i>
                        Quay lại danh sách bài viết
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Empty Trash Confirmation Modal -->
    <div class="modal fade" id="emptyTrashModal" tabindex="-1" aria-labelledby="emptyTrashModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="emptyTrashModalLabel">Xác nhận làm trống thùng rác</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Bạn có chắc chắn muốn xóa vĩnh viễn tất cả bài viết trong thùng rác? Hành động này không thể hoàn tác!
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <form action="{{ route('admin.blog.posts.empty-trash') }}" method="POST" id="emptyTrashForm">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Xóa vĩnh viễn tất cả</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal fade" id="deleteForeverModal" tabindex="-1" aria-labelledby="deleteForeverModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteForeverModalLabel">Xác nhận xóa vĩnh viễn</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Bạn có chắc chắn muốn xóa vĩnh viễn bài viết này? Hành động này không thể hoàn tác!
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
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Bulk actions
        $(document).ready(function() {
            // Select all checkbox
            $('#checkAll').change(function() {
                $('.post-checkbox').prop('checked', $(this).prop('checked'));
                toggleBulkActions();
            });

            // Individual checkbox
            $(document).on('change', '.post-checkbox', function() {
                if ($('.post-checkbox:checked').length === $('.post-checkbox').length) {
                    $('#checkAll').prop('checked', true);
                } else {
                    $('#checkAll').prop('checked', false);
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
                $('.post-checkbox, #checkAll').prop('checked', false);
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
                
                if (action === 'restore') {
                    confirmMessage = 'Bạn có chắc muốn khôi phục ' + postIds.length + ' bài viết đã chọn?';
                    formAction = '{{ route("admin.blog.posts.bulk-restore") }}';
                } else if (action === 'force-delete') {
                    confirmMessage = 'Bạn có chắc muốn xóa vĩnh viễn ' + postIds.length + ' bài viết đã chọn? Hành động này không thể hoàn tác!';
                    formAction = '{{ route("admin.blog.posts.bulk-force-delete") }}';
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
                    
                    // Thêm method spoofing cho force delete
                    if (action === 'force-delete') {
                        const methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'DELETE';
                        form.appendChild(methodInput);
                    }
                    
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
        // Check/Uncheck all
        const checkAll = document.getElementById('checkAll');
        if (checkAll) {
            checkAll.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.post-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = checkAll.checked;
                });
            });
        }

        // Empty trash confirmation
        const emptyTrashBtn = document.getElementById('emptyTrashBtn');
        if (emptyTrashBtn) {
            emptyTrashBtn.addEventListener('click', function() {
                const emptyTrashModal = new bootstrap.Modal(document.getElementById('emptyTrashModal'));
                emptyTrashModal.show();
            });
        }

        // Restore post
        const restoreButtons = document.querySelectorAll('.restore-post');
        restoreButtons.forEach(button => {
            button.addEventListener('click', function() {
                const postId = this.getAttribute('data-id');
                if (confirm('Bạn có chắc chắn muốn khôi phục bài viết này?')) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/admin/blog/posts/${postId}/restore`;
                    
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
                const postId = this.getAttribute('data-id');
                deleteForeverForm.action = `/admin/blog/posts/${postId}/force-delete`;
                
                const deleteForeverModal = new bootstrap.Modal(document.getElementById('deleteForeverModal'));
                deleteForeverModal.show();
            });
        });
    });
</script>
@endpush
