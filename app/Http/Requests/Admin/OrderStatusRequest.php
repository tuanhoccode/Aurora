<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class OrderStatusRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Hoặc kiểm tra quyền tại đây
    }

    public function rules()
    {
        return [
            'order_status_id' => 'required|exists:order_statuses,id',
            'is_paid' => 'required|boolean',
            'note' => 'nullable|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'order_status_id.required' => 'Vui lòng chọn trạng thái đơn hàng.',
            'order_status_id.exists' => 'Trạng thái đơn hàng không hợp lệ.',
            'is_paid.required' => 'Vui lòng chọn trạng thái thanh toán.',
            'is_paid.boolean' => 'Trạng thái thanh toán không hợp lệ.',
        ];
    }
}
