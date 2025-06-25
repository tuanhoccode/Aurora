@extends('admin.layouts.app')

@section('content')
    <div class="container py-4">
        <h1 class="mb-4 fw-bold">Chi tiết tồn kho</h1>

        <div class="card shadow-sm rounded mb-4">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="bi bi-box-seam"></i> Sản phẩm: <strong>{{ $stock->product->name }}</strong>
                </h5>
                <p class="card-text">
                    <i class="bi bi-upc-scan"></i> Mã sản phẩm: <strong>{{ $stock->product->code }}</strong>
                </p>
                <p class="card-text">
                    <i class="bi bi-archive"></i> Số lượng trong kho: <strong>{{ $stock->stock }}</strong>
                </p>
                <p class="card-text">
                    <i class="bi bi-calendar-plus"></i> Ngày tạo: {{ $stock->created_at->format('d/m/Y H:i') }}
                </p>
                <p class="card-text">
                    <i class="bi bi-clock-history"></i> Cập nhật gần nhất: {{ $stock->updated_at->format('d/m/Y H:i') }}
                </p>
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('admin.stocks.edit', $stock->id) }}" class="btn btn-warning shadow-sm rounded">
                <i class="bi bi-pencil-square"></i> Chỉnh sửa
            </a>

            <form action="{{ route('admin.stocks.destroy', $stock->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tồn kho này không?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger shadow-sm rounded">
                    <i class="bi bi-trash"></i> Xóa
                </button>
            </form>

            <a href="{{ route('admin.stocks.index') }}" class="btn btn-secondary shadow-sm rounded">
                <i class="bi bi-arrow-left-circle"></i> Quay lại danh sách
            </a>
        </div>
    </div>
@endsection
