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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Cập Nhật Trạng Thái Yêu Cầu Hoàn Tiền</h2>
        </div>
        <div class="content">
            <p>Kính gửi Quý khách,</p>
            <p>Yêu cầu hoàn tiền của bạn cho đơn hàng <strong>#{{ $order_code }}</strong> đã được cập nhật trạng thái:</p>
            <p><strong>Trạng Thái:</strong> {{ $status }}</p>
            @if ($admin_reason)
                <p><strong>Lý Do:</strong> {{ $admin_reason }}</p>
            @endif
            <p><strong>Mã Yêu Cầu:</strong> {{ $refund->id }}</p>
            <p><strong>Tổng Số Tiền:</strong> {{ number_format($refund->total_amount, 2) }} VNĐ</p>
            <p>Chi tiết sản phẩm hoàn tiền:</p>
            <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                <thead>
                    <tr style="background-color: #f2f2f2;">
                        <th style="border: 1px solid #ddd; padding: 8px;">Sản Phẩm</th>
                        <th style="border: 1px solid #ddd; padding: 8px;">Biến Thể</th>
                        <th style="border: 1px solid #ddd; padding: 8px;">Số Lượng</th>
                        <th style="border: 1px solid #ddd; padding: 8px;">Giá</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($refund->items as $item)
                        <tr>
                            <td style="border: 1px solid #ddd; padding: 8px;">{{ $item->name }}</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">{{ $item->name_variant ?? 'N/A' }}</td>
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
