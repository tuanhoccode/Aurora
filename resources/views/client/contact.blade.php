@extends('client.layouts.default')
@section('title', 'Liên hệ')
@section('content')
<style>
    .contact__form {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.07);
        padding: 32px 32px 24px 32px;
        margin-bottom: 32px;
    }
    .contact__form input,
    .contact__form textarea {
        border-radius: 8px;
        border: 1px solid #e5e5e5;
        padding-left: 40px;
        font-size: 1rem;
    }
    .contact__form .input-icon {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #b5b5b5;
        font-size: 1.1rem;
    }
    .contact__form .form-group {
        position: relative;
        margin-bottom: 1.5rem;
    }
    .contact__form .tp-btn {
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        font-weight: 600;
        padding: 10px 32px;
        font-size: 1.1rem;
        transition: background 0.2s;
    }
    .contact__form .tp-btn:hover {
        background: #2d2d2d;
        color: #fff;
    }
    .contact__info {
        background: #f8f9fa;
        border-radius: 16px;
        padding: 32px 24px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        margin-bottom: 24px;
        text-align: center;
    }
    .contact__info ul {
        padding-left: 0;
        list-style: none;
    }
    .contact__info li {
        margin-bottom: 18px;
        font-size: 1.08rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
    }
    .contact__info i {
        font-size: 1.4rem;
        color: #b48c5a;
    }
    .contact__map {
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        margin-top: 24px;
    }
    .alert {
        border-radius: 8px;
        font-size: 1rem;
    }
    .error {
        color: #dc3545;
        font-size: 0.95rem;
        margin-top: 4px;
    }
</style>
<section class="contact__area pt-120 pb-120">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="contact__form">
                    <h3 class="mb-4 text-center">Liên hệ với chúng tôi</h3>
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @elseif(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    <form action="{{ route('contact.send') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <span class="input-icon"><i class="fa fa-user"></i></span>
                                    <input type="text" name="name" class="form-control" placeholder="Họ và tên" value="{{ old('name') }}">
                                    @error('name')<div class="error">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <span class="input-icon"><i class="fa fa-envelope"></i></span>
                                    <input type="email" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}">
                                    @error('email')<div class="error">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <span class="input-icon"><i class="fa fa-phone"></i></span>
                                    <input type="text" name="phone" class="form-control" placeholder="Số điện thoại" value="{{ old('phone') }}">
                                    @error('phone')<div class="error">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <span class="input-icon" style="top:18px;"><i class="fa fa-comment"></i></span>
                                    <textarea name="message" class="form-control" rows="5" placeholder="Nội dung" style="padding-left:40px;">{{ old('message') }}</textarea>
                                    @error('message')<div class="error">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-12 text-center">
                                <button type="submit" class="tp-btn">Gửi liên hệ</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-5 mt-5 mt-lg-0">
                <div class="contact__info mb-4">
                    <h4 class="mb-3">Thông tin liên hệ</h4>
                    <ul class="list-unstyled mb-0">
                        <li><i class="fa fa-map-marker-alt"></i>68 Đường Trịnh Văn Bô - Phương Canh - Nam Từ Liêm - Hà Nội</li>
                        <li><i class="fa fa-envelope"></i> aurora@support.com</li>
                        <li><i class="fa fa-phone"></i> 0336689888</li>
                    </ul>
                </div>
                <div class="contact__map">
                    <iframe src="https://www.google.com/maps?q=10.7769,106.7009&z=15&output=embed" width="100%" height="220" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection 