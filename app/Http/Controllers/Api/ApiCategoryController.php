<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ApiCategoryService;
use OpenApi\Annotations as OA;
use Illuminate\Http\Request;
use App\Models\DanhMuc;

/**
 */
class ApiCategoryController extends Controller
{
    public function __construct(private ApiCategoryService $service)
    {
    }

    /**
     * @OA\Get(
     *   path="/api/categories",
     *   summary="Danh sách danh mục",
     *   tags={"3. Categories"},
     *   @OA\Parameter(name="search", in="query", @OA\Schema(type="string"), description="Tìm kiếm theo tên/slug", example="Đồ uống"),
     *   @OA\Parameter(name="all", in="query", @OA\Schema(type="boolean"), description="Lấy tất cả thay vì phân trang", example=true),
     *   @OA\Parameter(name="page", in="query", @OA\Schema(type="integer", minimum=1), description="Trang hiện tại", example=1),
     *   @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", minimum=1, maximum=100), description="Số bản ghi mỗi trang", example=10),
     *   @OA\Response(
     *     response=200,
     *     description="Thành công",
     *     @OA\JsonContent(
     *       example={
     *         "status_code":200,
     *         "data":{
     *           {"id":1,"name":"Đồ uống","slug":"do-uong","status":"active"}
     *         },
     *         "pagination":null,
     *         "message":"Lấy danh sách danh mục thành công"
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
                'status' => $request->input('status')
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
                        'message' => 'Không tìm thấy danh mục nào',
                        'errors' => ['search' => ['Từ khóa "' . $filters['keyword'] . '" không tìm thấy kết quả nào']]
                    ], 400);
                }
                if ($count === 1) {
                    $only = $returnAll ? $data->first() : collect($data->items())->first();
                    return response()->json([
                        'status_code' => 300,
                        'message' => 'Yêu cầu chuyển hướng',
                        'redirect_url' => '/api/categories/' . $only->id,
                        'suggestion' => 'Tìm thấy đúng 1 kết quả, có thể chuyển đến trang chi tiết',
                        'data' => $returnAll ? $data : $data->items(),
                    ], 300);
                }
            }

            return response()->json([
                'status_code' => 200,
                'data' => $returnAll ? $data : $data->items(),
                'pagination' => $returnAll ? null : [
                    'current_page' => $data->currentPage(),
                    'per_page' => $data->perPage(),
                    'total' => $data->total(),
                    'last_page' => $data->lastPage(),
                ],
                'message' => 'Lấy danh sách danh mục thành công'
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
     *   path="/api/categories/{id}",
     *   summary="Chi tiết danh mục",
     *   tags={"3. Categories"},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(
     *     response=200,
     *     description="Thành công",
     *     @OA\JsonContent(example={"status_code":200,"data":{"id":1,"name":"Đồ uống","slug":"do-uong"},"message":"Lấy thông tin danh mục thành công"})
     *   ),
     *   @OA\Response(response="default", description="Lỗi")
     * )
     */
    public function show(int $id)
    {
        try {
            $cat = $this->service->find($id);
            if (!$cat) {
                return response()->json([
                    'status_code' => 404,
                    'message' => 'Không tìm thấy danh mục',
                    'errors' => ['id' => ['Danh mục không tồn tại']]
                ], 404);
            }
            return response()->json([
                'status_code' => 200,
                'data' => $cat,
                'message' => 'Lấy thông tin danh mục thành công'
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
     *   path="/api/categories",
     *   summary="Tạo danh mục",
     *   tags={"3. Categories"},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       example={"name":"Đồ uống","slug":"do-uong","parent_id":null,"description":"Nhóm đồ uống","status":"active"}
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Tạo mới thành công",
     *     @OA\JsonContent(example={"status_code":200,"data":{"id":10,"name":"Đồ uống"},"message":"Tạo danh mục thành công"})
     *   ),
     *   @OA\Response(response="default", description="Lỗi")
     * )
     */
    public function store(Request $request)
    {
        try {
            $data = $request->only(['name','slug','parent_id','description','status']);
            if (empty($data['name'])) {
                return response()->json([
                    'status_code' => 422,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => ['name' => ['Tên là bắt buộc']]
                ], 422);
            }
            // Chuẩn hóa parent_id: nếu không gửi hoặc null thì set 0 (gốc)
            $data['parent_id'] = isset($data['parent_id']) && $data['parent_id'] !== '' ? (int) $data['parent_id'] : 0;
            // Check duplicate name/slug
            if (DanhMuc::where('name', $data['name'])->exists()) {
                return response()->json([
                    'status_code' => 400,
                    'message' => 'Tên danh mục đã tồn tại',
                    'errors' => ['name' => ['Tên danh mục đã tồn tại']]
                ], 400);
            }
            if (!empty($data['slug']) && DanhMuc::where('slug', $data['slug'])->exists()) {
                return response()->json([
                    'status_code' => 400,
                    'message' => 'Slug đã tồn tại',
                    'errors' => ['slug' => ['Slug đã tồn tại']]
                ], 400);
            }
            $created = $this->service->create($data);
            return response()->json([
                'status_code' => 200,
                'data' => $created,
                'message' => 'Tạo danh mục thành công'
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            $msg = 'Dữ liệu không hợp lệ hoặc trùng lặp';
            $errors = [];
            $raw = $e->getMessage();
            if (str_contains($raw, "'parent_id'")) {
                $msg = 'Danh mục cha không hợp lệ';
                $errors['parent_id'] = ['Danh mục cha không hợp lệ'];
            }
            if (str_contains($raw, 'Duplicate entry') && str_contains($raw, 'slug')) {
                $msg = 'Slug đã tồn tại';
                $errors['slug'] = ['Slug đã tồn tại'];
            }
            if (str_contains($raw, 'Duplicate entry') && str_contains($raw, 'name')) {
                $msg = 'Tên danh mục đã tồn tại';
                $errors['name'] = ['Tên danh mục đã tồn tại'];
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
     *   path="/api/categories/{id}",
     *   summary="Cập nhật danh mục",
     *   tags={"3. Categories"},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       example={"name":"Đồ uống 2025","slug":"do-uong-2025","parent_id":null,"description":"Cập nhật","status":"active"}
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Cập nhật thành công",
     *     @OA\JsonContent(example={"status_code":200,"data":{"id":10,"name":"Đồ uống 2025"},"message":"Cập nhật danh mục thành công"})
     *   ),
     *   @OA\Response(response="default", description="Lỗi")
     * )
     */
    public function update(Request $request, int $id)
    {
        try {
            $data = $request->only(['name','slug','parent_id','description','status']);
            // Chuẩn hóa parent_id: nếu không gửi hoặc null thì set 0 (gốc)
            $data['parent_id'] = isset($data['parent_id']) && $data['parent_id'] !== '' ? (int) $data['parent_id'] : 0;
            // Duplicate validation for update
            if (!empty($data['name']) && DanhMuc::where('name', $data['name'])->where('id', '!=', $id)->exists()) {
                return response()->json([
                    'status_code' => 400,
                    'message' => 'Tên danh mục đã tồn tại',
                    'errors' => ['name' => ['Tên danh mục đã tồn tại']]
                ], 400);
            }
            if (!empty($data['slug']) && DanhMuc::where('slug', $data['slug'])->where('id', '!=', $id)->exists()) {
                return response()->json([
                    'status_code' => 400,
                    'message' => 'Slug đã tồn tại',
                    'errors' => ['slug' => ['Slug đã tồn tại']]
                ], 400);
            }
            $updated = $this->service->update($id, $data);
            if (!$updated) {
                return response()->json([
                    'status_code' => 404,
                    'message' => 'Không tìm thấy danh mục',
                    'errors' => ['id' => ['Danh mục không tồn tại']]
                ], 404);
            }
            return response()->json([
                'status_code' => 200,
                'data' => $updated,
                'message' => 'Cập nhật danh mục thành công'
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status_code' => 422,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Illuminate\Database\QueryException $e) {
            $msg = 'Dữ liệu không hợp lệ hoặc trùng lặp';
            $errors = [];
            $raw = $e->getMessage();
            if (str_contains($raw, "'parent_id'")) {
                $msg = 'Danh mục cha không hợp lệ';
                $errors['parent_id'] = ['Danh mục cha không hợp lệ'];
            }
            if (str_contains($raw, 'Duplicate entry') && str_contains($raw, 'slug')) {
                $msg = 'Slug đã tồn tại';
                $errors['slug'] = ['Slug đã tồn tại'];
            }
            if (str_contains($raw, 'Duplicate entry') && str_contains($raw, 'name')) {
                $msg = 'Tên danh mục đã tồn tại';
                $errors['name'] = ['Tên danh mục đã tồn tại'];
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
     *   path="/api/categories/{id}",
     *   summary="Xóa danh mục",
     *   tags={"3. Categories"},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(
     *     response=200,
     *     description="Xóa thành công",
     *     @OA\JsonContent(example={"status_code":200,"message":"Xóa danh mục thành công"})
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
                    'message' => 'Không tìm thấy danh mục',
                    'errors' => ['id' => ['Danh mục không tồn tại']]
                ], 404);
            }
            return response()->json([
                'status_code' => 200,
                'message' => 'Xóa danh mục thành công'
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


