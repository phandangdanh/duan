<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('id');
        return [
            'name' => 'sometimes|required|string|max:225',
            'email' => 'sometimes|required|email|unique:users,email,' . $userId,
            'password' => 'sometimes|nullable|string|min:6',
            'phone' => 'sometimes|nullable|string|max:20',
            // Chấp nhận tên file hoặc URL; sẽ chuẩn hóa URL ở Service
            'image' => 'sometimes|nullable|string|max:255',
            'address' => 'sometimes|nullable|string|max:225',
            'province_id' => 'sometimes|nullable|integer|exists:provinces,code',
            'district_id' => 'sometimes|nullable|integer|exists:districts,code',
            'ward_id' => 'sometimes|nullable|integer|exists:wards,code',
            'birthday' => 'sometimes|nullable|date',
            'description' => 'sometimes|nullable|string|max:225',
            'status' => 'sometimes|in:0,1',
            'user_catalogue_id' => 'sometimes|integer|in:1,2',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên là bắt buộc.',
            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => 'Email đã tồn tại.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'user_catalogue_id.in' => 'Vai trò không hợp lệ.',
            'province_id.exists' => 'Tỉnh/Thành phố không hợp lệ.',
            'district_id.exists' => 'Quận/Huyện không hợp lệ.',
            'ward_id.exists' => 'Phường/Xã không hợp lệ.',
            'image.url' => 'Ảnh đại diện phải là một URL hợp lệ.',
            'birthday.date' => 'Ngày sinh phải là một ngày hợp lệ.',
        ];
    }
}


