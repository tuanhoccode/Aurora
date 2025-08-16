<?php

namespace App\Http\Requests\admin;

use Illuminate\Foundation\Http\FormRequest;

class RejectCommentRequest extends FormRequest
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
            'reason' => 'required|string|max:255|min:3',
        ];
    }

    public function messages(): array
    {
        return [
            'reason.required' => 'Vui lòng nhập lý do không duyệt.',
            'reason.string' => 'Lý do không hợp lệ.',
            'reason.max' => 'Lý do không được vượt quá 255 ký tự.',
            'reason.min' => 'Lý do phải lớn hơn 3 ký tự.',
        ];
    }
}
