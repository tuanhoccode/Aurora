@extends('client.layouts.default')

@section('title', 'Blog - ' . config('app.name'))

@push('styles')
<style>
    /* Blog item styles */
    .tp-blog-item {
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 0 15px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        margin-bottom: 30px;
        height: 100%;
    }
    .tp-blog-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    .tp-blog-thumb {
        position: relative;
        overflow: hidden;
    }
    .tp-blog-thumb img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        transition: all 0.5s ease;
    }
    .tp-blog-item:hover .tp-blog-thumb img {
        transform: scale(1.05);
    }
    .tp-blog-tag {
        position: absolute;
        bottom: 20px;
        left: 20px;
    }
    .tp-blog-tag a {
        display: inline-block;
        background: var(--tp-theme-1);
        color: #fff;
        padding: 4px 15px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
        text-transform: uppercase;
    }
    .tp-blog-content {
        padding: 25px 30px 30px;
    }
    .tp-blog-meta {
        margin-bottom: 10px;
    }
    .tp-blog-meta span {
        font-size: 14px;
        color: #6B7280;
        margin-right: 15px;
    }
    .tp-blog-meta i {
        margin-right: 5px;
    }
    .tp-blog-title {
        font-size: 20px;
        line-height: 1.4;
        margin-bottom: 15px;
    }
    .tp-blog-title a {
        color: #1F2937;
        transition: all 0.3s ease;
    }
    .tp-blog-title a:hover {
        color: var(--tp-theme-1);
        text-decoration: none;
    }
    .tp-blog-btn a {
        color: var(--tp-theme-1);
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        transition: all 0.3s ease;
    }
    .tp-blog-btn a i {
        margin-left: 5px;
        transition: all 0.3s ease;
    }
    .tp-blog-btn a:hover {
        color: var(--tp-theme-2);
        text-decoration: none;
    }
    .tp-blog-btn a:hover i {
        transform: translateX(5px);
    }
    .tp-breadcrumb-height {
        height: 550px;  /* chỉnh tùy ý, ví dụ 400px */
    }
</style>
@endpush

@section('content')
<!-- breadcrumb area start -->
<div class="tp-breadcrumb__area p-relative fix tp-breadcrumb-height" data-background="{{ asset('assets2/img/breadcrumb/breadcrumb-bg-1.jpg') }}">
    <div class="tp-breadcrumb__shape-1">
        <img src="{{ asset('assets2/img/breadcrumb/breadcrumb-shape-1.png') }}" alt="">
    </div>
    <div class="tp-breadcrumb__shape-2">
        <img src="{{ asset('assets2/img/breadcrumb/breadcrumb-shape-2.png') }}" alt="">
    </div>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="tp-breadcrumb__content text-center">
                    <div class="tp-breadcrumb__content-left">
                        <h3 class="tp-breadcrumb__title">Blog của chúng tôi</h3>
                        <p class="tp-breadcrumb__text">Khám phá những bài viết mới nhất, mẹo vặt và tin tức hữu ích từ chúng tôi</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- breadcrumb area end -->

<!-- blog area start -->
<section class="tp-blog-area pt-120 pb-120">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-xl-8 col-lg-8">
                @php
                    $currentCategory = null;
                    if (request('category')) {
                        $currentCategory = \App\Models\BlogCategory::where('slug', request('category'))->first();
                    }
                @endphp
                
                @if($currentCategory)
                    <div class="tp-blog-details-category mb-30">
                        <h4 class="tp-blog-details-category-title">Danh mục: {{ $currentCategory->name }}</h4>
                        <p>{{ $posts->total() }} bài viết trong danh mục này</p>
                    </div>
                @elseif(request('search'))
                    <div class="tp-blog-details-category mb-30">
                        <h4 class="tp-blog-details-category-title">Kết quả tìm kiếm cho: "{{ request('search') }}"</h4>
                        <p>{{ $posts->total() }} kết quả được tìm thấy</p>
                    </div>
                @endif

                <div class="tp-blog-main-wrapper">
                    @if($posts->count() > 0)
                        <div class="row">
                            @foreach($posts as $post)
                                <div class="col-md-6">
                                    <div class="tp-blog-item mb-50">
                                        <div class="tp-blog-thumb fix">
                                            <a href="{{ route('blog.show', $post->slug) }}">
                                                <img src="{{ $post->thumbnail ? Storage::url($post->thumbnail) : asset('assets2/img/blog/blog-thumb-1.jpg') }}" alt="{{ $post->title }}">
                                            </a>
                                            @if($post->category)
                                            <div class="tp-blog-tag">
                                                <a href="{{ route('blog.index', ['category' => $post->category->slug]) }}">
                                                    {{ $post->category->name }}
                                                </a>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="tp-blog-content">
                                            <div class="tp-blog-meta">
                                                <span><i class="fa-regular fa-user"></i> {{ $post->author->name ?? 'Quản trị viên' }}</span>
                                                <span><i class="fa-regular fa-calendar-days"></i> {{ $post->published_at ? $post->published_at->format('d M, Y') : $post->created_at->format('d M, Y') }}</span>
                                                <span><i class="fa-regular fa-eye"></i> {{ number_format($post->views) }}</span>
                                            </div>
                                            <h3 class="tp-blog-title">
                                                <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
                                            </h3>
                                            <p>{{ $post->excerpt ?? Str::limit(strip_tags($post->content), 150, '...') }}</p>
                                            <div class="tp-blog-btn">
                                                <a href="{{ route('blog.show', $post->slug) }}">
                                                    Đọc thêm <i class="fa-regular fa-arrow-right"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if($posts->hasPages())
                        <div class="tp-blog-pagination mt-10">
                            {{ $posts->links() }}
                        </div>
                        @endif
                    @else
                        <div class="tp-blog-no-results text-center py-5">
                            <div class="tp-blog-no-results-icon mb-30">
                                <i class="fa-regular fa-file-lines"></i>
                            </div>
                            <h3>Chưa có bài viết nào</h3>
                            <p class="mb-30">Hãy quay lại sau để xem các bài viết mới nhất.</p>
                            <a href="{{ route('home') }}" class="tp-btn">
                                <i class="fa-regular fa-arrow-left me-2"></i> Về trang chủ
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-xl-4 col-lg-4">
                <!-- Search Widget -->
                <div class="tp-blog-sidebar-widget mb-40">
                    <h4 class="tp-blog-sidebar-widget-title position-relative pb-3 mb-4">
                        <span class="position-relative">Tìm kiếm</span>
                    </h4>
                    <div class="tp-blog-sidebar-search">
                        <form action="{{ route('blog.index') }}" method="GET" class="position-relative">
                            <div class="tp-blog-sidebar-search-input">
                                <input type="text" 
                                       name="search" 
                                       placeholder="Nhập từ khóa tìm kiếm..." 
                                       value="{{ request('search') }}"
                                       class="w-100 px-4 py-3 border-0 rounded-pill shadow-sm"
                                       style="padding-right: 50px;">
                                <button type="submit" class="position-absolute end-0 top-0 h-100 bg-transparent border-0" style="width: 50px; outline: none;">
                                    <i class="fa-regular fa-magnifying-glass text-primary"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Categories Widget -->
                <div class="tp-blog-sidebar-widget mb-40">
                    <h4 class="tp-blog-sidebar-widget-title position-relative pb-3 mb-4">
                        <span class="position-relative">Danh mục</span>
                    </h4>
                    <div class="tp-blog-sidebar-category">
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <a href="{{ route('blog.index') }}" 
                                   class="d-flex justify-content-between align-items-center py-2 px-3 rounded-3 {{ !request('category') ? 'bg-primary text-white' : 'text-dark bg-light-hover' }}">
                                    <span>Tất cả bài viết</span>
                                    <span class="badge {{ !request('category') ? 'bg-white text-primary' : 'bg-secondary' }} rounded-pill">
                                        {{ \App\Models\BlogPost::published()->count() }}
                                    </span>
                                </a>
                            </li>
                            @foreach(\App\Models\BlogCategory::withCount(['posts' => function($query) {
                                $query->published();
                            }])->orderBy('name')->get() as $category)
                                @if($category->posts_count > 0)
                                    <li class="mb-2">
                                        <a href="{{ route('blog.index', ['category' => $category->slug]) }}" 
                                           class="d-flex justify-content-between align-items-center py-2 px-3 rounded-3 {{ request('category') == $category->slug ? 'bg-primary text-white' : 'text-dark bg-light-hover' }}">
                                            <span>{{ $category->name }}</span>
                                            <span class="badge {{ request('category') == $category->slug ? 'bg-white text-primary' : 'bg-light text-dark' }} rounded-pill">
                                                {{ $category->posts_count }}
                                            </span>
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- Recent Posts Widget -->
                <div class="tp-blog-sidebar-widget mb-40">
                    <h4 class="tp-blog-sidebar-widget-title">Bài viết gần đây</h4>
                    <div class="tp-blog-sidebar-rc-post">
                        @foreach(\App\Models\BlogPost::published()->latest()->take(3)->get() as $recentPost)
                            <div class="tp-blog-sidebar-rc-post-item">
                                <div class="tp-blog-sidebar-rc-post-thumb">
                                    <a href="{{ route('blog.show', $recentPost->slug) }}">
                                        <img src="{{ $recentPost->thumbnail ? Storage::url($recentPost->thumbnail) : asset('assets2/img/blog/rc-blog-1.jpg') }}" alt="{{ $recentPost->title }}" style="width: 200px; height: 150px; object-fit: cover">
                                    </a>
                                </div>
                                <div class="tp-blog-sidebar-rc-post-content">
                                    <h5 class="tp-blog-sidebar-rc-post-title">
                                        <a href="{{ route('blog.show', $recentPost->slug) }}">{{ $recentPost->title }}</a>
                                    </h5>
                                    <div class="tp-blog-sidebar-rc-post-meta">
                                        <span><i class="fa-regular fa-calendar-days"></i> {{ $recentPost->published_at ? $recentPost->published_at->format('d M, Y') : $recentPost->created_at->format('d M, Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Tags and Banner Widgets have been removed -->
            </div>
        </div>
    </div>
</section>
<!-- blog area end -->
@endsection
