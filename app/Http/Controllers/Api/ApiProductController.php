<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use OpenApi\Annotations as OA;
use App\Http\Requests\Api\ProductIndexRequest;
use App\Http\Requests\Api\ProductStoreRequest;
use App\Http\Requests\Api\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Services\ApiProductService;
use App\Models\SanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 */
class ApiProductController extends Controller
{
    protected $apiProductService;

    public function __construct(ApiProductService $apiProductService)
    {
        $this->apiProductService = $apiProductService;
    }

    /**
     * @OA\Get(
     *   path="/api/products",
     *   summary="Danh sách sản phẩm (có phân trang)",
     *   description="API này trả về danh sách sản phẩm với phân trang và các bộ lọc. Hỗ trợ tìm kiếm theo tên, mã sản phẩm, mô tả.",
     *   operationId="getProducts",
     *   tags={"4. Products"},
     *   @OA\Parameter(name="search", in="query", @OA\Schema(type="string"), description="Tìm kiếm theo tên, mã sản phẩm, mô tả"),
     *   @OA\Parameter(name="page", in="query", @OA\Schema(type="integer", minimum=1), description="Trang hiện tại"),
     *   @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", minimum=1, maximum=100), description="Số bản ghi mỗi trang"),
     *   @OA\Parameter(name="status", in="query", @OA\Schema(type="integer", enum={0,1}), description="Trạng thái: 0=ngừng kinh doanh, 1=kinh doanh"),
     *   @OA\Parameter(name="category", in="query", @OA\Schema(type="integer"), description="ID danh mục sản phẩm"),
     *   @OA\Parameter(name="min_price", in="query", @OA\Schema(type="number"), description="Giá tối thiểu"),
     *   @OA\Parameter(name="max_price", in="query", @OA\Schema(type="number"), description="Giá tối đa"),
     *   @OA\Response(
     *     response=200,
     *     description="Thành công",
     *     @OA\JsonContent(
     *       example={
     *         "data": {
     *           {
     *             "id": 1,
     *             "maSP": "SP001",
     *             "tenSP": "Áo thun nam",
     *             "id_danhmuc": 1,
     *             "moTa": "Áo thun chất lượng cao",
     *             "trangthai": true,
     *             "base_price": 150000.00,
     *             "base_sale_price": 120000.00,
     *             "danhmuc": {
     *               "id": 1,
     *               "name": "Áo thun"
     *             },
     *             "hinhanh": {},
     *             "chitietsanpham": {},
     *             "created_at": "2024-01-01T00:00:00.000000Z",
     *             "updated_at": "2024-01-01T00:00:00.000000Z"
     *           }
     *         },
     *         "pagination": {
     *           "current_page": 1,
     *           "per_page": 10,
     *           "total": 1,
     *           "last_page": 1
     *         },
     *         "message": "Lấy danh sách sản phẩm thành công"
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
    public function index(ProductIndexRequest $request)
    {
        $validated = $request->validated();
        
        // Map Vietnamese aliases to standard query names
        if (isset($validated['trang'])) {
            $request->merge(['page' => $validated['trang']]);
        }
        $perPage = $validated['per_page'] ?? $validated['so_tren_trang'] ?? 10;
        
        // Mặc định phân trang. Nếu muốn lấy hết, truyền all=true hoặc tat_ca=true
        $returnAll = filter_var($request->input('all', $request->input('tat_ca', false)), FILTER_VALIDATE_BOOLEAN);

        // Chuẩn hóa filters để chuyển xuống Service/Repository
        $keyword = $validated['keyword'] ?? ($validated['tu_khoa'] ?? ($request->input('search') ?? null));

        // Validate search min length
        if ($keyword !== null && $keyword !== '' && mb_strlen($keyword) < 2) {
            return response()->json([
                'status_code' => 400,
                'message' => 'Từ khóa tìm kiếm phải có ít nhất 2 ký tự',
                'errors' => ['search' => ['Từ khóa tìm kiếm không hợp lệ']],
            ], 400);
        }

        $sortBy = $request->input('sort_by', $request->input('sap_xep_theo', 'created_at'));
        $sortDir = strtolower($request->input('sort_dir', $request->input('chieu_sap_xep', 'desc'))) === 'asc' ? 'asc' : 'desc';
        
        $filters = [
            'keyword' => $keyword,
            'status' => $validated['status'] ?? $validated['trang_thai'] ?? null,
            'category' => $validated['category'] ?? $validated['danh_muc'] ?? null,
            'min_price' => $validated['min_price'] ?? $validated['gia_toi_thieu'] ?? null,
            'max_price' => $validated['max_price'] ?? $validated['gia_toi_da'] ?? null,
            'sort_by' => $sortBy,
            'sort_dir' => $sortDir,
        ];

        // Trả toàn bộ nếu all=true
        if ($returnAll) {
            $result = $this->apiProductService->getProducts($filters, $perPage, true);
            $items = collect($result['data']);

            // Không tìm thấy theo từ khóa
            if ($keyword !== null && $items->count() === 0) {
                return response()->json([
                    'status_code' => 400,
                    'message' => 'Không tìm thấy sản phẩm nào',
                    'errors' => ['search' => ['Từ khóa "' . $keyword . '" không tìm thấy kết quả nào']],
                ], 400);
            }

            // Nếu đúng 1 kết quả và có search → 300 (Multiple Choices)
            if ($keyword !== null && $items->count() === 1) {
                $only = $items->first();
                return response()->json([
                    'status_code' => 300,
                    'message' => 'Yêu cầu chuyển hướng',
                    'redirect_url' => '/api/products/' . $only->id,
                    'suggestion' => 'Tìm thấy đúng 1 kết quả, có thể chuyển đến trang chi tiết',
                    'data' => ProductResource::collection($items),
                ], 300);
            }

            return response()->json([
                'status_code' => 200,
                'data' => ProductResource::collection($items),
                'pagination' => null,
                'message' => 'Lấy danh sách sản phẩm thành công',
            ]);
        }

        // Mặc định: phân trang thông qua Service
        $serviceResult = $this->apiProductService->getProducts($filters, $perPage, false);
        $itemsCollection = collect($serviceResult['data']);
        $pagination = $serviceResult['pagination'];
        $currentPage = $pagination['current_page'];
        $lastPage = $pagination['last_page'];
        $pages = $pagination['pages'];

        // Nếu có từ khóa nhưng không có kết quả, trả về 400 giống nhánh all=true
        if ($keyword !== null && $keyword !== '' && $itemsCollection->count() === 0) {
            return response()->json([
                'status_code' => 400,
                'message' => 'Không tìm thấy sản phẩm nào',
                'errors' => ['search' => ['Từ khóa "' . $keyword . '" không tìm thấy kết quả nào']],
                'pagination' => [
                    'current_page' => $pagination['current_page'],
                    'per_page' => $pagination['per_page'],
                    'total' => $pagination['total'],
                    'last_page' => $pagination['last_page'],
                    'prev_page' => $currentPage > 1 ? $currentPage - 1 : null,
                    'next_page' => $currentPage < $lastPage ? $currentPage + 1 : null,
                    'prev_url' => $pagination['prev_url'],
                    'next_url' => $pagination['next_url'],
                    'first_url' => $pagination['first_url'],
                    'last_url' => $pagination['last_url'],
                    'pages' => $pages,
                    'path' => $pagination['path'],
                ],
            ], 400);
        }

        // Nếu có từ khóa và tổng kết quả đúng 1 → trả về 300 (Multiple Choices)
        if ($keyword !== null && $keyword !== '' && ($pagination['total'] ?? 0) === 1) {
            $onlyItem = $itemsCollection->first();
            return response()->json([
                'status_code' => 300,
                'message' => 'Yêu cầu chuyển hướng',
                'redirect_url' => '/api/products/' . ($onlyItem->id ?? $onlyItem['id'] ?? null),
                'suggestion' => 'Tìm thấy đúng 1 kết quả, có thể chuyển đến trang chi tiết',
                'data' => ProductResource::collection($itemsCollection),
                'pagination' => [
                    'current_page' => $pagination['current_page'],
                    'per_page' => $pagination['per_page'],
                    'total' => $pagination['total'],
                    'last_page' => $pagination['last_page'],
                    'prev_page' => $currentPage > 1 ? $currentPage - 1 : null,
                    'next_page' => $currentPage < $lastPage ? $currentPage + 1 : null,
                    'prev_url' => $pagination['prev_url'],
                    'next_url' => $pagination['next_url'],
                    'first_url' => $pagination['first_url'],
                    'last_url' => $pagination['last_url'],
                    'pages' => $pages,
                    'path' => $pagination['path'],
                ],
            ], 300);
        }

        return response()->json([
            'status_code' => 200,
            'data' => ProductResource::collection($itemsCollection),
            'pagination' => [
                'current_page' => $currentPage,
                'per_page' => $pagination['per_page'],
                'total' => $pagination['total'],
                'last_page' => $lastPage,
                'prev_page' => $currentPage > 1 ? $currentPage - 1 : null,
                'next_page' => $currentPage < $lastPage ? $currentPage + 1 : null,
                'prev_url' => $pagination['prev_url'],
                'next_url' => $pagination['next_url'],
                'first_url' => $pagination['first_url'],
                'last_url' => $pagination['last_url'],
                'pages' => $pages,
                'path' => $pagination['path'],
            ],
            'message' => 'Lấy danh sách sản phẩm thành công',
        ]);
    }

    /**
     * @OA\Post(
     *   path="/api/products",
     *   summary="Tạo mới sản phẩm",
     *   tags={"4. Products"},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       example={
     *         "maSP": "SP001",
     *         "tenSP": "Áo thun nam",
     *         "id_danhmuc": 1,
     *         "moTa": "Áo thun chất lượng cao",
     *         "trangthai": 1,
     *         "base_price": 150000.00,
     *         "base_sale_price": 120000.00,
     *         "variants": {
     *           {
     *             "ten": "Áo thun nam - Đỏ",
     *             "mausac": 1,
     *             "sizes": {
     *               {
     *                 "size": 1,
     *                 "so_luong": 50,
     *                 "gia": 150000.00,
     *                 "gia_khuyenmai": 120000.00
     *               }
     *             }
     *           }
     *         }
     *       }
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Tạo mới thành công",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="data", type="object"),
     *       @OA\Property(property="message", type="string", example="Tạo sản phẩm thành công")
     *     )
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Lỗi",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status_code", type="integer", example=409),
     *       @OA\Property(property="message", type="string", example="Mã sản phẩm đã tồn tại"),
     *       @OA\Property(property="errors", type="object")
     *     )
     *   )
     * )
     */
    public function store(ProductStoreRequest $request)
    {
        try {
            $data = $request->validated();
            $product = $this->apiProductService->createProduct($data);
            return response()->json([
                'status_code' => 200,
                'data' => new ProductResource($product),
                'message' => 'Tạo sản phẩm thành công',
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status_code' => 422,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Create product failed', ['error' => $e->getMessage(), 'info' => $e->errorInfo]);
            $sqlState = $e->errorInfo[0] ?? null;
            $driverCode = $e->errorInfo[1] ?? null; // e.g. 1062 duplicate, 1452 FK
            $message = $e->getMessage();

            // Duplicate entry
            if ((int) $driverCode === 1062) {
            return response()->json([
                'status_code' => 400,
                    'message' => 'Mã sản phẩm đã tồn tại',
                'errors' => ['maSP' => ['Mã sản phẩm đã tồn tại']],
                ], 400);
            }

            // Foreign key constraint fails
            if ((int) $driverCode === 1452) {
                return response()->json([
                    'status_code' => 400,
                    'message' => 'Dữ liệu tham chiếu không hợp lệ',
                    'errors' => [
                        'foreign_keys' => [
                            'Kiểm tra lại id_danhmuc, mausac, size có tồn tại hay không.'
                        ],
                    ],
                ], 400);
            }

            return response()->json([
                'status_code' => 400,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => ['db' => [$message]],
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Lỗi hệ thống',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *   path="/api/products/{id}",
     *   summary="Chi tiết sản phẩm",
     *   tags={"4. Products"},
     *   @OA\Parameter(name="id", in="path", required=true, description="ID sản phẩm", @OA\Schema(type="integer")),
     *   @OA\Response(
     *     response=200,
     *     description="Thành công",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="data", type="object"),
     *       @OA\Property(property="message", type="string", example="Lấy thông tin sản phẩm thành công")
     *     )
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Lỗi",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status_code", type="integer", example=404),
     *       @OA\Property(property="message", type="string", example="Không tìm thấy sản phẩm"),
     *       @OA\Property(property="errors", type="object")
     *     )
     *   )
     * )
     */
    public function show($id)
    {
        if (!ctype_digit((string) $id)) {
            return response()->json([
                'status_code' => 422,
                'message' => 'Vui lòng sửa các lỗi và thử lại.',
                'errors' => [
                    'id' => ['Giá trị phải là số nguyên'],
                ],
            ], 422);
        }
        $product = $this->apiProductService->getProductById((int) $id);
        if (!$product) {
            return response()->json([
                'status_code' => 404,
                'message' => 'Không tìm thấy sản phẩm',
                'errors' => ['id' => ['Sản phẩm không tồn tại']],
            ], 404);
        }
        return response()->json([
            'status_code' => 200,
            'data' => new ProductResource($product),
            'message' => 'Lấy thông tin sản phẩm thành công',
        ]);
    }

    /**
     * @OA\Put(
     *   path="/api/products/{id}",
     *   summary="Cập nhật sản phẩm",
     *   tags={"4. Products"},
     *   @OA\Parameter(name="id", in="path", required=true, description="ID sản phẩm", @OA\Schema(type="integer")),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       example={
     *         "maSP": "SP001",
     *         "tenSP": "Áo thun nam cập nhật",
     *         "id_danhmuc": 1,
     *         "moTa": "Áo thun chất lượng cao - đã cập nhật",
     *         "trangthai": 1,
     *         "base_price": 160000.00,
     *         "base_sale_price": 130000.00
     *       }
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Cập nhật thành công",
     *     @OA\JsonContent(
     *       example={
     *         "status_code": 200,
     *         "data": {
     *           "id": 1,
     *           "maSP": "SP001",
     *           "tenSP": "Áo thun nam cập nhật",
     *           "id_danhmuc": 1,
     *           "moTa": "Áo thun chất lượng cao - đã cập nhật",
     *           "trangthai": true,
     *           "base_price": 160000.00,
     *           "base_sale_price": 130000.00,
     *           "created_at": "2024-01-01T00:00:00.000000Z",
     *           "updated_at": "2024-01-01T00:00:00.000000Z"
     *         },
     *         "message": "Cập nhật sản phẩm thành công"
     *       }
     *     )
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Lỗi",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status_code", type="integer", example=400),
     *       @OA\Property(property="message", type="string", example="Dữ liệu không hợp lệ"),
     *       @OA\Property(property="errors", type="object")
     *     )
     *   )
     * )
     */
    public function update(ProductUpdateRequest $request, $id)
    {
        if (!ctype_digit((string) $id)) {
            return response()->json([
                'status_code' => 422,
                'message' => 'Vui lòng sửa các lỗi và thử lại.',
                'errors' => [
                    'id' => ['Giá trị phải là số nguyên'],
                ],
            ], 422);
        }
        try {
            $data = $request->validated();
            $updatedProduct = $this->apiProductService->updateProduct((int) $id, $data);
            if (!$updatedProduct) {
                return response()->json([
                    'status_code' => 404,
                    'message' => 'Không tìm thấy sản phẩm',
                    'errors' => ['id' => ['Sản phẩm không tồn tại']],
                ], 404);
            }
            return response()->json([
                'status_code' => 200,
                'data' => new ProductResource($updatedProduct),
                'message' => 'Cập nhật sản phẩm thành công',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status_code' => 422,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status_code' => 400,
                'message' => 'Mã sản phẩm đã tồn tại hoặc dữ liệu không hợp lệ',
                'errors' => ['maSP' => ['Mã sản phẩm đã tồn tại']],
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Lỗi hệ thống',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *   path="/api/products/{id}",
     *   summary="Xóa sản phẩm mềm",
     *   tags={"4. Products"},
     *   @OA\Parameter(name="id", in="path", required=true, description="ID sản phẩm", @OA\Schema(type="integer")),
     *   @OA\Response(
     *     response=200,
     *     description="Xóa thành công",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="message", type="string", example="Xóa sản phẩm thành công")
     *     )
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Lỗi",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status_code", type="integer", example=400),
     *       @OA\Property(property="message", type="string", example="Dữ liệu không hợp lệ"),
     *       @OA\Property(property="errors", type="object")
     *     )
     *   )
     * )
     */
    public function destroy($id)
    {
        if (!ctype_digit((string) $id)) {
            return response()->json([
                'status_code' => 422,
                'message' => 'Vui lòng sửa các lỗi và thử lại.',
                'errors' => [
                    'id' => ['Giá trị phải là số nguyên'],
                ],
            ], 422);
        }
        try {
            $deleted = $this->apiProductService->deleteProduct((int) $id);
            if (!$deleted) {
                return response()->json([
                    'status_code' => 404,
                    'message' => 'Không tìm thấy sản phẩm',
                    'errors' => ['id' => ['Sản phẩm không tồn tại']],
                ], 404);
            }
            return response()->json([
                'status_code' => 200,
                'message' => 'Xóa sản phẩm thành công',
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status_code' => 409,
                'message' => 'Không thể xóa sản phẩm vì đang được tham chiếu ở dữ liệu khác (ví dụ: đơn hàng).',
                'errors' => ['constraint' => ['Vui lòng xóa/thu hồi dữ liệu liên quan trước, hoặc vô hiệu hóa sản phẩm.']],
            ], 409);
        } catch (\Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Lỗi hệ thống',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *   path="/api/products/{id}/restore",
     *   summary="Khôi phục sản phẩm đã xóa mềm",
     *   tags={"4. Products"},
     *   @OA\Parameter(name="id", in="path", required=true, description="ID sản phẩm", @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="Khôi phục thành công"),
     *   @OA\Response(response=404, description="Không tìm thấy sản phẩm")
     * )
     */
    public function restore($id)
    {
        if (!ctype_digit((string) $id)) {
            return response()->json([
                'status_code' => 422,
                'message' => 'Vui lòng sửa các lỗi và thử lại.',
                'errors' => [
                    'id' => ['Giá trị phải là số nguyên'],
                ],
            ], 422);
        }
        $ok = $this->apiProductService->restoreProduct((int) $id);
        if (!$ok) {
            return response()->json([
                'status_code' => 404,
                'message' => 'Không tìm thấy sản phẩm cần khôi phục',
            ], 404);
        }
        return response()->json([
            'status_code' => 200,
            'message' => 'Khôi phục sản phẩm thành công',
        ]);
    }

    /**
     * @OA\Delete(
     *   path="/api/products/{id}/force",
     *   summary="Xóa vĩnh viễn sản phẩm",
     *   tags={"4. Products"},
     *   @OA\Parameter(name="id", in="path", required=true, description="ID sản phẩm", @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="Xóa vĩnh viễn thành công"),
     *   @OA\Response(response=404, description="Không tìm thấy sản phẩm")
     * )
     */
    public function forceDestroy($id)
    {
        if (!ctype_digit((string) $id)) {
            return response()->json([
                'status_code' => 422,
                'message' => 'Vui lòng sửa các lỗi và thử lại.',
                'errors' => [
                    'id' => ['Giá trị phải là số nguyên'],
                ],
            ], 422);
        }
        try {
            $result = $this->apiProductService->forceDeleteProduct((int) $id);
            if ($result === false) {
                return response()->json([
                    'status_code' => 404,
                    'message' => 'Không tìm thấy sản phẩm để xóa vĩnh viễn',
                ], 404);
            }
            if (is_array($result) && ($result['ok'] ?? false) === false && ($result['blocked_by_orders'] ?? false)) {
                return response()->json([
                    'status_code' => 409,
                    'message' => 'Không thể xóa vĩnh viễn vì sản phẩm đã có dữ liệu đơn hàng liên quan.',
                    'errors' => [
                        'orders' => ['Có ' . ($result['order_detail_count'] ?? 0) . ' chi tiết đơn hàng đang tham chiếu sản phẩm này. Hãy xóa/thu hồi dữ liệu liên quan hoặc chỉ dùng xóa mềm.']
                    ],
                ], 409);
            }
            return response()->json([
                'status_code' => 200,
                'message' => 'Xóa vĩnh viễn sản phẩm thành công',
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status_code' => 409,
                'message' => 'Không thể xóa vĩnh viễn do ràng buộc dữ liệu liên quan.',
            ], 409);
        }
    }

    /**
     * Cập nhật stock hiển thị
     */
    public function updateStock(Request $request)
    {
        try {
            $productId = $request->input('product_id');
            
            if (!$productId) {
                return response()->json([
                    'status_code' => 400,
                    'message' => 'Thiếu product_id',
                ], 400);
            }

            // Lấy sản phẩm
            $product = \App\Models\Sanpham::find($productId);
            if (!$product) {
                return response()->json([
                    'status_code' => 404,
                    'message' => 'Không tìm thấy sản phẩm',
                ], 404);
            }

            // Lấy số lượng trong giỏ hàng
            $cartQuantity = 0;
            if (session()->has('cart')) {
                $cart = session('cart');
                foreach ($cart as $item) {
                    if ($item['product_id'] == $productId) {
                        $cartQuantity += $item['quantity'];
                    }
                }
            }

            // Tính stock thực tế
            $mainStock = $product->soLuong - $cartQuantity;
            $variantStock = 0;
            
            // Tính stock của variants
            if ($product->chitietsanpham) {
                foreach ($product->chitietsanpham as $variant) {
                    $variantStock += $variant->soLuong;
                }
            }

            return response()->json([
                'status_code' => 200,
                'message' => 'Cập nhật stock thành công',
                'main_stock' => max(0, $mainStock),
                'variant_stock' => $variantStock,
                'cart_quantity' => $cartQuantity,
                'total_stock' => max(0, $mainStock) + $variantStock,
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating stock: ' . $e->getMessage());
            return response()->json([
                'status_code' => 500,
                'message' => 'Lỗi server khi cập nhật stock',
            ], 500);
        }
    }

    /**
     * Lấy thông tin stock của sản phẩm
     */
    public function getStock($id)
    {
        try {
            $product = SanPham::with(['chitietsanpham.mausac', 'chitietsanpham.size'])->find($id);
            
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy sản phẩm'
                ], 404);
            }

            // Debug: Log dữ liệu từ database
            Log::info('Product stock data:', [
                'product_id' => $product->id,
                'main_stock' => $product->soLuong,
                'variants_count' => $product->chitietsanpham->count()
            ]);
            
            $variants = $product->chitietsanpham->map(function($variant) {
                // Debug: Log từng variant
                Log::info('Variant data:', [
                    'id' => $variant->id,
                    'color_name' => $variant->mausac ? $variant->mausac->ten : null,
                    'size_name' => $variant->size ? $variant->size->ten : null,
                    'stock' => $variant->soLuong,
                    'price' => $variant->gia
                ]);
                
                return [
                    'id' => $variant->id,
                    'color_id' => $variant->id_mausac,
                    'color_name' => $variant->mausac ? $variant->mausac->ten : null,
                    'color_code' => $variant->mausac ? $variant->mausac->ma_mau : null,
                    'size_id' => $variant->id_size,
                    'size_name' => $variant->size ? $variant->size->ten : null,
                    'price' => $variant->gia,
                    'sale_price' => $variant->gia_khuyenmai,
                    'stock' => $variant->soLuong,
                    'original_stock' => $variant->soLuong
                ];
            });

            return response()->json([
                'success' => true,
                'product_id' => $product->id,
                'main_stock' => $product->soLuong,
                'variants' => $variants,
                'total_variants' => $variants->count(),
                'available_variants' => $variants->where('stock', '>', 0)->count(),
                'out_of_stock_variants' => $variants->where('stock', '<=', 0)->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting stock: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server khi lấy thông tin stock'
            ], 500);
        }
    }

    /**
     * Lấy thông tin stock chi tiết cho trang sản phẩm
     */
    public function getDetailedStock(Request $request, $id)
    {
        try {
            $cartService = new \App\Services\CartService();
            $result = $cartService->getDetailedStockInfo($id);
            
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Error getting detailed stock info', [
                'product_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy thông tin stock'
            ], 500);
        }
    }

    /**
     * Lấy stock hiển thị cho cart (tính cả cart quantity)
     */
    public function getDisplayStock($id)
    {
        try {
            $cartService = new \App\Services\CartService();
            
            // Lấy sản phẩm
            $product = SanPham::with(['chitietsanpham'])->find($id);
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy sản phẩm'
                ], 404);
            }

            // Tính stock hiển thị cho sản phẩm chính (không trừ cart)
            $mainStock = $product->soLuong ?? 0;
            
            // Tính stock hiển thị cho variants (không trừ cart)
            $variantStock = $product->chitietsanpham->sum('soLuong');
            $variantDetails = [];
            
            foreach ($product->chitietsanpham as $variant) {
                $variantDetails[] = [
                    'id' => $variant->id,
                    'stock' => $variant->soLuong ?? 0,
                    'original_stock' => $variant->soLuong ?? 0
                ];
            }

            $totalStock = $mainStock + $variantStock;
            $totalRealStock = $totalStock;
            $cartQuantity = 0; // Không tính cart quantity ở đây

            Log::info('Display stock calculation:', [
                'product_id' => $id,
                'main_stock' => $mainStock,
                'variant_stock' => $variantStock,
                'total_stock' => $totalStock,
                'total_real_stock' => $totalRealStock,
                'cart_quantity' => $cartQuantity
            ]);

            return response()->json([
                'success' => true,
                'product_id' => $id,
                'mainStock' => $mainStock,
                'variantStock' => $variantStock,
                'totalStock' => $totalStock,
                'totalRealStock' => $totalRealStock,
                'cartQuantity' => $cartQuantity,
                'variantDetails' => $variantDetails
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting display stock', [
                'product_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy thông tin stock hiển thị'
            ], 500);
        }
    }

    /**
     * Lấy ảnh sản phẩm theo ID
     */
    public function getImages($id)
    {
        try {
            $product = SanPham::with(['hinhanh'])->find($id);
            
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy sản phẩm'
                ], 404);
            }

            $images = $product->hinhanh->map(function ($image) {
                return [
                    'id' => $image->id,
                    'url' => asset('storage/' . $image->url),
                    'is_default' => $image->is_default,
                    'mota' => $image->mota
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'product_id' => $id,
                    'product_name' => $product->tenSP,
                    'images' => $images,
                    'total_images' => $images->count()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting product images: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy ảnh sản phẩm'
            ], 500);
        }
    }
}
