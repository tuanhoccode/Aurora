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
                    <style>
                        .posts-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
                        .post-card { border: 1px solid #e5e7eb; background: #fff; display: flex; flex-direction: column; }
                        .post-card .thumb { width: 100%; height: 210px; object-fit: cover; background: #f3f4f6; border-bottom: 1px solid #e5e7eb; }
                        .post-card .card-body { padding: 14px; }
                        .post-card h3 { font-size: 18px; margin: 0 0 8px; }
                        .post-card h3 a { color: #111827; text-decoration: none; }
                        .post-card h3 a:hover { text-decoration: underline; }
                        .post-card .meta { font-size: 12px; color: #6b7280; margin-bottom: 10px; }
                        .post-card .excerpt { color: #333; line-height: 1.6; font-size: 14px; }
                        @media (max-width: 1200px) { .posts-grid { grid-template-columns: repeat(2, 1fr); } }
                        @media (max-width: 980px) { .posts-grid { grid-template-columns: 1fr; } }
                    </style>
                    <div class="posts-grid">
                        @foreach($posts as $post)
                            <article class="post-card">
                                <a href="{{ route('blog.show', $post->slug) }}">
                                    <img src="{{ $post->thumbnail ? Storage::url($post->thumbnail) : asset('assets2/img/blog/blog-thumb-1.jpg') }}" class="thumb" alt="{{ $post->title }}">
                                </a>
                                <div class="card-body">
                                    <h3>
                                        <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
                                    </h3>
                                    <div class="meta">
                                        {{ optional($post->published_at ?? $post->created_at)->format('d/m/Y') }} · {{ $post->views }} lượt xem
                                    </div>
                                    <p class="excerpt">{{ Str::limit(strip_tags($post->excerpt ?? $post->content), 160, '...') }}</p>
                                </div>
                            </article>
                        @endforeach
                    </div>

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
                <div style="position: sticky; top: 20px;">
                    @include('client.blog.partials.sidebar')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
