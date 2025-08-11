@extends('admin.layouts.app')

@section('title', 'Thêm mới danh mục')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .preview-image img {
        max-width: 100%;
        max-height: 200px;
        margin-top: 10px;
    }
</style>
@endpush

@section('content')
<div class="content">
    <nav class="mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.blog.posts.index') }}">Bài viết</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.blog.categories.index') }}">Danh mục bài viết</a></li>
            <li class="breadcrumb-item active">Thêm mới</li>
        </ol>
    </nav>

    <div class="container-fluid">
        <form action="{{ route('admin.blog.categories.store') }}" method="POST" enctype="multipart/form-data">
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
            
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <i class="fas fa-info-circle me-1"></i> Thông tin cơ bản
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <label class="form-label fw-medium">Tên danh mục <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       name="name" value="{{ old('name') }}" 
                                       placeholder="Nhập tên danh mục" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-medium">Trạng thái <span class="text-danger">*</span></label>
                                    <select class="form-select @error('is_active') is-invalid @enderror" name="is_active" required>
                                        <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>Hoạt động</option>
                                        <option value="0" {{ old('is_active') == 0 ? 'selected' : '' }}>Không hoạt động</option>
                                    </select>
                                    @error('is_active')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-medium">Danh mục cha</label>
                                    <select class="form-select @error('parent_id') is-invalid @enderror" name="parent_id">
                                        <option value="">-- Chọn danh mục cha --</option>
                                        @foreach($parentCategories as $category)
                                            <option value="{{ $category->id }}" {{ old('parent_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('parent_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <a href="{{ route('admin.blog.categories.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Quay lại
                    </a>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Lưu danh mục
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Auto generate slug from name
        $('input[name="name"]').on('input', function() {
            if (!$('#slug').val() || $('#slug').data('auto-generated')) {
                const slug = $(this).val()
                    .toLowerCase()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .replace(/[^\w\s-]/g, '')
                    .trim()
                    .replace(/[\s-]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                $('#slug').val(slug).data('auto-generated', true);
            }
        });

        // Auto fill meta title from name
        $('input[name="name"]').on('input', function() {
            if (!$('input[name="meta_title"]').val()) {
                $('input[name="meta_title"]').val($(this).val().substring(0, 60));
            }
        });

        // Handle slug manual edit
        $('#slug').on('input', function() {
            $(this).data('auto-generated', false);
        });

        // Preview image
        $('#image').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#image-preview').attr('src', e.target.result);
                };
                reader.readAsDataURL(file);
            }
        });

        // Preview icon
        $('#icon').on('input', function() {
            const iconClass = $(this).val() || 'fas fa-folder';
            $('#icon-preview').attr('class', iconClass);
        });
    });
</script>
@endpush
