<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Địa chỉ email là bắt buộc',
            'email.email' => 'Địa chỉ email không hợp lệ',
            'password.required' => 'Mật khẩu là bắt buộc',
            'remember.boolean' => 'Trường ghi nhớ đăng nhập phải là true hoặc false',
        ];
    }
}
