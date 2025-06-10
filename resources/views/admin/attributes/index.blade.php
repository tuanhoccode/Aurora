@extends('admin.layouts.app')

@section('title', 'Quản lý thuộc tính')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="card bg-light-subtle border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-2">Quản lý thuộc tính</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item active">Thuộc tính</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('admin.attributes.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Thêm thuộc tính
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Attributes List -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Tên thuộc tính</th>
                            <th scope="col">Loại</th>
                            <th scope="col">Số giá trị</th>
                            <th scope="col">Trạng thái</th>
                            <th scope="col">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attributes as $attribute)
                            <tr>
                                <td>{{ $attribute->id }}</td>
                                <td>{{ $attribute->name }}</td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ $attribute->type === 'select' ? 'Lựa chọn' : 'Văn bản' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.attributes.values', $attribute) }}" 
                                       class="text-decoration-none">
                                        {{ $attribute->values_count }} giá trị
                                    </a>
                                </td>
                                <td>
                                    @if($attribute->is_active)
                                        <span class="badge bg-success">Đang hoạt động</span>
                                    @else
                                        <span class="badge bg-danger">Đã vô hiệu</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.attributes.values', $attribute) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-list"></i>
                                        </a>
                                        <a href="{{ route('admin.attributes.edit', $attribute) }}" 
                                           class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="confirmDelete({{ $attribute->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="text-muted">Chưa có thuộc tính nào</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {{ $attributes->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa thuộc tính này?</p>
                <p class="text-muted mb-0">Hành động này không thể hoàn tác.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmDelete(id) {
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        const form = document.getElementById('deleteForm');
        form.action = `/admin/attributes/${id}`;
        modal.show();
    }
</script>
@endpush

@endsection 