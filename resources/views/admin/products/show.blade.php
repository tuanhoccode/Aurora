@extends('admin.layouts.app')

@section('title', 'Chi tiết sản phẩm')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="card bg-light-subtle border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-2">Chi tiết sản phẩm</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}" class="text-decoration-none">Sản phẩm</a></li>
                            <li class="breadcrumb-item active">Chi tiết</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Quay lại
                    </a>
                    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-1"></i> Chỉnh sửa
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Basic Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header border-0 bg-white py-3">
                    <h5 class="card-title mb-0 text-primary">
                        <i class="fas fa-info-circle me-2"></i>Thông tin cơ bản
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center mb-4">
                        <div class="col-auto">
                            @if($product->thumbnail)
                                <img src="{{ asset('storage/' . $product->thumbnail) }}" 
                                    alt="{{ $product->name }}" class="rounded shadow-sm" 
                                    style="width: 150px; height: 150px; object-fit: cover;">
                            @else
                                <img src="https://via.placeholder.com/150" 
                                    alt="Placeholder" class="rounded shadow-sm" 
                                    style="width: 150px; height: 150px; object-fit: cover;">
                            @endif
                        </div>
                        <div class="col">
                            <h4 class="mb-2">{{ $product->name }}</h4>
                            @if($product->short_description && $product->short_description !== $product->name)
                                <p class="text-muted mb-2">{{ $product->short_description }}</p>
                            @endif
                            <div class="d-flex gap-2">
                                <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $product->is_active ? 'Đang kinh doanh' : 'Ngừng kinh doanh' }}
                                </span>
                                @if($product->sale_price)
                                    <span class="badge bg-warning text-dark">Đang giảm giá</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Mã sản phẩm (SKU)</label>
                            <p class="fw-medium mb-0">{{ $product->sku }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Danh mục</label>
                            <p class="fw-medium mb-0">{{ $product->category->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Thương hiệu</label>
                            <p class="fw-medium mb-0">{{ $product->brand->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Ngày tạo</label>
                            <p class="fw-medium mb-0">{{ $product->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        @if(!empty($product->sizes))
                        <div class="col-md-6">
                            <label class="form-label text-muted">Kích thước</label>
                            <div class="d-flex flex-wrap gap-1">
                                @php
                                    $sizes = is_array($product->sizes) ? $product->sizes : json_decode($product->sizes, true);
                                    $sizes = is_array($sizes) ? $sizes : [$sizes];
                                @endphp
                                @foreach($sizes as $size)
                                    <span class="badge bg-light text-dark border">{{ $size }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        @if(!empty($product->colors))
                        <div class="col-md-6">
                            <label class="form-label text-muted">Màu sắc</label>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($product->colors as $color)
                                    <span class="badge bg-light text-dark border">{{ $color }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="card border-0 shadow-sm">
                <div class="card-header border-0 bg-white py-3">
                    <h5 class="card-title mb-0 text-primary">
                        <i class="fas fa-align-left me-2"></i>Mô tả chi tiết
                    </h5>
                </div>
                <div class="card-body">
                    {!! $product->description !!}
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Pricing -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header border-0 bg-white py-3">
                    <h5 class="card-title mb-0 text-primary">
                        <i class="fas fa-tag me-2"></i>Giá & Kho hàng
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">Giá gốc</label>
                        <p class="h4 mb-0">{{ number_format($product->price) }}đ</p>
                    </div>

                    @if($product->sale_price)
                        <div class="mb-3">
                            <label class="form-label text-muted">Giá khuyến mãi</label>
                            <p class="h4 text-danger mb-0">{{ number_format($product->sale_price) }}đ</p>
                        </div>
                    @endif

                    <div class="mb-0">
                        <label class="form-label text-muted">Số lượng tồn kho</label>
                        <p class="h4 mb-0">{{ number_format($product->stock) }}</p>
                    </div>
                </div>
            </div>

            <!-- Variants -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header border-0 bg-white py-3">
                    <h5 class="card-title mb-0 text-primary">
                        <i class="fas fa-list me-2"></i>Thuộc tính & Biến thể
                    </h5>
                </div>
                <div class="card-body">
                    @if($product->type === 'variant')
                        <!-- Product Attributes -->
                        @if($product->attributes->count() > 0)
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3">Thuộc tính sản phẩm:</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($product->attributes as $attribute)
                                        <div class="card border">
                                            <div class="card-body p-2">
                                                <h6 class="card-title mb-2">{{ $attribute->name }}</h6>
                                                <div class="d-flex flex-wrap gap-1">
                                                    @php
                                                        $values = json_decode($attribute->pivot->values);
                                                        $attributeValues = $attribute->values->whereIn('id', $values);
                                                    @endphp
                                                    @foreach($attributeValues as $value)
                                                        <span class="badge bg-light text-dark border">
                                                            {{ $value->value }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Product Variants -->
                        <h6 class="fw-bold mb-3">Danh sách biến thể:</h6>
                        @if($product->variants->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Biến thể</th>
                                            <th>SKU</th>
                                            <th>Giá</th>
                                            <th>Giá KM</th>
                                            <th>Tồn kho</th>
                                            <th>Trạng thái</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($product->variants as $variant)
                                            <tr>
                                                <td>
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach($variant->attributeValues as $value)
                                                            <span class="badge bg-light text-dark border">
                                                                {{ $value->attribute->name }}: {{ $value->value }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </td>
                                                <td><code>{{ $variant->sku }}</code></td>
                                                <td>{{ number_format($variant->price) }}đ</td>
                                                <td>
                                                    @if($variant->sale_price)
                                                        <span class="text-danger">{{ number_format($variant->sale_price) }}đ</span>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge {{ $variant->stock > 0 ? 'bg-success' : 'bg-danger' }}">
                                                        {{ number_format($variant->stock) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge {{ $variant->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                        {{ $variant->is_active ? 'Đang bán' : 'Ngừng bán' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                Sản phẩm chưa có biến thể nào.
                            </div>
                        @endif
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Đây là sản phẩm đơn giản, không có biến thể.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Gallery -->
            @if($product->gallery)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header border-0 bg-white py-3">
                        <h5 class="card-title mb-0 text-primary">
                            <i class="fas fa-images me-2"></i>Thư viện ảnh
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            @foreach(json_decode($product->gallery) as $image)
                                <div class="col-4">
                                    <img src="{{ asset('storage/' . $image) }}" 
                                         alt="Gallery image" 
                                         class="img-fluid rounded shadow-sm" 
                                         style="width: 100%; height: 100px; object-fit: cover;">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .form-label {
        color: #444;
    }
    .card {
        transition: all 0.2s ease;
    }
    .card:hover {
        transform: translateY(-2px);
    }
    .text-primary {
        color: #435ebe !important;
    }
    .btn-primary {
        background: #435ebe;
        border-color: #435ebe;
    }
    .btn-primary:hover {
        background: #364b96;
        border-color: #364b96;
    }
    .breadcrumb-item a {
        color: #435ebe;
    }
    .breadcrumb-item a:hover {
        color: #364b96;
    }
</style>
@endpush

@endsection


