<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $couponId = $this->coupon?->id;

        return [
            'code' => [
                'required',
                'string',
                'min:3',
                'max:50',
                Rule::unique('coupons', 'code')->ignore($couponId),
            ],
            'discount_type' => ['required', 'in:percent,fix_amount'],
            'discount_value' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    if ($this->discount_type === 'percent' && $value > 100) {
                        $fail('Phần trăm giảm giá không được vượt quá 100%.');
                    }
                },
            ],
            'title' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'usage_limit' => ['required', 'numeric', 'min:1', 'max:2000000000'],
            'usage_count' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'is_notified' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Vui lòng nhập mã giảm giá.',
            'code.unique' => 'Mã giảm giá này đã tồn tại.',
            'code.min' => 'Mã giảm giá phải có ít nhất 3 ký tự.',
            'code.max' => 'Mã giảm giá không được vượt quá 50 ký tự.',
            'discount_type.required' => 'Vui lòng chọn loại giảm.',
            'discount_type.in' => 'Loại giảm không hợp lệ.',
            'discount_value.required' => 'Vui lòng nhập giá trị giảm.',
            'discount_value.numeric' => 'Giá trị giảm phải là số.',
            'discount_value.min' => 'Giá trị giảm không được âm.',
            'title.max' => 'Tiêu đề không được vượt quá 50 ký tự.',
            'description.max' => 'Mô tả không được vượt quá 255 ký tự.',
            'start_date.required' => 'Vui lòng nhập ngày bắt đầu.',
            'end_date.required' => 'Vui lòng nhập ngày kết thúc.',
            'end_date.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
            'usage_limit.required' => 'Vui lòng nhập giới hạn sử dụng.',
            'usage_limit.numeric' => 'Giới hạn sử dụng phải là số.',
            'usage_limit.min' => 'Giới hạn sử dụng tối thiểu là 1.',
            'usage_limit.max' => 'Giới hạn sử dụng tối đa là 2 tỷ.',
            'usage_count.integer' => 'Số lượt đã sử dụng phải là số nguyên.',
            'usage_count.min' => 'Số lượt đã sử dụng không được âm.',
        ];
    }

    public function attributes(): array
    {
        return [
            'code' => 'mã giảm giá',
            'discount_type' => 'loại giảm',
            'discount_value' => 'giá trị giảm',
            'title' => 'tiêu đề',
            'description' => 'mô tả',
            'start_date' => 'ngày bắt đầu',
            'end_date' => 'ngày kết thúc',
            'usage_limit' => 'giới hạn sử dụng',
            'usage_count' => 'đã sử dụng',
            'is_active' => 'trạng thái',
            'is_notified' => 'trạng thái thông báo',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'is_notified' => $this->boolean('is_notified'),
        ]);
    }
}
