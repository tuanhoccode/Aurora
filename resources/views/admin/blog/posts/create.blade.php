@extends('admin.layouts.app')

@section('title', 'Thêm mới bài viết')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    .ck-editor__editable {
        min-height: 400px;
    }
    .ck-editor.ck-editor-title .ck-editor__editable {
        min-height: auto !important;
        padding: 0.5rem 1rem !important;
        border: 1px solid #ced4da !important;
        border-radius: 0.25rem !important;
    }
    .ck-editor.ck-editor-title .ck-editor__editable.ck-focused {
        border-color: #80bdff !important;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
    }
    .card {
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
        border: none;
        margin-bottom: 1.5rem;
    }
    .card-header {
        background: #f8f9fa;
        border-bottom: 1px solid #eee;
        padding: 0.75rem 1.25rem;
    }
    .card-header h6 {
        margin: 0;
        font-weight: 600;
    }
    .form-control:focus, .form-select:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    }
    .preview-image {
        margin-top: 15px;
        border: 1px dashed #ddd;
        padding: 10px;
        border-radius: 5px;
        text-align: center;
        background-color: #f9f9f9;
    }
    .preview-image img {
        max-height: 200px;
        max-width: 100%;
        object-fit: contain;
    }
    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
    }
    .nav-tabs .nav-link {
        font-weight: 500;
    }
    .nav-tabs .nav-link.active {
        font-weight: 600;
    }
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 38px;
    }
</style>
@endpush

@section('content')
<div class="content">
    <nav class="mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.blog.posts.index') }}">Bài viết</a></li>
            <li class="breadcrumb-item active">Tạo mới</li>
        </ol>
    </nav>
    
    <div class="container-fluid">
        <form action="{{ route('admin.blog.posts.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            
            <div class="row">
                <!-- Cột trái: Nội dung chính -->
                <div class="col-lg-8">
                    <!-- Card: Thông tin cơ bản -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <i class="fas fa-info-circle me-1"></i> Thông tin cơ bản
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <label class="form-label fw-medium">Tiêu đề bài viết <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       name="title" id="title" value="{{ old('title') }}" 
                                       placeholder="Nhập tiêu đề bài viết">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Đường dẫn tĩnh (URL) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ url('/blog') }}/</span>
                                    <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                           id="slug" name="slug" value="{{ old('slug') }}" 
                                           placeholder="duong-dan-bai-viet">
                                    @error('slug')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">Đường dẫn tĩnh sẽ được tạo tự động từ tiêu đề nếu để trống</small>
                            </div>
                            
                            <div class="mb-0">
                                <label class="form-label fw-medium">Nội dung chi tiết <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('content') is-invalid @enderror" 
                                          id="ckeditor-content" name="content" rows="10">{{ old('content') }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Card: Ảnh đại diện -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <i class="fas fa-image me-1"></i> Ảnh đại diện
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Tải lên ảnh đại diện</label>
                                <input type="file" class="form-control @error('thumbnail') is-invalid @enderror" 
                                       id="thumbnail" name="thumbnail" accept="image/*">
                                @error('thumbnail')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Định dạng: JPG, JPEG, PNG. Kích thước đề xuất: 1200x630px</small>
                            </div>
                            
                            <div class="preview-image mt-2">
                                <img id="thumbnail-preview" src="{{ asset('images/default-placeholder.png') }}" alt="Preview" class="img-fluid" style="max-height: 200px; display: block;">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Cột phải: Sidebar -->
                <div class="col-lg-4">
                    <!-- Card: Danh mục -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <i class="fas fa-folder me-1"></i> Danh mục
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Chọn danh mục <span class="text-danger">*</span></label>
                                <select class="form-select select2 @error('category_id') is-invalid @enderror" 
                                        name="category_id">
                                    <option value="">-- Chọn danh mục --</option>
                                    @foreach($categories as $id => $name)
                                        <option value="{{ $id }}" {{ old('category_id') == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" role="switch" id="featured" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                                <label class="form-check-label" for="featured">Bài viết nổi bật</label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Card: Tùy chọn -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <i class="fas fa-cog me-1"></i> Tùy chọn
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="is_active" class="form-label">Trạng thái</label>
                                <select class="form-select @error('is_active') is-invalid @enderror" name="is_active" id="is_active">
                                    <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>Hiển thị</option>
                                    <option value="0" {{ old('is_active', 1) == 0 ? 'selected' : '' }}>Ẩn</option>
                                </select>
                                @error('is_active')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Ngày đăng</label>
                                <input type="datetime-local" class="form-control @error('published_at') is-invalid @enderror" 
                                       name="published_at" value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}">
                                @error('published_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" role="switch" id="allow_comments" name="allow_comments" value="1" {{ old('allow_comments', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="allow_comments">Cho phép bình luận</label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Card: SEO -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <i class="fas fa-search me-1"></i> Tối ưu SEO
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Meta Title</label>
                                <input type="text" class="form-control @error('meta_title') is-invalid @enderror" 
                                       name="meta_title" value="{{ old('meta_title') }}" 
                                       placeholder="Tối đa 60 ký tự">
                                @error('meta_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Meta Description</label>
                                <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                                          name="meta_description" rows="3" 
                                          placeholder="Tối đa 160 ký tự">{{ old('meta_description') }}</textarea>
                                @error('meta_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-0">
                                <label class="form-label">Meta Keywords</label>
                                <input type="text" class="form-control @error('meta_keywords') is-invalid @enderror" 
                                       name="meta_keywords" value="{{ old('meta_keywords') }}" 
                                       placeholder="Từ khóa cách nhau bởi dấu phẩy">
                                @error('meta_keywords')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mb-4">
               
                <div>
                   <a href="{{ route('admin.blog.posts.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Quay lại
                </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-1"></i> Đăng bài
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<!-- CKEditor -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    // Khởi tạo Select2
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5',
            placeholder: 'Chọn danh mục',
            allowClear: true,
            width: '100%'
        });
        
        // Preview thumbnail
        $('#thumbnail').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#thumbnail-preview').attr('src', e.target.result).show();
                };
                reader.readAsDataURL(file);
            }
        });

        // Tự động điền meta title từ tiêu đề
        $('input[name="title"]').on('input', function() {
            if (!$('input[name="meta_title"]').val()) {
                $('input[name="meta_title"]').val($(this).val().substring(0, 60));
            }
            
            // Tự động tạo slug nếu chưa có
            if (!$('#slug').val()) {
                const slug = $(this).val()
                    .toLowerCase()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .replace(/[^\w\s-]/g, '')
                    .trim()
                    .replace(/[\s-]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                $('#slug').val(slug);
            }
        });
    });

    // Khởi tạo CKEditor cho nội dung
    ClassicEditor
        .create(document.querySelector('#ckeditor-content'))
        .then(editor => {
            // Khôi phục nội dung nếu có lỗi validation
            @if(old('content'))
                editor.setData(@json(old('content')));
            @endif
        })
        .catch(error => {
            console.error('Lỗi khi khởi tạo CKEditor:', error);
        });
</script>
@endpush
