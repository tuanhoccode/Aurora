<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Cập Nhật Trạng Thái Yêu Cầu Hoàn Tiền</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .header {
            background-color: #f8f8f8;
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        .content {
            padding: 20px;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #777;
            margin-top: 20px;
        }

        .image-container {
            margin-top: 20px;
        }

        .image-container img {
            max-width: 100%;
            height: auto;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>Cập Nhật Trạng Thái Yêu Cầu Hoàn Tiền</h2>
        </div>
        <div class="content">
            <p>Kính gửi Quý khách,</p>
            <p>Yêu cầu hoàn tiền của bạn cho đơn hàng <strong>#{{ $refund->order->code }}</strong> đã được cập nhật
                trạng thái:</p>
            <p><strong>Trạng Thái:</strong> {{ \App\Http\Controllers\RefundController::getReasonText($refund->status) }}
            </p>
            @if ($refund->admin_reason)
                <p><strong>Lý Do:</strong> {{ $refund->admin_reason }}</p>
            @endif
            <p><strong>Mã Yêu Cầu:</strong> {{ $refund->id }}</p>
            <p><strong>Tổng Số Tiền:</strong> {{ number_format($refund->total_amount, 2) }} VNĐ</p>
            @if ($refund->reason_image)
                <div class="image-container">
                    <p><strong>Hình Ảnh Minh Chứng (Người Dùng):</strong></p>
                    <img src="{{ Storage::url($refund->reason_image) }}" alt="Hình ảnh minh chứng người dùng">
                </div>
            @endif
            @if ($refund->status === 'completed' && $refund->admin_reason_image)
                <div class="image-container">
                    <p><strong>Hình Ảnh Minh Chứng (Admin):</strong></p>
                    <img src="{{ Storage::url($refund->admin_reason_image) }}" alt="Hình ảnh minh chứng admin">
                </div>
            @endif
            <p>Chi tiết sản phẩm hoàn tiền:</p>
            @php
                $hasVariant = $refund->items->contains(function ($it) {
                    return !empty($it->variant_id);
                });
            @endphp
            <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                <thead>
                    <tr style="background-color: #f2f2f2;">
                        <th style="border: 1px solid #ddd; padding: 8px;">Sản Phẩm</th>
                        @if($hasVariant)
                            <th style="border: 1px solid #ddd; padding: 8px;">Biến Thể</th>
                        @endif
                        <th style="border: 1px solid #ddd; padding: 8px;">Số Lượng</th>
                        <th style="border: 1px solid #ddd; padding: 8px;">Giá</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($refund->items as $item)
                        @php
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
                            $variantText = trim(($color ?? '') . (($color && $size) ? ' / ' : '') . ($size ?? ''));
                        @endphp
                        <tr>
                            <td style="border: 1px solid #ddd; padding: 8px;">{{ $item->name }}</td>
                            @if($hasVariant)
                                <td style="border: 1px solid #ddd; padding: 8px;">
                                    {{ $variantText !== '' ? $variantText : ($item->name_variant ?? 'N/A') }}</td>
                            @endif
                            <td style="border: 1px solid #ddd; padding: 8px;">{{ $item->quantity }}</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">{{ number_format($item->price, 2) }} VNĐ</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <p>Vui lòng liên hệ với chúng tôi nếu bạn có bất kỳ câu hỏi nào.</p>
            <p>Trân trọng,</p>
            <p>Đội ngũ hỗ trợ</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Công ty của bạn. Mọi quyền được bảo lưu.</p>
        </div>
    </div>
</body>

</html>