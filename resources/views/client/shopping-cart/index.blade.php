@extends('client.layouts.default')

@section('title', 'Giỏ hàng - Aurora')

@section('content')
    <style>
        /* Nâng cấp toàn diện giao diện giỏ hàng */
        .tp-cart-area {
            background: #fff;
            padding-top: 2rem;
            padding-bottom: 4rem;
            min-height: 100vh;
        }

        .breadcrumb__area {
            background-color: #fff;
            border-bottom: 1px solid #e9ecef;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        /* Bố cục Grid cho danh sách sản phẩm */
        .cart-items-grid {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        /* Cart Header for Desktop */
        .cart-header-row {
            display: grid;
            grid-template-columns: 90px 2.7fr 1fr 1.1fr 1.1fr 56px;
            gap: 1rem;
            font-size: 0.95rem;
            font-weight: 600;
            color: #64748b;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 0.8rem;
            margin-bottom: 1rem;
            background: #f8fafc;
            align-items: center;
            border-radius: 8px;
        }

        .cart-header-row__product {
            grid-column: 1 / 3;
        }

        /* Thẻ sản phẩm */
        .cart-item-card {
            display: grid;
            grid-template-columns: 90px 2.5fr 1fr 1.1fr 1.1fr 56px;
            align-items: center;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            padding: 1.2rem 1.5rem;
            transition: all 0.3s ease;
            gap: 1rem;
            min-height: 90px;
            position: relative;
            overflow: hidden;
            margin-bottom: 1rem;
        }

        .cart-item-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .cart-item-card:hover {
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }

        .cart-item-card:hover::before {
            opacity: 1;
        }

        .cart-item-card__image {
            width: 85px;
            height: 85px;
            object-fit: cover;
            border-radius: 8px;
            display: block;
            margin: 0 auto;
            transition: all 0.3s ease;
            position: relative;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }

        /* Cải thiện giao diện bảng */
        .table {
            border-collapse: separate;
            border-spacing: 0;
            background: #fff;
        }

        .table th {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem;
            font-size: 0.9rem;
        }

        .table td {
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .table tr:last-child td {
            border-bottom: none;
        }

        /* Cải thiện checkbox */
        .form-check-input {
            margin: 0;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }

        /* Cải thiện nút số lượng */
        .qty-btn-custom {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            background: none;
            padding: 0;
            margin: 0;
        }

        .qty-btn-custom:hover {
            color: #667eea;
        }

        .qty-btn-custom:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .qty-btn-custom i {
            font-size: 1rem;
            color: #64748b;
        }

        .qty-input {
            width: 60px;
            height: 32px;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            text-align: center;
            font-weight: 600;
            background: #fff;
            padding: 0;
            margin: 0;
        }

        .qty-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.2);
        }

        .qty-input:disabled {
            background: #f8fafc;
            color: #64748b;
        }

        /* Cải thiện nút xóa */
        .btn-link {
            padding: 0;
            margin: 0;
            line-height: 1;
        }

        .btn-link:hover {
            text-decoration: none;
        }

        .btn-link i {
            font-size: 1rem;
        }

        /* Cải thiện hiển thị giá */
        .price, .total {
            white-space: nowrap;
        }

        .price span, .total span {
            display: inline-block;
        }

        .price .text-muted, .total .text-muted {
            font-size: 0.85rem;
            opacity: 0.7;
        }

        .cart-item-card__image:hover {
            box-shadow: 0 8px 24px rgba(44, 62, 80, 0.25);
            transform: scale(1.08);
            cursor: pointer;
            border-color: #667eea;
        }

        .cart-item-card__info {
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-width: 0;
        }

        .qty-input {
            width: 48px !important;
            min-width: 40px !important;
            height: 36px !important;
            text-align: center !important;
            font-size: 16px !important;
            color: #111 !important;
            background-color: #fff !important;
            border: 1px solid #ccc !important;
            border-radius: 4px !important;
            outline: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            line-height: 1 !important;
        }

        .cart-item-card__info .name {
            font-size: 1rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.25rem;
            line-height: 1.4;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 220px;
            display: block;
            transition: color 0.2s ease;
        }

        .cart-item-card:hover .cart-item-card__info .name {
            color: #667eea;
        }

        .cart-item-card__info .meta-attributes {
            font-size: 0.9rem;
            color: #64748b;
            display: flex;
            flex-direction: column;
            gap: 2px;
            margin-top: 2px;
            min-width: 0;
            max-width: 220px;
        }

        .cart-item-card__info .meta-attributes .meta {
            display: flex;
            align-items: center;
            gap: 4px;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 200px;
        }

        .cart-item-card__info .meta-attributes strong {
            font-weight: 500;
            color: #2d3748;
            margin-right: 2px;
        }

        .cart-item-card__info .meta-attributes .color-dot {
            display: inline-block;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 1px solid #ccc;
            margin-right: 6px;
            vertical-align: middle;
        }

        .cart-item-card__price,
        .cart-item-card__total {
            font-size: 1rem;
            font-weight: 600;
            color: #1e293b;
            text-align: center;
            max-width: 90px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .cart-item-card__quantity {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 0;
        }

        .cart-item-card__quantity .cart-qty {
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border: 2px solid #e2e8f0;
            border-radius: 25px;
            height: 40px;
            min-width: 110px;
            max-width: 130px;
            padding: 0 6px;
            gap: 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .cart-item-card__quantity .cart-qty:hover {
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
        }

        .cart-qty .qty-btn {
            background: none;
            border: none;
            color: #23272f;
            font-size: 1.2rem;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            cursor: pointer;
            transition: background 0.18s, color 0.18s;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            outline: none;
        }

        .cart-qty .qty-btn:hover {
            background: #f8f9fa;
            color: #000;
        }

        .cart-qty .qty-input {
            border: none;
            background: #f8f9fa;
            width: 40px;
            height: 32px;
            text-align: center;
            font-weight: 600;
            font-size: 1rem;
            color: #23272f !important;
            border-radius: 4px;
            outline: none;
            box-shadow: none;
            margin: 0 4px;
            display: inline-block;
            vertical-align: middle;
            max-width: 48px;
            min-width: 0;
        }

        .cart-qty .qty-input:focus {
            background: #f4faff;
        }

        .cart-item-card__remove form {
            display: contents;
        }

        .cart-item-card__remove {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cart-item-card__remove .remove-btn {
            background: linear-gradient(135deg, #f7f8fa 0%, #e2e8f0 100%);
            border: 2px solid #e2e8f0;
            color: #b0b3b8;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .cart-item-card__remove .remove-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s ease;
        }

        .cart-item-card__remove .remove-btn:hover {
            background: linear-gradient(135deg, #ef5350 0%, #d32f2f 100%);
            border-color: #ef5350;
            color: #fff;
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(239, 83, 80, 0.3);
        }

        .cart-item-card__remove .remove-btn:hover::before {
            left: 100%;
        }

        /* Bảng sản phẩm */
        .cart-table-row td,
        .cart-table-row th {
            vertical-align: middle;
            border-bottom: 1.5px solid #e9ecef;
            padding: 14px 14px;
        }

        .cart-table-row:last-child td {
            border-bottom: none;
        }

        .cart-table-row .products {
            text-align: left;
            padding-left: 18px;
            min-width: 220px;
        }

        .cart-table-row .product-variant-meta {
            margin-top: 4px;
            font-size: 0.98rem;
            color: #7b7e85;
            display: flex;
            gap: 16px;
            align-items: center;
        }

        .cart-table-row .product-variant-meta .color-dot {
            display: inline-block;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 1px solid #ccc;
            margin-right: 6px;
            vertical-align: middle;
        }

        .cart-table-row .color,
        .cart-table-row .size,
        .cart-table-row .price,
        .cart-table-row .quantity,
        .cart-table-row .total {
            text-align: center;
        }

        .cart-table-row img {
            width: 56px;
            height: 56px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid #e9ecef;
            background: #fff;
        }

        .qty-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 1px solid #e0e0e0;
            background: #fff;
            color: #1677ff;
            font-size: 1.2rem;
            font-weight: bold;
            transition: background 0.18s, color 0.18s;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .qty-btn:hover {
            background: #e3f0fc;
            color: #0056d6;
        }

        .qty-input {
            width: 38px;
            height: 32px;
            text-align: center;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            margin: 0 4px;
            font-size: 1.08rem;
            padding: 0 2px;
            line-height: 32px;
        }

        .tp-product-quantity,
        .cart-qty {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.2rem;
        }

        .cart-table-row .color .d-inline-block {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 1px solid #ccc;
            margin-right: 6px;
            vertical-align: middle;
        }

        .cart-table-row .products a {
            font-size: 1.1rem;
            line-height: 1.3;
            font-weight: 600;
            color: #23272f;
        }

        .cart-table-row .products .text-muted {
            font-size: 0.97rem;
        }

        .cart-table-row .size,
        .cart-table-row .color {
            font-size: 1rem;
        }

        .cart-table-row .price,
        .cart-table-row .total {
            font-size: 1.08rem;
            font-weight: 600;
            color: #1a202c;
        }

        @media (max-width: 600px) {

            .cart-header-row,
            .cart-item-card {
                grid-template-columns: 1fr;
                display: block;
            }

            .cart-item-card {
                padding: 1rem;
            }

            .cart-item-card__quantity .cart-qty {
                min-width: 80px;
                max-width: 100%;
                height: 34px;
            }

            .cart-qty .qty-input {
                width: 28px;
                height: 28px;
                font-size: 1rem;
            }

            .cart-qty .qty-btn {
                width: 28px;
                height: 28px;
                font-size: 1.05rem;
            }
        }

        .cart-summary-title,
        .cart-summary__item,
        .cart-summary__total,
        .checkout-btn {
            font-family: 'Segoe UI', Arial, sans-serif !important;
        }

        .cart-qty,
        .tp-product-quantity {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .qty-btn {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            border: 1.5px solid #e0e0e0;
            background: #fff;
            color: #1677ff;
            font-size: 1.2rem;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            margin: 0;
            transition: background 0.18s, color 0.18s, border 0.18s;
        }

        .qty-btn:hover {
            background: #e3f0fc;
            color: #0056d6;
            border-color: #1677ff;
        }

        .qty-input {
            width: 44px;
            height: 34px;
            text-align: center;
            border: 1.5px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1.08rem;
            margin: 0 2px;
            padding: 0 2px;
            line-height: 34px;
            outline: none;
            transition: border 0.18s;
            background: #fff;
        }

        .qty-input:focus {
            border-color: #1677ff;
        }

        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            opacity: 1;
        }

        body,
        .tp-cart-area {
            background: #fff !important;
        }

        .cart-summary-box {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
            border: 1px solid #e2e8f0;
            padding: 2rem;
            min-width: 320px;
            max-width: 420px;
            margin-top: 0;
            margin-bottom: 1.5rem;
            font-family: 'Segoe UI', Arial, sans-serif;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .cart-summary-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        }

        .cart-summary-box:hover {
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }

        .cart-summary-box h2 {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 1.25rem;
            font-weight: 800;
            color: #1a202c;
            margin-bottom: 1.5rem;
            margin-top: 0;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            text-align: left;
            display: inline-block;
            position: relative;
            padding-bottom: 0.5rem;
        }

        .cart-summary-box h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 3px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            border-radius: 2px;
        }

        .cart-summary-box .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 1rem;
            margin-bottom: 0.8rem;
            color: #23272f;
            font-weight: 400;
            padding: 0.2rem 0;
        }

        .cart-summary-box .summary-row .label {
            color: #6b7280;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .cart-summary-box .summary-row .value {
            min-width: 80px;
            text-align: right;
            font-weight: 600;
            font-size: 1rem;
            color: #23272f;
        }

        .cart-summary-box .summary-row .value.text-danger {
            color: #ef5350 !important;
            font-weight: 600;
        }

        .cart-summary-box .border-y {
            border-top: 1.5px dashed #e3e6ea !important;
            border-bottom: 1.5px dashed #e3e6ea !important;
            margin: 1.3rem 0 1.3rem 0;
            padding: 0.7rem 0;
        }

        .cart-summary-box .total-label {
            font-size: 1.18rem;
            font-weight: 700;
            color: #23272f;
        }

        .cart-summary-box .total-value {
            color: #667eea;
            font-size: 1.6rem;
            font-weight: 800;
            letter-spacing: 0.02em;
            min-width: 90px;
            text-align: right;
            text-shadow: 0 1px 2px rgba(102, 126, 234, 0.1);
        }

        .cart-summary-box .voucher-group {
            margin-top: 1.1rem;
            margin-bottom: 1.1rem;
            display: flex;
            gap: 0.5rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .cart-summary-box .voucher-group .form-select,
        .cart-summary-box .voucher-group .form-control {
            border-radius: 8px;
            border: 1.2px solid #e2e8f0;
            font-size: 1rem;
            height: 40px;
            box-shadow: none;
            padding: 0.2rem 1rem;
            background: #fff;
            color: #23272f;
        }

        .cart-summary-box .voucher-group .btn {
            min-width: 90px;
            font-weight: 600;
            box-shadow: none;
            border-radius: 8px;
            height: 40px;
            background: #1677ff;
            color: #fff;
            transition: background 0.18s;
        }

        .cart-summary-box .voucher-group .btn:hover {
            background: #0056d6;
        }

        .cart-summary-box .btn-checkout {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            font-size: 1rem;
            font-weight: 700;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 1.5rem;
            border: none;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .cart-summary-box .btn-checkout:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);
        }

        @media (max-width: 991.98px) {
            .cart-summary-box {
                min-width: 100%;
                max-width: 100%;
                margin-top: 1rem;
                padding: 1rem 0.5rem 0.7rem 0.5rem;
            }

            .cart-summary-box h2 {
                font-size: 1.1rem;
                margin-bottom: 0.7rem;
            }

            .cart-summary-box .total-label {
                font-size: 1rem;
            }

            .cart-summary-box .total-value {
                font-size: 1.08rem;
                min-width: 60px;
            }

            .cart-summary-box .btn-checkout {
                font-size: 1rem;
                padding: 0.7rem 0;
            }
        }

        /* Empty Cart Styles */
        .cart-empty-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 60vh;
            padding: 2rem 0;
            margin-bottom: 4rem;
        }

        .cart-empty-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
            padding: 4rem 3rem;
            text-align: center;
            max-width: 500px;
            width: 100%;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .cart-empty-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        }

        .cart-empty-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.15);
        }

        .empty-cart-icon {
            margin-bottom: 2rem;
        }

        .icon-wrapper {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            position: relative;
            transition: all 0.3s ease;
        }

        .icon-wrapper::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            z-index: -1;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .cart-empty-card:hover .icon-wrapper::before {
            opacity: 1;
        }

        .icon-wrapper i {
            font-size: 3.5rem;
            color: #94a3b8;
            transition: all 0.3s ease;
        }

        .cart-empty-card:hover .icon-wrapper i {
            color: #667eea;
            transform: scale(1.1);
        }

        .empty-cart-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 1rem;
            letter-spacing: 0.02em;
        }

        .empty-cart-description {
            font-size: 1.1rem;
            color: #64748b;
            margin-bottom: 2.5rem;
            line-height: 1.6;
        }

        .empty-cart-actions {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .btn-start-shopping {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: 1.2rem 3rem;
            border-radius: 15px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
            position: relative;
            overflow: hidden;
            min-width: 250px;
            text-align: center;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }

        .btn-start-shopping::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.6s ease;
        }

        .btn-start-shopping:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
            color: #fff;
        }

        .btn-start-shopping:hover::before {
            left: 100%;
        }

        @media (max-width: 768px) {
            .cart-empty-card {
                padding: 3rem 2rem;
                margin: 0 1rem;
            }

            .empty-cart-title {
                font-size: 1.5rem;
            }

            .empty-cart-description {
                font-size: 1rem;
            }

            .empty-cart-actions {
                flex-direction: column;
                gap: 0.8rem;
            }

            .btn-start-shopping {
                width: 100%;
                max-width: 280px;
                padding: 1rem 2rem;
                font-size: 1rem;
            }
        }

        /* Additional Info Styles */
        .additional-info {
            background: #fff;
            border-radius: 8px;
            padding: 1.5rem;
            border: 1px solid #e9ecef;
            margin-top: 1rem;
        }

        .additional-info .info-item {
            font-size: 0.9rem;
        }

        .additional-info .info-item i {
            width: 20px;
            text-align: center;
        }

        .voucher-group {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .voucher-group .form-select,
        .voucher-group .form-control,
        .voucher-group .btn {
            height: 42px;
            border-radius: 8px;
            font-size: 1rem;
        }

        .voucher-group .form-select,
        .voucher-group .form-control {
            min-width: 150px;
            max-width: 180px;
        }

        .voucher-group .form-select:disabled,
        .voucher-group .form-control:disabled {
            background: #f5f5f5;
            color: #b0b3b8;
        }

        .voucher-group .btn {
            min-width: 90px;
            font-weight: 600;
            box-shadow: none;
        }

        .voucher-group {
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }

        .voucher-group .form-select {
            min-width: 120px;
            max-width: 140px;
            font-size: 1rem;
            height: 40px;
            border-radius: 8px;
        }

        .voucher-group .form-control {
            min-width: 140px;
            max-width: 180px;
            font-size: 1rem;
            height: 40px;
            border-radius: 8px;
        }

        .voucher-group .btn {
            min-width: 90px;
            max-width: 120px;
            width: auto;
            font-size: 1rem;
            font-weight: 600;
            height: 40px;
            border-radius: 8px;
            box-shadow: none;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 1.2rem;
            white-space: nowrap;
        }

        @media (max-width: 600px) {
            .voucher-group {
                flex-direction: column;
                align-items: stretch;
                gap: 0.5rem;
            }

            .voucher-group .form-select,
            .voucher-group .form-control,
            .voucher-group .btn {
                max-width: 100%;
                min-width: 0;
            }
        }

        /* Căn chỉnh lại checkbox cart */
        .cart-table-row td:first-child,
        .cart-header-row th:first-child {
            text-align: center;
            vertical-align: middle;
            width: 40px;
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }

        .cart-item-checkbox,
        #select-all-cart-items {
            width: 18px;
            height: 18px;
            accent-color: #1677ff;
            cursor: pointer;
            margin: 0 auto;
            display: block;
            box-shadow: 0 1px 2px rgba(44, 62, 80, 0.07);
        }

        @media (max-width: 600px) {

            .cart-table-row td:first-child,
            .cart-header-row th:first-child {
                width: 32px;
                padding-left: 0.2rem;
                padding-right: 0.2rem;
            }

            .cart-item-checkbox,
            #select-all-cart-items {
                width: 16px;
                height: 16px;
            }
        }

        .cart-table-row {
            margin-bottom: 24px;
            /* tăng khoảng cách giữa các dòng */
            padding-top: 18px;
            padding-bottom: 18px;
        }

        .cart-table-row td {
            padding-top: 18px !important;
            padding-bottom: 18px !important;
        }

        @media (max-width: 600px) {
            .cart-table-row {
                margin-bottom: 16px;
                padding-top: 12px;
                padding-bottom: 12px;
            }

            .cart-table-row td {
                padding-top: 12px !important;
                padding-bottom: 12px !important;
            }
        }

        .cart-product-info .product-name a {
            color: #23272f;
            text-decoration: none;
            transition: color 0.18s, text-decoration 0.18s;
        }

        .cart-product-info .product-name a:hover {
            color: red;
            text-decoration: underline;
        }

        .btn-checkout:disabled,
        .btn-checkout.disabled {
            opacity: 0.6;
            pointer-events: none;
        }

        /* Mini-cart đẹp hơn */
        .cartmini__widget-item {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 12px rgba(44, 62, 80, 0.07);
            border: 1.5px solid #e9ecef;
            display: flex;
            align-items: flex-start;
            gap: 1.2rem;
            padding: 1.2rem 1.2rem 1.2rem 1rem;
            margin-bottom: 1.1rem;
            transition: box-shadow 0.18s, transform 0.18s;
            position: relative;
        }

        .cartmini__widget-item:hover {
            box-shadow: 0 8px 32px rgba(44, 62, 80, 0.13);
            transform: translateY(-2px) scale(1.01);
        }

        .cartmini__widget-item .cart-item-card__image {
            width: 72px;
            height: 72px;
            border-radius: 10px;
            border: 1.5px solid #e3e6ea;
            object-fit: cover;
            background: #fafbfc;
            transition: box-shadow 0.18s, transform 0.18s;
        }

        .cartmini__widget-item .cart-item-card__image:hover {
            box-shadow: 0 4px 16px rgba(44, 62, 80, 0.18);
            transform: scale(1.07);
        }

        .cartmini__widget-item .product-name a {
            font-size: 1.08rem;
            font-weight: 700;
            color: #23272f;
            text-decoration: none;
            transition: color 0.18s;
            display: block;
            max-width: 180px;
            white-space: normal;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .cartmini__widget-item .product-name a:hover {
            color: #1677ff;
            text-decoration: underline;
        }

        .cartmini__widget-item .product-meta {
            font-size: 0.97rem;
            color: #7b7e85;
            display: flex;
            flex-direction: column;
            gap: 2px;
            align-items: flex-start;
            flex-wrap: nowrap;
        }

        .cartmini__widget-item .cartmini__item-qty {
            font-size: 0.97rem;
            color: #4a5568;
            margin-top: 2px;
        }

        .cartmini__widget-item .cartmini__item-price {
            font-weight: 700;
            color: #1677ff;
            font-size: 1.08rem;
            white-space: nowrap;
        }

        .cartmini__widget-item .cartmini__remove-btn {
            background: #f7f8fa;
            border: 1.5px solid #e2e8f0;
            color: #b0b3b8;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.18s;
            margin-top: 0.2rem;
        }

        .cartmini__widget-item .cartmini__remove-btn:hover {
            background: #ef5350;
            border-color: #ef5350;
            color: #fff;
            transform: scale(1.08);
        }

        .cartmini__checkout {
            padding: 1.2rem 1.75rem 1.2rem 1.75rem !important;
            border-top: none;
            box-shadow: none;
            background: transparent;
            margin-top: 0;
        }

        .cartmini__checkout-btn {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0;
        }

        .cartmini__checkout-btn .tp-btn-border {
            margin-bottom: 0 !important;
            min-width: 180px;
            font-size: 1.08rem;
        }

        .bulk-delete-floating-btn {
            position: absolute;
            top: -54px;
            right: 0;
            z-index: 10;
            min-width: 180px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(44, 62, 80, 0.10);
            display: none;
        }

        @media (max-width: 991.98px) {
            .bulk-delete-floating-btn {
                top: -44px;
                min-width: 120px;
                font-size: 0.98rem;
            }
        }
    </style>

    <section class="pt-4 pb-6" style="margin-bottom: 4rem;">
        <div class="container">
            <div class="row g-4 justify-content-center">
                @if (isset($cartItems) && count($cartItems))
                    <div class="col-12 col-lg-8">
                        <div class="cart-header mb-4">
                            <div class="d-flex flex-column gap-2">
                                <h1 class="fw-bold mb-0" style="font-size: 2.2rem; color: #1e293b;">
                                    GIỎ HÀNG CỦA BẠN
                                </h1>
                                <span class="fs-6 text-muted" style="font-weight: 400;">
                                    (Có {{ count($cartItems) }} sản phẩm trong giỏ hàng)
                                </span>
                            </div>
                        </div>
                        <form id="cart-checkout-form" method="POST" action="{{ route('checkout') }}"
                            style="position:relative;">
                            @csrf
                            <button type="button" id="bulk-delete-btn" class="btn btn-danger bulk-delete-floating-btn" disabled>
                                <i class="fa fa-trash"></i> Xóa
                            </button>
                            <div id="cartTable">
                                <div class="table-responsive scrollbar mx-n1 px-1">
                                    <table class="table mb-0 align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="align-middle text-center" style="width:40px;">
                                                    <input type="checkbox" id="select-all-cart-items" 
                                                           class="form-check-input" 
                                                           style="width: 18px; height: 18px;"/>
                                                </th>
                                                <th class="align-middle product-info-cell" style="min-width:320px;">
                                                    <span class="fw-bold">Sản phẩm</span>
                                                </th>
                                                <th class="align-middle text-end price" style="min-width:120px;">
                                                    <span class="fw-bold">Giá</span>
                                                </th>
                                                <th class="align-middle text-center quantity" style="min-width:160px;">
                                                    <span class="fw-bold">Số lượng</span>
                                                </th>
                                                <th class="align-middle text-end total" style="min-width:120px;">
                                                    <span class="fw-bold">Tổng cộng</span>
                                                </th>
                                                <th class="align-middle text-end" style="width:60px;">
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="list" id="cart-table-body">
                                            @foreach ($cartItems as $item)
                                                @php
                                                    $product = $item->product;
                                                    $variant = $item->productVariant;
                                                    $unitPrice = $item->price_at_time;
                                                    $stock = (int) ($variant ? $variant->stock : $product->stock ?? 0);
                                                    $getAttrValue = function ($entity, $keywords) {
                                                        if (!$entity || !isset($entity->attributeValues)) {
                                                            return null;
                                                        }
                                                        foreach ($entity->attributeValues as $attrVal) {
                                                            $attrName = strtolower($attrVal->attribute->name ?? '');
                                                            foreach ($keywords as $kw) {
                                                                if (str_contains($attrName, $kw)) {
                                                                    return $attrVal->value;
                                                                }
                                                            }
                                                        }
                                                        return null;
                                                    };
                                                    $size = $getAttrValue($variant, ['size', 'kích']);
                                                    $color = $getAttrValue($variant, ['color', 'màu']);
                                                    // Lấy sản phẩm liên quan từ controller
                                                    $relatedProducts = $stock < 1 ? (isset($item->relatedProducts) ? $item->relatedProducts : []) : [];
                                                    // Lấy ảnh đúng cho biến thể hoặc sản phẩm
                                                    if ($variant) {
                                                        if (!empty($variant->img)) {
                                                            $img = asset('storage/' . $variant->img);
                                                        } elseif ($variant->images && $variant->images->count() > 0) {
                                                            $img = asset('storage/' . $variant->images->first()->url);
                                                        } else {
                                                            $img = $product->image_url ?? asset('assets2/img/product/2/default.png');
                                                        }
                                                    } else {
                                                        $img = $product->image_url ?? asset('assets2/img/product/2/default.png');
                                                    }
                                                @endphp
                                                <tr class="cart-table-row btn-reveal-trigger @if ($stock < 1) cart-item-out-of-stock @endif"
                                                    data-item-id="{{ $item->id }}" data-unit-price="{{ $unitPrice }}"
                                                    data-stock="{{ $stock }}">
                                                    <td class="align-middle text-center">
                                                        <div class="d-flex justify-content-center">
                                                            <input type="checkbox" class="cart-item-checkbox form-check-input"
                                                                name="selected_items[]" value="{{ $item->id }}"
                                                                @if ($stock < 1) disabled title="Sản phẩm này đã hết hàng, không thể thanh toán" @endif
                                                                style="width: 18px; height: 18px;"/>
                                                        </div>
                                                    </td>
                                                    <td class="align-middle product-info-cell" style="min-width:250px;">
                                                        <div style="display:flex;align-items:center;gap:16px;">
                                                            @if (!empty($product->slug))
                                                                <a class="d-block border border-translucent rounded-2"
                                                                    href="{{ route('client.product.show', ['slug' => $product->slug]) }}">
                                                                    <img class="cart-item-card__image"
                                                                        src="{{ $img }}"
                                                                        alt="{{ $product->name }}" />
                                                                </a>
                                                            @else
                                                                <span>
                                                                    <img class="cart-item-card__image"
                                                                        src="{{ $img }}"
                                                                        alt="{{ $product->name }}" />
                                                                </span>
                                                            @endif
                                                            <div class="cart-product-info" style="min-width:0;">
                                                                <div class="product-name"
                                                                    style="font-weight:600;font-size:1rem;color:#23272f;margin-bottom:4px;line-height:1.3;max-width:220px;white-space:normal;overflow:hidden;">
                                                                    @if (!empty($product->slug))
                                                                        <a
                                                                            href="{{ route('client.product.show', ['slug' => $product->slug]) }}">
                                                                            {{ $product->name }}
                                                                        </a>
                                                                    @else
                                                                        <span>{{ $product->name }}</span>
                                                                    @endif
                                                                </div>
                                                                <div class="product-meta"
                                                                    style="font-size:0.85rem;color:#7b7e85;display:flex;flex-direction:column;gap:2px;align-items:flex-start;flex-wrap:nowrap;">
                                                                    <span class="sku">Mã:
                                                                        {{ $variant->sku ?? $product->sku }}</span>
                                                                    @if ($color)
                                                                        <span class="color">Màu:
                                                                            {{ $color }}</span>
                                                                    @endif
                                                                    @if ($size)
                                                                        <span class="size">Size:
                                                                            {{ $size }}</span>
                                                                    @endif
                                                                    {{-- Nếu có thêm biến thể khác, hiển thị ở đây --}}
                                                                    @if (isset($variant) && isset($variant->attributeValues))
                                                                        @foreach ($variant->attributeValues as $attrVal)
                                                                            @php $attrName = strtolower($attrVal->attribute->name ?? ''); @endphp
                                                                            @if (
                                                                                !str_contains($attrName, 'size') &&
                                                                                    !str_contains($attrName, 'kích') &&
                                                                                    !str_contains($attrName, 'color') &&
                                                                                    !str_contains($attrName, 'màu'))
                                                                                <span>{{ $attrVal->attribute->name ?? '' }}:
                                                                                    {{ $attrVal->value }}</span>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                    @if ($stock < 1)
                                                                        <span class="badge bg-danger mt-1">Hết hàng</span>
                                                                        <span class="text-danger small">Sản phẩm này đã hết
                                                                            hàng, vui lòng quay lại sau.</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="price align-middle text-end">
                                                        <div class="price-wrapper" style="display: flex; align-items: center; gap: 0.25rem;">
                                                            <span class="price-number fw-semibold" style="font-size: 1.1rem; color: #333;">
                                                                {{ number_format($unitPrice, 0, ',', '.') }}
                                                            </span>
                                                            <span class="currency-symbol" style="font-size: 0.9rem; color: #ff4444;">
                                                                ₫
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td class="quantity align-middle text-center">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <button type="button" class="qty-btn-custom minus"
                                                                @if ($stock < 1) disabled @endif
                                                                @if ($item->quantity <= 1) disabled @endif>
                                                                <i class="fa fa-minus"></i>
                                                            </button>
                                                            <input type="text" class="qty-input"
                                                                value="{{ $stock < 1 ? 0 : $item->quantity }}"
                                                                @if ($stock < 1) disabled @endif readonly />
                                                            <button type="button" class="qty-btn-custom plus"
                                                                @if ($stock < 1) disabled @endif>
                                                                <i class="fa fa-plus"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                    <td class="total align-middle text-end">
                                                        <div class="total-wrapper" style="display: flex; align-items: center; gap: 0.25rem;">
                                                            <span class="total-number fw-semibold" style="font-size: 1.1rem; color: #333;">
                                                                {{ number_format($unitPrice * $item->quantity, 0, ',', '.') }}
                                                            </span>
                                                            <span class="currency-symbol" style="font-size: 0.9rem; color: #ff4444;">
                                                                ₫
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td class="align-middle text-end pe-0 ps-3">
                                                        <form action="{{ url('/shopping-cart/remove/' . $item->id) }}"
                                                            method="POST"
                                                            class="single-delete-form"
                                                            data-item-id="{{ $item->id }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-link text-danger p-0"
                                                                title="Xóa">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="cart-summary-box">
                            <h2 class="mb-3">TÓM TẮT ĐƠN HÀNG</h2>
                            <div class="summary-row">
                                <span class="label">Tổng tiền:</span>
                                <span class="value" id="total-value" style="font-size: 1.2rem; font-weight: 700;">{{ number_format($cartTotal ?? 0, 0, ',', '.') }}₫</span>
                            </div>
                            <div class="summary-row mt-3">
                                <span class="label" style="font-size: 0.9rem; color: #6b7280;">Bạn có thể nhập mã giảm giá ở trang thanh toán</span>
                            </div>
                            <button type="button" id="btn-checkout" class="btn-checkout w-100"
                                style="margin-top:1.5rem;">
                                TIẾN HÀNH ĐẶT HÀNG
                            </button>
                            <a href="{{ route('shop') }}" class="btn btn-outline-dark w-100 mt-3" style="border: 2px solid #667eea; background: #fff; color: #667eea; padding: 1rem; border-radius: 12px; font-weight: 600; transition: all 0.3s ease; text-transform: uppercase; letter-spacing: 0.05em; text-decoration: none; display: inline-block; text-align: center;">
                                MUA THÊM SẢN PHẨM
                            </a>
                        </div>
                        
                        <!-- Additional Information -->
                        <div class="additional-info mt-4">
                            <div class="info-item d-flex align-items-center mb-3">
                                <i class="fa fa-truck me-3" style="color: #23272f; font-size: 1.1rem;"></i>
                                <span style="color: #23272f; font-weight: 500; font-size: 0.9rem;">GIAO HÀNG NỘI THÀNH TRONG 24 GIỜ</span>
                            </div>
                            <div class="info-item d-flex align-items-center mb-3">
                                <i class="fa fa-exchange-alt me-3" style="color: #23272f; font-size: 1.1rem;"></i>
                                <span style="color: #23272f; font-weight: 500; font-size: 0.9rem;">ĐỔI HÀNG TRONG 30 NGÀY</span>
                            </div>
                            <div class="info-item d-flex align-items-center">
                                <i class="fa fa-headset me-3" style="color: #23272f; font-size: 1.1rem;"></i>
                                <span style="color: #23272f; font-weight: 500; font-size: 0.9rem;">TỔNG ĐÀI BÁN HÀNG 096728.4444</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="col-12">
                        <div class="cart-empty-container">
                            <div class="cart-empty-card">
                                <div class="empty-cart-icon">
                                    <div class="icon-wrapper">
                                        <i class="fa-light fa-cart-shopping"></i>
                                    </div>
                                </div>
                                <div class="empty-cart-content">
                                    <h2 class="empty-cart-title">Giỏ hàng của bạn còn trống</h2>
                                    <p class="empty-cart-description">Cùng khám phá hàng ngàn sản phẩm tuyệt vời tại Aurora nhé!</p>
                                    <div class="empty-cart-actions">
                                        <a href="{{ route('home') }}" class="btn-start-shopping">
                                            <i class="fa fa-shopping-bag me-2"></i>
                                            Bắt đầu mua sắm
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
    <!-- Modal xác nhận xóa hàng loạt -->
    <div class="modal fade" id="confirmBulkDeleteModal" tabindex="-1" aria-labelledby="confirmBulkDeleteLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="confirmBulkDeleteLabel">Xác nhận xóa sản phẩm</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
          </div>
          <div class="modal-body">
            <span id="bulk-delete-modal-message">Bạn có chắc muốn xóa các sản phẩm đã chọn khỏi giỏ hàng?</span>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            <button type="button" class="btn btn-danger" id="confirm-bulk-delete-btn">Xóa</button>
          </div>
        </div>
      </div>
    </div>
    <!-- Modal xác nhận xóa từng sản phẩm -->
    <div class="modal fade" id="confirmSingleDeleteModal" tabindex="-1" aria-labelledby="confirmSingleDeleteLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="confirmSingleDeleteLabel">Xác nhận xóa sản phẩm</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
          </div>
          <div class="modal-body">
            <span id="single-delete-modal-message">Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?</span>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            <button type="button" class="btn btn-danger" id="confirm-single-delete-btn">Xóa</button>
          </div>
        </div>
      </div>
    </div>
@endsection

@section('scripts')
    @parent
    <!-- SwiperJS CDN nếu chưa có -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cartTableBody = document.getElementById('cart-table-body');
            if (!cartTableBody) return;

            updateCartSummary();

            function formatCurrency(num) {
                return num.toLocaleString('vi-VN') + '₫';
            }

            function updateLineTotal(row, qty) {
                const unitPrice = parseInt(row.getAttribute('data-unit-price'), 10) || 0;
                const totalCell = row.querySelector('.total');
                if (totalCell) totalCell.textContent = formatCurrency(unitPrice * qty);
            }

            function updateCartSummary() {
                let total = 0;
                let checkedCount = 0;
                document.querySelectorAll('tr[data-item-id]').forEach(row => {
                    const checkbox = row.querySelector('.cart-item-checkbox');
                    if (!checkbox || !checkbox.checked) return;
                    const unitPrice = parseInt(row.getAttribute('data-unit-price'), 10) || 0;
                    const qty = parseInt(row.querySelector('.qty-input').value, 10) || 1;
                    total += unitPrice * qty;
                    checkedCount++;
                });

                // Cập nhật tổng tiền
                const totalEl = document.getElementById('total-value');
                if (totalEl) {
                    totalEl.textContent = formatCurrency(total);
                }
            }

            function updateServerQty(itemId, qty, cb) {
                const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';
                if (!csrfToken) {
                    if (cb) cb(false, {
                        message: 'CSRF token missing'
                    });
                    return;
                }

                fetch('/shopping-cart/update/' + itemId, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            quantity: qty
                        })
                    })
                    .then(res => res.json().then(data => ({
                        ok: res.ok,
                        data
                    })))
                    .then(({
                        ok,
                        data
                    }) => {
                        if (!ok || !data.success) {
                            if (cb) cb(false, data);
                        } else {
                            if (cb) cb(true, data);
                            document.dispatchEvent(new CustomEvent('cart:qty-updated', {
                                detail: {
                                    itemId: itemId,
                                    quantity: qty
                                }
                            }));
                        }
                    })
                    .catch(error => {
                        if (cb) cb(false, {
                            message: error.message || 'Lỗi kết nối server'
                        });
                    });
            }

            function toggleQtyButtons(row, qty, stock) {
                const plusBtn = row.querySelector('.qty-btn-custom.plus');
                const minusBtn = row.querySelector('.qty-btn-custom.minus');
                // Không disable plusBtn khi đạt tối đa, chỉ disable nếu hết hàng
                if (plusBtn) plusBtn.disabled = stock < 1;
                if (minusBtn) minusBtn.disabled = qty <= 1;
            }

            let debounceTimers = {};

            cartTableBody.addEventListener('click', function(e) {
                const btn = e.target.closest('.qty-btn-custom');
                if (!btn) return;
                const row = btn.closest('tr[data-item-id]');
                if (!row) return;
                const itemId = row.getAttribute('data-item-id');
                if (!itemId || itemId === 'null' || itemId === 'undefined') {
                    console.error('Thiếu hoặc sai itemId khi cập nhật số lượng giỏ hàng!', itemId, row);
                    return;
                }
                const qtyInput = row.querySelector('.qty-input');
                if (!qtyInput) return;
                let qty = parseInt(qtyInput.value, 10);
                if (isNaN(qty) || qty < 1) qty = 1;
                const stock = parseInt(row.getAttribute('data-stock'), 10) || 9999;
                if (btn.classList.contains('plus')) {
                    if (qty >= stock) {
                        if (window.toastr) toastr.error('Chỉ còn ' + stock + ' sản phẩm trong kho!');
                        qtyInput.value = stock;
                        updateLineTotal(row, stock);
                        updateCartSummary();
                        toggleQtyButtons(row, stock, stock);
                        return;
                    }
                    qty++;
                    if (qty > stock) {
                        qty = stock;
                        if (window.toastr) toastr.error('Chỉ còn ' + stock + ' sản phẩm trong kho!');
                    }
                } else if (btn.classList.contains('minus')) {
                    if (qty <= 1) return;
                    qty--;
                }
                qtyInput.value = qty;
                updateLineTotal(row, qty);
                updateCartSummary();
                toggleQtyButtons(row, qty, stock);
                if (debounceTimers[itemId]) clearTimeout(debounceTimers[itemId]);
                debounceTimers[itemId] = setTimeout(() => {
                    updateServerQty(itemId, qty, function(success, data) {
                        if (!success && data && data.message && data.message.includes(
                                'Chỉ còn')) {
                            qtyInput.value = stock;
                            updateLineTotal(row, stock);
                            updateCartSummary();
                            toggleQtyButtons(row, stock, stock);
                        }
                    });
                }, 500);
            });

            cartTableBody.addEventListener('input', function(e) {
                const input = e.target;
                if (!input.classList.contains('qty-input')) return;
                const row = input.closest('tr[data-item-id]');
                const itemId = row.getAttribute('data-item-id');
                if (!itemId || itemId === 'null' || itemId === 'undefined') {
                    console.error('Thiếu hoặc sai itemId khi cập nhật số lượng giỏ hàng!', itemId, row);
                    return;
                }
                let qty = parseInt(input.value, 10);
                if (isNaN(qty) || qty < 1) qty = 1;
                const stock = parseInt(row.getAttribute('data-stock'), 10) || 9999;
                if (qty > stock) {
                    qty = stock;
                    if (window.toastr) toastr.error('Chỉ còn ' + stock + ' sản phẩm trong kho!');
                }
                input.value = qty;
                updateLineTotal(row, qty);
                updateCartSummary();
                toggleQtyButtons(row, qty, stock);
                if (debounceTimers[itemId]) clearTimeout(debounceTimers[itemId]);
                debounceTimers[itemId] = setTimeout(() => {
                    updateServerQty(itemId, qty, function(success, data) {
                        if (!success) {
                            if (data && data.message && data.message.includes('Chỉ còn')) {
                                input.value = stock;
                                updateLineTotal(row, stock);
                                updateCartSummary();
                                toggleQtyButtons(row, stock, stock);
                            } else {
                                console.error('Lỗi cập nhật giỏ hàng:', data && data
                                    .message, 'itemId:', itemId, 'qty:', qty);
                            }
                        }
                        if (debounceTimers[itemId]) clearTimeout(debounceTimers[itemId]);
                    });
                }, 500);
            });

            cartTableBody.addEventListener('blur', function(e) {
                if (!e.target.classList.contains('qty-input')) return;
                const input = e.target;
                const row = input.closest('tr[data-item-id]');
                const stock = parseInt(row.getAttribute('data-stock'), 10) || 9999;
                const qty = parseInt(input.value, 10) || 1;
                if (qty === stock && window.toastr) toastr.error('Chỉ còn ' + stock +
                    ' sản phẩm trong kho!');
                toggleQtyButtons(row, qty, stock);
            }, true);

            cartTableBody.addEventListener('change', function(e) {
                if (e.target.classList.contains('cart-item-checkbox')) {
                    updateCartSummary();
                }
            });

            updateCartSummary();

            // Xử lý chọn tất cả
            const selectAllCheckbox = document.getElementById('select-all-cart-items');
            const form = document.getElementById('cart-checkout-form');



            function getAllItemCheckboxes() {
                return Array.from(document.querySelectorAll('.cart-item-checkbox:not(:disabled)'));
            }

            function updateSelectAllState() {
                const all = getAllItemCheckboxes();
                const checked = all.filter(cb => cb.checked);
                selectAllCheckbox.checked = all.length > 0 && checked.length === all.length;
                selectAllCheckbox.indeterminate = checked.length > 0 && checked.length < all.length;
            }

            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    const all = getAllItemCheckboxes();
                    all.forEach(cb => {
                        cb.checked = selectAllCheckbox.checked;
                    });
                    updateCartSummary();
                });
            }

            cartTableBody.addEventListener('change', function(e) {
                if (e.target.classList.contains('cart-item-checkbox')) {
                    updateSelectAllState();
                    updateCartSummary();
                }
            });

            updateSelectAllState();

            // Xử lý nút thanh toán
            const btnCheckout = document.getElementById('btn-checkout');
            if (btnCheckout) {
                btnCheckout.addEventListener('click', function(e) {
                    const checked = getAllItemCheckboxes().filter(cb => cb.checked);
                    if (checked.length === 0) {
                        if (window.toastr && typeof toastr.error === 'function') {
                            toastr.error('Vui lòng chọn ít nhất 1 sản phẩm để thanh toán!');
                        } else {
                            alert('Vui lòng chọn ít nhất 1 sản phẩm để thanh toán!');
                        }
                        return;
                    }

                    // Tạo query string từ selected_items
                    const selectedItems = checked.map(cb => cb.value);
                    const queryString = 'selected_items=' + encodeURIComponent(JSON.stringify(
                        selectedItems));
                    window.location.href = '{{ route('checkout') }}?' + queryString;
                });
            }

            // Xử lý xóa hàng loạt
            const bulkDeleteBtn = document.getElementById('bulk-delete-btn');

            function updateBulkDeleteBtn() {
                const checked = getAllItemCheckboxes().filter(cb => cb.checked);
                if (checked.length > 0) {
                    bulkDeleteBtn.disabled = false;
                    bulkDeleteBtn.innerHTML =
                        `<i class="fa fa-trash"></i> Xóa`;
                } else {
                    bulkDeleteBtn.disabled = true;
                    bulkDeleteBtn.innerHTML = `<i class="fa fa-trash"></i> Xóa`;
                }
                bulkDeleteBtn.style.display = 'inline-block';
            }

            cartTableBody.addEventListener('change', function(e) {
                if (e.target.classList.contains('cart-item-checkbox')) {
                    updateBulkDeleteBtn();
                }
            });

            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', updateBulkDeleteBtn);
            }

            updateBulkDeleteBtn();

            // Thay thế sự kiện click nút bulkDeleteBtn:
                bulkDeleteBtn.addEventListener('click', function() {
                    const checked = getAllItemCheckboxes().filter(cb => cb.checked);
                    if (checked.length === 0) return;
                    const ids = checked.map(cb => cb.value);
                // Cập nhật nội dung modal
                const msg = `Bạn có chắc muốn xóa ${ids.length > 1 ? ids.length + ' sản phẩm đã chọn' : 'sản phẩm đã chọn'} khỏi giỏ hàng?`;
                document.getElementById('bulk-delete-modal-message').textContent = msg;
                // Lưu ids vào modal để dùng khi xác nhận
                document.getElementById('confirm-bulk-delete-btn').dataset.ids = JSON.stringify(ids);
                // Hiện modal (Bootstrap 5)
                const modal = new bootstrap.Modal(document.getElementById('confirmBulkDeleteModal'));
                modal.show();
            });
            // Xử lý xác nhận xóa trong modal
            if (document.getElementById('confirm-bulk-delete-btn')) {
                document.getElementById('confirm-bulk-delete-btn').addEventListener('click', function() {
                    const ids = JSON.parse(this.dataset.ids || '[]');
                    if (!ids.length) return;
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    fetch('/shopping-cart/bulk-delete', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                        body: JSON.stringify({ ids })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                ids.forEach(id => {
                                const row = document.querySelector(`tr[data-item-id="${id}"]`);
                                    if (row) row.remove();
                                });
                                updateCartSummary();
                                updateBulkDeleteBtn();
                                updateSelectAllState();
                            if (window.toastr) toastr.success('Đã xóa các sản phẩm đã chọn!');
                            } else {
                            if (window.toastr) toastr.error(data.message || 'Có lỗi khi xóa hàng loạt!');
                            }
                        // Ẩn modal
                        const modalEl = document.getElementById('confirmBulkDeleteModal');
                        const modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) modal.hide();
                        })
                        .catch(() => {
                            if (window.toastr) toastr.error('Lỗi kết nối server khi xóa hàng loạt!');
                        // Ẩn modal
                        const modalEl = document.getElementById('confirmBulkDeleteModal');
                        const modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) modal.hide();
                        });
                });
            }

            // Xử lý slider sản phẩm liên quan
            document.querySelectorAll('.related-products-swiper').forEach(function(swiperEl) {
                new Swiper(swiperEl, {
                    slidesPerView: 3,
                    spaceBetween: 12,
                    loop: false,
                    navigation: {
                        nextEl: swiperEl.querySelector('.swiper-button-next'),
                        prevEl: swiperEl.querySelector('.swiper-button-prev'),
                    },
                    breakpoints: {
                        1200: {
                            slidesPerView: 3
                        },
                        992: {
                            slidesPerView: 2
                        },
                        0: {
                            slidesPerView: 1
                        }
                    }
                });
            });
        });



        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.related-products-swiper').forEach(function(swiperEl) {
                new Swiper(swiperEl, {
                    slidesPerView: 3,
                    spaceBetween: 12,
                    loop: false,
                    navigation: {
                        nextEl: swiperEl.querySelector('.swiper-button-next'),
                        prevEl: swiperEl.querySelector('.swiper-button-prev'),
                    },
                    breakpoints: {
                        1200: {
                            slidesPerView: 3
                        },
                        992: {
                            slidesPerView: 2
                        },
                        0: {
                            slidesPerView: 1
                        }
                    }
                });
            });
        });

        // JS: Modal confirm cho xóa từng sản phẩm
        document.querySelectorAll('.single-delete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                window._pendingSingleDeleteForm = this;
                const modal = new bootstrap.Modal(document.getElementById('confirmSingleDeleteModal'));
                modal.show();
            });
        });
        document.getElementById('confirm-single-delete-btn').addEventListener('click', function() {
            if (window._pendingSingleDeleteForm) {
                window._pendingSingleDeleteForm.submit();
                const modalEl = document.getElementById('confirmSingleDeleteModal');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
                window._pendingSingleDeleteForm = null;
            }
        });
    </script>
@endsection

<style>
    .related-products-slider-container {
        max-width: 700px;
        margin: 0 auto;
        padding-top: 18px;
        padding-bottom: 18px;
    }

    .related-products-swiper {
        padding: 0 28px;
    }

    .swiper-wrapper {
        gap: 0 !important;
    }

    .related-product-simple {
        min-width: 0;
        max-width: 100%;
        padding: 0 16px;
    }

    .related-product-thumb-simple:hover img {
        filter: brightness(0.95);
        transform: scale(1.04);
        box-shadow: 0 4px 16px rgba(44, 62, 80, 0.13);
    }

    .related-product-title-simple:hover {
        text-decoration: underline;
        cursor: pointer;
    }

    .swiper-button-prev,
    .swiper-button-next {
        background: #fff;
        border-radius: 50%;
        box-shadow: 0 2px 8px rgba(44, 62, 80, 0.13);
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0.92;
        transition: box-shadow 0.18s, background 0.18s;
    }

    .swiper-button-prev:hover,
    .swiper-button-next:hover {
        background: #e3f0fc;
        box-shadow: 0 4px 16px rgba(44, 62, 80, 0.18);
    }

    .swiper-button-prev {
        left: -8px;
    }

    .swiper-button-next {
        right: -8px;
    }

    .swiper-button-prev:after,
    .swiper-button-next:after {
        display: none;
    }

    @media (max-width: 900px) {
        .related-products-slider-container {
            max-width: 98vw;
        }

        .related-products-swiper {
            padding: 0 8px;
        }

        .related-product-simple {
            padding: 0 6px;
        }
    }

    @media (max-width: 600px) {
        .related-products-slider-container {
            padding-top: 8px;
            padding-bottom: 8px;
        }

        .related-product-thumb-simple {
            height: 70px;
        }

        .related-product-simple {
            padding: 0 2px;
        }
    }
</style>

