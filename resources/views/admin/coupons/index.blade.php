@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid py-4">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 fw-bold text-gray-800">Danh sách mã giảm giá</h1>
                <p class="text-muted mt-1">Quản lý mã giảm giá trong hệ thống</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                    <i class="bi bi-plus-circle me-1"></i> Thêm mới
                </a>
                <a href="{{ route('admin.coupons.trash') }}" class="btn btn-outline-danger rounded-pill px-4 shadow-sm">
                    <i class="bi bi-trash3 me-1"></i> Thùng rác
                </a>
            </div>
        </div>

        {{-- Alert --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm rounded" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Table --}}
        <div class="card shadow-sm rounded-3 border-0">
            <div class="card-body p-4">
                @if ($coupons->count())
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Mã</th>
                                    <th>Giảm</th>
                                    <th>Hạn sử dụng</th>
                                    <th>Trạng thái</th>
                                    <th class="text-end">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($coupons as $coupon)
                                    <tr>
                                        <td>{{ $coupon->id }}</td>
                                        <td>{{ $coupon->code }}</td>
                                        <td>{{ $coupon->discount_type === 'percent' ? $coupon->discount_value . '%' : number_format($coupon->discount_value) . 'đ' }}</td>
                                        <td>{{ $coupon->end_date ? $coupon->end_date->format('d/m/Y') : 'Không có' }}</td>
                                        <td>
                                            @if($coupon->is_active)
                                                <span class="badge bg-success">Kích hoạt</span>
                                            @else
                                                <span class="badge bg-secondary">Tắt</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="btn btn-warning btn-sm rounded-pill px-3">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Bạn có chắc muốn xóa mã này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger btn-sm rounded-pill px-3">
                                                    <i class="bi bi-trash"></i>
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
                        <i class="bi bi-ticket display-1 text-muted"></i>
                        <p class="text-muted mt-3">Không tìm thấy mã giảm giá nào.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
