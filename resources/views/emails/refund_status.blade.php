<!DOCTYPE html>
<html>
<head>
    <title>Cập nhật trạng thái hoàn tiền</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #f8f9fa; padding: 10px; text-align: center; }
        .content { padding: 20px; }
        .footer { font-size: 12px; color: #777; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Cập nhật trạng thái hoàn tiền</h1>
        </div>
        <div class="content">
            <p>Kính gửi {{ $refund->user->fullname }},</p>
            <p>Yêu cầu hoàn tiền #{{ $refund->id }} của bạn đã được xử lý.</p>
            <p><strong>Đơn hàng:</strong> {{ $refund->order->code }}</p>
            <p><strong>Số tiền thanh toán ban đầu:</strong> {{ number_format($refund->order->total_amount, 2) }} VND</p>
            <p><strong>Số tiền hoàn:</strong> {{ number_format($refund->total_amount, 2) }} VND</p>
            <p><strong>Trạng thái:</strong> {{ $refund->status }}</p>
            @if ($refund->status == 'completed')
                <p><strong>Đã chuyển vào tài khoản:</strong> {{ $refund->bank_account }}</p>
                <p><strong>Ngân hàng:</strong> {{ $refund->bank_name }}</p>
                <p><strong>Chủ tài khoản:</strong> {{ $refund->user_bank_name }}</p>
            @endif
            @if ($refund->admin_reason)
                <p><strong>Lý do:</strong> {{ $refund->admin_reason }}</p>
            @endif
            <p>Vui lòng liên hệ chúng tôi nếu bạn có bất kỳ câu hỏi nào.</p>
        </div>
        <div class="footer">
            <p>Trân trọng,<br>Đội ngũ hỗ trợ</p>
        </div>
    </div>
</body>
</html>