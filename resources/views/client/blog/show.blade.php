@extends('client.layouts.default')

@if($post->meta_description)
    @section('meta_description', $post->meta_description)
@endif

<style>
    body {
        background: #fff;
    }
    .blog-container {
        display: flex;
        gap: 30px;
    }
    .blog-sidebar {
        width: 25%;
    }
    .blog-content {
        width: 75%;
    }

    /* Sidebar */
    :root {
        --text: #222;
        --muted: #6b7280;
        --line: #e5e7eb;
        --thin: #f3f4f6;
        --accent: #111827;
    }
    
    .sidebar-section {
        margin-bottom: 24px;
    }
    
    .widget {
        border: 1px solid var(--line);
        padding: 18px;
        background: #fff;
        margin-bottom: 24px;
        
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    
    .widget-title {
        font-size: 16px;
        text-transform: uppercase;
        letter-spacing: .04em;
        text-align: center;
        margin: 0 0 14px;
        position: relative;
        padding-bottom: 10px;
    }
    
    .widget-title:after {
        content: "";
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        bottom: 0;
        width: 160px;
        height: 2px;
        background: #111;
    }
    .latest-post {
        display: flex;
        gap: 10px;
        margin-bottom: 15px;
    }
    .latest-post img {
        width: 70px;
        height: 70px;
        border-radius: 6px;
        object-fit: cover;
    }
    .latest-post .info {
        font-size: 13px;
    }
    .latest-post .info a {
        font-weight: 600;
        text-decoration: none;
        color: #333;
        display: block;
    }
    .latest-post small {
        color: #888;
    }

    .sidebar-categories ul {
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .sidebar-categories ul li {
        padding: 8px 0;
        border-bottom: 1px solid #f0f0f0;
        font-size: 14px;
        color: #333;
    }

    /* Post content */
    .post-thumbnail {
        max-width: 100%;
        margin-bottom: 20px;
        border-radius: 10px;
        box-shadow: 0 3px 12px rgba(0,0,0,0.08);
    }
    .post-header h1 {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 15px;
        color: #2E2F42;
    }
    .post-meta {
        font-size: 14px;
        color: #777;
        margin-bottom: 20px;
    }
    .post-content {
        font-size: 15px;
        line-height: 1.8;
        color: #444;
    }
    .post-content ul {
        margin: 15px 0;
        padding-left: 20px;
    }
    .post-content h2,
    .post-content h3 {
        margin-top: 30px;
        margin-bottom: 15px;
        font-weight: 700;
        color: #2E2F42;
        position: relative;
        padding-bottom: 5px;
    }
    .post-content h2::after,
    .post-content h3::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 60px;
        height: 3px;
        background: #4e73df;
        border-radius: 3px;
    }

    /* Comment */
    .comment-section {
        margin-top: 40px;
    }
    .comment-item {
        background: #fff;
        border: 1px solid #eee;
        padding: 15px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .comment-avatar img {
        border-radius: 50%;
        object-fit: cover;
    }
    .comment-content h6 {
        font-weight: 600;
        margin: 0;
        font-size: 0.95rem;
    }
    .comment-content .comment-text {
        margin-top: 5px;
        color: #333;
        font-size: 14px;
    }
    .comment-form textarea {
        margin-bottom: 10px;
    }
     /* Sidebar widgets */
     .widget {
        border: 1px solid var(--line);
        padding: 18px;
        background: #fff;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    
    .widget-title {
        font-size: 16px;
        text-transform: uppercase;
        letter-spacing: .04em;
        text-align: center;
        margin: 0 0 14px;
        position: relative;
        padding-bottom: 10px;
    }
    
    .widget-title:after {
        content: "";
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        bottom: 0;
        width: 160px;
        height: 2px;
        background: #111;
    }
    
    /* Latest posts */
    .latest-list {
        list-style: none;
        margin: 0;
        padding: 0;
    }
    
    .latest-item {
        display: flex;
        gap: 12px;
        padding: 12px 0;
        border-top: 1px solid var(--line);
    }
    
    .latest-item:first-child {
        border-top: 0;
    }
    
    .latest-thumb {
        flex: 0 0 64px;
        height: 64px;
        border: 1px solid var(--line);
        background: var(--thin);
        object-fit: cover;
    }
    
    .latest-title {
        font-size: 14px;
        margin: 0 0 6px;
    }
    
    .latest-title a {
        color: var(--accent);
        text-decoration: none;
    }
    
    .latest-meta {
        font-size: 12px;
        color: var(--muted);
    }
    
    /* Categories */
    .cat-list {
        list-style: none;
        margin: 0;
        padding: 8px 0;
    }
    
    .cat-list li {
        padding: 8px 0;
        border-top: 1px solid var(--line);
    }
    
    .cat-list li:first-child {
        border-top: 0;
    }
    
    .cat-list a {
        color: var(--text);
        text-decoration: none;
    }
</style>

@section('content')
<div class="container">
    <div class="breadcrumb mb-4" style="background: none; padding: 0; margin: 0; font-size: 14px;">
        <a href="{{ route('home') }}" class="text-decoration-none">Trang chủ</a>
        <span class="mx-2">/</span>
        <a href="{{ route('blog.index') }}" class="text-decoration-none">Tin Tức</a>
        <span class="mx-2">/</span>
        <span class="text-muted">{{ Str::limit($post->title, 30) }}</span>
    </div>
    <div class="blog-container">
        <!-- Sidebar -->
        <aside>
            <!-- Recent Posts Widget -->
            <div class="widget">
                <h3 class="widget-title">Bài viết mới nhất</h3>
                <ul class="latest-list">
                    @foreach(\App\Models\BlogPost::published()->latest()->take(5)->get() as $recentPost)
                        <li class="latest-item">
                            <a href="{{ route('blog.show', $recentPost->slug) }}">
                                <img class="latest-thumb" 
                                     src="{{ $recentPost->thumbnail ? Storage::url($recentPost->thumbnail) : asset('assets2/img/blog/rc-blog-1.jpg') }}" 
                                     alt="{{ $recentPost->title }}">
                            </a>
                            <div>
                                <h4 class="latest-title">
                                    <a href="{{ route('blog.show', $recentPost->slug) }}">
                                        {{ Str::limit($recentPost->title, 50) }}
                                    </a>
                                </h4>
                                <div class="latest-meta">
                                    {{ $recentPost->author->name ?? 'Quản trị viên' }} · 
                                    {{ $recentPost->published_at ? $recentPost->published_at->format('d.m.Y') : $recentPost->created_at->format('d.m.Y') }}
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Categories Widget -->
            <div class="widget">
                <h3 class="widget-title">Danh mục Blog</h3>
                <ul class="cat-list">
                    @php
                        $currentCategory = request('category');
                        $searchQuery = request('search') ? '&search=' . request('search') : '';
                    @endphp
                    <li>
                        <a href="?{{ $searchQuery ? ltrim($searchQuery, '&') : '' }}" class="{{ !$currentCategory ? 'active' : '' }}">
                            Tất cả bài viết
                            <span class="float-end">({{ \App\Models\BlogPost::where('is_active', true)->count() }})</span>
                        </a>
                    </li>
                    @foreach(\App\Models\BlogCategory::withCount(['posts' => function($query) {
                        $query->where('is_active', true);
                    }])->orderBy('name')->get() as $category)
                        @if($category->posts_count > 0)
                            <li>
                                <a href="?category={{ $category->id }}{{ $searchQuery }}" class="{{ $currentCategory == $category->id ? 'active' : '' }}">
                                    {{ $category->name }}
                                    <span class="float-end">({{ $category->posts_count }})</span>
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </aside>

        <!-- Content -->
        <main class="blog-content">
            <div class="post-header">
                <h1>{{ $post->title }}</h1>
                <div class="post-meta">
                    Người viết: {{ $post->author->name ?? 'Ẩn danh' }} |
                    {{ $post->created_at->format('d/m/Y') }} |
                    <i class="far fa-comment-alt"></i> {{ $post->comments_count }} bình luận
                </div>
            </div>

            @if($post->thumbnail)
                <img src="{{ Storage::url($post->thumbnail) }}" class="post-thumbnail" alt="{{ $post->title }}">
            @endif

            <div class="post-content">
                {!! $post->content !!}
            </div>

            <!-- Bình luận -->
            <div class="comment-section">
                <h3>Bình luận ({{ $approvedComments->count() }})</h3>
                @php
                    $groupedComments = $approvedComments->groupBy('parent_id');
                    $rootComments = $groupedComments->get(null, collect([]));
                @endphp
                
                @if($approvedComments->count() > 0)
                    <div class="comments-list">
                        @foreach($rootComments as $comment)
                            <div class="comment-item d-flex mb-3">
                                <div class="comment-avatar me-3">
                                    <i class="fas fa-user-circle" style="font-size: 42px; color: #6c757d;"></i>
                                </div>
                                <div class="comment-content">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">{{ $comment->user->name ?? 'Ẩn danh' }}</h6>
                                        <small class="text-muted">
                                            {{ $comment->created_at->format('H:i d/m/Y') }}
                                        </small>
                                    </div>
                                    <div class="text-muted small mb-2">
                                        {{ $comment->user->email ?? '' }}
                                    </div>
                                    <div class="comment-text">{{ $comment->content }}</div>
                                    
                                    @if($groupedComments->has($comment->id))
                                        <div class="replies ms-4 mt-3" style="border-left: 2px solid #e9ecef; padding-left: 15px;">
                                            @foreach($groupedComments[$comment->id] as $reply)
                                                <div class="comment-item d-flex mb-3">
                                                    <div class="comment-avatar me-3">
                                                        <i class="fas fa-user-circle" style="font-size: 36px; color: #adb5bd;"></i>
                                                    </div>
                                                    <div class="comment-content">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <h6 class="mb-0">{{ $reply->user->name ?? 'Ẩn danh' }}</h6>
                                                            <small class="text-muted">
                                                                {{ $reply->created_at->format('H:i d/m/Y') }}
                                                            </small>
                                                        </div>
                                                        <div class="text-muted small mb-2">
                                                            {{ $reply->user->email ?? '' }}
                                                        </div>
                                                        <div class="comment-text">{{ $reply->content }}</div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">Chưa có bình luận nào. Hãy là người đầu tiên bình luận!</p>
                @endif

                <div class="comment-form mt-4">
                    @auth
                        <form action="{{ route('blog.comments.store', $post->id) }}" method="POST">
                            @csrf
                            <textarea name="content" class="form-control mb-3" rows="4" placeholder="Viết bình luận..."></textarea>
                            <button type="submit" class="btn btn-primary mb-3">
                                <i class="far fa-paper-plane me-1"></i> Gửi bình luận
                            </button>
                        </form>
                    @else
                        <div class="alert alert-light">
                            Vui lòng <a href="{{ route('login') }}">đăng nhập</a> để bình luận.
                        </div>
                    @endauth
                </div>
            </div>
        </main>
    </div>
</div>
@endsection
