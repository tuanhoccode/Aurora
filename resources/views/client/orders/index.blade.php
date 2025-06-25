@extends('client.layouts.default')

@section('title', 'Đơn hàng của tôi')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h3 class="mb-4">Danh sách đơn hàng</h3>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Mã đơn hàng</th>
                            <th>Ngày đặt</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#ORD-12345678</td>
                            <td>23/06/2025 11:34</td>
                            <td>1,750,000đ</td>
                            <td>
                                <span class="badge bg-success">Đã hoàn thành</span>
                            </td>
                            <td>
                                <a href="{{ route('orders.show') }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> Xem chi tiết
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>#ORD-87654321</td>
                            <td>22/06/2025 14:50</td>
                            <td>2,150,000đ</td>
                            <td>
                                <span class="badge bg-warning">Đang xử lý</span>
                            </td>
                            <td>
                                <a href="{{ route('orders.show') }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> Xem chi tiết
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection