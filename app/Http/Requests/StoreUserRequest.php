<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Cho phép tất cả user gửi request
    }

    public function rules()
    {
        return [
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required|string|regex:/^\d{10}$/',
            'password' => 'required|string|min:6|confirmed',
            'gender' => 'required|in:male,female,other',
            'birthday' => 'required|date',
            'role' => 'required|in:customer,employee,admin',
            'status' => 'required|in:active,inactive',
            'bank_name' => 'required|string|max:255',
            'user_bank_name' => 'required|string|max:255',
            'bank_account' => 'required|string|regex:/^\d{10,}$/',
            'avatar' => 'nullable|image|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'fullname.required' => 'Họ và tên không được để trống!',
            'email.required' => 'Email không được để trống!',
            'email.email' => 'Email không hợp lệ!',
            'email.unique' => 'Email đã tồn tại!',
            'phone_number.required' => 'Số điện thoại không được để trống!',
            'phone_number.regex' => 'Số điện thoại phải là 10 chữ số!',
            'password.required' => 'Mật khẩu không được để trống!',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự!',
            'password.confirmed' => 'Mật khẩu và xác nhận mật khẩu không khớp!',
            'gender.required' => 'Vui lòng chọn giới tính!',
            'birthday.required' => 'Ngày sinh không được để trống!',
            'role.required' => 'Vui lòng chọn vai trò!',
            'status.required' => 'Vui lòng chọn trạng thái!',
            'bank_name.required' => 'Tên ngân hàng không được để trống!',
            'user_bank_name.required' => 'Tên tài khoản ngân hàng không được để trống!',
            'bank_account.required' => 'Số tài khoản ngân hàng không được để trống!',
            'bank_account.regex' => 'Số tài khoản ngân hàng phải là số và ít nhất 10 chữ số!',
            'avatar.image' => 'Ảnh đại diện phải là file ảnh!',
            'avatar.max' => 'Ảnh đại diện không được vượt quá 2MB!',
        ];
    }
}