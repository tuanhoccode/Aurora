<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileRequest extends FormRequest
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
        return [
            'fullname' => 'required|string|max:100',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->user()->id)],
            'phone_number' => ['required', 'string', 'max:20', Rule::unique('users', 'phone_number')->ignore($this->user()->id),],
            'birthday' => 'required|date',
            'gender' => ['required', Rule::in(['male', 'female', 'other'])],
            //User_address
            'address' => 'required|string|max:500',
            'address_phone_number' => 'required|string|max:20',
            'receiver_fullname' => 'nullable|string|max:100',

        ];
    }
    public function messages(): array
    {
        return [
            // users
            'fullname.required'         => 'Họ tên không được để trống.',
            'fullname.string'           => 'Họ tên phải là một chuỗi ký tự.',
            'fullname.max'              => 'Họ tên không được vượt quá 255 ký tự.',

            'email.required'            => 'Email không được để trống.',
            'email.email'               => 'Email không đúng định dạng.',
            'email.unique'              => 'Email đã được sử dụng.',

            'phone_number.string'              => 'Số điện thoại phải là chuỗi ký tự.',
            'phone_number.required'              => 'Vui lòng nhập số điện thoại.',
            'phone_number.max'                 => 'Số điện thoại không được vượt quá 20 ký tự.',
            'phone_number.unique'              => 'Số điện thoại đã được sử dụng.',

            'birthday.date'             => 'Ngày sinh không hợp lệ.',
            'gender.in'                 => 'Giới tính phải là Nam, Nữ hoặc Khác.',
            'gender.required'            => 'Giới tính không được để trống.',
            'birthday.required'            => 'Ngày sinh không được để trống.',

            // user_addresses
            'address.string'            => 'Địa chỉ phải là chuỗi ký tự.',
            'address.required'            => 'Vui lòng nhập địa chỉ.',
            'address.max'               => 'Địa chỉ không được vượt quá 500 ký tự.',

            'address_phone_number.string'       => 'Số điện thoại nhận hàng phải là chuỗi ký tự.',
            'address_phone_number.required'       => 'Vui lòng nhập số điện thoại cá nhân 2.',
            'address_phone_number.max'          => 'Số điện thoại nhận hàng không được vượt quá 20 ký tự.',

            'receiver_fullname.string'  => 'Tên người nhận phải là chuỗi ký tự.',
            'receiver_fullname.max'     => 'Tên người nhận không được vượt quá 255 ký tự.',
        ];
    }
}
