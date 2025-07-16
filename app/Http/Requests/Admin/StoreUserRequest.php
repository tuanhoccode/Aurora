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
            'fullname'           => 'required|string|max:255',
            'email'              => 'required|email|unique:users,email',
            'phone_number'       => 'required|regex:/^0\d{9}$/|unique:users,phone_number',
            'password'           => 'required|string|min:6|confirmed',
            'gender'             => 'required|in:male,female,other',
            'birthday'           => 'required|date|before:today',
            'role'               => 'required|in:customer,employee,admin',
            'status'             => 'required|in:active,inactive',
            'bank_name'          => 'required|string|max:255',
            'user_bank_name'     => 'required|string|max:255',
            'bank_account'       => 'required|regex:/^\d{10,20}$/',
            'avatar'             => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',

            // Thông tin địa chỉ (bảng user_addresses)
            'address'            => 'required|string|max:255',
            'address_name'       => 'required|string|max:100',
            'address_phone'      => 'required|regex:/^0\d{9}$/',
        ];
    }

    public function messages(): array
    {
        return [
            'fullname.required'           => 'Họ và tên không được để trống.',
            'email.required'              => 'Email không được để trống.',
            'email.email'                 => 'Email không đúng định dạng.',
            'email.unique'                => 'Email đã tồn tại.',
            'phone_number.required'       => 'Số điện thoại không được để trống.',
            'phone_number.regex'          => 'Số điện thoại phải bắt đầu bằng 0 và gồm 10 chữ số.',
            'phone_number.unique'         => 'Số điện thoại đã tồn tại.',
            'password.required'           => 'Mật khẩu không được để trống.',
            'password.min'                => 'Mật khẩu phải ít nhất 6 ký tự.',
            'password.confirmed'          => 'Xác nhận mật khẩu không khớp.',
            'gender.required'             => 'Vui lòng chọn giới tính.',
            'gender.in'                   => 'Giới tính không hợp lệ.',
            'birthday.required'           => 'Ngày sinh không được để trống.',
            'birthday.date'               => 'Ngày sinh không hợp lệ.',
            'birthday.before'             => 'Ngày sinh phải nhỏ hơn hôm nay.',
            'role.required'               => 'Vui lòng chọn vai trò.',
            'role.in'                     => 'Vai trò không hợp lệ.',
            'status.required'             => 'Vui lòng chọn trạng thái.',
            'status.in'                   => 'Trạng thái không hợp lệ.',
            'bank_name.required'          => 'Tên ngân hàng không được để trống.',
            'user_bank_name.required'     => 'Tên tài khoản ngân hàng không được để trống.',
            'bank_account.required'       => 'Số tài khoản ngân hàng không được để trống.',
            'bank_account.regex'          => 'Số tài khoản phải là số và có từ 10 đến 20 chữ số.',
            'avatar.image'                => 'Ảnh đại diện phải là file ảnh.',
            'avatar.mimes'                => 'Ảnh đại diện phải là jpg, jpeg, png hoặc gif.',
            'avatar.max'                  => 'Ảnh đại diện không được vượt quá 2MB.',

            'address.required'            => 'Địa chỉ không được để trống.',
            'address_name.required'       => 'Tên người nhận không được để trống.',
            'address_phone.required'      => 'Số điện thoại người nhận không được để trống.',
            'address_phone.regex'         => 'Số điện thoại người nhận phải bắt đầu bằng 0 và gồm 10 chữ số.',
        ];
    }
}
