@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm rounded">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-bold">Thêm thuộc tính mới</h5>
                         <a href="{{ route('admin.attributes.index') }}" class="btn btn-secondary btn-sm shadow-sm rounded">
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
                              action="{{ route('admin.attributes.store') }}"
                              id="createAttributeForm">
                            @csrf

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="name" class="form-label fw-bold">Tên thuộc tính <span class="text-danger">*</span></label>
                                    <input type="text"
                                           name="name"
                                           id="name"
                                           class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name') }}"
                                           maxlength="100"
                                           placeholder="Nhập tên thuộc tính">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="is_variant" class="form-label fw-bold">Loại thuộc tính <span class="text-danger">*</span></label>
                                    <select name="is_variant"
                                            id="is_variant"
                                            class="form-select @error('is_variant') is-invalid @enderror">
                                        <option value="0" {{ old('is_variant', 0) == 0 ? 'selected' : '' }}>Thuộc tính thường</option>
                                        <option value="1" {{ old('is_variant', 0) == 1 ? 'selected' : '' }}>Thuộc tính biến thể</option>
                                    </select>
                                    @error('is_variant')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
