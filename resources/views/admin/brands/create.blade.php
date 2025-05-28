@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm rounded">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-bold">Thêm thương hiệu mới</h5>
                         <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary btn-sm shadow-sm rounded">
                            <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
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
                              action="{{ route('admin.brands.store') }}" 
                              enctype="multipart/form-data"
                              id="createBrandForm">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label fw-bold">Tên thương hiệu <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="name" 
                                           id="name" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name') }}" 
                                           required 
                                           maxlength="100" 
                                           placeholder="Nhập tên thương hiệu">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                 <div class="col-md-6 mb-3">
                                    <label for="is_active" class="form-label fw-bold">Trạng thái <span class="text-danger">*</span></label>
                                    <select name="is_active" 
                                            id="is_active" 
                                            class="form-select @error('is_active') is-invalid @enderror" 
                                            required>
                                        <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>Đang hoạt động</option>
                                        <option value="0" {{ old('is_active', 1) == 0 ? 'selected' : '' }}>Không hoạt động</option>
                                    </select>
                                    @error('is_active')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div> {{-- End row --}}

                            <div class="mb-3">
                                <label for="logo" class="form-label fw-bold">Logo thương hiệu</label>
                                <input type="file" 
                                       name="logo" 
                                       id="logo" 
                                       class="form-control @error('logo') is-invalid @enderror"
                                       accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                                       onchange="previewImage(this)">
                                <div class="form-text">
                                    Định dạng: JPG, JPEG, PNG, GIF, WEBP. Kích thước tối đa: 2MB
                                </div>
                                @error('logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div id="imagePreview" class="mt-2 d-none">
                                    <img src="" alt="Preview" class="img-thumbnail" style="max-height: 200px;">
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-1"></i> Thêm mới
                                </button>
                                {{-- Nút quay lại đã chuyển lên card header --}}
                                {{-- <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left me-1"></i> Quay lại
                                </a> --}}
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            const previewImg = preview.querySelector('img');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.classList.remove('d-none');
                }
                
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.classList.add('d-none');
                previewImg.src = '';
            }
        }

        // Validate form trước khi submit
        document.getElementById('createBrandForm').addEventListener('submit', function(e) {
            const logoInput = document.getElementById('logo');
            const file = logoInput.files[0];
            
            if (file) {
                // Kiểm tra kích thước file (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    e.preventDefault();
                    alert('Kích thước file không được vượt quá 2MB');
                    return;
                }
                
                // Kiểm tra định dạng file
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    e.preventDefault();
                    alert('Định dạng file không hợp lệ. Chỉ chấp nhận: JPG, JPEG, PNG, GIF, WEBP');
                    return;
                }
            }
        });
    </script>
    @endpush
@endsection
