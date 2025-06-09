@extends('admin.layouts.app')

@section('content')
    <div class="container py-4">
        <h1 class="mb-4 fw-bold">Attributes</h1>

        @if (session('success'))
            <div class="alert alert-success shadow-sm rounded">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger shadow-sm rounded">{{ session('error') }}</div>
        @endif

        <div class="mb-3">
            <a href="{{ route('admin.attributes.create') }}" class="btn btn-primary shadow-sm rounded">
                <i class="bi bi-plus-circle"></i> Create New Attribute
            </a>
            <a href="{{ route('admin.attributes.trashed') }}" class="btn btn-outline-secondary shadow-sm rounded">
                <i class="bi bi-trash"></i> Trashed Attributes
            </a>
            
        </div>

        @if ($attributes->count())
            <table class="table table-bordered table-striped table-hover shadow-sm rounded">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Is Variant</th>
                        <th>Active</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($attributes as $attribute)
                        <tr>
                            <td>{{ $attribute->id }}</td>
                            <td>{{ $attribute->name }}</td>
                            <td>{{ $attribute->is_variant ? 'Yes' : 'No' }}</td>
                            <td>{{ $attribute->is_active ? 'Active' : 'Inactive' }}</td>
                            <td>{{ $attribute->created_at ? $attribute->created_at->format('Y-m-d') : '-' }}</td>
                            <td>
                                <a href="{{ route('admin.attribute_values.index', $attribute->id) }}"
                                    class="btn btn-outline-primary btn-sm shadow-sm rounded" title="Manage Values">
                                    <i class="bi bi-list-check"></i>
                                </a>
                                <a href="{{ route('admin.attributes.edit', $attribute->id) }}"
                                    class="btn btn-warning btn-sm shadow-sm rounded" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.attributes.destroy', $attribute->id) }}" method="POST"
                                    style="display:inline-block"
                                    onsubmit="return confirm('Are you sure you want to delete this attribute?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm shadow-sm rounded" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No attributes found.</p>
        @endif
    </div>
@endsection