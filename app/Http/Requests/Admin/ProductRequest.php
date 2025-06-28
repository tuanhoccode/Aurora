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
        return [
            'name' => 'required|string|max:250',
            'slug' => 'nullable|string|max:255|unique:products,slug,' . $this->product?->id,
            'brand_id' => 'required|exists:brands,id',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
            'short_description' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'nullable|string|max:50|unique:products,sku,' . $this->product?->id,
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'type' => 'required|in:simple,digital,variant',
            'thumbnail' => $this->isMethod('PUT') ? 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048' : 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'nullable|boolean',
            'is_sale' => 'nullable|boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên sản phẩm là bắt buộc',
            'name.max' => 'Tên sản phẩm không được vượt quá 250 ký tự',
            'categories.required' => 'Vui lòng chọn ít nhất một danh mục',
            'categories.array' => 'Dữ liệu danh mục không hợp lệ',
            'categories.min' => 'Vui lòng chọn ít nhất một danh mục',
            'categories.*.exists' => 'Danh mục không tồn tại',
            'brand_id.required' => 'Thương hiệu là bắt buộc',
            'brand_id.exists' => 'Thương hiệu không tồn tại',
            'short_description.max' => 'Mô tả ngắn không được vượt quá 255 ký tự',
            'description.string' => 'Mô tả chi tiết phải là chuỗi',
            'price.required' => 'Giá sản phẩm là bắt buộc',
            'price.numeric' => 'Giá sản phẩm phải là số',
            'price.min' => 'Giá sản phẩm không được âm',
            'sale_price.numeric' => 'Giá khuyến mãi phải là số',
            'sale_price.min' => 'Giá khuyến mãi không được âm',
            'sale_price.lt' => 'Giá khuyến mãi phải nhỏ hơn giá gốc',
            'sku.unique' => 'Mã SKU đã tồn tại',
            'thumbnail.required' => 'Ảnh đại diện là bắt buộc',
            'thumbnail.image' => 'File phải là hình ảnh',
            'thumbnail.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif',
            'thumbnail.max' => 'Kích thước hình ảnh không được vượt quá 2MB',
            'gallery_images.*.image' => 'File phải là hình ảnh',
            'gallery_images.*.mimes' => 'Chỉ chấp nhận các định dạng: jpeg, png, jpg, gif',
            'gallery_images.*.max' => 'Kích thước hình ảnh không được vượt quá 2MB',
            'type.required' => 'Loại sản phẩm là bắt buộc',
            'type.in' => 'Loại sản phẩm không hợp lệ'
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'is_sale' => $this->boolean('is_sale'),
            'price' => $this->price ? str_replace(',', '.', $this->price) : null,
            'sale_price' => $this->sale_price ? str_replace(',', '.', $this->sale_price) : null,
        ]);
    }

    public function attributes(): array
    {
        return [
            'name' => 'Tên sản phẩm',
            'brand_id' => 'Thương hiệu',
            'categories' => 'Danh mục',
            'short_description' => 'Mô tả ngắn',
            'description' => 'Mô tả chi tiết',
            'sku' => 'Mã SKU',
            'price' => 'Giá sản phẩm',
            'sale_price' => 'Giá khuyến mãi',
            'type' => 'Loại sản phẩm',
            'thumbnail' => 'Ảnh đại diện',
            'gallery_images' => 'Hình ảnh',
            'gallery_images.*' => 'Hình ảnh',
            'is_active' => 'Trạng thái',
            'is_sale' => 'Giảm giá'
        ];
    }
} 