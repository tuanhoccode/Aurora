@extends('admin.layouts.app')

@section('content')
    <div class="container py-4">
        <h1 class="mb-4 fw-bold">Danh sách tồn kho</h1>

        @if (session('success'))
            <div class="alert alert-success shadow-sm rounded">{{ session('success') }}</div>
        @endif

        <div class="mb-3">
            <a href="{{ route('admin.stocks.create') }}" class="btn btn-primary shadow-sm rounded">
                <i class="bi bi-plus-circle"></i> Thêm tồn kho mới
            </a>
        </div>

        @if ($stocks->count())
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover shadow-sm rounded">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Sản phẩm</th>
                            <th>Số lượng</th>
                            <th>Ngày tạo</th>
                            <th>Ngày cập nhật</th>
                            <th class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($stocks as $stock)
                            <tr>
                                <td>{{ $stock->id }}</td>
                                <td>{{ optional($stock->product)->name ?? 'Không có tên' }}</td>
                                <td>
                                    {{ $stock->stock }}
                                    {{ optional($stock->product)->unit ?? '' }}
                                </td>
                                <td>{{ $stock->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $stock->updated_at->format('d/m/Y H:i') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.stocks.show', $stock->id) }}"
                                       class="btn btn-outline-info btn-sm shadow-sm rounded" title="Chi tiết">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.stocks.edit', $stock->id) }}"
                                       class="btn btn-warning btn-sm shadow-sm rounded" title="Sửa">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('admin.stocks.destroy', $stock->id) }}" method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Bạn có chắc chắn muốn xóa?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm shadow-sm rounded" title="Xóa">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- PHÂN TRANG -->
            <div class="mt-3">
                {{ $stocks->links('pagination::bootstrap-5') }}
            </div>
        @else
            <div class="alert alert-info shadow-sm rounded">
                Không có dữ liệu tồn kho nào.
            </div>
        @endif
    </div>
@endsection
