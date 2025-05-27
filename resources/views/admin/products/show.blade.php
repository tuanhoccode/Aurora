@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-dark text-white">
            <h4><i class="fas fa-info-circle me-2"></i>Chi tiết sản phẩm</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Ảnh đại diện -->
                <div class="col-md-4 text-center">
                    <img src="https://via.placeholder.com/300" alt="Ảnh sản phẩm"
                         class="img-fluid rounded shadow-sm mb-3"
                         style="max-height: 300px; object-fit: cover;">

                    <p class="text-muted"><i class="fas fa-eye"></i> 123 lượt xem</p>
                    <p>
                        <span class="badge bg-success">Hiển thị</span>
                        <span class="badge bg-danger">Đang giảm giá</span>
                    </p>
                </div>

                <!-- Thông tin -->
                <div class="col-md-8">
                    <h3 class="mb-1">Sản phẩm demo</h3>
                    <p class="text-muted">Mô tả ngắn gọn của sản phẩm demo</p>

                    <div class="row">
                        <div class="col-md-6 mb-2"><strong>ID sản phẩm:</strong> 1</div>
                        <div class="col-md-6 mb-2"><strong>ID thương hiệu:</strong> 2</div>
                        <div class="col-md-6 mb-2"><strong>Loại:</strong> Điện thoại</div>
                        <div class="col-md-6 mb-2"><strong>SKU:</strong> SKU12345</div>
                        <div class="col-md-6 mb-2">
                            <strong>Giá gốc:</strong>
                            <span class="text-decoration-line-through text-muted">1.000.000đ</span>
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>Giá sale:</strong>
                            <span class="text-danger fw-bold">800.000đ</span>
                        </div>
                        <div class="col-md-6 mb-2"><strong>Ngày tạo:</strong> 01/01/2025</div>
                        <div class="col-md-6 mb-2"><strong>Ngày cập nhật:</strong> 05/01/2025</div>
                        <div class="col-md-6 mb-2"><strong>Xóa mềm:</strong> -</div>
                    </div>
                </div>
            </div>

            <!-- Mô tả chi tiết -->
            <div class="mt-4">
                <h5 class="mb-2"><i class="fas fa-file-alt me-2"></i>Mô tả chi tiết</h5>
                <div class="border rounded p-3 bg-light" style="min-height: 150px;">
                    Đây là mô tả chi tiết dạng HTML. Bạn có thể <strong>in đậm</strong>, <em>in nghiêng</em>, hoặc tạo danh sách:
                    <ul>
                        <li>Tính năng 1</li>
                        <li>Tính năng 2</li>
                    </ul>
                </div>
            </div>

            <!-- Nút hành động -->
            <div class="mt-4">
                <a href="#" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
                <a href="#" class="btn btn-warning"><i class="fas fa-edit"></i> Chỉnh sửa</a>
            </div>
        </div>
    </div>
</div>
@endsection
