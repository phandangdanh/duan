<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('id');
        
        return [
            'maSP' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('sanpham', 'maSP')->ignore($productId)
            ],
            'tenSP' => 'required|string|max:255',
            'id_danhmuc' => 'required|integer|exists:danhmuc,id',
            'moTa' => 'nullable|string|max:1000',
            'trangthai' => 'sometimes|in:0,1',
            'base_price' => 'nullable|numeric|min:0|max:9999999999999.99',
            'base_sale_price' => 'nullable|numeric|min:0|max:9999999999999.99|lte:base_price',
            'variants' => 'sometimes|array',
            'variants.*.ten' => 'required_with:variants|string|max:255',
            'variants.*.mausac' => 'required_with:variants|integer|exists:mausac,id',
            'variants.*.sizes' => 'required_with:variants|array|min:1',
            'variants.*.sizes.*.size' => 'required|integer|exists:size,id',
            'variants.*.sizes.*.so_luong' => 'required|integer|min:0',
            'variants.*.sizes.*.gia' => 'required|numeric|min:0|max:9999999999999.99',
            'variants.*.sizes.*.gia_khuyenmai' => 'nullable|numeric|min:0|max:9999999999999.99|lte:variants.*.sizes.*.gia',
        ];
    }

    public function messages(): array
    {
        return [
            'tenSP.required' => 'Tên sản phẩm là bắt buộc',
            'tenSP.max' => 'Tên sản phẩm không được vượt quá 255 ký tự',
            'id_danhmuc.required' => 'Danh mục sản phẩm là bắt buộc',
            'id_danhmuc.exists' => 'Danh mục sản phẩm không tồn tại',
            'maSP.unique' => 'Mã sản phẩm đã tồn tại',
            'maSP.max' => 'Mã sản phẩm không được vượt quá 50 ký tự',
            'moTa.max' => 'Mô tả không được vượt quá 1000 ký tự',
            'base_price.numeric' => 'Giá gốc phải là số',
            'base_price.min' => 'Giá gốc phải lớn hơn hoặc bằng 0',
            'base_price.max' => 'Giá gốc quá lớn',
            'base_sale_price.numeric' => 'Giá khuyến mãi phải là số',
            'base_sale_price.min' => 'Giá khuyến mãi phải lớn hơn hoặc bằng 0',
            'base_sale_price.max' => 'Giá khuyến mãi quá lớn',
            'base_sale_price.lte' => 'Giá khuyến mãi phải nhỏ hơn hoặc bằng giá gốc',
            'variants.array' => 'Biến thể sản phẩm phải là mảng',
            'variants.*.ten.required_with' => 'Tên biến thể là bắt buộc',
            'variants.*.mausac.required_with' => 'Màu sắc là bắt buộc',
            'variants.*.mausac.exists' => 'Màu sắc không tồn tại',
            'variants.*.sizes.required_with' => 'Kích thước là bắt buộc',
            'variants.*.sizes.min' => 'Phải có ít nhất 1 kích thước',
            'variants.*.sizes.*.size.required' => 'Kích thước là bắt buộc',
            'variants.*.sizes.*.size.exists' => 'Kích thước không tồn tại',
            'variants.*.sizes.*.so_luong.required' => 'Số lượng là bắt buộc',
            'variants.*.sizes.*.so_luong.integer' => 'Số lượng phải là số nguyên',
            'variants.*.sizes.*.so_luong.min' => 'Số lượng phải lớn hơn hoặc bằng 0',
            'variants.*.sizes.*.gia.required' => 'Giá là bắt buộc',
            'variants.*.sizes.*.gia.numeric' => 'Giá phải là số',
            'variants.*.sizes.*.gia.min' => 'Giá phải lớn hơn hoặc bằng 0',
            'variants.*.sizes.*.gia.max' => 'Giá quá lớn',
            'variants.*.sizes.*.gia_khuyenmai.numeric' => 'Giá khuyến mãi phải là số',
            'variants.*.sizes.*.gia_khuyenmai.min' => 'Giá khuyến mãi phải lớn hơn hoặc bằng 0',
            'variants.*.sizes.*.gia_khuyenmai.max' => 'Giá khuyến mãi quá lớn',
            'variants.*.sizes.*.gia_khuyenmai.lte' => 'Giá khuyến mãi phải nhỏ hơn hoặc bằng giá gốc',
        ];
    }
}
