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
            'variants' => 'required|array|min:1',
            'variants.*.sku' => 'nullable|string|max:255|unique:product_variants,sku',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.regular_price' => 'required|numeric|min:0',
            'variants.*.sale_price' => 'nullable|numeric|min:0|lt:variants.*.regular_price|required_without:variants.*.regular_price',
            'variants.*.img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'variants.*.attribute_values' => 'required|array|min:1',
            'variants.*.attribute_values.*' => 'required|exists:attribute_values,id',
        ];
    }

    public function messages(): array
    {
        return [
            'variants.required' => 'Phải có ít nhất một biến thể',
            'variants.array' => 'Dữ liệu biến thể không hợp lệ',
            'variants.min' => 'Phải có ít nhất một biến thể',
            'variants.*.sku.unique' => 'Mã SKU đã tồn tại',
            'variants.*.sku.max' => 'Mã SKU không được vượt quá 255 ký tự',
            'variants.*.stock.required' => 'Số lượng tồn kho là bắt buộc',
            'variants.*.stock.integer' => 'Số lượng tồn kho phải là số nguyên',
            'variants.*.stock.min' => 'Số lượng tồn kho không được âm',
            'variants.*.regular_price.required' => 'Giá gốc là bắt buộc',
            'variants.*.regular_price.numeric' => 'Giá gốc phải là số',
            'variants.*.regular_price.min' => 'Giá gốc không được âm',
            'variants.*.sale_price.numeric' => 'Giá khuyến mãi phải là số',
            'variants.*.sale_price.min' => 'Giá khuyến mãi không được âm',
            'variants.*.sale_price.lt' => 'Giá khuyến mãi phải nhỏ hơn giá gốc',
            'variants.*.img.image' => 'File phải là hình ảnh',
            'variants.*.img.max' => 'Kích thước hình ảnh không được vượt quá 2MB',
            'variants.*.attribute_values.required' => 'Vui lòng chọn ít nhất 1 thuộc tính',
            'variants.*.attribute_values.array' => 'Dữ liệu thuộc tính không hợp lệ',
            'variants.*.attribute_values.min' => 'Phải chọn ít nhất 1 thuộc tính',
            'variants.*.attribute_values.*.exists' => 'Giá trị thuộc tính không tồn tại',
        ];
    }
}