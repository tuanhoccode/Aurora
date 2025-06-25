@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm rounded">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-bold">Thêm danh mục mới</h5>
                         <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary btn-sm shadow-sm rounded">
                            <i class="mdi mdi-arrow-left me-1"></i> Quay lại danh sách
                        </a>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success shadow-sm rounded mb-3">{{ session('success') }}</div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger shadow-sm rounded mb-3">{{ session('error') }}</div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-danger shadow-sm rounded mb-3">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                       <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" 
                              action="{{ route('admin.categories.store') }}" 
                              enctype="multipart/form-data"
                              id="createCategoryForm">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label fw-bold">Tên danh mục <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="name" 
                                           id="name" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name') }}" 
                                           maxlength="100" 
                                           placeholder="Nhập tên danh mục">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="parent_id" class="form-label fw-bold">Danh mục cha</label>
                                    <select name="parent_id" 
                                            id="parent_id" 
                                            class="form-select @error('parent_id') is-invalid @enderror">
                                        <option value="">Chọn danh mục cha</option>
                                        @foreach($categories as $category)
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

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="icon" class="form-label fw-bold">Ảnh danh mục</label>
                                    <input type="file" 
                                           name="icon" 
                                           id="icon" 
                                           class="form-control @error('icon') is-invalid @enderror"
                                           accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
                                    <div class="form-text">
                                        Định dạng: JPG, JPEG, PNG, GIF, WEBP. Kích thước tối đa: 2MB
                                    </div>
                                    @error('icon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div id="imagePreview" class="mt-2 d-none">
                                        <img src="" alt="Preview" class="img-thumbnail" style="max-height: 200px;">
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="is_active" class="form-label fw-bold">Trạng thái <span class="text-danger">*</span></label>
                                    <select name="is_active" 
                                            id="is_active" 
                                            class="form-select @error('is_active') is-invalid @enderror">
                                        <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>Đang hoạt động</option>
                                        <option value="0" {{ old('is_active', 1) == 0 ? 'selected' : '' }}>Không hoạt động</option>
                                    </select>
                                    @error('is_active')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-plus-circle me-1"></i> Thêm mới
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
        // Initialize Select2 for parent category
        $('#parent_id').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Chọn danh mục cha'
        });

        // Image Preview
        $('#icon').change(function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview').removeClass('d-none').find('img').attr('src', e.target.result);
                }
                reader.readAsDataURL(file);
            } else {
                $('#imagePreview').addClass('d-none').find('img').attr('src', '');
            }
        });
    });
</script>
@endpush 