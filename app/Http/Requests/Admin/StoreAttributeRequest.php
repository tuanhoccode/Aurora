<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttributeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // You can add authorization logic here, e.g., check if the user has permission
        return true; // Assuming all authenticated admin users can create/update attributes
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'is_variant' => 'nullable|boolean', // Accepts 0 or 1
            'is_active' => 'nullable|boolean',  // Accepts 0 or 1
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
            'name.required' => 'Tên thuộc tính là bắt buộc.',
            'name.string' => 'Tên thuộc tính phải là chuỗi ký tự.',
            'name.max' => 'Tên thuộc tính không được vượt quá 255 ký tự.',
            'is_variant.boolean' => 'Giá trị biến thể phải là 0 hoặc 1.',
            'is_active.boolean' => 'Trạng thái phải là 0 hoặc 1.',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // Convert checkbox values to boolean (0 or 1) if needed
        $this->merge([
            'is_variant' => $this->has('is_variant') ? 1 : 0,
            'is_active' => $this->has('is_active') ? 1 : 0,
        ]);
    }
}
