@extends('admin.layouts.app')

@section('title', 'Chi tết hoàn tiền')
@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Chi Tiết Yêu Cầu Hoàn Tiền #{{ $refund->id }}</h1>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Mã Đơn Hàng:</strong> {{ $refund->order_id }}</p>
                    <p><strong>Người Dùng:</strong> {{ $refund->user->fullname ?? 'N/A' }}</p>
                    <p><strong>Tổng Tiền:</strong> {{ number_format($refund->total_amount, 2) }}</p>
                    <p><strong>Lý Do:</strong> {{ $refund->reason }}</p>
                    <p><strong>Số Tài Khoản:</strong> {{ $refund->bank_account }}</p>
                    <p><strong>Tên Chủ Tài Khoản:</strong> {{ $refund->user_bank_name }}</p>
                    <p><strong>Tên Ngân Hàng:</strong> {{ $refund->bank_name }}</p>
                    <p><strong>Trạng Thái:</strong> {{ $refund->status }}</p>
                    <p><strong>Lý Do Admin:</strong> {{ $refund->admin_reason ?? 'N/A' }}</p>
                    <p><strong>Đã Chuyển Tiền:</strong> {{ $refund->is_send_money ? 'Có' : 'Không' }}</p>
                </div>
            </div>
            <h4>Sản Phẩm Hoàn Tiền</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Sản Phẩm</th>
                        <th>Biến Thể</th>
                        <th>Số Lượng</th>
                        <th>Giá</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($refund->items as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->name_variant }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <h4>Cập Nhật Yêu Cầu</h4>
            <form action="{{ route('admin.refunds.update', $refund->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="status">Trạng Thái</label>
                    <select name="status" id="status" class="form-control">
                        <option value="pending" {{ $refund->status == 'pending' ? 'selected' : '' }}>Đang Chờ</option>
                        <option value="receiving" {{ $refund->status == 'receiving' ? 'selected' : '' }}>Đang Nhận Hàng</option>
                        <option value="completed" {{ $refund->status == 'completed' ? 'selected' : '' }}>Hoàn Thành</option>
                        <option value="rejected" {{ $refund->status == 'rejected' ? 'selected' : '' }}>Từ Chối</option>
                        <option value="failed" {{ $refund->status == 'failed' ? 'selected' : '' }}>Thất Bại</option>
                        <option value="cancel" {{ $refund->status == 'cancel' ? 'selected' : '' }}>Hủy</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Lý Do Admin (Nếu Từ Chối)</label>
                    <textarea name="admin_reason" class="form-control">{{ $refund->admin_reason }}</textarea>
                </div>
                <div class="form-group">
                    <label>Đã Chuyển Tiền</label>
                    <select name="is_send_money" class="form-control" required>
                        <option value="0" {{ $refund->is_send_money == 0 ? 'selected' : '' }}>Chưa</option>
                        <option value="1" {{ $refund->is_send_money == 1 ? 'selected' : '' }}>Đã Chuyển</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Cập Nhật</button>
            </form>
        </div>
    </div>
</div>
@endsection