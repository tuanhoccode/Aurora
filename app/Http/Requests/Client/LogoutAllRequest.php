<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class LogoutAllRequest extends FormRequest
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
        //Lấy người dùng hiện tại
        $user = $this->user();
        $isGoogleUser = !empty($user->google_id);
        //Tk gg k cần validate mk
        return $isGoogleUser ?[] : [
            'password' => 'required|string|min:6'
        ];
    }
    public function messages(): array
    {
        return
            [
                'password.required' => 'Vui lòng nhập mật khẩu',
                'password.string' => 'Mật khẩu không hợp lệ',
                'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự', 
            ];
    }
}
