<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ProductIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // English keys (backward compatibility)
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'keyword' => 'sometimes|string|max:255',
            'status' => 'sometimes|in:0,1',
            'category' => 'sometimes|integer|exists:danhmuc,id',
            'min_price' => 'sometimes|numeric|min:0',
            'max_price' => 'sometimes|numeric|min:0|gte:min_price',
            'sort_by' => 'sometimes|string|in:created_at,tenSP,maSP,base_price,trangthai',
            'sort_dir' => 'sometimes|string|in:asc,desc',
            'all' => 'sometimes|boolean',

            // Vietnamese keys
            'trang' => 'sometimes|integer|min:1',
            'so_tren_trang' => 'sometimes|integer|min:1|max:100',
            'tu_khoa' => 'sometimes|string|max:255',
            'trang_thai' => 'sometimes|in:0,1',
            'danh_muc' => 'sometimes|integer|exists:danhmuc,id',
            'gia_toi_thieu' => 'sometimes|numeric|min:0',
            'gia_toi_da' => 'sometimes|numeric|min:0|gte:gia_toi_thieu',
            'sap_xep_theo' => 'sometimes|string|in:created_at,tenSP,maSP,base_price,trangthai',
            'chieu_sap_xep' => 'sometimes|string|in:asc,desc',
            'tat_ca' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'category.exists' => 'Danh mục không tồn tại',
            'danh_muc.exists' => 'Danh mục không tồn tại',
            'min_price.numeric' => 'Giá tối thiểu phải là số',
            'max_price.numeric' => 'Giá tối đa phải là số',
            'max_price.gte' => 'Giá tối đa phải lớn hơn hoặc bằng giá tối thiểu',
            'gia_toi_thieu.numeric' => 'Giá tối thiểu phải là số',
            'gia_toi_da.numeric' => 'Giá tối đa phải là số',
            'gia_toi_da.gte' => 'Giá tối đa phải lớn hơn hoặc bằng giá tối thiểu',
        ];
    }
}
