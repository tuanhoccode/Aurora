@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h2>➕ Thêm sản phẩm mới</h2>
    <form action="#" method="POST">
        @csrf

        <div class="row">
            <!-- Cột trái -->
            <div class="col-md-8">
                <div class="mb-3">
                    <label for="name" class="form-label">Tên sản phẩm</label>
                    <input type="text" name="name" class="form-control" placeholder="Nhập tên sản phẩm">
                </div>

                <div class="mb-3">
                    <label for="short_description" class="form-label">Mô tả ngắn</label>
                    <textarea name="short_description" class="form-control" rows="2"></textarea>
                </div>

                <div class="mb-3">
                    <label for="category_id" class="form-label">Danh mục sản phẩm</label>
                    <select name="category_id" class="form-select">
                        <option value="">-- Chọn danh mục --</option>
                        <option value="1">Điện thoại</option>
                        <option value="2">Laptop</option>
                        <option value="3">Phụ kiện</option>
                        <option value="4">Máy tính bảng</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="brand_id" class="form-label">Thương hiệu</label>
                    <select name="brand_id" class="form-select">
                        <option value="">-- Chọn thương hiệu --</option>
                        <option value="1">Apple</option>
                        <option value="2">Samsung</option>
                        <option value="3">Xiaomi</option>
                        <option value="4">Oppo</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Mô tả chi tiết (soạn thảo như Word)</label>
                    <div style="border: 1px solid #ced4da; border-radius: 4px; padding: 4px;">
                        <textarea name="description" id="editor" rows="15"></textarea>
                    </div>
                </div>
            </div>

            <!-- Cột phải -->
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header">Thông tin sản phẩm</div>
                    <div class="card-body">
                        <div class="mb-2">
                            <label>Giá gốc</label>
                            <input type="number" name="price" class="form-control">
                        </div>
                        <div class="mb-2">
                            <label>Giá sale</label>
                            <input type="number" name="sale_price" class="form-control">
                        </div>
                        <div class="mb-2">
                            <label>SKU</label>
                            <input type="text" name="sku" class="form-control">
                        </div>
                        <div class="mb-2">
                            <label>Loại sản phẩm</label>
                            <input type="text" name="type" class="form-control">
                        </div>
                        <div class="mb-2">
                            <label>Ảnh đại diện (URL)</label>
                            <input type="text" name="thumbnail" class="form-control">
                        </div>
                        <div class="mb-2">
                            <label>Hiển thị</label>
                            <select name="is_active" class="form-select">
                                <option value="1">Hiển thị</option>
                                <option value="0">Ẩn</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label>Trạng thái sale</label>
                            <select name="is_sale" class="form-select">
                                <option value="1">Đang sale</option>
                                <option value="0">Không</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Lưu sản phẩm</button>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Quay lại</a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<!-- CKEditor -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#editor'))
        .catch(error => {
            console.error(error);
        });
</script>
@endsection
