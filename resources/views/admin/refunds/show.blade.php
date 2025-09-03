@extends('admin.layouts.app')

@section('title', 'Chi tiết hoàn tiền')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Chi Tiết Yêu Cầu Hoàn Tiền #{{ $refund->id }}</h1>
        <div class="card shadow mb-4">
            <div class="card-body">
                <!-- Hiển thị thông báo thành công hoặc lỗi -->
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
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

                <div class="row">
                    <div class="col-md-6">
                        @php
                            $isVNPay = isset($refund->order) && (int) ($refund->order->payment_id) === 2;
                            $vnpLog = null;
                            if ($isVNPay) {
                                $vnpLog = \App\Models\PaymentLog::where('order_id', $refund->order_id)
                                    ->where('response_code', '00')
                                    ->latest('id')
                                    ->first();
                            }
                        @endphp
                        <p><strong>Mã Đơn Hàng:</strong> {{ $refund->order->code }}</p>
                        <p><strong>Người Dùng:</strong> {{ $refund->user->fullname ?? 'N/A' }}</p>
                        <p><strong>Email:</strong> {{ $refund->order->email ?? ($refund->user->email ?? 'N/A') }}</p>
                        <p><strong>Tổng Tiền:</strong> {{ number_format($refund->total_amount, 0, ',', '.') }} ₫</p>
                        <p><strong>Lý Do:</strong> {{ \App\Http\Controllers\RefundController::getReasonText($refund->reason) }}</p>
                        <p><strong>Phương Thức Thanh Toán:</strong> {{ $isVNPay ? 'VNPay' : 'Khác' }}</p>

                        <p><strong>Số Tài Khoản:</strong> {{ !empty($refund->bank_account) ? $refund->bank_account : '9704198526191432198' }}</p>
                        <p><strong>Tên Chủ Tài Khoản:</strong> {{ !empty($refund->user_bank_name) ? $refund->user_bank_name : 'NGUYEN VAN A' }}</p>
                        <p><strong>Tên Ngân Hàng:</strong> {{ !empty($refund->bank_name) ? $refund->bank_name : 'NCB' }}</p>
                        <p><strong>Trạng Thái:</strong> {{ \App\Http\Controllers\RefundController::getReasonText($refund->status) }}</p>
                        <p><strong>Lý Do Admin:</strong> {{ $refund->admin_reason ?? '' }}</p>
                        <p><strong>Đã Chuyển Tiền:</strong> {{ $refund->is_send_money ? 'Có' : 'Không' }}</p>
                        <p><strong>Ngày Tạo Yêu Cầu:</strong> {{ optional($refund->created_at)->format('d/m/Y, H:i') ?? 'N/A' }}</p>
                        <p><strong>Cập Nhật Lần Cuối:</strong> {{ optional($refund->updated_at)->format('d/m/Y, H:i') ?? 'N/A' }}</p>
                        @if ($refund->reason_image)
                            <p><strong>Ảnh Minh Chứng (Người Dùng):</strong></p>
                            <img src="{{ Storage::url($refund->reason_image) }}" alt="Reason Image" style="max-width: 200px; max-height: 200px; object-fit: cover;">
                        @else
                            <p><strong>Ảnh Minh Chứng (Người Dùng):</strong> Không có</p>
                        @endif
                        @if ($refund->admin_reason_image)
                            <p><strong>Ảnh Minh Chứng (Admin):</strong></p>
                            <img src="{{ Storage::url($refund->admin_reason_image) }}" alt="Admin Reason Image" style="max-width: 200px; max-height: 200px; object-fit: cover;">
                        @else
                            <p><strong>Ảnh Minh Chứng (Admin):</strong> Không có</p>
                        @endif
                        @if ($refund->order->img_refunded_money)
                            <p><strong>Ảnh Hoàn Tiền (Đơn Hàng):</strong></p>
                            <img src="{{ Storage::url($refund->order->img_refunded_money) }}" alt="Refunded Money Image" style="max-width: 200px; max-height: 200px; object-fit: cover;">
                        @else
                            <p><strong>Ảnh Hoàn Tiền (Đơn Hàng):</strong> Không có</p>
                        @endif
                    </div>
                </div>
                <h4>Sản Phẩm Hoàn Tiền</h4>
                @php
                    $hasVariant = $refund->items->contains(function($it){
                        return !empty($it->variant_id);
                    });
                @endphp
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tên Sản Phẩm</th>
                            <th>Ảnh</th>
                            @if($hasVariant)
                                <th>Biến Thể</th>
                            @endif
                            <th>Số Lượng</th>
                            <th>Giá</th>
                            <th>Thành Tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($refund->items as $item)
                            @php
                                $product = $item->product ?? \App\Models\Product::find($item->product_id);
                                $variant = $item->productVariant ?? \App\Models\ProductVariant::with('attributeValues.attribute')->find($item->variant_id);

                                $getAttrValue = function ($variant, $keywords) {
                                    if (!$variant || !$variant->attributeValues) {
                                        return null;
                                    }
                                    foreach ($variant->attributeValues as $attr) {
                                        $attrName = strtolower($attr->attribute->name ?? '');
                                        foreach ($keywords as $kw) {
                                            if (str_contains($attrName, $kw)) {
                                                return $attr->value;
                                            }
                                        }
                                    }
                                    return null;
                                };

                                $size = $getAttrValue($variant, ['size', 'kích']);
                                $color = $getAttrValue($variant, ['color', 'màu']);

                                $img = $variant && $variant->img
                                    ? Storage::url($variant->img)
                                    : ($product && $product->thumbnail
                                        ? Storage::url($product->thumbnail)
                                        : asset('assets1/img/placeholder.jpg'));
                            @endphp
                            <tr>
                                <td>{{ $item->name ?? 'Sản phẩm #' . $item->product_id }}</td>
                                <td>
                                    <img src="{{ $img }}" alt="{{ $item->name }}"
                                        style="width: 50px; height: 50px; object-fit: cover;">
                                </td>
                                @if($hasVariant)
                                    <td>
                                        {{ $color ?? '' }}{{ $color && $size ? ' / ' : '' }}{{ $size ?? '' }}
                                    </td>
                                @endif
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->price, 0, ',', '.') }} ₫</td>
                                <td>{{ number_format($item->price * $item->quantity, 0, ',', '.') }} ₫</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <h4>Cập Nhật Yêu Cầu</h4>
                <form action="{{ route('admin.refunds.update', $refund->id) }}" method="POST" enctype="multipart/form-data" id="refundUpdateForm">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="status">Trạng Thái</label>
                        <select name="status" id="status" class="form-control">
                            <option value="pending" {{ $refund->status == 'pending' ? 'selected' : '' }}>Đang chờ</option>
                            <option value="receiving" {{ $refund->status == 'receiving' ? 'selected' : '' }}>Đang nhận hàng</option>
                            <option value="completed" {{ $refund->status == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                            <option value="rejected" {{ $refund->status == 'rejected' ? 'selected' : '' }}>Từ chối</option>
                            <option value="failed" {{ $refund->status == 'failed' ? 'selected' : '' }}>Thất bại</option>
                            <option value="cancel" {{ $refund->status == 'cancel' ? 'selected' : '' }}>Hủy</option>
                        </select>
                        <span id="status_error" class="text-danger text-sm"></span>
                        @error('status')
                            <span class="text-danger text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Lý Do Admin (Nếu Từ Chối)</label>
                        <textarea name="admin_reason" class="form-control">{{ old('admin_reason', $refund->admin_reason) }}</textarea>
                        <span id="admin_reason_error" class="text-danger text-sm"></span>
                        @error('admin_reason')
                            <span class="text-danger text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Đã Chuyển Tiền</label>
                        <select name="is_send_money" id="is_send_money" class="form-control" required>
                            <option value="0" {{ old('is_send_money', $refund->is_send_money) == 0 ? 'selected' : '' }}>Chưa</option>
                            <option value="1" {{ old('is_send_money', $refund->is_send_money) == 1 ? 'selected' : '' }}>Đã chuyển</option>
                        </select>
                        <span id="is_send_money_error" class="text-danger text-sm"></span>
                        @error('is_send_money')
                            <span class="text-danger text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Ảnh Minh Chứng Admin (Bắt buộc khi Hoàn Thành)</label>
                        <input type="file" name="admin_reason_image" id="admin_reason_image" accept="image/*" class="form-control">
                        <small class="text-muted">Tải lên ảnh minh chứng (JPG, PNG, tối đa 5MB).</small>
                        <span id="admin_reason_image_error" class="text-danger text-sm"></span>
                        @error('admin_reason_image')
                            <span class="text-danger text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary" id="submitButton">Cập Nhật</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            toastr.options = {
                positionClass: 'toast-top-right',
                preventDuplicates: true,
                timeOut: 3000,
                closeButton: true,
                progressBar: true
            };

            let isSubmitting = false;
            $('#refundUpdateForm').on('submit', function(e) {
                e.preventDefault();
                if (isSubmitting) return;

                isSubmitting = true;
                const $button = $('#submitButton');
                $button.prop('disabled', true);
                $('.text-danger').text('');

                const status = $('#status').val();
                const isSendMoney = $('#is_send_money').val();
                const adminReasonImage = $('#admin_reason_image')[0].files.length;
                const hasExistingImage = {{ $refund->admin_reason_image ? 'true' : 'false' }};

                // Client-side validation
                if (status === 'completed') {
                    if (isSendMoney !== '1') {
                        $('#is_send_money_error').text('Vui lòng chọn "Đã chuyển" khi chuyển trạng thái sang Hoàn thành.');
                        isSubmitting = false;
                        $button.prop('disabled', false);
                        return;
                    }
                    if (adminReasonImage === 0 && !hasExistingImage) {
                        $('#admin_reason_image_error').text('Vui lòng tải lên ảnh minh chứng khi chuyển trạng thái sang Hoàn thành.');
                        isSubmitting = false;
                        $button.prop('disabled', false);
                        return;
                    }
                    if (adminReasonImage > 0) {
                        const fileSize = $('#admin_reason_image')[0].files[0].size / 1024 / 1024; // MB
                        if (fileSize > 5) {
                            $('#admin_reason_image_error').text('Ảnh minh chứng không được vượt quá 5MB.');
                            isSubmitting = false;
                            $button.prop('disabled', false);
                            return;
                        }
                    }
                }

                const formData = new FormData(this);
                $.ajax({
                    url: '{{ route('admin.refunds.update', $refund->id) }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        toastr.success('Cập nhật yêu cầu hoàn tiền thành công!', 'Thành công');
                        setTimeout(() => window.location.reload(), 2000);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors || {};
                            Object.keys(errors).forEach(function(key) {
                                $(`#${key.replace('.', '_')}_error`).text(errors[key][0]);
                            });
                        } else {
                            toastr.error(xhr.responseJSON?.message || 'Lỗi khi cập nhật yêu cầu hoàn tiền.', 'Lỗi');
                        }
                    },
                    complete: function() {
                        isSubmitting = false;
                        $button.prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endsection
