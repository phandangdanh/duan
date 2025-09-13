<?php

namespace App\Repositories;

use App\Models\Province;
use App\Models\District;
use App\Models\Ward;
use Illuminate\Database\Eloquent\Collection;

class ApiLocationRepository
{
    /**
     * Get all provinces with optional search
     */
    public function getProvinces(string $search = null): Collection
    {
        $query = Province::query();
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%");
            });
        }
        
        return $query->orderBy('name')->get();
    }

    /**
     * Get districts by province code with optional search
     */
    public function getDistricts(int $provinceCode, string $search = null): Collection
    {
        $query = District::where('province_code', $provinceCode);
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%");
            });
        }
        
        return $query->orderBy('name')->get();
    }

    /**
     * Get wards by district code with optional search
     */
    public function getWards(int $districtCode, string $search = null): Collection
    {
        $query = Ward::where('district_code', $districtCode);
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%");
            });
        }
        
        return $query->orderBy('name')->get();
    }

    /**
     * Get full address by ward code
     */
    public function getFullAddress(int $wardCode): ?array
    {
        $ward = Ward::with(['district.province'])
            ->where('code', $wardCode)
            ->first();

        if (!$ward) {
            return null;
        }

        $province = $ward->district->province;
        $district = $ward->district;

        return [
            'province' => [
                'code' => $province->code,
                'name' => $province->name
            ],
            'district' => [
                'code' => $district->code,
                'name' => $district->name
            ],
            'ward' => [
                'code' => $ward->code,
                'name' => $ward->name
            ],
            'full_address' => "{$ward->name}, {$district->name}, {$province->name}"
        ];
    }
}
