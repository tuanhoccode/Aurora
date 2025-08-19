@extends('client.layouts.default')

@section('title', 'Chỉnh sửa địa chỉ')

@section('content')
    <section class="tp-address-area pb-120" data-bg-color="#EFF1F5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="tp-checkout-place white-bg p-4">
                        <h3 class="tp-checkout-place-title">Chỉnh sửa địa chỉ</h3>
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
                                Vui lòng bật JavaScript để đảm bảo form hoạt động chính xác.
                            </div>
                        </noscript>
                        <form action="{{ route('address.save') }}" method="POST">
                            @csrf
                            <input type="hidden" name="address_id" value="{{ $address->id }}">
                            <div class="mb-3">
                                <label for="fullname">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" name="fullname" id="fullname" class="form-control"
                                    value="{{ old('fullname', $address->fullname) }}" required
                                    pattern="^[A-Za-z\sÀ-ỹ]{2,255}$"
                                    title="Họ và tên chỉ chứa chữ cái và dấu cách, từ 2 đến 255 ký tự"
                                    aria-describedby="fullname-error">
                                @error('fullname')
                                    <span id="fullname-error" class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="phone_number">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="text" name="phone_number" id="phone_number" class="form-control"
                                    value="{{ old('phone_number', $address->phone_number) }}" required
                                    pattern="^0[35789][0-9]{8}$"
                                    title="Số điện thoại phải bắt đầu bằng 0, theo sau là 9 chữ số (bắt đầu bằng 3, 5, 7, 8, hoặc 9)"
                                    aria-describedby="phone_number-error">
                                @error('phone_number')
                                    <span id="phone_number-error" class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="email">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" class="form-control"
                                    value="{{ old('email', $address->email) }}" required aria-describedby="email-error">
                                @error('email')
                                    <span id="email-error" class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="province">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                                <select name="province" id="province" class="form-control" required
                                    aria-describedby="province-error">
                                    <option value="">Chọn tỉnh/thành phố</option>
                                </select>
                                @error('province')
                                    <span id="province-error" class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="district">Quận/Huyện <span class="text-danger">*</span></label>
                                <select name="district" id="district" class="form-control" required disabled
                                    aria-describedby="district-error">
                                    <option value="">Chọn quận/huyện</option>
                                </select>
                                @error('district')
                                    <span id="district-error" class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="ward">Phường/Xã <span class="text-danger">*</span></label>
                                <select name="ward" id="ward" class="form-control" required disabled
                                    aria-describedby="ward-error">
                                    <option value="">Chọn phường/xã</option>
                                </select>
                                @error('ward')
                                    <span id="ward-error" class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="street">Địa chỉ cụ thể <span class="text-danger">*</span></label>
                                <input type="text" name="street" id="street" class="form-control"
                                    value="{{ old('street', $address->street) }}" required
                                    pattern="^[A-Za-z0-9\s,À-ỹ]{5,255}$"
                                    title="Địa chỉ cụ thể chứa chữ cái, số, dấu cách hoặc dấu phẩy, từ 5 đến 255 ký tự"
                                    aria-describedby="street-error">
                                @error('street')
                                    <span id="street-error" class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <input type="hidden" name="address" id="address"
                                value="{{ old('address', $address->address) }}">
                            <div class="mb-3">
                                <label>Loại địa chỉ:</label>
                                <div>
                                    <input type="radio" name="address_type" id="home" value="home"
                                        {{ old('address_type', $address->address_type) === 'home' ? 'checked' : '' }}
                                        required>
                                    <label for="home">Nhà riêng</label>
                                    <input type="radio" name="address_type" id="office" value="office"
                                        {{ old('address_type', $address->address_type) === 'office' ? 'checked' : '' }}>
                                    <label for="office">Văn phòng</label>
                                </div>
                                @error('address_type')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <input type="checkbox" name="is_default" id="is_default" value="1"
                                    {{ old('is_default', $address->is_default) ? 'checked' : '' }}>
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
        let addressData = [];
        const provinceSelect = document.getElementById('province');
        const districtSelect = document.getElementById('district');
        const wardSelect = document.getElementById('ward');
        const streetInput = document.getElementById('street');
        const addressInput = document.getElementById('address');

        // Hàm decode HTML entities
        function decodeHtml(html) {
            const txt = document.createElement('textarea');
            txt.innerHTML = html;
            return txt.value;
        }

        function updateAddressField() {
            const province = provinceSelect.value || '';
            const district = districtSelect.value || '';
            const ward = wardSelect.value || '';
            const street = streetInput.value || '';
            const addressParts = [street, ward, district, province].filter(part => part);
            addressInput.value = addressParts.join(', ');
            console.log('Updated address:', addressInput.value);
        }

        async function fetchAddressData() {
            try {
                console.log('Fetching address data from local file...');
                const response = await fetch('/address.json', {
                    headers: {
                        'Accept': 'application/json; charset=utf-8'
                    }
                });
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                addressData = await response.json();
                console.log('Raw address data:', addressData);
                // Decode tên tỉnh, quận, xã
                addressData = addressData.map(province => ({
                    ...province,
                    name: decodeHtml(province.name),
                    districts: province.districts.map(district => ({
                        ...district,
                        name: decodeHtml(district.name),
                        wards: district.wards.map(ward => ({
                            ...ward,
                            name: decodeHtml(ward.name)
                        }))
                    }))
                }));
                console.log('Decoded address data:', addressData);
                populateProvinces();
                // Chọn giá trị ban đầu cho edit
                @if (isset($address))
                    const initialProvince = '{{ old('province', $address->province ?? '') }}';
                    const initialDistrict = '{{ old('district', $address->district ?? '') }}';
                    const initialWard = '{{ old('ward', $address->ward ?? '') }}';
                    console.log('Initial values:', {
                        initialProvince,
                        initialDistrict,
                        initialWard
                    });
                    if (initialProvince) {
                        const normalizedProvince = decodeHtml(initialProvince);
                        provinceSelect.value = addressData.find(p => p.name === normalizedProvince)?.name || '';
                        if (provinceSelect.value) {
                            console.log('Setting province:', provinceSelect.value);
                            provinceSelect.dispatchEvent(new Event('change'));
                            if (initialDistrict) {
                                const normalizedDistrict = decodeHtml(initialDistrict);
                                districtSelect.value = addressData
                                    .find(p => p.name === provinceSelect.value)?.districts
                                    .find(d => d.name === normalizedDistrict)?.name || '';
                                if (districtSelect.value) {
                                    console.log('Setting district:', districtSelect.value);
                                    districtSelect.dispatchEvent(new Event('change'));
                                    if (initialWard) {
                                        const normalizedWard = decodeHtml(initialWard);
                                        wardSelect.value = addressData
                                            .find(p => p.name === provinceSelect.value)?.districts
                                            .find(d => d.name === districtSelect.value)?.wards
                                            .find(w => w.name === normalizedWard)?.name || '';
                                        console.log('Setting ward:', wardSelect.value);
                                    }
                                }
                            }
                        }
                    }
                @endif
                updateAddressField();
            } catch (error) {
                console.error('Error fetching address data:', error);
                alert('Không thể tải dữ liệu địa chỉ. Vui lòng thử lại sau hoặc kiểm tra console để biết chi tiết.');
            }
        }

        function populateProvinces() {
            console.log('Populating provinces...');
            provinceSelect.innerHTML = '<option value="">Chọn tỉnh/thành phố</option>';
            if (!Array.isArray(addressData)) {
                console.error('addressData is not an array:', addressData);
                return;
            }
            addressData.sort((a, b) => a.name.localeCompare(b.name, 'vi')).forEach(province => {
                const option = document.createElement('option');
                option.value = province.name;
                option.textContent = province.name;
                provinceSelect.appendChild(option);
            });
            provinceSelect.disabled = false;
            console.log('Provinces populated:', provinceSelect.options.length - 1, 'options');
        }

        function populateDistricts(provinceName) {
            console.log('Populating districts for province:', provinceName);
            districtSelect.innerHTML = '<option value="">Chọn quận/huyện</option>';
            wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
            wardSelect.disabled = true;
            districtSelect.disabled = true;

            const province = addressData.find(p => p.name === provinceName);
            if (province && province.districts) {
                province.districts.sort((a, b) => a.name.localeCompare(b.name, 'vi')).forEach(district => {
                    const option = document.createElement('option');
                    option.value = district.name;
                    option.textContent = district.name;
                    districtSelect.appendChild(option);
                });
                districtSelect.disabled = false;
                console.log('Districts populated:', districtSelect.options.length - 1, 'options');
            } else {
                console.warn('No districts found for province:', provinceName);
            }
            updateAddressField();
        }

        function populateWards(provinceName, districtName) {
            console.log('Populating wards for district:', districtName);
            wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
            wardSelect.disabled = true;

            const province = addressData.find(p => p.name === provinceName);
            if (province && province.districts) {
                const district = province.districts.find(d => d.name === districtName);
                if (district && district.wards) {
                    district.wards.sort((a, b) => a.name.localeCompare(b.name, 'vi')).forEach(ward => {
                        const option = document.createElement('option');
                        option.value = ward.name;
                        option.textContent = ward.name;
                        wardSelect.appendChild(option);
                    });
                    wardSelect.disabled = false;
                    console.log('Wards populated:', wardSelect.options.length - 1, 'options');
                } else {
                    console.warn('No wards found for district:', districtName);
                }
            }
            updateAddressField();
        }

        provinceSelect.addEventListener('change', () => {
            const provinceName = provinceSelect.value;
            console.log('Province changed:', provinceName);
            populateDistricts(provinceName);
        });

        districtSelect.addEventListener('change', () => {
            const provinceName = provinceSelect.value;
            const districtName = districtSelect.value;
            console.log('District changed:', districtName);
            populateWards(provinceName, districtName);
        });

        wardSelect.addEventListener('change', () => {
            console.log('Ward changed:', wardSelect.value);
            updateAddressField();
        });

        streetInput.addEventListener('input', () => {
            console.log('Street input changed:', streetInput.value);
            updateAddressField();
        });

        window.addEventListener('load', () => {
            console.log('Window loaded, fetching address data...');
            fetchAddressData();
        });
    </script>
@endpush
