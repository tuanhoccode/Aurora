@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 fw-bold">Chi tiết tồn kho</h1>
            <a href="{{ route('admin.stocks.index') }}" class="btn btn-secondary shadow-sm rounded">
                <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success shadow-sm rounded mb-3">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger shadow-sm rounded mb-3">{{ session('error') }}</div>
        @endif

        <div class="card shadow-sm rounded">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center mb-3">
                        @if($stock->product->image)
                            <img src="{{ asset('storage/' . $stock->product->image) }}" 
                                 alt="{{ $stock->product->name }}" 
                                 class="img-fluid rounded border" 
                                 style="max-width: 200px; height: auto; object-fit: contain;">
                        @else
                            <div class="text-muted small border p-3 rounded">
                                <i class="bi bi-image me-1"></i><br>
                                Không có ảnh sản phẩm
                            </div>
                        @endif
                    </div>
                    <div class="col-md-8">
                        <dl class="row">
                            <dt class="col-sm-3">ID:</dt>
                            <dd class="col-sm-9">{{ $stock->id }}</dd>

                            <dt class="col-sm-3">Sản phẩm:</dt>
                            <dd class="col-sm-9">
                                <a href="{{ route('admin.products.show', $stock->product_id) }}" 
                                   class="badge bg-info text-decoration-none">
                                    {{ $stock->product->name }}
                                </a>
                            </dd>

                            <dt class="col-sm-3">Số lượng tồn kho:</dt>
                            <dd class="col-sm-9">
                                <span class="badge rounded-pill bg-info-subtle text-info px-3 py-2">
                                    <i class="bi bi-box me-1 small"></i>
                                    {{ $stock->stock }}
                                </span>
                            </dd>

                            <dt class="col-sm-3">Ngày tạo:</dt>
                            <dd class="col-sm-9">{{ $stock->created_at ? $stock->created_at->format('d/m/Y H:i') : 'N/A' }}</dd>

                            <dt class="col-sm-3">Ngày cập nhật:</dt>
                            <dd class="col-sm-9">{{ $stock->updated_at ? $stock->updated_at->format('d/m/Y H:i') : 'N/A' }}</dd>
                        </dl>

                        <div class="mt-4">
                            <a href="{{ route('admin.stocks.edit', $stock->id) }}" class="btn btn-warning shadow-sm rounded me-2">
                                <i class="bi bi-pencil-square me-1"></i> Chỉnh sửa
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
