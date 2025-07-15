@extends('admin.layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="mb-4">Thêm mã giảm giá</h3>

    {{-- Hiển thị tất cả lỗi nếu có --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.coupons.store') }}" method="POST" novalidate>
        @csrf

        <div class="mb-3">
            <label for="code" class="form-label">Mã giảm giá <span class="text-danger">*</span></label>
            <input type="text" name="code" id="code" class="form-control" value="{{ old('code') }}">
            @error('code')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="title" class="form-label">Tiêu đề</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}">
            @error('title')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Mô tả</label>
            <textarea name="description" id="description" class="form-control" rows="3">{{ old('description') }}</textarea>
            @error('description')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="discount_type" class="form-label">Loại giảm <span class="text-danger">*</span></label>
                <select name="discount_type" id="discount_type" class="form-select">
                    <option value="percent" {{ old('discount_type') === 'percent' ? 'selected' : '' }}>Phần trăm</option>
                    <option value="fix_amount" {{ old('discount_type') === 'fix_amount' ? 'selected' : '' }}>Giá cố định</option>
                </select>
                @error('discount_type')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="discount_value" class="form-label">Giá trị giảm <span class="text-danger">*</span></label>
                <input type="text" inputmode="numeric" name="discount_value" id="discount_value" class="form-control" value="{{ old('discount_value') }}">
                @error('discount_value')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="start_date" class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date') }}">
                @error('start_date')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="end_date" class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date') }}">
                @error('end_date')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="usage_limit" class="form-label">Giới hạn sử dụng <span class="text-danger">*</span></label>
                <input type="text" inputmode="numeric" name="usage_limit" id="usage_limit" class="form-control" value="{{ old('usage_limit') }}">
                @error('usage_limit')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="usage_count" class="form-label">Đã sử dụng</label>
                <input type="text" inputmode="numeric" name="usage_count" id="usage_count" class="form-control" value="{{ old('usage_count', 0) }}" readonly>
                @error('usage_count')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-check form-switch mb-3">
            <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active') ? 'checked' : '' }}>
            <label for="is_active" class="form-check-label">Kích hoạt</label>
        </div>

        <div class="form-check form-switch mb-4">
            <input type="checkbox" name="is_notified" id="is_notified" class="form-check-input" value="1" {{ old('is_notified') ? 'checked' : '' }}>
            <label for="is_notified" class="form-check-label">Đã thông báo</label>
        </div>

        <button type="submit" class="btn btn-primary rounded-pill px-4">Tạo mã</button>
        <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Hủy</a>
    </form>
</div>
@endsection
