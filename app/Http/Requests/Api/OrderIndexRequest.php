<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class OrderIndexRequest extends FormRequest
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
            'search' => 'nullable|string|max:255',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
            'status' => 'nullable|string|in:cho_xac_nhan,da_xac_nhan,dang_giao,da_giao,da_huy,hoan_tra',
            'user_id' => 'nullable|integer|exists:users,id',
            'date_from' => 'nullable|date|before_or_equal:date_to',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'min_amount' => 'nullable|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0|gte:min_amount',
            'payment_method' => 'nullable|string|in:cod,banking,momo,zalopay',
            'payment_status' => 'nullable|string|in:chua_thanh_toan,da_thanh_toan,hoan_tien',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'search.max' => 'Từ khóa tìm kiếm không được vượt quá 255 ký tự',
            'page.integer' => 'Số trang phải là số nguyên',
            'page.min' => 'Số trang phải lớn hơn 0',
            'per_page.integer' => 'Số bản ghi mỗi trang phải là số nguyên',
            'per_page.min' => 'Số bản ghi mỗi trang phải lớn hơn 0',
            'per_page.max' => 'Số bản ghi mỗi trang không được vượt quá 100',
            'status.in' => 'Trạng thái đơn hàng không hợp lệ',
            'user_id.integer' => 'ID khách hàng phải là số nguyên',
            'user_id.exists' => 'Khách hàng không tồn tại',
            'date_from.date' => 'Ngày bắt đầu không hợp lệ',
            'date_from.before_or_equal' => 'Ngày bắt đầu phải nhỏ hơn hoặc bằng ngày kết thúc',
            'date_to.date' => 'Ngày kết thúc không hợp lệ',
            'date_to.after_or_equal' => 'Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu',
            'min_amount.numeric' => 'Số tiền tối thiểu phải là số',
            'min_amount.min' => 'Số tiền tối thiểu phải lớn hơn hoặc bằng 0',
            'max_amount.numeric' => 'Số tiền tối đa phải là số',
            'max_amount.min' => 'Số tiền tối đa phải lớn hơn hoặc bằng 0',
            'max_amount.gte' => 'Số tiền tối đa phải lớn hơn hoặc bằng số tiền tối thiểu',
            'payment_method.in' => 'Phương thức thanh toán không hợp lệ',
            'payment_status.in' => 'Trạng thái thanh toán không hợp lệ',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'search' => 'từ khóa tìm kiếm',
            'page' => 'số trang',
            'per_page' => 'số bản ghi mỗi trang',
            'status' => 'trạng thái đơn hàng',
            'user_id' => 'ID khách hàng',
            'date_from' => 'ngày bắt đầu',
            'date_to' => 'ngày kết thúc',
            'min_amount' => 'số tiền tối thiểu',
            'max_amount' => 'số tiền tối đa',
            'payment_method' => 'phương thức thanh toán',
            'payment_status' => 'trạng thái thanh toán',
        ];
    }
}
