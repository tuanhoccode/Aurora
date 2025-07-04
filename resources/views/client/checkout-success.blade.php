@extends('client.layouts.default')

@section('title', 'Đặt hàng thành công')

@section('content')
<style>
    .checkout-success-container {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.07);
        padding: 2.5rem 1.5rem 2rem 1.5rem;
        margin: 2rem auto;
        max-width: 480px;
        text-align: center;
    }
    .checkout-success-icon {
        font-size: 4rem;
        color: #43b047;
        margin-bottom: 1.2rem;
    }
    .checkout-success-title {
        font-family: 'Segoe UI', Arial, Helvetica, sans-serif;
        font-size: 2rem;
        font-weight: 800;
        color: #222;
        margin-bottom: 1rem;
        letter-spacing: 0.01em;
    }
    .checkout-success-desc {
        font-size: 1.08rem;
        color: #444;
        margin-bottom: 2rem;
        line-height: 1.6;
    }
    .checkout-success-actions {
        display: flex;
        justify-content: center;
        gap: 1rem;
    }
    .checkout-success-actions .tp-btn {
        font-size: 1rem;
        font-weight: 600;
        padding: 0.8rem 1.8rem;
        border-radius: 8px;
        background: #23272f;
        color: #fff;
        border: none;
        transition: background 0.18s, color 0.18s;
    }
    .checkout-success-actions .tp-btn:hover {
        background: #4a90e2;
        color: #fff;
    }
    @media (max-width: 600px) {
        .checkout-success-container { padding: 1.2rem 0.3rem; }
        .checkout-success-title { font-size: 1.2rem; }
        .checkout-success-desc { font-size: 0.98rem; }
        .checkout-success-actions .tp-btn { font-size: 0.95rem; padding: 0.7rem 1.1rem; }
    }
</style>
<section class="tp-checkout-success-area py-5" style="background: #f7f8fa;">
    <div class="container">
        <div class="checkout-success-container">
            <div class="checkout-success-icon">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="checkout-success-title">Đặt hàng thành công!</div>
            <div class="checkout-success-desc">
                Cảm ơn bạn đã mua sắm tại Aurora.<br>
                Đơn hàng của bạn đã được ghi nhận và sẽ được xử lý trong thời gian sớm nhất.
            </div>
            <div class="checkout-success-actions">
                <a href="{{ route('home') }}" class="tp-btn">Về trang chủ</a>
                <a href="{{ route('client.orders.show', ['order' => $order->id]) }}" class="tp-btn">Xem đơn hàng</a>
            </div>
        </div>
    </div>
</section>
@endsection 