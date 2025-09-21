<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSanPhamRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $input = $this->all();

        // Trim tên sản phẩm để tránh trùng do khoảng trắng
        if (isset($input['tenSP']) && is_string($input['tenSP'])) {
            $input['tenSP'] = trim($input['tenSP']);
        }

        // Đặt mặc định số lượng = 1 nếu không nhập hoặc nhập rỗng
        if (isset($input['variants']) && is_array($input['variants'])) {
            foreach ($input['variants'] as $vIndex => $variant) {
                if (!isset($variant['sizes']) || !is_array($variant['sizes'])) {
                    continue;
                }
                foreach ($variant['sizes'] as $sIndex => $sizeRow) {
                    if (!isset($sizeRow['so_luong']) || $sizeRow['so_luong'] === '' || $sizeRow['so_luong'] === null) {
                        $input['variants'][$vIndex]['sizes'][$sIndex]['so_luong'] = 1;
                    }
                }
            }
        }

        $this->replace($input);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'maSP'              => 'nullable|string|max:100|unique:sanpham,maSP',
            'tenSP'             => 'required|string|max:255|unique:sanpham,tenSP',
            'id_danhmuc'        => 'required|integer|exists:danhmuc,id',
            'moTa'              => 'nullable|string',
            'status'            => 'nullable|in:0,1',
            'soLuong'           => 'nullable|integer|min:0',
            // Giá mặc định cấp sản phẩm (áp cho biến thể nếu không nhập)
            'base_price'        => 'nullable|numeric|min:0',
            'base_sale_price'   => 'nullable|numeric|min:0',
            // Biến thể với cấu trúc mới
            'variants.*.ten'                    => 'nullable|string|max:255',
            'variants.*.mausac'                 => 'nullable|integer|exists:mausac,id',
            'variants.*.sizes.*.size'           => 'nullable|integer|exists:size,id',
            'variants.*.sizes.*.so_luong'       => 'nullable|integer|min:0',
            'variants.*.sizes.*.gia'            => 'nullable|numeric|min:0',
            'variants.*.sizes.*.gia_khuyenmai'  => 'nullable|numeric|min:0',
            // Hình ảnh
            // Cho phép bỏ trống khi người dùng submit lại sau khi lỗi validate các trường khác
            // Nếu muốn bắt buộc ở lần đầu, hãy kiểm tra ở Controller khi không có session lỗi trước đó
            'image_main'        => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:6144',
            'image_extra.*'     => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:6144',
            // Tên file ảnh để giữ lại khi validation fail
            'image_main_name'   => 'nullable|string',
            'image_extra_names' => 'nullable|array',
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
            'variants.*.ten' => 'Tên biến thể',
            'variants.*.mausac' => 'Màu sắc',
            'variants.*.sizes.*.size' => 'Size',
            'variants.*.sizes.*.so_luong' => 'Số lượng',
            'variants.*.sizes.*.gia' => 'Giá',
            'variants.*.sizes.*.gia_khuyenmai' => 'Giá khuyến mãi',
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


