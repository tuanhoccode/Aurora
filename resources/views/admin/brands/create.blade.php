@extends('admin.layouts.app')

@section('content')
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
          enctype="multipart/form-data" class="shadow-sm rounded p-3 bg-white">
        @csrf
        <div class="form-group mb-3">
            <label for="name" class="fw-bold">Brand Name</label>
            <input type="text" name="name" id="name" class="form-control rounded" 
                value="{{ old('name') }}" required maxlength="100" placeholder="Enter brand name">
        </div>
        <div class="form-group mb-3">
            <label for="logo" class="fw-bold">Brand Logo</label>
            <input type="file" name="logo" id="logo" class="form-control-file rounded">
        </div>
        <div class="form-group mb-3">
            <label for="is_active" class="fw-bold">Status</label>
            <select name="is_active" id="is_active" class="form-control rounded" required>
                <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>Active</option>
                <option value="0" {{ old('is_active', 1) == 0 ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary shadow-sm rounded">
            Create
        </button>
        <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary ms-2">Back to List</a>
    </form>
@endsection
