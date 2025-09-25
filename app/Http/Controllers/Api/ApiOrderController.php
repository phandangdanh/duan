<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use OpenApi\Annotations as OA;
use App\Http\Requests\Api\OrderIndexRequest;
use App\Http\Requests\Api\OrderStoreRequest;
use App\Http\Requests\Api\OrderUpdateRequest;
use App\Http\Resources\OrderResource;
use App\Services\ApiOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 */
class ApiOrderController extends Controller
{
    protected $apiOrderService;

    public function __construct(ApiOrderService $apiOrderService)
    {
        $this->apiOrderService = $apiOrderService;
    }

    /**
     * @OA\Get(
     *   path="/api/orders",
     *   summary="Danh sách đơn hàng (có phân trang)",
     *   description="API này trả về danh sách đơn hàng với phân trang và các bộ lọc. Hỗ trợ tìm kiếm theo mã đơn hàng, tên khách hàng, trạng thái.",
     *   operationId="getOrders",
     *   tags={"5. Orders"},
     *   @OA\Parameter(name="search", in="query", @OA\Schema(type="string"), description="Tìm kiếm theo mã đơn hàng, tên khách hàng"),
     *   @OA\Parameter(name="page", in="query", @OA\Schema(type="integer", minimum=1), description="Trang hiện tại"),
     *   @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", minimum=1, maximum=100), description="Số bản ghi mỗi trang"),
     *   @OA\Parameter(name="status", in="query", @OA\Schema(type="string", enum={"cho_xac_nhan","da_xac_nhan","dang_giao","da_giao","da_huy","hoan_tra"}), description="Trạng thái đơn hàng"),
     *   @OA\Parameter(name="user_id", in="query", @OA\Schema(type="integer"), description="ID khách hàng"),
     *   @OA\Parameter(name="date_from", in="query", @OA\Schema(type="string", format="date"), description="Từ ngày (YYYY-MM-DD)"),
     *   @OA\Parameter(name="date_to", in="query", @OA\Schema(type="string", format="date"), description="Đến ngày (YYYY-MM-DD)"),
     *   @OA\Parameter(name="min_amount", in="query", @OA\Schema(type="number"), description="Số tiền tối thiểu"),
     *   @OA\Parameter(name="max_amount", in="query", @OA\Schema(type="number"), description="Số tiền tối đa"),
     *   @OA\Response(
     *     response=200,
     *     description="Thành công",
     *     @OA\JsonContent(
     *       example={
     *         "data": {
     *           {
     *             "id": 1,
     *             "id_user": 1,
     *             "trangthai": "cho_xac_nhan",
     *             "trangthai_text": "Chờ xác nhận",
     *             "ngaytao": "2024-01-15T10:30:00.000000Z",
     *             "ngaythanhtoan": null,
     *             "tongtien": 500000.00,
     *             "tongtien_formatted": "500.000 VNĐ",
     *             "hoten": "Nguyễn Văn A",
     *             "email": "nguyenvana@email.com",
     *             "sodienthoai": "0123456789",
     *             "diachigiaohang": "123 Đường ABC, Quận 1, TP.HCM",
     *             "phuongthucthanhtoan": "cod",
     *             "trangthaithanhtoan": "chua_thanh_toan",
     *             "ghichu": "Giao hàng vào buổi chiều",
     *             "user": {
     *               "id": 1,
     *               "name": "Nguyễn Văn A",
     *               "email": "nguyenvana@email.com"
     *             },
     *             "chi_tiet_don_hang": {
     *               {
     *                 "id": 1,
     *                 "tensanpham": "Áo thun nam",
     *                 "dongia": 250000.00,
     *                 "soluong": 2,
     *                 "thanhtien": 500000.00,
     *                 "chi_tiet_san_pham": {
     *                   "id": 1,
     *                   "tenSp": "Áo thun nam - Đỏ - M",
     *                   "gia": 250000.00,
     *                   "gia_khuyenmai": 200000.00,
     *                   "mausac": {
     *                     "id": 1,
     *                     "tenMau": "Đỏ"
     *                   },
     *                   "size": {
     *                     "id": 1,
     *                     "tenSize": "M"
     *                   }
     *                 }
     *               }
     *             },
     *             "vouchers": {
     *               {
     *                 "id": 1,
     *                 "ma_voucher": "GIAM10",
     *                 "ten_voucher": "Giảm 10%",
     *                 "gia_tri": 10.00,
     *                 "loai_giam_gia": "phan_tram"
     *               }
     *             }
     *           }
     *         },
     *         "pagination": {
     *           "current_page": 1,
     *           "per_page": 10,
     *           "total": 25,
     *           "last_page": 3,
     *           "prev_page": null,
     *           "next_page": 2,
     *           "prev_url": null,
     *           "next_url": "http://localhost/duan/duan/duantotnghiep/public/api/orders?page=2"
     *         }
     *       }
     *     )
     *   ),
     *   @OA\Response(response=400, description="Lỗi validation hoặc không tìm thấy kết quả"),
     *   @OA\Response(response=422, description="Dữ liệu không hợp lệ"),
     *   @OA\Response(response=500, description="Lỗi server")
     * )
     */
    public function index(OrderIndexRequest $request)
    {
        try {
            $filters = $request->validated();
            $perPage = $request->input('per_page', 10);
            
            $result = $this->apiOrderService->getOrders($filters, $perPage);
            
            // Kiểm tra nếu có search keyword nhưng không có kết quả
            if (isset($filters['search']) && $filters['search'] !== null && 
                $result['pagination']['total'] === 0) {
                return response()->json([
                    'status_code' => 400,
                    'message' => 'Không tìm thấy đơn hàng nào với từ khóa: ' . $filters['search'],
                    'data' => [],
                    'pagination' => $result['pagination']
                ], 400);
            }
            
            return response()->json([
                'status_code' => 200,
                'message' => 'Lấy danh sách đơn hàng thành công',
                'data' => $result['data'],
                'pagination' => $result['pagination']
            ]);
            
        } catch (\Exception $e) {
            Log::error('ApiOrderController@index: ' . $e->getMessage());
            return response()->json([
                'status_code' => 500,
                'message' => 'Lỗi server khi lấy danh sách đơn hàng',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *   path="/api/orders",
     *   summary="Tạo đơn hàng mới",
     *   description="API này tạo đơn hàng mới với các sản phẩm và voucher (nếu có)",
     *   operationId="createOrder",
     *   tags={"5. Orders"},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"id_user","chi_tiet_don_hang","hoten","email","sodienthoai","diachigiaohang"},
     *       @OA\Property(property="id_user", type="integer", description="ID khách hàng"),
     *       @OA\Property(property="hoten", type="string", description="Họ tên người nhận"),
     *       @OA\Property(property="email", type="string", format="email", description="Email người nhận"),
     *       @OA\Property(property="sodienthoai", type="string", description="Số điện thoại người nhận"),
     *       @OA\Property(property="diachigiaohang", type="string", description="Địa chỉ giao hàng"),
     *       @OA\Property(property="phuongthucthanhtoan", type="string", enum={"cod","banking","momo","zalopay"}, description="Phương thức thanh toán"),
     *       @OA\Property(property="ghichu", type="string", description="Ghi chú đơn hàng"),
     *       @OA\Property(
     *         property="chi_tiet_don_hang",
     *         type="array",
     *         description="Chi tiết sản phẩm trong đơn hàng",
     *         @OA\Items(
     *           @OA\Property(property="id_chitietsanpham", type="integer", description="ID chi tiết sản phẩm"),
     *           @OA\Property(property="soluong", type="integer", minimum=1, description="Số lượng"),
     *           @OA\Property(property="dongia", type="number", description="Đơn giá (tùy chọn, lấy từ chi tiết sản phẩm nếu không có)"),
     *           @OA\Property(property="ghichu", type="string", description="Ghi chú cho sản phẩm này")
     *         )
     *       ),
     *       @OA\Property(
     *         property="vouchers",
     *         type="array",
     *         description="Danh sách voucher áp dụng (tùy chọn)",
     *         @OA\Items(
     *           @OA\Property(property="id_voucher", type="integer", description="ID voucher")
     *         )
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Tạo đơn hàng thành công",
     *     @OA\JsonContent(
     *       example={
     *         "status_code": 201,
     *         "message": "Tạo đơn hàng thành công",
     *         "data": {
     *           "id": 1,
     *           "id_user": 1,
     *           "trangthai": "cho_xac_nhan",
     *           "ngaytao": "2024-01-15T10:30:00.000000Z",
     *           "tongtien": 500000.00,
     *           "hoten": "Nguyễn Văn A",
     *           "email": "nguyenvana@email.com"
     *         }
     *       }
     *     )
     *   ),
     *   @OA\Response(response=422, description="Dữ liệu không hợp lệ"),
     *   @OA\Response(response=500, description="Lỗi server")
     * )
     */
    public function store(OrderStoreRequest $request)
    {
        try {
            $validated = $request->validated();
            
            $order = $this->apiOrderService->createOrder($validated);
            
            return response()->json([
                'status_code' => 201,
                'message' => 'Tạo đơn hàng thành công',
                'data' => new OrderResource($order)
            ], 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status_code' => 422,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('ApiOrderController@store: ' . $e->getMessage());
            return response()->json([
                'status_code' => 500,
                'message' => 'Lỗi server khi tạo đơn hàng',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *   path="/api/orders/{id}",
     *   summary="Chi tiết đơn hàng",
     *   description="API này trả về thông tin chi tiết của một đơn hàng",
     *   operationId="getOrder",
     *   tags={"5. Orders"},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), description="ID đơn hàng"),
     *   @OA\Response(
     *     response=200,
     *     description="Thành công",
     *     @OA\JsonContent(
     *       example={
     *         "status_code": 200,
     *         "message": "Lấy thông tin đơn hàng thành công",
     *         "data": {
     *           "id": 1,
     *           "id_user": 1,
     *           "trangthai": "cho_xac_nhan",
     *           "trangthai_text": "Chờ xác nhận",
     *           "ngaytao": "2024-01-15T10:30:00.000000Z",
     *           "ngaythanhtoan": null,
     *           "tongtien": 500000.00,
     *           "tongtien_formatted": "500.000 VNĐ",
     *           "hoten": "Nguyễn Văn A",
     *           "email": "nguyenvana@email.com",
     *           "sodienthoai": "0123456789",
     *           "diachigiaohang": "123 Đường ABC, Quận 1, TP.HCM",
     *           "phuongthucthanhtoan": "cod",
     *           "trangthaithanhtoan": "chua_thanh_toan",
     *           "ghichu": "Giao hàng vào buổi chiều",
     *           "user": {
     *             "id": 1,
     *             "name": "Nguyễn Văn A",
     *             "email": "nguyenvana@email.com"
     *           },
     *           "chi_tiet_don_hang": {
     *             {
     *               "id": 1,
     *               "tensanpham": "Áo thun nam",
     *               "dongia": 250000.00,
     *               "soluong": 2,
     *               "thanhtien": 500000.00,
     *               "chi_tiet_san_pham": {
     *                 "id": 1,
     *                 "tenSp": "Áo thun nam - Đỏ - M",
     *                 "gia": 250000.00,
     *                 "gia_khuyenmai": 200000.00,
     *                 "mausac": {
     *                   "id": 1,
     *                   "tenMau": "Đỏ"
     *                 },
     *                 "size": {
     *                   "id": 1,
     *                   "tenSize": "M"
     *                 }
     *               }
     *             }
     *           },
     *           "vouchers": {
     *             {
     *               "id": 1,
     *               "ma_voucher": "GIAM10",
     *               "ten_voucher": "Giảm 10%",
     *               "gia_tri": 10.00,
     *               "loai_giam_gia": "phan_tram"
     *             }
     *           }
     *         }
     *       }
     *     )
     *   ),
     *   @OA\Response(response=404, description="Không tìm thấy đơn hàng"),
     *   @OA\Response(response=422, description="ID không hợp lệ"),
     *   @OA\Response(response=500, description="Lỗi server")
     * )
     */
    public function show(Request $request, $id)
    {
        try {
            // Validate ID parameter
            if (!is_numeric($id) || $id <= 0) {
                return response()->json([
                    'status_code' => 422,
                    'message' => 'ID đơn hàng phải là số nguyên dương',
                    'errors' => ['id' => ['ID đơn hàng phải là số nguyên dương']]
                ], 422);
            }

            $order = $this->apiOrderService->getOrderById((int)$id);
            
            if (!$order) {
                return response()->json([
                    'status_code' => 404,
                    'message' => 'Không tìm thấy đơn hàng với ID: ' . $id
                ], 404);
            }
            
            return response()->json([
                'status_code' => 200,
                'message' => 'Lấy thông tin đơn hàng thành công',
                'data' => new OrderResource($order)
            ]);
            
        } catch (\Exception $e) {
            Log::error('ApiOrderController@show: ' . $e->getMessage());
            return response()->json([
                'status_code' => 500,
                'message' => 'Lỗi server khi lấy thông tin đơn hàng',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *   path="/api/orders/{id}",
     *   summary="Cập nhật đơn hàng",
     *   description="API này cập nhật thông tin đơn hàng (chủ yếu là trạng thái và thông tin giao hàng)",
     *   operationId="updateOrder",
     *   tags={"5. Orders"},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), description="ID đơn hàng"),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       @OA\Property(property="trangthai", type="string", enum={"cho_xac_nhan","da_xac_nhan","dang_giao","da_giao","da_huy","hoan_tra"}, description="Trạng thái đơn hàng"),
     *       @OA\Property(property="hoten", type="string", description="Họ tên người nhận"),
     *       @OA\Property(property="email", type="string", format="email", description="Email người nhận"),
     *       @OA\Property(property="sodienthoai", type="string", description="Số điện thoại người nhận"),
     *       @OA\Property(property="diachigiaohang", type="string", description="Địa chỉ giao hàng"),
     *       @OA\Property(property="phuongthucthanhtoan", type="string", enum={"cod","banking","momo","zalopay"}, description="Phương thức thanh toán"),
     *       @OA\Property(property="trangthaithanhtoan", type="string", enum={"chua_thanh_toan","da_thanh_toan","hoan_tien"}, description="Trạng thái thanh toán"),
     *       @OA\Property(property="ghichu", type="string", description="Ghi chú đơn hàng"),
     *       @OA\Property(property="nhanvien", type="string", description="Tên nhân viên xử lý")
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Cập nhật đơn hàng thành công",
     *     @OA\JsonContent(
     *       example={
     *         "status_code": 200,
     *         "message": "Cập nhật đơn hàng thành công",
     *         "data": {
     *           "id": 1,
     *           "trangthai": "da_xac_nhan",
     *           "trangthai_text": "Đã xác nhận",
     *           "ngaytao": "2024-01-15T10:30:00.000000Z",
     *           "tongtien": 500000.00
     *         }
     *       }
     *     )
     *   ),
     *   @OA\Response(response=404, description="Không tìm thấy đơn hàng"),
     *   @OA\Response(response=422, description="Dữ liệu không hợp lệ"),
     *   @OA\Response(response=500, description="Lỗi server")
     * )
     */
    public function update(OrderUpdateRequest $request, $id)
    {
        try {
            // Validate ID parameter
            if (!is_numeric($id) || $id <= 0) {
                return response()->json([
                    'status_code' => 422,
                    'message' => 'ID đơn hàng phải là số nguyên dương',
                    'errors' => ['id' => ['ID đơn hàng phải là số nguyên dương']]
                ], 422);
            }

            $validated = $request->validated();
            
            $order = $this->apiOrderService->updateOrder((int)$id, $validated);
            
            if (!$order) {
                return response()->json([
                    'status_code' => 404,
                    'message' => 'Không tìm thấy đơn hàng với ID: ' . $id
                ], 404);
            }
            
            return response()->json([
                'status_code' => 200,
                'message' => 'Cập nhật đơn hàng thành công',
                'data' => new OrderResource($order)
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status_code' => 422,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('ApiOrderController@update: ' . $e->getMessage());
            return response()->json([
                'status_code' => 500,
                'message' => 'Lỗi server khi cập nhật đơn hàng',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *   path="/api/orders/{id}",
     *   summary="Xóa đơn hàng",
     *   description="API này xóa đơn hàng (soft delete)",
     *   operationId="deleteOrder",
     *   tags={"5. Orders"},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), description="ID đơn hàng"),
     *   @OA\Response(
     *     response=200,
     *     description="Xóa đơn hàng thành công",
     *     @OA\JsonContent(
     *       example={
     *         "status_code": 200,
     *         "message": "Xóa đơn hàng thành công"
     *       }
     *     )
     *   ),
     *   @OA\Response(response=404, description="Không tìm thấy đơn hàng"),
     *   @OA\Response(response=422, description="ID không hợp lệ"),
     *   @OA\Response(response=500, description="Lỗi server")
     * )
     */
    public function destroy(Request $request, $id)
    {
        try {
            // Validate ID parameter
            if (!is_numeric($id) || $id <= 0) {
                return response()->json([
                    'status_code' => 422,
                    'message' => 'ID đơn hàng phải là số nguyên dương',
                    'errors' => ['id' => ['ID đơn hàng phải là số nguyên dương']]
                ], 422);
            }

            $result = $this->apiOrderService->deleteOrder((int)$id);
            
            if (!$result) {
                return response()->json([
                    'status_code' => 404,
                    'message' => 'Không tìm thấy đơn hàng với ID: ' . $id
                ], 404);
            }
            
            return response()->json([
                'status_code' => 200,
                'message' => 'Xóa đơn hàng thành công'
            ]);
            
        } catch (\Exception $e) {
            Log::error('ApiOrderController@destroy: ' . $e->getMessage());
            return response()->json([
                'status_code' => 500,
                'message' => 'Lỗi server khi xóa đơn hàng',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hủy đơn hàng (chỉ user sở hữu đơn hàng)
     */
    public function cancel(Request $request, $id)
    {
        try {
            $user = $request->user();
            
            $order = $this->apiOrderService->getOrderById((int)$id);
            
            if (!$order) {
                return response()->json([
                    'status_code' => 404,
                    'message' => 'Không tìm thấy đơn hàng với ID: ' . $id
                ], 404);
            }

            // Kiểm tra quyền sở hữu
            if ($order->id_user !== $user->id) {
                return response()->json([
                    'status_code' => 403,
                    'message' => 'Bạn không có quyền hủy đơn hàng này'
                ], 403);
            }

            // Chỉ cho phép hủy đơn hàng ở trạng thái chờ xác nhận hoặc đã xác nhận
            if (!in_array($order->trangthai, ['cho_xac_nhan', 'da_xac_nhan'])) {
                return response()->json([
                    'status_code' => 400,
                    'message' => 'Không thể hủy đơn hàng ở trạng thái này'
                ], 400);
            }

            $result = $this->apiOrderService->cancelOrder((int)$id);
            
            return response()->json([
                'status_code' => 200,
                'message' => 'Đơn hàng đã được hủy thành công'
            ]);
            
        } catch (\Exception $e) {
            Log::error('ApiOrderController@cancel: ' . $e->getMessage());
            return response()->json([
                'status_code' => 500,
                'message' => 'Lỗi server khi hủy đơn hàng',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mua lại đơn hàng (thêm vào giỏ hàng)
     */
    public function reorder(Request $request, $id)
    {
        try {
            $user = $request->user();
            
            $order = $this->apiOrderService->getOrderById((int)$id);
            
            if (!$order) {
                return response()->json([
                    'status_code' => 404,
                    'message' => 'Không tìm thấy đơn hàng với ID: ' . $id
                ], 404);
            }

            // Kiểm tra quyền sở hữu
            if ($order->id_user !== $user->id) {
                return response()->json([
                    'status_code' => 403,
                    'message' => 'Bạn không có quyền mua lại đơn hàng này'
                ], 403);
            }

            $result = $this->apiOrderService->reorderOrder((int)$id, $user->id);
            
            return response()->json([
                'status_code' => 200,
                'message' => 'Đã thêm sản phẩm vào giỏ hàng',
                'data' => $result
            ]);
            
        } catch (\Exception $e) {
            Log::error('ApiOrderController@reorder: ' . $e->getMessage());
            return response()->json([
                'status_code' => 500,
                'message' => 'Lỗi server khi mua lại đơn hàng',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy đơn hàng của user hiện tại
     */
    public function myOrders(Request $request)
    {
        try {
            $user = $request->user();
            $perPage = $request->input('per_page', 10);
            
            $result = $this->apiOrderService->getUserOrders($user->id, $perPage);
            
            return response()->json([
                'status_code' => 200,
                'message' => 'Lấy danh sách đơn hàng thành công',
                'data' => $result['data'],
                'pagination' => $result['pagination']
            ]);
            
        } catch (\Exception $e) {
            Log::error('ApiOrderController@myOrders: ' . $e->getMessage());
            return response()->json([
                'status_code' => 500,
                'message' => 'Lỗi server khi lấy danh sách đơn hàng',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lịch sử đơn hàng của user
     */
    public function orderHistory(Request $request)
    {
        try {
            $user = $request->user();
            $perPage = $request->input('per_page', 20);
            $status = $request->input('status');
            
            $result = $this->apiOrderService->getUserOrderHistory($user->id, $perPage, $status);
            
            return response()->json([
                'status_code' => 200,
                'message' => 'Lấy lịch sử đơn hàng thành công',
                'data' => $result['data'],
                'pagination' => $result['pagination']
            ]);
            
        } catch (\Exception $e) {
            Log::error('ApiOrderController@orderHistory: ' . $e->getMessage());
            return response()->json([
                'status_code' => 500,
                'message' => 'Lỗi server khi lấy lịch sử đơn hàng',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật trạng thái đơn hàng (chỉ admin)
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'trangthai' => 'required|string|in:cho_xac_nhan,da_xac_nhan,dang_giao,da_giao,da_huy,hoan_tra',
                'ghichu' => 'nullable|string|max:500'
            ]);

            $order = $this->apiOrderService->updateOrderStatus((int)$id, $validated);
            
            if (!$order) {
                return response()->json([
                    'status_code' => 404,
                    'message' => 'Không tìm thấy đơn hàng với ID: ' . $id
                ], 404);
            }
            
            return response()->json([
                'status_code' => 200,
                'message' => 'Cập nhật trạng thái đơn hàng thành công',
                'data' => new OrderResource($order)
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status_code' => 422,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('ApiOrderController@updateStatus: ' . $e->getMessage());
            return response()->json([
                'status_code' => 500,
                'message' => 'Lỗi server khi cập nhật trạng thái đơn hàng',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kiểm tra trạng thái thanh toán (public - không cần đăng nhập)
     */
    public function checkPayment(Request $request)
    {
        try {
            $orderId = $request->input('order_id');
            $phone = $request->input('phone');
            
            if (!$orderId || !$phone) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng nhập đầy đủ mã đơn hàng và số điện thoại'
                ]);
            }
            
            // Find order by ID and phone
            $order = \App\Models\DonHang::where('id', $orderId)
                                        ->where('sodienthoai', $phone)
                                        ->first();
            
            if (!$order) {
                // Debug: Check if order exists with different phone
                $orderById = \App\Models\DonHang::where('id', $orderId)->first();
                
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy đơn hàng với thông tin đã nhập',
                    'debug' => [
                        'searched_order_id' => $orderId,
                        'searched_phone' => $phone,
                        'order_exists' => $orderById ? true : false,
                        'actual_phone' => $orderById ? $orderById->sodienthoai : null,
                        'suggestion' => $orderById ? "Thử với số điện thoại: " . $orderById->sodienthoai : "Đơn hàng không tồn tại"
                    ]
                ]);
            }
            
            // Return order data with payment status
            return response()->json([
                'success' => true,
                'order' => [
                    'id' => $order->id,
                    'tongtien' => $order->tongtien,
                    'status' => $order->trangthaithanhtoan ?? 0, // 0=chưa, 1=thành công, 2=thất bại
                    'ngaytao' => $order->created_at ? $order->created_at->toISOString() : null,
                    'tenkhachhang' => $order->hoten,
                    'sdt' => $order->sodienthoai,
                    'diachi' => $order->diachigiaohang,
                    'payment_method' => $order->phuongthucthanhtoan,
                    'transaction_id' => $order->transaction_id ?? null,
                    'payment_time' => $order->payment_time ? (is_string($order->payment_time) ? $order->payment_time : $order->payment_time->toISOString()) : null
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('ApiOrderController@checkPayment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi kiểm tra trạng thái thanh toán',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
