<?php

namespace App\Http\Requests\Admin;

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
        $rules = [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('categories', 'name')->ignore($this->route('category')?->id),
            ],
            'parent_id' => ['nullable', 'exists:categories,id'],
            'is_active' => ['required', 'boolean'],
        ];

        // Thêm validate cho icon khi tạo mới
        if ($this->isMethod('POST')) {
            $rules['icon'] = [
                'required',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:2048', 
            ];
        }

        // Validate icon khi cập nhật (nếu có file mới)
        if ($this->isMethod('PUT') && $this->hasFile('icon')) {
            $rules['icon'] = [
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:2048',
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên danh mục',
            'name.unique' => 'Tên danh mục đã tồn tại',
            'name.max' => 'Tên danh mục không được vượt quá :max ký tự',
            'parent_id.exists' => 'Danh mục cha không tồn tại',
            'is_active.required' => 'Vui lòng chọn trạng thái',
            'is_active.boolean' => 'Trạng thái không hợp lệ',
            'icon.required' => 'Vui lòng chọn ảnh danh mục',
            'icon.image' => 'File phải là định dạng hình ảnh',
            'icon.mimes' => 'Ảnh phải có định dạng: jpeg, png, jpg, gif hoặc webp',
            'icon.max' => 'Kích thước ảnh không được vượt quá 2MB',
        ];
    }
}
