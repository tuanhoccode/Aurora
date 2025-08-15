@extends('client.layouts.default')



@if($post->meta_description)
    @section('meta_description', $post->meta_description)
@endif

@push('styles')
    <style>
        /* General Styles */
        :root {
            --primary-color: #4e73df;
            --primary-hover: #2e59d9;
            --secondary-color: #6c757d;
            --light-gray: #f8f9fc;
            --border-color: #e3e6f0;
            --shadow-sm: 0 .125rem .25rem rgba(0,0,0,.075);
            --shadow: 0 .15rem 1.75rem 0 rgba(58, 59, 69, .15);
            --border-radius: 0.35rem;
        }

        [data-bs-toggle="tooltip"] {
            cursor: help;
            text-decoration: none;
            border-bottom: 1px dotted var(--secondary-color);
        }

        /* Post Content Styles */
        .post-content {
            line-height: 1.8;
            color: #5a5c69;
            font-size: 1.05rem;
        }

        /* Style cho thumbnail bài viết */
        .post-thumbnail {
            max-width: 100%;
            max-height: 500px;
            height: auto;
            width: auto;
            display: block;
            margin: 0 auto 2rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            object-fit: cover;
        }
        
        /* Style cho ảnh trong nội dung bài viết */
        .post-content img {
            max-width: 100%;
            height: auto;
            border-radius: var(--border-radius);
            margin: 1.5rem 0;
            max-height: 500px; /* Giới hạn chiều cao tối đa */
            width: auto;
            display: block;
            margin-left: auto;
            margin-right: auto;
            object-fit: contain;
        }
        
        /* Điều chỉnh kích thước ảnh trên mobile */
        @media (max-width: 768px) {
            .post-content img {
                max-height: 300px;
            }
        }

        .post-content img {
            box-shadow: var(--shadow);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .post-content img:hover {
            transform: translateY(-3px);
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1);
        }
        
        /* Danh mục bài viết */
        .post-categories {
            background: rgba(78, 115, 223, 0.03);
            border: 1px solid rgba(78, 115, 223, 0.1);
            transition: all 0.3s ease;
        }
        
        .post-categories:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
            background: rgba(78, 115, 223, 0.05);
        }
        
        .category-badge {
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .category-badge:hover {
            background-color: #4e73df !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(78, 115, 223, 0.2);
        }
        
        .category-badge:hover i,
        .category-badge:hover span {
            color: white !important;
        }

        .post-content h2, 
        .post-content h3, 
        .post-content h4 {
            margin-top: 2.5rem;
            margin-bottom: 1.25rem;
            font-weight: 700;
            color: #2E2F42;
            position: relative;
            padding-bottom: 0.5rem;
        }

        .post-content h2::after,
        .post-content h3::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 60px;
            height: 3px;
            background: var(--primary-color);
            border-radius: 3px;
        }

        .post-content p {
            margin-bottom: 1.5rem;
            line-height: 1.8;
        }

        .post-content a {
            color: var(--primary-color);
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .post-content a:hover {
            color: var(--primary-hover);
            text-decoration: underline;
        }

        /* Comment Section Styles */
        .comments {
            margin-top: 2rem;
        }
        
        .comment {
            position: relative;
            margin-bottom: 1.5rem;
            padding: 1.5rem;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            border: 1px solid #eaeaea;
        }
        
        .comment:hover {
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }
        
        /* Comment Header */
        .comment-header {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1rem;
        }
        
        .comment-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            flex-shrink: 0;
        }
        
        .comment-user {
            margin-left: 1rem;
            flex: 1;
        }
        
        .comment-user h6 {
            font-size: 1rem;
            margin-bottom: 0.25rem;
            color: #2d3748;
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        
        .comment-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            font-size: 0.8rem;
            color: #718096;
            margin-top: 0.25rem;
        }
        
        .comment-meta i {
            margin-right: 0.25rem;
            color: #a0aec0;
        }
        
        /* Comment Body */
        .comment-body {
            margin: 1rem 0;
        }
        
        .comment-content {
            line-height: 1.7;
            color: #4a5568;
            padding: 1rem;
            background: #f8fafc;
            border-radius: 8px;
            border-left: 3px solid #4e73df;
            position: relative;
        }
        
        /* Comment Actions */
        .comment-actions {
            margin-top: 1rem;
        }
        
        .btn-reply {
            background: none;
            border: none;
            color: #4e73df;
            font-size: 0.85rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            transition: all 0.2s;
            font-weight: 500;
        }
        
        .btn-reply:hover {
            background-color: #edf2f7;
            color: #2c5282;
        }
        
        .btn-reply i {
            margin-right: 0.4rem;
            font-size: 0.9em;
        }
        
        /* Badge */
        .badge-admin {
            background: linear-gradient(135deg, #4e73df, #3b4d9a);
            color: white;
            font-size: 0.65rem;
            padding: 0.25rem 0.6rem;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-left: 0.5rem;
            box-shadow: 0 2px 5px rgba(78, 115, 223, 0.3);
        }
        
        /* Replies Section */
        .replies {
            margin-top: 1.5rem;
            padding-left: 3rem;
            position: relative;
        }
        
        .replies:before {
            content: '';
            position: absolute;
            left: 1.5rem;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(to bottom, #e2e8f0, #cbd5e0, #e2e8f0);
        }
        
        .comment.reply {
            position: relative;
            margin-bottom: 1.25rem;
            padding: 1.25rem;
            background: #f8fafc;
            border: 1px solid #edf2f7;
            border-radius: 10px;
            box-shadow: 0 1px 10px rgba(0, 0, 0, 0.03);
        }
        
        .comment.reply:before {
            content: '';
            position: absolute;
            left: -2rem;
            top: 2rem;
            width: 2rem;
            height: 2px;
            background: #cbd5e0;
        }
        
        .comment.reply:last-child {
            margin-bottom: 0;
        }
        
        /* Reply Form */
        .reply-form {
            margin-top: 1.5rem;
            padding: 1.5rem;
            background: #f8fafc;
            border-radius: 10px;
            border: 1px dashed #cbd5e0;
            transition: all 0.3s ease;
        }
        
        .reply-form:hover {
            border-color: #a0aec0;
        }
        
        .reply-form textarea {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
            resize: vertical;
            min-height: 100px;
        }
        
        .reply-form textarea:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.15);
        }
        
        /* Responsive Styles */
        @media (max-width: 768px) {
            .comment {
                padding: 1.25rem;
            }
            
            .replies {
                padding-left: 1.5rem;
            }
            
            .replies:before {
                left: 0.75rem;
            }
            
            .comment.reply:before {
                left: -1.5rem;
                width: 1.5rem;
            }
            
            .comment-avatar {
                width: 42px;
                height: 42px;
            }
            
            .comment-user h6 {
                font-size: 0.95rem;
            }
            
            .comment-meta {
                font-size: 0.75rem;
                gap: 0.5rem;
            }
            
            .reply-form {
                padding: 1.25rem;
            }
        }
        
        @media (max-width: 480px) {
            .comment {
                padding: 1rem;
            }
            
            .comment-avatar {
                width: 38px;
                height: 38px;
            }
            
            .comment-meta {
                flex-direction: column;
                gap: 0.25rem;
            }
            
            .comment-meta span {
                display: flex;
                align-items: center;
            }
            
            .replies {
                padding-left: 1rem;
            }
            
            .reply-form {
                padding: 1rem;
            }
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
                        <img src="{{ Storage::url($post->thumbnail) }}" alt="{{ $post->title }}" class="post-thumbnail" style="width: 500px; height: auto;">
                    @endif

                    @if($post->excerpt)
                        <div class="lead mb-4">{{ $post->excerpt }}</div>
                    @endif
                </header>

                <div class="post-content mb-5">
                    {!! $post->content !!}
                </div>

                <!-- Danh mục bài viết -->
                @if(!empty($post->categories) && $post->categories->isNotEmpty())
                    <div class="post-categories mb-5">
                        <span class="me-2"><i class="fas fa-folder text-primary me-1"></i> Danh mục:</span>
                        @foreach($post->categories as $key => $category)
                            <a href="{{ route('blog.category', $category->slug) }}" class="text-primary text-decoration-none">
                                {{ $category->name }}
                            </a>
                            @if(!$loop->last), @endif
                        @endforeach
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
