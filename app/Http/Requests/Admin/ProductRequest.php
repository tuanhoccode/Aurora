<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:250',
            'slug' => 'nullable|string|max:255|unique:products,slug,' . $this->product?->id,
            'brand_id' => 'required|exists:brands,id',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
            'short_description' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'nullable|string|max:50|unique:products,sku,' . $this->product?->id,
            'price' => $this->input('type') === 'variant' ? 'nullable|numeric|min:0' : 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'type' => 'required|in:simple,digital,variant',
            'thumbnail' => $this->isMethod('PUT') ? 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048' : 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'nullable|boolean',
            'is_sale' => 'nullable|boolean'
        ];

        // Thêm validation cho biến thể nếu là sản phẩm biến thể và có dữ liệu variants
        if ($this->input('type') === 'variant' && $this->has('variants')) {
            $rules['variants'] = 'required|array|min:1';
            // Nếu là PUT (update), ignore id của các biến thể cũ
            if ($this->isMethod('PUT') && $this->has('variants_old')) {
                foreach ($this->input('variants_old') as $variantId => $variantData) {
                    $rules["variants_old.$variantId.sku"] = [
                        'nullable',
                        'string',
                        'max:50'
                    ];
                }
            }
            $rules['variants.*.sku'] = [
                'nullable',
                'string',
                'max:50'
            ];
            $rules['variants.*.price'] = 'required|numeric|min:0';
            $rules['variants.*.sale_price'] = 'nullable|numeric|min:0';
            $rules['variants.*.stock'] = 'required|numeric|min:0';
            $rules['variants.*.attributes'] = 'required|array|min:1';
            $rules['variants.*.attributes.*'] = 'exists:attribute_values,id';
            $rules['variants.*.image'] = 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048';
            $rules['variants.*.gallery_images.*'] = 'nullable|string';
        }

        return $rules;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Kiểm tra SKU trùng lặp trong cùng một request cho biến thể (chỉ khi có SKU)
            if ($this->input('type') === 'variant' && $this->has('variants') && !empty($this->input('variants'))) {
                $skus = collect($this->input('variants'))->pluck('sku')->filter();
                $duplicateSkus = $skus->duplicates();
                
                if ($duplicateSkus->count() > 0) {
                    $validator->errors()->add('variants', 'Có SKU trùng lặp trong danh sách biến thể: ' . $duplicateSkus->implode(', '));
                }
                
                // Kiểm tra giá khuyến mãi không lớn hơn giá gốc
                foreach ($this->input('variants') as $index => $variant) {
                    if (!empty($variant['sale_price']) && !empty($variant['price'])) {
                        if ($variant['sale_price'] >= $variant['price']) {
                            $validator->errors()->add("variants.{$index}.sale_price", 'Giá khuyến mãi phải nhỏ hơn giá gốc');
                        }
                    }
                }
            }
            
            // Kiểm tra SKU trùng lặp cho variants_old (khi cập nhật sản phẩm)
            if ($this->isMethod('PUT') && $this->input('type') === 'variant' && $this->has('variants_old')) {
                $allSkus = [];
                $duplicateSkus = [];
                
                // Thu thập tất cả SKU từ variants_old
                foreach ($this->input('variants_old') as $variantId => $variantData) {
                    $sku = $variantData['sku'] ?? null;
                    if ($sku) {
                        if (in_array($sku, $allSkus)) {
                            $duplicateSkus[] = $sku;
                        } else {
                            $allSkus[] = $sku;
                        }
                    }
                }
                
                // Thu thập SKU từ variants mới (nếu có)
                if ($this->has('variants') && !empty($this->input('variants'))) {
                    foreach ($this->input('variants') as $variantData) {
                        $sku = $variantData['sku'] ?? null;
                        if ($sku) {
                            if (in_array($sku, $allSkus)) {
                                $duplicateSkus[] = $sku;
                            } else {
                                $allSkus[] = $sku;
                            }
                        }
                    }
                }
                
                // Báo lỗi nếu có SKU trùng lặp
                if (!empty($duplicateSkus)) {
                    $uniqueDuplicates = array_unique($duplicateSkus);
                    $validator->errors()->add('variants_old', 'Có SKU trùng lặp trong danh sách biến thể: ' . implode(', ', $uniqueDuplicates));
                }
            }
        });
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
            'type.in' => 'Loại sản phẩm không hợp lệ',
            // Messages cho biến thể
            'variants.required' => 'Vui lòng tạo ít nhất một biến thể',
            'variants.array' => 'Dữ liệu biến thể không hợp lệ',
            'variants.min' => 'Vui lòng tạo ít nhất một biến thể',
            'variants.*.sku.string' => 'SKU phải là chuỗi',
            'variants.*.sku.max' => 'SKU không được vượt quá 50 ký tự',
            'variants.*.sku.unique' => 'SKU đã tồn tại trong hệ thống',
            'variants.*.price.required' => 'Giá của biến thể là bắt buộc',
            'variants.*.price.numeric' => 'Giá phải là số',
            'variants.*.price.min' => 'Giá không được âm',
            'variants.*.sale_price.numeric' => 'Giá khuyến mãi phải là số',
            'variants.*.sale_price.min' => 'Giá khuyến mãi không được âm',
            'variants.*.stock.required' => 'Tồn kho của biến thể là bắt buộc',
            'variants.*.stock.numeric' => 'Tồn kho phải là số',
            'variants.*.stock.min' => 'Tồn kho không được âm',
            'variants.*.attributes.required' => 'Thuộc tính của biến thể là bắt buộc',
            'variants.*.attributes.array' => 'Thuộc tính phải là mảng',
            'variants.*.attributes.min' => 'Vui lòng chọn ít nhất một thuộc tính',
            'variants.*.attributes.*.exists' => 'Thuộc tính không tồn tại',
            'variants.*.image.image' => 'File phải là hình ảnh',
            'variants.*.image.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif',
            'variants.*.image.max' => 'Kích thước hình ảnh không được vượt quá 2MB'
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