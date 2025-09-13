<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UserIndexRequest extends FormRequest
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
            'role' => 'sometimes|integer|in:1,2',
            'sort_by' => 'sometimes|string|in:created_at,name,email,status',
            'sort_dir' => 'sometimes|string|in:asc,desc',
            'all' => 'sometimes|boolean',

            // Vietnamese keys
            'trang' => 'sometimes|integer|min:1',
            'so_tren_trang' => 'sometimes|integer|min:1|max:100',
            'tu_khoa' => 'sometimes|string|max:255',
            'trang_thai' => 'sometimes|in:0,1',
            'vai_tro' => 'sometimes|integer|in:1,2',
            'sap_xep_theo' => 'sometimes|string|in:created_at,name,email,status',
            'chieu_sap_xep' => 'sometimes|string|in:asc,desc',
            'tat_ca' => 'sometimes|boolean',
        ];
    }
}


