@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa thuộc tính')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="card bg-light-subtle border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-2">Chỉnh sửa thuộc tính</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.attributes.index') }}" class="text-decoration-none">Thuộc tính</a></li>
                            <li class="breadcrumb-item active">Chỉnh sửa</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('admin.attributes.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.attributes.update', $attribute) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label fw-medium">Tên thuộc tính <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $attribute->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="type" class="form-label fw-medium">Loại thuộc tính <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" 
                                    id="type" name="type" required>
                                <option value="select" {{ old('type', $attribute->type) === 'select' ? 'selected' : '' }}>Lựa chọn</option>
                                <option value="text" {{ old('type', $attribute->type) === 'text' ? 'selected' : '' }}>Văn bản</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_active" 
                               name="is_active" value="1" {{ old('is_active', $attribute->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label fw-medium" for="is_active">
                            Kích hoạt thuộc tính
                        </label>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 