<?php

namespace App\Http\Controllers\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'parent_id' => ['nullable', 'exists:categories,id'],
            'icon' => ['nullable', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:255', Rule::unique('categories')->ignore($this->category)],
            'is_active' => ['boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function messages(): array
    {
        return [
            'parent_id.exists' => 'Danh mục cha không tồn tại trong hệ thống.',
            'name.required' => 'Vui lòng nhập tên danh mục.',
            'name.string' => 'Tên danh mục phải là chuỗi ký tự.',
            'name.max' => 'Tên danh mục không được vượt quá 255 ký tự.',
            'name.unique' => 'Tên danh mục này đã được sử dụng.',
            'icon.string' => 'Icon phải là chuỗi ký tự.',
            'icon.max' => 'Icon không được vượt quá 50 ký tự.',
            'is_active.boolean' => 'Trạng thái không hợp lệ.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'parent_id' => 'Danh mục cha',
            'name' => 'Tên danh mục',
            'icon' => 'Icon',
            'is_active' => 'Trạng thái',
        ];
    }
} 