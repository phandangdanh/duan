<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ApiVoucherService;
use App\Http\Resources\VoucherResource;
use OpenApi\Annotations as OA;
use Illuminate\Http\Request;
use App\Models\Voucher;

/**
 * @OA\Tag(
 *     name="6. Vouchers",
 *     description="Voucher management endpoints"
 * )
 */
class ApiVoucherController extends Controller
{
    public function __construct(private ApiVoucherService $service)
    {
    }

    /**
     * @OA\Get(
     *   path="/api/vouchers",
     *   summary="Danh sách voucher",
     *   tags={"6. Vouchers"},
     *   @OA\Parameter(name="search", in="query", @OA\Schema(type="string"), description="Tìm kiếm theo mã/tên voucher"),
     *   @OA\Parameter(name="all", in="query", @OA\Schema(type="boolean"), description="Lấy tất cả thay vì phân trang"),
     *   @OA\Parameter(name="page", in="query", @OA\Schema(type="integer", minimum=1), description="Trang hiện tại"),
     *   @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", minimum=1, maximum=100), description="Số bản ghi mỗi trang"),
     *   @OA\Parameter(name="status", in="query", @OA\Schema(type="integer", enum={0,1}), description="Trạng thái: 0=tạm dừng, 1=hoạt động"),
     *   @OA\Parameter(name="discount_type", in="query", @OA\Schema(type="string", enum={"phan_tram","tien_mat"}), description="Loại giảm giá"),
     *   @OA\Parameter(name="usable", in="query", @OA\Schema(type="boolean"), description="Chỉ lấy voucher có thể sử dụng"),
     *   @OA\Response(
     *     response=200,
     *     description="Thành công",
     *     @OA\JsonContent(
     *       example={
     *         "status_code":200,
     *         "data":{
     *           {"id":1,"ma_voucher":"GIAM10","ten_voucher":"Giảm 10%","loai_giam_gia":"phan_tram","gia_tri":"10.00","trang_thai":1}
     *         },
     *         "pagination":null,
     *         "message":"Lấy danh sách voucher thành công"
     *       }
     *     )
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Lỗi",
     *     @OA\JsonContent(type="object",
     *       @OA\Property(property="status_code", type="integer", example=400),
     *       @OA\Property(property="message", type="string", example="Có lỗi xảy ra"),
     *       @OA\Property(property="errors", type="object")
     *     )
     *   )
     * )
     */
    public function index(Request $request)
    {
        try {
            $perPage = (int)($request->input('per_page', 10));
            $returnAll = filter_var($request->input('all', false), FILTER_VALIDATE_BOOLEAN);
            $filters = [
                'keyword' => $request->input('search'),
                'status' => $request->input('status'),
                'discount_type' => $request->input('discount_type'),
                'usable' => filter_var($request->input('usable', false), FILTER_VALIDATE_BOOLEAN)
            ];

            if ($filters['keyword'] !== null && $filters['keyword'] !== '' && mb_strlen($filters['keyword']) < 2) {
                return response()->json([
                    'status_code' => 400,
                    'message' => 'Từ khóa tìm kiếm phải có ít nhất 2 ký tự',
                    'errors' => ['search' => ['Từ khóa tìm kiếm không hợp lệ']]
                ], 400);
            }

            $data = $this->service->list($filters, $perPage, $returnAll);

            // Khi có search: chuẩn hóa 300/400 cho cả hai chế độ all/paginate
            if ($filters['keyword'] !== null && $filters['keyword'] !== '') {
                $count = $returnAll ? $data->count() : $data->total();
                if ($count === 0) {
                    return response()->json([
                        'status_code' => 400,
                        'message' => 'Không tìm thấy voucher nào',
                        'errors' => ['search' => ['Từ khóa "' . $filters['keyword'] . '" không tìm thấy kết quả nào']]
                    ], 400);
                }
                if ($count === 1) {
                    $only = $returnAll ? $data->first() : collect($data->items())->first();
                    return response()->json([
                        'status_code' => 300,
                        'message' => 'Yêu cầu chuyển hướng',
                        'redirect_url' => '/api/vouchers/' . $only->id,
                        'suggestion' => 'Tìm thấy đúng 1 kết quả, có thể chuyển đến trang chi tiết',
                        'data' => $returnAll ? $data : $data->items(),
                    ], 300);
                }
            }

            return response()->json([
                'status_code' => 200,
                'data' => $returnAll ? VoucherResource::collection($data) : VoucherResource::collection($data->items()),
                'pagination' => $returnAll ? null : [
                    'current_page' => $data->currentPage(),
                    'per_page' => $data->perPage(),
                    'total' => $data->total(),
                    'last_page' => $data->lastPage(),
                ],
                'message' => 'Lấy danh sách voucher thành công'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Lỗi hệ thống',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *   path="/api/vouchers/{id}",
     *   summary="Chi tiết voucher",
     *   tags={"6. Vouchers"},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(
     *     response=200,
     *     description="Thành công",
     *     @OA\JsonContent(example={"status_code":200,"data":{"id":1,"ma_voucher":"GIAM10","ten_voucher":"Giảm 10%","loai_giam_gia":"phan_tram","gia_tri":"10.00","trang_thai":1},"message":"Lấy thông tin voucher thành công"})
     *   ),
     *   @OA\Response(response="default", description="Lỗi")
     * )
     */
    public function show(int $id)
    {
        try {
            $voucher = $this->service->find($id);
            if (!$voucher) {
                return response()->json([
                    'status_code' => 404,
                    'message' => 'Không tìm thấy voucher',
                    'errors' => ['id' => ['Voucher không tồn tại']]
                ], 404);
            }
            return response()->json([
                'status_code' => 200,
                'data' => new VoucherResource($voucher),
                'message' => 'Lấy thông tin voucher thành công'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Lỗi hệ thống',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *   path="/api/vouchers",
     *   summary="Tạo voucher",
     *   tags={"6. Vouchers"},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       example={"ma_voucher":"GIAM10","ten_voucher":"Giảm 10%","mota":"Voucher giảm 10%","loai_giam_gia":"phan_tram","gia_tri":"10.00","gia_tri_toi_thieu":"100000.00","gia_tri_toi_da":"50000.00","so_luong":100,"ngay_bat_dau":"2025-01-01 00:00:00","ngay_ket_thuc":"2025-12-31 23:59:59","trang_thai":1}
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Tạo mới thành công",
     *     @OA\JsonContent(example={"status_code":200,"data":{"id":10,"ma_voucher":"GIAM10","ten_voucher":"Giảm 10%"},"message":"Tạo voucher thành công"})
     *   ),
     *   @OA\Response(response="default", description="Lỗi")
     * )
     */
    public function store(Request $request)
    {
        try {
            $data = $request->only([
                'ma_voucher', 'ten_voucher', 'mota', 'loai_giam_gia', 
                'gia_tri', 'gia_tri_toi_thieu', 'gia_tri_toi_da', 
                'so_luong', 'ngay_bat_dau', 'ngay_ket_thuc', 'trang_thai'
            ]);
            
            if (empty($data['ma_voucher'])) {
                return response()->json([
                    'status_code' => 422,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => ['ma_voucher' => ['Mã voucher là bắt buộc']]
                ], 422);
            }
            
            if (empty($data['ten_voucher'])) {
                return response()->json([
                    'status_code' => 422,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => ['ten_voucher' => ['Tên voucher là bắt buộc']]
                ], 422);
            }

            // Check duplicate ma_voucher
            if (Voucher::where('ma_voucher', $data['ma_voucher'])->exists()) {
                return response()->json([
                    'status_code' => 400,
                    'message' => 'Mã voucher đã tồn tại',
                    'errors' => ['ma_voucher' => ['Mã voucher đã tồn tại']]
                ], 400);
            }

            $created = $this->service->create($data);
            return response()->json([
                'status_code' => 200,
                'data' => new VoucherResource($created),
                'message' => 'Tạo voucher thành công'
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            $msg = 'Dữ liệu không hợp lệ hoặc trùng lặp';
            $errors = [];
            $raw = $e->getMessage();
            if (str_contains($raw, 'Duplicate entry') && str_contains($raw, 'ma_voucher')) {
                $msg = 'Mã voucher đã tồn tại';
                $errors['ma_voucher'] = ['Mã voucher đã tồn tại'];
            }
            return response()->json([
                'status_code' => 400,
                'message' => $msg,
                'errors' => $errors ?: ['general' => ['Vui lòng kiểm tra lại dữ liệu nhập']]
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Lỗi hệ thống',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *   path="/api/vouchers/{id}",
     *   summary="Cập nhật voucher",
     *   tags={"6. Vouchers"},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       example={"ma_voucher":"GIAM10","ten_voucher":"Giảm 10% 2025","mota":"Voucher giảm 10% cập nhật","loai_giam_gia":"phan_tram","gia_tri":"10.00","gia_tri_toi_thieu":"100000.00","gia_tri_toi_da":"50000.00","so_luong":100,"ngay_bat_dau":"2025-01-01 00:00:00","ngay_ket_thuc":"2025-12-31 23:59:59","trang_thai":1}
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Cập nhật thành công",
     *     @OA\JsonContent(example={"status_code":200,"data":{"id":10,"ma_voucher":"GIAM10","ten_voucher":"Giảm 10% 2025"},"message":"Cập nhật voucher thành công"})
     *   ),
     *   @OA\Response(response="default", description="Lỗi")
     * )
     */
    public function update(Request $request, int $id)
    {
        try {
            $data = $request->only([
                'ma_voucher', 'ten_voucher', 'mota', 'loai_giam_gia', 
                'gia_tri', 'gia_tri_toi_thieu', 'gia_tri_toi_da', 
                'so_luong', 'ngay_bat_dau', 'ngay_ket_thuc', 'trang_thai'
            ]);
            
            // Duplicate validation for update
            if (!empty($data['ma_voucher']) && Voucher::where('ma_voucher', $data['ma_voucher'])->where('id', '!=', $id)->exists()) {
                return response()->json([
                    'status_code' => 400,
                    'message' => 'Mã voucher đã tồn tại',
                    'errors' => ['ma_voucher' => ['Mã voucher đã tồn tại']]
                ], 400);
            }
            
            $updated = $this->service->update($id, $data);
            if (!$updated) {
                return response()->json([
                    'status_code' => 404,
                    'message' => 'Không tìm thấy voucher',
                    'errors' => ['id' => ['Voucher không tồn tại']]
                ], 404);
            }
            return response()->json([
                'status_code' => 200,
                'data' => new VoucherResource($updated),
                'message' => 'Cập nhật voucher thành công'
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            $msg = 'Dữ liệu không hợp lệ hoặc trùng lặp';
            $errors = [];
            $raw = $e->getMessage();
            if (str_contains($raw, 'Duplicate entry') && str_contains($raw, 'ma_voucher')) {
                $msg = 'Mã voucher đã tồn tại';
                $errors['ma_voucher'] = ['Mã voucher đã tồn tại'];
            }
            return response()->json([
                'status_code' => 400,
                'message' => $msg,
                'errors' => $errors ?: ['general' => ['Vui lòng kiểm tra lại dữ liệu nhập']]
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Lỗi hệ thống',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *   path="/api/vouchers/{id}",
     *   summary="Xóa voucher",
     *   tags={"6. Vouchers"},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(
     *     response=200,
     *     description="Xóa thành công",
     *     @OA\JsonContent(example={"status_code":200,"message":"Xóa voucher thành công"})
     *   ),
     *   @OA\Response(response="default", description="Lỗi")
     * )
     */
    public function destroy(int $id)
    {
        try {
            $deleted = $this->service->delete($id);
            if (!$deleted) {
                return response()->json([
                    'status_code' => 404,
                    'message' => 'Không tìm thấy voucher',
                    'errors' => ['id' => ['Voucher không tồn tại']]
                ], 404);
            }
            return response()->json([
                'status_code' => 200,
                'message' => 'Xóa voucher thành công'
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status_code' => 409,
                'message' => 'Không thể xóa do đang được tham chiếu ở nơi khác',
                'errors' => ['constraint' => ['Vui lòng xóa dữ liệu liên quan trước hoặc đổi trạng thái']],
            ], 409);
        } catch (\Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Lỗi hệ thống',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
