@php
    $currentCategory = request('category');
    $searchQuery = request('search') ? '&search=' . request('search') : '';
@endphp

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

<div class="widget">
    <h3 class="widget-title">Danh mục bài viết</h3>
    <ul class="cat-list">
        <li>
            <a href="{{ route('blog.index', array_filter(['search' => request('search')])) }}" class="{{ !$currentCategory ? 'active' : '' }}">
                Tất cả bài viết
                <span class="float-end">({{ \App\Models\BlogPost::where('is_active', true)->count() }})</span>
            </a>
        </li>
        @foreach(\App\Models\BlogCategory::withCount(['posts' => function($query) {
            $query->where('is_active', true);
        }])->orderBy('name')->get() as $category)
            @if($category->posts_count > 0)
                <li>
                    <a href="{{ route('blog.index', array_filter(['category' => $category->id, 'search' => request('search')])) }}" class="{{ $currentCategory == $category->id ? 'active' : '' }}">
                        {{ $category->name }}
                        <span class="float-end">({{ $category->posts_count }})</span>
                    </a>
                </li>
            @endif
        @endforeach
    </ul>
</div>


