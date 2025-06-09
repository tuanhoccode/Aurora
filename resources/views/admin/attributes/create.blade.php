@extends('admin.layouts.app')

@section('content')
    <div class="container py-4">
        <h1 class="mb-4 fw-bold">Create New Attribute</h1>

        @if (session('success'))
            <div class="alert alert-success shadow-sm rounded">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger shadow-sm rounded">{{ session('error') }}</div>
        @endif

        <form action="{{ route('admin.attributes.store') }}" method="POST" class="card p-4 shadow-sm rounded">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" name="name" id="name" class="form-control" required value="{{ old('name') }}">
                @error('name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <input type="hidden" name="is_variant" value="0">
                <label class="form-check-label">
                    <input type="checkbox" name="is_variant" value="1" class="form-check-input"
                        {{ old('is_variant') ? 'checked' : '' }}>
                    Is Variant
                </label>
            </div>

            <div class="mb-3">
                <input type="hidden" name="is_active" value="0">
                <label class="form-check-label">
                    <input type="checkbox" name="is_active" value="1" class="form-check-input"
                        {{ old('is_active', 1) ? 'checked' : '' }}>
                    Is Active
                </label>
            </div>

            <button type="submit" class="btn btn-primary shadow-sm rounded">
                <i class="bi bi-save"></i> Save Attribute
            </button>
            <a href="{{ route('admin.attributes.index') }}" class="btn btn-secondary shadow-sm rounded">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </form>
    </div>
@endsection
