@extends('client.layouts.default')

@section('title', 'Tin Tức - ' . config('app.name'))


<style>
    :root {
        --text: #222;
        --muted: #6b7280;
        --line: #e5e7eb;
        --thin: #f3f4f6;
        --accent: #111827;
    }
    
    * { box-sizing: border-box; }
    
    body { 
        margin: 0; 
        font-family: system-ui, -apple-system, "Segoe UI", Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji"; 
        color: var(--text);
        background: #fff;
        line-height: 1.6;
    }
    
    /* Layout */
    .container {
        max-width: 1200px;
        margin: 0 auto;
        
    }
    
    .layout {
        display: grid;
        grid-template-columns: 300px 1fr;
        gap: 28px;
    }
    
    /* Sidebar widgets */
    .widget {
        border: 1px solid var(--line);
        padding: 18px;
        background: #fff;
        margin-bottom: 24px;
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
    
    /* Main content */
    .page-title {
        font-size: 36px;
        margin: 6px 0 18px;
    }
    
    .article {
        display: grid;
        grid-template-columns: 360px 1fr;
        gap: 26px;
        padding: 26px 0;
        border-bottom: 1px solid var(--line);
    }
    
    .article:first-of-type {
        padding-top: 0;
    }
    
    .thumb {
        width: 100%;
        height: 220px;
        object-fit: cover;
        background: var(--thin);
        border: 1px solid var(--line);
    }
    
    .article h3 {
        font-size: 20px;
        margin: 0 0 8px;
    }
    
    .article h3 a {
        color: var(--accent);
        text-decoration: none;
    }
    
    .article h3 a:hover {
        text-decoration: underline;
    }
    
    .meta {
        font-size: 13px;
        color: var(--muted);
        margin-bottom: 10px;
    }
    
    .excerpt {
        color: #333;
        line-height: 1.6;
    }
    
    /* Breadcrumb */
    .breadcrumb {
        max-width: 1200px;
        margin: 10px auto 0;
        padding: 10px 18px;
        color: var(--muted);
        font-size: 14px;
    }
    
    .breadcrumb a {
        color: inherit;
        text-decoration: none;
    }
    
    /* Responsive */
    @media (max-width: 980px) {
        .layout {
            grid-template-columns: 1fr;
        }
        
        .article {
            grid-template-columns: 1fr;
        }
        
        .thumb {
            height: 260px;
        }
    }
    .tp-breadcrumb-height {
        height: 550px;  /* chỉnh tùy ý, ví dụ 400px */
    }
</style>


@section('content')
<div class="container">
    <div class="breadcrumb mb-4" style="background: none; padding: 0; margin: 0; font-size: 14px;">
        <a href="{{ route('home') }}" class="text-decoration-none">Trang chủ</a>
        <span class="mx-2">/</span>
        <span class="text-muted">Tin Tức</span>
    </div>
    <div class="layout">
        <!-- SIDEBAR -->
        <aside>
            <!-- Search Widget -->
            <div class="widget">
                <h3 class="widget-title">Tìm kiếm tin tức</h3>
                <div class="search-form">
                    <form action="{{ route('blog.index') }}" method="GET">
                        <input type="text" 
                               name="search" 
                               placeholder="Nhập từ khóa tìm kiếm..." 
                               value="{{ request('search') }}"
                               class="w-100 p-2 border">
                        <button type="submit" class="btn btn-primary w-100 mt-2">
                            <i class="fa fa-search"></i> Tìm kiếm
                        </button>
                    </form>
                </div>
            </div>

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

        <!-- MAIN CONTENT -->
        <main>
            <h1 class="page-title">
                @if(isset($searchTerm) && $searchTerm)
                    Kết quả tìm kiếm cho: "{{ $searchTerm }}"
                @else
                    Tin Tức
                @endif
            </h1>

            @if(isset($hasSearchResults) && $hasSearchResults === false)
                <div class="alert alert-info">
                    Không tìm thấy bài viết nào phù hợp với từ khóa "{{ $searchTerm }}".
                    <a href="{{ route('blog.index') }}" class="text-primary">Xem tất cả bài viết</a>
                </div>
            @elseif($posts->count() > 0)
                @foreach($posts as $post)
                    <article class="article">
                        <img class="thumb" 
                             src="{{ $post->thumbnail ? Storage::url($post->thumbnail) : asset('assets2/img/blog/blog-thumb-1.jpg') }}" 
                             alt="{{ $post->title }}">
                        <div>
                            <h3>
                                <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
                            </h3>
                            <div class="meta">
                                Người viết: {{ $post->author->name ?? 'Quản trị viên' }} / 
                                {{ $post->published_at ? $post->published_at->format('d.m.Y') : $post->created_at->format('d.m.Y') }}
                            </div>
                            <p class="excerpt">
                                {{ $post->excerpt ?? Str::limit(strip_tags($post->content), 200) }}
                            </p>
                        </div>
                    </article>
                @endforeach

                <!-- Pagination -->
                @if($posts->hasPages())
                    <div class="mt-4">
                        {{ $posts->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <h3>Không tìm thấy bài viết nào</h3>
                    <p>Xin lỗi, không có bài viết nào phù hợp với tìm kiếm của bạn.</p>
                    <a href="{{ route('blog.index') }}" class="btn btn-primary">
                        Quay lại trang tin tức
                    </a>
                </div>
            @endif
        </main>
    </div>
</div>
@endsection
