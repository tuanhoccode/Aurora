<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ReplyRequest extends FormRequest
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
            'content' => 'required|string|min:3|max:255'
        ];
    }
    public function messages(): array
    {
        return [
            'content.required' => 'Nội dung phản hồi không được để trống.',
            'content.min' => 'Nội dung phản hồi quá ngắn (tối thiểu 3 ký tự).',
            'content.max' => 'Nội dung phản hồi quá dài (tối đa 255 ký tự).',
        ];
    }
}
