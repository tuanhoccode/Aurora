<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminLoginRequest extends FormRequest
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
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:6'
        ];  
    }
     public function messages(): array
    {
        return
            [
                'email.required' => 'Không được để trống email',
                'email.email' => 'Email không đúng định dạng',
                'email.exists' => 'Email chưa được đăng kí',
                'password.required' => 'Password không được để trống',
                'password.string' => 'Pasword chưa được đăng kí',
                'password.min' => 'Password có độ dài ngắn hơn 6 kí tự',
                
            ];
    }
}
