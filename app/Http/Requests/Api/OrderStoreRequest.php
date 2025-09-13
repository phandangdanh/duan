<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderStoreRequest extends FormRequest
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
            'id_user' => 'required|integer|exists:users,id',
            'hoten' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'sodienthoai' => 'required|string|max:20',
            'diachigiaohang' => 'required|string|max:500',
            'phuongthucthanhtoan' => 'nullable|string|in:cod,banking,momo,zalopay',
            'ghichu' => 'nullable|string|max:1000',
            
            'chi_tiet_don_hang' => 'required|array|min:1',
            'chi_tiet_don_hang.*.id_chitietsanpham' => 'required|integer|exists:chitietsanpham,id',
            'chi_tiet_don_hang.*.soluong' => 'required|integer|min:1|max:999',
            'chi_tiet_don_hang.*.dongia' => 'nullable|numeric|min:0',
            'chi_tiet_don_hang.*.ghichu' => 'nullable|string|max:500',
            
            'vouchers' => 'nullable|array',
            'vouchers.*.id_voucher' => 'nullable|integer|exists:voucher,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'id_user.required' => 'ID khách hàng là bắt buộc',
            'id_user.integer' => 'ID khách hàng phải là số nguyên',
            'id_user.exists' => 'Khách hàng không tồn tại',
            
            'hoten.required' => 'Họ tên người nhận là bắt buộc',
            'hoten.string' => 'Họ tên người nhận phải là chuỗi ký tự',
            'hoten.max' => 'Họ tên người nhận không được vượt quá 255 ký tự',
            
            'email.required' => 'Email người nhận là bắt buộc',
            'email.email' => 'Email người nhận không hợp lệ',
            'email.max' => 'Email người nhận không được vượt quá 255 ký tự',
            
            'sodienthoai.required' => 'Số điện thoại người nhận là bắt buộc',
            'sodienthoai.string' => 'Số điện thoại người nhận phải là chuỗi ký tự',
            'sodienthoai.max' => 'Số điện thoại người nhận không được vượt quá 20 ký tự',
            
            'diachigiaohang.required' => 'Địa chỉ giao hàng là bắt buộc',
            'diachigiaohang.string' => 'Địa chỉ giao hàng phải là chuỗi ký tự',
            'diachigiaohang.max' => 'Địa chỉ giao hàng không được vượt quá 500 ký tự',
            
            'phuongthucthanhtoan.in' => 'Phương thức thanh toán không hợp lệ',
            
            'ghichu.string' => 'Ghi chú phải là chuỗi ký tự',
            'ghichu.max' => 'Ghi chú không được vượt quá 1000 ký tự',
            
            'chi_tiet_don_hang.required' => 'Chi tiết đơn hàng là bắt buộc',
            'chi_tiet_don_hang.array' => 'Chi tiết đơn hàng phải là mảng',
            'chi_tiet_don_hang.min' => 'Đơn hàng phải có ít nhất 1 sản phẩm',
            
            'chi_tiet_don_hang.*.id_chitietsanpham.required' => 'ID chi tiết sản phẩm là bắt buộc',
            'chi_tiet_don_hang.*.id_chitietsanpham.integer' => 'ID chi tiết sản phẩm phải là số nguyên',
            'chi_tiet_don_hang.*.id_chitietsanpham.exists' => 'Chi tiết sản phẩm không tồn tại',
            
            'chi_tiet_don_hang.*.soluong.required' => 'Số lượng là bắt buộc',
            'chi_tiet_don_hang.*.soluong.integer' => 'Số lượng phải là số nguyên',
            'chi_tiet_don_hang.*.soluong.min' => 'Số lượng phải lớn hơn 0',
            'chi_tiet_don_hang.*.soluong.max' => 'Số lượng không được vượt quá 999',
            
            'chi_tiet_don_hang.*.dongia.numeric' => 'Đơn giá phải là số',
            'chi_tiet_don_hang.*.dongia.min' => 'Đơn giá phải lớn hơn hoặc bằng 0',
            
            'chi_tiet_don_hang.*.ghichu.string' => 'Ghi chú sản phẩm phải là chuỗi ký tự',
            'chi_tiet_don_hang.*.ghichu.max' => 'Ghi chú sản phẩm không được vượt quá 500 ký tự',
            
            'vouchers.array' => 'Danh sách voucher phải là mảng',
            'vouchers.*.id_voucher.integer' => 'ID voucher phải là số nguyên',
            'vouchers.*.id_voucher.exists' => 'Voucher không tồn tại',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'id_user' => 'ID khách hàng',
            'hoten' => 'họ tên người nhận',
            'email' => 'email người nhận',
            'sodienthoai' => 'số điện thoại người nhận',
            'diachigiaohang' => 'địa chỉ giao hàng',
            'phuongthucthanhtoan' => 'phương thức thanh toán',
            'ghichu' => 'ghi chú',
            'chi_tiet_don_hang' => 'chi tiết đơn hàng',
            'vouchers' => 'danh sách voucher',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validate product stock
            if ($this->has('chi_tiet_don_hang')) {
                foreach ($this->input('chi_tiet_don_hang', []) as $index => $detail) {
                    if (isset($detail['id_chitietsanpham']) && isset($detail['soluong'])) {
                        $chiTietSanPham = \App\Models\ChiTietSanPham::find($detail['id_chitietsanpham']);
                        
                        if ($chiTietSanPham && $chiTietSanPham->soLuong < $detail['soluong']) {
                            $validator->errors()->add(
                                "chi_tiet_don_hang.{$index}.soluong",
                                "Sản phẩm '{$chiTietSanPham->tenSp}' không đủ số lượng. Còn lại: {$chiTietSanPham->soLuong}"
                            );
                        }
                    }
                }
            }
            
            // Validate vouchers
            if ($this->has('vouchers')) {
                foreach ($this->input('vouchers', []) as $index => $voucher) {
                    if (isset($voucher['id_voucher'])) {
                        $voucherModel = \App\Models\Voucher::active()->find($voucher['id_voucher']);
                        
                        if (!$voucherModel) {
                            $validator->errors()->add(
                                "vouchers.{$index}.id_voucher",
                                "Voucher không hợp lệ hoặc đã hết hạn"
                            );
                        }
                    }
                }
            }
        });
    }
}
