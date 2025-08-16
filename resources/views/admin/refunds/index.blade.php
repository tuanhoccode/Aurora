<!-- resources/views/admin/refunds/index.blade.php -->
@extends('admin.layouts.app')

@section('title', 'Quản lý hoàn tiền')

@section('styles')
    <style>
        .card { border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 20px; }
        .card-header { background: #f8f9fa; padding: 15px; font-weight: bold; }
        .card-body { padding: 15px; }
        .summary-box { background: #fff; border: 1px solid #e0e0e0; padding: 20px; border-radius: 8px; }
        .summary-box h4 { margin-bottom: 20px; }
        .summary-item { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .summary-item strong { font-weight: 600; }
        .form-group { margin-bottom: 15px; }
        .form-control { width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ced4da; }
        .btn-primary { background: #007bff; color: white; padding: 8px 16px; border-radius: 4px; }
        .btn-primary:hover { background: #0056b3; }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4">Quản lý hoàn tiền</h1>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Danh sách yêu cầu hoàn tiền</div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Đơn hàng</th>
                                    <th>Người dùng</th>
                                    <th>Tiền hoàn</th>
                                    <th>Phương thức</th>
                                    <th>Trạng thái</th>
                                    <th>Thời gian hợp lệ</th>
                                    <th>Ngân hàng</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($refunds as $refund)
                                    @php
                                        $isEligible = $refund->order->isEligibleForRefund();
                                    @endphp
                                    <tr>
                                        <td>{{ $refund->id }}</td>
                                        <td>{{ $refund->order->code }}</td>
                                        <td>{{ $refund->user->fullname }}</td>
                                        <td>{{ number_format($refund->total_amount, 2) }} VND</td>
                                        <td>{{ $refund->order->payment->name ?? 'COD' }}</td>
                                        <td>{{ $refund->status }}</td>
                                        <td>{{ $isEligible ? 'Hợp lệ' : 'Quá hạn' }}</td>
                                        <td>
                                            {{ $refund->bank_account_status }}<br>
                                            {{ $refund->bank_name }}<br>
                                            {{ $refund->bank_account }}
                                        </td>
                                        <td>
                                            <form action="{{ route('admin.refunds.update', $refund->id) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                @method('PATCH')
                                                <div class="form-group">
                                                    <select name="status" class="form-control">
                                                        <option value="pending" {{ $refund->status == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                                        <option value="receiving" {{ $refund->status == 'receiving' ? 'selected' : '' }}>Đang nhận hàng</option>
                                                        <option value="completed" {{ $refund->status == 'completed' ? 'selected' : '' }} {{ !$isEligible ? 'disabled' : '' }}>Hoàn thành</option>
                                                        <option value="rejected" {{ $refund->status == 'rejected' ? 'selected' : '' }}>Từ chối</option>
                                                        <option value="failed" {{ $refund->status == 'failed' ? 'selected' : '' }}>Thất bại</option>
                                                        <option value="cancel" {{ $refund->status == 'cancel' ? 'selected' : '' }}>Hủy</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <input type="text" name="admin_reason" class="form-control" placeholder="Lý do từ chối (nếu có)" value="{{ $refund->admin_reason }}">
                                                </div>
                                                @if (!$refund->order->payment || $refund->order->payment->name != 'VNPay')
                                                    <div class="form-group">
                                                        <input type="file" name="img_refunded_money" accept="image/*,application/pdf" {{ $refund->status == 'completed' ? 'disabled' : '' }}>
                                                    </div>
                                                @endif
                                                <button type="submit" class="btn btn-primary">Cập nhật</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary-box">
                    <h4>Tóm tắt hoàn tiền</h4>
                    @if ($refunds->isNotEmpty())
                        @php
                            $selectedRefund = $refunds->first(); // Hiển thị tóm tắt của yêu cầu hoàn tiền đầu tiên
                            $itemsSubtotal = $selectedRefund->refundItems->sum(function ($item) {
                                return $item->price * $item->quantity;
                            });
                            $discount = $selectedRefund->order->discount ?? 0;
                            $tax = $selectedRefund->order->tax ?? 0;
                            $subtotal = $itemsSubtotal - $discount + $tax;
                            $shippingCost = $selectedRefund->order->shipping_cost ?? 0;
                            $refundAmount = $selectedRefund->total_amount;
                        @endphp
                        <div class="summary-item">
                            <span>Tổng giá trị sản phẩm:</span>
                            <strong>{{ number_format($itemsSubtotal, 2) }} VND</strong>
                        </div>
                        <div class="summary-item">
                            <span>Giảm giá:</span>
                            <strong>-{{ number_format($discount, 2) }} VND</strong>
                        </div>
                        <div class="summary-item">
                            <span>Thuế:</span>
                            <strong>{{ number_format($tax, 2) }} VND</strong>
                        </div>
                        <div class="summary-item">
                            <span>Tổng phụ:</span>
                            <strong>{{ number_format($subtotal, 2) }} VND</strong>
                        </div>
                        <div class="summary-item">
                            <span>Phí vận chuyển:</span>
                            <strong>{{ number_format($shippingCost, 2) }} VND</strong>
                        </div>
                        <hr>
                        <div class="summary-item">
                            <span><strong>Số tiền hoàn:</strong></span>
                            <strong>{{ number_format($refundAmount, 2) }} VND</strong>
                        </div>
                    @else
                        <p>Chưa có yêu cầu hoàn tiền nào.</p>
                    @endif
                    <div class="mt-4">
                        <p>Cảm ơn bạn đã sử dụng hệ thống!</p>
                        <p>2025 © Your Company</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection