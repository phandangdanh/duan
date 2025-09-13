<?php

namespace App\Services;

use App\Repositories\ApiLocationRepository;
use Illuminate\Database\Eloquent\Collection;

class ApiLocationService
{
    protected $apiLocationRepository;

    public function __construct(ApiLocationRepository $apiLocationRepository)
    {
        $this->apiLocationRepository = $apiLocationRepository;
    }

    /**
     * Get provinces with search
     */
    public function getProvinces(string $search = null): array
    {
        $provinces = $this->apiLocationRepository->getProvinces($search);
        
        return [
            'data' => $provinces,
            'message' => 'Lấy danh sách tỉnh/thành phố thành công'
        ];
    }

    /**
     * Get districts by province code with search
     */
    public function getDistricts(int $provinceCode, string $search = null): array
    {
        $districts = $this->apiLocationRepository->getDistricts($provinceCode, $search);
        
        return [
            'data' => $districts,
            'message' => 'Lấy danh sách quận/huyện thành công'
        ];
    }

    /**
     * Get wards by district code with search
     */
    public function getWards(int $districtCode, string $search = null): array
    {
        $wards = $this->apiLocationRepository->getWards($districtCode, $search);
        
        return [
            'data' => $wards,
            'message' => 'Lấy danh sách phường/xã thành công'
        ];
    }

    /**
     * Get full address by ward code
     */
    public function getFullAddress(int $wardCode): array
    {
        $address = $this->apiLocationRepository->getFullAddress($wardCode);
        
        if (!$address) {
            return [
                'data' => null,
                'message' => 'Không tìm thấy địa chỉ'
            ];
        }
        
        return [
            'data' => $address,
            'message' => 'Lấy địa chỉ đầy đủ thành công'
        ];
    }
}
