<div class="profile__password">
    <form action="{{route('changePassword')}}" method="POST">
        <input type="hidden" name="tab" value="info">
        <div class="row">
            <div class="col-xxl-12">
                <div class="tp-profile-input-box">
                    <div class="tp-contact-input">
                        <input type="password" name="old_password" id="old_password" placeholder="Vui lòng nhập mật khẩu hiện tại" type="password">
                    </div>
                    <div class="tp-profile-input-title">
                        <label for="old_password">Mật khẩu cũ</label>
                    </div>
                    @error('old_password')
                    <div class="error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-xxl-6 col-md-6">
                <div class="tp-profile-input-box">
                    <div class="tp-profile-input">
                        <input type="password" name="new_password" id="new_password" placeholder="Vui lòng nhập mật khẩu mới" type="password">
                    </div>
                    <div class="tp-profile-input-title">
                        <label for="new_password">Mật khẩu mới</label>
                    </div>
                    @error('new_password')
                    <div class="error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-xxl-6 col-md-6">
                <div class="tp-profile-input-box">
                    <div class="tp-profile-input">
                        <input type="password" name="con_new_password" id="con_new_pass" placeholder="Vui lòng xác nhận mật khẩu mới" type="password">
                    </div>
                    <div class="tp-profile-input-title">
                        <label for="con_new_pass">Xác nhận mật khẩu</label>
                    </div>
                </div>
            </div>
            <div class="col-xxl-6 col-md-6">
                <div class="profile__btn">
                    <button type="submit" class="tp-btn">Cập nhật mật khẩu</button>
                </div>
            </div>
        </div>
    </form>
</div> 