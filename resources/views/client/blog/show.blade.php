@extends('client.layouts.default')

@section('title', $post->meta_title ?: $post->title . ' - ' . config('app.name'))

@if($post->meta_description)
    @section('meta_description', $post->meta_description)
@endif

@push('styles')
    <style>
        [data-bs-toggle="tooltip"] {
            cursor: help;
            text-decoration: underline;
            text-decoration-style: dotted;
            text-underline-offset: 2px;
        }
        .comment-timestamp {
            font-size: 0.8em;
            color: #6c757d;
        }
<style>
    .post-content {
        line-height: 1.8;
    }
    .post-content img {
        max-width: 100%;
        height: auto;
        margin: 1.5rem 0;
        border-radius: 0.5rem;
    }
    .post-content h2, 
    .post-content h3, 
    .post-content h4 {
        margin-top: 2rem;
        margin-bottom: 1rem;
        font-weight: 600;
    }
    .post-content p {
        margin-bottom: 1.25rem;
    }
    .comment-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }
    .comment {
        transition: all 0.3s ease;
        position: relative;
    }
    .comment:hover {
        background-color: rgba(0,0,0,0.02);
    }
    .comment-reply {
        margin-left: 60px;
        padding-left: 15px;
        border-left: 2px solid #e9ecef;
    }
    .comment-reply .comment-reply {
        margin-left: 40px;
    }
    .comment-actions {
        display: flex;
        gap: 10px;
        margin-top: 8px;
    }
    .comment-actions button {
        background: none;
        border: none;
        color: #6c757d;
        font-size: 0.85rem;
        padding: 2px 8px;
        cursor: pointer;
        transition: color 0.2s;
    }
    .comment-actions button:hover {
        color: #0d6efd;
        text-decoration: underline;
    }
    .reply-form {
        margin-top: 15px;
        margin-left: 60px;
        display: none;
    }
    .reply-form.active {
        display: block;
    }
    .comment-content {
        white-space: pre-line;
        word-break: break-word;
    }
    .comment-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 5px;
    }
    .comment-author {
        font-weight: 600;
        color: #212529;
    }
    .comment-meta {
        font-size: 0.85rem;
        color: #6c757d;
    }
    .replies-count {
        font-size: 0.85rem;
        color: #0d6efd;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        margin-top: 5px;
    }
    .replies-count i {
        margin-right: 5px;
        transition: transform 0.3s;
    }
    .replies-count.collapsed i {
        transform: rotate(-90deg);
    }
    .replies-container {
        margin-left: 60px;
        padding-left: 15px;
        border-left: 2px solid #e9ecef;
        display: none;
    }
    .replies-container.show {
        display: block;
    }
</style>
@endpush

@section('content')
<div class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('blog.index') }}">Blog</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($post->title, 50) }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<article class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <header class="mb-5">
                    <h1 class="mb-3">{{ $post->title }}</h1>
                    
                    <div class="d-flex align-items-center text-muted mb-3">
                        <div class="d-flex align-items-center me-4">
                            @if($post->author && $post->author->avatar)
                                <img src="{{ Storage::url($post->author->avatar) }}" alt="{{ $post->author->name }}" class="rounded-circle me-2" width="40" height="40">
                            @else
                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                            @endif
                            <div>
                                <div>{{ $post->author ? $post->author->name : 'Ẩn danh' }}</div>
                                <small>{{ $post->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                        <div class="me-4">
                            <i class="far fa-eye me-1"></i> {{ $post->views }} lượt xem
                        </div>
                        <div>
                            <i class="far fa-comment-alt me-1"></i> {{ $post->comments_count }} bình luận
                        </div>
                    </div>

                    @if($post->thumbnail)
                        <img src="{{ Storage::url($post->thumbnail) }}" alt="{{ $post->title }}" class="img-fluid rounded mb-4">
                    @endif

                    @if($post->excerpt)
                        <div class="lead mb-4">{{ $post->excerpt }}</div>
                    @endif
                </header>

                <div class="post-content mb-5">
                    {!! $post->content !!}
                </div>

                <!-- Categories & Tags -->
                @if(($post->categories && $post->categories->count() > 0) || ($post->tags && $post->tags->count() > 0))
                    <div class="d-flex flex-wrap gap-2 mb-5">
                        @if($post->categories)
                            @foreach($post->categories as $category)
                                <a href="#" class="badge bg-light text-dark text-decoration-none">
                                    <i class="fas fa-folder me-1"></i> {{ $category->name }}
                                </a>
                            @endforeach
                        @endif
                        
                        @if($post->tags)
                            @foreach($post->tags as $tag)
                                <a href="#" class="badge bg-light text-dark text-decoration-none">
                                    <i class="fas fa-tag me-1"></i> {{ $tag->name }}
                                </a>
                            @endforeach
                        @endif
                    </div>
                @endif

                <!-- Author Box -->
                @if($post->author)
                    <div class="card mb-5">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                @if($post->author->avatar)
                                    <img src="{{ Storage::url($post->author->avatar) }}" alt="{{ $post->author->name }}" class="rounded-circle me-3" width="80" height="80">
                                @else
                                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center me-3" style="width: 80px; height: 80px;">
                                        <i class="fas fa-user text-white" style="font-size: 2rem;"></i>
                                    </div>
                                @endif
                                <div>
                                    <h5 class="mb-1">{{ $post->author->name }}</h5>
                                    <p class="text-muted mb-2">{{ $post->author->bio ?? 'Tác giả bài viết' }}</p>
                                    @if($post->author->social_links)
                                        <div class="d-flex gap-2">
                                            @if($post->author->social_links['facebook'] ?? false)
                                                <a href="{{ $post->author->social_links['facebook'] }}" target="_blank" class="text-muted">
                                                    <i class="fab fa-facebook-f"></i>
                                                </a>
                                            @endif
                                            @if($post->author->social_links['twitter'] ?? false)
                                                <a href="{{ $post->author->social_links['twitter'] }}" target="_blank" class="text-muted">
                                                    <i class="fab fa-twitter"></i>
                                                </a>
                                            @endif
                                            @if($post->author->social_links['instagram'] ?? false)
                                                <a href="{{ $post->author->social_links['instagram'] }}" target="_blank" class="text-muted">
                                                    <i class="fab fa-instagram"></i>
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Comments Section -->
                <div class="mb-5">
                    <h4 class="mb-4">Bình luận ({{ $post->comments_count }})</h4>
                    
                    @auth
                        <!-- Comment Form -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <form action="{{ route('blog.comments.store', $post) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="content" class="form-label">Bình luận của bạn</label>
                                        <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="3" required></textarea>
                                        @error('content')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <button type="submit" class="btn btn-primary">Gửi bình luận</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info mb-4">
                            <a href="{{ route('login') }}" class="fw-bold">Đăng nhập</a> hoặc 
                            <a href="{{ route('register.post') }}" class="fw-bold">đăng ký</a> để bình luận
                        </div>
                    @endauth

                    @php
                        $approvedComments = $post->activeComments()->whereNull('parent_id')->latest()->get();
                    @endphp
                    @if($approvedComments->count() > 0)
                        <div class="comments">
                            @foreach($approvedComments as $comment)
                                <div class="comment mb-4 p-4 border rounded shadow-sm">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                <i class="fas fa-user text-primary"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <h6 class="mb-0 fw-bold">
                                                        {{ $comment->user->name }}
                                                        @if($comment->user->is_admin)
                                                            <span class="badge bg-primary ms-2">Admin</span>
                                                        @endif
                                                    </h6>
                                                    <small class="text-muted">
                                                        <i class="far fa-envelope me-1"></i>
                                                        {{ $comment->user->email }}
                                                    </small>
                                                </div>
                                                <small class="text-muted">
                                                    <i class="far fa-clock me-1"></i>
                                                    {{ $comment->created_at->format('H:i d/m/Y') }}
                                                </small>
                                            </div>
                                            <div class="comment-content p-3 bg-light rounded">
                                                {{ $comment->content }}
                                            </div>
                                    
                                    <!-- Hiển thị các bình luận con (replies) nếu có -->
                                    @php
                                        $replies = $post->comments->where('parent_id', $comment->id);
                                    @endphp
                                    @if($replies->count() > 0)
                                        <div class="replies mt-4 ms-5 ps-4 border-start border-2">
                                            @foreach($replies as $reply)
                                                <div class="reply mb-3 p-0">
                                                    <div class="d-flex align-items-start">
                                                        <div class="flex-shrink-0 me-3 mt-2">
                                                            @if($reply->user->is_admin)
                                                                <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                                    <i class="fas fa-shield-alt text-primary"></i>
                                                                </div>
                                                            @else
                                                                <div class="bg-secondary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                                    <i class="fas fa-user text-secondary"></i>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <div class="d-flex justify-content-between align-items-start mb-1">
                                                                <div>
                                                                    <h6 class="mb-0 fw-bold">
                                                                        {{ $reply->user->name }}
                                                                        @if($reply->user->is_admin)
                                                                            <span class="badge bg-primary ms-2">Admin</span>
                                                                        @endif
                                                                    </h6>
                                                                    <small class="text-muted">
                                                                        <i class="far fa-envelope me-1"></i>
                                                                        {{ $reply->user->email }}
                                                                    </small>
                                                                </div>
                                                                <small class="text-muted">
                                                                    <i class="far fa-clock me-1"></i>
                                                                    {{ $reply->created_at->format('H:i d/m/Y') }}
                                                                </small>
                                                            </div>
                                                            <div class="comment-content p-3 rounded @if($reply->user->is_admin) bg-primary bg-opacity-5 border-start border-primary border-3 @else bg-light @endif">
                                                                {{ $reply->content }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                    
                                    @auth
                                        <div class="mt-3">
                                            <button type="button" class="btn btn-sm btn-outline-primary btn-reply" data-comment-id="{{ $comment->id }}">
                                                <i class="fas fa-reply me-1"></i> Trả lời
                                            </button>
                                        </div>
                                        
                                        <form class="reply-form mt-3" id="reply-form-{{ $comment->id }}" style="display: none;" action="{{ route('admin.blog.comments.reply', $comment) }}" method="POST">
                                            @csrf
                                            <div class="input-group mb-2">
                                                <textarea name="content" class="form-control" rows="2" required placeholder="Viết phản hồi..."></textarea>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    <i class="far fa-paper-plane me-1"></i> Gửi phản hồi
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary btn-sm btn-cancel-reply" data-comment-id="{{ $comment->id }}">
                                                    <i class="fas fa-times me-1"></i> Hủy
                                                </button>
                                            </div>
                                        </form>
                                    @endauth
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="far fa-comment-dots fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Chưa có bình luận nào. Hãy là người đầu tiên bình luận!</p>
                        </div>
                    @endif
                </div>

                <!-- Related Posts -->
                @if($relatedPosts->count() > 0)
                    <div class="related-posts">
                        <h4 class="mb-4">Bài viết liên quan</h4>
                        <div class="row">
                            @foreach($relatedPosts as $relatedPost)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100">
                                        @if($relatedPost->thumbnail)
                                            <a href="{{ route('blog.show', $relatedPost->slug) }}">
                                                <img src="{{ Storage::url($relatedPost->thumbnail) }}" class="card-img-top" alt="{{ $relatedPost->title }}" style="height: 150px; object-fit: cover;">
                                            </a>
                                        @endif
                                        <div class="card-body">
                                            <h5 class="card-title h6">
                                                <a href="{{ route('blog.show', $relatedPost->slug) }}" class="text-decoration-none">
                                                    {{ Str::limit($relatedPost->title, 50) }}
                                                </a>
                                            </h5>
                                            <div class="text-muted small">
                                                <i class="far fa-calendar-alt me-1"></i> {{ $relatedPost->created_at->format('d/m/Y') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</article>
@endsection

@push('scripts')
<script>
    // Khởi tạo tooltip Bootstrap
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
