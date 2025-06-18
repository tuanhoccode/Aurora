@extends('admin.layouts.app')

@section('title', 'Quản lý giá trị thuộc tính')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="card bg-light-subtle border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-2">Giá trị thuộc tính: {{ $attribute->name }}</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.attributes.index') }}" class="text-decoration-none">Thuộc tính</a></li>
                            <li class="breadcrumb-item active">Giá trị</li>
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

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Add Value Form -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-0 bg-white py-3">
                    <h5 class="card-title mb-0 text-primary">
                        <i class="fas fa-plus me-2"></i>Thêm giá trị mới
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.attributes.values.store', $attribute) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="value" class="form-label fw-medium">Giá trị <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('value') is-invalid @enderror" 
                                   id="value" name="value" value="{{ old('value') }}" required>
                            @error('value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if($attribute->type === 'select')
                            <div class="mb-3">
                                <label for="color_code" class="form-label fw-medium">Mã màu</label>
                                <div class="input-group">
                                    <span class="input-group-text">#</span>
                                    <input type="text" class="form-control @error('color_code') is-invalid @enderror" 
                                           id="color_code" name="color_code" value="{{ old('color_code') }}"
                                           placeholder="FF0000">
                                </div>
                                @error('color_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Chỉ áp dụng cho thuộc tính màu sắc</small>
                            </div>
                        @endif

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Thêm giá trị
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Values List -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-0 bg-white py-3">
                    <h5 class="card-title mb-0 text-primary">
                        <i class="fas fa-list me-2"></i>Danh sách giá trị
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Giá trị</th>
                                    @if($attribute->type === 'select')
                                        <th scope="col">Mã màu</th>
                                    @endif
                                    <th scope="col">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($values as $value)
                                    <tr>
                                        <td>{{ $value->id }}</td>
                                        <td>{{ $value->value }}</td>
                                        @if($attribute->type === 'select')
                                            <td>
                                                @if($value->color_code)
                                                    <div class="d-flex align-items-center">
                                                        <div class="color-preview me-2" 
                                                             style="width: 20px; height: 20px; border-radius: 4px; background-color: #{{ $value->color_code }}">
                                                        </div>
                                                        #{{ $value->color_code }}
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        @endif
                                        <td>
                                            <form action="{{ route('admin.attributes.values.destroy', [$attribute, $value]) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                        onclick="return confirm('Bạn có chắc chắn muốn xóa giá trị này?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $attribute->type === 'select' ? 4 : 3 }}" class="text-center py-4">
                                            <div class="text-muted">Chưa có giá trị nào</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        {{ $values->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 