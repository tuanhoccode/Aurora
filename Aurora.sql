CREATE TABLE `users` (
  `id` INT PRIMARY KEY AUTO_INCREMENT COMMENT 'ID người dùng',
  `phone_number` VARCHAR(20) UNIQUE NOT NULL COMMENT 'Số điện thoại (duy nhất)',
  `email` VARCHAR(100) UNIQUE COMMENT 'Email (duy nhất)',
  `password` VARCHAR(255) NOT NULL COMMENT 'Mật khẩu đã mã hóa',
  `fullname` VARCHAR(100) COMMENT 'Họ và tên',
  `avatar` VARCHAR(255) COMMENT 'Ảnh đại diện',
  `gender` ENUM ('male', 'female', 'other') COMMENT 'Giới tính',
  `birthday` DATE COMMENT 'Ngày sinh',
  `role` ENUM ('customer', 'employee', 'admin') DEFAULT 'customer' COMMENT 'Vai trò người dùng',
  `status` ENUM ('inactive', 'active') DEFAULT 'active' COMMENT 'Trạng thái tài khoản',
  `bank_name` VARCHAR(255) COMMENT 'Tên ngân hàng',
  `user_bank_name` VARCHAR(255) COMMENT 'Tên người dùng ngân hàng',
  `bank_account` VARCHAR(255) COMMENT 'Số tài khoản ngân hàng',
  `reason_lock` VARCHAR(255) COMMENT 'Lý do khóa tài khoản',
  `is_change_password` TINYINT(1) COMMENT '1 Nếu đã thay đổi mật khẩu, 0 Nếu chưa thay đổi mật khẩu',
  `created_at` TIMESTAMP COMMENT 'Thời gian tạo',
  `updated_at` TIMESTAMP COMMENT 'Thời gian cập nhật'
);

CREATE TABLE `user_addresses` (
  `id` INT PRIMARY KEY AUTO_INCREMENT COMMENT 'ID',
  `user_id` INT NOT NULL COMMENT 'ID người dùng liên kết',
  `address` TEXT COMMENT 'Địa chỉ đầy đủ của người dùng',
  `phone_number` VARCHAR(100) COMMENT 'Số điện thoại của người dùng',
  `fullname` VARCHAR(100) COMMENT 'Họ và tên của người dùng',
  `id_default` TINYINT(1) COMMENT '1 nếu là địa chỉ mặc định, 0 nếu không',
  `created_at` TIMESTAMP COMMENT 'Thời gian tạo',
  `updated_at` TIMESTAMP COMMENT 'Thời gian cập nhật'
);

CREATE TABLE `categories` (
  `id` INT PRIMARY KEY AUTO_INCREMENT COMMENT 'ID danh mục',
  `parent_id` INT COMMENT 'ID danh mục cha',
  `icon` VARCHAR(255) COMMENT 'Icon của danh mục',
  `name` VARCHAR(100) UNIQUE NOT NULL COMMENT 'Tên danh mục (duy nhất)',
  `is_active` TINYINT(1) COMMENT '1 là danh mục đang hiển thị, 0 nếu ẩn',
  `created_at` TIMESTAMP COMMENT 'Thời gian tạo danh mục',
  `updated_at` TIMESTAMP COMMENT 'Thời gian cập nhật danh mục',
  `deleted_at` TIMESTAMP COMMENT 'Thời gian xóa mềm'
);

CREATE TABLE `brands` (
  `id` INT PRIMARY KEY AUTO_INCREMENT COMMENT 'ID thương hiệu',
  `name` VARCHAR(100) UNIQUE NOT NULL COMMENT 'Tên thương hiệu (duy nhất)',
  `logo` VARCHAR(255) COMMENT 'Logo thương hiệu',
  `is_active` TINYINT(1) COMMENT '1 nếu thương hiệu đang hiển thị, 0 nếu ẩn',
  `created_at` TIMESTAMP COMMENT 'Thời gian tạo thương hiệu',
  `updated_at` TIMESTAMP COMMENT 'Thời gian cập nhật thương hiệu',
  `deleted_at` TIMESTAMP COMMENT 'Thời gian xóa mềm'
);

CREATE TABLE `category_product` (
  `category_id` INT NOT NULL COMMENT 'ID danh mục liên kết',
  `product_id` INT NOT NULL COMMENT 'ID sản phẩm liên kết'
);

CREATE TABLE `products` (
  `id` INT PRIMARY KEY AUTO_INCREMENT COMMENT 'ID sản phẩm',
  `brand_id` INT NOT NULL COMMENT 'ID thương hiệu',
  `name` VARCHAR(250) NOT NULL COMMENT 'Tên sản phẩm',
  `views` INT DEFAULT 0 COMMENT 'Số lượt xem sản phẩm',
  `short_description` VARCHAR(255) COMMENT 'Mô tả ngắn của sản phẩm',
  `description` TEXT COMMENT 'Mô tả chi tiết sản phẩm',
  `thumbnail` VARCHAR(255) NOT NULL COMMENT 'Ảnh đại diện của sản phẩm',
  `type` ENUM ('single', 'variant') COMMENT 'Loại sản phẩm',
  `sku` VARCHAR(255) COMMENT 'Mã SKU của sản phẩm',
  `price` DECIMAL(11,2) COMMENT 'Giá gốc sản phẩm',
  `sale_price` DECIMAL(11,2) COMMENT 'Giá giảm khuyến mãi',
  `is_sale` TINYINT(1) NOT NULL COMMENT '1 nếu sản phẩm đang sale, 0 nếu không sale',
  `is_active` TINYINT(1) NOT NULL COMMENT '1 nếu sản phẩm đang hiển thị, 0 nếu ẩn',
  `created_at` TIMESTAMP COMMENT 'Thời gian tạo sản phẩm',
  `updated_at` TIMESTAMP COMMENT 'Thời gian cập nhật sản phẩm',
  `deleted_at` TIMESTAMP COMMENT 'Thời gian xóa mềm'
);

CREATE TABLE `attributes` (
  `id` INT PRIMARY KEY AUTO_INCREMENT COMMENT 'ID thuộc tính',
  `name` VARCHAR(255) COMMENT 'Tên thuộc tính',
  `is_variant` TINYINT(1) COMMENT '1 nếu là thuộc tính của biến thể, 0 nếu là thông số kĩ thuật',
  `is_active` TINYINT(1) COMMENT '1 nếu thuộc tính đang hiển thị, 0 nếu ẩn',
  `created_at` TIMESTAMP COMMENT 'Thời gian tạo thuộc tính',
  `updated_at` TIMESTAMP COMMENT 'Thời gian cập nhật thuộc tính',
  `deleted_at` TIMESTAMP COMMENT 'Thời gian xóa mềm'
);

CREATE TABLE `attribute_values` (
  `id` BIGINT PRIMARY KEY AUTO_INCREMENT COMMENT 'ID giá trị thuộc tính',
  `attribute_id` INT NOT NULL COMMENT 'ID thuộc tính liên kết',
  `value` VARCHAR(255) NOT NULL COMMENT 'Giá trị thuộc tính',
  `is_active` TINYINT(1) NOT NULL COMMENT '1 nếu giá trị thuộc tính đang hiển thị, 0 nếu ẩn',
  `created_at` TIMESTAMP COMMENT 'Thời gian tạo giá trị thuộc tính',
  `updated_at` TIMESTAMP COMMENT 'Thời gian cập nhật giá trị thuộc tính',
  `deleted_at` TIMESTAMP COMMENT 'Thời gian xóa mềm'
);

CREATE TABLE `attribute_value_product` (
  `product_id` INT NOT NULL COMMENT 'ID sản phẩm liên kết',
  `attribute_value_id` BIGINT NOT NULL COMMENT 'ID giá trị thuộc tính liên kết'
);

CREATE TABLE `product_variants` (
  `id` INT PRIMARY KEY AUTO_INCREMENT COMMENT 'ID biến thể sản phẩm',
  `product_id` INT NOT NULL COMMENT 'ID sản phẩm chính liên kết',
  `sku` VARCHAR(255) COMMENT 'Mã SKU của biến thể',
  `price` DECIMAL(11,2) NOT NULL COMMENT 'Giá bán của biến thể',
  `sale_price` DECIMAL(11,2) COMMENT 'Giá khuyến mãi của biến thể',
  `thumbnail` VARCHAR(255) NOT NULL COMMENT 'Ảnh đại diện của biến thể',
  `is_active` TINYINT(1) NOT NULL COMMENT '1 nếu sản phẩm biến thể đang hiển thị, 0 nếu ẩn',
  `created_at` TIMESTAMP COMMENT 'Thời gian tạo biến thể',
  `updated_at` TIMESTAMP COMMENT 'Thời gian cập nhật biến thể'
);

CREATE TABLE `attribute_value_product_variant` (
  `product_variant_id` INT NOT NULL COMMENT 'ID biến thể sản phẩm',
  `attribute_value_id` BIGINT NOT NULL COMMENT 'ID giá trị thuộc tính'
);

CREATE TABLE `product_galleries` (
  `id` INT PRIMARY KEY AUTO_INCREMENT COMMENT 'ID hình ảnh sản phẩm',
  `product_id` INT NOT NULL COMMENT 'ID sản phẩm liên kết',
  `image` VARCHAR(255) NOT NULL COMMENT 'URL hình ảnh sản phẩm'
);

CREATE TABLE `coupons` (
  `id` INT PRIMARY KEY AUTO_INCREMENT COMMENT 'ID mã giảm giá',
  `code` VARCHAR(50) UNIQUE NOT NULL COMMENT 'Mã giảm giá (duy nhất)',
  `title` VARCHAR(50) COMMENT 'Tiêu đề của mã giảm giá',
  `description` VARCHAR(255) COMMENT 'Mô tả chi tiết của mã giảm giá',
  `discount_type` ENUM ('fix_amount', 'percent') DEFAULT 'percent' COMMENT 'Kiểu giảm giá (phần trăm hoặc số tiền cố định)',
  `discount_value` DECIMAL(10,2) COMMENT 'Giá trị giảm giá áp dụng',
  `usage_limit` INT COMMENT 'Số lần sử dụng tối đa',
  `usage_count` INT COMMENT 'Số lần mã giảm giá đã được sử dụng',
  `is_active` TINYINT(1) NOT NULL COMMENT '1 nếu mã đang kích hoạt, 0 nếu không hoạt động',
  `is_notified` TINYINT(1) NOT NULL COMMENT '1 nếu mã đã được thông báo, 0 nếu mã chưa được thông báo',
  `start_date` TIMESTAMP COMMENT 'Ngày bắt đầu áp dụng mã giảm giá',
  `end_date` TIMESTAMP COMMENT 'Ngày kết thúc áp dụng mã giảm giá',
  `created_at` TIMESTAMP COMMENT 'Thời gian tạo mã giảm giá',
  `updated_at` TIMESTAMP COMMENT 'Thời gian cập nhật mã giảm giá',
  `deleted_at` TIMESTAMP COMMENT 'Thời gian xóa mềm'
);

CREATE TABLE `orders` (
  `id` BIGINT PRIMARY KEY AUTO_INCREMENT COMMENT 'ID đơn hàng',
  `code` VARCHAR(50) UNIQUE NOT NULL COMMENT 'Mã đơn hàng (duy nhất)',
  `user_id` INT COMMENT 'ID người dùng đặt hàng',
  `payment_id` INT COMMENT 'ID phương thức thanh toán',
  `phone_number` VARCHAR(20) COMMENT 'Số điện thoại liên lạc của người mua',
  `email` VARCHAR(255) COMMENT 'Email liên lạc của người mua',
  `fullname` VARCHAR(255) COMMENT 'Họ và tên của người nhận',
  `address` VARCHAR(255) COMMENT 'Địa chỉ giao hàng',
  `note` VARCHAR(255) COMMENT 'Ghi chú của khách hàng',
  `total_amount` DECIMAL(12,2) NOT NULL COMMENT 'Tổng tiền thanh toán cho đơn hàng',
  `is_paid` TINYINT(1) COMMENT '1 nếu đã thanh toán, 0 nếu chưa thanh toán',
  `is_refund` TINYINT(1) COMMENT '1 nếu là đơn hoàn, 0 nếu không phải đơn hoàn',
  `coupon_id` INT COMMENT 'ID mã giảm giá',
  `coupon_code` VARCHAR(50) COMMENT 'Code mã giảm giá',
  `coupon_description` VARCHAR(50) COMMENT 'Mô tả giảm giá',
  `coupon_discount_type` VARCHAR(50) COMMENT 'Loại giảm giá',
  `coupon_discount_value` VARCHAR(50) COMMENT 'Giá trị giảm của mã giảm giá',
  `max_discount_value` DECIMAL(11,2) COMMENT 'Giá trị giảm tối đa',
  `is_refund_cancel` TINYINT(1) COMMENT '1 Nếu hủy hàng, 0 Nếu không hủy hàng',
  `check_refund_cancel` TINYINT(1) COMMENT '1 Nếu đã chuyển tiền, 0 Nếu chưa chuyển tiền',
  `img_send_refund_money` VARCHAR(255) COMMENT 'Ảnh minh chứng khi đã trả tiền đơn hoàn',
  `created_at` TIMESTAMP COMMENT 'Thời gian tạo đơn hàng',
  `updated_at` TIMESTAMP COMMENT 'Thời gian cập nhật đơn hàng'
);

CREATE TABLE `order_statuses` (
  `id` INT PRIMARY KEY AUTO_INCREMENT COMMENT 'ID trạng thái đơn hàng',
  `name` VARCHAR(255) COMMENT 'Tên trạng thái'
);

CREATE TABLE `order_order_status` (
  `order_id` INT NOT NULL COMMENT 'ID đơn hàng',
  `order_status_id` INT NOT NULL COMMENT 'ID trạng thái đơn hàng',
  `modified_by` INT NOT NULL COMMENT 'ID người xử lý đơn hàng',
  `note` VARCHAR(255) COMMENT 'Ghi chú của người xử lý',
  `employee_evidence` JSON COMMENT 'Minh chứng của nhân viên',
  `customer_confirmation` TINYINT(1) COMMENT 'null nếu nhân viên mới gửi minh chứng, 1 nếu bấm xác nhận đã nhận được hàng, 0 nếu bấm xác nhận không nhận được hàng',
  `is_current` TINYINT(1) COMMENT '1 nếu là trạng thái hiện tại, 0 nếu là trạng thái cũ',
  `created_at` TIMESTAMP COMMENT 'Thời gian tạo trạng thái đơn hàng',
  `updated_at` TIMESTAMP COMMENT 'Thời gian cập nhật trạng thái đơn hàng'
);

CREATE TABLE `payments` (
  `id` INT PRIMARY KEY AUTO_INCREMENT COMMENT 'ID phương thức thanh toán',
  `name` VARCHAR(255) NOT NULL COMMENT 'Tên phương thức thanh toán',
  `logo` VARCHAR(255) COMMENT 'Logo phương thức thanh toán',
  `is_active` TINYINT(1) COMMENT '1 nếu đang kích hoạt, 0 nếu không',
  `created_at` TIMESTAMP COMMENT 'Thời gian tạo phương thức thanh toán',
  `updated_at` TIMESTAMP COMMENT 'Thời gian cập nhật phương thức thanh toán'
);

CREATE TABLE `order_items` (
  `id` INT PRIMARY KEY AUTO_INCREMENT COMMENT 'ID chi tiết đơn hàng',
  `order_id` BIGINT NOT NULL COMMENT 'ID đơn hàng liên kết',
  `product_id` INT COMMENT 'ID sản phẩm',
  `product_variant_id` INT COMMENT 'ID biến thể sản phẩm',
  `name` VARCHAR(255) COMMENT 'Tên sản phẩm',
  `price` DECIMAL(11,2) COMMENT 'Giá sản phẩm',
  `old_price` DECIMAL(11,2) COMMENT 'Giá cũ sản phẩm',
  `old_price_variant` DECIMAL(11,2) COMMENT 'Giá cũ sản phẩm biến thể',
  `quantity` INT COMMENT 'Số lượng sản phẩm trong đơn hàng',
  `name_variant` VARCHAR(255) COMMENT 'Tên biến thể của sản phẩm',
  `attributes_variant` JSONB COMMENT 'Thông tin thuộc tính biến thể (dạng JSON)',
  `price_variant` DECIMAL(11,2) COMMENT 'Giá của biến thể sản phẩm',
  `quantity_variant` INT COMMENT 'Số lượng của biến thể sản phẩm'
);

CREATE TABLE `cart_items` (
  `id` INT PRIMARY KEY AUTO_INCREMENT COMMENT 'ID giỏ hàng',
  `user_id` INT NOT NULL COMMENT 'ID người dùng liên kết',
  `product_id` INT COMMENT 'ID sản phẩm',
  `product_variant_id` INT COMMENT 'ID biến thể sản phẩm',
  `quantity` INT NOT NULL COMMENT 'Số lượng sản phẩm trong giỏ hàng',
  `created_at` TIMESTAMP COMMENT 'Thời gian tạo giỏ hàng',
  `updated_at` TIMESTAMP COMMENT 'Thời gian cập nhật giỏ hàng'
);

CREATE TABLE `reviews` (
  `id` INT PRIMARY KEY AUTO_INCREMENT COMMENT 'ID đánh giá',
  `product_id` INT NOT NULL COMMENT 'ID sản phẩm được đánh giá',
  `order_id` BIGINT NOT NULL COMMENT 'ID đơn hàng liên quan',
  `user_id` INT COMMENT 'ID người dùng đánh giá',
  `rating` INT NOT NULL COMMENT 'Số sao đánh giá (1-5)',
  `review_text` TEXT COMMENT 'Nội dung đánh giá',
  `reason` VARCHAR(255) COMMENT 'Lý do không duyệt đánh giá',
  `is_active` TINYINT(1) COMMENT '1: là trạng thái duyệt, 0: là trạng thái không duyệt',
  `created_at` TIMESTAMP COMMENT 'Thời gian tạo đánh giá',
  `updated_at` TIMESTAMP COMMENT 'Thời gian cập nhật đánh giá'
);

CREATE TABLE `comments` (
  `id` INT PRIMARY KEY AUTO_INCREMENT COMMENT 'ID bình luận',
  `product_id` INT NOT NULL COMMENT 'ID sản phẩm được bình luận',
  `user_id` INT NOT NULL COMMENT 'ID người dùng bình luận',
  `content` TEXT NOT NULL COMMENT 'Nội dung bình luận',
  `created_at` TIMESTAMP COMMENT 'Thời gian tạo bình luận',
  `updated_at` TIMESTAMP COMMENT 'Thời gian cập nhật bình luận'
);

CREATE TABLE `refunds` (
  `id` INT PRIMARY KEY AUTO_INCREMENT COMMENT 'ID',
  `order_id` INT COMMENT 'ID đơn hàng',
  `user_id` INT COMMENT 'ID người dùng',
  `total_amount` DECIMAL(12,2) COMMENT 'Tổng tiền',
  `bank_account` VARCHAR(255) COMMENT 'Tài khoản ngân hàng',
  `user_bank_name` VARCHAR(255) COMMENT 'Tên tài khoản ngân hàng',
  `phone_number` VARCHAR(20) COMMENT 'Số điện thoại',
  `bank_name` VARCHAR(100) COMMENT 'Tên ngân hàng thụ hưởng',
  `reason` TEXT COMMENT 'Lý do của khách hàng',
  `fail_reason` TEXT COMMENT 'Lý do lỗi',
  `img_fail_or_completed` TEXT COMMENT 'Ảnh khi đơn hàng bị lỗi',
  `reason_image` TEXT COMMENT 'Ảnh hoặc video của sản phẩm lỗi',
  `admin_reason` TEXT COMMENT 'Lý do của admin khi từ chối',
  `is_send_money` TINYINT(1) COMMENT '1 Nếu đã chuyển tiền, 0 nếu chưa chuyển tiền',
  `status` ENUM(pending,receiving,completed,rejected,failed,cancel) COMMENT 'Trạng thái hoàn hàng',
  `bank_account_status` ENUM(unverified,sent,verified) COMMENT 'Trạng thái tài khoản ngân hàng',
  `created_at` TIMESTAMP COMMENT 'Thời gian tạo',
  `updated_at` TIMESTAMP COMMENT 'Thời gian cập nhật'
);

CREATE TABLE `refund_items` (
  `id` INT PRIMARY KEY AUTO_INCREMENT COMMENT 'ID chi tiết đơn hàng',
  `refund_id` INT NOT NULL COMMENT 'ID hoàn hàng',
  `product_id` INT COMMENT 'ID sản phẩm',
  `variant_id` INT COMMENT 'ID biến thể sản phẩm',
  `name` VARCHAR(255) COMMENT 'Tên sản phẩm',
  `name_variant` VARCHAR(255) COMMENT 'Tên biến thể của sản phẩm',
  `quantity` INT COMMENT 'Số lượng sản phẩm trong đơn hàng',
  `price` DECIMAL(11,2) COMMENT 'Giá sản phẩm',
  `price_variant` DECIMAL(11,2) COMMENT 'Giá của biến thể sản phẩm',
  `quantity_variant` INT COMMENT 'Số lượng của biến thể sản phẩm',
  `created_at` TIMESTAMP COMMENT 'Thời gian tạo',
  `updated_at` TIMESTAMP COMMENT 'Thời gian cập nhật'
);

CREATE TABLE `product_stocks` (
  `id` INT PRIMARY KEY AUTO_INCREMENT COMMENT 'ID',
  `product_id` INT COMMENT 'ID sản phẩm',
  `product_variant_id` INT COMMENT 'ID sản phẩm biến thể',
  `stock` INT COMMENT 'Số lượng tồn kho',
  `created_at` TIMESTAMP COMMENT 'Thời gian tạo tồn kho sản phẩm',
  `updated_at` TIMESTAMP COMMENT 'Thời gian cập nhật tồn kho sản phẩm'
);

ALTER TABLE `user_addresses` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `categories` ADD FOREIGN KEY (`id`) REFERENCES `categories` (`parent_id`);

ALTER TABLE `category_product` ADD FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

ALTER TABLE `category_product` ADD FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

ALTER TABLE `products` ADD FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`);

ALTER TABLE `attribute_value_product` ADD FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

ALTER TABLE `attribute_value_product` ADD FOREIGN KEY (`attribute_value_id`) REFERENCES `attribute_values` (`id`);

ALTER TABLE `attribute_values` ADD FOREIGN KEY (`attribute_id`) REFERENCES `attributes` (`id`);

ALTER TABLE `product_variants` ADD FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

ALTER TABLE `attribute_value_product_variant` ADD FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`);

ALTER TABLE `attribute_value_product_variant` ADD FOREIGN KEY (`attribute_value_id`) REFERENCES `attribute_values` (`id`);

ALTER TABLE `product_galleries` ADD FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

ALTER TABLE `orders` ADD FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`);

ALTER TABLE `orders` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `orders` ADD FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE SET NULL;

ALTER TABLE `order_items` ADD FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

ALTER TABLE `order_items` ADD FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

ALTER TABLE `order_items` ADD FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`);

ALTER TABLE `order_order_status` ADD FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

ALTER TABLE `order_order_status` ADD FOREIGN KEY (`order_status_id`) REFERENCES `order_statuses` (`id`);

ALTER TABLE `order_order_status` ADD FOREIGN KEY (`modified_by`) REFERENCES `users` (`id`);

ALTER TABLE `cart_items` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `cart_items` ADD FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

ALTER TABLE `cart_items` ADD FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`);

ALTER TABLE `reviews` ADD FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

ALTER TABLE `reviews` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `reviews` ADD FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

ALTER TABLE `comments` ADD FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

ALTER TABLE `comments` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `refunds` ADD FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

ALTER TABLE `refunds` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `refund_items` ADD FOREIGN KEY (`refund_id`) REFERENCES `refunds` (`id`);

ALTER TABLE `refund_items` ADD FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

ALTER TABLE `refund_items` ADD FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`);

ALTER TABLE `product_stocks` ADD FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

ALTER TABLE `product_stocks` ADD FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`);
