<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use OpenApi\Annotations as OA;
use App\Http\Requests\Api\UserIndexRequest;
use App\Http\Requests\Api\UserStoreRequest;
use App\Http\Requests\Api\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\UserModel;
use App\Services\ApiUserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 */
class ApiUserController extends Controller
{
    protected $apiUserService;

    public function __construct(ApiUserService $apiUserService)
    {
        $this->apiUserService = $apiUserService;
    }

    /**
     * @OA\Get(
     *   path="/api/users",
     *   summary="Danh sách người dùng (có phân trang)",
     *   description="API này trả về danh sách người dùng với phân trang và các bộ lọc. Hỗ trợ tìm kiếm theo tên, email, số điện thoại.",
     *   operationId="getUsers",
     *   tags={"2. Users"},
     *   @OA\Parameter(name="search", in="query", @OA\Schema(type="string"), description="Tìm kiếm theo tên, email, số điện thoại"),
     *   @OA\Parameter(name="page", in="query", @OA\Schema(type="integer", minimum=1), description="Trang hiện tại"),
     *   @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", minimum=1, maximum=100), description="Số bản ghi mỗi trang"),
     *   @OA\Parameter(name="status", in="query", @OA\Schema(type="integer", enum={0,1}), description="Trạng thái: 0=không hoạt động, 1=hoạt động"),
     *   @OA\Parameter(name="role", in="query", @OA\Schema(type="integer", enum={1,2}), description="Vai trò: 1=Quản trị, 2=Cộng tác viên"),
     *   @OA\Response(
     *     response=200,
     *     description="Thành công",
     *     @OA\JsonContent(
     *       example={
     *         "data": {
     *           {
     *             "id": 1,
     *             "name": "Nguyễn Văn A",
     *             "email": "nguyenvana@example.com",
     *             "phone": "0123456789",
     *             "address": "123 Đường ABC, Quận 1, TP.HCM",
     *             "province_id": 79,
     *             "district_id": 761,
     *             "ward_id": 26782,
     *             "status": 1,
     *             "user_catalogue_id": 2,
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
     *         "message": "Lấy danh sách người dùng thành công"
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
    public function index(UserIndexRequest $request)
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

        // Validate search min length (giống Location)
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
            'role' => $validated['role'] ?? $validated['vai_tro'] ?? null,
            'sort_by' => $sortBy,
            'sort_dir' => $sortDir,
        ];

        // Trả toàn bộ nếu all=true
        if ($returnAll) {
            $result = $this->apiUserService->getUsers($filters, $perPage, true);
            $items = collect($result['data']);

            // Không tìm thấy theo từ khóa
            if ($keyword !== null && $items->count() === 0) {
                return response()->json([
                    'status_code' => 400,
                    'message' => 'Không tìm thấy người dùng nào',
                    'errors' => ['search' => ['Từ khóa "' . $keyword . '" không tìm thấy kết quả nào']],
                ], 400);
            }

            // Nếu đúng 1 kết quả và có search → 300 (Multiple Choices) giống Location
            if ($keyword !== null && $items->count() === 1) {
                $only = $items->first();
                return response()->json([
                    'status_code' => 300,
                    'message' => 'Yêu cầu chuyển hướng',
                    'redirect_url' => '/api/users/' . $only->id,
                    'suggestion' => 'Tìm thấy đúng 1 kết quả, có thể chuyển đến trang chi tiết',
                    'data' => UserResource::collection($items),
                ], 300);
            }

            return response()->json([
                'status_code' => 200,
                'data' => UserResource::collection($items),
                'pagination' => null,
                'message' => 'Lấy danh sách người dùng thành công',
            ]);
        }

        // Mặc định: phân trang thông qua Service
        $serviceResult = $this->apiUserService->getUsers($filters, $perPage, false);
        $itemsCollection = collect($serviceResult['data']);
        $pagination = $serviceResult['pagination'];
        $currentPage = $pagination['current_page'];
        $lastPage = $pagination['last_page'];
        $pages = $pagination['pages'];

        // Chỉ 200 + default (gom lỗi) khi người dùng thực thi. 300/400 sẽ hiển thị qua default khi xảy ra.

        return response()->json([
            'status_code' => 200,
            'data' => UserResource::collection($itemsCollection),
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
            'message' => 'Lấy danh sách người dùng thành công',
        ]);
    }

    /**
     * @OA\Post(
     *   path="/api/users",
     *   summary="Tạo mới người dùng",
     *   tags={"2. Users"},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       example={
     *         "name": "Nguyễn Văn A",
     *         "email": "a@example.com",
     *         "password": "matkhau@123",
     *         "phone": "0901234567",
     *         "image": "avatar.jpg",
     *         "address": "123 Đường ABC",
     *         "province_id": 79,
     *         "district_id": 760,
     *         "ward_id": 26734,
     *         "birthday": "2000-01-01",
     *         "description": "Ghi chú",
     *         "status": 1,
     *         "user_catalogue_id": 2
     *       }
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Tạo mới thành công",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="data", ref="#/components/schemas/User"),
     *       @OA\Property(property="message", type="string", example="Tạo người dùng thành công")
     *     )
     *   ),
     *
     *   @OA\Response(
     *     response="default",
     *     description="Lỗi",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status_code", type="integer", example=409),
     *       @OA\Property(property="message", type="string", example="Không thể xóa người dùng vì đang được tham chiếu ở dữ liệu khác (ví dụ: đơn hàng)."),
     *       @OA\Property(property="errors", type="object")
     *     )
     *   )
     * )
     */
    public function store(UserStoreRequest $request)
    {
        try {
            $data = $request->validated();
            $user = $this->apiUserService->createUser($data);
            return response()->json([
                'status_code' => 200,
                'data' => new UserResource($user),
                'message' => 'Tạo người dùng thành công',
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status_code' => 422,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Illuminate\Database\QueryException $e) {
            // Lỗi trùng email/unique sẽ rơi vào đây nếu không do FormRequest bắt trước
            return response()->json([
                'status_code' => 400,
                'message' => 'Email đã tồn tại hoặc dữ liệu không hợp lệ',
                'errors' => ['email' => ['Email đã tồn tại']],
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
     *   path="/api/users/{id}",
     *   summary="Chi tiết người dùng",
     *   tags={"2. Users"},
     *   @OA\Parameter(name="id", in="path", required=true, description="ID người dùng", @OA\Schema(type="integer")),
     *   @OA\Response(
     *     response=200,
     *     description="Thành công",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="data", ref="#/components/schemas/User"),
     *       @OA\Property(property="message", type="string", example="Lấy thông tin người dùng thành công")
     *     )
     *   ),
     *
     *   @OA\Response(
     *     response="default",
     *     description="Lỗi",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status_code", type="integer", example=409),
     *       @OA\Property(property="message", type="string", example="Không thể xóa người dùng vì đang được tham chiếu"),
     *       @OA\Property(property="errors", type="object")
     *     )
     *   )
     * )
     */
    public function show($id)
    {
        $user = $this->apiUserService->getUserById((int) $id);
        if (!$user) {
            return response()->json([
                'status_code' => 404,
                'message' => 'Không tìm thấy người dùng',
                'errors' => ['id' => ['Người dùng không tồn tại']],
            ], 404);
        }
        return response()->json([
            'status_code' => 200,
            'data' => new UserResource($user),
            'message' => 'Lấy thông tin người dùng thành công',
        ]);
    }

    /**
     * @OA\Put(
     *   path="/api/users/{id}",
     *   summary="Cập nhật người dùng",
     *   tags={"2. Users"},
     *   @OA\Parameter(name="id", in="path", required=true, description="ID người dùng", @OA\Schema(type="integer")),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\MediaType(
     *       mediaType="application/json",
     *       @OA\Schema(
     *         type="object",
     *         required={"name","email","password","user_catalogue_id"},
     *         @OA\Property(property="name", type="string"),
     *         @OA\Property(property="email", type="string", format="email"),
     *         @OA\Property(property="password", type="string", minLength=6),
     *         @OA\Property(property="phone", type="string"),
     *         @OA\Property(property="image", type="string", description="Cho phép tên file; API sẽ build URL"),
     *         @OA\Property(property="address", type="string"),
     *         @OA\Property(property="province_id", type="integer"),
     *         @OA\Property(property="district_id", type="integer"),
     *         @OA\Property(property="ward_id", type="integer"),
     *         @OA\Property(property="birthday", type="string", format="date"),
     *         @OA\Property(property="description", type="string"),
     *         @OA\Property(property="status", type="integer", enum={0,1}),
     *         @OA\Property(property="user_catalogue_id", type="integer", enum={1,2})
     *       ),
     *       example={
     *         "name": "Nguyễn Văn Example",
     *         "email": "user@example.com",
     *         "password": "Matkhau@123",
     *         "phone": "0379559690",
     *         "image": "avatar.jpg",
     *         "address": "Quận 12, Hồ Chí Minh",
     *         "province_id": 82,
     *         "district_id": 824,
     *         "ward_id": 28735,
     *         "birthday": "2002-10-16",
     *         "description": "Thêm mô tả",
     *         "status": 0,
     *         "user_catalogue_id": 1
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
     *           "id": 88,
     *           "name": "Đỗ Văn Nam",
     *           "email": "nam.do@gmail.com",
     *           "phone": "0905123456",
     *           "image": "http://localhost/duan/duan/duantotnghiep/public/uploads/avatars/1755724419_68a63a836c6f1.jpg",
     *           "status": 1,
     *           "role": 2,
     *           "province_id": 48,
     *           "district_id": 492,
     *           "ward_id": 20233,
     *           "address": "67 Bạch Đằng",
     *           "birthday": "1997-07-12 00:00:00",
     *           "description": "Thành viên mới",
     *           "created_at": "2025-08-20T21:13:39.000000Z",
     *           "updated_at": "2025-08-20T21:13:39.000000Z"
     *         },
     *         "message": "Cập nhật người dùng thành công"
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
    public function update(UserUpdateRequest $request, $id)
    {
        try {
            $data = $request->validated();
            $updatedUser = $this->apiUserService->updateUser((int) $id, $data);
            if (!$updatedUser) {
                return response()->json([
                    'status_code' => 404,
                    'message' => 'Không tìm thấy người dùng',
                    'errors' => ['id' => ['Người dùng không tồn tại']],
                ], 404);
            }
            return response()->json([
                'status_code' => 200,
                'data' => new UserResource($updatedUser),
                'message' => 'Cập nhật người dùng thành công',
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
                'message' => 'Email đã tồn tại hoặc dữ liệu không hợp lệ',
                'errors' => ['email' => ['Email đã tồn tại']],
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
     *   path="/api/users/{id}",
     *   summary="Xóa người dùng",
     *   tags={"2. Users"},
     *   @OA\Parameter(name="id", in="path", required=true, description="ID người dùng", @OA\Schema(type="integer")),
     *   @OA\Response(
     *     response=200,
     *     description="Xóa thành công",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="message", type="string", example="Xóa người dùng thành công")
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
        try {
            $deleted = $this->apiUserService->deleteUser((int) $id);
            if (!$deleted) {
                return response()->json([
                    'status_code' => 404,
                    'message' => 'Không tìm thấy người dùng',
                    'errors' => ['id' => ['Người dùng không tồn tại']],
                ], 404);
            }
            return response()->json([
                'status_code' => 200,
                'message' => 'Xóa người dùng thành công',
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            // Lỗi ràng buộc FK: người dùng đã có đơn hàng/quan hệ liên quan
            return response()->json([
                'status_code' => 409,
                'message' => 'Không thể xóa người dùng vì đang được tham chiếu ở dữ liệu khác (ví dụ: đơn hàng).',
                'errors' => ['constraint' => ['Vui lòng xóa/thu hồi dữ liệu liên quan trước, hoặc vô hiệu hóa tài khoản.']],
            ], 409);
        } catch (\Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Lỗi hệ thống',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
