<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProductVariantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sku' => 'required|string|max:255|unique:product_variants,sku',
            'stock' => 'required|integer|min:0',
            'regular_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:regular_price',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'attribute_values' => 'required|array|min:2',
            'attribute_values.*' => 'required|exists:attribute_values,id',
        ];
    }

    public function messages(): array
    {
        return [
            'sku.required' => 'Mã SKU là bắt buộc',
            'sku.unique' => 'Mã SKU đã tồn tại',
            'stock.required' => 'Số lượng tồn kho là bắt buộc',
            'stock.integer' => 'Số lượng tồn kho phải là số nguyên',
            'stock.min' => 'Số lượng tồn kho không được âm',
            'regular_price.required' => 'Giá gốc là bắt buộc',
            'regular_price.numeric' => 'Giá gốc phải là số',
            'regular_price.min' => 'Giá gốc không được âm',
            'sale_price.numeric' => 'Giá khuyến mãi phải là số',
            'sale_price.min' => 'Giá khuyến mãi không được âm',
            'sale_price.lt' => 'Giá khuyến mãi phải nhỏ hơn giá gốc',
            'img.image' => 'File phải là hình ảnh',
            'img.max' => 'Kích thước hình ảnh không được vượt quá 2MB',
            'attribute_values.required' => 'Vui lòng chọn ít nhất 2 thuộc tính',
            'attribute_values.array' => 'Dữ liệu thuộc tính không hợp lệ',
            'attribute_values.min' => 'Phải chọn ít nhất 2 thuộc tính',
            'attribute_values.*.exists' => 'Giá trị thuộc tính không tồn tại',
        ];
    }
}
