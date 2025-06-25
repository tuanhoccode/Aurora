<div class="profile__info">
    <h3 class="profile__info-title">Chi Tiết Cá Nhân</h3>
    <div class="profile__info-content">
        <form action="{{route('updateProfile')}}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-xxl-6 col-md-6">
                    <div class="profile__input-box">
                        <div class="profile__input">
                            <input type="text" name="fullname" placeholder="Vui lòng nhập tên của bạn" value="{{ old('fullname', $user->fullname ) }}">
                            <span>
                                <svg width="17" height="19" viewBox="0 0 17 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9 9C11.2091 9 13 7.20914 13 5C13 2.79086 11.2091 1 9 1C6.79086 1 5 2.79086 5 5C5 7.20914 6.79086 9 9 9Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M15.5 17.6C15.5 14.504 12.3626 12 8.5 12C4.63737 12 1.5 14.504 1.5 17.6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                        </div>
                        @error('fullname')
                        <div class="error">{{$message}}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-xxl-6 col-md-6">
                    <div class="profile__input-box">
                        <div class="profile__input">
                            <input type="email" name="email" placeholder="Vui lòng nhập email" value="{{old('email', $user->email)}}">
                            <span>
                                <svg width="18" height="16" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M13 14.6H5C2.6 14.6 1 13.4 1 10.6V5C1 2.2 2.6 1 5 1H13C15.4 1 17 2.2 17 5V10.6C17 13.4 15.4 14.6 13 14.6Z" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M13 5.3999L10.496 7.3999C9.672 8.0559 8.32 8.0559 7.496 7.3999L5 5.3999" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                        </div>
                        @error('email')
                        <div class="error">{{$message}}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-xxl-6 col-md-6">
                    <div class="profile__input-box">
                        <div class="profile__input">
                            <input type="date" name="birthday" value="{{ old('birthday', $user->birthday ? \Carbon\Carbon::parse($user->birthday)->format('Y-m-d') : '') }}">
                            <span>
                                <i class="fa-solid fa-calendar-days"></i>
                            </span>
                        </div>
                        @error('birthday')
                        <div class="error">{{$message}}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-xxl-6 col-md-6">
                    <div class="profile__input-box">
                        <div class="profile__input">
                            <input type="text" name="phone_number" placeholder="Vui lòng nhập số điện thoại cá nhân" value="{{old('phone_number', $user->phone_number ?? 'Chưa cập nhật' )}}">
                            <span><i class="fa-solid fa-phone-volume"></i></i></span>
                        </div>
                        @error('phone_number')
                        <div class="error">{{$message}}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-xxl-6 col-md-6">
                    <div class="profile__input-box">
                        <div class="profile__input">
                            <input type="text" name="address_phone_number" placeholder="Vui lòng nhập SĐT nhận hàng" value="{{ old('address_phone_number', $user->address->phone_number ?? '') }}">
                            <span><i class="fa-solid fa-phone-volume"></i></span>
                        </div>
                    </div>
                    @error('address_phone_number')
                    <div class="error">{{$message}}</div>
                    @enderror
                </div>

                <div class="col-xxl-6 col-md-6">
                    <div class="profile__input-box">
                        <div class="profile__input">
                            <select name="gender">
                                <option value="" disabled selected>Chọn giới tính</option>
                                <option value="male" {{old('gender', $user->gender) ==  'male' ? 'selected' : ''}}> Nam</option>
                                <option value="female" {{old('gender', $user -> gender) ==  'female' ? 'selected' : ''}}> Nữ</option>
                                <option value="other" {{old('gender', $user->gender) ==  'other' ? 'selected' : ''}}> Khác</option>

                            </select>
                        </div>
                        @error('gender')
                        <div class="error">{{$message}}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-xxl-12">
                    <div class="profile__input-box">
                        <div class="profile__input">
                            <input type="text" name="address" value="{{ old('address', $user->address->address ?? '') }}" placeholder="Vui lòng nhập địa chỉ của bạn">
                            <span>
                                <svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M7.99377 10.1461C9.39262 10.1461 10.5266 9.0283 10.5266 7.64946C10.5266 6.27061 9.39262 5.15283 7.99377 5.15283C6.59493 5.15283 5.46094 6.27061 5.46094 7.64946C5.46094 9.0283 6.59493 10.1461 7.99377 10.1461Z" stroke="currentColor" stroke-width="1.5" />
                                    <path d="M1.19707 6.1933C2.79633 -0.736432 13.2118 -0.72843 14.803 6.2013C15.7365 10.2663 13.1712 13.7072 10.9225 15.8357C9.29079 17.3881 6.70924 17.3881 5.06939 15.8357C2.8288 13.7072 0.263493 10.2583 1.19707 6.1933Z" stroke="currentColor" stroke-width="1.5" />
                                </svg>
                            </span>
                        </div>
                        @error('address')
                        <div class="error">{{$message}}</div>
                        @enderror
                    </div>
                </div>

                <!-- <div class="col-xxl-12">
                    <div class="profile__input-box">
                        <div class="profile__input">
                            <textarea placeholder="Enter your bio">Hi there, this is my bio...</textarea>
                        </div>
                    </div>
                </div> -->
                <div class="col-xxl-12">
                    <div class="profile__btn">
                        <button type="submit" class="tp-btn">Cập nhập hồ sơ</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div> 