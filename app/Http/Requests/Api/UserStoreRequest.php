<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:225',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:20',
            'image' => 'nullable|url',
            'address' => 'nullable|string|max:225',
            'province_id' => 'nullable|integer|exists:provinces,code',
            'district_id' => 'nullable|integer|exists:districts,code',
            'ward_id' => 'nullable|integer|exists:wards,code',
            'birthday' => 'nullable|date',
            'description' => 'nullable|string|max:225',
            'status' => 'nullable|in:0,1',
            'user_catalogue_id' => 'required|integer|in:1,2',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên là bắt buộc.',
            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => 'Email đã tồn tại.',
            'password.required' => 'Mật khẩu là bắt buộc.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'user_catalogue_id.required' => 'Vai trò là bắt buộc.',
            'user_catalogue_id.in' => 'Vai trò không hợp lệ.',
            'province_id.exists' => 'Tỉnh/Thành phố không hợp lệ.',
            'district_id.exists' => 'Quận/Huyện không hợp lệ.',
            'ward_id.exists' => 'Phường/Xã không hợp lệ.',
        ];
    }
}


