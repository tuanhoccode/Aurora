@extends('admin.layouts.app')

@section('content')
    <div class="container">
        @if (session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            </div>
        @endif

        <h1><i class="fas fa-shopping-cart me-2"></i> Đơn hàng #{{ $order->code }}</h1>

        <h2><i class="fas fa-box-open me-2"></i> Sản phẩm</h2>

        {{-- Danh sách sản phẩm --}}
        <div class="section-card">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th><i class="fas fa-tag me-1"></i> Tên sản phẩm</th>
                            <th><i class="fas fa-image me-1"></i> Ảnh sản phẩm</th>
                            <th><i class="fas fa-cube me-1"></i> Biến thể</th>
                            <th><i class="fas fa-money-bill me-1"></i> Giá</th>
                            <th><i class="fas fa-hashtag me-1"></i> Số lượng</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td>
                                    @if ($item->product && $item->product->thumbnail)
                                        <img src="{{ asset('storage/' . $item->product->thumbnail) }}" alt="{{ $item->name }}"
                                            style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        <span><i class="fas fa-ban me-1"></i> Không có ảnh</span>
                                    @endif
                                </td>
                                <td>{{ $item->product_variant_id ?? 'Không có' }}</td>
                                <td>{{ number_format($item->price, 0, ',', '.') }} VNĐ</td>
                                <td>{{ $item->quantity }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <h2><i class="fas fa-wallet me-2"></i> Chi tiết thanh toán</h2>
                <p><strong><i class="fas fa-user me-1"></i> Khách hàng:</strong> {{ $user->fullname ?? 'Không xác định' }}</p>
                <p><strong><i class="fas fa-envelope me-1"></i> Email:</strong> {{ $user->email ?? 'Không xác định' }}</p>
                <p><strong><i class="fas fa-map-marker-alt me-1"></i> Địa chỉ:</strong> {{ $user->address->address ?? 'Không xác định' }}</p>
                <p><strong><i class="fas fa-phone me-1"></i> Điện thoại:</strong> {{ $user->phone_number ?? 'Không xác định' }}</p>
            </div>
            <div class="col-md-6">
                <h2><i class="fas fa-truck me-2"></i> Chi tiết giao hàng</h2>
                <p><strong><i class="fas fa-user me-1"></i> Khách hàng:</strong> {{ $order->fullname ?? 'Không xác định' }}</p>
                <p><strong><i class="fas fa-envelope me-1"></i> Email:</strong> {{ $order->email ?? 'Không xác định' }}</p>
                <p><strong><i class="fas fa-phone me-1"></i> Điện thoại:</strong> {{ $order->phone_number ?? 'Không xác định' }}</p>
                <p><strong><i class="fas fa-map-marker-alt me-1"></i> Địa chỉ:</strong> {{ $order->address ?? 'Không xác định' }}</p>
                <p><strong><i class="fas fa-shipping-fast me-1"></i> Phương thức vận chuyển: </strong>Giao hàng {{ $order->shipping_type }}</p>
            </div>
        </div>

        <h2><i class="fas fa-list-alt me-2"></i> Tóm tắt</h2>
        <table class="table table-bordered">
            <tr>
                <td><i class="fas fa-shopping-basket me-1"></i> Tổng phụ sản phẩm:</td>
                <td>{{ number_format($order->items_subtotal ?? ($order->items->sum(fn($item) => $item->price * $item->quantity) ?? 0), 0, ',', '.') }} ₫</td>
            </tr>
            <tr>
                <td><i class="fas fa-tags me-1"></i> Giảm giá:</td>
                <td>-{{ number_format($order->discount_type ?? 0, 0, ',', '.') }} ₫</td>
            </tr>
            <tr>
                <td><i class="fas fa-percent me-1"></i> Thuế:</td>
                <td>{{ number_format($order->tax ?? 0, 0, ',', '.') }} ₫</td>
            </tr>
            <tr>
                <td><i class="fas fa-shipping-fast me-1"></i> Phí giao hàng:</td>
                <td>
                    @if ($order->shipping_type == 'thường')
                        {{ number_format(16500, 0, ',', '.') }} ₫
                    @elseif ($order->shipping_type == 'nhanh')
 Oto')
                        {{ number_format(30000, 0, ',', '.') }} ₫
                    @else
                        {{ number_format($order->shipping_cost ?? 0, 0, ',', '.') }} ₫
                    @endif
                </td>
            </tr>
            <tr>
                <td><strong><i class="fas fa-coins me-1"></i> Tổng cộng:</strong></td>
                <td><strong>{{ number_format($order->total_amount, 0, ',', '.') }} ₫</strong></td>
            </tr>
        </table>

        <h2><i class="fas fa-info-circle me-2"></i> Trạng thái đơn hàng</h2>
        <p><strong><i class="fas fa-credit-card me-1"></i> Trạng thái thanh toán:</strong> {{ $paymentStatus }}</p>
        <p><strong><i class="fas fa-check-square me-1"></i> Trạng thái hoàn thành:</strong> {{ $fulfillmentStatus }}</p>

        <h2><i class="fas fa-history me-2"></i> Lịch sử trạng thái</h2>
        @if ($order->statusHistory->isEmpty())
            <p class="text-muted"><i class="fas fa-exclamation-triangle me-1"></i> Chưa có lịch sử trạng thái.</p>
        @else
            <div class="table-responsive">
                <table class="table table-bordered align-middle text-nowrap">
                    <thead class="table-light">
                        <tr>
                            <th><i class="fas fa-list-ol me-1"></i> STT</th>
                            <th><i class="fas fa-id-badge me-1"></i> ID</th>
                            <th><i class="fas fa-info me-1"></i> Trạng thái</th>
                            <th><i class="fas fa-sticky-note me-1"></i> Ghi chú</th>
                            <th><i class="fas fa-user-edit me-1"></i> Người cập nhật</th>
                            <th><i class="fas fa-clock me-1"></i> Thời gian</th>
                            <th><i class="fas fa-check-circle me-1"></i> Hiện tại</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->statusHistory as $status)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $status->id }}</td>
                                <td>{{ $status->status->name }}</td>
                                <td>{{ $status->note ?? 'Không có ghi chú' }}</td>
                                <td>{{ $status->modifier?->name ?? 'Hệ thống' }}</td>
                                <td>{{ $status->created_at->format('d/m/Y H:i:s') }}</td>
                                <td>
                                    @if ($status->is_current)
                                        <span class="badge bg-success"><i class="fas fa-check me-1"></i> Hiện tại</span>
                                    @else
                                        <span class="text-muted"><i class="fas fa-times me-1"></i> Cũ</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <h2><i class="fas fa-sync-alt me-2"></i> Cập nhật trạng thái đơn hàng</h2>
        <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
            @csrf
            @method('PATCH')
            <div class="mb-3">
                <label for="order_status_id" class="form-label"><i class="fas fa-info-circle me-1"></i> Trạng thái đơn hàng</label>
                <select name="order_status_id" id="order_status_id" class="form-control">
                    @foreach ($statuses as $status)
                        <option value="{{ $status->id }}"
                            {{ $currentStatus && $currentStatus->order_status_id == $status->id ? 'selected' : '' }}>
                            {{ $status->name }}
                        </option>
                    @endforeach
                </select>
                @error('order_status_id')
                    <div class="text-danger"><i class="fas fa-exclamation-circle me-1"></i> {{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="is_paid" class="form-label"><i class="fas fa-credit-card me-1"></i> Trạng thái thanh toán</label>
                <select name="is_paid" id="is_paid" class="form-control">
                    <option value="1" {{ $order->is_paid ? 'selected' : '' }}>Đã thanh toán</option>
                    <option value="0" {{ !$order->is_paid ? 'selected' : '' }}>Chờ thanh toán</option>
                </select>
                @error('is_paid')
                    <div class="text-danger"><i class="fas fa-exclamation-circle me-1"></i> {{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="note" class="form-label"><i class="fas fa-sticky-note me-1"></i> Ghi chú</label>
                <textarea name="note" id="note" class="form-control">{{ old('note', $currentStatus?->note) }}</textarea>
                @error('note')
                    <div class="text-danger"><i class="fas fa-exclamation-circle me-1"></i> {{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="customer_confirmation" class="form-label"><i class="fas fa-user-check me-1"></i> Xác nhận của khách hàng</label>
                <select name="customer_confirmation" id="customer_confirmation" class="form-control">
                    <option value="1"
                        {{ old('customer_confirmation', $currentStatus?->customer_confirmation) == 1 ? 'selected' : '' }}>
                        Đã xác nhận</option>
                    <option value="0"
                        {{ old('customer_confirmation', $currentStatus?->customer_confirmation) == 0 ? 'selected' : '' }}>
                        Chưa xác nhận</option>
                </select>
                @error('customer_confirmation')
                    <div class="text-danger"><i class="fas fa-exclamation-circle me-1"></i> {{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Cập nhật</button>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i> Quay lại</a>
        </form>
    </div>
@endsection