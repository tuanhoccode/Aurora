@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-between align-items-center mb-4">
            <div class="col-md-auto">
                <h1 class="h3 fw-bold mb-0">Danh sách ảnh sản phẩm</h1>
            </div>
            <div class="col-md-auto">
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.product-images.create') }}" class="btn btn-primary shadow-sm rounded">
                        <i class="bi bi-plus-circle me-1"></i> Thêm ảnh mới
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success shadow-sm rounded mb-3">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger shadow-sm rounded mb-3">{{ session('error') }}</div>
        @endif

        <div class="card shadow-sm rounded">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0">#</th>
                                <th class="border-0">Ảnh</th>
                                <th class="border-0">Sản phẩm</th>
                                <th class="border-0">Biến thể</th>
                                <th class="border-0 text-end">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($images as $image)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <img src="{{ asset('storage/' . $image->url) }}" 
                                             width="100" 
                                             alt="{{ $image->product->name ?? 'Ảnh sản phẩm' }}" 
                                             class="img-thumbnail">
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.products.show', $image->product_id) }}" 
                                           class="text-decoration-none">
                                            {{ $image->product->name ?? '(Không rõ sản phẩm)' }}
                                        </a>
                                    </td>
                                    <td>
                                        {{ $image->variant->sku ?? '(Không có biến thể)' }}
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('admin.product-images.edit', $image->id) }}" 
                                                class="btn btn-warning btn-sm rounded-pill px-3" 
                                                data-bs-toggle="tooltip" 
                                                title="Chỉnh sửa">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-danger btn-sm rounded-pill px-3" 
                                                    onclick="confirmDelete('{{ $image->id }}', '{{ $image->product->name ?? 'Ảnh sản phẩm' }}')"
                                                    data-bs-toggle="tooltip" 
                                                    title="Xóa">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="bi bi-image me-2"></i>
                                            Chưa có ảnh sản phẩm nào
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Xác nhận xóa -->
        <div class="modal fade" id="deleteModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Xác nhận xóa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        Bạn có chắc chắn muốn xóa ảnh của sản phẩm "<span id="imageProductName"></span>"?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <form id="deleteForm" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Xóa</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function confirmDelete(id, productName) {
            document.getElementById('imageProductName').textContent = productName;
            document.getElementById('deleteForm').action = `/admin/product-images/${id}`;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }
    </script>
    @endpush
@endsection
