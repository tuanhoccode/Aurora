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

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           id="title" name="title" value="{{ old('title') }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="subtitle" class="form-label">Tiêu đề phụ</label>
                                    <input type="text" class="form-control @error('subtitle') is-invalid @enderror" 
                                           id="subtitle" name="subtitle" value="{{ old('subtitle') }}" 
                                           >
                                    @error('subtitle')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                 
                                </div>



                                <div class="mb-3">
                                    <label for="link" class="form-label">Link</label>
                                    <input type="url" class="form-control @error('link') is-invalid @enderror" 
                                           id="link" name="link" value="{{ old('link') }}" 
                                           placeholder="https://example.com">
                                    @error('link')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="position" class="form-label">Vị trí <span class="text-danger">*</span></label>
                                            <select class="form-select @error('position') is-invalid @enderror" 
                                                    id="position" name="position" required>
                                                <option value="">Chọn vị trí</option>
                                                <option value="slider" {{ old('position') == 'slider' ? 'selected' : '' }}>Slider</option>
                                                <option value="banner" {{ old('position') == 'banner' ? 'selected' : '' }}>Banner</option>
                                            </select>
                                            @error('position')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="sort_order" class="form-label">Thứ tự</label>
                                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                                   id="sort_order" name="sort_order" 
                                                   value="{{ old('sort_order', \App\Models\Banner::getNextAvailableSortOrder()) }}" 
                                                   min="0" placeholder="Tự động gán nếu để trống">
                                            @error('sort_order')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">
                                                <small class="text-muted">
                                                    Thứ tự hiện tại cao nhất: {{ \App\Models\Banner::max('sort_order') ?? 0 }}
                                                </small>
                                            </div>
                                        </div>
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
                                           id="image" name="image" accept="image/*" required>
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Định dạng: JPG, PNG, GIF, WEBP. Tối đa 2MB.</div>
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

@push('scripts')
<script>
$(document).ready(function() {
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
});
</script>
@endpush 