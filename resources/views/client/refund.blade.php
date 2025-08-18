@extends('client.layouts.default')

@section('title', 'Hoàn tiền')

@section('content')
<div class="tp-product-details-area tp-product-details-space">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <div class="tp-product-details-wrapper">
                    <h3 class="tp-product-details-title">Gửi Yêu Cầu Hoàn Tiền - Đơn Hàng #{{ $order->code }}</h3>
                    <form id="refundForm" class="tp-product-details-form">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        <div class="tp-checkout-input">
                            <label class="block text-gray-700">Mã Đơn Hàng</label>
                            <input type="text" value="{{ $order->code }}" disabled class="w-full p-2 border rounded">
                        </div>
                        <div class="tp-checkout-input">
                            <label class="block text-gray-700">Tổng Số Tiền Hoàn</label>
                            <input type="number" name="total_amount" step="0.01" value="{{ $order->total_amount }}" required class="w-full p-2 border rounded">
                        </div>
                        <div class="tp-checkout-input">
                            <label class="block text-gray-700">Lý Do Hoàn Tiền</label>
                            <select name="reason" required class="w-full p-2 border rounded">
                                <option value="" disabled selected>Chọn lý do</option>
                                <option value="product_defective">Sản phẩm lỗi</option>
                                <option value="changed_mind">Thay đổi ý định</option>
                                <option value="wrong_item_delivered">Giao sai hàng</option>
                                <option value="other">Khác</option>
                            </select>
                        </div>
                        <div class="tp-checkout-input">
                            <label class="block text-gray-700">Số Tài Khoản Ngân Hàng</label>
                            <input type="text" name="bank_account" required class="w-full p-2 border rounded">
                        </div>
                        <div class="tp-checkout-input">
                            <label class="block text-gray-700">Tên Chủ Tài Khoản</label>
                            <input type="text" name="user_bank_name" required class="w-full p-2 border rounded">
                        </div>
                        <div class="tp-checkout-input">
                            <label class="block text-gray-700">Tên Ngân Hàng</label>
                            <input type="text" name="bank_name" required class="w-full p-2 border rounded">
                        </div>
                        <div class="tp-checkout-input">
                            <label class="block text-gray-700">Danh Sách Sản Phẩm</label>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Tên Sản Phẩm</th>
                                        <th>Biến Thể</th>
                                        <th>Số Lượng</th>
                                        <th>Giá</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->items as $item)
                                        <tr>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->name_variant ?? 'N/A' }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ number_format($item->price, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <input type="hidden" name="items" value="{{ json_encode($order->items->map(function ($item) {
                                return [
                                    'product_id' => $item->product_id,
                                    'variant_id' => $item->variant_id ?? 0,
                                    'name' => $item->name,
                                    'name_variant' => $item->name_variant ?? '',
                                    'quantity' => $item->quantity,
                                    'price' => $item->price,
                                    'price_variant' => $item->price_variant ?? $item->price,
                                    'quantity_variant' => $item->quantity_variant ?? $item->quantity,
                                ];
                            })->toArray()) }}">
                        </div>
                        <button type="submit" class="tp-product-details-buy-now-btn w-100">Gửi Yêu Cầu Hoàn Tiền</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#refundForm').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = {
                _token: formData.get('_token'),
                order_id: formData.get('order_id'),
                total_amount: formData.get('total_amount'),
                reason: formData.get('reason'),
                bank_account: formData.get('bank_account'),
                user_bank_name: formData.get('user_bank_name'),
                bank_name: formData.get('bank_name'),
                items: JSON.parse(formData.get('items') || '[]')
            };

            $.ajax({
                url: '{{ route("refund.submit") }}',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(data),
                success: function(response) {
                    toastr.success(`Yêu cầu hoàn tiền đã được gửi! Mã yêu cầu: ${response.refund_id}`);
                    setTimeout(() => window.location.href = '{{ route("home") }}', 2000);
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Lỗi khi gửi yêu cầu hoàn tiền');
                }
            });
        });
    });
</script>
@endsection
