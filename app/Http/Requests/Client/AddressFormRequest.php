<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AddressFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Concatenate address if not provided (fallback for disabled JavaScript)
        if (!$this->has('address') || empty($this->input('address'))) {
            $this->merge([
                'address' => implode(', ', array_filter([
                    $this->input('street'),
                    $this->input('ward'),
                    $this->input('district'),
                    $this->input('province'),
                ])),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $user = Auth::user();

        return [
            'fullname' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\sÀ-ỹ]{2,255}$/'],
            'phone_number' => ['required', 'string', 'regex:/^0[35789][0-9]{8}$/'],
            'email' => ['required', 'email', 'max:255'],
            'province' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z\sÀ-ỹ]{2,100}$/'],
            'district' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z\sÀ-ỹ]{2,100}$/'],
            'ward' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z\sÀ-ỹ]{2,100}$/'],
            'street' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z0-9\s,À-ỹ]{5,255}$/'],
            'address' => ['required', 'string', 'max:255'],
            'address_type' => ['required', 'in:home,office'],
            'is_default' => ['nullable', 'boolean'],
            'address_id' => ['nullable', 'exists:user_addresses,id,user_id,' . $user->id],
        ];
    }

    /**
     * Get custom error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'fullname.required' => 'Họ và tên là bắt buộc.',
            'fullname.max' => 'Họ và tên không được vượt quá 255 ký tự.',
            'fullname.regex' => 'Họ và tên chỉ được chứa chữ cái và dấu cách.',
            'phone_number.required' => 'Số điện thoại là bắt buộc.',
            'phone_number.regex' => 'Số điện thoại phải bắt đầu bằng 0, theo sau là 9 chữ số (bắt đầu bằng 3, 5, 7, 8, hoặc 9).',
            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Email không đúng định dạng.',
            'email.max' => 'Email không được vượt quá 255 ký tự.',
            'province.required' => 'Tỉnh/Thành phố là bắt buộc.',
            'province.max' => 'Tỉnh/Thành phố không được vượt quá 100 ký tự.',
            'province.regex' => 'Tỉnh/Thành phố chỉ được chứa chữ cái và dấu cách.',
            'district.required' => 'Quận/Huyện là bắt buộc.',
            'district.max' => 'Quận/Huyện không được vượt quá 100 ký tự.',
            'district.regex' => 'Quận/Huyện chỉ được chứa chữ cái và dấu cách.',
            'ward.required' => 'Phường/Xã là bắt buộc.',
            'ward.max' => 'Phường/Xã không được vượt quá 100 ký tự.',
            'ward.regex' => 'Phường/Xã chỉ được chứa chữ cái và dấu cách.',
            'street.required' => 'Địa chỉ cụ thể là bắt buộc.',
            'street.max' => 'Địa chỉ cụ thể không được vượt quá 255 ký tự.',
            'street.regex' => 'Địa chỉ cụ thể chỉ được chứa chữ cái, số, dấu cách hoặc dấu phẩy.',
            'address.required' => 'Địa chỉ đầy đủ là bắt buộc.',
            'address.max' => 'Địa chỉ đầy đủ không được vượt quá 255 ký tự.',
            'address_type.required' => 'Loại địa chỉ là bắt buộc.',
            'address_type.in' => 'Loại địa chỉ phải là "Nhà riêng" hoặc "Văn phòng".',
            'address_id.exists' => 'Địa chỉ được chọn không hợp lệ hoặc không thuộc về bạn.',
        ];
    }
}