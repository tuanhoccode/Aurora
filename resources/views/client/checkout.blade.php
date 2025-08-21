@extends('client.layouts.default')

@section('title', 'Thanh toán')

@section('content')
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --primary-light: #eef2ff;
            --success: #10b981;
            --success-light: #d1fae5;
            --warning: #f59e0b;
            --warning-light: #fef3c7;
            --danger: #ef4444;
            --danger-light: #fee2e2;
            --text-dark: #1f2937;
            --text-muted: #6b7280;
            --text-light: #9ca3af;
            --border: #e5e7eb;
            --border-light: #f3f4f6;
            --shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            --shadow-hover: 0 8px 25px rgba(0, 0, 0, 0.12);
            --radius: 16px;
            --radius-sm: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --input-height: 48px;
            --input-font-size: 0.95rem;
        }

        /* Main Layout - Shopee Style */
        .checkout__section {
            background: #f5f5f5;
            padding: 20px 0;
            min-height: 100vh;
        }

        .checkout__container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
            display: flex;
            gap: 20px;
        }

        /* Force 2-column layout on larger screens */
        @media (min-width: 1200px) {
            .checkout__container {
                flex-direction: row !important;
            }
            
            .checkout__left-column {
                flex: 2;
                min-width: 0;
            }
            
            .checkout__right-column {
                flex: 1;
                max-width: 480px;
            }
        }

        /* Improved layout for better visual hierarchy */
        .checkout__left-column {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            flex: 2;
            min-width: 0;
        }

        .checkout__right-column {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            position: sticky;
            top: 2rem;
            height: fit-content;
            width: 100%;
            max-width: 480px;
            flex-shrink: 0;
        }

        /* Checkout Blocks - Shopee Style */
        .checkout__block {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
            padding: 20px;
            border: 1px solid #f0f0f0;
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .checkout__block:hover {
            box-shadow: 0 2px 8px 0 rgba(0, 0, 0, 0.1);
        }

        .checkout__block-title {
            font-size: 18px;
            font-weight: 500;
            color: #222;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            border-bottom: 1px solid #f0f0f0;
        }

        .checkout__block-title::before {
            content: '';
            width: 4px;
            height: 20px;
            background: #ee4d2d;
            border-radius: 2px;
        }

        /* Address - Shopee Style */
        .checkout__address-selection {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            background: #fff7f2;
            border-radius: 8px;
            padding: 20px;
            border: 1px solid #f0f0f0;
            transition: all 0.2s ease;
            position: relative;
            margin-bottom: 16px;
        }

        .checkout__address-selection:hover {
            border-color: #ee4d2d;
            box-shadow: 0 2px 8px 0 rgba(238, 77, 45, 0.1);
        }

        .checkout__address-selection label {
            font-size: 14px;
            font-weight: 400;
            color: #222;
            line-height: 1.5;
            flex: 1;
        }

        /* Cart Items - Shopee Style */
        .checkout__cart-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .checkout__cart-header,
        .checkout__cart-item {
            display: grid;
            grid-template-columns: 80px 2fr 1fr 1fr 1fr;
            align-items: center;
            gap: 16px;
            padding: 16px;
            font-size: 14px;
            border: 1px solid #f0f0f0;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .checkout__cart-header {
            font-weight: 500;
            color: #666;
            border-bottom: 1px solid #f0f0f0;
            text-transform: none;
            font-size: 13px;
            letter-spacing: 0;
            padding-bottom: 16px;
            margin-bottom: 16px;
        }

        .checkout__cart-item {
            background: #fff;
            border-radius: 4px;
            margin-bottom: 12px;
            padding: 16px;
            transition: all 0.2s ease;
            border: 1px solid #f0f0f0;
        }

        .checkout__cart-item:hover {
            background: #fff7f2;
            border-color: #f0f0f0;
        }

        .checkout__cart-item img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #f0f0f0;
            transition: all 0.2s ease;
        }

        .checkout__cart-item p {
            margin: 0;
            font-size: 14px;
            color: #222;
            font-weight: 500;
            line-height: 1.4;
        }

        .checkout__cart-item small {
            color: #666;
            font-size: 12px;
            display: block;
            margin-top: 4px;
        }

        /* Note Textarea - Shopee Style */
        .checkout__note {
            border-radius: 4px;
            border: 1px solid #f0f0f0;
            background: #fff;
            font-size: 14px;
            padding: 12px;
            width: 100%;
            min-height: 80px;
            resize: vertical;
            transition: all 0.2s ease;
            font-family: inherit;
            margin-top: 16px;
        }

        .checkout__note:focus {
            border-color: #ee4d2d;
            outline: none;
            box-shadow: 0 0 0 2px rgba(238, 77, 45, 0.1);
            background: #fff;
        }

        /* Shipping & Payment Methods - Shopee Style */
        .checkout__shipping-method .form-check,
        .checkout__payment-method .form-check {
            border-radius: 4px;
            border: 1px solid #f0f0f0;
            padding: 20px;
            margin-bottom: 16px;
            cursor: pointer;
            position: relative;
            display: flex;
            align-items: flex-start;
            gap: 16px;
            transition: all 0.2s ease;
            background: #fff;
        }

        .checkout__shipping-method .form-check:hover,
        .checkout__shipping-method .form-check.selected,
        .checkout__payment-method .form-check:hover,
        .checkout__payment-method .form-check.selected {
            border-color: #ee4d2d;
            box-shadow: 0 2px 8px 0 rgba(238, 77, 45, 0.1);
            background: #fff7f2;
        }

        .checkout__shipping-method .form-check-input,
        .checkout__payment-method .form-check-input {
            width: 18px;
            height: 18px;
            margin: 2px 12px 0 0;
            flex-shrink: 0;
            border: 1px solid #d9d9d9;
            border-radius: 50%;
            transition: all 0.2s ease;
        }

        .checkout__shipping-method .form-check-input:checked,
        .checkout__payment-method .form-check-input:checked {
            background-color: #ee4d2d;
            border-color: #ee4d2d;
            box-shadow: 0 0 0 2px rgba(238, 77, 45, 0.2);
        }

        .checkout__shipping-method .form-check-label,
        .checkout__payment-method .form-check-label {
            font-size: 16px;
            font-weight: 500;
            color: #222;
            flex: 1;
            margin-bottom: 8px;
        }

        .checkout__shipping-method p,
        .checkout__payment-method p {
            margin: 4px 0 0;
            font-size: 13px;
            color: #666;
            line-height: 1.5;
        }

        /* Coupon Section - Shopee Style */
        .coupon-section {
            background: #fff7f2;
            border: 1px solid #f0f0f0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }

        .coupon-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #ee4d2d, #ff6b4a);
        }

        .coupon-applied {
            background: #f0fdf4;
            border: 1px solid #00bfa5;
            border-radius: 4px;
            padding: 16px;
            margin-bottom: 16px;
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: center;
            animation: slideInDown 0.3s ease-out;
        }

        .coupon-applied::before {
            content: '✓';
            position: absolute;
            top: -8px;
            left: 16px;
            width: 20px;
            height: 20px;
            background: #00bfa5;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }

        .coupon-applied .coupon-info {
            flex: 1;
        }

        .coupon-applied .coupon-code {
            font-size: 14px;
            font-weight: 500;
            color: #00bfa5;
            margin-bottom: 4px;
        }

        .coupon-applied .coupon-discount {
            font-size: 13px;
            color: #065f46;
            font-weight: 500;
        }

        .coupon-applied .coupon-title {
            font-size: 12px;
            color: #666;
            margin-top: 2px;
        }

        .coupon-input-section {
            background: #fff;
            border-radius: 4px;
            padding: 20px;
            border: 1px solid #f0f0f0;
            margin-bottom: 20px;
        }

        .coupon-input-title {
            font-size: 14px;
            font-weight: 500;
            color: #222;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .checkout__coupon-group {
            display: flex;
            gap: 16px;
            align-items: center;
            margin-bottom: 16px;
        }

        .checkout__coupon-group .form-control {
            border-radius: 4px;
            border: 1px solid #f0f0f0;
            font-size: 14px;
            height: 40px;
            padding: 8px 12px;
            flex: 1;
            transition: all 0.2s ease;
            background: #fff;
        }

        .checkout__coupon-group .form-control:focus {
            border-color: #ee4d2d;
            outline: none;
            box-shadow: 0 0 0 2px rgba(238, 77, 45, 0.1);
            background: #fff;
        }

        .checkout__coupon-group .form-control::placeholder {
            color: #999;
            font-style: normal;
        }

        .coupon-available-section {
            background: #fff;
            border-radius: 4px;
            padding: 20px;
            border: 1px solid #f0f0f0;
        }

        .coupon-available-title {
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .coupon-card {
            background: #fff;
            border: 1px solid #f0f0f0;
            border-radius: 4px;
            padding: 16px;
            margin-bottom: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
        }

        .coupon-card:hover {
            border-color: #ee4d2d;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px 0 rgba(238, 77, 45, 0.1);
        }

        .coupon-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, #ee4d2d, #ff6b4a);
        }

        .coupon-card .coupon-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .coupon-card .coupon-code {
            font-size: 16px;
            font-weight: 500;
            color: #ee4d2d;
        }

        .coupon-card .coupon-discount {
            font-size: 14px;
            font-weight: 500;
            color: #00bfa5;
        }

        .coupon-card .coupon-details {
            font-size: 13px;
            color: #666;
            line-height: 1.4;
        }

        .coupon-card .coupon-expiry {
            font-size: 12px;
            color: #f59e0b;
            margin-top: 4px;
        }

        .coupon-empty {
            text-align: center;
            padding: 32px;
            color: #666;
        }

        .coupon-empty i {
            font-size: 48px;
            opacity: 0.3;
            margin-bottom: 16px;
            display: block;
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Buttons - Shopee Style */
        .checkout__coupon-group .btn,
        .checkout__btn-main,
        .checkout__submit-btn {
            border-radius: 4px;
            font-size: 14px;
            padding: 8px 16px;
            background: #ee4d2d;
            color: #fff;
            border: none;
            transition: all 0.2s ease;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 500;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            min-width: 120px;
        }

        .checkout__coupon-group .btn:hover,
        .checkout__btn-main:hover,
        .checkout__submit-btn:hover {
            background: #d7381d;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px 0 rgba(238, 77, 45, 0.3);
        }

        .checkout__submit-btn {
            width: 100%;
            font-size: 18px;
            padding: 16px;
            height: auto;
            margin-top: 20px;
            background: #ee4d2d;
            border-radius: 4px;
            position: relative;
            overflow: hidden;
            transition: all 0.2s ease;
        }

        .checkout__submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }

        .checkout__submit-btn:hover {
            background: #d7381d;
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(238, 77, 45, 0.3);
        }

        .checkout__submit-btn:hover::before {
            left: 100%;
        }

        /* Summary - Shopee Style */
        .checkout__summary-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .checkout__summary-list li {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            margin-bottom: 12px;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.2s ease;
        }

        .checkout__summary-list .total {
            font-size: 18px;
            font-weight: 600;
            color: #ee4d2d;
            padding: 20px;
            border-radius: 4px;
            border: 2px solid #ee4d2d;
            background: #fff7f2;
            margin-top: 20px;
            
        }

        .alert {
            border-radius: 4px;
            padding: 16px;
            margin-bottom: 20px;
            font-size: 14px;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .alert::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
        }

        .alert-success {
            background: #f0fdf4;
            color: #065f46;
        }

        .alert-success::before {
            background: #00bfa5;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .alert-danger::before {
            background: #ef4444;
        }

        /* Modal Styles - Shopee Style */
        .modal-content {
            border: none;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .modal-content:hover {
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            background: linear-gradient(135deg, #ee4d2d, #ff6b4a);
            border-bottom: none;
            padding: 24px 24px 20px;
            position: relative;
            overflow: hidden;
        }

        .modal-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .modal-title {
            color: #fff;
            font-weight: 700;
            font-size: 20px;
            margin: 0;
            position: relative;
            z-index: 1;
        }

        .modal-title i {
            color: #fff;
            margin-right: 8px;
        }

        .btn-close {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            font-size: 18px;
            color: #fff;
            transition: all 0.3s ease;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 1;
        }

        .btn-close:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1) rotate(90deg);
        }

        .modal-body {
            padding: 24px;
            background: #fff;
        }

        .modal-footer {
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
            padding: 20px 24px;
            display: flex;
            justify-content: flex-end;
            gap: 16px;
        }

        .modal-footer .btn {
            border-radius: 8px;
            font-weight: 600;
            padding: 12px 24px;
            font-size: 14px;
            transition: all 0.3s ease;
            min-width: 120px;
            position: relative;
            overflow: hidden;
        }

        .modal-footer .btn-secondary {
            background: #6c757d;
            border: none;
            color: #fff;
            box-shadow: 0 2px 8px rgba(108, 117, 125, 0.3);
        }

        .modal-footer .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(108, 117, 125, 0.4);
        }

        .modal-footer .btn-primary {
            background: #ee4d2d;
            border: none;
            color: #fff;
            box-shadow: 0 2px 8px rgba(238, 77, 45, 0.3);
        }

        .modal-footer .btn-primary:hover {
            background: #d7381d;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(238, 77, 45, 0.4);
        }

        .modal-footer .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.6s ease;
        }

        .modal-footer .btn-primary:hover::before {
            left: 100%;
        }

        /* Address Modal Specific - Shopee Style */
        .address-item {
            border-color: #f0f0f0 !important;
            transition: all 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
        }

        .address-item:hover {
            border-color: #ee4d2d !important;
            box-shadow: 0 4px 20px rgba(238, 77, 45, 0.15);
            transform: translateY(-2px);
        }

        .address-item .card {
            border-radius: 12px;
            border: 2px solid #f0f0f0;
            transition: all 0.3s ease;
            background: #fff;
        }

        .address-item:hover .card {
            border-color: #ee4d2d;
            background: #fff7f2;
        }

        .address-item .form-check-input {
            width: 18px !important;
            height: 18px !important;
            margin-top: 2px;
            border: 2px solid #d9d9d9;
            transition: all 0.3s ease;
        }

        .address-item .form-check-input:checked {
            background-color: #ee4d2d !important;
            border-color: #ee4d2d !important;
            box-shadow: 0 0 0 3px rgba(238, 77, 45, 0.2);
        }

        .address-item .form-check-input:focus {
            box-shadow: 0 0 0 3px rgba(238, 77, 45, 0.25) !important;
        }

        .address-item .btn-outline-primary {
            border-color: #ee4d2d;
            color: #ee4d2d;
            background: transparent;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .address-item .btn-outline-primary:hover {
            background: #ee4d2d;
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(238, 77, 45, 0.3);
        }

        .address-item .btn-outline-danger {
            border-color: #ef4444;
            color: #ef4444;
            background: transparent;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .address-item .btn-outline-danger:hover {
            background: #ef4444;
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
        }

        .address-item .btn-primary {
            background: linear-gradient(135deg, #ee4d2d, #ff6b4a);
            border: none;
            color: #fff;
            border-radius: 12px;
            font-weight: 700;
            padding: 16px 32px;
            font-size: 16px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(238, 77, 45, 0.3);
        }

        .address-item .btn-primary:hover {
            background: linear-gradient(135deg, #d7381d, #ee4d2d);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(238, 77, 45, 0.4);
        }

        .address-item .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.6s ease;
        }

        .address-item .btn-primary:hover::before {
            left: 100%;
        }

        /* Utility Classes - Shopee Style */
        .text-danger {
            color: #ef4444 !important;
            font-size: 13px;
            font-weight: 500;
        }

        .text-success {
            color: #00bfa5 !important;
        }

        .text-muted {
            color: #666 !important;
        }

        .btn-outline-primary {
            border-color: #ee4d2d;
            color: #ee4d2d;
            background: transparent;
            transition: all 0.2s ease;
            border-radius: 4px;
            padding: 8px 16px;
        }

        .btn-outline-primary:hover {
            background: #ee4d2d;
            color: #fff;
            border-color: #ee4d2d;
            transform: translateY(-1px);
        }

        .btn-outline-danger {
            border-color: #ef4444;
            color: #ef4444;
            background: transparent;
            transition: all 0.2s ease;
            border-radius: 4px;
            padding: 8px 16px;
        }

        .btn-outline-danger:hover {
            background: #ef4444;
            color: #fff;
            border-color: #ef4444;
            transform: translateY(-1px);
        }

        /* Enhanced Coupon Card Styles - Shopee Style */
        .coupon-card.applied {
            background: #f0fdf4;
            border-color: #00bfa5;
            position: relative;
        }

        .coupon-card.applied::after {
            content: '✓';
            position: absolute;
            top: 10px;
            right: 10px;
            width: 24px;
            height: 24px;
            background: #00bfa5;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }

        .coupon-card.applied .coupon-code {
            color: #00bfa5;
        }

        /* Coupon Modal Styles - Shopee Style */
        .bg-gradient-primary {
            background: #ee4d2d !important;
        }

        .coupon-modal-card {
            background: #fff;
            border: 2px solid #f0f0f0;
            border-radius: 12px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 16px;
            position: relative;
            overflow: hidden;
        }

        .coupon-modal-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, #ee4d2d, #ff6b4a);
            transition: all 0.3s ease;
        }

        .coupon-modal-card:hover {
            border-color: #ee4d2d;
            box-shadow: 0 4px 20px rgba(238, 77, 45, 0.15);
            transform: translateY(-2px);
        }

        .coupon-modal-card:hover::before {
            width: 8px;
        }

        .coupon-modal-card.applied {
            background: #f0fdf4;
            border-color: #00bfa5;
            border-left: 6px solid #00bfa5;
        }

        .coupon-modal-card.applied::before {
            background: linear-gradient(180deg, #00bfa5, #00a08a);
        }

        .coupon-modal-card.applied::after {
            content: '✓';
            position: absolute;
            top: 16px;
            right: 16px;
            width: 24px;
            height: 24px;
            background: #00bfa5;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: bold;
            box-shadow: 0 2px 8px rgba(0, 191, 165, 0.3);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .coupon-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .coupon-modal-code {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .coupon-code-text {
            font-size: 16px;
            font-weight: 500;
            color: #ee4d2d;
            background: #f3f4f6;
            padding: 4px 8px;
            border-radius: 4px;
        }

        .coupon-modal-card.applied .coupon-code-text {
            color: #00bfa5;
            background: #d1fae5;
        }

        .coupon-modal-discount {
            font-size: 14px;
            font-weight: 500;
            color: #00bfa5;
            background: #f0fdf4;
            padding: 4px 8px;
            border-radius: 4px;
        }

        .coupon-modal-details {
            margin-bottom: 12px;
            line-height: 1.5;
        }

        .coupon-modal-details .discount-type {
            font-size: 14px;
            font-weight: 500;
            color: #222;
            display: block;
            margin-bottom: 4px;
        }

        .coupon-modal-details .coupon-title {
            font-size: 13px;
            color: #666;
            display: block;
        }

        .coupon-modal-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
        }

        .coupon-modal-footer .coupon-expiry {
            color: #f59e0b;
            font-weight: 500;
        }

        .coupon-modal-footer .apply-text {
            color: #ee4d2d;
            font-weight: 500;
        }

        .coupon-modal-card.applied .apply-text {
            color: #00bfa5;
        }

        /* Responsive improvements for coupon section - Shopee Style */
        @media (max-width: 768px) {
            .coupon-section {
                padding: 16px;
            }

            .coupon-applied {
                flex-direction: column;
                gap: 12px;
                text-align: center;
            }

            .coupon-applied::before {
                left: 50%;
                transform: translateX(-50%);
            }

            .checkout__coupon-group {
                flex-direction: column;
                gap: 12px;
            }

            .coupon-card {
                padding: 12px;
            }

            .coupon-card .coupon-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }

            /* Modal coupon responsive */
            .coupon-modal-card {
                padding: 1rem;
            }

            .coupon-modal-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.6rem;
            }

            .coupon-modal-footer {
                flex-direction: column;
                align-items: flex-start;
                gap: 6px;
            }

            .coupon-code-text {
                font-size: 14px;
                padding: 2px 6px;
            }

            .coupon-modal-discount {
                font-size: 13px;
                padding: 2px 6px;
            }
        }

        /* Responsive Design - Shopee Style */
        @media (min-width: 1400px) {
            .checkout__container {
                max-width: 1400px;
            }
        }

        @media (max-width: 1399px) {
            .checkout__container {
                max-width: 1400px;
            }
        }

        @media (max-width: 1199px) {
            .checkout__container {
                flex-direction: column;
            }

            .checkout__right-column {
                position: static;
                width: 100%;
                max-width: 100%;
            }

            .checkout__left-column,
            .checkout__right-column {
                width: 100%;
                max-width: 100%;
            }
        }

        @media (max-width: 992px) {
            .checkout__container {
                flex-direction: column;
            }

            .checkout__right-column {
                position: static;
                width: 100%;
                max-width: 100%;
                gap: 20px;
                position: sticky;
                top: 20px;
                height: fit-content;
            }
        }

        @media (max-width: 768px) {
            .modal-dialog {
                margin: 1rem;
                max-width: calc(100% - 2rem);
            }

            .address-item .row {
                flex-direction: column;
            }

            .address-item .col-auto:last-child {
                margin-top: 1rem;
                align-self: stretch;
            }

            .address-item .col-auto:last-child .btn {
                width: 100%;
            }

            .modal-footer {
                flex-direction: column;
            }

            .modal-footer .btn {
                width: 100%;
                margin-bottom: 0.5rem;
            }
        }

        @media (max-width: 600px) {
            .checkout__section {
                padding: 1rem 0.5rem;
            }

            .checkout__block {
                padding: 1.2rem;
                margin-bottom: 1rem;
            }

            .checkout__coupon-group {
                flex-direction: column;
                align-items: stretch;
            }

            .checkout__address-selection {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .checkout__btn-main,
            .checkout__submit-btn {
                width: 100%;
            }

            .checkout__cart-header,
            .checkout__cart-item {
                grid-template-columns: 60px 1fr 1fr 1fr;
                gap: 0.8rem;
            }

            .checkout__cart-header span:nth-child(4),
            .checkout__cart-item span:nth-child(4) {
                display: none;
            }
        }
    </style>

    <section class="checkout__section">
        <div class="checkout__container">
            <div class="checkout__left-column">
                <!-- 1. Địa chỉ nhận hàng -->
                <div class="checkout__block">
                    <div class="checkout__block-title">📍 Địa chỉ nhận hàng</div>
                    <div class="checkout__address-selection">
                        <div>
                            <label id="address_label">
                                @if (auth()->check() && $addresses->count() > 0)
                                    @php
                                        $selectedAddress = $addresses->firstWhere(
                                            'id',
                                            old(
                                                'selected_address',
                                                session('checkout_address_id', $defaultAddress->id ?? ''),
                                            ),
                                        );
                                    @endphp
                                    {{ $selectedAddress ? ($selectedAddress->fullname ?? 'Chưa cung cấp họ tên') . ' (+84) ' . ($selectedAddress->phone_number && preg_match('/^0[0-9]{9}$/', $selectedAddress->phone_number) ? $selectedAddress->phone_number : 'Số điện thoại không hợp lệ') . ' - ' . ($selectedAddress->street ? $selectedAddress->street . ', ' : '') . ($selectedAddress->ward ? $selectedAddress->ward . ', ' : '') . ($selectedAddress->district ? $selectedAddress->district . ', ' : '') . ($selectedAddress->province ?? 'Chưa cung cấp tỉnh/thành phố') : 'Chưa chọn địa chỉ' }}
                                    @if ($selectedAddress && $selectedAddress->is_default)
                                        <span class="badge bg-primary">Mặc định</span>
                                    @endif
                                @else
                                    Chưa có địa chỉ
                                @endif
                            </label>
                        </div>
                        <button class="checkout__btn-main" data-bs-toggle="modal" data-bs-target="#addressModal">Thay
                            đổi</button>
                    </div>
                    @error('selected_address')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- 2. Sản phẩm đã chọn -->
                <div class="checkout__block">
                    <div class="checkout__block-title">🛍️ Sản phẩm đã chọn ({{ $cartItems->sum('quantity') }} sản phẩm)
                    </div>
                    <ul class="checkout__cart-list">
                        <li class="checkout__cart-header">
                            <span></span>
                            <span>Sản phẩm</span>
                            <span>Đơn giá</span>
                            <span>Số lượng</span>
                            <span>Thành tiền</span>
                        </li>
                        @foreach ($cartItems->groupBy('shop_id') as $shopId => $items)
                            @foreach ($items as $item)
                                @php
                                    $product = $item->product;
                                    $variant = $item->productVariant;
                                    $unitPrice = $item->price_at_time;

                                    // Function to get attribute value
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

                                    // Get the correct image for variant or product
                                    if ($variant) {
                                        if (!empty($variant->img)) {
                                            $img = asset('storage/' . $variant->img);
                                        } elseif ($variant->images && $variant->images->count() > 0) {
                                            $img = asset('storage/' . $variant->images->first()->url);
                                        } else {
                                            $img = $product->image_url ?? asset('assets/img/product/placeholder.jpg');
                                        }
                                    } else {
                                        $img = $product->image_url ?? asset('assets/img/product/placeholder.jpg');
                                    }
                                @endphp
                                <li class="checkout__cart-item">
                                    <img src="{{ $img }}"
                                        alt="{{ $product->name ?? 'Sản phẩm ' . $item->product_id }}">
                                    <div>
                                        <p>{{ $product->name ?? 'Sản phẩm ' . $item->product_id }}</p>
                                        @if ($variant)
                                            <small>
                                                @if ($size)
                                                    Kích thước: {{ $size }}
                                                @endif
                                                @if ($color)
                                                    Màu: {{ $color }}
                                                @endif
                                                @if (!$size && !$color)
                                                    Loại: {{ $item->variant_name ?? 'N/A' }}
                                                @endif
                                            </small>
                                        @endif
                                    </div>
                                    <span>{{ number_format($unitPrice ?? $product->price) }}</span>
                                    <span>{{ $item->quantity }}</span>
                                    <span>{{ number_format(($unitPrice ?? $product->price) * $item->quantity) }}</span>
                                </li>
                            @endforeach
                        @endforeach
                    </ul>

                    <!-- Lời nhắn -->
                    <div style="margin-top: 1.5rem;">
                        <label for="note"
                            style="font-weight: 600; color: var(--text-dark); margin-bottom: 0.5rem; display: block;">💬 Lời
                            nhắn cho người bán:</label>
                        <form action="{{ route('checkout.update') }}" method="POST">
                            @csrf
                            <textarea name="note" id="note" class="checkout__note"
                                placeholder="Nhập lưu ý cho người bán (không bắt buộc)..." onchange="this.form.submit()">{{ old('note', session('note', '')) }}</textarea>
                            @error('note')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </form>
                    </div>
                </div>
            </div>

            <div class="checkout__right-column">
                <!-- 3. Phương thức vận chuyển -->
                <div class="checkout__block checkout__shipping-method">
                    <div class="checkout__block-title">🚚 Phương thức vận chuyển</div>
                    @php
                        // Danh sách tỉnh lân cận Hà Nội
                        $nearbyProvinces = ['Hà Nội', 'Bắc Ninh', 'Hưng Yên', 'Hải Dương', 'Hải Phòng', 'Quảng Ninh'];
                        $centralProvinces = [
                            'Thanh Hóa',
                            'Nghệ An',
                            'Hà Tĩnh',
                            'Quảng Bình',
                            'Quảng Trị',
                            'Thừa Thiên Huế',
                            'Đà Nẵng',
                            'Quảng Nam',
                            'Quảng Ngãi',
                            'Bình Định',
                            'Phú Yên',
                            'Khánh Hòa',
                            'Ninh Thuận',
                            'Bình Thuận',
                        ];
                        $southProvinces = [
                            'TP Hồ Chí Minh',
                            'Bình Dương',
                            'Đồng Nai',
                            'Bà Rịa - Vũng Tàu',
                            'Long An',
                            'Tiền Giang',
                            'Bến Tre',
                            'Trà Vinh',
                            'Vĩnh Long',
                            'Đồng Tháp',
                            'An Giang',
                            'Kiên Giang',
                            'Cần Thơ',
                            'Hậu Giang',
                            'Sóc Trăng',
                            'Bạc Liêu',
                            'Cà Mau',
                        ];

                        $shopProvince = 'Hà Nội';
                        $destinationProvince = $selectedAddress->province ?? 'Hà Nội';

                        // Logic phí vận chuyển
                        $normalShippingFee = 16500; // Giao thường: 16.500 VNĐ cho tất cả khu vực
                        $fastShippingFee = in_array($destinationProvince, $nearbyProvinces)
                            ? 50000
                            : (in_array($destinationProvince, $centralProvinces) ||
                            in_array($destinationProvince, $southProvinces)
                                ? 50000
                                : 60000);

                        // Logic thời gian giao hàng
                        $normalShippingDates =
                            \Carbon\Carbon::today()->addDays(2)->format('d/m/Y') .
                            ' - ' .
                            \Carbon\Carbon::today()->addDays(4)->format('d/m/Y');
                        $fastShippingDates = in_array($destinationProvince, $nearbyProvinces)
                            ? 'Trong 4 giờ nếu đặt trước 16:00'
                            : \Carbon\Carbon::today()->addDays(1)->format('d/m/Y') .
                                ' - ' .
                                \Carbon\Carbon::today()->addDays(2)->format('d/m/Y');

                        $shippingFee =
                            old('shipping_type', session('shipping_type', 'thường')) === 'thường'
                                ? $normalShippingFee
                                : $fastShippingFee;
                    @endphp
                    <form action="{{ route('checkout.update') }}" method="POST" id="shippingForm">
                        @csrf
                        <div
                            class="form-check {{ old('shipping_type', session('shipping_type', 'thường')) === 'thường' ? 'selected' : '' }}">
                            <input class="form-check-input" type="radio" id="normal_shipping" name="shipping_type"
                                value="thường"
                                {{ old('shipping_type', session('shipping_type', 'thường')) === 'thường' ? 'checked' : '' }}
                                onchange="this.form.submit()" required>
                            <div>
                                <label class="form-check-label" for="normal_shipping">
                                    Giao hàng thường - ₫{{ number_format($normalShippingFee) }}
                                </label>
                                <p class="text-muted small">
                                    Dự kiến: {{ $normalShippingDates }}
                                </p>
                            </div>
                        </div>
                        <div
                            class="form-check {{ old('shipping_type', session('shipping_type', 'thường')) === 'nhanh' ? 'selected' : '' }}">
                            <input class="form-check-input" type="radio" id="fast_shipping" name="shipping_type"
                                value="nhanh"
                                {{ old('shipping_type', session('shipping_type', 'thường')) === 'nhanh' ? 'checked' : '' }}
                                onchange="this.form.submit()">
                            <div>
                                <label class="form-check-label" for="fast_shipping">
                                    Giao hàng nhanh - ₫{{ number_format($fastShippingFee) }}
                                </label>
                                <p class="text-muted small">
                                    Dự kiến: {{ $fastShippingDates }}
                                </p>
                                <p class="text-muted small">
                                    Hỗ trợ <strong>đồng kiểm</strong> (kiểm tra hàng trước khi nhận)
                                </p>
                            </div>
                        </div>
                        @error('shipping_type')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </form>
                </div>

                <!-- 4. Phương thức thanh toán -->
                <div class="checkout__block checkout__payment-method">
                    <div class="checkout__block-title">💳 Phương thức thanh toán</div>
                    <form action="{{ route('checkout.update') }}" method="POST" id="paymentForm">
                        @csrf
                        <div
                            class="form-check {{ old('payment_method', session('payment_method', 'cod')) === 'cod' ? 'selected' : '' }}">
                            <input class="form-check-input" type="radio" id="cod_payment" name="payment_method"
                                value="cod"
                                {{ old('payment_method', session('payment_method', 'cod')) === 'cod' ? 'checked' : '' }}
                                onchange="this.form.submit()" required>
                            <div>
                                <label class="form-check-label" for="cod_payment">
                                    Thanh toán khi nhận hàng
                                </label>
                                <p class="text-muted small">
                                    Thanh toán bằng tiền mặt khi nhận hàng
                                </p>
                            </div>
                        </div>
                        <div
                            class="form-check {{ old('payment_method', session('payment_method', 'cod')) === 'vnpay' ? 'selected' : '' }}">
                            <input class="form-check-input" type="radio" id="vnpay_payment" name="payment_method"
                                value="vnpay"
                                {{ old('payment_method', session('payment_method', 'cod')) === 'vnpay' ? 'checked' : '' }}
                                onchange="this.form.submit()">
                            <div>
                                <label class="form-check-label" for="vnpay_payment">
                                    VNPay
                                </label>
                                <p class="text-muted small">
                                    Thanh toán trực tuyến qua VNPay
                                </p>
                            </div>
                        </div>
                        @error('payment_method')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </form>
                </div>

                <!-- 5. Mã giảm giá -->
                <div class="checkout__block coupon-section">
                    <div class="checkout__block-title">🎫 Mã giảm giá</div>

                    <!-- Hiển thị mã đã áp dụng -->
                    @if ($coupon)
                        <div class="coupon-applied">
                            <div class="coupon-info">
                                <div class="coupon-code">🎉 Mã {{ $coupon->code }} đã được áp dụng!</div>
                                <div class="coupon-discount">
                                    Giảm {{ number_format($discount) }} ₫
                                    @if ($coupon->discount_type === 'percent')
                                        ({{ $coupon->discount_value }}%)
                                    @else
                                        ({{ number_format($coupon->discount_value) }} ₫)
                                    @endif
                                </div>
                                @if ($coupon->title)
                                    <div class="coupon-title">{{ $coupon->title }}</div>
                                @endif
                            </div>
                            <form action="{{ route('checkout.remove-coupon') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    <i class="fa fa-times me-1"></i>Xóa
                                </button>
                            </form>
                        </div>
                    @endif

                    <!-- Nhập mã thủ công -->
                    <div class="coupon-input-section">
                        <div class="coupon-input-title">
                            <i class="fa fa-tag"></i>
                            Nhập mã giảm giá
                        </div>
                        <form action="{{ route('checkout.apply-coupon') }}" method="POST"
                            class="checkout__coupon-group">
                            @csrf
                            <input type="text" name="coupon_code" class="form-control"
                                placeholder="Nhập mã giảm giá của bạn..." value="{{ old('coupon_code') }}">
                            <button type="submit" class="btn">
                                <i class="fa fa-check me-1"></i>Áp dụng
                            </button>
                        </form>
                        @error('coupon_code')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    @if ($availableCoupons->count() > 0)
                        <div class="coupon-available-section">
                            <div class="coupon-available-title">
                                <i class="fa fa-gift"></i>
                                Chọn mã giảm giá
                            </div>
                            <button class="btn btn-outline-primary w-100" data-bs-toggle="modal"
                                data-bs-target="#couponModal">
                                <i class="fa fa-tags me-2"></i>
                                Xem {{ $availableCoupons->count() }} mã giảm giá khả dụng
                            </button>
                        </div>
                    @else
                        <div class="coupon-empty">
                            <i class="fa fa-gift"></i>
                            <h6 class="fw-bold mb-2">Không có mã giảm giá</h6>
                            <p class="mb-0">Hiện tại không có mã giảm giá nào khả dụng cho đơn hàng của bạn</p>
                        </div>
                    @endif
                </div>

                <!-- 6. Tổng thanh toán -->
                <div class="checkout__block">
                    <div class="checkout__block-title">💰 Tổng thanh toán</div>
                    <ul class="checkout__summary-list">
                        <li><span>Tổng tiền hàng</span><span>{{ number_format($cartTotal) }} ₫</span></li>
                        <li><span>Phí vận chuyển</span><span>{{ number_format($shippingFee) }} ₫</span></li>
                        @if ($coupon)
                            <li><span>Giảm giá ({{ $coupon->code }})</span><span
                                    class="text-danger">-{{ number_format($discount) }} ₫</span></li>
                        @endif
                        <li class="total"><span>Tổng thanh
                                toán</span><span>{{ number_format($cartTotal + $shippingFee - $discount) }} ₫</span></li>
                    </ul>
                    <form id="checkoutForm" action="{{ route('checkout.process') }}" method="POST">
                        @csrf
                        <input type="hidden" name="shipping_type"
                            value="{{ old('shipping_type', session('shipping_type', 'thường')) }}">
                        <input type="hidden" name="payment_method"
                            value="{{ old('payment_method', session('payment_method', 'cod')) }}">
                        <input type="hidden" name="address_id"
                            value="{{ old('address_id', session('checkout_address_id', $defaultAddress->id ?? '')) }}">
                        <input type="hidden" name="note" value="{{ old('note', session('note', '')) }}">
                        <button type="submit" class="checkout__submit-btn">🛒 Đặt hàng ngay</button>
                        <p class="text-muted small mt-2" style="text-align: center; line-height: 1.4;">
                            Nhấn "Đặt hàng" đồng nghĩa với việc bạn đồng ý tuân theo <a href="#"
                                style="color: var(--primary);">Điều khoản sử dụng</a>
                        </p>
                        @error('address_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        @error('shipping_type')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        @error('payment_method')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </form>
                </div>
            </div>
        </div>

        <!-- Address Modal -->
        <div class="modal fade" id="addressModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold">
                            <i class="fa fa-map-marker me-2"></i>
                            Chọn địa chỉ nhận hàng
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('checkout.update') }}" method="POST" id="addressForm">
                        @csrf
                        <div class="modal-body p-4">
                            @if (auth()->check() && $addresses->count() > 0)
                                <div class="row g-3" id="addressList">
                                    @foreach ($addresses as $address)
                                        <div class="col-12 address-item" data-address-id="{{ $address->id }}">
                                            <div class="card border-2 h-100"
                                                style="cursor: pointer; transition: all 0.3s ease;">
                                                <div class="card-body p-3">
                                                    <div class="row align-items-start">
                                                        <div class="col-auto">
                                                            <div class="form-check">
                                                                <input type="radio" id="address_{{ $address->id }}"
                                                                    name="selected_address" value="{{ $address->id }}"
                                                                    class="form-check-input address-radio"
                                                                    {{ $address->id == old('selected_address', session('checkout_address_id', $defaultAddress->id ?? '')) ? 'checked' : '' }}>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="d-flex align-items-center mb-2">
                                                                <h6 class="mb-0 fw-bold text-dark me-2">
                                                                    {{ $address->fullname ?? 'Chưa cung cấp họ tên' }}
                                                                </h6>
                                                                @if ($address->is_default)
                                                                    <span class="badge bg-primary">Mặc định</span>
                                                                @endif
                                                            </div>
                                                            <p class="text-muted mb-1">
                                                                <i class="fa fa-phone me-1"></i>
                                                                (+84)
                                                                {{ $address->phone_number && preg_match('/^0[0-9]{9}$/', $address->phone_number) ? $address->phone_number : 'Số điện thoại không hợp lệ' }}
                                                            </p>
                                                            <p class="text-muted mb-2">
                                                                <i class="fa fa-map-marker me-1"></i>
                                                                {{ $address->street ? $address->street . ', ' : '' }}
                                                                {{ $address->ward ? $address->ward . ', ' : '' }}
                                                                {{ $address->district ? $address->district . ', ' : '' }}
                                                                {{ $address->province ?? 'Chưa cung cấp tỉnh/thành phố' }}
                                                            </p>
                                                            @if (
                                                                !$address->fullname ||
                                                                    !$address->phone_number ||
                                                                    !$address->province ||
                                                                    !$address->district ||
                                                                    !$address->ward ||
                                                                    !$address->street)
                                                                <div class="alert alert-warning py-2 mb-0">
                                                                    <i class="fa fa-exclamation-triangle me-1"></i>
                                                                    <small>Thông tin chưa đầy đủ</small>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="col-auto">
                                                            <a href="{{ route('address.edit', ['id' => $address->id]) }}"
                                                                class="btn btn-outline-primary btn-sm">
                                                                <i class="fa fa-edit me-1"></i>
                                                                Cập nhật
                                                            </a>
                                                            @if (!$address->is_default)
                                                                <button type="button"
                                                                    class="btn btn-outline-danger btn-sm delete-address"
                                                                    data-address-id="{{ $address->id }}">
                                                                    <i class="fa fa-trash me-1"></i>
                                                                    Xóa
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="text-center mt-4 pt-3 border-top">
                                    <a href="{{ route('address.create') }}" class="btn btn-primary btn-lg">
                                        <i class="fa fa-plus me-2"></i>
                                        Thêm địa chỉ mới
                                    </a>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <div class="mb-4">
                                        <i class="fa fa-map-marker"
                                            style="font-size: 4rem; color: #6c757d; opacity: 0.5;"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-2">Chưa có địa chỉ</h5>
                                    <p class="text-muted mb-4">Vui lòng thêm địa chỉ mới để tiếp tục thanh toán.</p>
                                    <a href="{{ route('address.create') }}" class="btn btn-primary btn-lg">
                                        <i class="fa fa-plus me-2"></i>
                                        Thêm địa chỉ
                                    </a>
                                </div>
                            @endif
                            @error('selected_address')
                                <div class="alert alert-danger mt-3">
                                    <i class="fa fa-exclamation-circle me-2"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fa fa-times me-1"></i>
                                Hủy
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check me-1"></i>
                                Xác nhận
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Coupon Modal -->
        <div class="modal fade" id="couponModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold">
                            <i class="fa fa-gift me-2"></i>
                            Chọn mã giảm giá
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            @foreach ($availableCoupons as $availableCoupon)
                                @php
                                    $isApplied = $coupon && $coupon->id === $availableCoupon->id;
                                    $discountAmount =
                                        $availableCoupon->discount_type === 'percent'
                                            ? ($cartTotal * $availableCoupon->discount_value) / 100
                                            : $availableCoupon->discount_value;
                                @endphp
                                <div class="col-12">
                                    <div class="coupon-modal-card {{ $isApplied ? 'applied' : '' }}"
                                        onclick="applySelectedCoupon('{{ $availableCoupon->id }}')">
                                        <div class="coupon-modal-header">
                                            <div class="coupon-modal-code">
                                                <span class="coupon-code-text">{{ $availableCoupon->code }}</span>
                                                @if ($isApplied)
                                                    <span class="badge bg-success ms-2">Đã áp dụng</span>
                                                @endif
                                            </div>
                                            <div class="coupon-modal-discount">
                                                -{{ number_format($discountAmount) }} ₫
                                            </div>
                                        </div>
                                        <div class="coupon-modal-details">
                                            @if ($availableCoupon->discount_type === 'percent')
                                                <span class="discount-type">Giảm {{ $availableCoupon->discount_value }}%
                                                    giá trị đơn hàng</span>
                                            @else
                                                <span class="discount-type">Giảm
                                                    {{ number_format($availableCoupon->discount_value) }} ₫</span>
                                            @endif
                                            @if ($availableCoupon->title)
                                                <span class="coupon-title">• {{ $availableCoupon->title }}</span>
                                            @endif
                                        </div>
                                        <div class="coupon-modal-footer">
                                            <span class="coupon-expiry">
                                                <i class="fa fa-clock me-1"></i>
                                                Hết hạn: {{ $availableCoupon->end_date->format('d/m/Y') }}
                                            </span>
                                            @if (!$isApplied)
                                                <span class="apply-text">Nhấn để áp dụng</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if ($availableCoupons->count() === 0)
                            <div class="text-center py-5">
                                <div class="mb-4">
                                    <i class="fa fa-gift" style="font-size: 4rem; color: #6c757d; opacity: 0.5;"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-2">Không có mã giảm giá</h5>
                                <p class="text-muted mb-0">Hiện tại không có mã giảm giá nào khả dụng cho đơn hàng của bạn
                                </p>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fa fa-times me-1"></i>
                            Đóng
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(document).ready(function() {
                // Xử lý chọn địa chỉ
                $('.address-item').on('click', function(e) {
                    if (!$(e.target).is('.btn, .btn *')) {
                        const radio = $(this).find('.address-radio');
                        radio.prop('checked', true);
                    }
                });

                // Xử lý xóa địa chỉ
                $('.delete-address').on('click', function() {
                    const addressId = $(this).data('address-id');
                    if (confirm('Bạn có chắc muốn xóa địa chỉ này?')) {
                        $.ajax({
                            url: '{{ route('address.delete', ':addressId') }}'.replace(':addressId',
                                addressId),
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success) {
                                    $(`.address-item[data-address-id="${addressId}"]`).remove();
                                    if ($('#addressList .address-item').length === 0) {
                                        $('#addressList').replaceWith(`
                                        <div class="text-center py-5">
                                            <div class="mb-4">
                                                <i class="fa fa-map-marker" style="font-size: 4rem; color: #6c757d; opacity: 0.5;"></i>
                                            </div>
                                            <h5 class="fw-bold text-dark mb-2">Chưa có địa chỉ</h5>
                                            <p class="text-muted mb-4">Vui lòng thêm địa chỉ mới để tiếp tục thanh toán.</p>
                                            <a href="{{ route('address.create') }}" class="btn btn-primary btn-lg">
                                                <i class="fa fa-plus me-2"></i>
                                                Thêm địa chỉ
                                            </a>
                                        </div>
                                    `);
                                    }
                                    alert('Địa chỉ đã được xóa thành công!');
                                } else {
                                    alert(response.message ||
                                        'Không thể xóa địa chỉ. Vui lòng thử lại.');
                                }
                            },
                            error: function(xhr) {
                                console.error('Error deleting address:', xhr.status, xhr.statusText,
                                    xhr.responseText);
                                alert('Không thể xóa địa chỉ. Vui lòng thử lại.');
                            }
                        });
                    }
                });
            });

            document.addEventListener('DOMContentLoaded', function() {
                // Handle shipping method selection
                const shippingOptions = document.querySelectorAll('.checkout__shipping-method .form-check');
                shippingOptions.forEach(option => {
                    const radio = option.querySelector('input[type="radio"]');
                    if (radio.checked) {
                        option.classList.add('selected');
                    }
                    option.addEventListener('click', function(e) {
                        if (e.target !== radio) {
                            radio.checked = true;
                            updateShippingState();
                            radio.form.submit();
                        }
                    });
                    radio.addEventListener('change', function() {
                        updateShippingState();
                    });
                });

                function updateShippingState() {
                    shippingOptions.forEach(option => {
                        const radio = option.querySelector('input[type="radio"]');
                        if (radio.checked) {
                            option.classList.add('selected');
                        } else {
                            option.classList.remove('selected');
                        }
                    });
                }

                // Handle payment method selection
                const paymentOptions = document.querySelectorAll('.checkout__payment-method .form-check');
                paymentOptions.forEach(option => {
                    const radio = option.querySelector('input[type="radio"]');
                    if (radio.checked) {
                        option.classList.add('selected');
                    }
                    option.addEventListener('click', function(e) {
                        if (e.target !== radio) {
                            radio.checked = true;
                            updatePaymentState();
                            radio.form.submit();
                        }
                    });
                    radio.addEventListener('change', function() {
                        updatePaymentState();
                    });
                });

                function updatePaymentState() {
                    paymentOptions.forEach(option => {
                        const radio = option.querySelector('input[type="radio"]');
                        if (radio.checked) {
                            option.classList.add('selected');
                        } else {
                            option.classList.remove('selected');
                        }
                    });
                }
            });

            function applySelectedCoupon(couponId) {
                if (!couponId) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route('checkout.remove-coupon') }}';

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);

                    document.body.appendChild(form);
                    form.submit();
                    return;
                }

                // Hiển thị loading state cho modal coupon
                const couponModalCard = event.target.closest('.coupon-modal-card');
                if (couponModalCard) {
                    couponModalCard.style.opacity = '0.6';
                    couponModalCard.style.pointerEvents = 'none';
                    couponModalCard.innerHTML =
                        '<div class="text-center p-4"><i class="fa fa-spinner fa-spin fa-2x text-primary"></i><p class="mt-2 mb-0">Đang áp dụng mã giảm giá...</p></div>';
                }

                // Tạo form ẩn để submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('checkout.apply-coupon-by-id') }}';

                // Thêm CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                // Thêm ID mã giảm giá
                const couponInput = document.createElement('input');
                couponInput.type = 'hidden';
                couponInput.name = 'coupon_id';
                couponInput.value = couponId;
                form.appendChild(couponInput);

                // Submit form
                document.body.appendChild(form);
                form.submit();
            }

            // Thêm hiệu ứng hover cho coupon modal cards
            document.addEventListener('DOMContentLoaded', function() {
                const couponModalCards = document.querySelectorAll('.coupon-modal-card:not(.applied)');
                couponModalCards.forEach(card => {
                    card.addEventListener('mouseenter', function() {
                        this.style.transform = 'translateY(-3px) scale(1.02)';
                    });

                    card.addEventListener('mouseleave', function() {
                        this.style.transform = 'translateY(0) scale(1)';
                    });
                });

                // Đóng modal coupon sau khi áp dụng thành công
                const couponModal = document.getElementById('couponModal');
                if (couponModal) {
                    couponModal.addEventListener('hidden.bs.modal', function() {
                        // Reset loading state nếu có
                        const loadingCards = this.querySelectorAll('.coupon-modal-card[style*="opacity: 0.6"]');
                        loadingCards.forEach(card => {
                            card.style.opacity = '';
                            card.style.pointerEvents = '';
                        });
                    });
                }
            });
        </script>
    @endpush
@endsection
