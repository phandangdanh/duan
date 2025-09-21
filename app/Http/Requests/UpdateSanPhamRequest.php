<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSanPhamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id') ?? $this->route('sanpham');
        return [
            'maSP'              => 'nullable|string|max:100|unique:sanpham,maSP,' . $id,
            'tenSP'             => 'required|string|max:255',
            'id_danhmuc'        => 'required|integer|exists:danhmuc,id',
            'moTa'              => 'nullable|string',
            'status'            => 'nullable|in:0,1',
            'soLuong'           => 'nullable|integer|min:0',
            'base_price'        => 'nullable|numeric|min:0',
            'base_sale_price'   => 'nullable|numeric|min:0',
            'variants.*.ten'                    => 'nullable|string|max:255',
            'variants.*.mausac'                 => 'nullable|integer|exists:mausac,id',
            'variants.*.sizes.*.size'           => 'nullable|integer|exists:size,id',
            'variants.*.sizes.*.so_luong'       => 'nullable|integer|min:0',
            'variants.*.sizes.*.gia'            => 'nullable|numeric|min:0',
            'variants.*.sizes.*.gia_khuyenmai'  => 'nullable|numeric|min:0',
            'image_main'        => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:6144',
            'image_extra.*'     => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:6144',
        ];
    }

    public function attributes(): array
    {
        return [
            'maSP' => 'Mã sản phẩm',
            'tenSP' => 'Tên sản phẩm',
            'id_danhmuc' => 'Danh mục',
            'moTa' => 'Mô tả',
            'status' => 'Trạng thái',
            'soLuong' => 'Số lượng sản phẩm chính',
            'image_main' => 'Hình ảnh chính',
            'image_extra.*' => 'Hình ảnh phụ',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute là bắt buộc.',
            'unique' => ':attribute đã tồn tại.',
            'string' => ':attribute phải là chuỗi ký tự.',
            'max' => ':attribute không được vượt quá :max ký tự.',
            'integer' => ':attribute phải là số nguyên.',
            'exists' => ':attribute không hợp lệ.',
            'numeric' => ':attribute phải là số.',
            'min' => ':attribute phải lớn hơn hoặc bằng :min.',
            'image' => ':attribute phải là hình ảnh hợp lệ.',
            'mimes' => ':attribute chỉ cho phép các định dạng: :values.',
        ];
    }
}


