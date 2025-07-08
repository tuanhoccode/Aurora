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
            'code' => ['required', 'string', 'max:50', Rule::unique('coupons', 'code')->ignore($couponId)],
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
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'usage_limit' => ['required', 'integer', 'min:1'],
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
            'discount_type.required' => 'Vui lòng chọn loại giảm.',
            'discount_type.in' => 'Loại giảm không hợp lệ.',
            'discount_value.required' => 'Vui lòng nhập giá trị giảm.',
            'discount_value.numeric' => 'Giá trị giảm phải là số.',
            'discount_value.min' => 'Giá trị giảm không được âm.',
            'start_date.required' => 'Vui lòng nhập ngày bắt đầu.',
            'end_date.required' => 'Vui lòng nhập ngày kết thúc.',
            'end_date.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
            'usage_limit.required' => 'Vui lòng nhập giới hạn sử dụng.',
            'usage_limit.integer' => 'Giới hạn sử dụng phải là số nguyên.',
            'usage_limit.min' => 'Giới hạn sử dụng tối thiểu là 1.',
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
