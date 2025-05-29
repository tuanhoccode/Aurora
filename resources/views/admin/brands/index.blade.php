@extends('admin.layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4 fw-bold">Brands List</h1>

    @if(session('success'))
        <div class="alert alert-success shadow-sm rounded">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger shadow-sm rounded">{{ session('error') }}</div>
    @endif

    <a href="{{ route('admin.brands.create') }}" class="btn btn-primary mb-3 shadow-sm rounded">
        <i class="bi bi-plus-circle"></i> Create New Brand
    </a>

    @if($brands->count())
        <table class="table table-bordered table-striped table-hover shadow-sm rounded">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Logo</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($brands as $brand)
                <tr>
                    <td>{{ $brand->id }}</td>
                    <td>{{ $brand->name }}</td>
                    <td>
                        @if($brand->logo_url)
                            <img src="{{ $brand->logo_url }}" alt="Logo" style="max-width:80px;" class="rounded">
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ $brand->is_active ? 'Active' : 'Inactive' }}</td>
                    <td>{{ $brand->created_at->format('Y-m-d') }}</td>
                    <td>
                        <a href="{{ route('admin.brands.show', $brand->id) }}" class="btn btn-info btn-sm shadow-sm rounded" title="View">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('admin.brands.edit', $brand->id) }}" class="btn btn-warning btn-sm shadow-sm rounded" title="Edit">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        <form action="{{ route('admin.brands.destroy', $brand->id) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Are you sure you want to delete this brand?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm shadow-sm rounded" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                        @if($brand->deleted_at)
                        <form action="{{ route('admin.brands.forceDelete', $brand->id) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Force delete? This cannot be undone!');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-dark btn-sm shadow-sm rounded" title="Force Delete">
                                <i class="bi bi-x-octagon"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No brands found.</p>
    @endif
</div>
@endsection
