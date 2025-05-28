    @extends('admin.layouts.app')

    @section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 fw-bold">Thùng rác thương hiệu</h1>
            <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary shadow-sm rounded">
                <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success shadow-sm rounded">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger shadow-sm rounded">{{ session('error') }}</div>
        @endif

        <div class="card shadow-sm rounded">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <form action="{{ route('admin.brands.trash') }}" method="GET" class="d-flex gap-2">
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Tìm kiếm thương hiệu đã xóa..." 
                                   value="{{ request('search') }}">
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">Tất cả trạng thái</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Đang hoạt động</option>
                                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Không hoạt động</option>
                            </select>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i>
                            </button>
                        </form>
                    </div>
                </div>

                @if ($brands->count())
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 60px">ID</th>
                                    <th>Tên thương hiệu</th>
                                    <th style="width: 120px">Logo</th>
                                    <th style="width: 120px">Trạng thái</th>
                                    <th style="width: 150px">Ngày xóa</th>
                                    <th style="width: 200px">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($brands as $brand)
                                    <tr>
                                        <td>{{ $brand->id }}</td>
                                        <td>{{ $brand->name }}</td>
                                        <td class="text-center">
                                            @if ($brand->logo)
                                                @php
                                                    $logoPath = 'storage/' . $brand->logo;
                                                    $logoExists = file_exists(public_path($logoPath));
                                                @endphp
                                                @if ($logoExists)
                                                    <img src="{{ asset($logoPath) }}" 
                                                        alt="{{ $brand->name }}" 
                                                        class="img-thumbnail" 
                                                        style="max-width:80px; max-height:80px; object-fit:contain;">
                                                @else
                                                    <div class="text-muted small">
                                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                                        Logo không tồn tại
                                                    </div>
                                                @endif
                                            @else
                                                <div class="text-muted small">
                                                    <i class="bi bi-image me-1"></i>
                                                    Không có logo
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $brand->is_active ? 'bg-success' : 'bg-danger' }}">
                                                {{ $brand->is_active ? 'Đang hoạt động' : 'Không hoạt động' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            {{ $brand->deleted_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" 
                                                        class="btn btn-success btn-sm" 
                                                        onclick="confirmRestore('{{ $brand->id }}', '{{ $brand->name }}')"
                                                        title="Khôi phục">
                                                    <i class="bi bi-arrow-counterclockwise"></i>
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-danger btn-sm" 
                                                        onclick="confirmForceDelete('{{ $brand->id }}', '{{ $brand->name }}')"
                                                        title="Xóa vĩnh viễn">
                                                    <i class="bi bi-x-octagon"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        {{ $brands->links() }}
                    </div>
                @else
                    <div class="alert alert-info mb-0">
                        Không có thương hiệu nào trong thùng rác.
                    </div>
                @endif
            </div>
        </div>
    </div>

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

        function confirmForceDelete(id, name) {
            document.getElementById('forceDeleteBrandName').textContent = name;
            document.getElementById('forceDeleteForm').action = `/admin/brands/force-delete/${id}`;
            new bootstrap.Modal(document.getElementById('forceDeleteModal')).show();
        }
    </script>
    @endpush
    @endsection
