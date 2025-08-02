@extends('client.layouts.default')

@section('title', 'Giỏ hàng - Aurora')

@section('content')
    <style>
        /* Nâng cấp toàn diện giao diện giỏ hàng */
        .tp-cart-area {
            background-color: #f7f8fa;
            padding-top: 3rem;
            padding-bottom: 6rem;
        }

        .breadcrumb__area {
            background-color: #fff;
            border-bottom: 1px solid #e9ecef;
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
            grid-template-columns: 110px 2.7fr 1fr 1.1fr 1.1fr 56px;
            gap: 1.2rem;
            font-size: 1.08rem;
            font-weight: 700;
            color: #7b7e85;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
            background: transparent;
            align-items: center;
        }

        .cart-header-row__product {
            grid-column: 1 / 3;
        }

        /* Thẻ sản phẩm */
        .cart-item-card {
            display: grid;
            grid-template-columns: 110px 2.5fr 1fr 1.1fr 1.1fr 56px;
            align-items: center;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.07);
            border: 1px solid #e9ecef;
            padding: 1.2rem 1.5rem;
            transition: box-shadow 0.2s;
            gap: 1.2rem;
            min-height: 92px;
        }

        .cart-item-card:hover {
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
        }

        .cart-item-card__image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid #f0f2f5;
            display: block;
            margin: 0 auto;
            transition: box-shadow 0.18s, transform 0.18s;
        }

        .cart-item-card__image:hover {
            box-shadow: 0 4px 16px rgba(44, 62, 80, 0.18);
            transform: scale(1.07);
            cursor: pointer;
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
            font-size: 1.13rem;
            font-weight: 700;
            color: #23272f;
            margin-bottom: 0.18rem;
            line-height: 1.25;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 220px;
            display: block;
        }

        .cart-item-card__info .meta-attributes {
            font-size: 0.97rem;
            color: #7b7e85;
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
            font-size: 1.08rem;
            font-weight: 600;
            color: #1a202c;
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
            background: #fff;
            border: 1.5px solid #e2e8f0;
            border-radius: 24px;
            height: 40px;
            min-width: 110px;
            max-width: 140px;
            padding: 0 6px;
            gap: 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
        }

        .cart-qty .qty-btn {
            background: none;
            border: none;
            color: #4a90e2;
            font-size: 1.3rem;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            cursor: pointer;
            transition: background 0.18s, color 0.18s;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            outline: none;
        }

        .cart-qty .qty-btn:hover {
            background: #e3f0fc;
            color: #1565c0;
        }

        .cart-qty .qty-input {
            border: none;
            background: #fff;
            width: 38px;
            height: 36px;
            text-align: center;
            font-weight: 700;
            font-size: 1.15rem;
            color: #23272f !important;
            border-radius: 8px;
            outline: none;
            box-shadow: none;
            margin: 0 2px;
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
            background: #f7f8fa;
            border: 1px solid #e2e8f0;
            color: #b0b3b8;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            transition: all 0.18s;
        }

        .cart-item-card__remove .remove-btn:hover {
            background: #ef5350;
            border-color: #ef5350;
            color: #fff;
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
            background: #f7f8fa !important;
        }

        .cart-summary-box {
            background: #fafbfc;
            border-radius: 18px;
            box-shadow: 0 2px 16px rgba(44, 62, 80, 0.07);
            border: 1.2px solid #e3e6ea;
            padding: 2.2rem 1.7rem 1.7rem 1.7rem;
            min-width: 320px;
            max-width: 420px;
            margin-top: 1.5rem;
            margin-bottom: 1.5rem;
            font-family: 'Inter', Arial, sans-serif;
            transition: box-shadow 0.18s, opacity 0.18s;
        }

        .cart-summary-box h2 {
            font-family: 'Inter', Arial, sans-serif;
            font-size: 1.18rem;
            font-weight: 700;
            color: #23272f;
            margin-bottom: 1.2rem;
            margin-top: 0;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            text-align: left;
            display: inline-block;
        }

        .cart-summary-box .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 1.08rem;
            margin-bottom: 1.1rem;
            color: #23272f;
            font-weight: 400;
            padding: 0.2rem 0;
        }

        .cart-summary-box .summary-row .label {
            color: #6b7280;
            font-weight: 500;
        }

        .cart-summary-box .summary-row .value {
            min-width: 80px;
            text-align: right;
            font-weight: 500;
            font-size: 1.08rem;
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
            color: #1677ff;
            font-size: 1.45rem;
            font-weight: 800;
            letter-spacing: 0.01em;
            min-width: 90px;
            text-align: right;
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
            background: linear-gradient(90deg, #1677ff 60%, #4fc3f7 100%);
            color: #fff;
            font-size: 1.18rem;
            font-weight: 700;
            border-radius: 12px;
            padding: 1.1rem 0;
            width: 100%;
            margin-top: 1.2rem;
            box-shadow: 0 2px 8px rgba(44, 62, 80, 0.10);
            border: none;
            transition: background 0.18s, box-shadow 0.18s;
            text-transform: none;
            display: flex;
            align-items: center;
            justify-content: center;
            letter-spacing: 0.01em;
        }

        .cart-summary-box .btn-checkout:hover,
        .cart-summary-box .btn-checkout:focus {
            background: linear-gradient(90deg, #0056d6 60%, #1677ff 100%);
            color: #fff;
            box-shadow: 0 4px 16px rgba(44, 62, 80, 0.13);
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

        /* Sản phẩm hết hàng hoặc ngừng kinh doanh */
        .cart-item-out-of-stock {
            opacity: 0.6;
            background-color: #f8f9fa;
        }

        .cart-item-out-of-stock .cart-item-card__image {
            filter: grayscale(100%);
        }

        /* Sản phẩm ngừng kinh doanh */
        .cart-item-out-of-stock[data-discontinued="true"] {
            opacity: 0.7;
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
        }

        .cart-item-out-of-stock[data-discontinued="true"] .cart-item-card__image {
            filter: grayscale(50%) sepia(20%);
        }

        .cart-item-out-of-stock[data-discontinued="true"] .product-name a,
        .cart-item-out-of-stock[data-discontinued="true"] .product-name span {
            color: #856404 !important;
        }

        /* Sản phẩm có giá thay đổi */
        .cart-item-price-changed {
            background-color: #e3f2fd !important;
            border: 1px solid #2196f3 !important;
        }

        .cart-item-price-changed .cart-item-card__image {
            filter: brightness(1.05);
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

    <section class="pt-4 pb-6">
        <div class="container">
            <h1 class="fw-bold mb-5" style="font-size:2.2rem;">
                Giỏ hàng
                @if (isset($cartItems) && count($cartItems))
                    <span class="fs-5 text-muted" style="font-weight:400;"> ({{ count($cartItems) }} sản phẩm)</span>
                @endif
            </h1>
            <div class="row g-4 justify-content-center">
                @if (isset($cartItems) && count($cartItems))
                    <div class="col-12 col-lg-8">
                        <form id="cart-checkout-form" method="POST" action="{{ route('checkout') }}"
                            style="position:relative;">
                            @csrf
                            <button type="button" id="bulk-delete-btn" class="btn btn-danger bulk-delete-floating-btn" disabled>
                                <i class="fa fa-trash"></i> Xóa
                            </button>
                            <div id="cartTable">
                                <div class="table-responsive scrollbar mx-n1 px-1">
                                    <table class="table fs-9 mb-0 border-top border-translucent align-middle">
                                        <thead>
                                            <tr>
                                                <th class="align-middle" style="width:40px;">
                                                    <input type="checkbox" id="select-all-cart-items" />
                                                </th>
                                                <th class="align-middle product-info-cell" style="min-width:320px;">Sản phẩm
                                                </th>
                                                <th class="align-middle text-end price" style="min-width:120px;">Giá</th>
                                                <th class="align-middle text-center quantity" style="min-width:160px;">Số
                                                    lượng</th>
                                                <th class="align-middle text-end total" style="min-width:120px;">Tổng cộng
                                                </th>
                                                <th class="align-middle text-end" style="width:60px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody class="list" id="cart-table-body">
                                            @foreach ($cartItems as $item)
                                                @php
                                                    $product = $item->product;
                                                    $variant = $item->productVariant;
                                                    $unitPrice = $item->current_price ?? ($item->productVariant ? $item->productVariant->current_price : $item->product->current_price);
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
                                                <tr class="cart-table-row btn-reveal-trigger @if ($stock < 1 || $item->is_discontinued) cart-item-out-of-stock @endif @if (isset($item->price_changed) && $item->price_changed) cart-item-price-changed @endif"
                                                    data-item-id="{{ $item->id }}" data-unit-price="{{ $unitPrice }}"
                                                    data-stock="{{ $stock }}" data-discontinued="{{ $item->is_discontinued ? 'true' : 'false' }}"
                                                    @if (isset($item->price_changed) && $item->price_changed) data-old-price="{{ $item->old_price }}" @endif>
                                                    <td class="align-middle text-center">
                                                        <input type="checkbox" class="cart-item-checkbox"
                                                            name="selected_items[]" value="{{ $item->id }}"
                                                            @if ($stock < 1 || $item->is_discontinued) disabled 
                                                                title="@if($item->is_discontinued) Sản phẩm này đã ngừng kinh doanh, không thể thanh toán @else Sản phẩm này đã hết hàng, không thể thanh toán @endif" @endif />
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
                                                                    style="font-weight:700;font-size:1.13rem;color:#23272f;margin-bottom:2px;line-height:1.3;max-width:220px;white-space:normal;overflow:hidden;">
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
                                                                    style="font-size:0.97rem;color:#7b7e85;display:flex;flex-direction:column;gap:2px;align-items:flex-start;flex-wrap:nowrap;">
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
                                                                    @if ($item->is_discontinued)
                                                                        <span class="badge bg-warning mt-1">Ngừng kinh doanh</span>
                                                                        <span class="text-warning small">Sản phẩm này đã ngừng kinh doanh, không thể thanh toán.</span>
                                                                    @elseif ($stock < 1)
                                                                        <span class="badge bg-danger mt-1">Hết hàng</span>
                                                                        <span class="text-danger small">Sản phẩm này đã hết
                                                                            hàng, vui lòng quay lại sau.</span>
                                                                    @elseif (isset($item->price_changed) && $item->price_changed)
                                                                        <span class="badge bg-info mt-1">Giá đã thay đổi</span>
                                                                        <span class="text-info small">Giá sản phẩm đã được cập nhật.</span>
                                                                        <button type="button" class="btn btn-sm btn-outline-info mt-1" 
                                                                            onclick="dismissPriceChange({{ $item->id }})"
                                                                            style="font-size:0.8rem;padding:2px 6px;">
                                                                            <i class="fa fa-check me-1"></i>Đã xem
                                                                        </button>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="price align-middle text-body fs-9 fw-semibold text-end"
                                                        style="font-size:1.08rem;">
                                                        @if($item->is_discontinued)
                                                            <span class="text-muted">--</span>
                                                        @else
                                                            @if (isset($item->price_changed) && $item->price_changed)
                                                                <div>
                                                                    <span class="text-decoration-line-through text-muted" style="font-size:0.9rem;">
                                                                        {{ number_format($item->old_price, 0, ',', '.') }}₫
                                                                    </span>
                                                                    <br>
                                                                    <span class="text-success fw-bold">
                                                                        {{ number_format($unitPrice, 0, ',', '.') }}₫
                                                                    </span>
                                                                </div>
                                                            @else
                                                                {{ number_format($unitPrice, 0, ',', '.') }}₫
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td class="quantity align-middle text-center">
                                                        <div class="tp-product-quantity mb-15 mr-15 d-flex justify-content-center align-items-center" style="gap:8px;">
                                                            <button type="button" class="qty-btn minus" @if ($stock < 1 || $item->is_discontinued) disabled @endif>-</button>
                                                            <input type="text" class="qty-input" value="{{ ($stock < 1 || $item->is_discontinued) ? 0 : $item->quantity }}" style="min-width:38px;max-width:54px;text-align:center;font-weight:600;" @if ($stock < 1 || $item->is_discontinued) disabled @endif />
                                                            <button type="button" class="qty-btn plus" @if ($stock < 1 || $item->is_discontinued) disabled @endif>+</button>
                                                        </div>
                                                    </td>
                                                    <td class="total align-middle fw-bold text-body-highlight text-end"
                                                        style="font-size:1.08rem;">
                                                        @if($item->is_discontinued)
                                                            <span class="text-muted">--</span>
                                                        @else
                                                            @if (isset($item->price_changed) && $item->price_changed)
                                                                <div>
                                                                    <span class="text-decoration-line-through text-muted" style="font-size:0.9rem;">
                                                                        {{ number_format($item->old_price * $item->quantity, 0, ',', '.') }}₫
                                                                    </span>
                                                                    <br>
                                                                    <span class="text-success fw-bold">
                                                                        {{ number_format($unitPrice * $item->quantity, 0, ',', '.') }}₫
                                                                    </span>
                                                                </div>
                                                            @else
                                                                {{ number_format($unitPrice * $item->quantity, 0, ',', '.') }}₫
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td class="align-middle white-space-nowrap text-end pe-0 ps-3">
                                                        <form action="{{ url('/shopping-cart/remove/' . $item->id) }}"
                                                            method="POST"
                                                            class="single-delete-form"
                                                            data-item-id="{{ $item->id }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm" style="color:#b0b3b8;"
                                                                title="Xóa"
                                                                onmouseover="this.style.color='#ef5350'"
                                                                onmouseout="this.style.color='#b0b3b8'">
                                                                <span class="fa-regular fa-trash-can"></span>
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
                            <div
                                style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.5rem;">
                                <h2 class="mb-0">Tóm tắt đơn hàng</h2>
                            </div>
                            <div class="summary-row">
                                <span class="label">Tổng phụ :</span>
                                <span class="value">{{ number_format($cartTotal ?? 0, 0, ',', '.') }}₫</span>
                            </div>
                            <div class="summary-row">
                                <span class="label">Giảm giá :</span>
                                <span class="value text-danger">-{{ number_format($discount ?? 0, 0, ',', '.') }}₫</span>
                            </div>
                            <div class="summary-row">
                                <span class="label">Phí vận chuyển :</span>
                                <span class="value">{{ number_format($shipping ?? 0, 0, ',', '.') }}₫</span>
                            </div>
                            <div class="voucher-group mb-3">
                                <select class="form-select" id="voucher-select">
                                    <option value="">Chọn voucher</option>
                                </select>
                                <input class="form-control" type="text" id="voucher-input"
                                    placeholder="Nhập mã voucher" />
                                <button class="btn btn-primary" id="apply-voucher-btn">Áp dụng</button>
                            </div>
                            <div class="border-y">
                                <div class="summary-row" style="margin-bottom:0;">
                                    <span class="total-label">Tổng cộng :</span>
                                    <span class="total-value">{{ number_format($total ?? 0, 0, ',', '.') }}₫</span>
                                </div>
                            </div>
                            <button type="button" id="btn-checkout" class="btn-checkout w-100"
                                style="margin-top:1.2rem;">
                                Thanh toán <span class="fa-solid fa-chevron-right ms-1 fs-10"></span>
                            </button>
                        </div>
                    </div>
                @else
                    <div class="col-12">
                        <div class="cart-empty text-center p-5 bg-white rounded-3 d-flex flex-column align-items-center justify-content-center" style="min-height:340px; box-shadow:0 4px 24px rgba(44,62,80,0.07); border:1.5px solid #e9ecef; margin-bottom: 80px;">
                            <i class="fa-light fa-cart-shopping" style="font-size: 5rem; color: #dee2e6;"></i>
                            <h4 class="mt-4">Giỏ hàng của bạn còn trống</h4>
                            <p class="text-muted">Cùng khám phá hàng ngàn sản phẩm tuyệt vời tại Aurora nhé!</p>
                            <a href="{{ route('home') }}" class="tp-btn">Bắt đầu mua sắm</a>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cartTableBody = document.getElementById('cart-table-body');
            if (!cartTableBody) return;

            function formatCurrency(num) {
                return num.toLocaleString('vi-VN') + '₫';
            }

            function updateLineTotal(row, qty) {
                const unitPrice = parseInt(row.getAttribute('data-unit-price'), 10) || 0;
                const totalCell = row.querySelector('.total');
                if (totalCell) {
                    // Kiểm tra nếu có giá thay đổi
                    const priceChanged = row.classList.contains('cart-item-price-changed');
                    if (priceChanged) {
                        // Hiển thị giá cũ và mới
                        const oldPrice = parseInt(row.getAttribute('data-old-price'), 10) || unitPrice;
                        const oldTotal = oldPrice * qty;
                        const newTotal = unitPrice * qty;
                        totalCell.innerHTML = `
                            <div>
                                <span class="text-decoration-line-through text-muted" style="font-size:0.9rem;">
                                    ${formatCurrency(oldTotal)}
                                </span>
                                <br>
                                <span class="text-success fw-bold">
                                    ${formatCurrency(newTotal)}
                                </span>
                            </div>
                        `;
                    } else {
                        totalCell.textContent = formatCurrency(unitPrice * qty);
                    }
                }
            }

            function updateCartSummary() {
                let subtotal = 0;
                let checkedCount = 0;
                document.querySelectorAll('tr[data-item-id]').forEach(row => {
                    const checkbox = row.querySelector('.cart-item-checkbox');
                    if (!checkbox || !checkbox.checked) return;
                    
                    // Kiểm tra sản phẩm ngừng kinh doanh
                    const isDiscontinued = row.getAttribute('data-discontinued') === 'true';
                    if (isDiscontinued) return; // Bỏ qua sản phẩm ngừng kinh doanh
                    
                    const unitPrice = parseInt(row.getAttribute('data-unit-price'), 10) || 0;
                    const qty = parseInt(row.querySelector('.qty-input').value, 10) || 1;
                    subtotal += unitPrice * qty;
                    checkedCount++;
                });

                const summaryBox = document.querySelector('.cart-summary-box') || document.querySelector(
                    '.card-body');
                if (!summaryBox) return;

                let subtotalEl, discountEl, shippingEl;
                summaryBox.querySelectorAll('.summary-row').forEach(row => {
                    const label = row.querySelector('.label');
                    const value = row.querySelector('.value');
                    if (!label || !value) return;
                    if (label.textContent.includes('Tổng phụ')) subtotalEl = value;
                    if (label.textContent.includes('Giảm giá')) discountEl = value;
                    if (label.textContent.includes('Phí vận chuyển')) shippingEl = value;
                });

                if (checkedCount === 0) {
                    if (subtotalEl) subtotalEl.textContent = formatCurrency(0);
                    if (discountEl) discountEl.textContent = '-' + formatCurrency(0);
                    if (shippingEl) shippingEl.textContent = formatCurrency(0);
                    const totalEl = summaryBox.querySelector('.total-value');
                    if (totalEl) totalEl.textContent = formatCurrency(0);
                    return;
                }

                if (subtotalEl) subtotalEl.textContent = formatCurrency(subtotal);
                let discount = 0;
                if (discountEl) {
                    discountEl.textContent = '-' + formatCurrency(discount);
                }
                let shipping = 0; // Luôn là 0
                if (shippingEl) {
                    shippingEl.textContent = formatCurrency(0);
                }
                const totalEl = summaryBox.querySelector('.total-value');
                if (totalEl) {
                    const total = subtotal - discount + shipping;
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

            // Function để ẩn thông báo giá thay đổi
            window.dismissPriceChange = function(itemId) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                fetch(`/shopping-cart/dismiss-price-change/${itemId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const row = document.querySelector(`tr[data-item-id="${itemId}"]`);
                        if (row) {
                            row.classList.remove('cart-item-price-changed');
                            const priceChangeElements = row.querySelectorAll('.badge.bg-info, .text-info.small, .btn-outline-info');
                            priceChangeElements.forEach(el => el.remove());
                            
                            // Cập nhật lại tổng tiền
                            updateCartSummary();
                        }
                        
                        if (window.toastr && typeof toastr.success === 'function') {
                            toastr.success(data.message || 'Đã ẩn thông báo giá thay đổi.');
                        }
                    } else {
                        if (window.toastr && typeof toastr.error === 'function') {
                            toastr.error(data.message || 'Có lỗi xảy ra.');
                        }
                    }
                })
                .catch(error => {
                    console.error('Lỗi khi ẩn thông báo giá thay đổi:', error);
                    if (window.toastr && typeof toastr.error === 'function') {
                        toastr.error('Không thể ẩn thông báo. Vui lòng thử lại sau.');
                    }
                });
            };

            // Kiểm tra và hiển thị thông báo giá thay đổi
            const priceChangedItems = document.querySelectorAll('.cart-item-price-changed');
            if (priceChangedItems.length > 0) {
                if (window.toastr && typeof toastr.info === 'function') {
                    toastr.info(`Có ${priceChangedItems.length} sản phẩm có giá đã thay đổi và đã được cập nhật tự động.`);
                } else {
                    alert(`Có ${priceChangedItems.length} sản phẩm có giá đã thay đổi và đã được cập nhật tự động.`);
                }
            }

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

                    // Kiểm tra sản phẩm ngừng kinh doanh
                    const discontinuedItems = [];
                    checked.forEach(checkbox => {
                        const row = checkbox.closest('tr');
                        const isDiscontinued = row.getAttribute('data-discontinued') === 'true';
                        if (isDiscontinued) {
                            const productName = row.querySelector('.product-name a, .product-name span').textContent.trim();
                            discontinuedItems.push(productName);
                        }
                    });

                    if (discontinuedItems.length > 0) {
                        const message = 'Không thể thanh toán vì các sản phẩm sau đã ngừng kinh doanh: ' + discontinuedItems.join(', ');
                        if (window.toastr && typeof toastr.error === 'function') {
                            toastr.error(message);
                        } else {
                            alert(message);
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


        });


    </script>
@endsection


