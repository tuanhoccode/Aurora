
@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        {{-- Header Section --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 fw-bold text-gray-800">Thùng rác: Giá trị thuộc tính - {{ $attribute->name }}</h1>
                <p class="text-muted mt-1">
                    Quản lý các giá trị thuộc tính đã xóa mềm {{ $attribute->is_variant ? '(Biến thể)' : '' }}
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.attributes.index') }}" class="btn btn-secondary shadow-sm rounded-pill px-4">
                    <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách thuộc tính
                </a>
                <a href="{{ route('admin.attribute_values.index', $attribute->id) }}" class="btn btn-primary shadow-sm rounded-pill px-4">
                    <i class="bi bi-list me-1"></i> Danh sách giá trị
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
                {{-- Search Form --}}
                <div class="row mb-4">
                    <div class="col-md-6">
                        <form action="{{ route('admin.attribute_values.trashed', $attribute->id) }}" method="GET" class="d-flex gap-2">
                            <div class="input-group">
                                <input type="text" 
                                       name="search" 
                                       class="form-control"
                                       placeholder="Tìm kiếm giá trị..." 
                                       value="{{ request('search') }}">
                            </div>
                            <button type="submit" class="btn btn-primary rounded-pill px-4">
                                <i class="bi bi-search"></i>
                            </button>
                        </form>
                    </div>
                </div>

                @if ($values->count())
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0">
                                        <a href="{{ route('admin.attribute_values.trashed', array_merge(['attributeId' => $attribute->id], request()->query(), ['sort_by' => 'value', 'sort_dir' => ($sortBy == 'value' && $sortDir == 'asc') ? 'desc' : 'asc'])) }}"
                                           class="text-decoration-none text-dark d-flex align-items-center">
                                            Giá trị
                                            @if ($sortBy == 'value')
                                                <i class="bi bi-arrow-{{ $sortDir == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="border-0" style="width: 120px">
                                        <a href="{{ route('admin.attribute_values.trashed', array_merge(['attributeId' => $attribute->id], request()->query(), ['sort_by' => 'deleted_at', 'sort_dir' => ($sortBy == 'deleted_at' && $sortDir == 'asc') ? 'desc' : 'asc'])) }}"
                                           class="text-decoration-none text-dark d-flex align-items-center">
                                            Ngày xóa
                                            @if ($sortBy == 'deleted_at')
                                                <i class="bi bi-arrow-{{ $sortDir == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="border-0 text-end" style="width: 150px">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($values as $value)
                                    <tr class="position-relative">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="fw-medium">{{ $value->value }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="text-muted" data-bs-toggle="tooltip" title="{{ $value->deleted_at->format('H:i:s d/m/Y') }}">
                                                {{ $value->deleted_at->format('d/m/Y') }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-end gap-2">
                                                <button type="button" 
                                                        class="btn btn-success btn-sm rounded-pill px-3" 
                                                        onclick="confirmRestore('{{ $value->id }}', '{{ $value->value }}')"
                                                        data-bs-toggle="tooltip" 
                                                        title="Khôi phục">
                                                    <i class="bi bi-arrow-counterclockwise"></i>
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-danger btn-sm rounded-pill px-3" 
                                                        onclick="confirmForceDelete('{{ $value->id }}', '{{ $value->value }}')"
                                                        data-bs-toggle="tooltip" 
                                                        title="Xóa vĩnh viễn">
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
                    @if ($values->hasPages())
                        <div class="d-flex justify-content-end mt-3">
                            {{ $values->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-trash display-1 text-muted"></i>
                        <p class="text-muted mt-3">Không có giá trị nào trong thùng rác.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Restore Confirmation Modal --}}
    <div class="modal fade" id="restoreModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Xác nhận khôi phục</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <i class="bi bi-arrow-counterclockwise text-success display-4"></i>
                    </div>
                    <p class="text-center mb-0">
                        Bạn có chắc chắn muốn khôi phục giá trị "<span id="restoreValueName" class="fw-bold"></span>"?
                    </p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                    <form id="restoreForm" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-success rounded-pill px-4">Khôi phục</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Force Delete Confirmation Modal --}}
    <div class="modal fade" id="forceDeleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Xác nhận xóa vĩnh viễn</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <i class="bi bi-exclamation-triangle text-danger display-4"></i>
                    </div>
                    <p class="text-center mb-0">
                        Bạn có chắc chắn muốn xóa vĩnh viễn giá trị "<span id="forceDeleteValueName" class="fw-bold"></span>"? Hành động này không thể hoàn tác.
                    </p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                    <form id="forceDeleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger rounded-pill px-4">Xóa vĩnh viễn</button>
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
    });

    // Xác nhận khôi phục
    function confirmRestore(id, value) {
        if (confirm(`Bạn có chắc chắn muốn khôi phục giá trị "${value}"?`)) {
            const form = document.getElementById('restoreForm');
            form.action = `{{ url('admin/attributes') }}/{{ $attribute->id }}/values/${id}/restore`;
            form.submit();
        }
    }

    // Xác nhận xóa vĩnh viễn
    function confirmForceDelete(id, value) {
        if (confirm(`Bạn có chắc chắn muốn xóa vĩnh viễn giá trị "${value}"? Hành động này không thể hoàn tác.`)) {
            const form = document.getElementById('forceDeleteForm');
            form.action = `{{ url('admin/attributes') }}/{{ $attribute->id }}/values/${id}/force-delete`;
            form.submit();
        }
    }
</script>
@endpush