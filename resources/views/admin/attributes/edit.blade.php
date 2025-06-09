@extends('admin.layouts.app')

@section('content')
    <div class="container py-4">
        <h1 class="mb-4 fw-bold">Edit Attribute</h1>

        @if (session('success'))
            <div class="alert alert-success shadow-sm rounded">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger shadow-sm rounded">{{ session('error') }}</div>
        @endif

        <form action="{{ route('admin.attributes.update', $attribute->id) }}" method="POST" class="card p-4 shadow-sm rounded">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" name="name" id="name" class="form-control" required
                    value="{{ old('name', $attribute->name) }}">
                @error('name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3 form-check">
                <input type="hidden" name="is_variant" value="0">
                <input type="checkbox" name="is_variant" value="1" class="form-check-input"
                    {{ old('is_variant', $attribute->is_variant) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_variant">Is Variant</label>
            </div>

            <div class="mb-3 form-check">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" class="form-check-input"
                    {{ old('is_active', $attribute->is_active) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Is Active</label>
            </div>

            <button type="submit" class="btn btn-primary shadow-sm rounded">
                <i class="bi bi-save"></i> Update Attribute
            </button>
            <a href="{{ route('admin.attributes.index') }}" class="btn btn-secondary shadow-sm rounded">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </form>
    </div>
@endsection
