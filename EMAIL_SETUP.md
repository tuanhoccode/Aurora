# Cấu hình Email cho Aurora E-commerce

## Tính năng Email đã thêm

✅ **Email xác nhận đơn hàng** - Gửi tự động khi đặt hàng thành công

## Cấu hình Email

### 1. Cấu hình SMTP (Khuyến nghị cho Production)

Thêm các biến môi trường vào file `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="Aurora E-commerce"
```

### 2. Cấu hình Gmail (Ví dụ)

1. Bật 2FA cho tài khoản Gmail
2. Tạo App Password:
   - Vào Google Account Settings
   - Security > 2-Step Verification > App passwords
   - Tạo password cho "Mail"
3. Sử dụng App Password thay vì mật khẩu thường

### 3. Cấu hình khác

#### Mailgun
```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=your-domain.com
MAILGUN_SECRET=your-mailgun-secret
```

#### SendGrid
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
```

## Test Email

### 1. Sử dụng Command
```bash
# Test email cơ bản
php artisan test:order-email {order_id}

# Test email với ảnh sản phẩm
php artisan test:order-email-image {order_id} {email?}
```

### 2. Kiểm tra Log
Với cấu hình `log` driver, email sẽ được ghi vào:
```
storage/logs/laravel.log
```

### 3. Preview Email
```bash
php artisan tinker
```
```php
$order = App\Models\Order::with(['items.product', 'user'])->first();
Mail::to('test@example.com')->send(new App\Mail\OrderConfirmationMail($order));
```

## Template Email

### Vị trí file
- **Mail Class**: `app/Mail/OrderConfirmationMail.php`
- **Template**: `resources/views/client/emails/order-confirmation.blade.php`

### Ảnh sản phẩm trong Email
✅ Hiển thị ảnh thumbnail của sản phẩm
✅ Fallback icon khi không có ảnh
✅ Responsive design cho email
✅ Tối ưu kích thước ảnh (80x80px)

### Nội dung Email
✅ Thông tin đơn hàng chi tiết
✅ Danh sách sản phẩm với ảnh
✅ Thông tin giao hàng
✅ Tổng tiền và giảm giá
✅ Link xem chi tiết đơn hàng
✅ Thông tin liên hệ hỗ trợ

## Tích hợp vào Hệ thống

Email sẽ được gửi tự động trong các trường hợp:

1. **Đặt hàng COD**: Gửi ngay khi tạo đơn hàng
2. **Thanh toán VNPay thành công**: Gửi khi callback/return thành công
3. **Thanh toán VNPay qua callback**: Gửi khi callback thành công

## Troubleshooting

### Email không gửi được
1. Kiểm tra cấu hình SMTP
2. Kiểm tra log lỗi: `storage/logs/laravel.log`
3. Test với command: `php artisan test:order-email 1`

### Email bị spam
1. Cấu hình SPF, DKIM, DMARC
2. Sử dụng domain email thay vì Gmail
3. Kiểm tra reputation domain

### Template không hiển thị đúng
1. Clear cache: `php artisan view:clear`
2. Kiểm tra relationship trong Order model
3. Kiểm tra route `client.orders.show`

## Cấu hình Queue (Tùy chọn)

Để gửi email bất đồng bộ, cấu hình queue:

```env
QUEUE_CONNECTION=database
```

Tạo migration:
```bash
php artisan queue:table
php artisan migrate
```

Chạy queue worker:
```bash
php artisan queue:work
```

## Bảo mật

1. Không commit file `.env` chứa thông tin email
2. Sử dụng App Password thay vì mật khẩu thường
3. Giới hạn rate limit cho email
4. Validate email trước khi gửi 