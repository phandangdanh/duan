<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexSanPhamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search'    => 'nullable|string|max:255',
            'category'  => 'nullable|integer|exists:danhmuc,id',
            'status'    => 'nullable|in:0,1',
            'stock'     => 'nullable|in:in_stock,out_of_stock',
            'sort'      => 'nullable|string|in:id,tenSP,tenSP_desc,maSP,gia_asc,gia_desc',
            'perpage'   => 'nullable|string|in:5,10,25,50,100,all',
        ];
    }

    public function messages(): array
    {
        return [
            'integer' => ':attribute phải là số nguyên.',
            'exists'  => ':attribute không hợp lệ.',
            'in'      => ':attribute không hợp lệ.',
            'numeric' => ':attribute phải là số.',
            'min'     => ':attribute phải lớn hơn hoặc bằng :min.',
        ];
    }

    public function attributes(): array
    {
        return [
            'search'     => 'Từ khóa',
            'category'   => 'Danh mục',
            'status'     => 'Trạng thái',
            'stock'      => 'Tồn kho',
            'sort'       => 'Sắp xếp',
            'perpage'    => 'Số bản ghi/trang',
        ];
    }
}


