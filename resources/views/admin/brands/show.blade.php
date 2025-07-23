@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 fw-bold">Chi tiết thương hiệu</h1>
            <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary shadow-sm rounded">
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
                        @if ($brand->logo)
                            @php
                                $logoPath = 'storage/' . $brand->logo;
                                $logoExists = file_exists(public_path($logoPath));
                            @endphp
                            @if ($logoExists)
                                <img src="{{ asset($logoPath) }}" 
                                     alt="{{ $brand->name }}" 
                                     class="img-fluid rounded border" 
                                     style="max-width: 200px; height: auto; object-fit: contain;">
                            @else
                                <div class="text-muted small border p-3 rounded">
                                    <i class="bi bi-exclamation-triangle me-1"></i><br>
                                    Logo không tồn tại
                                </div>
                            @endif
                        @else
                            <div class="text-muted small border p-3 rounded">
                                <i class="bi bi-image me-1"></i><br>
                                Không có logo
                            </div>
                        @endif
                    </div>
                    <div class="col-md-8">
                        <dl class="row">
                            <dt class="col-sm-3">ID:</dt>
                            <dd class="col-sm-9">{{ $brand->id }}</dd>

                            <dt class="col-sm-3">Tên thương hiệu:</dt>
                            <dd class="col-sm-9">{{ $brand->name }}</dd>

                            <dt class="col-sm-3">Slug:</dt>
                            <dd class="col-sm-9">{{ $brand->slug }}</dd>
                            @if ($brand->description)
                                <dt class="col-sm-3">Mô tả:</dt>
                                <dd class="col-sm-9">{{ $brand->description }}</dd>
                            @endif

                            <dt class="col-sm-3">Số sản phẩm:</dt>
                            <dd class="col-sm-9">
                                <span class="fw-bold">{{ $brand->products()->count() }}</span> sản phẩm
                                @if($brand->products()->count() > 0)
                                    <a href="{{ route('admin.products.index', ['brand' => $brand->id]) }}" class="btn btn-link btn-sm ms-2 px-1 py-0" style="font-size:13px;" title="Xem sản phẩm liên kết">
                                        <i class="bi bi-box-seam"></i> Xem sản phẩm
                                    </a>
                                @endif
                            </dd>

                            <dt class="col-sm-3">Trạng thái:</dt>
                            <dd class="col-sm-9">
                                <span class="badge {{ $brand->is_active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $brand->is_active ? 'Đang hoạt động' : 'Không hoạt động' }}
                                </span>
                            </dd>

                            <dt class="col-sm-3">Ngày tạo:</dt>
                            <dd class="col-sm-9">{{ $brand->created_at ? $brand->created_at->format('d/m/Y H:i') : 'N/A' }}</dd>

                            <dt class="col-sm-3">Ngày cập nhật:</dt>
                            <dd class="col-sm-9">{{ $brand->updated_at ? $brand->updated_at->format('d/m/Y H:i') : 'N/A' }}</dd>

                            @if ($brand->deleted_at)
                                <dt class="col-sm-3 text-danger">Ngày xóa:</dt>
                                <dd class="col-sm-9 text-danger">{{ $brand->deleted_at->format('d/m/Y H:i') }}</dd>
                            @endif
                        </dl>

                        {{-- Hiển thị bảng sản phẩm liên kết --}}
                        @if($brand->products()->count() > 0)
                            <h5 class="mt-4">Sản phẩm liên kết</h5>
                            <table class="table table-sm align-middle mb-2">
                                <thead>
                                    <tr>
                                        <th>Tên sản phẩm</th>
                                        <th>Trạng thái</th>
                                        <th>Giá</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($brand->products()->latest()->limit(5)->get() as $product)
                                        <tr>
                                            <td>{{ $product->name }}</td>
                                            <td>
                                                <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $product->is_active ? 'Đang kinh doanh' : 'Ngừng kinh doanh' }}
                                                </span>
                                            </td>
                                            <td>{{ number_format($product->price) }}đ</td>
                                            <td>
                                                <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-sm btn-outline-primary">Chi tiết</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if($brand->products()->count() > 5)
                                <a href="{{ route('admin.products.index', ['brand' => $brand->id]) }}" class="btn btn-link btn-sm px-0"><i class="bi bi-box-seam"></i> Xem tất cả sản phẩm</a>
                            @endif
                        @endif

                        <div class="mt-4">
                            <a href="{{ route('admin.brands.edit', $brand->id) }}" class="btn btn-warning shadow-sm rounded me-2">
                                <i class="bi bi-pencil-square me-1"></i> Chỉnh sửa
                            </a>
                            {{-- Nút xóa mềm hoặc xóa vĩnh viễn tùy trạng thái --}}
                            @if ($brand->deleted_at)
                                <button type="button" 
                                        class="btn btn-success shadow-sm rounded me-2" 
                                        onclick="confirmRestore('{{ $brand->id }}', '{{ $brand->name }}')">
                                     <i class="bi bi-arrow-counterclockwise me-1"></i> Khôi phục
                                </button>
                                <button type="button" 
                                        class="btn btn-danger shadow-sm rounded" 
                                        onclick="confirmForceDelete('{{ $brand->id }}', '{{ $brand->name }}')">
                                     <i class="bi bi-x-octagon me-1"></i> Xóa vĩnh viễn
                                </button>
                            @else
                                <button type="button" class="btn btn-danger shadow-sm rounded" 
                                    onclick="confirmDelete('{{ $brand->id }}', '{{ $brand->name }}')">
                                    <i class="bi bi-trash me-1"></i> Xóa mềm
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Inject the modals from index/trash or define them here if only used on show --}}
    {{-- For simplicity, let's assume modals for restore/force delete are needed here --}}
    
    <!-- Modal Xác nhận khôi phục -->
    <div class="modal fade" id="restoreModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Xác nhận khôi phục</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Bạn có chắc chắn muốn khôi phục thương hiệu "<span id="restoreBrandName"></span>"?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <form id="restoreForm" method="POST" style="display: inline;">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-success">Khôi phục</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Xác nhận xóa mềm -->
     <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Xác nhận xóa mềm</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Bạn có chắc chắn muốn xóa mềm thương hiệu "<span id="brandName"></span>"?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Xóa mềm</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Xác nhận xóa vĩnh viễn -->
    <div class="modal fade" id="forceDeleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Xác nhận xóa vĩnh viễn</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        Cảnh báo: Hành động này cần cân nhắc khi thao tác!
                    </div>
                    Bạn có chắc chắn muốn xóa vĩnh viễn thương hiệu "<span id="forceDeleteBrandName"></span>"?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <form id="forceDeleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Xóa vĩnh viễn</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function confirmRestore(id, name) {
            document.getElementById('restoreBrandName').textContent = name;
            document.getElementById('restoreForm').action = `/admin/brands/${id}/restore`;
            new bootstrap.Modal(document.getElementById('restoreModal')).show();
        }
         function confirmDelete(id, name) {
            document.getElementById('brandName').textContent = name;
            document.getElementById('deleteForm').action = `/admin/brands/${id}`;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        function confirmForceDelete(id, name) {
            document.getElementById('forceDeleteBrandName').textContent = name;
            document.getElementById('forceDeleteForm').action = `/admin/brands/force-delete/${id}`;
            new bootstrap.Modal(document.getElementById('forceDeleteModal')).show();
        }
    </script>
    @endpush
@endsection
