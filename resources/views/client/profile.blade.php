@extends('client.layouts.default')

@section('title', 'Thông tin cá nhân - Aurora')

@section('content')
<main>
    <!-- profile area start -->
    <section class="profile__area pt-120 pb-120">
        <div class="container">
            <div class="profile__inner p-relative">
                <!-- Các hình shape trang trí -->
                <div class="profile__shape">
                    <img class="profile__shape-1" src="{{ asset('assets/img/login/laptop.png') }}" alt="">
                    <img class="profile__shape-2" src="{{ asset('assets/img/login/man.png') }}" alt="">
                    <img class="profile__shape-3" src="{{ asset('assets/img/login/shape-1.png') }}" alt="">
                    <img class="profile__shape-4" src="{{ asset('assets/img/login/shape-2.png') }}" alt="">
                    <img class="profile__shape-5" src="{{ asset('assets/img/login/shape-3.png') }}" alt="">
                    <img class="profile__shape-6" src="{{ asset('assets/img/login/shape-4.png') }}" alt="">
                </div>
                <div class="row">
                    <!-- Sidebar tab -->
                    <div class="col-xxl-4 col-lg-4">
                        <div class="profile__tab mr-40">
                            <nav>
                                <div class="nav nav-tabs tp-tab-menu flex-column" id="profile-tab" role="tablist">
                                    <a class="nav-link{{ request()->routeIs('client.profile') ? ' active' : '' }}" href="{{ route('client.profile') }}">
                                        <span><i class="fa-regular fa-user-pen"></i></span>Thông tin cá nhân
                                    </a>
                                    <a class="nav-link" href="">
                                        <span><i class="fa-regular fa-circle-info"></i></span> Thông tin
                                    </a>
                                    <a class="nav-link" href="">
                                        <span><i class="fa-light fa-location-dot"></i></span> Địa chỉ
                                    </a>
                                    <a class="nav-link" href="">
                                        <span><i class="fa-light fa-clipboard-list-check"></i></span> Đơn hàng
                                    </a>
                                    <a class="nav-link" href="">
                                        <span><i class="fa-regular fa-bell"></i></span> Thông báo
                                    </a>
                                    <a class="nav-link" href="">
                                        <span><i class="fa-regular fa-lock"></i></span> Đổi mật khẩu
                                    </a>
                                    <span id="marker-vertical" class="tp-tab-line d-none d-sm-inline-block"></span>
                                </div>
                            </nav>
                        </div>
                    </div>
                    <!-- Nội dung tab -->
                    <div class="col-xxl-8 col-lg-8">
                        <div class="profile__tab-content">
                            <div class="tab-content" id="profile-tabContent">
                                <!-- Tab Profile -->
                                <div class="tab-pane fade show active" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                                    <!-- Nội dung Thông tin cá nhân -->
                                </div>
                                <!-- Tab Information -->
                                <div class="tab-pane fade" id="nav-information" role="tabpanel" aria-labelledby="nav-information-tab">
                                    <!-- Nội dung Thông tin -->
                                </div>
                                <!-- Tab Address -->
                                <div class="tab-pane fade" id="nav-address" role="tabpanel" aria-labelledby="nav-address-tab">
                                    <!-- Nội dung Địa chỉ -->
                                </div>
                                <!-- Tab Orders -->
                                <div class="tab-pane fade" id="nav-order" role="tabpanel" aria-labelledby="nav-order-tab">
                                    <!-- Nội dung Đơn hàng -->
                                </div>
                                <!-- Tab Notification -->
                                <div class="tab-pane fade" id="nav-notification" role="tabpanel" aria-labelledby="nav-notification-tab">
                                    <!-- Nội dung Thông báo -->
                                </div>
                                <!-- Tab Password -->
                                <div class="tab-pane fade" id="nav-password" role="tabpanel" aria-labelledby="nav-password-tab">
                                    <!-- Nội dung Đổi mật khẩu -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End nội dung tab -->
                </div>
            </div>
        </div>
    </section>
    <!-- profile area end -->
</main>
@endsection 