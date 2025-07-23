<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SaveAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        $user = Auth::user();

        return [
            'fullname' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\sÀ-ỹ]+$/'],
            'phone_number' => ['required', 'string', 'regex:/^0[35789][0-9]{8}$/'],
            'email' => ['required', 'email', 'max:255'],
            'province' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z\sÀ-ỹ]+$/'],
            'district' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z\sÀ-ỹ]+$/'],
            'ward' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z\sÀ-ỹ]+$/'],
            'street' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z0-9\s,À-ỹ]+$/'],
            'address' => ['required', 'string', 'max:255'],
            'address_type' => ['required', 'in:home,office'],
            'is_default' => ['nullable', 'boolean'],
            'address_id' => ['nullable', 'exists:user_addresses,id,user_id,' . $user->id],
        ];
    }

    public function messages(): array
    {
        return [
            'fullname.regex' => 'Họ và tên chỉ được chứa chữ cái và dấu cách.',
            'phone_number.regex' => 'Số điện thoại phải bắt đầu bằng 0, theo sau là 9 chữ số (bắt đầu bằng 3, 5, 7, 8, hoặc 9).',
            'province.regex' => 'Tỉnh/Thành phố chỉ được chứa chữ cái và dấu cách.',
            'district.regex' => 'Quận/Huyện chỉ được chứa chữ cái và dấu cách.',
            'ward.regex' => 'Phường/Xã chỉ được chứa chữ cái và dấu cách.',
            'street.regex' => 'Địa chỉ cụ thể chỉ được chứa chữ cái, số, dấu cách hoặc dấu phẩy.',
            'address_id.exists' => 'Địa chỉ được chọn không hợp lệ hoặc không thuộc về bạn.',
        ];
    }
}