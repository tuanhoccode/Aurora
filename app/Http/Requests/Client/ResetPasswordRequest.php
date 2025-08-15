<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
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
            'token' =>'required|string',
            'email' => 'required|email|exists:users,email|max:255',
            'password' => 'required|string|min:6|confirmed|max:255|regex:/[A-Z]/',
        ];
    }
    public function messages(): array
    {
        return [
            'token.required' => 'Thiếu mã khôi phục mật khẩu',

            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'email.exists' => 'Email không tồn tại trong hệ thống.',
            'email.max' => 'Email không được quá 255 ký tự.',
            
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.max' => 'Mật khẩu không được quá 255 ký tự.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
            'password.regex' => 'Mật khẩu phải có ít nhất 1 chữ cái in hoa.',
        ];
    }
}
