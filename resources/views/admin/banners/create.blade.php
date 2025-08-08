@extends('admin.layouts.app')

@section('title', 'Thêm Banner')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Thêm Banner</h4>
                <div class="page-title-right">
                    <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="row">
            <div class="col-12">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h5 class="alert-heading">
                        <i class="fas fa-exclamation-triangle"></i> Có lỗi xảy ra!
                    </h5>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           id="title" name="title" value="{{ old('title') }}" 
                                           placeholder="Nhập tiêu đề banner">
                                    @error('title')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="subtitle" class="form-label">Tiêu đề phụ</label>
                                    <input type="text" class="form-control @error('subtitle') is-invalid @enderror" 
                                           id="subtitle" name="subtitle" value="{{ old('subtitle') }}" 
                                           placeholder="Nhập tiêu đề phụ (tùy chọn)">
                                    @error('subtitle')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="link" class="form-label">Link</label>
                                    <input type="url" class="form-control @error('link') is-invalid @enderror" 
                                           id="link" name="link" value="{{ old('link') }}" 
                                           placeholder="https://example.com">
                                    @error('link')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="sort_order" class="form-label">Thứ tự</label>
                                    <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                           id="sort_order" name="sort_order" 
                                           value="{{ old('sort_order', \App\Models\Banner::getNextAvailableSortOrder()) }}" 
                                           min="0" placeholder="Tự động gán nếu để trống">
                                    @error('sort_order')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <small class="text-muted">
                                            Thứ tự hiện tại cao nhất: {{ \App\Models\Banner::max('sort_order') ?? 0 }}
                                        </small>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                               {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Kích hoạt banner
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="image" class="form-label">Ảnh banner <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                           id="image" name="image" accept="image/*">
                                    @error('image')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Định dạng: JPG, PNG, GIF, WEBP. Tối đa 2MB. Kích thước tối thiểu: 800x400px.</div>
                                </div>

                                <div class="mb-3">
                                    <div id="image-preview" class="d-none">
                                        <img id="preview-img" src="" alt="Preview" 
                                             class="img-fluid rounded" style="max-height: 200px;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Lưu Banner
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .form-control.is-invalid {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }
    
    .invalid-feedback.d-block {
        display: block !important;
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
        font-weight: 500;
    }
    
    .alert-danger {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
        border-radius: 0.375rem;
    }
    
    .alert-heading {
        color: #721c24;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .form-label .text-danger {
        font-weight: bold;
    }
    
    /* Highlight required fields */
    .form-label:has(.text-danger) {
        font-weight: 600;
    }
    
    /* Better error styling */
    .alert ul {
        margin-bottom: 0;
        padding-left: 1.5rem;
    }
    
    .alert li {
        margin-bottom: 0.25rem;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Disable HTML5 validation
    $('form').attr('novalidate', true);
    
    // Image preview
    $('#image').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#preview-img').attr('src', e.target.result);
                $('#image-preview').removeClass('d-none');
            }
            reader.readAsDataURL(file);
        } else {
            $('#image-preview').addClass('d-none');
        }
    });
    
    // Auto-hide alert after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
    
    // Form submission handling
    $('form').on('submit', function(e) {
        // Clear any previous validation styling
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').hide();
    });
});
</script>
@endpush 