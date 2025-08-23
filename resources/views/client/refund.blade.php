@extends('client.layouts.default')

@section('title', 'Hoàn tiền')

@section('content')
    <div class="tp-product-details-area tp-product-details-space">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="tp-product-details-wrapper">
                        <h3 class="tp-product-details-title">Gửi Yêu Cầu Hoàn Tiền - Đơn Hàng #{{ $order->code }}</h3>
                        
                        <!-- Hiển thị thông báo lỗi hoặc thành công -->
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
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

                        <form action="{{ route('refund.submit') }}" method="POST" class="tp-product-details-form" enctype="multipart/form-data" id="refundForm">
                            @csrf
                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                            <div class="tp-checkout-input">
                                <label class="block text-gray-700">Mã Đơn Hàng</label>
                                <input type="text" value="{{ $order->code }}" disabled class="w-full p-2 border rounded">
                            </div>
                            <div class="tp-checkout-input">
                                <label class="block text-gray-700">Tổng Số Tiền Hoàn</label>
                                <input type="number" name="total_amount" step="0.01" value="{{ old('total_amount', $order->total_amount) }}" required class="w-full p-2 border rounded">
                                @error('total_amount')
                                    <span class="text-red-500 text-sm error-message">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="tp-checkout-input">
                                <label class="block text-gray-700">Lý Do Hoàn Tiền</label>
                                <select name="reason" required class="w-1/4 p-2 border rounded">
                                    <option value="" disabled {{ old('reason') ? '' : 'selected' }}>Chọn lý do</option>
                                    <option value="product_defective" {{ old('reason') == 'product_defective' ? 'selected' : '' }}>Sản phẩm lỗi</option>
                                    <option value="changed_mind" {{ old('reason') == 'changed_mind' ? 'selected' : '' }}>Thay đổi ý định</option>
                                    <option value="wrong_item_delivered" {{ old('reason') == 'wrong_item_delivered' ? 'selected' : '' }}>Giao sai hàng</option>
                                    <option value="other" {{ old('reason') == 'other' ? 'selected' : '' }}>Khác</option>
                                </select>
                                @error('reason')
                                    <span class="text-red-500 text-sm error-message">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="tp-checkout-input">
                                <label class="block text-gray-700">Hình Ảnh Minh Chứng (Tùy chọn)</label>
                                <input type="file" name="reason_image" accept="image/*" class="w-full p-2 border rounded">
                                <small class="text-muted">Tải lên ảnh minh chứng lý do hoàn tiền (định dạng JPG, PNG, tối đa 5MB).</small>
                                <span id="reason_image_error" class="text-red-500 text-sm error-message"></span>
                                @error('reason_image')
                                    <span class="text-red-500 text-sm error-message">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="tp-checkout-input">
                                <label class="block text-gray-700">Số Tài Khoản Ngân Hàng</label>
                                <input type="text" name="bank_account" value="{{ old('bank_account') }}" required class="w-full p-2 border rounded" placeholder="Ví dụ: 1234567890">
                                @error('bank_account')
                                    <span class="text-red-500 text-sm error-message">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="tp-checkout-input">
                                <label class="block text-gray-700">Tên Chủ Tài Khoản</label>
                                <input type="text" name="user_bank_name" value="{{ old('user_bank_name') }}" required class="w-full p-2 border rounded" placeholder="Ví dụ: Nguyen Van An">
                                @error('user_bank_name')
                                    <span class="text-red-500 text-sm error-message">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="tp-checkout-input">
                                <label class="block text-gray-700">Tên Ngân Hàng</label>
                                <select name="bank_name" required class="w-1/4 p-2 border rounded">
                                    <option value="" disabled {{ old('bank_name') ? '' : 'selected' }}>Chọn ngân hàng</option>
                                    <option value="Vietcombank" {{ old('bank_name') == 'Vietcombank' ? 'selected' : '' }}>Vietcombank</option>
                                    <option value="Techcombank" {{ old('bank_name') == 'Techcombank' ? 'selected' : '' }}>Techcombank</option>
                                    <option value="MBBank" {{ old('bank_name') == 'MBBank' ? 'selected' : '' }}>MBBank</option>
                                    <option value="BIDV" {{ old('bank_name') == 'BIDV' ? 'selected' : '' }}>BIDV</option>
                                    <option value="Agribank" {{ old('bank_name') == 'Agribank' ? 'selected' : '' }}>Agribank</option>
                                    <option value="VPBank" {{ old('bank_name') == 'VPBank' ? 'selected' : '' }}>VPBank</option>
                                    <option value="Sacombank" {{ old('bank_name') == 'Sacombank' ? 'selected' : '' }}>Sacombank</option>
                                    <option value="ACB" {{ old('bank_name') == 'ACB' ? 'selected' : '' }}>ACB</option>
                                </select>
                                @error('bank_name')
                                    <span class="text-red-500 text-sm error-message">{{ $message }}</span>
                                @enderror
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
                                                        $thumbnail = $item->variant->img ?? ($item->product->thumbnail ?? null);
                                                        $imageUrl = $thumbnail ? Storage::url($thumbnail) : asset('assets1/img/placeholder.jpg');
                                                    @endphp
                                                    <img src="{{ $imageUrl }}" alt="{{ $item->name }}" style="max-width: 80px; max-height: 80px; object-fit: cover;">
                                                </td>
                                                <td>
                                                    @if ($item->product_variant_id && $item->variant)
                                                        @if ($item->attributes_variant)
                                                            <div class="text-muted small">
                                                                @php
                                                                    $attributes = json_decode($item->attributes_variant, true);
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
                                <input type="hidden" name="items" value="{{ json_encode(
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
                                    })->toArray()
                                ) }}">
                                @error('items')
                                    <span class="text-red-500 text-sm error-message">{{ $message }}</span>
                                @enderror
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
        // Kiểm tra kích thước tệp trước khi gửi form
        document.querySelector('form#refundForm').addEventListener('submit', function (e) {
            const fileInput = document.querySelector('input[name="reason_image"]');
            const errorSpan = document.querySelector('#reason_image_error');
            errorSpan.textContent = ''; // Xóa thông báo lỗi cũ
            if (fileInput.files.length > 0) {
                const fileSize = fileInput.files[0].size / 1024 / 1024; // MB
                if (fileSize > 5) {
                    e.preventDefault();
                    errorSpan.textContent = 'Ảnh không được vượt quá 5MB.';
                }
            }
        });
    </script>
@endsection
