@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 fw-bold">Chi tiết danh mục</h1>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary shadow-sm rounded">
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
                        @if ($category->icon)
                            @php
                                $iconPath = 'storage/' . $category->icon;
                                $iconExists = file_exists(public_path($iconPath));
                            @endphp
                            @if ($iconExists)
                                <img src="{{ asset($iconPath) }}" 
                                     alt="{{ $category->name }}" 
                                     class="img-fluid rounded border" 
                                     style="max-width: 200px; height: auto; object-fit: contain;">
                            @else
                                <div class="text-muted small border p-3 rounded">
                                    <i class="bi bi-exclamation-triangle me-1"></i><br>
                                    Icon không tồn tại
                                </div>
                            @endif
                        @else
                            <div class="text-muted small border p-3 rounded">
                                <i class="bi bi-image me-1"></i><br>
                                Không có icon
                            </div>
                        @endif
                    </div>
                    <div class="col-md-8">
                        <dl class="row">
                            <dt class="col-sm-3">ID:</dt>
                            <dd class="col-sm-9">{{ $category->id }}</dd>

                            <dt class="col-sm-3">Tên danh mục:</dt>
                            <dd class="col-sm-9">{{ $category->name }}</dd>

                            <dt class="col-sm-3">Danh mục cha:</dt>
                            <dd class="col-sm-9">
                                @if($category->parent)
                                    <a href="{{ route('admin.categories.show', $category->parent->id) }}" 
                                       class="badge bg-info text-decoration-none">
                                        {{ $category->parent->name }}
                                    </a>
                                @else
                                    <span class="badge bg-secondary">Danh mục gốc</span>
                                @endif
                            </dd>

                            <dt class="col-sm-3">Trạng thái:</dt>
                            <dd class="col-sm-9">
                                <span class="badge rounded-pill {{ $category->is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} px-3 py-2">
                                    <i class="bi bi-circle-fill me-1 small"></i>
                                    {{ $category->is_active ? 'Đang hoạt động' : 'Không hoạt động' }}
                                </span>
                            </dd>

                            <dt class="col-sm-3">Ngày tạo:</dt>
                            <dd class="col-sm-9">{{ $category->created_at ? $category->created_at->format('d/m/Y H:i') : 'N/A' }}</dd>

                            <dt class="col-sm-3">Ngày cập nhật:</dt>
                            <dd class="col-sm-9">{{ $category->updated_at ? $category->updated_at->format('d/m/Y H:i') : 'N/A' }}</dd>

                            @if ($category->deleted_at)
                                <dt class="col-sm-3 text-danger">Ngày xóa:</dt>
                                <dd class="col-sm-9 text-danger">{{ $category->deleted_at->format('d/m/Y H:i') }}</dd>
                            @endif
                        </dl>

                        <div class="mt-4">
                            <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-warning shadow-sm rounded me-2">
                                <i class="bi bi-pencil-square me-1"></i> Chỉnh sửa
                            </a>
                            {{-- Nút xóa mềm hoặc xóa vĩnh viễn tùy trạng thái --}}
                            @if ($category->deleted_at)
                                <button type="button" 
                                        class="btn btn-success shadow-sm rounded me-2" 
                                        onclick="confirmRestore('{{ $category->id }}', '{{ $category->name }}')">
                                     <i class="bi bi-arrow-counterclockwise me-1"></i> Khôi phục
                                </button>
                                <button type="button" 
                                        class="btn btn-danger shadow-sm rounded" 
                                        onclick="confirmForceDelete('{{ $category->id }}', '{{ $category->name }}')">
                                     <i class="bi bi-x-octagon me-1"></i> Xóa vĩnh viễn
                                </button>
                            @else
                                <button type="button" class="btn btn-danger shadow-sm rounded" 
                                    onclick="confirmDelete('{{ $category->id }}', '{{ $category->name }}')">
                                    <i class="bi bi-trash me-1"></i> Xóa mềm
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
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
                    Bạn có chắc chắn muốn khôi phục danh mục "<span id="restoreCategoryName"></span>"?
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
                    Bạn có chắc chắn muốn xóa mềm danh mục "<span id="categoryName"></span>"?
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
                    Bạn có chắc chắn muốn xóa vĩnh viễn danh mục "<span id="forceDeleteCategoryName"></span>"?
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
            document.getElementById('restoreCategoryName').textContent = name;
            document.getElementById('restoreForm').action = `/admin/categories/${id}/restore`;
            new bootstrap.Modal(document.getElementById('restoreModal')).show();
        }

        function confirmDelete(id, name) {
            document.getElementById('categoryName').textContent = name;
            document.getElementById('deleteForm').action = `/admin/categories/${id}`;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        function confirmForceDelete(id, name) {
            document.getElementById('forceDeleteCategoryName').textContent = name;
            document.getElementById('forceDeleteForm').action = `/admin/categories/force-delete/${id}`;
            new bootstrap.Modal(document.getElementById('forceDeleteModal')).show();
        }
    </script>
    @endpush
@endsection 