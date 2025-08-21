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
                                <input type="text" value="{{ $order->code }}" disabled
                                    class="w-full p-2 border rounded">
                            </div>
                            <div class="tp-checkout-input">
                                <label class="block text-gray-700">Tổng Số Tiền Hoàn</label>
                                <input type="number" name="total_amount" step="0.01" value="{{ $order->total_amount }}"
                                    required class="w-full p-2 border rounded">
                                <span class="text-red-500 text-sm error-message" id="total_amount_error"></span>
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
                                <span class="text-red-500 text-sm error-message" id="reason_error"></span>
                            </div>
                            <div class="tp-checkout-input">
                                <label class="block text-gray-700">Số Tài Khoản Ngân Hàng</label>
                                <input type="text" name="bank_account" required class="w-full p-2 border rounded"
                                    placeholder="Ví dụ: 1234567890">
                                <span class="text-red-500 text-sm error-message" id="bank_account_error"></span>
                            </div>
                            <div class="tp-checkout-input">
                                <label class="block text-gray-700">Tên Chủ Tài Khoản</label>
                                <input type="text" name="user_bank_name" required class="w-full p-2 border rounded"
                                    placeholder="Ví dụ: Nguyen Van An">
                                <span class="text-red-500 text-sm error-message" id="user_bank_name_error"></span>
                            </div>
                            <div class="tp-checkout-input">
                                <label class="block text-gray-700">Tên Ngân Hàng</label>
                                <select name="bank_name" required class="w-full p-2 border rounded">
                                    <option value="" disabled selected>Chọn ngân hàng</option>
                                    <option value="Vietcombank">Vietcombank</option>
                                    <option value="Techcombank">Techcombank</option>
                                    <option value="MBBank">MBBank</option>
                                    <option value="BIDV">BIDV</option>
                                    <option value="Agribank">Agribank</option>
                                    <option value="VPBank">VPBank</option>
                                    <option value="Sacombank">Sacombank</option>
                                    <option value="ACB">ACB</option>
                                    <!-- Thêm các ngân hàng khác nếu cần -->
                                </select>
                                <span class="text-red-500 text-sm error-message" id="bank_name_error"></span>
                            </div>
                            <div class="tp-checkout-input">
                                <label class="block text-gray-700">Danh Sách Sản Phẩm</label>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Tên Sản Phẩm</th>
                                            <th>Hình ảnh</th>
                                            <th>Biến Thể</th>
                                            <th>Số Lượng</th>
                                            <th>Giá</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($order->items as $item)
                                            <tr>
                                                <td>
                                                    {{ $item->name }}
                                                    @if ($item->variant && $item->variant->sku)
                                                        <div class="text-muted small">MÃ SP: {{ $item->variant->sku }}</div>
                                                    @elseif($item->product && $item->product->sku)
                                                        <div class="text-muted small">MÃ SP: {{ $item->product->sku }}</div>
                                                    @endif
                                                </td>
                                                <td class="align-middle">
                                                    @php
                                                        $thumbnail =
                                                            $item->variant->img ?? ($item->product->thumbnail ?? null);
                                                        $imageUrl = $thumbnail
                                                            ? Storage::url($thumbnail)
                                                            : asset('assets1/img/placeholder.jpg');
                                                    @endphp
                                                    <img src="{{ $imageUrl }}" alt="{{ $item->name }}"
                                                        style="max-width: 80px; max-height: 80px; object-fit: cover;">
                                                </td>
                                                <td>
                                                    @if ($item->product_variant_id && $item->variant)
                                                        @if ($item->attributes_variant)
                                                            <div class="text-muted small">
                                                                @php
                                                                    $attributes = json_decode(
                                                                        $item->attributes_variant,
                                                                        true,
                                                                    );
                                                                @endphp
                                                                @if (is_array($attributes))
                                                                    @foreach ($attributes as $key => $value)
                                                                        {{ $key }}: {{ $value }}
                                                                        @if (!$loop->last)
                                                                            ,
                                                                        @endif
                                                                    @endforeach
                                                                @endif
                                                            </div>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">Không có biến thể</span>
                                                    @endif
                                                </td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>{{ number_format($item->price, 0, ',', '.') }} ₫</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <input type="hidden" name="items"
                                    value="{{ json_encode(
                                        $order->items->map(function ($item) {
                                                return [
                                                    'product_id' => $item->product_id,
                                                    'variant_id' => $item->product_variant_id ?? 0,
                                                    'name' => $item->name,
                                                    'name_variant' => $item->name_variant ?? '',
                                                    'quantity' => $item->quantity,
                                                    'price' => $item->price,
                                                    'price_variant' => $item->price_variant ?? $item->price,
                                                    'quantity_variant' => $item->quantity_variant ?? $item->quantity,
                                                ];
                                            })->toArray(),
                                    ) }}">
                            </div>
                            <button type="submit" class="tp-product-details-buy-now-btn w-100">Gửi Yêu Cầu Hoàn
                                Tiền</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@section('scripts')
    <script>
        $(document).ready(function() {
            $('#refundForm').on('submit', function(e) {
                e.preventDefault();
                // Xóa thông báo lỗi cũ
                $('.error-message').text('');

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
                    url: '{{ route('refund.submit') }}',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(data),
                    success: function(response) {
                        toastr.success(
                            `Yêu cầu hoàn tiền đã được gửi! Mã yêu cầu: ${response.refund_id}`
                            );
                        setTimeout(() => window.location.href = '{{ route('home') }}', 2000);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors || {};
                            // Hiển thị lỗi validate bên dưới các trường
                            Object.keys(errors).forEach(function(key) {
                                const field = key.split('.')
                            .shift(); // Lấy tên trường chính
                                $(`#${field}_error`).text(errors[key][0]);
                            });
                        } else {
                            toastr.error(xhr.responseJSON?.message ||
                                'Lỗi khi gửi yêu cầu hoàn tiền');
                        }
                    }
                });
            });
        });
    </script>
@endsection
