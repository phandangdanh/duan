<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true; 
    }

    public function rules()
    {
        $userId = $this->route('id'); 
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $userId,
            'phone' => 'nullable|string|max:20',
            // 'province_id' => 'nullable|numeric|exists:provinces,code',
            // 'district_id' => 'nullable|numeric|exists:districts,code',
            // 'ward_id' => 'nullable|numeric|exists:wards,code',
            'address' => 'nullable|string|max:255',
            'birthday' => 'nullable|date',
            'description' => 'nullable|string|max:255',
            'status' => 'required|in:0,1',
            'role' => 'required|in:user,admin',
            'user_catalogue_id' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            
        ];
    }
}