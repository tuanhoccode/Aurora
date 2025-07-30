<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fullname'           => 'required|string|min:2|max:100',
            'email'              => [
                'required',
                'email:rfc,dns',
                'max:100',
                'unique:users,email',
            ],
            'phone_number'       => 'nullable|regex:/^0\d{9,10}$/|unique:users,phone_number',
            'password'           => 'required|string|min:6|max:255|confirmed',
            'gender'             => 'required|in:male,female,other',
            'birthday'           => 'required|date|before:today',
            'role'               => 'required|in:customer,employee,admin',
            'status'             => 'required|in:inactive,active',

            // Thông tin ngân hàng (không bắt buộc)
            'bank_name'          => 'nullable|string|max:100',
            'user_bank_name'     => 'nullable|string|max:100',
            'bank_account'       => 'nullable|regex:/^\d{10,20}$/',

            'avatar'             => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',

            // Địa chỉ người dùng (không bắt buộc)
            'address'            => 'nullable|string|max:255',
            'fullname_address'   => 'nullable|string|min:2|max:100',
            'address_phone'      => 'nullable|regex:/^0\d{9,10}$/',
        ];
    }

    public function messages(): array
    {
        return [
            'fullname.required'           => 'Họ và tên không được để trống.',
            'fullname.min'                => 'Họ và tên phải có ít nhất 2 ký tự.',
            'fullname.max'                => 'Họ và tên không được vượt quá 100 ký tự.',

            'email.required'              => 'Email không được để trống.',
            'email.email'                 => 'Email không đúng định dạng.',
            'email.max'                   => 'Email không được vượt quá 100 ký tự.',
            'email.unique'                => 'Email đã tồn tại.',

            'phone_number.required'       => 'Số điện thoại không được để trống.',
            'phone_number.regex'          => 'Số điện thoại phải bắt đầu bằng 0 và có 10 đến 11 chữ số.',
            'phone_number.unique'         => 'Số điện thoại đã tồn tại.',

            'password.required'           => 'Mật khẩu không được để trống.',
            'password.min'                => 'Mật khẩu phải ít nhất 6 ký tự.',
            'password.max'                => 'Mật khẩu không được vượt quá 255 ký tự.',
            'password.confirmed'          => 'Xác nhận mật khẩu không khớp.',

            'gender.required'             => 'Vui lòng chọn giới tính.',
            'gender.in'                   => 'Giới tính không hợp lệ.',

            'birthday.required'           => 'Ngày sinh không được để trống.',
            'birthday.date'               => 'Ngày sinh không hợp lệ.',
            'birthday.before'             => 'Ngày sinh phải nhỏ hơn ngày hôm nay.',

            'role.required'               => 'Vui lòng chọn vai trò.',
            'role.in'                     => 'Vai trò không hợp lệ.',

            'status.required'             => 'Vui lòng chọn trạng thái.',
            'status.in'                   => 'Trạng thái không hợp lệ.',

            'bank_name.max'               => 'Tên ngân hàng không được vượt quá 100 ký tự.',
            'user_bank_name.max'          => 'Tên tài khoản ngân hàng không được vượt quá 100 ký tự.',
            'bank_account.regex'          => 'Số tài khoản phải là dãy số từ 10 đến 20 chữ số.',

            'avatar.image'                => 'Ảnh đại diện phải là file ảnh.',
            'avatar.mimes'                => 'Ảnh đại diện phải là jpg, jpeg, png hoặc gif.',
            'avatar.max'                  => 'Ảnh đại diện không được vượt quá 2MB.',

            'address.max'                 => 'Địa chỉ không được vượt quá 255 ký tự.',
            'fullname_address.min'        => 'Tên người nhận phải có ít nhất 2 ký tự.',
            'fullname_address.max'        => 'Tên người nhận không được vượt quá 100 ký tự.',
            'address_phone.regex'         => 'Số điện thoại người nhận phải bắt đầu bằng 0 và có 10 đến 11 chữ số.',
        ];
    }
}
