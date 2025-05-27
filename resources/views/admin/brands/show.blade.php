@extends('admin.layouts.app')

@section('content')
    @if(session('success'))
        <div class="alert alert-success shadow-sm rounded mb-3">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger shadow-sm rounded mb-3">{{ session('error') }}</div>
    @endif
    <div class="container py-4">
        <h1 class="mb-4 fw-bold">Brand Details</h1>

        <table class="table table-bordered table-striped table-hover shadow-sm rounded">
            <tr>
                <th>ID</th>
                <td>{{ $brand->id }}</td>
            </tr>
            <tr>
                <th>Name</th>
                <td>{{ $brand->name }}</td>
            </tr>
            <tr>
                <th>Logo</th>
                <td>
                    @if ($brand->logo_url)
                        <img src="{{ $brand->logo_url }}" alt="Logo" style="max-width:150px;" class="rounded">
                    @else
                        N/A
                    @endif
                </td>
            </tr>
            <tr>
                <th>Status</th>
                <td>{{ $brand->is_active ? 'Active' : 'Inactive' }}</td>
            </tr>
            <tr>
                <th>Created At</th>
                <td>{{ $brand->created_at ? $brand->created_at->format('Y-m d H:i') : 'N/A' }}</td>
            </tr>
            <tr>
                <th>Updated At</th>
                <td>{{ $brand->updated_at ? $brand->updated_at->format('Y-m d H:i') : 'N/A' }}</td>
            </tr>

        </table>

        <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary shadow-sm rounded">Back to List</a>
        <a href="{{ route('admin.brands.edit', $brand->id) }}" class="btn btn-warning btn-sm shadow-sm rounded" title="Edit">
            <i class="bi bi-pencil-square"></i>
        </a>
    </div>
@endsection
