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

    /* Main Layout */
    .checkout__section {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        padding: 3rem 1rem;
        min-height: 100vh;
    }

    .checkout__container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 20px;
        display: grid;
        gap: 2rem;
        grid-template-columns: 1fr 480px;
    }

    /* Force 2-column layout on larger screens */
    @media (min-width: 1200px) {
        .checkout__container {
            grid-template-columns: 1fr 480px !important;
        }
    }

    /* Improved layout for better visual hierarchy */
    .checkout__left-column {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .checkout__right-column {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        position: sticky;
        top: 2rem;
        height: fit-content;
    }

    /* Checkout Blocks */
    .checkout__block {
        background: #fff;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 2rem;
        border: 1px solid var(--border-light);
        transition: var(--transition);
        position: relative;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .checkout__block::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary) 0%, var(--primary-dark) 100%);
        opacity: 0;
        transition: var(--transition);
    }

    .checkout__block:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-hover);
    }

    .checkout__block:hover::before {
        opacity: 1;
    }

    .checkout__block-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 1.5rem;
        position: relative;
        padding-bottom: 0.8rem;
        display: flex;
        align-items: center;
        gap: 0.8rem;
    }

    .checkout__block-title::before {
        content: '';
        width: 5px;
        height: 25px;
        background: var(--primary);
        border-radius: 3px;
    }

    /* User Info & Address */
    .checkout__user-info p,
    .checkout__address-info p {
        margin-bottom: 1rem;
        font-size: 1rem;
        color: var(--text-muted);
        line-height: 1.6;
        padding: 1rem;
        background: var(--border-light);
        border-radius: var(--radius-sm);
        transition: var(--transition);
        border-left: 4px solid transparent;
    }

    .checkout__user-info strong {
        color: var(--text-dark);
        font-weight: 600;
        display: inline-block;
        min-width: 120px;
    }

    .checkout__address-selection {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1.5rem;
        background: linear-gradient(135deg, var(--primary-light) 0%, #f8fafc 100%);
        border-radius: var(--radius);
        padding: 1.5rem;
        border: 2px solid var(--border);
        transition: var(--transition);
        position: relative;
        margin-bottom: 1rem;
    }

    .checkout__address-selection:hover {
        border-color: var(--primary);
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.1);
    }

    .checkout__address-selection label {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-dark);
        line-height: 1.5;
        flex: 1;
    }

    /* Cart Items */
    .checkout__cart-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .checkout__cart-header,
    .checkout__cart-item {
        display: grid;
        grid-template-columns: 80px 2.5fr 1fr 1fr 1fr;
        align-items: center;
        gap: 1rem;
        padding: 1rem 0;
        font-size: 0.95rem;
    }

    .checkout__cart-header {
        font-weight: 700;
        color: var(--text-muted);
        border-bottom: 3px solid var(--border);
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.8px;
        padding-bottom: 1.2rem;
        margin-bottom: 1rem;
    }

    .checkout__cart-item {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-radius: var(--radius-sm);
        margin-bottom: 0.8rem;
        padding: 1.2rem;
        transition: var(--transition);
        border: 2px solid var(--border-light);
    }

    .checkout__cart-item:hover {
        background: var(--primary-light);
        border-color: var(--primary);
    }

    .checkout__cart-item img {
        width: 70px;
        height: 70px;
        object-fit: cover;
        border-radius: var(--radius-sm);
        border: 2px solid var(--border);
        transition: var(--transition);
    }

    .checkout__cart-item p {
        margin: 0;
        font-size: 0.95rem;
        color: var(--text-dark);
        font-weight: 600;
        line-height: 1.4;
    }

    .checkout__cart-item small {
        color: var(--text-muted);
        font-size: 0.85rem;
        display: block;
        margin-top: 0.3rem;
    }

    /* Note Textarea */
    .checkout__note {
        border-radius: var(--radius-sm);
        border: 2px solid var(--border);
        background: #f8fafc;
        font-size: var(--input-font-size);
        padding: 1rem;
        width: 100%;
        min-height: 100px;
        resize: vertical;
        transition: var(--transition);
        font-family: inherit;
        margin-top: 1rem;
    }

    .checkout__note:focus {
        border-color: var(--primary);
        outline: none;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        background: #fff;
    }

    /* Shipping Method */
    .checkout__shipping-method .form-check {
        border-radius: var(--radius);
        border: 2px solid var(--border);
        padding: 1.5rem;
        margin-bottom: 1.2rem;
        cursor: pointer;
        position: relative;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        transition: var(--transition);
        background: #f8fafc;
    }

    .checkout__shipping-method .form-check:hover,
    .checkout__shipping-method .form-check.selected {
        border-color: var(--primary);
        box-shadow: var(--shadow);
        background: var(--primary-light);
    }

    .checkout__shipping-method .form-check-input {
        width: 22px;
        height: 22px;
        margin: 0.3rem 0.8rem 0 0;
        flex-shrink: 0;
        border: 2px solid var(--border);
        border-radius: 50%;
        transition: var(--transition);
    }

    .checkout__shipping-method .form-check-input:checked {
        background-color: var(--primary);
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.2);
    }

    .checkout__shipping-method .form-check-label {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-dark);
        flex: 1;
        margin-bottom: 0.5rem;
    }

    .checkout__shipping-method .form-check p {
        margin: 0.4rem 0 0;
        font-size: 0.9rem;
        color: var(--text-muted);
        line-height: 1.5;
    }

    /* Payment Method */
    .checkout__payment-method {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1.5rem;
        background: linear-gradient(135deg, var(--primary-light) 0%, #f8fafc 100%);
        border-radius: var(--radius);
        padding: 1.5rem;
        border: 2px solid var(--border);
        transition: var(--transition);
        margin-bottom: 1rem;
    }

    .checkout__payment-method:hover {
        border-color: var(--primary);
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.1);
    }

    .checkout__payment-method label {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-dark);
    }

    .checkout__payment-method img {
        width: 50px;
        vertical-align: middle;
        border-radius: var(--radius-sm);
    }

    /* Coupon Section - Enhanced Design */
    .coupon-section {
        background: linear-gradient(135deg, #fef7ff 0%, #f3e8ff 100%);
        border: 2px solid #e9d5ff;
        border-radius: var(--radius);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
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
        background: linear-gradient(90deg, #8b5cf6, #a855f7, #c084fc);
    }

    .coupon-applied {
        background: #f0fdf4;
        border: 1px solid #10b981;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
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
        background: #10b981;
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
        font-size: 1rem;
        font-weight: 600;
        color: #10b981;
        margin-bottom: 0.2rem;
    }

    .coupon-applied .coupon-discount {
        font-size: 0.9rem;
        color: #065f46;
        font-weight: 500;
    }

    .coupon-applied .coupon-title {
        font-size: 0.8rem;
        color: #6b7280;
        margin-top: 0.1rem;
    }

    .coupon-input-section {
        background: #fff;
        border-radius: var(--radius-sm);
        padding: 1.5rem;
        border: 2px solid #e9d5ff;
        margin-bottom: 1.5rem;
    }

    .coupon-input-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .checkout__coupon-group {
        display: flex;
        gap: 1rem;
        align-items: center;
        margin-bottom: 1rem;
    }

    .checkout__coupon-group .form-control {
        border-radius: var(--radius-sm);
        border: 2px solid #e9d5ff;
        font-size: var(--input-font-size);
        height: var(--input-height);
        padding: 1rem;
        flex: 1;
        transition: var(--transition);
        background: #fef7ff;
    }

    .checkout__coupon-group .form-control:focus {
        border-color: #8b5cf6;
        outline: none;
        box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.1);
        background: #fff;
    }

    .checkout__coupon-group .form-control::placeholder {
        color: #a855f7;
        font-style: italic;
    }

    .coupon-available-section {
        background: #fff;
        border-radius: var(--radius-sm);
        padding: 1.5rem;
        border: 2px solid #e9d5ff;
    }

    .coupon-available-title {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .coupon-card {
        background: linear-gradient(135deg, #fef7ff 0%, #f3e8ff 100%);
        border: 2px solid #e9d5ff;
        border-radius: var(--radius-sm);
        padding: 1rem;
        margin-bottom: 0.8rem;
        cursor: pointer;
        transition: var(--transition);
        position: relative;
        overflow: hidden;
    }

    .coupon-card:hover {
        border-color: #8b5cf6;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(139, 92, 246, 0.15);
    }

    .coupon-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, #8b5cf6, #a855f7);
    }

    .coupon-card .coupon-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .coupon-card .coupon-code {
        font-size: 1.1rem;
        font-weight: 700;
        color: #8b5cf6;
    }

    .coupon-card .coupon-discount {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--success);
    }

    .coupon-card .coupon-details {
        font-size: 0.85rem;
        color: var(--text-muted);
        line-height: 1.4;
    }

    .coupon-card .coupon-expiry {
        font-size: 0.8rem;
        color: var(--warning);
        margin-top: 0.3rem;
    }

    .coupon-empty {
        text-align: center;
        padding: 2rem;
        color: var(--text-muted);
    }

    .coupon-empty i {
        font-size: 3rem;
        opacity: 0.3;
        margin-bottom: 1rem;
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

    /* Buttons */
    .checkout__coupon-group .btn,
    .checkout__btn-main,
    .checkout__submit-btn {
        border-radius: var(--radius-sm);
        font-size: var(--input-font-size);
        padding: 1rem 1.5rem;
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        color: #fff;
        border: none;
        transition: var(--transition);
        height: var(--input-height);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        min-width: 120px;
    }

    .checkout__coupon-group .btn::before,
    .checkout__btn-main::before,
    .checkout__submit-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }

    .checkout__coupon-group .btn:hover::before,
    .checkout__btn-main:hover::before,
    .checkout__submit-btn:hover::before {
        left: 100%;
    }

    .checkout__coupon-group .btn:hover,
    .checkout__btn-main:hover,
    .checkout__submit-btn:hover {
        background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(139, 92, 246, 0.3);
    }

    .checkout__submit-btn {
        width: 100%;
        font-size: 1.2rem;
        padding: 1.2rem;
        height: auto;
        margin-top: 1.5rem;
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        border-radius: var(--radius);
    }

    .checkout__submit-btn:hover {
        background: linear-gradient(135deg, #047857 0%, #065f46 100%);
        box-shadow: 0 8px 20px rgba(5, 150, 105, 0.3);
    }

    /* Summary */
    .checkout__summary-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .checkout__summary-list li {
        display: flex;
        justify-content: space-between;
        font-size: 1rem;
        margin-bottom: 0.8rem;
        padding: 0.8rem 0;
        border-bottom: 1px solid var(--border-light);
        transition: var(--transition);
    }

    .checkout__summary-list .total {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--primary);
        padding: 1.5rem;
        border-radius: var(--radius);
        border: 3px solid var(--primary);
        background: var(--primary-light);
        margin-top: 1.5rem;
        border-bottom: none;
    }

    .alert {
        border-radius: var(--radius);
        padding: 1.2rem;
        margin-bottom: 1.5rem;
        font-size: 0.95rem;
        border: none;
        position: relative;
        overflow: hidden;
    }

    .alert::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 5px;
        height: 100%;
    }

    .alert-success {
        background: var(--success-light);
        color: #065f46;
    }
    .alert-success::before { background: var(--success); }

    .alert-danger {
        background: var(--danger-light);
        color: #991b1b;
    }
    .alert-danger::before { background: var(--danger); }

    /* Modal Styles */
    .modal-content {
        border: none;
        border-radius: var(--radius);
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        overflow: hidden;
    }

    .modal-header {
        background: linear-gradient(135deg, var(--primary-light) 0%, #f8fafc 100%);
        border-bottom: 2px solid var(--primary);
        padding: 1.5rem 2rem;
    }

    .modal-title {
        color: var(--text-dark);
        font-weight: 700;
        font-size: 1.25rem;
        margin: 0;
    }

    .modal-title i {
        color: var(--primary);
    }

    .btn-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: var(--text-muted);
        transition: var(--transition);
    }

    .btn-close:hover {
        color: var(--text-dark);
        transform: scale(1.1);
    }

    .modal-body {
        padding: 2rem;
        background: #fff;
    }

    .modal-footer {
        background: var(--border-light);
        border-top: 2px solid var(--border);
        padding: 1.5rem 2rem;
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
    }

    .modal-footer .btn {
        border-radius: var(--radius);
        font-weight: 700;
        padding: 0.8rem 2rem;
        font-size: 1rem;
        transition: var(--transition);
        min-width: 120px;
    }

    .modal-footer .btn-light {
        background: #fff;
        border: 2px solid var(--border);
        color: var(--text-muted);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .modal-footer .btn-light:hover {
        background: var(--border-light);
        border-color: var(--text-muted);
        color: var(--text-dark);
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
    }

    .modal-footer .btn-primary {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        border: none;
        color: #fff;
        box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
    }

    .modal-footer .btn-primary:hover {
        background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(139, 92, 246, 0.4);
    }

    /* Address Modal Specific */
    .address-item {
        border-color: #dee2e6 !important;
        transition: all 0.3s ease;
    }

    .address-item:hover {
        border-color: #6366f1 !important;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.15);
        transform: translateY(-2px);
    }

    .address-item .form-check-input {
        width: 14px !important;
        height: 14px !important;
        margin-top: 0.1rem;
    }

    .address-item .form-check-input:checked {
        background-color: #6366f1 !important;
        border-color: #6366f1 !important;
    }

    .address-item .form-check-input:focus {
        box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.25) !important;
    }

    /* Payment Modal Specific */
    .payment-item {
        border-color: #dee2e6 !important;
        transition: all 0.3s ease;
    }

    .payment-item:hover {
        border-color: #6366f1 !important;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.15);
        transform: translateY(-2px);
    }

    .payment-item .form-check-input {
        width: 14px !important;
        height: 14px !important;
        margin-top: 0.1rem;
    }

    .payment-item .form-check-input:checked {
        background-color: #6366f1 !important;
        border-color: #6366f1 !important;
    }

    .payment-item .form-check-input:focus {
        box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.25) !important;
    }

    /* Utility Classes */
    .text-danger {
        color: var(--danger) !important;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .text-success {
        color: var(--success) !important;
    }

    .text-muted {
        color: var(--text-muted) !important;
    }

    .btn-outline-primary {
        border-color: var(--primary);
        color: var(--primary);
        background: transparent;
        transition: var(--transition);
        border-radius: var(--radius-sm);
        padding: 0.8rem 1.2rem;
    }

    .btn-outline-primary:hover {
        background: var(--primary);
        color: #fff;
        border-color: var(--primary);
        transform: translateY(-1px);
    }

    .btn-outline-danger {
        border-color: var(--danger);
        color: var(--danger);
        background: transparent;
        transition: var(--transition);
        border-radius: var(--radius-sm);
        padding: 0.8rem 1.2rem;
    }

    .btn-outline-danger:hover {
        background: var(--danger);
        color: #fff;
        border-color: var(--danger);
        transform: translateY(-1px);
    }



    /* Enhanced Coupon Card Styles */
    .coupon-card.applied {
        background: linear-gradient(135deg, var(--success-light) 0%, #d1fae5 100%);
        border-color: var(--success);
        position: relative;
    }

    .coupon-card.applied::after {
        content: '✓';
        position: absolute;
        top: 10px;
        right: 10px;
        width: 24px;
        height: 24px;
        background: var(--success);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
    }

    .coupon-card.applied .coupon-code {
        color: var(--success);
    }

    /* Coupon Modal Styles - Simplified */
    .bg-gradient-primary {
        background: #6366f1 !important;
    }

    .coupon-modal-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 1.2rem;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-bottom: 0.8rem;
        position: relative;
    }

    .coupon-modal-card:hover {
        border-color: #6366f1;
        box-shadow: 0 2px 8px rgba(99, 102, 241, 0.1);
    }

    .coupon-modal-card.applied {
        background: #f0fdf4;
        border-color: #10b981;
        border-left: 4px solid #10b981;
    }

    .coupon-modal-card.applied::after {
        content: '✓';
        position: absolute;
        top: 12px;
        right: 12px;
        width: 20px;
        height: 20px;
        background: #10b981;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
    }

    .coupon-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.8rem;
    }

    .coupon-modal-code {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .coupon-code-text {
        font-size: 1.1rem;
        font-weight: 600;
        color: #6366f1;
        background: #f3f4f6;
        padding: 0.3rem 0.6rem;
        border-radius: 4px;
    }

    .coupon-modal-card.applied .coupon-code-text {
        color: #10b981;
        background: #d1fae5;
    }

    .coupon-modal-discount {
        font-size: 1rem;
        font-weight: 600;
        color: #10b981;
        background: #f0fdf4;
        padding: 0.3rem 0.6rem;
        border-radius: 4px;
    }

    .coupon-modal-details {
        margin-bottom: 0.8rem;
        line-height: 1.5;
    }

    .coupon-modal-details .discount-type {
        font-size: 0.95rem;
        font-weight: 500;
        color: #374151;
        display: block;
        margin-bottom: 0.2rem;
    }

    .coupon-modal-details .coupon-title {
        font-size: 0.85rem;
        color: #6b7280;
        display: block;
    }

    .coupon-modal-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.8rem;
    }

    .coupon-modal-footer .coupon-expiry {
        color: #f59e0b;
        font-weight: 500;
    }

    .coupon-modal-footer .apply-text {
        color: #6366f1;
        font-weight: 500;
    }

    .coupon-modal-card.applied .apply-text {
        color: #10b981;
    }

    /* Responsive improvements for coupon section */
    @media (max-width: 768px) {
        .coupon-section {
            padding: 1rem;
        }

        .coupon-applied {
            flex-direction: column;
            gap: 0.8rem;
            text-align: center;
        }

        .coupon-applied::before {
            left: 50%;
            transform: translateX(-50%);
        }

        .checkout__coupon-group {
            flex-direction: column;
            gap: 0.8rem;
        }

        .coupon-card {
            padding: 0.8rem;
        }

        .coupon-card .coupon-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
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
            gap: 0.4rem;
        }

        .coupon-code-text {
            font-size: 1rem;
            padding: 0.2rem 0.5rem;
        }

        .coupon-modal-discount {
            font-size: 0.9rem;
            padding: 0.2rem 0.5rem;
        }
    }

    /* Responsive Design */
    @media (min-width: 1400px) {
        .checkout__container {
            max-width: 1400px;
        }
    }

    @media (max-width: 1399px) {
        .checkout__container {
            max-width: 1200px;
        }
    }

    @media (max-width: 1199px) {
        .checkout__container {
            grid-template-columns: 1fr;
            gap: 1.5rem;
            max-width: 100%;
        }

        .checkout__left-column,
        .checkout__right-column {
            position: static;
        }

        .checkout__cart-header,
        .checkout__cart-item {
            grid-template-columns: 70px 1.5fr 1fr 1fr 1fr;
            font-size: 0.9rem;
        }

        .checkout__section {
            padding: 2rem 1rem;
        }

        .checkout__block {
            padding: 1.5rem;
        }

        .coupon-section {
            padding: 1.2rem;
        }

        .coupon-applied {
            flex-direction: column;
            gap: 1rem;
            text-align: center;
        }

        .coupon-applied::before {
            left: 50%;
            transform: translateX(-50%);
        }
    }

    /* Đảm bảo layout 2 cột trên desktop */
    @media (min-width: 992px) {
        .checkout__container {
            grid-template-columns: 1fr 480px;
            gap: 2rem;
        }

        .checkout__left-column {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .checkout__right-column {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            position: sticky;
            top: 2rem;
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
        .checkout__payment-method,
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
            <!-- 1. Thông tin khách hàng -->
            <div class="checkout__block">
                <div class="checkout__block-title">👤 Thông tin khách hàng</div>
                <div class="checkout__user-info">
                    @if (auth()->check())
                        <p><strong>Họ và tên:</strong> {{ $user->fullname }}</p>
                        <p><strong>Email:</strong> {{ $user->email }}</p>
                        <p><strong>Số điện thoại:</strong> {{ $user->phone_number ?? 'Chưa cung cấp' }}</p>
                        @if ($user->avatar)
                            <p><strong>Avatar:</strong> <img src="{{ asset($user->avatar) }}" alt="Avatar" width="40" style="border-radius:50%;border:1px solid #e3e6ea;"></p>
                        @else
                            <p class="text-muted">Avatar: Chưa có</p>
                        @endif
                    @else
                        <p class="text-muted">Vui lòng đăng nhập để xem thông tin khách hàng.</p>
                    @endif
                </div>
            </div>

            <!-- 2. Địa chỉ nhận hàng -->
            <div class="checkout__block">
                <div class="checkout__block-title">📍 Địa chỉ nhận hàng</div>
                <div class="checkout__address-selection">
                    <div>
                        <label id="address_label">
                            @if (auth()->check() && $addresses->count() > 0)
                                @php
                                    $selectedAddress = $addresses->firstWhere('id', old('selected_address', session('checkout_address_id', $defaultAddress->id ?? '')));
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
                    <button class="checkout__btn-main" data-bs-toggle="modal" data-bs-target="#addressModal">Thay đổi</button>
                </div>
                @error('selected_address')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <!-- 3. Sản phẩm đã chọn -->
            <div class="checkout__block">
                <div class="checkout__block-title">🛍️ Sản phẩm đã chọn ({{ $cartItems->sum('quantity') }} sản phẩm)</div>
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
                    <label for="note" style="font-weight: 600; color: var(--text-dark); margin-bottom: 0.5rem; display: block;">💬 Lời nhắn cho người bán:</label>
                    <form action="{{ route('checkout.update') }}" method="POST">
                        @csrf
                        <textarea name="note" id="note" class="checkout__note" placeholder="Nhập lưu ý cho người bán (không bắt buộc)..."
                            onchange="this.form.submit()">{{ old('note', session('note', '')) }}</textarea>
                        @error('note')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </form>
                </div>
            </div>
        </div>

        <div class="checkout__right-column">
            <!-- 4. Phương thức vận chuyển -->
            <div class="checkout__block checkout__shipping-method">
                <div class="checkout__block-title">🚚 Phương thức vận chuyển</div>
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
                                Giao hàng thường - ₫16.500
                            </label>
                            <p class="text-muted small">
                                Dự kiến giao hàng từ
                                <strong>{{ \Carbon\Carbon::today()->addDays(2)->format('d/m/Y') }}</strong>
                                đến <strong>{{ \Carbon\Carbon::today()->addDays(4)->format('d/m/Y') }}</strong>
                            </p>
                            <p class="text-muted small">
                                Nhận Voucher <strong>₫15.000</strong> nếu giao hàng sau
                                <strong>{{ \Carbon\Carbon::today()->addDays(4)->format('d/m/Y') }}</strong>
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
                                Giao hàng nhanh - ₫30.000
                            </label>
                            <p class="text-muted small">
                                Dự kiến giao hàng trong vòng <strong>4 giờ</strong> nếu đặt trước 16:00 hôm nay
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

            <!-- 5. Phương thức thanh toán -->
            <div class="checkout__block">
                <div class="checkout__block-title">💳 Phương thức thanh toán</div>
                <div class="checkout__payment-method">
                    <div>
                        <label id="payment_method_label">
                            @if (old('payment_method', session('payment_method', 'cod')) === 'cod')
                                💵 Thanh toán khi nhận hàng
                            @else
                                🏦 VNPay
                            @endif
                        </label>
                    </div>
                    <button class="checkout__btn-main" data-bs-toggle="modal" data-bs-target="#paymentModal">Thay đổi</button>
                </div>
                @error('payment_method')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <!-- 6. Mã giảm giá -->
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
                <form action="{{ route('checkout.apply-coupon') }}" method="POST" class="checkout__coupon-group">
                    @csrf
                        <input type="text" name="coupon_code" class="form-control"
                               placeholder="Nhập mã giảm giá của bạn..."
                        value="{{ old('coupon_code') }}">
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
                        <button class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#couponModal">
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

            <!-- 7. Tổng thanh toán -->
            <div class="checkout__block">
                <div class="checkout__block-title">💰 Tổng thanh toán</div>
                <ul class="checkout__summary-list">
                    <li><span>Tổng tiền hàng</span><span>{{ number_format($cartTotal) }} ₫</span></li>
                    <li><span>Phí vận chuyển</span><span>{{ number_format($shippingFee) }} ₫</span></li>
                    @if ($coupon)
                        <li><span>Giảm giá ({{ $coupon->code }})</span><span class="text-danger">-{{ number_format($discount) }} ₫</span></li>
                    @endif
                    <li class="total"><span>Tổng thanh toán</span><span>{{ number_format($cartTotal + $shippingFee - $discount) }} ₫</span></li>
                </ul>
                <form id="checkoutForm" action="{{ route('checkout.process') }}" method="POST">
                    @csrf
                    <input type="hidden" name="shipping_type" value="{{ old('shipping_type', session('shipping_type', 'thường')) }}">
                    <input type="hidden" name="payment_method" value="{{ old('payment_method', session('payment_method', 'cod')) }}">
                    <input type="hidden" name="address_id" value="{{ old('address_id', session('checkout_address_id', $defaultAddress->id ?? '')) }}">
                    <input type="hidden" name="note" value="{{ old('note', session('note', '')) }}">
                    <button type="submit" class="checkout__submit-btn">🛒 Đặt hàng ngay</button>
                    <p class="text-muted small mt-2" style="text-align: center; line-height: 1.4;">
                        Nhấn "Đặt hàng" đồng nghĩa với việc bạn đồng ý tuân theo <a href="#" style="color: var(--primary);">Điều khoản sử dụng</a>
                    </p>
                    @error('address_id') <span class="text-danger">{{ $message }}</span> @enderror
                    @error('shipping_type') <span class="text-danger">{{ $message }}</span> @enderror
                    @error('payment_method') <span class="text-danger">{{ $message }}</span> @enderror
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
                <form action="{{ route('checkout.update') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        @if (auth()->check() && $addresses->count() > 0)
                            <div class="row g-3">
                                @foreach ($addresses as $address)
                                    <div class="col-12">
                                        <div class="card border-2 h-100 address-item" style="cursor: pointer; transition: all 0.3s ease;">
                                            <div class="card-body p-3">
                                                <div class="row align-items-start">
                                                    <div class="col-auto">
                                                        <div class="form-check">
                                                            <input type="radio" id="address_{{ $address->id }}" name="selected_address" value="{{ $address->id }}"
                                                                class="form-check-input"
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
                                                            (+84) {{ $address->phone_number && preg_match('/^0[0-9]{9}$/', $address->phone_number) ? $address->phone_number : 'Số điện thoại không hợp lệ' }}
                                                        </p>
                                                        <p class="text-muted mb-2">
                                                            <i class="fa fa-map-marker me-1"></i>
                                                            {{ $address->street ? $address->street . ', ' : '' }}
                                                            {{ $address->ward ? $address->ward . ', ' : '' }}
                                                            {{ $address->district ? $address->district . ', ' : '' }}
                                                            {{ $address->province ?? 'Chưa cung cấp tỉnh/thành phố' }}
                                                        </p>
                                                        @if (!$address->fullname || !$address->phone_number || !$address->province || !$address->district || !$address->ward || !$address->street)
                                                            <div class="alert alert-warning py-2 mb-0">
                                                                <i class="fa fa-exclamation-triangle me-1"></i>
                                                                <small>Thông tin chưa đầy đủ</small>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="col-auto">
                                                        <a href="{{ route('address.edit', ['id' => $address->id]) }}" class="btn btn-outline-primary btn-sm">
                                                            <i class="fa fa-edit me-1"></i>
                                                            Cập nhật
                                                        </a>
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
                                    <i class="fa fa-map-marker" style="font-size: 4rem; color: #6c757d; opacity: 0.5;"></i>
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

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="fa fa-credit-card me-2"></i>
                        Chọn phương thức thanh toán
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('checkout.update') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="card border-2 h-100 payment-item" style="cursor: pointer; transition: all 0.3s ease;">
                                    <div class="card-body p-3">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <div class="form-check">
                                                    <input type="radio" id="cod_modal" name="payment_method" value="cod"
                                                        class="form-check-input"
                                                        {{ old('payment_method', session('payment_method', 'cod')) === 'cod' ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="d-flex align-items-center">
                                                    <h6 class="mb-0 fw-bold text-dark me-2">
                                                        💵 Thanh toán khi nhận hàng
                                                    </h6>
                                                </div>
                                                <p class="text-muted mb-0 small">
                                                    Thanh toán bằng tiền mặt khi nhận hàng
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="card border-2 h-100 payment-item" style="cursor: pointer; transition: all 0.3s ease;">
                                    <div class="card-body p-3">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <div class="form-check">
                                                    <input type="radio" id="vnpay_modal" name="payment_method" value="vnpay"
                                                        class="form-check-input"
                                                        {{ old('payment_method', session('payment_method', 'cod')) === 'vnpay' ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="d-flex align-items-center">
                                                    <h6 class="mb-0 fw-bold text-dark me-2">
                                                        🏦 VNPay
                                                    </h6>
                                                </div>
                                                <p class="text-muted mb-0 small">
                                                    Thanh toán trực tuyến qua VNPay
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @error('payment_method')
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
                                $discountAmount = $availableCoupon->discount_type === 'percent'
                                    ? ($cartTotal * $availableCoupon->discount_value / 100)
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
                                            <span class="discount-type">Giảm {{ $availableCoupon->discount_value }}% giá trị đơn hàng</span>
                                        @else
                                            <span class="discount-type">Giảm {{ number_format($availableCoupon->discount_value) }} ₫</span>
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
                            <p class="text-muted mb-0">Hiện tại không có mã giảm giá nào khả dụng cho đơn hàng của bạn</p>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const shippingOptions = document.querySelectorAll('.checkout__shipping-method .form-check');
        shippingOptions.forEach(option => {
            const radio = option.querySelector('input[type="radio"]');
            if (radio.checked) {
                option.classList.add('selected');
            }
            option.addEventListener('click', function(e) {
                if (e.target !== radio) {
                    radio.checked = true;
                    updateSelectedState();
                    radio.form.submit();
                }
            });
            radio.addEventListener('change', function() {
                updateSelectedState();
            });
        });

        function updateSelectedState() {
            shippingOptions.forEach(option => {
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
            form.action = '{{ route("checkout.remove-coupon") }}';

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
            couponModalCard.innerHTML = '<div class="text-center p-4"><i class="fa fa-spinner fa-spin fa-2x text-primary"></i><p class="mt-2 mb-0">Đang áp dụng mã giảm giá...</p></div>';
        }

        // Tạo form ẩn để submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("checkout.apply-coupon-by-id") }}';

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
@endsection
