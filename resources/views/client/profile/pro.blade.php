@extends('client.layouts.default')
@section('title', 'Hồ sơ')
@section('content')
    <!-- profile area start -->
    <style>
        .error {
            color: #dc3545;
            /* Màu đỏ Bootstrap */
            font-size: 0.9rem;
            margin-top: 5px;
        }
    </style>

    <section class="profile__area pt-120 pb-120">
        <div class="container">
            <div class="profile__inner p-relative">
                <div class="profile__shape">
                    <img class="profile__shape-1" src="{{ asset('assets2/img/login/laptop.png') }}" alt="">
                    <img class="profile__shape-2" src="{{ asset('assets2/img/login/man.png') }}" alt="">
                    <img class="profile__shape-3" src="{{ asset('assets2/img/login/shape-1.png') }}" alt="">
                    <img class="profile__shape-4" src="{{ asset('assets2/img/login/shape-2.png') }}" alt="">
                    <img class="profile__shape-5" src="{{ asset('assets2/img/login/shape-3.png') }}" alt="">
                    <img class="profile__shape-6" src="{{ asset('assets2/img/login/shape-4.png') }}" alt="">
                </div>
                <div class="row">
                    <div class="col-xxl-4 col-lg-4">
                        <div class="profile__tab mr-40">
                            <nav>
                                <div class="nav nav-tabs tp-tab-menu flex-column" id="profile-tab" role="tablist">
                                    <button class="nav-link active" id="nav-profile-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-profile" type="button" role="tab"
                                        aria-controls="nav-profile" aria-selected="false"><span><i
                                                class="fa-regular fa-user-pen"></i></span>Hồ sơ</button>
                                    <button class="nav-link" id="nav-information-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-information" type="button" role="tab"
                                        aria-controls="nav-information" aria-selected="false"><span><i
                                                class="fa-regular fa-circle-info"></i></span> Thông tin</button>
                                    <button class="nav-link" id="nav-order-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-order" type="button" role="tab" aria-controls="nav-order"
                                        aria-selected="false"><span><i
                                                class="fa-light fa-clipboard-list-check"></i></span> Đơn hàng của tôi
                                    </button>
                                    <button class="nav-link" id="nav-password-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-password" type="button" role="tab"
                                        aria-controls="nav-password" aria-selected="false"><span><i
                                                class="fa-regular fa-lock"></i></span> Thay đổi mật khẩu</button>
                                    <span id="marker-vertical" class="tp-tab-line d-none d-sm-inline-block"></span>
                                </div>
                            </nav>
                        </div>
                    </div>
                    <div class="col-xxl-8 col-lg-8">
                        <div class="profile__tab-content">
                            <div class="tab-content" id="profile-tabContent">
                                <div class="tab-pane fade show active" id="nav-profile" role="tabpanel"
                                    aria-labelledby="nav-profile-tab">
                                    @include('client.profile.components.profile-main', ['user' => $user])
                                </div>

                                <!-- Chi Tiết Cá Nhân -->
                                <div class="tab-pane fade" id="nav-information" role="tabpanel"
                                    aria-labelledby="nav-information-tab">
                                    @include('client.profile.components.update-information-form', [
                                        'user' => $user,
                                    ])
                                </div>
                                <!-- Thay đổi mật khẩu -->
                                <div class="tab-pane fade" id="nav-password" role="tabpanel"
                                    aria-labelledby="nav-password-tab">
                                    @include('client.profile.components.change-password-form')
                                </div>

                                <!-- Đơn hàng của tôi -->
                                <div class="tab-pane fade" id="nav-order" role="tabpanel"
                                    aria-labelledby="nav-order-tab">
                                    @include('client.profile.components.my-orders')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- profile area end -->
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('#profile-thumb-input').on('change', function(e) {
                e.preventDefault();

                var formData = new FormData($('#avatar-upload-form')[0]);
                var errorDiv = $('#avatar-upload-error');
                var spinner = $('.profile-thumb-spinner');
                var avatarImg = $('#profile-avatar-img');
                var originalSrc = avatarImg.attr('src'); // Store original image source

                // Show spinner and hide error message
                spinner.show();
                errorDiv.text('');

                $.ajax({
                    url: "{{ route('avatar') }}",
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        spinner.hide();
                        if (response.success) {
                            // Update avatar image source
                            avatarImg.attr('src', response.avatar_url);
                            // Optionally, show a success message that fades out
                            toastr.success('Cập nhật ảnh đại diện thành công');
                        } else {
                            // Show error message from server
                            errorDiv.text(response.error);
                            // Revert to original image if upload fails
                            avatarImg.attr('src', originalSrc);
                        }
                    },
                    error: function(xhr) {
                        spinner.hide();
                        // Revert to original image on error
                        avatarImg.attr('src', originalSrc);
                        // Handle generic error
                        var errorMsg = 'Lỗi không xác định. Vui lòng thử lại.';
                        if (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON
                            .errors.avatar) {
                            errorMsg = xhr.responseJSON.errors.avatar[0];
                        }
                        errorDiv.text(errorMsg);
                    }
                });
            });
        });
    </script>
@endpush
