<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'sku' => 'nullable|string|max:50|unique:products,sku,' . $this->product?->id,
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'stock' => 'required|integer|min:0',
            'type' => 'required|in:simple,variant',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'nullable|boolean'
        ];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên sản phẩm là bắt buộc',
            'name.max' => 'Tên sản phẩm không được vượt quá 255 ký tự',
            'category_id.required' => 'Danh mục là bắt buộc',
            'category_id.exists' => 'Danh mục không tồn tại',
            'brand_id.exists' => 'Thương hiệu không tồn tại',
            'price.required' => 'Giá sản phẩm là bắt buộc',
            'price.numeric' => 'Giá sản phẩm phải là số',
            'price.min' => 'Giá sản phẩm không được âm',
            'sale_price.numeric' => 'Giá khuyến mãi phải là số',
            'sale_price.min' => 'Giá khuyến mãi không được âm',
            'sale_price.lt' => 'Giá khuyến mãi phải nhỏ hơn giá gốc',
            'stock.required' => 'Số lượng tồn kho là bắt buộc',
            'stock.integer' => 'Số lượng tồn kho phải là số nguyên',
            'stock.min' => 'Số lượng tồn kho không được âm',
            'sku.unique' => 'Mã SKU đã tồn tại',
            'thumbnail.image' => 'File phải là hình ảnh',
            'thumbnail.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif',
            'thumbnail.max' => 'Kích thước hình ảnh không được vượt quá 2MB'
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'price' => $this->price ? str_replace(',', '.', $this->price) : null,
            'sale_price' => $this->sale_price ? str_replace(',', '.', $this->sale_price) : null,
        ]);
    }

    public function attributes(): array
    {
        return [
            'name' => 'Tên sản phẩm',
            'category_id' => 'Danh mục',
            'brand_id' => 'Thương hiệu',
            'type' => 'Loại sản phẩm',
            'sku' => 'Mã SKU',
            'price' => 'Giá gốc',
            'sale_price' => 'Giá khuyến mãi',
            'stock' => 'Số lượng tồn kho',
            'thumbnail' => 'Ảnh đại diện',
            'gallery' => 'Thư viện ảnh',
            'description' => 'Mô tả chi tiết',
            'short_description' => 'Mô tả ngắn',
            'is_active' => 'Trạng thái',
        ];
    }
} 