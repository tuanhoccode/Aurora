<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    // Cho phép tất cả request thực thi (có thể chỉnh sửa nếu cần giới hạn quyền)
    public function authorize(): bool
    {
        return true;
    }

    // Quy tắc validate cho việc cập nhật người dùng
    public function rules(): array
    {
        $rules = [
            // Vai trò: bắt buộc và chỉ được là 1 trong các giá trị sau
            'role' => 'required|in:customer,employee,admin',

            // Trạng thái: bắt buộc và chỉ có thể là active hoặc inactive
            'status' => 'required|in:active,inactive',

            // Lý do khóa tài khoản: không bắt buộc, nếu có phải là chuỗi, tối đa 255 ký tự
            'reason_lock' => 'nullable|string|max:255',

            // Cờ kiểm tra có thay đổi mật khẩu không: kiểu boolean, không bắt buộc
            'is_change_password' => 'nullable|boolean',
        ];

        // Nếu có chọn thay đổi mật khẩu thì thêm các rule cho password
        if ($this->filled('is_change_password')) {
            $rules['password'] = ['required', 'string', 'min:6', 'confirmed'];
            // => Trường `password_confirmation` phải được gửi kèm theo và giống với `password`
        }

        return $rules;
    }

    // Tùy chỉnh thông báo lỗi cho từng rule
    public function messages(): array
    {
        return [
            'role.required' => 'Vui lòng chọn vai trò.',
            'role.in' => 'Vai trò không hợp lệ. Chỉ chấp nhận: customer, employee, admin.',

            'status.required' => 'Vui lòng chọn trạng thái.',
            'status.in' => 'Trạng thái không hợp lệ. Chỉ chấp nhận: active hoặc inactive.',

            'reason_lock.string' => 'Lý do khóa tài khoản phải là chuỗi.',
            'reason_lock.max' => 'Lý do khóa tài khoản không được vượt quá 255 ký tự.',

            'is_change_password.boolean' => 'Trường đổi mật khẩu không hợp lệ.',

            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.string' => 'Mật khẩu phải là chuỗi.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ];
    }
}
