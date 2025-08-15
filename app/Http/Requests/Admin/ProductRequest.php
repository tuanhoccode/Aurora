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
            'sale_starts_at' => 'nullable|date|required_with:sale_price',
            'sale_ends_at' => 'nullable|date|after:sale_starts_at|required_with:sale_price',
            'type' => 'required|in:simple,digital,variant',
            'thumbnail' => $this->isMethod('PUT') ? 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048' : 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'nullable|boolean',
            'is_sale' => 'nullable|boolean',
        ];

        // Nếu là sản phẩm biến thể
        if ($this->input('type') === 'variant') {
            if ($this->isMethod('POST')) {
                // Khi tạo mới: bắt buộc phải có variants
                $rules['variants'] = 'required|array|min:1';
            } else {
                // Khi update: chỉ cần variants hoặc variants_old có ít nhất 1 biến thể
                $rules['variants'] = 'nullable|array';
                $rules['variants_old'] = 'nullable|array';
            }
            $rules['variants.*.attributes'] = 'required|array|min:1';
            $rules['variants.*.attributes.*'] = 'required|exists:attribute_values,id';
            $rules['variants.*.image'] = 'required|image|mimes:jpeg,png,jpg,gif|max:2048';
            $rules['variants.*.sku'] = [
                'nullable',
                'string',
                'max:50'
            ];
            $rules['variants.*.price'] = 'required|numeric|min:0';
            $rules['variants.*.sale_price'] = 'nullable|numeric|min:0';
            $rules['variants.*.sale_starts_at'] = 'nullable|date|required_with:sale_price';
            $rules['variants.*.sale_ends_at'] = 'nullable|date|after:sale_starts_at|required_with:sale_price';
            $rules['variants.*.stock'] = 'required|numeric|min:0';
            $rules['variants.*.gallery_images.*'] = 'nullable|string';
        }

        return $rules;
    }






    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Khi update sản phẩm biến thể: phải có ít nhất 1 biến thể (cũ hoặc mới)
            if ($this->input('type') === 'variant' && $this->isMethod('PUT')) {
                $variants = $this->input('variants', []);
                $variantsOld = $this->input('variants_old', []);
                if (empty($variants) && empty($variantsOld)) {
                    $validator->errors()->add('variants', 'Vui lòng thêm ít nhất một biến thể cho sản phẩm biến thể');
                }
            }

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
                   
                    // Kiểm tra thời gian khuyến mãi
                    if (!empty($variant['sale_price'])) {
                        if (empty($variant['sale_starts_at'])) {
                            $validator->errors()->add("variants.{$index}.sale_starts_at", 'Vui lòng chọn thời gian bắt đầu khuyến mãi');
                        }
                        if (empty($variant['sale_ends_at'])) {
                            $validator->errors()->add("variants.{$index}.sale_ends_at", 'Vui lòng chọn thời gian kết thúc khuyến mãi');
                        }
                        if (!empty($variant['sale_starts_at']) && !empty($variant['sale_ends_at'])) {
                            if (strtotime($variant['sale_starts_at']) >= strtotime($variant['sale_ends_at'])) {
                                $validator->errors()->add("variants.{$index}.sale_ends_at", 'Thời gian kết thúc phải sau thời gian bắt đầu');
                            }
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
            'sale_starts_at.required_with' => 'Vui lòng chọn thời gian bắt đầu khuyến mãi khi nhập giá khuyến mãi',
            'sale_ends_at.required_with' => 'Vui lòng chọn thời gian kết thúc khuyến mãi khi nhập giá khuyến mãi',
            'sale_ends_at.after' => 'Thời gian kết thúc khuyến mãi phải sau thời gian bắt đầu',
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
            'variants.required_if' => 'Vui lòng thêm ít nhất một biến thể cho sản phẩm biến thể',
            'variants.array' => 'Dữ liệu biến thể không hợp lệ',
            'variants.min' => 'Vui lòng tạo ít nhất một biến thể',
            'variants.*.sku.string' => 'SKU phải là chuỗi',
            'variants.*.sku.max' => 'SKU không được vượt quá 50 ký tự',
            'variants.*.sku.unique' => 'SKU đã tồn tại trong hệ thống',
            'variants.*.price.required' => 'Giá bán là bắt buộc',
            'variants.*.price.numeric' => 'Giá bán phải là số',
            'variants.*.price.min' => 'Giá bán không được âm',
            'variants.*.sale_price.numeric' => 'Giá khuyến mãi phải là số',
            'variants.*.sale_price.min' => 'Giá khuyến mãi không được âm',
            'variants.*.sale_starts_at.required_with' => 'Vui lòng chọn thời gian bắt đầu khuyến mãi khi nhập giá khuyến mãi',
            'variants.*.sale_ends_at.required_with' => 'Vui lòng chọn thời gian kết thúc khuyến mãi khi nhập giá khuyến mãi',
            'variants.*.sale_ends_at.after' => 'Thời gian kết thúc khuyến mãi phải sau thời gian bắt đầu',
            'variants.*.stock.required' => 'Số lượng tồn kho là bắt buộc',
            'variants.*.stock.numeric' => 'Số lượng tồn kho phải là số',
            'variants.*.stock.min' => 'Số lượng tồn kho không được âm',
            'variants.*.image.required' => 'Vui lòng chọn ảnh đại diện cho biến thể',
            'variants.*.image.image' => 'File phải là hình ảnh',
            'variants.*.image.mimes' => 'Định dạng ảnh không hợp lệ. Chấp nhận: jpeg, png, jpg, gif',
            'variants.*.image.max' => 'Kích thước ảnh không được vượt quá 2MB',
            'variants.*.attributes.required' => 'Vui lòng chọn ít nhất một thuộc tính cho biến thể',
            'variants.*.attributes.array' => 'Thuộc tính phải là mảng',
            'variants.*.attributes.min' => 'Vui lòng chọn ít nhất một thuộc tính',
            'variants.*.attributes.*.required' => 'Giá trị thuộc tính không được để trống',
            'variants.*.attributes.*.exists' => 'Giá trị thuộc tính không tồn tại',
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
            'sale_starts_at' => 'Bắt đầu khuyến mãi',
            'sale_ends_at' => 'Kết thúc khuyến mãi',
            'type' => 'Loại sản phẩm',
            'thumbnail' => 'Ảnh đại diện',
            'gallery_images' => 'Hình ảnh',
            'gallery_images.*' => 'Hình ảnh',
            'is_active' => 'Trạng thái',
            'is_sale' => 'Giảm giá'
        ];
    }
}