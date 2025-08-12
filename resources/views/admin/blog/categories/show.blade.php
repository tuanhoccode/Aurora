@extends('admin.layouts.app')

@section('title', 'Chi tiết danh mục: ' . $category->name)

@push('styles')
<style>
    .category-info {
        background: #f8f9fa;
        border-radius: 0.5rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .info-label {
        font-weight: 600;
        color: #495057;
        min-width: 150px;
        display: inline-block;
    }
    .info-value {
        color: #212529;
    }
    .category-image {
        max-width: 100%;
        height: auto;
        border-radius: 0.5rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    .badge {
        font-size: 0.875rem;
        padding: 0.35em 0.65em;
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        {{ $category->name }}
        @if($category->trashed())
            <span class="badge bg-danger ms-2">Đã xóa</span>
        @else
            <span class="badge bg-{{ $category->is_active ? 'success' : 'secondary' }}">
                {{ $category->is_active ? 'Hoạt động' : 'Không hoạt động' }}
            </span>
        @endif
    </h1>
    <div class="d-flex gap-2">
        @if($category->trashed())
            <form action="{{ route('admin.blog.categories.restore', $category->id) }}" method="POST" class="d-inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Khôi phục
                </button>
            </form>
        @else
            <a href="{{ route('admin.blog.categories.edit', $category->id) }}" class="btn btn-primary">
                <i class="bi bi-pencil-square me-1"></i> Chỉnh sửa
            </a>
        @endif
        
        <a href="{{ route('admin.blog.categories.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Quay lại
        </a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-medium">Tên danh mục</label>
                            <p class="mb-0">{{ $category->name }}</p>
                        </div>
                        
                        @if($category->parent)
                        <div class="mb-3">
                            <label class="form-label fw-medium">Danh mục cha</label>
                            <p class="mb-0">
                                <a href="{{ route('admin.blog.categories.show', $category->parent_id) }}" class="text-primary">
                                    {{ $category->parent->name }}
                                </a>
                            </p>
                        </div>
                        @endif
                        
                        <div class="mb-3">
                            <label class="form-label fw-medium">Trạng thái</label>
                            <p class="mb-0">
                                <span class="badge bg-{{ $category->is_active ? 'success' : 'secondary' }}">
                                    {{ $category->is_active ? 'Hoạt động' : 'Không hoạt động' }}
                                </span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-medium">Ngày tạo</label>
                            <p class="mb-0">{{ $category->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-medium">Cập nhật lần cuối</label>
                            <p class="mb-0">{{ $category->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                        
                        @if($category->trashed())
                        <div class="mb-3">
                            <label class="form-label fw-medium">Ngày xóa</label>
                            <p class="mb-0">{{ $category->deleted_at->format('d/m/Y H:i') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                
                <div class="mt-4 pt-3 border-top">
                    <h5 class="mb-3">Thống kê bài viết</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center">
                                <div class="h4 mb-1">{{ $category->posts_count }}</div>
                                <div class="text-muted small">Tổng số bài viết</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center">
                                <div class="h4 mb-1 text-success">{{ $category->posts()->published()->count() }}</div>
                                <div class="text-muted small">Đã xuất bản</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center">
                                <div class="h4 mb-1 text-secondary">{{ $category->posts()->draft()->count() }}</div>
                                <div class="text-muted small">Bản nháp</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
