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

        if ($this->type === 'variant') {
            $rules['variant_attributes'] = 'required|array|min:1';
            $rules['variant_attributes.*'] = 'required|exists:attributes,id';
            $rules['attribute_values'] = 'required|array';
            $rules['attribute_values.*'] = 'required|array|min:1';
            $rules['variants'] = 'required|array|min:1';
            $rules['variants.*.sku'] = 'required|string|max:50|distinct';
            $rules['variants.*.price'] = 'required|numeric|min:0';
            $rules['variants.*.sale_price'] = 'nullable|numeric|min:0';
            $rules['variants.*.stock'] = 'required|integer|min:0';
            $rules['variants.*.is_active'] = 'nullable|boolean';
            $rules['variants.*.attributes'] = 'required|array';
            $rules['variants.*.attributes.*'] = 'required|exists:attribute_values,id';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên sản phẩm',
            'name.max' => 'Tên sản phẩm không được vượt quá 255 ký tự',
            'category_id.required' => 'Vui lòng chọn danh mục',
            'category_id.exists' => 'Danh mục không tồn tại',
            'brand_id.exists' => 'Thương hiệu không tồn tại',
            'sku.unique' => 'SKU đã tồn tại',
            'sku.max' => 'SKU không được vượt quá 50 ký tự',
            'price.required' => 'Vui lòng nhập giá',
            'price.numeric' => 'Giá phải là số',
            'price.min' => 'Giá phải lớn hơn hoặc bằng 0',
            'sale_price.numeric' => 'Giá khuyến mãi phải là số',
            'sale_price.min' => 'Giá khuyến mãi phải lớn hơn hoặc bằng 0',
            'sale_price.lt' => 'Giá khuyến mãi phải nhỏ hơn giá gốc',
            'stock.required' => 'Vui lòng nhập số lượng tồn kho',
            'stock.integer' => 'Số lượng tồn kho phải là số nguyên',
            'stock.min' => 'Số lượng tồn kho phải lớn hơn hoặc bằng 0',
            'type.required' => 'Vui lòng chọn loại sản phẩm',
            'type.in' => 'Loại sản phẩm không hợp lệ',
            'thumbnail.image' => 'File phải là hình ảnh',
            'thumbnail.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif',
            'thumbnail.max' => 'Kích thước hình ảnh không được vượt quá 2MB',

            // Messages cho biến thể
            'variant_attributes.required' => 'Vui lòng chọn ít nhất một thuộc tính cho biến thể',
            'variant_attributes.array' => 'Dữ liệu thuộc tính không hợp lệ',
            'variant_attributes.min' => 'Vui lòng chọn ít nhất một thuộc tính cho biến thể',
            'variant_attributes.*.exists' => 'Thuộc tính không tồn tại',
            'attribute_values.required' => 'Vui lòng chọn giá trị cho các thuộc tính',
            'attribute_values.array' => 'Dữ liệu giá trị thuộc tính không hợp lệ',
            'attribute_values.*.required' => 'Vui lòng chọn giá trị cho thuộc tính',
            'attribute_values.*.array' => 'Dữ liệu giá trị thuộc tính không hợp lệ',
            'attribute_values.*.min' => 'Vui lòng chọn ít nhất một giá trị cho thuộc tính',
            'variants.required' => 'Vui lòng tạo ít nhất một biến thể',
            'variants.array' => 'Dữ liệu biến thể không hợp lệ',
            'variants.min' => 'Vui lòng tạo ít nhất một biến thể',
            'variants.*.sku.required' => 'Vui lòng nhập SKU cho biến thể',
            'variants.*.sku.max' => 'SKU của biến thể không được vượt quá 50 ký tự',
            'variants.*.sku.distinct' => 'SKU của các biến thể không được trùng nhau',
            'variants.*.price.required' => 'Vui lòng nhập giá cho biến thể',
            'variants.*.price.numeric' => 'Giá biến thể phải là số',
            'variants.*.price.min' => 'Giá biến thể phải lớn hơn hoặc bằng 0',
            'variants.*.sale_price.numeric' => 'Giá khuyến mãi biến thể phải là số',
            'variants.*.sale_price.min' => 'Giá khuyến mãi biến thể phải lớn hơn hoặc bằng 0',
            'variants.*.stock.required' => 'Vui lòng nhập số lượng tồn kho cho biến thể',
            'variants.*.stock.integer' => 'Số lượng tồn kho biến thể phải là số nguyên',
            'variants.*.stock.min' => 'Số lượng tồn kho biến thể phải lớn hơn hoặc bằng 0',
            'variants.*.attributes.required' => 'Vui lòng chọn giá trị thuộc tính cho biến thể',
            'variants.*.attributes.array' => 'Dữ liệu giá trị thuộc tính không hợp lệ',
            'variants.*.attributes.*.required' => 'Vui lòng chọn giá trị thuộc tính cho biến thể',
            'variants.*.attributes.*.exists' => 'Giá trị thuộc tính không tồn tại'
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