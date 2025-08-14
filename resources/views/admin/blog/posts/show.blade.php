@extends('admin.layouts.app')

@section('title', 'Chi tiết bài viết: ' . $post->title)

@push('styles')
<style>
    .post-header {
        position: relative;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #eee;
    }
    .post-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 1rem;
    }
    .post-meta {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 1.5rem;
    }
    .post-meta i {
        width: 20px;
        text-align: center;
        margin-right: 3px;
    }
    .post-thumbnail {
        border-radius: 8px;
        margin: 1.5rem 0;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        max-height: 400px;
        width: 100%;
        object-fit: cover;
    }
    .post-content {
        font-size: 1.05rem;
        line-height: 1.8;
        color: #4a5568;
    }
    .post-content img {
        max-width: 100%;
        height: auto;
        border-radius: 6px;
        margin: 1.5rem 0;
    }
    .info-card {
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        margin-bottom: 1.5rem;
    }
    .info-card .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #eee;
        font-weight: 600;
        padding: 0.75rem 1.25rem;
    }
    .info-card .card-body {
        padding: 1.25rem;
    }
    .info-item {
        margin-bottom: 0.75rem;
        display: flex;
        align-items: flex-start;
    }
    .info-label {
        font-weight: 600;
        color: #4a5568;
        min-width: 120px;
    }
    .info-value {
        flex: 1;
    }
    .status-badge {
        padding: 0.35em 0.65em;
        font-size: 0.85em;
        border-radius: 0.25rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.blog.posts.index') }}">Bài viết</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Chi tiết</li>
                    </ol>
                </nav>
                <div>
                    <a href="{{ route('admin.blog.posts.edit', $post) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-1"></i> Chỉnh sửa
                    </a>
                    <a href="{{ route('admin.blog.posts.index') }}" class="btn btn-light">
                        <i class="fas fa-arrow-left me-1"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="post-header">
                        <h1 class="post-title">{{ $post->title }}</h1>
                        <div class="post-meta">
                            <span class="me-3"><i class="far fa-user"></i> {{ $post->author->name ?? 'N/A' }}</span>
                            <span class="me-3"><i class="far fa-folder"></i> {{ $post->category->name ?? 'Chưa phân loại' }}</span>
                            <span class="me-3"><i class="far fa-calendar-alt"></i> {{ $post->created_at->format('d/m/Y H:i') }}</span>
                            <span><i class="far fa-eye"></i> {{ number_format($post->views) }} lượt xem</span>
                        </div>
                    </div>
                    
                    @if($post->thumbnail)
                        <div class="text-center">
                            <img src="{{ asset('storage/' . $post->thumbnail) }}" alt="{{ $post->title }}" class="post-thumbnail">
                        </div>
                    @endif
                    
                    <div class="post-content mt-4">
                        {!! $post->content !!}
                    </div>
                </div>
            </div>
            
            @if($post->comments->count() > 0)
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="far fa-comments me-2"></i>Bình luận ({{ $post->comments->count() }})</h5>
                    </div>
                    <div class="card-body">
                        @foreach($post->comments as $comment)
                            <div class="d-flex mb-3 {{ !$loop->last ? 'border-bottom pb-3' : '' }}">
                                <div class="flex-shrink-0 me-3">
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="far fa-user text-muted"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="mb-1">{{ $comment->user->name ?? $comment->user_name }}</h6>
                                        <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1">{{ $comment->content }}</p>
                                    <div class="small">
                                        <span class="badge {{ $comment->is_active ? 'bg-success' : 'bg-warning' }} me-2">
                                            {{ $comment->is_active ? 'Đã duyệt' : 'Chờ duyệt' }}
                                        </span>
                                        @if(!$comment->is_active)
                                            <a href="{{ route('admin.blog.comments.approve', $comment) }}" class="text-success me-2">
                                                <i class="fas fa-check"></i> Duyệt
                                            </a>
                                        @endif
                                        <a href="#" class="text-danger" 
                                           onclick="event.preventDefault(); if(confirm('Xác nhận xóa bình luận này?')) { document.getElementById('delete-comment-{{ $comment->id }}').submit(); }">
                                            <i class="fas fa-trash"></i> Xóa
                                        </a>
                                        <form id="delete-comment-{{ $comment->id }}" 
                                              action="{{ route('admin.blog.comments.destroy', $comment) }}" 
                                              method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
        
        <div class="col-lg-4">
            <div class="info-card card mb-4">
                <div class="card-header">
                    <i class="fas fa-info-circle me-2"></i>Thông tin bài viết
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">Trạng thái:</div>
                        <div class="info-value">
                            @if($post->status === 'published')
                                <span class="badge bg-success status-badge">
                                    <i class="fas fa-check-circle me-1"></i> Đã xuất bản
                                </span>
                            @elseif($post->status === 'draft')
                                <span class="badge bg-warning text-dark status-badge">
                                    <i class="fas fa-file-alt me-1"></i> Bản nháp
                                </span>
                            @else
                                <span class="badge bg-secondary status-badge">
                                    <i class="fas fa-archive me-1"></i> Lưu trữ
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Đường dẫn SEO:</div>
                        <div class="info-value">
                            <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="text-primary">
                                {{ $post->slug }}
                                <i class="fas fa-external-link-alt ms-1"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Cho phép bình luận:</div>
                        <div class="info-value">
                            @if($post->allow_comments)
                                <span class="badge bg-success status-badge">
                                    <i class="fas fa-check me-1"></i> Đã bật
                                </span>
                            @else
                                <span class="badge bg-secondary status-badge">
                                    <i class="fas fa-times me-1"></i> Đã tắt
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Ngày tạo:</div>
                        <div class="info-value">
                            {{ $post->created_at->format('d/m/Y H:i') }}
                            <small class="text-muted">({{ $post->created_at->diffForHumans() }})</small>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Cập nhật cuối:</div>
                        <div class="info-value">
                            {{ $post->updated_at->format('d/m/Y H:i') }}
                            <small class="text-muted">({{ $post->updated_at->diffForHumans() }})</small>
                        </div>
                    </div>
                    
                    @if($post->published_at)
                        <div class="info-item">
                            <div class="info-label">Ngày xuất bản:</div>
                            <div class="info-value">
                                {{ $post->published_at->format('d/m/Y H:i') }}
                                <small class="text-muted">({{ $post->published_at->diffForHumans() }})</small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="info-card card mb-4">
                <div class="card-header">
                    <i class="fas fa-tasks me-2"></i>Hành động
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        
                        <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="btn btn-outline-primary">
                            <i class="fas fa-external-link-alt me-1"></i> Xem bài viết
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Xử lý xem ảnh lớn khi click
    document.addEventListener('DOMContentLoaded', function() {
        const images = document.querySelectorAll('.post-content img');
        images.forEach(img => {
            img.style.cursor = 'pointer';
            img.addEventListener('click', function() {
                const src = this.src;
                const modal = `
                    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-xl">
                            <div class="modal-content">
                                <div class="modal-body text-center p-0">
                                    <img src="${src}" class="img-fluid" style="max-height: 90vh;">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                    <a href="${src}" download class="btn btn-primary">
                                        <i class="fas fa-download me-1"></i> Tải xuống
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                // Tạo và hiển thị modal
                document.body.insertAdjacentHTML('beforeend', modal);
                const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
                imageModal.show();
                
                // Xóa modal khi đóng
                document.getElementById('imageModal').addEventListener('hidden.bs.modal', function () {
                    this.remove();
                });
            });
        });
    });
</script>
@endpush
