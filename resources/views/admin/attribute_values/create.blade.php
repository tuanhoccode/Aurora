@extends('admin.layouts.app')

@section('content')
    <div class="container py-4">
        <h1 class="mb-4 fw-bold">Create Value for {{ $attribute->name }} {{ $attribute->is_variant ? '(Variant)' : '' }}</h1>

        @if (session('success'))
            <div class="alert alert-success shadow-sm rounded">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger shadow-sm rounded">{{ session('error') }}</div>
        @endif

        <a href="{{ route('admin.attribute_values.index', $attribute->id) }}" class="btn btn-outline-secondary mb-3 shadow-sm rounded">
            <i class="bi bi-arrow-left"></i> Back to Values
        </a>

        <form action="{{ route('admin.attribute_values.store', $attribute->id) }}" method="POST" class="card shadow-sm rounded p-4">
            @csrf
            <div class="mb-3">
                <label for="value" class="form-label">Value</label>
                <input type="text" name="value" id="value" class="form-control rounded @error('value') is-invalid @enderror" value="{{ old('value') }}" required>
                @error('value')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="is_active" class="form-label">Status</label>
                <select name="is_active" id="is_active" class="form-select rounded @error('is_active') is-invalid @enderror" required>
                    <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('is_active', 1) == 0 ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('is_active')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary shadow-sm rounded">
                <i class="bi bi-save"></i> Create Value
            </button>
        </form>
    </div>
@endsection