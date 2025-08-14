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
            'review_text' => 'required|string|max:200',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }
    public function messages(): array
    {
        return [
            'review_text.required' => 'Vui lòng nhập nội dung đánh giá.',
            'review_text.string' => 'Nội dung đánh giá không hợp lệ.',
            'review_text.max' => 'Nội dung đánh giá tối đa 200 ký tự.',

            'images.*.image' => 'Tệp tải lên phải là hình ảnh',
            'images.*.mimes' => 'Ảnh phải có định dạng là jpg,jpeg,png,webp',
            'images.*.max' => 'Kích thước của ảnh tối đa là 2MB',
        ];
    }
}
