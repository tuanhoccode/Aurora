-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 24, 2025 at 02:33 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `aurora`
--

-- --------------------------------------------------------

--
-- Table structure for table `attributes`
--

CREATE TABLE `attributes` (
  `id` int NOT NULL COMMENT 'ID thuộc tính',
  `name` varchar(100) NOT NULL COMMENT 'Tên thuộc tính',
  `is_variant` tinyint(1) DEFAULT '0' COMMENT '1 nếu là thuộc tính của biến thể, 0 nếu là thông số kĩ thuật',
  `is_active` tinyint(1) DEFAULT '1' COMMENT '1 nếu thuộc tính đang hiển thị, 0 nếu ẩn',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian tạo thuộc tính',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Thời gian cập nhật thuộc tính',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Thời gian xóa mềm'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `attributes`
--

INSERT INTO `attributes` (`id`, `name`, `is_variant`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Size', 1, 1, '2025-05-31 09:50:27', '2025-06-10 02:10:45', NULL),
(2, 'Màu sắc', 1, 1, '2025-05-31 03:34:55', '2025-06-10 21:24:06', NULL),
(3, 'Chất lượng', 1, 1, '2025-06-16 20:22:40', '2025-06-16 20:22:40', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `attribute_values`
--

CREATE TABLE `attribute_values` (
  `id` int NOT NULL COMMENT 'ID giá trị thuộc tính',
  `attribute_id` int NOT NULL COMMENT 'ID thuộc tính liên kết',
  `value` varchar(100) NOT NULL COMMENT 'Giá trị thuộc tính',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 nếu giá trị thuộc tính đang hiển thị, 0 nếu ẩn',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian tạo giá trị thuộc tính',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Thời gian cập nhật giá trị thuộc tính',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Thời gian xóa mềm'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `attribute_values`
--

INSERT INTO `attribute_values` (`id`, `attribute_id`, `value`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'M', 1, '2025-06-10 02:26:20', '2025-06-10 02:27:25', NULL),
(2, 2, 'Đỏ', 1, '2025-06-10 21:24:14', '2025-06-10 21:24:14', NULL),
(4, 1, 'S', 1, '2025-06-11 19:51:21', '2025-06-11 19:51:21', NULL),
(5, 1, 'L', 1, '2025-06-11 20:09:16', '2025-06-11 20:09:16', NULL),
(6, 2, 'Xanh', 1, '2025-06-11 20:10:30', '2025-06-11 20:10:30', NULL),
(7, 3, 'Vải', 1, '2025-06-16 20:22:50', '2025-06-16 20:22:50', NULL),
(8, 3, 'Giấy', 1, '2025-06-16 20:22:56', '2025-06-16 20:22:56', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `attribute_value_product`
--

CREATE TABLE `attribute_value_product` (
  `product_id` int NOT NULL COMMENT 'ID sản phẩm',
  `attribute_value_id` int NOT NULL COMMENT 'ID giá trị thuộc tính',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attribute_value_product`
--

INSERT INTO `attribute_value_product` (`product_id`, `attribute_value_id`, `created_at`, `updated_at`) VALUES
(5, 4, '2025-06-12 04:42:20', '2025-06-12 04:42:20'),
(5, 6, '2025-06-12 04:42:20', '2025-06-12 04:42:20'),
(6, 1, '2025-06-16 20:31:13', '2025-06-16 20:31:13'),
(6, 6, '2025-06-13 07:48:27', '2025-06-13 07:48:27'),
(6, 8, '2025-06-16 20:31:13', '2025-06-16 20:31:13');

-- --------------------------------------------------------

--
-- Table structure for table `attribute_value_product_variant`
--

CREATE TABLE `attribute_value_product_variant` (
  `id` int UNSIGNED NOT NULL,
  `product_variant_id` int NOT NULL,
  `attribute_value_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `attribute_value_product_variant`
--

INSERT INTO `attribute_value_product_variant` (`id`, `product_variant_id`, `attribute_value_id`, `created_at`, `updated_at`) VALUES
(1, 16, 1, '2025-06-17 04:02:46', '2025-06-17 04:02:46'),
(2, 16, 2, '2025-06-17 04:02:46', '2025-06-17 04:02:46'),
(3, 16, 7, '2025-06-17 04:02:46', '2025-06-17 04:02:46'),
(4, 17, 5, '2025-06-17 04:05:37', '2025-06-17 04:05:37'),
(5, 17, 6, '2025-06-17 04:05:37', '2025-06-17 04:05:37'),
(6, 17, 8, '2025-06-17 04:05:37', '2025-06-17 04:05:37'),
(7, 15, 4, '2025-06-17 04:06:51', '2025-06-17 04:06:51'),
(8, 15, 6, '2025-06-17 04:06:51', '2025-06-17 04:06:51'),
(9, 15, 8, '2025-06-17 04:06:51', '2025-06-17 04:06:51'),
(10, 18, 1, '2025-06-17 07:20:18', '2025-06-17 07:20:18'),
(11, 19, 4, '2025-06-17 07:20:18', '2025-06-17 07:20:18'),
(24, 27, 2, '2025-06-17 20:37:59', '2025-06-17 20:37:59'),
(25, 28, 2, '2025-06-17 20:37:59', '2025-06-17 20:37:59'),
(26, 30, 2, '2025-06-17 20:54:23', '2025-06-17 20:54:23'),
(27, 31, 1, '2025-06-18 09:00:32', '2025-06-18 09:00:32'),
(28, 31, 6, '2025-06-18 09:00:32', '2025-06-18 09:00:32'),
(29, 32, 1, '2025-06-18 09:53:07', '2025-06-18 09:53:07'),
(30, 32, 6, '2025-06-18 09:53:07', '2025-06-18 09:53:07'),
(31, 32, 7, '2025-06-18 09:53:07', '2025-06-18 09:53:07');

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` int NOT NULL COMMENT 'ID thương hiệu',
  `name` varchar(100) NOT NULL COMMENT 'Tên thương hiệu (duy nhất)',
  `logo` varchar(255) DEFAULT NULL COMMENT 'Logo thương hiệu',
  `is_active` tinyint(1) DEFAULT '1' COMMENT '1 nếu thương hiệu đang hiển thị, 0 nếu ẩn',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian tạo thương hiệu',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Thời gian cập nhật thương hiệu',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Thời gian xóa mềm'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `name`, `logo`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Sản phẩm 13', 'san-pham-13_1748347419.png', 1, '2025-05-27 00:47:05', '2025-06-10 02:37:42', '2025-06-10 02:37:42'),
(2, 'Nguyễn Đình Tuân', 'nguyen-dinh-tuan_1748348035.png', 1, '2025-05-27 05:12:20', '2025-05-27 05:14:06', '2025-05-27 05:14:06'),
(3, 'Nike', 'brands/9EJHGmK2XVeJO6d59XFmJK6QkF5rAoUILtSiuhFp.png', 1, '2025-05-27 05:25:13', '2025-06-10 02:37:10', NULL),
(4, 'Nguyễn Đình Tuâ', 'brands/NbFwCrNcoE0AsfUGslhpeTX42r2jio1HJYNVxMWs.png', 1, '2025-05-27 05:28:14', '2025-06-10 02:37:42', '2025-06-10 02:37:42'),
(5, 'hhhh', 'brands/eTg0M0Mnnw0Xgqdg0OG6S6UpkdkElXf5dX2kmgRg.png', 1, '2025-05-27 05:33:38', '2025-06-10 02:37:42', '2025-06-10 02:37:42'),
(6, 'Adidas', 'brands/0d00qtJrcEXze1h41UFup7PSlTgxenBOvZ1JhEgc.png', 1, '2025-06-10 02:37:30', '2025-06-10 02:37:30', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int NOT NULL COMMENT 'ID giỏ hàng',
  `user_id` int NOT NULL COMMENT 'ID người dùng liên kết',
  `product_id` int NOT NULL COMMENT 'ID sản phẩm',
  `product_variant_id` int DEFAULT NULL COMMENT 'ID biến thể sản phẩm',
  `quantity` int NOT NULL COMMENT 'Số lượng sản phẩm trong giỏ hàng',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian tạo giỏ hàng',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Thời gian cập nhật giỏ hàng'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL COMMENT 'ID danh mục',
  `parent_id` int DEFAULT NULL COMMENT 'ID danh mục cha',
  `icon` varchar(255) DEFAULT NULL COMMENT 'Icon của danh mục',
  `name` varchar(100) NOT NULL COMMENT 'Tên danh mục (duy nhất)',
  `is_active` tinyint(1) DEFAULT '1' COMMENT '1 là danh mục đang hiển thị, 0 nếu ẩn',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian tạo danh mục',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Thời gian cập nhật danh mục',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Thời gian xóa mềm'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `parent_id`, `icon`, `name`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, NULL, 'categories/PSzCWqDxEB3zKKSECqqE6oy9vybvHVGSx9FUMHNW.png', 'Áo', 1, '2025-06-10 00:19:27', '2025-06-14 04:26:10', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `category_product`
--

CREATE TABLE `category_product` (
  `category_id` int NOT NULL COMMENT 'ID danh mục liên kết',
  `product_id` int NOT NULL COMMENT 'ID sản phẩm liên kết'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `category_product`
--

INSERT INTO `category_product` (`category_id`, `product_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 15),
(1, 16),
(1, 17);

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int NOT NULL COMMENT 'ID bình luận',
  `product_id` int NOT NULL COMMENT 'ID sản phẩm được bình luận',
  `user_id` int NOT NULL COMMENT 'ID người dùng bình luận',
  `content` text NOT NULL COMMENT 'Nội dung bình luận',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian tạo bình luận',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Thời gian cập nhật bình luận'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` int NOT NULL COMMENT 'ID mã giảm giá',
  `code` varchar(50) NOT NULL COMMENT 'Mã giảm giá (duy nhất)',
  `title` varchar(50) DEFAULT NULL COMMENT 'Tiêu đề của mã giảm giá',
  `description` varchar(255) DEFAULT NULL COMMENT 'Mô tả chi tiết của mã giảm giá',
  `discount_type` enum('fix_amount','percent') NOT NULL DEFAULT 'percent' COMMENT 'Kiểu giảm giá',
  `discount_value` decimal(10,2) NOT NULL COMMENT 'Giá trị giảm giá áp dụng',
  `usage_limit` int DEFAULT NULL COMMENT 'Số lần sử dụng tối đa',
  `usage_count` int DEFAULT '0' COMMENT 'Số lần mã giảm giá đã được sử dụng',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 nếu mã đang kích hoạt, 0 nếu không',
  `is_notified` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 nếu mã đã được thông báo, 0 nếu chưa',
  `start_date` timestamp NULL DEFAULT NULL COMMENT 'Ngày bắt đầu áp dụng mã giảm giá',
  `end_date` timestamp NULL DEFAULT NULL COMMENT 'Ngày kết thúc áp dụng mã giảm giá',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian tạo mã giảm giá',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Thời gian cập nhật mã giảm giá',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Thời gian xóa mềm'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_logs`
--

CREATE TABLE `login_logs` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int NOT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `logged_in_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_current` tinyint(1) NOT NULL DEFAULT '0',
  `notified` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `login_logs`
--

INSERT INTO `login_logs` (`id`, `user_id`, `session_id`, `ip_address`, `user_agent`, `logged_in_at`, `is_current`, `notified`, `created_at`, `updated_at`) VALUES
(93, 83, 'hol135AqMWGTQNENMvO2wJ2FkTMSOqKxV09znppY', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36 Avast/131.0.0.0', '2025-06-17 02:28:14', 0, 0, '2025-06-17 02:28:14', '2025-06-17 02:29:53'),
(94, 83, '9zafgG32QVtlrEUQI60bpNbOqhnSUnLkzyzl6o3y', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36 Avast/131.0.0.0', '2025-06-17 02:29:17', 0, 0, '2025-06-17 02:29:17', '2025-06-17 02:29:53'),
(95, 83, '7IdVu3f2R40HRkvy467gSpcxvaZCRUc3vE6Ve5Jy', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-17 02:31:38', 0, 0, '2025-06-17 02:31:38', '2025-06-17 02:56:18'),
(96, 83, 'OyK66VTsBqMzDe9uFeGUXSzBw1aJQrn7ND2GfHRV', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-17 02:32:00', 0, 0, '2025-06-17 02:32:00', '2025-06-17 02:56:18'),
(97, 83, 'SR91Lw4KLtawzSBqa6uvNmPBfsBvh7XwbTy2dSxb', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-17 02:51:10', 0, 0, '2025-06-17 02:51:10', '2025-06-17 02:56:18'),
(98, 83, 'RqlR5Cigyp8mQEWw8RPv3V45pnj9eOlXwzdE5iD3', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36 Avast/131.0.0.0', '2025-06-17 02:52:07', 0, 0, '2025-06-17 02:52:07', '2025-06-17 02:56:18'),
(99, 83, 'ycf6WpncIbh5ipDBPXgC3Wtwa8C05N7G7ZVqnPRu', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-17 02:54:40', 0, 0, '2025-06-17 02:54:40', '2025-06-17 02:56:18'),
(100, 83, 'vHMZcDtSxYbbMm8DSHATzyZtZ4C2ycHjdGmVP7JH', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36 Avast/131.0.0.0', '2025-06-17 02:56:05', 0, 0, '2025-06-17 02:56:05', '2025-06-17 02:57:56'),
(101, 83, 'urFHHYUtGwMPF6UqU2E3Oyd4x3GNvGd8AVEtheAv', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-17 02:57:15', 1, 0, '2025-06-17 02:57:15', '2025-06-17 02:57:15'),
(102, 85, 'noOddQR45BWX4WjJMcbNQQiCrPfnpg9pTnpHbkbk', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-18 07:17:37', 1, 0, '2025-06-18 07:17:37', '2025-06-18 07:17:37'),
(103, 12, 'dPqd6bPV8OkjAuVeNNwUtZigUhPpGnD9naD0YJLf', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-18 08:14:34', 1, 0, '2025-06-18 08:14:34', '2025-06-18 08:14:34'),
(104, 13, '14DHkMCiN0kvbspC5oUmtxAeaid9YHvDyj1q49qd', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-18 08:30:02', 0, 0, '2025-06-18 08:30:02', '2025-06-18 08:30:55'),
(105, 13, 'wZWV2CkgYDBK16zp4jUN8veE4ENzsmPJ0DokoZ3q', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-18 08:32:29', 0, 0, '2025-06-18 08:32:29', '2025-06-18 10:04:10'),
(106, 13, 'ke4cYnVnA5AnFDjFPWQtlIXujgSz5nqDNoxO9UxD', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-18 08:47:28', 0, 0, '2025-06-18 08:47:28', '2025-06-18 10:04:10'),
(107, 13, 'oQPUFWoaxy1QSj2eMRib3ACsCylhO2hq6v83vbJp', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-18 09:57:32', 0, 0, '2025-06-18 09:57:32', '2025-06-18 10:04:10'),
(108, 14, 'pEaaeJZAgORfssotohUPIXK0tF1bInnWLjEM6LiT', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-18 09:57:54', 1, 0, '2025-06-18 09:57:54', '2025-06-18 09:57:54'),
(109, 14, 'Grl7VS8CAhmpsrDRAeoyPen9NQlHn2gy5IlsKeeE', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36 Avast/131.0.0.0', '2025-06-18 09:59:48', 1, 0, '2025-06-18 09:59:48', '2025-06-18 09:59:48'),
(110, 13, '14aqvZUxqak1s5GWM3Vx02UFklIya1Lhc9ER9NKy', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36 Avast/131.0.0.0', '2025-06-18 10:00:17', 0, 0, '2025-06-18 10:00:17', '2025-06-18 10:04:10'),
(111, 13, 'tnJZi9eAd5FvoFZM66srNOh3LF6OIlS0hyf40nHi', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-18 10:03:37', 0, 0, '2025-06-18 10:03:37', '2025-06-18 10:04:10'),
(112, 13, '9PfprvGR9J7le8XYkbVTXjfgpHwdRnNLE4v2a3SE', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36 Avast/131.0.0.0', '2025-06-18 10:03:58', 1, 0, '2025-06-18 10:03:58', '2025-06-18 10:03:58'),
(113, 15, 'UoleOO57P75hSHjn7AdOQp4jRXU51kcJaTBI1OVH', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-18 18:34:57', 1, 0, '2025-06-18 18:34:57', '2025-06-18 18:34:57'),
(114, 15, 'XjK6C6Hl80JQS9rCHNSyGeLVZmyixXpa8oxTaoby', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36 Avast/131.0.0.0', '2025-06-18 18:35:38', 1, 0, '2025-06-18 18:35:38', '2025-06-18 18:35:38'),
(115, 16, 'h4J9I8mZKmiVjwuGKvrxPTaOodNhVxJhnovJNgbm', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-18 18:37:10', 0, 0, '2025-06-18 18:37:10', '2025-06-18 18:39:02'),
(116, 16, 'JPrCExmJAG4i2WHwzbxu8C8pADQwJeElXT9yx1jv', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36 Avast/131.0.0.0', '2025-06-18 18:38:37', 1, 0, '2025-06-18 18:38:37', '2025-06-18 18:38:37'),
(117, 16, 'S6JhCu5Trd2dd540hppGHKo7P5T3lFyDHmPfCnbL', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-18 18:40:05', 1, 0, '2025-06-18 18:40:05', '2025-06-18 18:40:05'),
(118, 17, 'SnnRRL0YBcsbcI2SjEMrBeF94UrLG3nG2qg1jNdI', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-24 03:12:45', 1, 0, '2025-06-24 03:12:45', '2025-06-24 03:12:45'),
(119, 19, 'P72zc09moyfkGRmD8PcGxCxd2Q9uJvMRDz6RvNvj', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-06-24 03:15:54', 1, 0, '2025-06-24 03:15:54', '2025-06-24 03:15:54');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(10, '0001_01_01_000001_create_cache_table', 2),
(11, '0001_01_01_000002_create_jobs_table', 2);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint NOT NULL COMMENT 'ID đơn hàng',
  `code` varchar(50) NOT NULL COMMENT 'Mã đơn hàng (duy nhất)',
  `user_id` int DEFAULT NULL COMMENT 'ID người dùng đặt hàng',
  `payment_id` int DEFAULT NULL COMMENT 'ID phương thức thanh toán',
  `phone_number` varchar(20) NOT NULL COMMENT 'Số điện thoại liên lạc của người mua',
  `email` varchar(100) DEFAULT NULL COMMENT 'Email liên lạc của người mua',
  `fullname` varchar(100) NOT NULL COMMENT 'Họ và tên của người nhận',
  `address` varchar(255) NOT NULL COMMENT 'Địa chỉ giao hàng',
  `note` varchar(255) DEFAULT NULL COMMENT 'Ghi chú của khách hàng',
  `total_amount` decimal(12,2) NOT NULL COMMENT 'Tổng tiền thanh toán cho đơn hàng',
  `is_paid` tinyint(1) DEFAULT '0' COMMENT '1 nếu đã thanh toán, 0 nếu chưa thanh toán',
  `is_refunded` tinyint(1) DEFAULT '0' COMMENT '1 nếu là đơn hoàn, 0 nếu không',
  `coupon_id` int DEFAULT NULL COMMENT 'ID mã giảm giá',
  `is_refunded_canceled` tinyint(1) DEFAULT '0' COMMENT '1 nếu hủy hàng, 0 nếu không hủy hàng',
  `check_refunded_canceled` tinyint(1) DEFAULT '0' COMMENT '1 nếu đã chuyển tiền, 0 nếu chưa chuyển tiền',
  `img_refunded_money` varchar(255) DEFAULT NULL COMMENT 'Ảnh minh chứng khi đã trả tiền đơn hoàn',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian tạo đơn hàng',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Thời gian cập nhật đơn hàng'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int NOT NULL COMMENT 'ID chi tiết đơn hàng',
  `order_id` bigint NOT NULL COMMENT 'ID đơn hàng liên kết',
  `product_id` int DEFAULT NULL COMMENT 'ID sản phẩm',
  `product_variant_id` int DEFAULT NULL COMMENT 'ID biến thể sản phẩm',
  `name` varchar(255) NOT NULL COMMENT 'Tên sản phẩm',
  `price` decimal(11,2) NOT NULL COMMENT 'Giá sản phẩm',
  `old_price` decimal(11,2) DEFAULT NULL COMMENT 'Giá cũ sản phẩm',
  `old_price_variant` decimal(11,2) DEFAULT NULL COMMENT 'Giá cũ sản phẩm biến thể',
  `quantity` int NOT NULL COMMENT 'Số lượng sản phẩm trong đơn hàng',
  `name_variant` varchar(255) DEFAULT NULL COMMENT 'Tên biến thể của sản phẩm',
  `attributes_variant` json DEFAULT NULL COMMENT 'Thông tin thuộc tính biến thể (dạng JSON)',
  `price_variant` decimal(11,2) DEFAULT NULL COMMENT 'Giá của biến thể sản phẩm',
  `quantity_variant` int DEFAULT NULL COMMENT 'Số lượng của biến thể sản phẩm',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian tạo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_order_status`
--

CREATE TABLE `order_order_status` (
  `order_id` bigint NOT NULL COMMENT 'ID đơn hàng',
  `order_status_id` int NOT NULL COMMENT 'ID trạng thái đơn hàng',
  `modified_by` int NOT NULL COMMENT 'ID người xử lý đơn hàng',
  `note` varchar(255) DEFAULT NULL COMMENT 'Ghi chú của người xử lý',
  `employee_evidence` json DEFAULT NULL COMMENT 'Minh chứng của nhân viên',
  `customer_confirmation` tinyint(1) DEFAULT NULL COMMENT 'null nếu nhân viên mới gửi minh chứng, 1 nếu xác nhận đã nhận hàng, 0 nếu xác nhận không nhận hàng',
  `is_current` tinyint(1) DEFAULT '1' COMMENT '1 nếu là trạng thái hiện tại, 0 nếu là trạng thái cũ',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian tạo trạng thái đơn hàng',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Thời gian cập nhật trạng thái đơn hàng'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_statuses`
--

CREATE TABLE `order_statuses` (
  `id` int NOT NULL COMMENT 'ID trạng thái đơn hàng',
  `name` varchar(50) NOT NULL COMMENT 'Tên trạng thái'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int NOT NULL COMMENT 'ID phương thức thanh toán',
  `name` varchar(50) NOT NULL COMMENT 'Tên phương thức thanh toán',
  `logo` varchar(255) DEFAULT NULL COMMENT 'Logo phương thức thanh toán',
  `is_active` tinyint(1) DEFAULT '1' COMMENT '1 nếu đang kích hoạt, 0 nếu không',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian tạo phương thức thanh toán',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Thời gian cập nhật phương thức thanh toán'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL COMMENT 'ID sản phẩm',
  `brand_id` int NOT NULL COMMENT 'ID thương hiệu',
  `name` varchar(250) NOT NULL COMMENT 'Tên sản phẩm',
  `slug` varchar(255) NOT NULL COMMENT 'chuỗi ký tự',
  `views` int DEFAULT '0' COMMENT 'Số lượt xem sản phẩm',
  `short_description` varchar(255) DEFAULT NULL COMMENT 'Mô tả ngắn của sản phẩm',
  `description` text COMMENT 'Mô tả chi tiết sản phẩm',
  `thumbnail` varchar(255) NOT NULL COMMENT 'Ảnh đại diện của sản phẩm',
  `type` varchar(255) NOT NULL,
  `sku` varchar(50) DEFAULT NULL COMMENT 'Mã SKU của sản phẩm',
  `price` decimal(11,2) DEFAULT NULL COMMENT 'Giá gốc sản phẩm',
  `sale_price` decimal(11,2) DEFAULT NULL COMMENT 'Giá giảm khuyến mãi',
  `stock` int DEFAULT NULL COMMENT 'số lượng',
  `is_sale` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 nếu sản phẩm đang sale, 0 nếu không sale',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 nếu sản phẩm đang hiển thị, 0 nếu ẩn',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian tạo sản phẩm',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Thời gian cập nhật sản phẩm',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Thời gian xóa mềm'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `brand_id`, `name`, `slug`, `views`, `short_description`, `description`, `thumbnail`, `type`, `sku`, `price`, `sale_price`, `stock`, `is_sale`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 3, 'Nike', 'nike', 2, '22', '<p>222</p>', 'products/PRD-ggg-1749541909.png', 'simple', 'PRD-222', 200000.00, NULL, 5, 2, 0, '2025-06-04 15:21:41', '2025-06-18 08:50:29', '2025-06-18 08:50:29'),
(2, 6, 'Quần Adidas', 'quan-adidas', 0, 'S', '<p>s</p>', 'products/PRD-ao-1749541104.png', 'variant', 'PRD-SP001', 200000.00, NULL, 0, 0, 0, '2025-06-10 00:38:24', '2025-06-18 08:51:04', NULL),
(3, 3, 'Áo Nike', '', 0, 'Áo', '<p>Nike</p>', 'products/product-ao-nike-1749545671.png', 'simple', 'PRD-SP006', 200000.00, 180000.00, 0, 1, 1, '2025-06-10 01:48:29', '2025-06-18 08:51:10', '2025-06-18 08:51:10'),
(5, 3, 'Áo', '', 0, 'Nike', '<p>Áo</p>', 'products/product-ao-1-1749614948.png', 'variant', 'PRD-SP005', 200000.00, NULL, 0, 0, 1, '2025-06-10 21:09:08', '2025-06-17 06:33:48', NULL),
(6, 6, 'Áo Adidas', 'ao-adidas', 0, 'Adidas', '<p>Áo Adidas</p>', 'products/product-ao-adidas-1749724701.png', 'variant', 'PRD-SP003', 100000.00, NULL, 0, 0, 1, '2025-06-12 03:38:22', '2025-06-17 03:33:59', NULL),
(7, 3, 'Áo Nike Fake', 'ao-nike-fake', 0, 'Nike', '<p>Áo</p>', 'products/product-ao-nike-1749902300.png', 'variant', 'PRD-SP001', 200000.00, NULL, 0, 0, 1, '2025-06-14 04:58:20', '2025-06-17 03:32:20', NULL),
(8, 3, 'Áo Gucci', 'ao-gucci', 0, 'Gucci', '<p>Áo Gucci</p>', 'products/product-ao-gucci-1750158139.png', 'variant', 'PRD-SP001', 200000.00, NULL, NULL, 0, 1, '2025-06-17 04:02:20', '2025-06-17 21:03:33', NULL),
(9, 6, 'Quần Gucci', 'quan-gucci', 0, 'Gucci', '<p>Quần Gucci</p>', 'products/product-quan-gucci-1750158925.png', 'simple', 'PRD-6QXS5FOX', 200000.00, 190000.00, 5, 1, 1, '2025-06-17 04:15:25', '2025-06-17 04:15:25', NULL),
(10, 3, 'Mũ Nice', 'mu-nice', 0, 'Nike', '<p>Mũ Nike</p>', 'products/product-mu-nice-1750158999.png', 'simple', 'PRD-QXMOB', 100000.00, 90000.00, 5, 1, 1, '2025-06-17 04:16:39', '2025-06-17 21:03:40', NULL),
(11, 3, 'Mũ Nike', 'mu-nike', 0, 'Mũ Nik', '<p>Mũ Nike</p>', 'products/product-mu-nike-1750216911.png', 'variant', 'PRD-9FLPN', 200000.00, NULL, NULL, 0, 1, '2025-06-17 20:21:51', '2025-06-17 20:21:51', NULL),
(12, 3, 'Áo mono', 'ao-mono', 0, 'quá đẹp', '<p>12345</p>', 'products/product-ao-mono-1750262386.png', 'variant', 'PRD-C3QNH', 4.00, NULL, NULL, 0, 1, '2025-06-18 08:59:46', '2025-06-18 08:59:46', NULL),
(13, 3, 'Áo polo', 'ao-polo', 0, 'mê phê tê', '<p>mê phê tê</p>', 'products/product-ao-polo-1750263095.jfif', 'simple', 'PRD-NRSVY', 5000.00, NULL, 10, 0, 1, '2025-06-18 09:11:35', '2025-06-18 09:11:50', NULL),
(14, 3, 'Áo polo12', 'ao-polo12', 0, '123456789', '<p>123456789</p>', 'products/product-ao-polo11-1750264125.jfif', 'digital', 'PRD-CS0BS', 5000.00, NULL, 11, 0, 1, '2025-06-18 09:28:45', '2025-06-18 18:43:44', NULL),
(15, 6, 'Áo polo1111', 'ao-polo1111', 0, '12345678987654', '<p>123456776543</p>', 'products/product-ao-polo1111-1750264191.jpg', 'simple', 'PRD-TEPLX', 19000.00, NULL, 19, 0, 1, '2025-06-18 09:29:51', '2025-06-18 09:29:51', NULL),
(16, 6, 'quần tây1', 'quan-tay1', 0, '76543234', '<p>1234565432</p>', 'products/product-quan-tay-1750265550.jpg', 'variant', 'PRD-AGQBB', 30000.00, NULL, NULL, 0, 1, '2025-06-18 09:52:30', '2025-06-18 18:50:31', NULL),
(17, 3, 'Áo meno', 'ao-meno', 0, '123456787654', '<p>12345wqwe</p>', 'products/product-ao-meno-1750297312.jpg', 'simple', 'PRD-SQ63T', 20.00, NULL, 23, 0, 1, '2025-06-18 18:41:52', '2025-06-18 18:41:52', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_galleries`
--

CREATE TABLE `product_galleries` (
  `id` int NOT NULL COMMENT 'ID hình ảnh sản phẩm',
  `product_id` int NOT NULL COMMENT 'ID sản phẩm liên kết',
  `image` varchar(255) NOT NULL COMMENT 'URL hình ảnh sản phẩm',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian tạo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_stocks`
--

CREATE TABLE `product_stocks` (
  `id` int NOT NULL COMMENT 'ID',
  `product_id` int NOT NULL COMMENT 'ID sản phẩm',
  `product_variant_id` int DEFAULT NULL COMMENT 'ID sản phẩm biến thể',
  `stock` int NOT NULL COMMENT 'Số lượng tồn kho',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian tạo tồn kho sản phẩm',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Thời gian cập nhật tồn kho sản phẩm'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `id` int NOT NULL COMMENT 'ID biến thể sản phẩm',
  `product_id` int NOT NULL COMMENT 'ID sản phẩm liên kết',
  `sku` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Mã SKU riêng của biến thể',
  `stock` int NOT NULL DEFAULT '0' COMMENT 'Số lượng tồn kho',
  `regular_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Giá gốc',
  `sale_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Giá khuyến mãi',
  `img` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ảnh riêng của biến thể',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian tạo',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Thời gian cập nhật'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_variants`
--

INSERT INTO `product_variants` (`id`, `product_id`, `sku`, `stock`, `regular_price`, `sale_price`, `img`, `created_at`, `updated_at`) VALUES
(1, 5, 'Á-S-XA', 5, 200000.00, 100000.00, 'products/variants/02gylVu5kKAHN0Gm97O9nmzZttDY6oKm81fclVCm.png', '2025-06-11 19:40:53', '2025-06-12 04:42:20'),
(13, 5, 'Á-L-ĐỎ', 3, 200000.00, 150000.00, 'products/variants/26DWh7fmgGz1VyvaAwt6wUr1H3EAOxKj3XPC9lSp.png', '2025-06-11 20:09:44', '2025-06-11 20:09:44'),
(15, 6, 'SH-S-XA', 5, 200000.00, 100000.00, 'products/variants/zBljQ5CjVhWFM9MHXTXr9sSMCTlFQJXdXEim75Sg.png', '2025-06-12 04:16:01', '2025-06-17 04:06:51'),
(16, 8, 'SH-M-DO', 5, 200000.00, 180000.00, 'products/variants/HreKROfDvrTo1Mh0huZtvYzZXCqA7exXEFbTpPou.png', '2025-06-17 04:02:46', '2025-06-17 04:02:46'),
(17, 8, 'SH-L-XA', 4, 190000.00, 180000.00, 'products/variants/5IYwXndzOmW1K3uA7WxaRMX6dBvXpCEJppGEAGn5.png', '2025-06-17 04:05:37', '2025-06-17 04:05:37'),
(18, 8, 'SP001', 2, 200000.00, 100000.00, NULL, '2025-06-17 07:20:18', '2025-06-17 07:20:18'),
(19, 8, 'SP002', 2, 200000.00, 100000.00, NULL, '2025-06-17 07:20:18', '2025-06-17 07:20:18'),
(27, 11, 'HA-M-DO', 2, 200000.00, 100000.00, 'products/variants/1ef2M9Ka4pQ2iErD7QPLdyzmevMBVnykgZHoCJDW.png', '2025-06-17 20:37:59', '2025-06-17 20:37:59'),
(28, 11, 'HA-S-DO', 2, 200000.00, 100000.00, 'products/variants/DOwn8V73oaHE53CLUluo7c24mPyYEMGUYznIQ8CK.png', '2025-06-17 20:37:59', '2025-06-17 20:37:59'),
(30, 10, 'HA-L-DO', 2, 200000.00, 100000.00, 'products/variants/4ZPDTspUG9ySfvrtSeQIfzrSWexC94NIMJEWMlBv.png', '2025-06-17 20:54:23', '2025-06-17 20:54:23'),
(31, 12, 'AO-M-XA', 8, 100000.00, 90000.00, 'products/variants/lShmfpoNTTAR9JUVUiWWjA6LYItsemtMvsZSAH5d.jpg', '2025-06-18 09:00:32', '2025-06-18 09:03:06'),
(32, 16, 'PA-M-XA', 70, 38000.00, 30000.00, 'products/variants/JaySxTtdKgaSjXw3VgVjSD1yp9EDv3TydRhfDwrE.jpg', '2025-06-18 09:53:07', '2025-06-18 09:53:07');

-- --------------------------------------------------------

--
-- Table structure for table `refunds`
--

CREATE TABLE `refunds` (
  `id` int NOT NULL COMMENT 'ID',
  `order_id` bigint NOT NULL COMMENT 'ID đơn hàng',
  `user_id` int NOT NULL COMMENT 'ID người dùng',
  `total_amount` decimal(12,2) NOT NULL COMMENT 'Tổng tiền',
  `bank_account` varchar(50) DEFAULT NULL COMMENT 'Tài khoản ngân hàng',
  `user_bank_name` varchar(100) DEFAULT NULL COMMENT 'Tên tài khoản ngân hàng',
  `phone_number` varchar(20) NOT NULL COMMENT 'Số điện thoại',
  `bank_name` varchar(100) DEFAULT NULL COMMENT 'Tên ngân hàng thụ hưởng',
  `reason` text NOT NULL COMMENT 'Lý do của khách hàng',
  `fail_reason` text COMMENT 'Lý do lỗi',
  `img_fail_or_completed` text COMMENT 'Ảnh khi đơn hàng bị lỗi',
  `reason_image` text COMMENT 'Ảnh hoặc video của sản phẩm lỗi',
  `admin_reason` text COMMENT 'Lý do của admin khi từ chối',
  `is_send_money` tinyint(1) DEFAULT '0' COMMENT '1 nếu đã chuyển tiền, 0 nếu chưa chuyển tiền',
  `status` enum('pending','receiving','completed','rejected','failed','canceled') NOT NULL COMMENT 'Trạng thái hoàn hàng',
  `bank_account_status` enum('unverified','sent','verified') NOT NULL COMMENT 'Trạng thái tài khoản ngân hàng',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian tạo',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Thời gian cập nhật'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `refund_items`
--

CREATE TABLE `refund_items` (
  `id` int NOT NULL COMMENT 'ID chi tiết đơn hàng',
  `refund_id` int NOT NULL COMMENT 'ID hoàn hàng',
  `product_id` int DEFAULT NULL COMMENT 'ID sản phẩm',
  `variant_id` int DEFAULT NULL COMMENT 'ID biến thể sản phẩm',
  `name` varchar(255) NOT NULL COMMENT 'Tên sản phẩm',
  `name_variant` varchar(255) DEFAULT NULL COMMENT 'Tên biến thể của sản phẩm',
  `quantity` int NOT NULL COMMENT 'Số lượng sản phẩm trong đơn hàng',
  `price` decimal(11,2) NOT NULL COMMENT 'Giá sản phẩm',
  `price_variant` decimal(11,2) DEFAULT NULL COMMENT 'Giá của biến thể sản phẩm',
  `quantity_variant` int DEFAULT NULL COMMENT 'Số lượng của biến thể sản phẩm',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian tạo',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Thời gian cập nhật'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int NOT NULL COMMENT 'ID đánh giá',
  `product_id` int NOT NULL COMMENT 'ID sản phẩm được đánh giá',
  `order_id` bigint NOT NULL COMMENT 'ID đơn hàng liên quan',
  `user_id` int NOT NULL COMMENT 'ID người dùng đánh giá',
  `rating` int NOT NULL COMMENT 'Số sao đánh giá (1-5)',
  `review_text` text COMMENT 'Nội dung đánh giá',
  `reason` varchar(255) DEFAULT NULL COMMENT 'Lý do không duyệt đánh giá',
  `is_active` tinyint(1) DEFAULT '0' COMMENT '1: trạng thái duyệt, 0: trạng thái không duyệt',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian tạo đánh giá',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Thời gian cập nhật đánh giá'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('23JAn2HTaFGLB01qM7UMnF2QX7u8sC5IsCciIpwS', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWjhrUnNOR2pNOXJNaUlTSkxISXlZWlpTYmw4V1hpTU5YYWpLVVVycCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDI6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9wcm9kdWN0LWRldGFpbHMuaHRtbCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1750774495),
('fvcOCrTLv2ynO9m8WuABm0z5EuYOEFMQFu3E9END', 20, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoib1l3dTNjYU8ybGxpUEV2dGkyaTZmemNhVDlLV2xHT2RhUHRjZmFXeiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hc3NldHMvanMvY2hhcnQuanMiO31zOjM6InVybCI7YTowOnt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MjA7fQ==', 1750760475),
('JBEbp4YGv4L6gFMJpGESRzAxzeUybyYA1OUp0w8m', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRTRiMzBIZ3gzbFBqN3BXOVBzNlMyb1Q1alR4bTIwQ0pjakpRdXBpSCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1750759752),
('P72zc09moyfkGRmD8PcGxCxd2Q9uJvMRDz6RvNvj', 19, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoibUdiM3RzNkpBb29iQVJwREdMZGoxcXZlbzI0MlhVT2oyNlZMUVVnNCI7czo1OiJzdGF0ZSI7czo0MDoiYnBBNFRLRWR0eE9wM0tBb2VnaVFmNUtyNmh0N2N3T2lSZDhLMDBpbSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzQ0OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXV0aC9nb29nbGUvY2FsbGJhY2s/YXV0aHVzZXI9MCZjb2RlPTQlMkYwQVVKUi14N2VNNXVOOHVQUXVJdUNOekhrV3lXNVA0VG9JREI0N0ItNlp3Q2hjWHpEOUdNNTByd1hJbnBkbjdvSzhNNm82USZwcm9tcHQ9bm9uZSZzY29wZT1lbWFpbCUyMHByb2ZpbGUlMjBodHRwcyUzQSUyRiUyRnd3dy5nb29nbGVhcGlzLmNvbSUyRmF1dGglMkZ1c2VyaW5mby5lbWFpbCUyMGh0dHBzJTNBJTJGJTJGd3d3Lmdvb2dsZWFwaXMuY29tJTJGYXV0aCUyRnVzZXJpbmZvLnByb2ZpbGUlMjBvcGVuaWQmc3RhdGU9YnBBNFRLRWR0eE9wM0tBb2VnaVFmNUtyNmh0N2N3T2lSZDhLMDBpbSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjE6e2k6MDtzOjc6InN1Y2Nlc3MiO31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE5O3M6Nzoic3VjY2VzcyI7czo0MToixJDEg25nIG5o4bqtcCBi4bqxbmcgR29vZ2xlIHRow6BuaCBjw7RuZyEiO30=', 1750760154),
('uGZl16u3g8rys2jFU9vBQ259EJ9feosrcb4mVzEa', 18, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoibGdIaHZxMjFpeUtYYU9IdVh0RWJJRm1DaVpHT0NtNlpMSmRGb3Y4ViI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9yZWdpc3RlciI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjE6e2k6MDtzOjc6InN1Y2Nlc3MiO31zOjM6Im5ldyI7YTowOnt9fXM6NToic3RhdGUiO3M6NDA6IktISkZNZ1VVVzgwWkVpbkdXZjJ2T1VVSUQxR1hNOGNaT2tpZWNyeGgiO3M6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE4O3M6Nzoic3VjY2VzcyI7czo4MDoixJDEg25nIGvDvSB0aMOgbmggY8O0bmchIFZ1aSBsw7JuZyBraeG7g20gdHJhIGVtYWlsIMSR4buDIHjDoWMgbWluaCB0w6BpIGtob+G6o24iO30=', 1750760152);

-- --------------------------------------------------------

--
-- Table structure for table `temp_product_variants`
--

CREATE TABLE `temp_product_variants` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `sku` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `stock_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'out_of_stock',
  `regular_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL COMMENT 'ID người dùng',
  `phone_number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Số điện thoại (duy nhất)',
  `email` varchar(100) DEFAULT NULL COMMENT 'Email (duy nhất)',
  `password` varchar(255) NOT NULL COMMENT 'Mật khẩu đã mã hóa',
  `fullname` varchar(100) DEFAULT NULL COMMENT 'Họ và tên',
  `avatar` varchar(255) DEFAULT NULL COMMENT 'Ảnh đại diện',
  `gender` enum('male','female','other') DEFAULT NULL COMMENT 'Giới tính',
  `birthday` date DEFAULT NULL COMMENT 'Ngày sinh',
  `role` enum('customer','employee','admin') NOT NULL DEFAULT 'customer' COMMENT 'Vai trò người dùng',
  `status` enum('inactive','active') NOT NULL DEFAULT 'active' COMMENT 'Trạng thái tài khoản',
  `bank_name` varchar(100) DEFAULT NULL COMMENT 'Tên ngân hàng',
  `user_bank_name` varchar(100) DEFAULT NULL COMMENT 'Tên người dùng ngân hàng',
  `bank_account` varchar(50) DEFAULT NULL COMMENT 'Số tài khoản ngân hàng',
  `reason_lock` varchar(100) DEFAULT NULL COMMENT 'Lý do khóa tài khoản',
  `is_change_password` tinyint(1) DEFAULT '0' COMMENT '1 nếu đã thay đổi mật khẩu, 0 nếu chưa',
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `google_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian tạo',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Thời gian cập nhật'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `phone_number`, `email`, `password`, `fullname`, `avatar`, `gender`, `birthday`, `role`, `status`, `bank_name`, `user_bank_name`, `bank_account`, `reason_lock`, `is_change_password`, `remember_token`, `email_verified_at`, `google_id`, `created_at`, `updated_at`) VALUES
(1, '0378328023', 'tuan@gmail.com', 'tuan@gmail.com', NULL, NULL, NULL, NULL, 'admin', 'active', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, '2025-05-31 10:04:41', '2025-05-31 10:04:41'),
(2, '0320288517', 'gmiller@example.com', '$2y$12$2eoOd5Yi7oWfxWFSLkmgBOh9Utj23bBnxNLDM6Zgfqm.WTTqL8.Mq', 'Lacy Langworth', 'default.png', 'other', '1978-06-23', 'admin', 'active', NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, '2025-06-03 06:28:03', '2025-06-03 06:28:03'),
(3, '0394523259', 'bart.goyette@example.com', '$2y$12$2eoOd5Yi7oWfxWFSLkmgBOh9Utj23bBnxNLDM6Zgfqm.WTTqL8.Mq', 'Melany Ernser', 'default.png', 'female', '2021-07-27', 'employee', 'active', NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, '2025-06-03 06:28:03', '2025-06-03 06:28:03'),
(4, '0363249944', 'kunde.lesley@example.com', '$2y$12$2eoOd5Yi7oWfxWFSLkmgBOh9Utj23bBnxNLDM6Zgfqm.WTTqL8.Mq', 'Isobel Stokes', 'default.png', 'other', '1985-08-19', 'admin', 'active', NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, '2025-06-03 06:28:03', '2025-06-03 06:28:03'),
(5, '0372276621', 'billy96@example.com', '$2y$12$2eoOd5Yi7oWfxWFSLkmgBOh9Utj23bBnxNLDM6Zgfqm.WTTqL8.Mq', 'Prof. Jeff Tremblay III', 'default.png', 'other', '2005-08-22', 'customer', 'active', NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, '2025-06-03 06:28:03', '2025-06-03 06:28:03'),
(6, '0307447882', 'lorine29@example.org', '$2y$12$2eoOd5Yi7oWfxWFSLkmgBOh9Utj23bBnxNLDM6Zgfqm.WTTqL8.Mq', 'Scot Cole', 'default.png', 'other', '1970-03-03', 'admin', 'active', NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, '2025-06-03 06:28:03', '2025-06-03 06:28:03'),
(7, '0308346184', 'lue.roberts@example.com', '$2y$12$2eoOd5Yi7oWfxWFSLkmgBOh9Utj23bBnxNLDM6Zgfqm.WTTqL8.Mq', 'Bailee Crooks', 'default.png', 'other', '1984-01-29', 'customer', 'active', NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, '2025-06-03 06:28:03', '2025-06-03 06:28:03'),
(8, '0315264757', 'vella43@example.net', '$2y$12$2eoOd5Yi7oWfxWFSLkmgBOh9Utj23bBnxNLDM6Zgfqm.WTTqL8.Mq', 'Christopher Smitham', 'default.png', 'female', '2010-05-11', 'customer', 'active', NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, '2025-06-03 06:28:03', '2025-06-03 06:28:03'),
(9, '0374451533', 'tdenesik@example.com', '$2y$12$2eoOd5Yi7oWfxWFSLkmgBOh9Utj23bBnxNLDM6Zgfqm.WTTqL8.Mq', 'Prof. Adonis Cartwright DVM', 'default.png', 'male', '1986-01-18', 'admin', 'active', NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, '2025-06-03 06:28:03', '2025-06-03 06:28:03'),
(10, '0373543773', 'connor55@example.com', '$2y$12$2eoOd5Yi7oWfxWFSLkmgBOh9Utj23bBnxNLDM6Zgfqm.WTTqL8.Mq', 'Ms. Agnes Robel', 'default.png', 'female', '1998-11-30', 'admin', 'active', NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, '2025-06-03 06:28:03', '2025-06-03 13:32:24'),
(11, '0319385331', 'rachael37@example.net', '$2y$12$2eoOd5Yi7oWfxWFSLkmgBOh9Utj23bBnxNLDM6Zgfqm.WTTqL8.Mq', 'Hollis Moore', 'default.png', 'other', '2006-02-25', 'customer', 'active', NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, '2025-06-03 06:28:03', '2025-06-03 06:28:03'),
(14, NULL, 'thanhngo7112005@gmail.com', '$2y$12$kMPg5D3VWbRSi5Icjbe30etd/pYMqN.jopHZaMVExXxz8UO1f0fGu', 'THÀNH NGÔ', NULL, NULL, NULL, 'customer', 'active', NULL, NULL, NULL, NULL, 0, NULL, '2025-06-18 09:59:48', '101273778499572064319', '2025-06-18 09:57:54', '2025-06-18 09:59:48'),
(15, NULL, 'thanhntph49043@gmail.com', '$2y$12$M/5dl2Wo5J3B3.tjDmbHKe0YOENiThfCb04bbVV2MYmAhGLu.MMby', 'Ngô Trung Thành PH 4 9 0 4 3', NULL, NULL, NULL, 'customer', 'active', NULL, NULL, NULL, NULL, 0, NULL, '2025-06-18 18:35:38', '102068162118645501876', '2025-06-18 18:34:57', '2025-06-18 18:35:38'),
(16, NULL, 'ngothanh678112005@gmail.com', '$2y$12$pcQhnRlP0kytKBaWNtUfb.6Z5hbgb83i7dsBzttUlhyk4EzZAx1i6', 't11', NULL, NULL, NULL, 'admin', 'active', NULL, NULL, NULL, NULL, 0, '5xnKUNPQ3TAb16rKuGUToDeMOZiP7dTG4qHrxmkb5uZ72b10SOskLT7b8NI6', '2025-06-18 18:36:50', NULL, '2025-06-18 18:36:30', '2025-06-19 01:40:30'),
(20, NULL, 'doanngocquang2305@gmail.com', '$2y$12$yJlFEVaPlE.TRG9E9voxqOBUmGd6DFVjl9oA59Vjey2hMB0ZY9Dge', 'QuangAdmin', NULL, NULL, NULL, 'admin', 'active', NULL, NULL, NULL, NULL, 0, NULL, '2025-06-24 03:18:21', NULL, '2025-06-24 03:17:50', '2025-06-24 10:18:46');

-- --------------------------------------------------------

--
-- Table structure for table `user_addresses`
--

CREATE TABLE `user_addresses` (
  `id` int NOT NULL COMMENT 'ID',
  `user_id` int NOT NULL COMMENT 'ID người dùng liên kết',
  `address` text NOT NULL COMMENT 'Địa chỉ đầy đủ của người dùng',
  `phone_number` varchar(20) NOT NULL COMMENT 'Số điện thoại của người dùng',
  `fullname` varchar(100) NOT NULL COMMENT 'Họ và tên của người dùng',
  `is_default` tinyint(1) DEFAULT '0' COMMENT '1 nếu là địa chỉ mặc định, 0 nếu không',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời gian tạo',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Thời gian cập nhật'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attributes`
--
ALTER TABLE `attributes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_name` (`name`);

--
-- Indexes for table `attribute_values`
--
ALTER TABLE `attribute_values`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_attribute_id` (`attribute_id`);

--
-- Indexes for table `attribute_value_product`
--
ALTER TABLE `attribute_value_product`
  ADD PRIMARY KEY (`product_id`,`attribute_value_id`),
  ADD KEY `idx_product_id` (`product_id`),
  ADD KEY `idx_attribute_value_id` (`attribute_value_id`);

--
-- Indexes for table `attribute_value_product_variant`
--
ALTER TABLE `attribute_value_product_variant`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `variant_value_unique` (`product_variant_id`,`attribute_value_id`),
  ADD KEY `fk_attribute_value` (`attribute_value_id`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `idx_name` (`name`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_product_id` (`product_id`),
  ADD KEY `product_variant_id` (`product_variant_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `idx_parent_id` (`parent_id`);

--
-- Indexes for table `category_product`
--
ALTER TABLE `category_product`
  ADD PRIMARY KEY (`category_id`,`product_id`),
  ADD KEY `idx_category_id` (`category_id`),
  ADD KEY `idx_product_id` (`product_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_product_id` (`product_id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `idx_code` (`code`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_login_logs_user` (`user_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_code` (`code`),
  ADD KEY `payment_id` (`payment_id`),
  ADD KEY `coupon_id` (`coupon_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `product_variant_id` (`product_variant_id`);

--
-- Indexes for table `order_order_status`
--
ALTER TABLE `order_order_status`
  ADD PRIMARY KEY (`order_id`,`order_status_id`,`created_at`),
  ADD KEY `idx_order_id` (`order_id`),
  ADD KEY `order_status_id` (`order_status_id`),
  ADD KEY `modified_by` (`modified_by`);

--
-- Indexes for table `order_statuses`
--
ALTER TABLE `order_statuses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_name` (`name`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_name` (`name`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_brand_id` (`brand_id`),
  ADD KEY `idx_sku` (`sku`);

--
-- Indexes for table `product_galleries`
--
ALTER TABLE `product_galleries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_product_id` (`product_id`);

--
-- Indexes for table `product_stocks`
--
ALTER TABLE `product_stocks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_product_id` (`product_id`),
  ADD KEY `idx_product_variant_id` (`product_variant_id`);

--
-- Indexes for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_variants_sku_unique` (`sku`),
  ADD KEY `idx_product_id` (`product_id`);

--
-- Indexes for table `refunds`
--
ALTER TABLE `refunds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order_id` (`order_id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `refund_items`
--
ALTER TABLE `refund_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_refund_id` (`refund_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_product_id` (`product_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_order_id` (`order_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `temp_product_variants`
--
ALTER TABLE `temp_product_variants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phone_number` (`phone_number`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_phone_number` (`phone_number`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attributes`
--
ALTER TABLE `attributes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID thuộc tính', AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `attribute_values`
--
ALTER TABLE `attribute_values`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID giá trị thuộc tính', AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `attribute_value_product_variant`
--
ALTER TABLE `attribute_value_product_variant`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID thương hiệu', AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID giỏ hàng';

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID danh mục', AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID bình luận';

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID mã giảm giá';

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `login_logs`
--
ALTER TABLE `login_logs`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT COMMENT 'ID đơn hàng';

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID chi tiết đơn hàng';

--
-- AUTO_INCREMENT for table `order_statuses`
--
ALTER TABLE `order_statuses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID trạng thái đơn hàng';

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID phương thức thanh toán';

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID sản phẩm', AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `product_galleries`
--
ALTER TABLE `product_galleries`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID hình ảnh sản phẩm';

--
-- AUTO_INCREMENT for table `product_stocks`
--
ALTER TABLE `product_stocks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID biến thể sản phẩm', AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `refunds`
--
ALTER TABLE `refunds`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- AUTO_INCREMENT for table `refund_items`
--
ALTER TABLE `refund_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID chi tiết đơn hàng';

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID đánh giá';

--
-- AUTO_INCREMENT for table `temp_product_variants`
--
ALTER TABLE `temp_product_variants`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID người dùng', AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `user_addresses`
--
ALTER TABLE `user_addresses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attribute_values`
--
ALTER TABLE `attribute_values`
  ADD CONSTRAINT `attribute_values_ibfk_1` FOREIGN KEY (`attribute_id`) REFERENCES `attributes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `attribute_value_product`
--
ALTER TABLE `attribute_value_product`
  ADD CONSTRAINT `fk_attribute_value_product_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_attribute_value_product_value` FOREIGN KEY (`attribute_value_id`) REFERENCES `attribute_values` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `attribute_value_product_variant`
--
ALTER TABLE `attribute_value_product_variant`
  ADD CONSTRAINT `fk_attribute_value` FOREIGN KEY (`attribute_value_id`) REFERENCES `attribute_values` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_variant` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `category_product`
--
ALTER TABLE `category_product`
  ADD CONSTRAINT `category_product_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `category_product_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_order_status`
--
ALTER TABLE `order_order_status`
  ADD CONSTRAINT `order_order_status_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_order_status_ibfk_2` FOREIGN KEY (`order_status_id`) REFERENCES `order_statuses` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `order_order_status_ibfk_3` FOREIGN KEY (`modified_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `product_galleries`
--
ALTER TABLE `product_galleries`
  ADD CONSTRAINT `product_galleries_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `fk_product_variant_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `refunds`
--
ALTER TABLE `refunds`
  ADD CONSTRAINT `refunds_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `refunds_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD CONSTRAINT `user_addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
