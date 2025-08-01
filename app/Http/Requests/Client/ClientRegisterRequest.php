<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class ClientRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'fullname' => 'required|string|max:100',
            'email' => 'required|email|max:255|regex:/^[^@]+@[^@]+\.[^@]+$/|unique:users,email',
            'password' => 'required|string|min:6|max:255|regex:/[A-Z]/'
        ];
    }
    public function messages(): array
    {
        return
            [
                'fullname.required' => 'Vui lòng nhập lại tên',
                'fullname.string' => 'Họ tên không hợp lệ',
                'fullname.max' => 'Họ tên tối đa 100 ký tự',

                'email.required' => 'Vui lòng nhập email',
                'email.email' => 'Email không đúng định dạng',
                'email.exists' => 'Email chưa được đăng kí',
                'email.unique' => 'Email đã được sử dụng',
                'email.max' => 'Email không được quá 255 ký tự',
                'email.regex' => 'Email phải chứa ký tự @ và "."',

                'password.required' => 'Vui lòng nhập mật khẩu',
                'password.string' => 'Mật khẩu không hợp lệ',
                'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
                'password.max' => 'Mật khẩu không được quá 255 ký tự',
                'password.regex' => 'Mật khẩu phải có ít nhất 1 chữ cái in hoa',


            ];
    }
}
