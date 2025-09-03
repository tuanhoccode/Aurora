@extends('admin.layouts.app')

@section('title', 'Quản lý hoàn Tiền')
@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Danh Sách Yêu Cầu Hoàn Tiền</h1>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Mã Đơn Hàng</th>
                            <th>Người Dùng</th>
                            <th>Tổng Tiền</th>
                            <th>Trạng Thái</th>
                            <th>Ngày Tạo</th>
                            <th>Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($refunds as $refund)
                            <tr>
                                <td>{{ $refund->order->code ?? 'N/A' }}</td>
                                <td>{{ $refund->user->fullname ?? 'N/A' }}</td>
                                <td>{{ number_format($refund->total_amount, 2) }}</td>
                                <td>
                                    <span class="badge {{ $refund->status == 'pending' ? 'badge-warning' : ($refund->status == 'approved' ? 'badge-success' : 'badge-danger') }}">
                                        {{ $refund->status }}
                                    </span>
                                </td>
                                <td>{{ $refund->created_at }}</td>
                                <td>
                                    <a href="{{ route('admin.refunds.show', $refund->id) }}" class="btn btn-primary btn-sm">Xem</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $refunds->links() }}
            </div>
        </div>
    </div>
</div>
@endsection