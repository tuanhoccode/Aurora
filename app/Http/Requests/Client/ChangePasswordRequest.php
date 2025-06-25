<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
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
            'old_password' => 'required|string|min:6|',
            'new_password' => 'required|string|min:6|same:con_new_password',
        ];
    }
    public function messages(): array
    {
        return [

            'old_password.required' => 'Vui lòng nhập mật khẩu.',
            'old_password.min' => 'Mật khẩu phải có ít nhất :min ký tự.',
            'old_password.confirmed' => 'Mật khẩu xác nhận không khớp.',
            'new_password.required' => 'Vui lòng nhập mật khẩu mới.',
            'new_password.min' => 'Mật khẩu mới phải có ít nhất :min ký tự.',
            'new_password.same' => 'Mật khẩu xác nhận không khớp.',
        ];
    }
}
