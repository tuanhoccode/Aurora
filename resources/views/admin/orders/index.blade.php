@extends('admin.layouts.app')

@section('content')
    <div class="container">
        <h1>Danh sách Đơn hàng</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Mã Đơn hàng</th>
                    <th>Khách hàng</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->code }}</td>
                        <td>{{ $order->fullname }} ({{ $order->phone_number }})</td>
                        <td>{{ number_format($order->total_amount) }} VNĐ</td>
                        <td>{{ $order->is_paid ? 'Đã thanh toán' : 'Chưa thanh toán' }}</td>
                        <td>
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-info btn-sm">Xem</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $orders->links() }}
    </div>
@endsection