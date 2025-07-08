@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 fw-bold text-gray-800">Thùng rác</h1>
                <p class="text-muted mt-1">Các mã giảm giá đã bị xóa mềm</p>
            </div>
            <div>
                <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
                    <i class="bi bi-arrow-left-circle"></i> Quay lại danh sách
                </a>
            </div>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm rounded" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded" role="alert">
                <i class="bi bi-x-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Table --}}
        <div class="card shadow-sm rounded-3 border-0">
            <div class="card-body p-4">
                @if ($coupons->count())
                    {{-- SỬA TẠI ĐÂY --}}
                    <div class="d-flex justify-content-between mb-3">
                        <div class="d-flex gap-2">
                            {{-- Form XÓA --}}
                            <form id="bulk-delete-form" action="{{ route('admin.coupons.bulk-force-delete') }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa vĩnh viễn các mã đã chọn?')">
                                @csrf
                                <input type="hidden" name="ids_json" id="delete_ids">
                                <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3">
                                    <i class="bi bi-trash3"></i> Xóa vĩnh viễn
                                </button>
                            </form>

                            {{-- Form KHÔI PHỤC --}}
                            <form id="bulk-restore-form" action="{{ route('admin.coupons.bulk-restore') }}" method="POST" onsubmit="return confirm('Khôi phục các mã đã chọn?')">
                                @csrf
                                <input type="hidden" name="ids_json" id="restore_ids">
                                <button type="submit" class="btn btn-success btn-sm rounded-pill px-3">
                                    <i class="bi bi-arrow-clockwise"></i> Khôi phục
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th><input type="checkbox" id="select-all"></th>
                                    <th>ID</th>
                                    <th>Mã</th>
                                    <th>Giảm</th>
                                    <th>Hết hạn</th>
                                    <th class="text-end">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($coupons as $coupon)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="row-checkbox" value="{{ $coupon->id }}">
                                        </td>
                                        <td>{{ $coupon->id }}</td>
                                        <td>{{ $coupon->code }}</td>
                                        <td>{{ $coupon->discount_type === 'percent' ? $coupon->discount_value . '%' : number_format($coupon->discount_value) . 'đ' }}</td>
                                        <td>{{ $coupon->end_date ? $coupon->end_date->format('d/m/Y') : 'Không có' }}</td>
                                        <td class="text-end">
                                            <form action="{{ route('admin.coupons.restore', $coupon->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <button class="btn btn-success btn-sm rounded-pill px-3" onclick="return confirm('Khôi phục mã này?')">
                                                    <i class="bi bi-arrow-clockwise"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.coupons.force-delete', $coupon->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa vĩnh viễn mã này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger btn-sm rounded-pill px-3">
                                                    <i class="bi bi-trash3-fill"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if ($coupons->hasPages())
                        <div class="d-flex justify-content-end mt-3">
                            {{ $coupons->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-trash display-1 text-muted"></i>
                        <p class="text-muted mt-3">Không có mã nào trong thùng rác.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.row-checkbox');

        // Chọn tất cả
        selectAll.addEventListener('change', function () {
            checkboxes.forEach(cb => cb.checked = this.checked);
        });

        function getSelectedIds() {
            return Array.from(checkboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value);
        }

        const deleteForm = document.getElementById('bulk-delete-form');
        const restoreForm = document.getElementById('bulk-restore-form');

        deleteForm.addEventListener('submit', function (e) {
            const ids = getSelectedIds();
            if (ids.length === 0) {
                alert('Vui lòng chọn ít nhất một mã để xóa.');
                e.preventDefault();
            } else {
                document.getElementById('delete_ids').value = JSON.stringify(ids);
            }
        });

        restoreForm.addEventListener('submit', function (e) {
            const ids = getSelectedIds();
            if (ids.length === 0) {
                alert('Vui lòng chọn ít nhất một mã để khôi phục.');
                e.preventDefault();
            } else {
                document.getElementById('restore_ids').value = JSON.stringify(ids);
            }
        });
    });
</script>
@endpush
