<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BannerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $bannerId = $this->route('banner'); // Lấy ID banner nếu đang edit
        
        $rules = [
            'title' => [
                'required',
                'string',
                'max:255',
                'min:2',
                // Đảm bảo tiêu đề không trùng, loại trừ bản ghi hiện tại khi update
                'unique:banners,title' . ($bannerId ? ',' . $bannerId : ''),
            ],
            'subtitle' => 'nullable|string|max:255',
            'link' => 'nullable|url|max:255',
            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
                function ($attribute, $value, $fail) use ($bannerId) {
                    if ($value !== null) {
                        if (!\App\Models\Banner::isSortOrderUnique($value, $bannerId)) {
                            $fail('Thứ tự này đã được sử dụng bởi banner khác. Vui lòng chọn thứ tự khác.');
                        }
                    }
                }
            ],
            'is_active' => 'boolean',
        ];

        // Nếu là tạo mới (POST) thì ảnh bắt buộc
        if ($this->isMethod('POST')) {
            $rules['image'] = 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048|';
        } else {
            // Nếu là cập nhật (PUT/PATCH) thì ảnh tùy chọn
            $rules['image'] = 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048|';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Tiêu đề banner là bắt buộc',
            'title.min' => 'Tiêu đề phải có ít nhất 2 ký tự',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự',
            'title.unique' => 'Tiêu đề này đã được sử dụng. Vui lòng chọn tiêu đề khác.',
            'subtitle.max' => 'Dòng chữ nhỏ không được vượt quá 255 ký tự',
            'image.required' => 'Ảnh banner là bắt buộc',
            'image.image' => 'File phải là hình ảnh',
            'image.mimes' => 'Ảnh phải có định dạng: jpeg, png, jpg, gif, webp',
            'image.max' => 'Kích thước ảnh không được vượt quá 2MB',
            'link.url' => 'Link không đúng định dạng URL',
            'sort_order.integer' => 'Thứ tự phải là số nguyên',
            'sort_order.min' => 'Thứ tự phải lớn hơn hoặc bằng 0',
            'sort_order.unique' => 'Thứ tự này đã được sử dụng bởi banner khác. Vui lòng chọn thứ tự khác.',
        ];
    }
}