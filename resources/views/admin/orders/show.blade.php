@extends('admin.layouts.app')

@section('title', 'Chi tiết đơn hàng #' . $order->code)

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 fw-bold text-gray-800">Chi tiết đơn hàng #{{ $order->code }}</h1>
                <p class="text-muted mt-1">Thông tin chi tiết đơn hàng trong hệ thống</p>
            </div>
            <div>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-light rounded-pill shadow-sm"><i
                        class="fas fa-arrow-left me-1"></i> Quay lại danh sách</a>
            </div>
        </div>
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded shadow-sm" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded shadow-sm" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm rounded mb-4">
                    <div class="card-header bg-light fw-bold"><i class="fas fa-shopping-cart me-2"></i>Đơn hàng
                        #{{ $order->code }}</div>
                    <div class="card-body">
                        <div class="table-responsive mb-3">
                            <table class="table table-bordered align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th><i class="fas fa-tag me-1"></i> Tên sản phẩm</th>
                                        <th><i class="fas fa-image me-1"></i> Ảnh</th>
                                        <th><i class="fas fa-cube me-1"></i> Biến thể</th>
                                        <th><i class="fas fa-money-bill me-1"></i> Giá</th>
                                        <th><i class="fas fa-hashtag me-1"></i> Số lượng</th>
                                        <th><i class="fas fa-coins me-1"></i> Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
    @foreach ($order->items as $item)
        @php
            $variant = $item->productVariant;
            $product = $item->product;
            $unitPrice = $item->price_at_time;

            $getAttrValue = function ($entity, $keywords) {
                if (!$entity || !isset($entity->attributeValues)) return null;
                foreach ($entity->attributeValues as $attrVal) {
                    $attrName = strtolower($attrVal->attribute->name ?? '');
                    foreach ($keywords as $kw) {
                        if (str_contains($attrName, $kw)) {
                            return $attrVal->value;
                        }
                    }
                }
                return null;
            };

            $size = $getAttrValue($variant, ['size', 'kích']);
            $color = $getAttrValue($variant, ['color', 'màu']);

            if ($variant) {
                if (!empty($variant->img)) {
                    $img = asset('storage/' . $variant->img);
                } elseif ($variant->images && $variant->images->count() > 0) {
                    $img = asset('storage/' . $variant->images->first()->url);
                } else {
                    $img = $product->image_url ?? asset('assets/img/product/placeholder.jpg');
                }
            } else {
                $img = $product->image_url ?? asset('assets/img/product/placeholder.jpg');
            }
        @endphp
        <tr>
            <td>{{ $item->name ?? 'Sản phẩm ' . $item->product_id }}</td>
            <td>
                <img src="{{ $img }}" alt="{{ $item->name }}" style="width: 50px; height: 50px; object-fit: cover;">
            </td>
            <td>
                @if ($size) <span>Size: {{ $size }}</span><br> @endif
                @if ($color) <span>Màu: {{ $color }}</span> @endif
            </td>
            <td>{{ number_format($item->price, 0, ',', '.') }} ₫</td>
            <td>{{ $item->quantity }}</td>
            <td>{{ number_format($item->price * $item->quantity, 0, ',', '.') }} ₫</td>
        </tr>
    @endforeach
</tbody>

                            </table>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="fw-bold mb-2"><i class="fas fa-wallet me-2"></i>Chi tiết thanh toán</div>
                                    <div><i class="fas fa-user me-1"></i> <strong>Khách hàng:</strong>
                                        {{ $order->fullname ?? 'Không xác định' }}</div>
                                    <div><i class="fas fa-envelope me-1"></i> <strong>Email:</strong>
                                        {{ $order->email ?? 'Không xác định' }}</div>
                                    <div><i class="fas fa-phone me-1"></i> <strong>Điện thoại:</strong>
                                        {{ $order->phone_number ?? 'Không xác định' }}</div>
                                    <div><i class="fas fa-map-marker-alt me-1"></i> <strong>Địa chỉ:</strong>
                                        {{ $order->address ?? 'Không xác định' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="fw-bold mb-2"><i class="fas fa-truck me-2"></i>Chi tiết giao hàng</div>
                                    <div><i class="fas fa-user me-1"></i> <strong>Khách hàng:</strong>
                                        {{ $order->fullname ?? 'Không xác định' }}</div>
                                    <div><i class="fas fa-envelope me-1"></i> <strong>Email:</strong>
                                        {{ $order->email ?? 'Không xác định' }}</div>
                                    <div><i class="fas fa-phone me-1"></i> <strong>Điện thoại:</strong>
                                        {{ $order->phone_number ?? 'Không xác định' }}</div>
                                    <div><i class="fas fa-map-marker-alt me-1"></i> <strong>Địa chỉ:</strong>
                                        {{ $order->address ?? 'Không xác định' }}</div>
                                    <div><i class="fas fa-shipping-fast me-1"></i> <strong>Phương thức vận chuyển:</strong>
                                        Giao hàng {{ $order->shipping_type ?? 'Không xác định' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card shadow-sm rounded mb-4">
                    <div class="card-header bg-light fw-bold"><i class="fas fa-list-alt me-2"></i>Tóm tắt</div>
                    <div class="card-body p-0">
                        <table class="table table-bordered mb-0">
                            <tr>
                                <td><i class="fas fa-shopping-basket me-1"></i> Tổng phụ sản phẩm:</td>
                                <td>{{ number_format($order->items->sum(fn($item) => $item->price * $item->quantity), 0, ',', '.') }}
                                    ₫</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-tags me-1"></i> Giảm giá:</td>
                                <td>-{{ number_format($order->discount_amount ?? 0, 0, ',', '.') }} ₫</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-shipping-fast me-1"></i> Phí giao hàng:</td>
                                <td>
                                    @if ($order->shipping_type == 'thường')
                                        {{ number_format(16500, 0, ',', '.') }} ₫
                                    @elseif ($order->shipping_type == 'nhanh')
                                        {{ number_format(30000, 0, ',', '.') }} ₫
                                    @else
                                        {{ number_format(0, 0, ',', '.') }} ₫
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong><i class="fas fa-coins me-1"></i> Tổng cộng:</strong></td>
                                <td><strong>{{ number_format($order->total_amount, 0, ',', '.') }} ₫</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="card shadow-sm rounded mb-4">
                    <div class="card-header bg-light fw-bold"><i class="fas fa-info-circle me-2"></i>Trạng thái đơn hàng
                    </div>
                    <div class="card-body">
                        <div><i class="fas fa-credit-card me-1"></i> <strong>Trạng thái thanh toán:</strong>
                            {{ $paymentStatus }}</div>
                        <div><i class="fas fa-check-square me-1"></i> <strong>Trạng thái hoàn thành:</strong>
                            {{ $fulfillmentStatus }}</div>
                    </div>
                </div>

                @if($order->cancel_reason || $order->cancelled_at)
                <div class="card shadow-sm rounded mb-4 border-danger">
                    <div class="card-header bg-danger text-white fw-bold">
                        <i class="fas fa-times-circle me-2"></i>Thông tin hủy đơn hàng
                    </div>
                    <div class="card-body">
                        @if($order->cancel_reason)
                        <div class="mb-3">
                            <div class="fw-bold text-danger mb-2">
                                <i class="fas fa-exclamation-triangle me-2"></i>Lý do hủy đơn:
                            </div>
                            <div class="p-3 bg-light rounded border">
                                {{ $order->cancel_reason }}
                            </div>
                        </div>
                        @endif

                        @if($order->cancel_note)
                        <div class="mb-3">
                            <div class="fw-bold text-secondary mb-2">
                                <i class="fas fa-sticky-note me-2"></i>Ghi chú hủy đơn:
                            </div>
                            <div class="p-3 bg-light rounded border">
                                {{ $order->cancel_note }}
                            </div>
                        </div>
                        @endif

                        @if($order->cancelled_at)
                        <div class="mb-3">
                            <div class="fw-bold text-secondary mb-2">
                                <i class="fas fa-clock me-2"></i>Thời gian hủy đơn:
                            </div>
                            <div class="p-3 bg-light rounded border">
                                <i class="fas fa-calendar-alt me-2"></i>
                                {{ \Carbon\Carbon::parse($order->cancelled_at)->format('d/m/Y H:i:s') }}
                                <br>
                                <small class="text-muted">
                                    ({{ \Carbon\Carbon::parse($order->cancelled_at)->diffForHumans() }})
                                </small>
                            </div>
                        </div>
                        @endif

                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Lưu ý:</strong> Đơn hàng này đã được khách hàng hủy. Vui lòng kiểm tra và xử lý theo quy trình của công ty.
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <div class="col-lg-4 mb-4">

                <div class="card shadow-sm rounded mb-4">
                    <div class="card-header bg-light fw-bold"><i class="fas fa-sync-alt me-2"></i>Cập nhật trạng thái đơn
                        hàng</div>
                    <div class="card-body">
                        <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="mb-3">
                                <label for="order_status_id" class="form-label"><i class="fas fa-info-circle me-1"></i>
                                    Trạng thái đơn hàng</label>
                                <select name="order_status_id" id="order_status_id" class="form-control">
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status->id }}"
                                            {{ $currentStatus && $currentStatus->order_status_id == $status->id ? 'selected' : '' }}>
                                            {{ $status->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('order_status_id')
                                    <div class="text-danger"><i class="fas fa-exclamation-circle me-1"></i>
                                        {{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="is_paid" class="form-label"><i class="fas fa-credit-card me-1"></i> Trạng
                                    thái thanh toán</label>
                                <select name="is_paid" id="is_paid" class="form-control">
                                    <option value="1" {{ $order->is_paid ? 'selected' : '' }}>Đã thanh toán</option>
                                    <option value="0" {{ !$order->is_paid ? 'selected' : '' }}>Chờ thanh toán
                                    </option>
                                </select>
                                @error('is_paid')
                                    <div class="text-danger"><i class="fas fa-exclamation-circle me-1"></i>
                                        {{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="note" class="form-label"><i class="fas fa-sticky-note me-1"></i> Ghi
                                    chú</label>
                                <textarea name="note" id="note" class="form-control">{{ old('note', $currentStatus?->note) }}</textarea>
                                @error('note')
                                    <div class="text-danger"><i class="fas fa-exclamation-circle me-1"></i>
                                        {{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="customer_confirmation" class="form-label"><i
                                        class="fas fa-user-check me-1"></i> Xác nhận của khách hàng</label>
                                <select name="customer_confirmation" id="customer_confirmation" class="form-control">
                                    <option value="1"
                                        {{ old('customer_confirmation', $currentStatus?->customer_confirmation) == 1 ? 'selected' : '' }}>
                                        Đã xác nhận</option>
                                    <option value="0"
                                        {{ old('customer_confirmation', $currentStatus?->customer_confirmation) == 0 ? 'selected' : '' }}>
                                        Chưa xác nhận</option>
                                </select>
                                @error('customer_confirmation')
                                    <div class="text-danger"><i class="fas fa-exclamation-circle me-1"></i>
                                        {{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary w-100 mb-2"><i class="fas fa-save me-1"></i>
                                Cập nhật</button>
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary w-100"><i
                                    class="fas fa-arrow-left me-1"></i> Quay lại</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Lịch sử trạng thái đơn hàng - Full width -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm rounded mb-4">
                    <div class="card-header bg-light fw-bold">
                        <i class="fas fa-history me-2"></i>Lịch sử trạng thái đơn hàng
                    </div>
                    <div class="card-body p-0">
                        @if ($order->statusHistory->isEmpty())
                            <p class="text-muted p-3">
                                <i class="fas fa-exclamation-triangle me-1"></i> Chưa có lịch sử trạng thái.
                            </p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle text-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th><i class="fas fa-list-ol me-1"></i> STT</th>
                                            {{-- <th><i class="fas fa-id-badge me-1"></i> ID</th> --}}
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
                                                {{-- <td>{{ $status->id }}</td> --}}
                                                <td>{{ $status->orderStatus->name ?? 'Không rõ' }}</td>
                                                <td>{{ $status->note ?? 'Không có ghi chú' }}</td>
                                                <td>{{ $status->modifier?->name ?? 'Hệ thống' }}</td>
                                                <td>{{ $status->created_at->format('d/m/Y H:i:s') }}</td>
                                                <td>
                                                    @if ($status->is_current)
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check me-1"></i>Hiện tại
                                                        </span>
                                                    @else
                                                        <span class="text-muted">
                                                            <i class="fas fa-times me-1"></i>Cũ
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

