<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VoucherStoreRequest extends FormRequest
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
            'ma_voucher' => [
                'required',
                'string',
                'max:50',
                'unique:voucher,ma_voucher',
                'regex:/^[A-Z0-9_]+$/'
            ],
            'ten_voucher' => 'required|string|max:255',
            'mota' => 'nullable|string',
            'loai_giam_gia' => [
                'required',
                Rule::in(['phan_tram', 'tien_mat'])
            ],
            'gia_tri' => 'required|numeric|min:0|max:999999999999.99',
            'gia_tri_toi_thieu' => 'required|numeric|min:0|max:999999999999.99',
            'gia_tri_toi_da' => 'nullable|numeric|min:0|max:999999999999.99',
            'so_luong' => 'required|integer|min:1',
            'ngay_bat_dau' => 'required|date|after_or_equal:today',
            'ngay_ket_thuc' => 'required|date|after:ngay_bat_dau',
            'trang_thai' => 'boolean'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'ma_voucher.required' => 'Mã voucher không được để trống',
            'ma_voucher.unique' => 'Mã voucher đã tồn tại',
            'ma_voucher.regex' => 'Mã voucher chỉ được chứa chữ hoa, số và dấu gạch dưới',
            'ten_voucher.required' => 'Tên voucher không được để trống',
            'ten_voucher.max' => 'Tên voucher không được quá 255 ký tự',
            'loai_giam_gia.required' => 'Loại giảm giá không được để trống',
            'loai_giam_gia.in' => 'Loại giảm giá không hợp lệ',
            'gia_tri.required' => 'Giá trị giảm giá không được để trống',
            'gia_tri.numeric' => 'Giá trị giảm giá phải là số',
            'gia_tri.min' => 'Giá trị giảm giá phải lớn hơn hoặc bằng 0',
            'gia_tri.max' => 'Giá trị giảm giá không được vượt quá 999,999,999,999.99',
            'gia_tri_toi_thieu.required' => 'Giá trị tối thiểu không được để trống',
            'gia_tri_toi_thieu.numeric' => 'Giá trị tối thiểu phải là số',
            'gia_tri_toi_thieu.min' => 'Giá trị tối thiểu phải lớn hơn hoặc bằng 0',
            'gia_tri_toi_da.numeric' => 'Giá trị tối đa phải là số',
            'gia_tri_toi_da.min' => 'Giá trị tối đa phải lớn hơn hoặc bằng 0',
            'so_luong.required' => 'Số lượng voucher không được để trống',
            'so_luong.integer' => 'Số lượng voucher phải là số nguyên',
            'so_luong.min' => 'Số lượng voucher phải lớn hơn 0',
            'ngay_bat_dau.required' => 'Ngày bắt đầu không được để trống',
            'ngay_bat_dau.date' => 'Ngày bắt đầu không hợp lệ',
            'ngay_bat_dau.after_or_equal' => 'Ngày bắt đầu phải từ hôm nay trở đi',
            'ngay_ket_thuc.required' => 'Ngày kết thúc không được để trống',
            'ngay_ket_thuc.date' => 'Ngày kết thúc không hợp lệ',
            'ngay_ket_thuc.after' => 'Ngày kết thúc phải sau ngày bắt đầu',
            'trang_thai.boolean' => 'Trạng thái phải là true hoặc false'
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Kiểm tra logic giảm giá
            if ($this->loai_giam_gia === 'phan_tram' && $this->gia_tri > 100) {
                $validator->errors()->add('gia_tri', 'Giá trị giảm giá theo phần trăm không được vượt quá 100%');
            }

            if ($this->loai_giam_gia === 'tien_mat' && $this->gia_tri_toi_da && $this->gia_tri > $this->gia_tri_toi_da) {
                $validator->errors()->add('gia_tri', 'Giá trị giảm giá không được lớn hơn giá trị tối đa');
            }

            if ($this->gia_tri_toi_da && $this->gia_tri_toi_thieu && $this->gia_tri_toi_da < $this->gia_tri_toi_thieu) {
                $validator->errors()->add('gia_tri_toi_da', 'Giá trị tối đa phải lớn hơn hoặc bằng giá trị tối thiểu');
            }
        });
    }
}
