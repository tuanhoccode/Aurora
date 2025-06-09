@extends('admin.layouts.app')

@section('content')
    <div class="container py-4">
        <h1 class="mb-4 fw-bold">Attribute Values for {{ $attribute->name }} {{ $attribute->is_variant ? '(Variant)' : '' }}</h1>

        @if (session('success'))
            <div class="alert alert-success shadow-sm rounded">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger shadow-sm rounded">{{ session('error') }}</div>
        @endif

        <div class="mb-3">
            <a href="{{ route('admin.attributes.index') }}" class="btn btn-outline-secondary shadow-sm rounded">
                <i class="bi bi-arrow-left"></i> Back to Attributes
            </a>
            <a href="{{ route('admin.attribute_values.create', $attribute->id) }}" class="btn btn-primary shadow-sm rounded">
                <i class="bi bi-plus-circle"></i> Create New Value
            </a>
        </div>

        @if ($values->count())
            <table class="table table-bordered table-striped table-hover shadow-sm rounded">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Value</th>
                        <th>Is Active</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Deleted At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($values as $value)
                        <tr>
                            <td>{{ $value->id }}</td>
                            <td>{{ $value->value }}</td>
                            <td>{{ $value->is_active ? 'Active' : 'Inactive' }}</td>
                            <td>{{ $value->created_at ? $value->created_at->format('Y-m-d') : '-' }}</td>
                            <td>{{ $value->updated_at ? $value->updated_at->format('Y-m-d') : '-' }}</td>
                            <td>{{ $value->deleted_at ? $value->deleted_at->format('Y-m-d') : '-' }}</td>
                            <td>
                                @if ($value->deleted_at)
                                    <form action="{{ route('admin.attribute_values.restore', [$attribute->id, $value->id]) }}" method="POST" style="display:inline-block"
                                        onsubmit="return confirm('Are you sure you want to restore this value?');">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm shadow-sm rounded" title="Restore">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('admin.attribute_values.edit', [$attribute->id, $value->id]) }}"
                                        class="btn btn-warning btn-sm shadow-sm rounded" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('admin.attribute_values.destroy', [$attribute->id, $value->id]) }}" method="POST"
                                        style="display:inline-block"
                                        onsubmit="return confirm('Are you sure you want to delete this value?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm shadow-sm rounded" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No attribute values found.</p>
        @endif
    </div>
@endsection