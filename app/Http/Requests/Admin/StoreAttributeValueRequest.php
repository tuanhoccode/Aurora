<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttributeValueRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Assuming all authenticated admin users can create/update attribute values
        return true; // Add specific authorization logic if needed (e.g., check permissions)
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'value' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'value.required' => 'Giá trị thuộc tính là bắt buộc.',
            'value.string' => 'Giá trị thuộc tính phải là chuỗi ký tự.',
            'value.max' => 'Giá trị thuộc tính không được vượt quá 255 ký tự.',
            'is_active.required' => 'Trạng thái là bắt buộc.',
            'is_active.boolean' => 'Trạng thái phải là Đang hoạt động hoặc Không hoạt động.',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // Convert checkbox or select input for is_active to boolean (0 or 1)
        $this->merge([
            'is_active' => $this->has('is_active') ? 1 : 0,
        ]);
    }
}