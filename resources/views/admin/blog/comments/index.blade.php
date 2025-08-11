@extends('admin.layouts.app')

@section('title', 'Quản lý bình luận bài viết')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Danh sách bình luận</h4>
                    <div class="d-flex">
                        <div class="btn-group me-2">
                            <a href="{{ route('admin.blog.comments.index') }}" 
                               class="btn {{ !request()->has('status') ? 'btn-primary' : 'btn-outline-secondary' }}">
                                Tất cả
                            </a>
                            <a href="{{ route('admin.blog.comments.index', ['status' => 'pending']) }}" 
                               class="btn {{ request('status') === 'pending' ? 'btn-warning' : 'btn-outline-secondary' }}">
                                Chờ duyệt
                            </a>
                            <a href="{{ route('admin.blog.comments.index', ['status' => 'approved']) }}" 
                               class="btn {{ request('status') === 'approved' ? 'btn-success' : 'btn-outline-secondary' }}">
                                Đã duyệt
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <form action="{{ route('admin.blog.comments.index') }}" method="GET" class="row g-2">
                            <div class="col-md-10">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Tìm kiếm nội dung, tên, email..." 
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-1"></i> Tìm kiếm
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nội dung</th>
                                    <th>Bài viết</th>
                                    <th>Người bình luận</th>
                                    <th>Thời gian</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($comments as $comment)
                                <tr>
                                    <td>{{ $comment->id }}</td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 300px;" 
                                             title="{{ $comment->content }}">
                                            {{ $comment->content }}
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('blog.show', $comment->post->slug) }}" target="_blank">
                                            {{ Str::limit($comment->post->title, 30) }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($comment->user)
                                            {{ $comment->user->name }}
                                        @else
                                            {{ $comment->user_name }}<br>
                                            <small class="text-muted">{{ $comment->user_email }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $comment->created_at->diffForHumans() }}</td>
                                    <td>
                                        @if($comment->is_active)
                                            <span class="badge bg-success">Đã duyệt</span>
                                        @else
                                            <span class="badge bg-warning">Chờ duyệt</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <div class="d-flex gap-1 flex-wrap">
                                                <!-- Nút trả lời -->
                                                <button type="button" class="btn btn-sm btn-info text-white" 
                                                        data-bs-toggle="modal" data-bs-target="#replyModal{{ $comment->id }}"
                                                        title="Trả lời bình luận">
                                                    <i class="fas fa-reply me-1"></i> Trả lời
                                                </button>
                                                
                                                @if(!$comment->is_active)
                                                    <form action="{{ route('admin.blog.comments.approve', $comment) }}" 
                                                          method="POST" class="d-inline"
                                                          onsubmit="return confirm('Bạn có chắc chắn muốn duyệt bình luận này?')">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-success" 
                                                                title="Duyệt bình luận">
                                                            <i class="fas fa-check me-1"></i> Duyệt
                                                        </button>
                                                    </form>
                                                @else
                                                    <button class="btn btn-sm btn-success" disabled>
                                                        <i class="fas fa-check me-1"></i> Đã duyệt
                                                    </button>
                                                @endif
                                                
                                                <form action="{{ route('admin.blog.comments.destroy', $comment) }}" 
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Bạn có chắc chắn muốn xóa bình luận này?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" 
                                                            title="Xóa bình luận">
                                                        <i class="fas fa-trash me-1"></i> Xóa
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <img src="{{ asset('admin/assets/images/no-data.svg') }}" alt="No data" 
                                             class="img-fluid" style="max-width: 200px;">
                                        <p class="mt-3">Không có bình luận nào</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-3">
                        {{ $comments->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Xử lý xác nhận trước khi xóa
    document.querySelectorAll('form[onsubmit]').forEach(form => {
        form.onsubmit = function() {
            return confirm('Bạn có chắc chắn muốn xóa bình luận này?');
        };
    });
</script>
@endpush

{{-- Modal trả lời bình luận --}}
@foreach($comments as $comment)
<div class="modal fade" id="replyModal{{ $comment->id }}" tabindex="-1" aria-labelledby="replyModalLabel{{ $comment->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="replyModalLabel{{ $comment->id }}">
                    <i class="fas fa-reply me-2"></i>Trả lời bình luận #{{ $comment->id }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <form action="{{ route('admin.blog.comments.reply', $comment) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="replyContent{{ $comment->id }}" class="form-label">Nội dung trả lời</label>
                        <textarea class="form-control" id="replyContent{{ $comment->id }}" name="content" 
                                  rows="4" required minlength="3" maxlength="1000"
                                  placeholder="Nhập nội dung trả lời..."></textarea>
                        <div class="form-text">Trả lời của bạn sẽ được đăng với tên của bạn và hiển thị ngay lập tức.</div>
                    </div>
                    
                    <div class="alert alert-info mb-0">
                        <div class="d-flex">
                            <i class="fas fa-info-circle me-2 mt-1"></i>
                            <div>
                                <strong>Bình luận gốc:</strong>
                                <div class="mt-1 p-2 bg-light rounded">
                                    {{ $comment->content }}
                                </div>
                                <div class="small text-muted mt-1">
                                    - {{ $comment->user_name }}, {{ $comment->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Hủy
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-1"></i> Gửi trả lời
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
