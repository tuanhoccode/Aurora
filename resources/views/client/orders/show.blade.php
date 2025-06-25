@extends('client.layouts.default')

@section('title', 'Chi tiết đơn hàng')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-6 bg-purple  p-5 rounded">
            <div class="text-center mb-4">
                <i class="bi bi-gift" style="font-size: 3rem;"></i>
                <h2>Đơn hàng đã được xác nhận</h2>
                <p>Chúng tôi sẽ gửi email xác nhận vận chuyển ngay khi đơn hàng được gửi đi.</p>
            </div>

            {{-- Thông tin đơn hàng --}}
            <div class="mb-4">
                <h4 class="mb-3">Thông tin đơn hàng</h4>
                <div class="row mb-2">
                    <div class="col-5 fw-semibold">Ngày đặt hàng:</div>
                    <div class="col-7">23/06/2025</div>
                </div>
                <div class="row mb-2">
                    <div class="col-5 fw-semibold">Ngày giao dự kiến:</div>
                    <div class="col-7">25/06/2025</div>
                </div>
                <div class="row mb-2">
                    <div class="col-5 fw-semibold">Mã đơn hàng:</div>
                    <div class="col-7">#ORD-12345678</div>
                </div>
                <div class="row mb-2">
                    <div class="col-5 fw-semibold">Phương thức thanh toán:</div>
                    <div class="col-7">Thanh toán khi giao hàng</div>
                </div>
            </div>

            {{-- Thông tin nhận hàng --}}
            <div>
                <h4 class="mb-3">Thông tin nhận hàng</h4>
                <div class="row mb-2">
                    <div class="col-5 fw-semibold">Họ và tên:</div>
                    <div class="col-7">Nguyễn Văn A</div>
                </div>
                <div class="row mb-2">
                    <div class="col-5 fw-semibold">Số điện thoại:</div>
                    <div class="col-7">0987654321</div>
                </div>
                <div class="row mb-2">
                    <div class="col-5 fw-semibold">Địa chỉ:</div>
                    <div class="col-7">123 Đường Số 1, Phường 1, Quận 1, TP.HCM</div>
                </div>
                <div class="row mb-2">
                    <div class="col-5 fw-semibold">Ghi chú:</div>
                    <div class="col-7">Giao hàng vào buổi chiều</div>
                </div>
            </div>
        </div>

        <div class="col-md-6 p-5">
            <h4>Chi tiết đơn hàng</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th class="text-end">Tổng</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Áo thun nam (Màu: Đen, Size: M) x 2</td>
                        <td class="text-end">1,000,000đ</td>
                    </tr>
                    <tr>
                        <td>Quần jean nam (Màu: Xanh, Size: 30) x 1</td>
                        <td class="text-end">750,000đ</td>
                    </tr>
                    <tr>
                        <td><strong>Tạm tính</strong></td>
                        <td class="text-end">1,750,000đ</td>
                    </tr>
                    <tr>
                        <td><strong>Phí vận chuyển</strong></td>
                        <td class="text-end">Miễn phí</td>
                    </tr>
                    <tr>
                        <td><strong>Tổng cộng</strong></td>
                        <td class="text-end text-primary fw-bold">1,750,000đ</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
