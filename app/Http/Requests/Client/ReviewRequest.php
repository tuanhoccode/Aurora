<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
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
            // 'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'required|string|max:200',
        ];
    }
    public function messages(): array
    {
        return [
            // 'rating.required' => 'Vui lòng chọn số sao đánh giá.',
            // 'rating.integer' => 'Số sao phải là số nguyên.',
            // 'rating.min' => 'Số sao tối thiểu là 1.',
            // 'rating.max' => 'Số sao tối đa là 5.',

            'review_text.required' => 'Vui lòng nhập nội dung đánh giá.',
            'review_text.string' => 'Nội dung đánh giá không hợp lệ.',
            'review_text.max' => 'Nội dung đánh giá tối đa 200 ký tự.',
        ];
    }
}
