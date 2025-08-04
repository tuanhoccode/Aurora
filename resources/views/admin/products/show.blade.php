@extends('admin.layouts.app')

@section('title', 'Chi tiết sản phẩm')

@push('styles')
<style>
.price-original {
    text-decoration: line-through;
    color: #6c757d;
    font-size: 0.85rem;
}
.price-sale {
    color: #dc3545;
    font-weight: bold;
    font-size: 0.95rem;
}
.discount-badge {
/* Custom style for variant attribute badges */
.badge.bg-secondary {
  background-color: #e4e4e4 !important;
  color: #333 !important;
}
    font-size: 0.7rem;
    padding: 0.2rem 0.4rem;
}
.variant-price-container {
    min-width: 120px;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Chi tiết sản phẩm</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Sản phẩm</a></li>
                    <li class="breadcrumb-item active">{{ $product->name }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary">
                <i class="bi bi-pencil-square"></i> Chỉnh sửa
            </a>
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                <i class="bi bi-trash"></i> Xóa
            </button>
        </div>
    </div>

    <!-- Hàng đầu tiên: Thông tin sản phẩm và hình ảnh -->
    <div class="row">
        <!-- Thông tin cơ bản -->
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3 d-flex align-items-center">
                    <h5 class="card-title mb-0">Thông tin cơ bản</h5>
                    <span class="ms-2 badge {{ $product->is_active ? 'bg-success' : 'bg-danger' }}">
                        {{ $product->is_active ? 'Đang kinh doanh' : 'Ngừng kinh doanh' }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">Mã sản phẩm:</th>
                                    <td><code>{{ $product->sku }}</code></td>
                                </tr>
                                <tr>
                                    <th>Tên sản phẩm:</th>
                                    <td>{{ $product->name }}</td>
                                </tr>
                                <tr>
                                    <th>Slug:</th>
                                    <td><code>{{ $product->slug }}</code></td>
                                </tr>
                                <tr>
                                    <th>Loại sản phẩm:</th>
                                    <td>
                                        @switch($product->type)
                                            @case('simple')
                                                <span class="badge bg-primary">Sản phẩm đơn giản</span>
                                                @break
                                            @case('variant')
                                                <span class="badge bg-info">Sản phẩm có biến thể</span>
                                                @break
                                            @case('digital')
                                                <span class="badge bg-warning">Sản phẩm số</span>
                                                @break
                                        @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <th>Danh mục:</th>
                                    <td>
                                        @foreach($product->categories as $category)
                                            <span class="badge bg-secondary">{{ $category->name }}</span>
                                        @endforeach
                                    </td>
                                </tr>
                                <tr>
                                    <th>Thương hiệu:</th>
                                    <td>
                                        @if($product->brand)
                                            <span class="badge bg-dark">{{ $product->brand->name }}</span>
                                        @else
                                            <span class="text-muted">Không có</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Lượt mua:</th>
                                    <td>
                                        <span class="badge bg-gradient-primary text-white fs-6 px-3 py-2" style="background: linear-gradient(90deg,#36d1c4 0,#5b86e5 100%); box-shadow:0 2px 8px rgba(54,209,196,0.15);">
                                            <i class="bi bi-cart-check me-1"></i>
                                            {{ number_format($product->getSuccessfulOrderItems()->sum('quantity')) }} lượt mua
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                @if($product->type === 'digital')
                                <tr>
                                    <th>File đính kèm:</th>
                                    <td>
                                        @if($product->digital_file)
                                            <a href="{{ asset('storage/' . $product->digital_file) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-download"></i> Tải xuống
                                            </a>
                                        @else
                                            <span class="text-muted">Chưa có file</span>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mô tả -->
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3">
                    <h5 class="card-title mb-0">Mô tả sản phẩm</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">Mô tả ngắn:</h6>
                        <p class="text-muted">{!! $product->short_description ?: 'Không có mô tả ngắn' !!}</p>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-3">Mô tả chi tiết:</h6>
                        <div class="border rounded p-3 bg-light">
                            {!! $product->description ?: 'Không có mô tả chi tiết' !!}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thông tin giá -->
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3">
                    <h5 class="card-title mb-0">Thông tin giá</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="150">Giá:</th>
                            <td>
                                @if($product->type === 'variant' && $product->variants->count() > 0)
                                    @php $variant = $product->variants->first(); @endphp
                                    @if($variant->sale_price > 0 && $variant->sale_price < $variant->regular_price)
                                        <div class="d-flex flex-column">
                                            <span class="price-original">Giá gốc: {{ number_format($variant->regular_price) }}đ</span>
                                            <span class="price-sale">Giá khuyến mãi: {{ number_format($variant->sale_price) }}đ</span>
                                            <span class="badge bg-danger discount-badge">Giảm {{ number_format((($variant->regular_price - $variant->sale_price) / $variant->regular_price) * 100, 1) }}%</span>
                                        </div>
                                    @else
                                        <span class="fw-bold">{{ number_format($variant->regular_price) }}đ</span>
                                    @endif
                                @else
                                    @if($product->is_sale && $product->sale_price < $product->price)
                                        <div class="d-flex flex-column">
                                            <span class="price-original">Giá gốc: {{ number_format($product->price) }}đ</span>
                                            <span class="price-sale">Giá khuyến mãi: {{ number_format($product->sale_price) }}đ</span>
                                            <span class="badge bg-danger discount-badge">Giảm {{ number_format((($product->price - $product->sale_price) / $product->price) * 100, 1) }}%</span>
                                        </div>
                                    @else
                                        <span class="fw-bold">{{ number_format($product->price) }}đ</span>
                                    @endif
                                @endif
                            </td>
                        </tr>
                        @if($product->type === 'simple')
                        <tr>
                            <th>Kho hàng:</th>
                            <td>
                                @if($product->stock > 0)
                                    <span class="text-success">Còn {{ number_format($product->stock) }} sản phẩm</span>
                                @else
                                    <span class="text-danger">Hết hàng</span>
                                @endif
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

          
        </div>

        <!-- Sidebar -->
        <div class="col-12 col-lg-4">
            <!-- Hình ảnh -->
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3">
                    <h5 class="card-title mb-0">Hình ảnh</h5>
                </div>
                <div class="card-body">
                    @if($product->thumbnail)
                    <img src="{{ asset('storage/' . $product->thumbnail) }}"
                         class="img-fluid rounded mb-3"
                         alt="{{ $product->name }}">
                    @endif

                    @if($product->type === 'simple' && $product->images && $product->images->count())
                    <div class="row g-2">
                        @foreach($product->images as $image)
                        <div class="col-4">
                            <img src="{{ asset('storage/' . $image->url) }}"
                                 class="img-fluid rounded"
                                 alt="Gallery image">
                        </div>
                        @endforeach
                    </div>
                    @endif

                    @if(!$product->thumbnail && (!$product->images || $product->images->count() === 0))
                    <div class="text-center py-4">
                        <div class="text-muted mb-2">
                            <i class="bi bi-image fa-2x"></i>
                        </div>
                        <p class="mb-0">Chưa có hình ảnh</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Thông tin thêm -->
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3">
                    <h5 class="card-title mb-0">Thông tin thêm</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th width="150">Ngày tạo:</th>
                            <td>{{ $product->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Cập nhật cuối:</th>
                            <td>{{ $product->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Lượt xem:</th>
                            <td>{{ number_format($product->views) }}</td>
                        </tr>
                        <tr>
                            <th>Đã bán:</th>
                            <td>{{ number_format($product->sold) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Hàng thứ hai: Danh sách biến thể full width -->
    <div class="row mt-4">
        <div class="col-12">
            @if($product->type === 'variant')
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3 d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Danh sách biến thể</h5>
                    <span class="badge bg-primary">{{ $product->variants->count() }} biến thể</span>
                </div>
                <div class="card-body">
                    @if($product->variants->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="variantTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Thuộc tính</th>
                                    <th>SKU</th>
                                    <th>Giá gốc</th>
                                    <th>Giá khuyến mãi</th>
                                    <th>% giảm</th>
                                    <th>Tồn kho</th>
                                    <th>Ảnh chính</th>
                                    <th>Thư viện ảnh</th>
                                    <th>Lượt mua</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product->variants as $variant)
                                @php
                                    $purchaseCount = $variant->orderItems()->whereHas('order.currentOrderStatus', function($q) {
                                        $q->where('order_status_id', 4)->where('is_current', 1);
                                    })->sum('quantity');
                                    $hasDiscount = $variant->sale_price > 0 && $variant->sale_price < $variant->regular_price;
                                    $discountPercent = $hasDiscount ? round((($variant->regular_price - $variant->sale_price) / $variant->regular_price) * 100, 1) : 0;
                                @endphp
                                <tr>
                                    <td>
                                        @foreach($variant->attributeValues as $attributeValue)
                                            <span class="badge bg-secondary me-1">{{ $attributeValue->attribute->name }}: {{ $attributeValue->value }}</span>
                                        @endforeach
                                    </td>
                                    <td>{{ $variant->sku }}</td>
                                    <td>{{ number_format($variant->regular_price) }}đ</td>
                                    <td>{{ $variant->sale_price ? number_format($variant->sale_price) . 'đ' : '' }}</td>
                                    <td>
                                        @if($hasDiscount)
                                            <span class="badge bg-danger">-{{ $discountPercent }}%</span>
                                        @endif
                                    </td>
                                    <td>{{ $variant->stock }}</td>
                                    <td>
                                        @if($variant->img)
                                            <img src="{{ asset('storage/' . $variant->img) }}" class="img-thumbnail" style="max-width: 60px; max-height: 60px; object-fit: cover;">
                                        @else
                                            <span class="text-muted">No image</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-2">
                                            @forelse($variant->images as $image)
                                                <div class="position-relative" style="width: 60px; height: 60px;">
                                                    <img src="{{ asset('storage/' . $image->url) }}" 
                                                         class="img-thumbnail h-100 w-100" 
                                                         style="object-fit: cover; cursor: pointer;"
                                                         alt="Variant image"
                                                         onclick="showVariantImages({{ $variant->id }}, {{ json_encode($variant->images->pluck('url')->toArray()) }})"
                                                         title="Click để xem ảnh lớn">
                                                </div>
                                            @empty
                                                <span class="text-muted">Chưa có ảnh</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            <i class="fas fa-shopping-cart me-1"></i>{{ number_format($purchaseCount) }}
                                        </span>
                                    </td>

                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-info-circle text-muted" style="font-size: 2rem;"></i>
                        <p class="text-muted mt-2">Chưa có biến thể nào được tạo.</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal hiển thị ảnh biến thể -->
<div class="modal fade" id="variantImagesModal" tabindex="-1" aria-labelledby="variantImagesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="variantImagesModalLabel">Thư viện ảnh biến thể</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="variantImagesContainer" class="row g-3">
                    <!-- Ảnh sẽ được tải ở đây bằng JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Hàm xóa ảnh biến thể
function deleteVariantImage(productId, variantId, imageId) {
    if (!confirm('Bạn có chắc chắn muốn xóa ảnh này?')) {
        return false;
    }

    // Tìm phần tử ảnh tương ứng
    const imageElement = document.querySelector(`img[src*="${imageId}"]`);
    const imageContainer = imageElement ? imageElement.closest('.position-relative') : null;
    
    // Nếu không tìm thấy phần tử ảnh, thoát
    (!imageContainer) return;
    
    // Thêm lớp loading
    const deleteButton = imageContainer.querySelector('button');
    const originalContent = deleteButton.innerHTML;
    deleteButton.innerHTML = '<i class="bi bi-arrow-clockwise"></i>';
    deleteButton.disabled = true;
    
    // Gọi API xóa ảnh
    fetch(`/admin/products/${productId}/variants/${variantId}/images/${imageId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Xóa phần tử ảnh
            imageContainer.remove();
            // Hiển thị thông báo thành công
            toastr.success(data.message || 'Đã xóa ảnh thành công');
        } else {
            throw new Error(data.message || 'Có lỗi xảy ra khi xóa ảnh');
        }
    })
    .catch(error => {
        console.error('Lỗi khi xóa ảnh:', error);
        toastr.error(error.message || 'Có lỗi xảy ra khi xóa ảnh');
        // Khôi phục lại nút xóa
        if (deleteButton) {
            deleteButton.innerHTML = originalContent;
            deleteButton.disabled = false;
        }
    });
}

// Khởi tạo tooltip
$(document).ready(function() {
    $('[data-bs-toggle="tooltip"]').tooltip();
});

// Hàm hiển thị ảnh biến thể trong modal
function showVariantImages(variantId, imageUrls) {
    const container = document.getElementById('variantImagesContainer');
    container.innerHTML = '';
    
    imageUrls.forEach(function(url) {
        const col = document.createElement('div');
        col.className = 'col-md-4 col-sm-6';
        col.innerHTML = `
            <div class="text-center">
                <img src="/storage/${url}" 
                     class="img-fluid rounded shadow-sm" 
                     style="max-height: 300px; object-fit: cover;"
                     alt="Variant image">
            </div>
        `;
        container.appendChild(col);
    });
    
    // Hiển thị modal
    const modal = new bootstrap.Modal(document.getElementById('variantImagesModal'));
    modal.show();
}
</script>
@endpush

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn chuyển sản phẩm <strong>{{ $product->name }}</strong> vào thùng rác?</p>
                <p class="mb-0 text-muted">
                    <i class="bi bi-info-circle"></i>
                    Sản phẩm sẽ được chuyển vào thùng rác và có thể khôi phục lại sau.
                </p>
            </div>
            <div class="modal-footer">
                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i>
                        Chuyển vào thùng rác
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
