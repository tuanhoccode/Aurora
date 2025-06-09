@extends('admin.layouts.app')

@section('content')
    <div class="container py-4">
        <h1 class="mb-4 fw-bold">Thuộc tính đã xóa</h1>

        <a href="{{ route('admin.attributes.index') }}" class="btn btn-secondary mb-3">
            <i class="bi bi-arrow-left"></i> Quay lại danh sách
        </a>

        @if ($attributes->isEmpty())
            <div class="alert alert-info">Không có thuộc tính nào đã bị xóa.</div>
        @else
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên</th>
                        <th>Biến thể</th>
                        <th>Đang hoạt động</th>
                        <th>Thời gian xóa</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($attributes as $attribute)
                        <tr>
                            <td>{{ $attribute->id }}</td>
                            <td>{{ $attribute->name }}</td>
                            <td>{{ $attribute->is_variant ? 'Có' : 'Không' }}</td>
                            <td>{{ $attribute->is_active ? 'Hiện' : 'Ẩn' }}</td>
                            <td>{{ $attribute->deleted_at }}</td>
                            <td>
                                <div class="btn-group" role="group" aria-label="Actions">
                                    <form action="{{ route('admin.attributes.restore', $attribute->id) }}" method="POST"
                                        style="margin:0;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Restore">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.attributes.force-delete', $attribute->id) }}"
                                        method="POST" onsubmit="return confirm('Xóa vĩnh viễn?');" style="margin:0;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" title="Xóa vĩnh viễn">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
