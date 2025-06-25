@extends('admin.layouts.app')

@section('content')
    <div class="container py-4">
        <h1 class="mb-4 fw-bold">Thêm tồn kho mới</h1>

        @if($errors->any())
            <div class="alert alert-danger shadow-sm rounded">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card shadow-sm rounded">
            <div class="card-body">
                <form action="{{ route('admin.stocks.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="product_id" class="form-label">Sản phẩm</label>
                        <select name="product_id" id="product_id" class="form-select" required>
                            <option value="">-- Chọn sản phẩm --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="stock" class="form-label">Số lượng</label>
                        <input type="number" name="stock" id="stock" class="form-control" min="0" required>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success shadow-sm rounded">
                            <i class="bi bi-save"></i> Lưu
                        </button>
                        <a href="{{ route('admin.stocks.index') }}" class="btn btn-secondary shadow-sm rounded">
                            <i class="bi bi-x-circle"></i> Hủy
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
