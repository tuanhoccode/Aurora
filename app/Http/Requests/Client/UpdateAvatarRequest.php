<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAvatarRequest extends FormRequest
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
            'avatar' => 'required|image|mimes:jpeg,png,jpg,jfif,svg|max:2048',
        ];
    }
    public function messages():array
    {
        return[
            'avatar.required' => 'Vui lòng chọn ảnh đại diện.',
            'avatar.image' => 'Tệp tải lên phải là hình ảnh.',
            'avatar.mimes' => 'Chỉ chấp nhận định dạng jpeg, png,jfif, jpg, svg.',
            'avatar.max' => 'Kích thước ảnh không được vượt quá 2MB.',
        ];
    }
}
