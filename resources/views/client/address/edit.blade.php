@extends('client.layouts.default')

@section('title', $address ? 'Chỉnh sửa địa chỉ' : 'Thêm địa chỉ mới')

@section('content')
    <section class="tp-address-area pb-120" data-bg-color="#EFF1F5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="tp-checkout-place white-bg p-4">
                        <h3 class="tp-checkout-place-title">{{ $address ? 'Chỉnh sửa địa chỉ' : 'Thêm địa chỉ mới' }}</h3>
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <noscript>
                            <div class="alert alert-warning">
                                Vui lòng bật JavaScript để đảm bảo form hoạt động chính xác. Nếu không, địa chỉ sẽ được tự động tạo từ các trường nhập liệu.
                            </div>
                        </noscript>
                        <form action="{{ route('address.save') }}" method="POST">
                            @csrf
                            <input type="hidden" name="address_id" value="{{ $address->id ?? '' }}">
                            <div class="mb-3">
                                <label for="fullname">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" name="fullname" id="fullname" class="form-control"
                                       value="{{ old('fullname', $address->fullname ?? $user->fullname ?? '') }}"
                                       required pattern="^[A-Za-z\sÀ-ỹ]{2,255}$"
                                       title="Họ và tên chỉ chứa chữ cái và dấu cách, từ 2 đến 255 ký tự"
                                       aria-describedby="fullname-error">
                                @error('fullname')
                                    <span id="fullname-error" class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="phone_number">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="text" name="phone_number" id="phone_number" class="form-control"
                                       value="{{ old('phone_number', $address->phone_number ?? $user->phone_number ?? '') }}"
                                       required pattern="^0[35789][0-9]{8}$"
                                       title="Số điện thoại phải bắt đầu bằng 0, theo sau là 9 chữ số (bắt đầu bằng 3, 5, 7, 8, hoặc 9)"
                                       aria-describedby="phone_number-error">
                                @error('phone_number')
                                    <span id="phone_number-error" class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="email">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" class="form-control"
                                       value="{{ old('email', $address->email ?? $user->email ?? '') }}"
                                       required aria-describedby="email-error">
                                @error('email')
                                    <span id="email-error" class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="province">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                                <input type="text" name="province" id="province" class="form-control"
                                       value="{{ old('province', $address->province ?? '') }}"
                                       required pattern="^[A-Za-z\sÀ-ỹ]{2,100}$"
                                       title="Tỉnh/Thành phố chỉ chứa chữ cái và dấu cách, từ 2 đến 100 ký tự"
                                       aria-describedby="province-error">
                                @error('province')
                                    <span id="province-error" class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="district">Quận/Huyện <span class="text-danger">*</span></label>
                                <input type="text" name="district" id="district" class="form-control"
                                       value="{{ old('district', $address->district ?? '') }}"
                                       required pattern="^[A-Za-z\sÀ-ỹ]{2,100}$"
                                       title="Quận/Huyện chỉ chứa chữ cái và dấu cách, từ 2 đến 100 ký tự"
                                       aria-describedby="district-error">
                                @error('district')
                                    <span id="district-error" class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="ward">Phường/Xã <span class="text-danger">*</span></label>
                                <input type="text" name="ward" id="ward" class="form-control"
                                       value="{{ old('ward', $address->ward ?? '') }}"
                                       required pattern="^[A-Za-z\sÀ-ỹ]{2,100}$"
                                       title="Phường/Xã chỉ chứa chữ cái và dấu cách, từ 2 đến 100 ký tự"
                                       aria-describedby="ward-error">
                                @error('ward')
                                    <span id="ward-error" class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="street">Địa chỉ cụ thể <span class="text-danger">*</span></label>
                                <input type="text" name="street" id="street" class="form-control"
                                       value="{{ old('street', $address->street ?? '') }}"
                                       required pattern="^[A-Za-z0-9\s,À-ỹ]{5,255}$"
                                       title="Địa chỉ cụ thể chứa chữ cái, số, dấu cách hoặc dấu phẩy, từ 5 đến 255 ký tự"
                                       aria-describedby="street-error">
                                @error('street')
                                    <span id="street-error" class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <input type="hidden" name="address" id="address"
                                   value="{{ old('address', $address->address ?? '') }}">
                            <div class="mb-3">
                                <label>Loại địa chỉ:</label>
                                <div>
                                    <input type="radio" name="address_type" id="home" value="home"
                                           {{ old('address_type', $address->address_type ?? 'home') === 'home' ? 'checked' : '' }}
                                           required>
                                    <label for="home">Nhà riêng</label>
                                    <input type="radio" name="address_type" id="office" value="office"
                                           {{ old('address_type', $address->address_type ?? '') === 'office' ? 'checked' : '' }}>
                                    <label for="office">Văn phòng</label>
                                </div>
                                @error('address_type')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <input type="checkbox" name="is_default" id="is_default" value="1"
                                       {{ old('is_default', $address->is_default ?? 0) ? 'checked' : '' }}>
                                <label for="is_default">Đặt làm địa chỉ mặc định</label>
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('checkout') }}" class="btn btn-light rounded-pill">Hủy</a>
                                <button type="submit" class="btn btn-primary rounded-pill">Hoàn thành</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    function updateAddressField() {
        const province = document.getElementById('province')?.value || '';
        const district = document.getElementById('district')?.value || '';
        const ward = document.getElementById('ward')?.value || '';
        const street = document.getElementById('street')?.value || '';
        const addressParts = [street, ward, district, province].filter(part => part);
        const addressInput = document.getElementById('address');
        if (addressInput) {
            addressInput.value = addressParts.join(', ');
        }
    }

    // Update address field when input changes
    const inputs = ['province', 'district', 'ward', 'street'];
    inputs.forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            input.addEventListener('input', updateAddressField);
        }
    });
</script>
@endpush