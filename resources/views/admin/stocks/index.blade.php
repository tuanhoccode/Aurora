@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        {{-- Header Section --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 fw-bold text-gray-800">Danh sách tồn kho</h1>
                <p class="text-muted mt-1">Quản lý thông tin tồn kho sản phẩm</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.stocks.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4">
                    <i class="bi bi-plus-circle me-1"></i> Thêm mới
                </a>
            </div>
        </div>

        {{-- Alert Messages --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm rounded" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Main Card --}}
        <div class="card shadow-sm rounded-3 border-0">
            <div class="card-body p-4">
                @if ($stocks->count())
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0" style="width: 60px">ID</th>
                                    <th class="border-0">Sản phẩm</th>
                                    <th class="border-0" style="width: 120px">Số lượng</th>
                                    <th class="border-0" style="width: 180px">Ngày tạo</th>
                                    <th class="border-0" style="width: 180px">Ngày cập nhật</th>
                                    <th class="border-0 text-end" style="width: 250px">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($stocks as $stock)
                                    <tr>
                                        <td class="text-muted">{{ $stock->id }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="fw-medium">{{ $stock->product->name ?? 'Không có tên' }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill bg-info-subtle text-info px-3 py-2">
                                                <i class="bi bi-box me-1 small"></i>
                                                {{ $stock->stock }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="text-muted" data-bs-toggle="tooltip" title="{{ $stock->created_at->format('H:i:s d/m/Y') }}">
                                                {{ $stock->created_at->format('d/m/Y') }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="text-muted" data-bs-toggle="tooltip" title="{{ $stock->updated_at->format('H:i:s d/m/Y') }}">
                                                {{ $stock->updated_at->format('d/m/Y') }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="{{ route('admin.stocks.show', $stock->id) }}"
                                                    class="btn btn-info btn-sm rounded-pill px-3" 
                                                    data-bs-toggle="tooltip" 
                                                    title="Xem chi tiết">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.stocks.edit', $stock->id) }}"
                                                    class="btn btn-warning btn-sm rounded-pill px-3" 
                                                    data-bs-toggle="tooltip" 
                                                    title="Chỉnh sửa">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-danger btn-sm rounded-pill px-3" 
                                                        onclick="confirmDelete('{{ $stock->id }}', '{{ $stock->product->name ?? 'Không có tên' }}')"
                                                        data-bs-toggle="tooltip" 
                                                        title="Xóa">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if ($stocks->hasPages())
                        <div class="d-flex justify-content-end mt-3">
                            {{ $stocks->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-box display-1 text-muted"></i>
                        <p class="text-muted mt-3">Không tìm thấy tồn kho nào.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Xác nhận xóa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <i class="bi bi-exclamation-triangle text-danger display-4"></i>
                    </div>
                    <p class="text-center mb-0">
                        Bạn có chắc chắn muốn xóa tồn kho "<span id="deleteStockName" class="fw-bold"></span>"?
                    </p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger rounded-pill px-4">Xóa</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Xác nhận xóa một tồn kho
        window.confirmDelete = function(id, name) {
            document.getElementById('deleteStockName').textContent = name;
            const form = document.getElementById('deleteForm');
            form.action = `{{ url('admin/stocks') }}/${id}`;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        };
    });
</script>
@endpush
