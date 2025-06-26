@extends('admin.layouts.app')

@section('content')
    <div class="container py-4">
        <h1 class="mb-4 fw-bold">Danh sách ảnh sản phẩm</h1>

        <div class="mb-3">
            <a href="{{ route('admin.product-images.create') }}" class="btn btn-primary">
                + Thêm ảnh mới
            </a>
        </div>

        <table class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Ảnh</th>
                    <th>Sản phẩm</th>
                    <th>Biến thể</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($images as $image)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <img src="{{ asset('storage/' . $image->url) }}" width="100" alt="Ảnh sản phẩm" class="img-thumbnail">
                        </td>
                        <td>{{ $image->product->name ?? '(Không rõ sản phẩm)' }}</td>
                        <td>{{ $image->variant->sku ?? '(Không có biến thể)' }}</td>
                        <td>
                            <form action="{{ route('admin.product-images.destroy', $image->id) }}" method="POST"
                                  onsubmit="return confirm('Bạn có chắc chắn muốn xóa ảnh này không?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">Chưa có ảnh nào được thêm.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
