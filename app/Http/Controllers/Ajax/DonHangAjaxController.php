<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\DonHang;
use App\Models\ChiTietDonHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use App\Services\DonHangService;

class DonHangAjaxController extends Controller
{
    protected $donHangService;

    public function __construct()
    {
        $this->donHangService = new DonHangService();
    }

    // Cập nhật trạng thái đơn hàng
    public function updateTrangThai(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|integer|exists:donhang,id',
                'trangthai' => 'required|string|in:' . implode(',', array_keys(DonHang::getTrangThaiOptions())),
                'nhanvien' => 'nullable|string|max:255'
            ]);

            $donhang = $this->donHangService->updateTrangThai(
                $request->id,
                $request->trangthai,
                $request->nhanvien ?? auth()->user()->name
            );

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái đơn hàng thành công',
                'data' => [
                    'id' => $donhang->id,
                    'trangthai' => $donhang->trangthai,
                    'trangthai_text' => $donhang->trang_thai_text,
                    'trangthai_badge_class' => $donhang->trang_thai_badge_class,
                    'nhanvien' => $donhang->nhanvien,
                    'ngaythanhtoan' => $donhang->ngaythanhtoan ? $donhang->ngaythanhtoan->format('d/m/Y H:i') : null
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi cập nhật trạng thái đơn hàng: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật trạng thái đơn hàng'
            ], 500);
        }
    }

    // Cập nhật trạng thái hàng loạt
    public function updateTrangThaiBulk(Request $request)
    {
        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'integer|exists:donhang,id',
                'trangthai' => 'required|string|in:' . implode(',', array_keys(DonHang::getTrangThaiOptions())),
                'nhanvien' => 'nullable|string|max:255'
            ]);

            $updatedCount = 0;
            $errors = [];

            foreach ($request->ids as $id) {
                try {
                    $this->donHangService->updateTrangThai(
                        $id,
                        $request->trangthai,
                        $request->nhanvien ?? auth()->user()->name
                    );
                    $updatedCount++;
                } catch (\Exception $e) {
                    $errors[] = "Đơn hàng #{$id}: " . $e->getMessage();
                }
            }

            if ($updatedCount > 0) {
                $message = "Đã cập nhật thành công {$updatedCount} đơn hàng";
                if (!empty($errors)) {
                    $message .= ". Có " . count($errors) . " đơn hàng gặp lỗi.";
                }

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => [
                        'updated_count' => $updatedCount,
                        'errors' => $errors
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể cập nhật đơn hàng nào'
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Lỗi khi cập nhật trạng thái hàng loạt: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật trạng thái hàng loạt'
            ], 500);
        }
    }

    // Xóa đơn hàng
    public function destroy(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|integer|exists:donhang,id'
            ]);

            $donhang = DonHang::findOrFail($request->id);
            
            // Kiểm tra quyền xóa
            if ($donhang->trangthai !== DonHang::TRANGTHAI_DA_HUY) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ có thể xóa đơn hàng đã hủy'
                ], 400);
            }

            // Xóa chi tiết đơn hàng trước
            ChiTietDonHang::where('id_donhang', $request->id)->delete();
            
            // Xóa đơn hàng
            $donhang->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa đơn hàng thành công'
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi xóa đơn hàng: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa đơn hàng'
            ], 500);
        }
    }

    // Lấy thông tin đơn hàng
    public function getDonHangInfo(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|integer|exists:donhang,id'
            ]);

            $donhang = $this->donHangService->getDonHangById($request->id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $donhang->id,
                    'user' => $donhang->user ? [
                        'id' => $donhang->user->id,
                        'name' => $donhang->user->name,
                        'email' => $donhang->user->email,
                        'phone' => $donhang->user->phone ?? 'N/A'
                    ] : null,
                    'trangthai' => $donhang->trangthai,
                    'trangthai_text' => $donhang->trang_thai_text,
                    'trangthai_badge_class' => $donhang->trang_thai_badge_class,
                    'ngaytao' => $donhang->ngaytao ? $donhang->ngaytao->format('d/m/Y H:i') : null,
                    'ngaythanhtoan' => $donhang->ngaythanhtoan ? $donhang->ngaythanhtoan->format('d/m/Y H:i') : null,
                    'nhanvien' => $donhang->nhanvien,
                    'tongtien' => $donhang->tongtien,
                    'tongtien_formatted' => $donhang->tong_tien_formatted,
                    'ghichu' => $donhang->ghichu,
                    'lichsutrangthai' => json_decode($donhang->lichsutrangthai ?? '[]', true),
                    'chi_tiet' => $donhang->chiTietDonHang->map(function($item) {
                        return [
                            'id' => $item->id,
                            'tensanpham' => $item->tensanpham,
                            'dongia' => $item->dongia,
                            'dongia_formatted' => $item->dongia_formatted,
                            'soluong' => $item->soluong,
                            'thanhtien' => $item->thanhtien,
                            'thanhtien_formatted' => $item->thanhtien_formatted,
                            'ghichu' => $item->ghichu
                        ];
                    })
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy thông tin đơn hàng: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy thông tin đơn hàng'
            ], 500);
        }
    }

    // Lấy thống kê đơn hàng
    public function getStats()
    {
        try {
            $stats = $this->donHangService->getDonHangStats();

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy thống kê đơn hàng: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy thống kê'
            ], 500);
        }
    }

    // Lấy dữ liệu biểu đồ
    public function getChartData(Request $request)
    {
        try {
            $period = $request->get('period', 'month');
            $chartData = $this->donHangService->getDonHangChartData($period);

            return response()->json([
                'success' => true,
                'data' => $chartData
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy dữ liệu biểu đồ: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy dữ liệu biểu đồ'
            ], 500);
        }
    }

    // Lấy top khách hàng
    public function getTopCustomers(Request $request)
    {
        try {
            $limit = $request->get('limit', 10);
            $topCustomers = $this->donHangService->getTopCustomers($limit);

            return response()->json([
                'success' => true,
                'data' => $topCustomers
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy top khách hàng: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy top khách hàng'
            ], 500);
        }
    }

    // Lấy top sản phẩm
    public function getTopProducts(Request $request)
    {
        try {
            $limit = $request->get('limit', 10);
            $topProducts = $this->donHangService->getTopProducts($limit);

            return response()->json([
                'success' => true,
                'data' => $topProducts
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy top sản phẩm: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy top sản phẩm'
            ], 500);
        }
    }
}
