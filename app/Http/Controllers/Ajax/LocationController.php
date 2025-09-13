<?php

namespace App\Http\Controllers\Ajax;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\DistrictRepositoryInterface as DistrictRepository;
use App\Repositories\Interfaces\ProvinceRepositoryInterface as ProvinceRepository;
use App\Repositories\Interfaces\WardRepositoryInterface as WardRepository;

class LocationController extends Controller
{
    protected $districtRepository;
    protected $provinceRepository;
    protected $wardRepository;
    public function __construct(DistrictRepository $districtRepository, ProvinceRepository $provinceRepository, WardRepository $wardRepository)
    {
        $this->districtRepository = $districtRepository;
        $this->provinceRepository = $provinceRepository;
        $this->wardRepository = $wardRepository;
    }

    public function getLocation(Request $request)
    {
        $getData = $request->input();
        $html = '';
        $locationId = $getData['data']['locationId'];

        if (!$locationId) {
            return response()->json(['html' => '<option value="0">Không tìm thấy Quận/Huyện</option>']);
        }

        $selectedId = $getData['data']['selected_id'] ?? 0;
        $mode = $getData['data']['mode'] ?? 'create';

        if ($getData['target'] == 'district') {
            $districts = $this->districtRepository->getByProvinceId($locationId);
            $html = $this->renderHtml($districts, '[Chọn Quận/Huyện]', $selectedId);
        } elseif ($getData['target'] == 'wards') {
            $wards = $this->wardRepository->getByDistrictId($locationId);
            $html = $this->renderHtml($wards, '[Chọn Phường/Xã]', $selectedId);
        }

        // Nếu đang ở mode 'edit', bạn có thể hoãn trả về hoặc xử lý khác
        // Nhưng thường thì vẫn nên return đúng html (có selected rồi)
        return response()->json(['html' => $html]);
    }



    public function renderHtml($districts, $root = '[Chọn Quận/Huyện]', $selectedId = 0){
        $html = '<option value="0">'.$root.'</option>';
        foreach($districts as $district){
            $selected = ($district->code == $selectedId) ? 'selected' : '';
            $html .= '<option value="'.$district->code.'" '.$selected.'>'.$district->name.'</option>';
        }
        return $html;
    }
}
