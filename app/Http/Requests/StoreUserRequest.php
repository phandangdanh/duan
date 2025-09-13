<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
        'email' => 'required|string|email|unique:users,email',
        'name' => 'required|string|max:225',
        'user_catalogue_id' => 'required|integer|gt:0',
        'password' => 'required|string|min:6',
        'rest_password' => 'required|string|same:password',
        'image' => 'nullable|mimes:jpeg,png,jpg,gif,svg',
        'phone' => 'nullable|string|max:20|regex:/^[0-9+ ]*$/',
        'province_id' => 'nullable|string|not_in:0',
        // 'district_id' => 'nullable|string|not_in:0',
        // 'ward_id' => 'nullable|string|not_in:0',
        'address' => 'nullable|string|max:225',
        'description' => 'nullable|string|max:225',
    ];
}

public function messages(): array
{
    return [
        'email.required' => 'Vui lòng nhập địa chỉ email.',
        'email.email' => 'Địa chỉ email không đúng định dạng.',
        'email.unique' => 'Email đã được sử dụng.',
        'name.required' => 'Vui lòng nhập họ và tên.',
        'name.max' => 'Họ và tên không được vượt quá 225 ký tự.',
        'user_catalogue_id.required' => 'Vui lòng chọn nhóm thành viên.',
        'user_catalogue_id.gt' => 'Vui lòng chọn một nhóm thành viên hợp lệ.',
        'password.required' => 'Vui lòng nhập mật khẩu.',
        'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
        'rest_password.required' => 'Vui lòng nhập lại mật khẩu.',
        'rest_password.same' => 'Mật khẩu nhập lại không khớp.',
        'image.mimes' => 'Ảnh chỉ chấp nhận các định dạng: jpeg, png, jpg, gif, svg.',
        'phone.max' => 'Số điện thoại không được vượt quá 20 ký tự.',
        'phone.regex' => 'Số điện thoại chỉ được chứa số, dấu cộng và khoảng trắng.',
        'province_id.not_in' => 'Vui lòng chọn Tỉnh/Thành hợp lệ.',
        // 'district_id.not_in' => 'Vui lòng chọn Quận/Huyện hợp lệ.',
        // 'ward_id.not_in' => 'Vui lòng chọn Phường/Xã hợp lệ.',
        'address.max' => 'Địa chỉ không được vượt quá 225 ký tự.',
        'description.max' => 'Ghi chú không được vượt quá 225 ký tự.',
    ];
}
}
