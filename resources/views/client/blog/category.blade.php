@extends('client.layouts.default')

@section('title', $category->name . ' - Blog - ' . config('app.name'))

@section('content')
<div class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">{{ $category->name }}</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('blog.index') }}">Blog</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="py-5">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="mb-4">
                    <p class="lead">{{ $category->description ?? 'Các bài viết trong danh mục ' . $category->name }}</p>
                </div>

                @if($posts->count() > 0)
                    @foreach($posts as $post)
                        <article class="card mb-4">
                            @if($post->thumbnail)
                                <a href="{{ route('blog.show', $post->slug) }}">
                                    <img src="{{ Storage::url($post->thumbnail) }}" class="card-img-top" alt="{{ $post->title }}" style="height: 300px; object-fit: cover;">
                                </a>
                            @endif
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <span class="text-muted me-3">
                                        <i class="far fa-calendar-alt me-1"></i> 
                                        {{ $post->published_at->format('d/m/Y') }}
                                    </span>
                                    <span class="text-muted">
                                        <i class="far fa-eye me-1"></i> {{ $post->views }} lượt xem
                                    </span>
                                </div>
                                
                                <h2 class="card-title h4">
                                    <a href="{{ route('blog.show', $post->slug) }}" class="text-decoration-none">
                                        {{ $post->title }}
                                    </a>
                                </h2>
                                
                                <p class="card-text">{{ Str::limit(strip_tags($post->excerpt ?? $post->content), 200, '...') }}</p>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="{{ route('blog.show', $post->slug) }}" class="btn btn-outline-primary">
                                        Đọc tiếp
                                    </a>
                                    <div class="text-muted small">
                                        <i class="far fa-comment-alt me-1"></i> 
                                        {{ $post->comments_count ?? 0 }} bình luận
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $posts->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <img src="{{ asset('images/no-data.svg') }}" alt="No posts" class="img-fluid mb-4" style="max-width: 300px;">
                        <h3 class="h4">Chưa có bài viết nào trong danh mục này</h3>
                        <p class="text-muted">Hãy quay lại sau để xem các bài viết mới nhất.</p>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                @include('client.blog.partials.sidebar')
            </div>
        </div>
    </div>
</div>
@endsection
