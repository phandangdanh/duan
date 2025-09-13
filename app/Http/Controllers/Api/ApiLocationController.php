<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use OpenApi\Annotations as OA;
use App\Services\ApiLocationService;
use Illuminate\Http\Request;

/**
 */
class ApiLocationController extends Controller
{
    protected $apiLocationService;

    public function __construct(ApiLocationService $apiLocationService)
    {
        $this->apiLocationService = $apiLocationService;
    }
    /**
     * @OA\Get(
     *   path="/api/locations/provinces",
     *   summary="Lấy danh sách tỉnh/thành phố",
     *   description="API này trả về danh sách tất cả tỉnh/thành phố trong hệ thống. Có thể tìm kiếm theo tên tỉnh/thành phố.",
     *   operationId="getProvinces",
     *   tags={"1. Location"},
     *   @OA\Parameter(name="search", in="query", @OA\Schema(type="string"), description="Tìm kiếm theo tên tỉnh/thành phố", example="Hồ Chí Minh"),
     *   @OA\Response(
     *     response=200,
     *     description="Thành công",
     *     @OA\JsonContent(
     *       example={
     *         "data": {
     *           {
     *             "code": 79,
     *             "name": "Hồ Chí Minh",
     *             "name_en": "Ho Chi Minh",
     *             "full_name": "Thành phố Hồ Chí Minh",
     *             "full_name_en": "Ho Chi Minh City",
     *             "code_name": "ho_chi_minh",
     *             "administrative_unit_id": 1,
     *             "administrative_region_id": 7
     *           }
     *         },
     *         "message": "Lấy danh sách tỉnh/thành phố thành công"
     *       }
     *     )
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Lỗi",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status_code", type="integer", example=400),
     *       @OA\Property(property="message", type="string", example="Có lỗi xảy ra"),
     *       @OA\Property(property="errors", type="object")
     *     )
     *   )
     * )
     */
    public function getProvinces(Request $request)
    {
        try {
        $search = $request->input('search');
            
            // Validate search parameter if provided
            if ($search && strlen($search) < 2) {
                return response()->json([
                    'status_code' => 400,
                    'message' => 'Từ khóa tìm kiếm phải có ít nhất 2 ký tự',
                    'errors' => ['search' => ['Từ khóa tìm kiếm không hợp lệ']]
                ], 400);
            }
            
            $result = $this->apiLocationService->getProvinces($search);
            
            // Check if no results found when searching
            if ($search && $result['data']->count() === 0) {
                return response()->json([
                    'status_code' => 400,
                    'message' => 'Không tìm thấy tỉnh/thành phố nào',
                    'errors' => ['search' => ['Từ khóa "' . $search . '" không tìm thấy kết quả nào']]
                ], 400);
            }
            
            // Check if redirect is needed (when search returns exactly one result)
            if ($search && $result['data']->count() === 1) {
                return response()->json([
                    'status_code' => 300,
                    'message' => 'Yêu cầu chuyển hướng',
                    'redirect_url' => '/api/locations/provinces/' . $result['data']->first()->code,
                    'suggestion' => 'Tìm thấy đúng 1 kết quả, có thể chuyển đến trang chi tiết',
                    'data' => $result['data']
                ], 300);
            }
            
            return response()->json([
                'status_code' => 200,
                'data' => $result['data'],
                'message' => $result['message']
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Lỗi hệ thống',
                'error' => 'Lỗi máy chủ nội bộ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *   path="/api/locations/districts",
     *   summary="Lấy danh sách quận/huyện theo tỉnh/thành phố",
     *   description="API này trả về danh sách quận/huyện thuộc một tỉnh/thành phố cụ thể. Cần truyền mã tỉnh/thành phố.",
     *   operationId="getDistricts",
     *   tags={"1. Location"},
     *   @OA\Parameter(name="province_code", in="query", required=true, @OA\Schema(type="integer"), description="Mã tỉnh/thành phố", example=79),
     *   @OA\Parameter(name="search", in="query", @OA\Schema(type="string"), description="Tìm kiếm theo tên quận/huyện", example="12"),
     *   @OA\Response(
     *     response=200,
     *     description="Thành công",
     *     @OA\JsonContent(
     *       example={
     *         "data": {
     *           {
     *             "code": 761,
     *             "name": "12",
     *             "name_en": "12",
     *             "full_name": "Quận 12",
     *             "full_name_en": "District 12",
     *             "code_name": "12",
     *             "province_code": 79,
     *             "administrative_unit_id": 5
     *           }
     *         },
     *         "message": "Lấy danh sách quận/huyện thành công"
     *       }
     *     )
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Lỗi",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status_code", type="integer", example=400),
     *       @OA\Property(property="message", type="string", example="Có lỗi xảy ra"),
     *       @OA\Property(property="errors", type="object")
     *     )
     *   )
     * )
     */
    public function getDistricts(Request $request)
    {
        try {
            // Validate required parameter
        if (!$request->has('province_code') || empty($request->province_code)) {
            return response()->json([
                    'status_code' => 400,
                'message' => 'Tham số không hợp lệ',
                    'errors' => ['province_code' => ['Mã tỉnh/thành phố là bắt buộc']]
            ], 400);
        }

        $provinceCode = $request->input('province_code');
        $search = $request->input('search');
            
            // Validate province_code
            if (!is_numeric($provinceCode) || $provinceCode <= 0) {
                return response()->json([
                    'status_code' => 400,
                'message' => 'Tham số không hợp lệ',
                    'errors' => ['province_code' => ['Mã tỉnh/thành phố phải là số dương']]
                ], 400);
            }
            
            // Validate search parameter if provided
            if ($search && strlen($search) < 2) {
                return response()->json([
                    'status_code' => 400,
                'message' => 'Tham số không hợp lệ',
                    'errors' => ['search' => ['Từ khóa tìm kiếm phải có ít nhất 2 ký tự']]
                ], 400);
            }
            
            $result = $this->apiLocationService->getDistricts($provinceCode, $search);
            
            // Check if no results found when searching
            if ($search && $result['data']->count() === 0) {
                return response()->json([
                    'status_code' => 400,
                    'message' => 'Không tìm thấy quận/huyện nào',
                    'errors' => ['search' => ['Từ khóa "' . $search . '" không tìm thấy kết quả nào']]
                ], 400);
            }
            
            // Check if redirect is needed (when search returns exactly one result)
            if ($search && $result['data']->count() === 1) {
                return response()->json([
                    'status_code' => 300,
                    'message' => 'Yêu cầu chuyển hướng',
                    'redirect_url' => '/api/locations/districts/' . $result['data']->first()->code,
                    'suggestion' => 'Tìm thấy đúng 1 kết quả, có thể chuyển đến trang chi tiết',
                    'data' => $result['data']
                ], 300);
            }
            
            return response()->json([
                'status_code' => 200,
                'data' => $result['data'],
                'message' => $result['message']
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Lỗi hệ thống',
                'error' => 'Lỗi máy chủ nội bộ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *   path="/api/locations/wards",
     *   summary="Lấy danh sách phường/xã theo quận/huyện",
     *   description="API này trả về danh sách phường/xã thuộc một quận/huyện cụ thể. Cần truyền mã quận/huyện.",
     *   operationId="getWards",
     *   tags={"1. Location"},
     *   @OA\Parameter(name="district_code", in="query", required=true, @OA\Schema(type="integer"), description="Mã quận/huyện", example=761),
     *   @OA\Parameter(name="search", in="query", @OA\Schema(type="string"), description="Tìm kiếm theo tên phường/xã", example="Tân Thới Hiệp"),
     *   @OA\Response(
     *     response=200,
     *     description="Thành công",
     *     @OA\JsonContent(
     *       example={
     *         "data": {
     *           {
     *             "code": 26782,
     *             "name": "Tân Thới Hiệp",
     *             "name_en": "Tan Thoi Hiep",
     *             "full_name": "Phường Tân Thới Hiệp",
     *             "full_name_en": "Tan Thoi Hiep Ward",
     *             "code_name": "tan_thoi_hiep",
     *             "district_code": 761,
     *             "administrative_unit_id": 8
     *           }
     *         },
     *         "message": "Lấy danh sách phường/xã thành công"
     *       }
     *     )
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Lỗi",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status_code", type="integer", example=400),
     *       @OA\Property(property="message", type="string", example="Có lỗi xảy ra"),
     *       @OA\Property(property="errors", type="object")
     *     )
     *   )
     * )
     */
    public function getWards(Request $request)
    {
        try {
            // Validate required parameter
        if (!$request->has('district_code') || empty($request->district_code)) {
            return response()->json([
                    'status_code' => 400,
                'message' => 'Tham số không hợp lệ',
                    'errors' => ['district_code' => ['Mã quận/huyện là bắt buộc']]
            ], 400);
        }

        $districtCode = $request->input('district_code');
        $search = $request->input('search');
            
            // Validate district_code
            if (!is_numeric($districtCode) || $districtCode <= 0) {
                return response()->json([
                    'status_code' => 400,
                'message' => 'Tham số không hợp lệ',
                    'errors' => ['district_code' => ['Mã quận/huyện phải là số dương']]
                ], 400);
            }
            
            // Validate search parameter if provided
            if ($search && strlen($search) < 2) {
                return response()->json([
                    'status_code' => 400,
                'message' => 'Tham số không hợp lệ',
                    'errors' => ['search' => ['Từ khóa tìm kiếm phải có ít nhất 2 ký tự']]
                ], 400);
            }
            
            $result = $this->apiLocationService->getWards($districtCode, $search);
            
            // Check if no results found when searching
            if ($search && $result['data']->count() === 0) {
                return response()->json([
                    'status_code' => 400,
                    'message' => 'Không tìm thấy phường/xã nào',
                    'errors' => ['search' => ['Từ khóa "' . $search . '" không tìm thấy kết quả nào']]
                ], 400);
            }
            
            // Check if redirect is needed (when search returns exactly one result)
            if ($search && $result['data']->count() === 1) {
                return response()->json([
                    'status_code' => 300,
                    'message' => 'Yêu cầu chuyển hướng',
                    'redirect_url' => '/api/locations/wards/' . $result['data']->first()->code,
                    'suggestion' => 'Tìm thấy đúng 1 kết quả, có thể chuyển đến trang chi tiết',
                    'data' => $result['data']
                ], 300);
            }
            
            return response()->json([
                'status_code' => 200,
                'data' => $result['data'],
                'message' => $result['message']
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Lỗi hệ thống',
                'error' => 'Lỗi máy chủ nội bộ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *   path="/api/locations/full-address",
     *   summary="Lấy địa chỉ đầy đủ theo mã phường/xã",
     *   description="API này trả về thông tin địa chỉ đầy đủ (tỉnh/thành phố, quận/huyện, phường/xã) khi truyền mã phường/xã.",
     *   operationId="getFullAddress",
     *   tags={"1. Location"},
     *   @OA\Parameter(name="ward_code", in="query", required=true, @OA\Schema(type="integer"), description="Mã phường/xã", example=26782),
     *   @OA\Response(
     *     response=200,
     *     description="Thành công",
     *     @OA\JsonContent(
     *       example={
     *         "data": {
     *           "province": {
     *             "code": 79,
     *             "name": "Hồ Chí Minh"
     *           },
     *           "district": {
     *             "code": 761,
     *             "name": "12"
     *           },
     *           "ward": {
     *             "code": 26782,
     *             "name": "Tân Thới Hiệp"
     *           },
     *           "full_address": "Tân Thới Hiệp, 12, Hồ Chí Minh"
     *         },
     *         "message": "Lấy địa chỉ đầy đủ thành công"
     *       }
     *     )
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Lỗi",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status_code", type="integer", example=400),
     *       @OA\Property(property="message", type="string", example="Có lỗi xảy ra"),
     *       @OA\Property(property="errors", type="object")
     *     )
     *   )
     * )
     */
    public function getFullAddress(Request $request)
    {
        try {
            // Validate required parameter
        if (!$request->has('ward_code') || empty($request->ward_code)) {
            return response()->json([
                    'status_code' => 400,
                'message' => 'Tham số không hợp lệ',
                    'errors' => ['ward_code' => ['Mã phường/xã là bắt buộc']]
            ], 400);
        }

        $wardCode = $request->input('ward_code');
            
            // Validate ward_code
            if (!is_numeric($wardCode) || $wardCode <= 0) {
                return response()->json([
                    'status_code' => 400,
                'message' => 'Tham số không hợp lệ',
                    'errors' => ['ward_code' => ['Mã phường/xã phải là số dương']]
                ], 400);
            }
            
            $result = $this->apiLocationService->getFullAddress($wardCode);
            
            // Check if address not found
        if (!$result['data']) {
                return response()->json([
                    'status_code' => 404,
                'message' => 'Không tìm thấy địa chỉ',
                    'errors' => ['ward_code' => ['Mã phường/xã không tồn tại']]
                ], 404);
            }
            
            return response()->json([
                'status_code' => 200,
                'data' => $result['data'],
                'message' => $result['message']
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Lỗi hệ thống',
                'error' => 'Lỗi máy chủ nội bộ: ' . $e->getMessage()
            ], 500);
        }
    }
}
