
@extends('admin.layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">📦 Danh sách sản phẩm</h2>
    <a href="{{ route('admin.products.create') }}" class="btn btn-success mb-3">
        <i class="fas fa-plus-circle"></i> Thêm sản phẩm
    </a>

    <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle text-center">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Thương hiệu</th>
                    <th>Tên</th>
                    <th>Ảnh</th>
                    <th>Giá</th>
                    <th>Sale</th>
                    <th>Hiển thị</th>
                    <th>SKU</th>
                    <th>Lượt xem</th>
                    <th>Ngày tạo</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Thương hiệu A</td>
                    <td class="text-start">Sản phẩm A</td>
                    <td>
                        <img src="https://via.placeholder.com/50" alt="ảnh" width="50" height="50" class="rounded shadow-sm">
                    </td>
                    <td><span class="text-decoration-line-through text-muted">1.000.000đ</span><br><strong class="text-danger">800.000đ</strong></td>
                    <td><span class="badge bg-danger"><i class="fas fa-bolt"></i> Có</span></td>
                    <td><span class="badge bg-success"><i class="fas fa-eye"></i> Hiện</span></td>
                    <td>SKU12345</td>
                    <td>123</td>
                    <td>01/01/2025</td>
                    <td>
                        <div class="d-flex justify-content-center gap-1">
                            <a href="{{ route('admin.products.edit') }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
