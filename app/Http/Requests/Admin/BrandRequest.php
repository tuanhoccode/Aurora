<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BrandRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $brandId = $this->route('brand') ? $this->route('brand')->id : null;
        return [
            'name' => 'required|string|max:255|unique:brands,name,' . $brandId,
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Tên thương hiệu là bắt buộc.',
            'name.unique' => 'Tên thương hiệu đã tồn tại.',
            'name.max' => 'Tên thương hiệu tối đa 255 ký tự.',
            'logo.image' => 'Logo phải là file ảnh.',
            'logo.mimes' => 'Logo phải có định dạng: jpeg, png, jpg, gif, webp.',
            'logo.max' => 'Logo tối đa 2MB.',
        ];
    }
} 