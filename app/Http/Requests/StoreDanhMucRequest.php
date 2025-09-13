<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDanhMucRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('danhmuc', 'name')->where(function ($query) {
                    return $query->where('deleted_at', null);
                })
            ],
            'description' => 'nullable|string',
            'parent_id' => 'nullable|integer|min:0',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Tên danh mục không được để trống!',
            'name.max' => 'Tên danh mục không được quá 255 ký tự!',
            'name.unique' => 'Tên danh mục  đã tồn tại! Vui lòng chọn tên khác.',
            'parent_id.integer' => 'ID danh mục cha phải là số!',
            'parent_id.min' => 'ID danh mục cha không hợp lệ!',
            'sort_order.integer' => 'Thứ tự phải là số!',
            'sort_order.min' => 'Thứ tự không được âm!',
            'status.required' => 'Trạng thái không được để trống!',
            'status.in' => 'Trạng thái không hợp lệ!',
            'image.image' => 'File phải là hình ảnh!',
            'image.mimes' => 'Chỉ chấp nhận file JPG, PNG, GIF!',
            'image.max' => 'Kích thước file không được quá 2MB!'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'tên danh mục',
            'description' => 'mô tả',
            'parent_id' => 'danh mục cha',
            'sort_order' => 'thứ tự',
            'status' => 'trạng thái',
            'image' => 'ảnh danh mục'
        ];
    }
}
