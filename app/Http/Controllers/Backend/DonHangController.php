<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\DonHang;
use App\Models\ChiTietDonHang;
use App\Models\User;
use App\Models\SanPham;
use App\Models\ChiTietSanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use App\Services\DonHangService;

class DonHangController extends Controller
{
    protected $donHangService;

    public function __construct()
    {
        $this->donHangService = new DonHangService();
    }

    // Danh sách đơn hàng
    public function index(Request $request)
    {
        try {
            $filters = $request->only([
                'trangthai', 'from_date', 'to_date', 'user_id', 'search', 'per_page', 'voucher_filter'
            ]);
            // Mặc định 10; hỗ trợ 'all' để hiển thị tất cả
            if (!isset($filters['per_page']) || $filters['per_page'] === null || $filters['per_page'] === '') {
                $filters['per_page'] = 10;
            } elseif ((string)$filters['per_page'] === 'all') {
                $filters['per_page'] = 1000000; // hiển thị tất cả
            }

            $donhangs = $this->donHangService->getDonHangList($filters);
            $stats = $this->donHangService->getDonHangStats();
            $users = User::select('id', 'name', 'email')->get();

            return view('backend.donhang.index', [
                'donhangs' => $donhangs,
                'stats' => $stats,
                'users' => $users,
                'filters' => $filters,
                'trangThaiOptions' => DonHang::getTrangThaiOptions()
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy danh sách đơn hàng: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi tải danh sách đơn hàng');
        }
    }

    // Chi tiết đơn hàng
    public function show($id)
    {
        try {
            $donhang = $this->donHangService->getDonHangById($id);
            
            return view('backend.donhang.show', [
                'donhang' => $donhang,
                'trangThaiOptions' => DonHang::getTrangThaiOptions()
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi xem chi tiết đơn hàng: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Không thể tải chi tiết đơn hàng');
        }
    }

    // Cập nhật trạng thái đơn hàng
    public function updateTrangThai(Request $request, $id)
    {
        try {
            $request->validate([
                'trangthai' => 'required|string|in:' . implode(',', array_keys(DonHang::getTrangThaiOptions())),
                'nhanvien' => 'nullable|string|max:255'
            ]);

            $donhang = $this->donHangService->updateTrangThai(
                $id, 
                $request->trangthai, 
                $request->nhanvien ?? auth()->user()->name
            );

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái đơn hàng thành công',
                'donhang' => $donhang
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi cập nhật trạng thái đơn hàng: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật trạng thái đơn hàng'
            ], 500);
        }
    }

    // Xóa đơn hàng
    public function destroy($id)
    {
        try {
            $donhang = DonHang::findOrFail($id);
            
            // Kiểm tra quyền xóa (chỉ cho phép xóa đơn hàng đã hủy)
            if ($donhang->trangthai !== DonHang::TRANGTHAI_DA_HUY) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ có thể xóa đơn hàng đã hủy'
                ], 400);
            }

            // Xóa chi tiết đơn hàng trước
            ChiTietDonHang::where('id_donhang', $id)->delete();
            
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

    // Thống kê đơn hàng
    public function statistics()
    {
        try {
            // Lấy thống kê cơ bản trước
            $stats = $this->donHangService->getDonHangStats();
            
            // Khởi tạo dữ liệu mặc định
            $chartData = collect([]);
            $topCustomers = collect([]);
            $topProducts = collect([]);
            
            // Thử lấy dữ liệu chart
            try {
                $chartData = $this->donHangService->getDonHangChartData('month');
            } catch (\Exception $e) {
                Log::error('Lỗi getDonHangChartData: ' . $e->getMessage());
            }
            
            // Thử lấy top customers
            try {
                $topCustomers = $this->donHangService->getTopCustomers(5);
            } catch (\Exception $e) {
                Log::error('Lỗi getTopCustomers: ' . $e->getMessage());
            }
            
            // Thử lấy top products
            try {
                $topProducts = $this->donHangService->getTopProducts(5);
            } catch (\Exception $e) {
                Log::error('Lỗi getTopProducts: ' . $e->getMessage());
            }

            return view('backend.donhang.statistics', [
                'stats' => $stats,
                'chartData' => $chartData,
                'topCustomers' => $topCustomers,
                'topProducts' => $topProducts
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy thống kê đơn hàng: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Trả về view với dữ liệu mặc định
            return view('backend.donhang.statistics', [
                'stats' => [
                    'total_orders' => 0,
                    'pending_orders' => 0,
                    'confirmed_orders' => 0,
                    'shipping_orders' => 0,
                    'delivered_orders' => 0,
                    'cancelled_orders' => 0,
                    'today_revenue' => 0,
                    'month_revenue' => 0,
                    'total_revenue' => 0
                ],
                'chartData' => collect([]),
                'topCustomers' => collect([]),
                'topProducts' => collect([])
            ])->with('error', 'Có lỗi xảy ra khi tải thống kê: ' . $e->getMessage());
        }
    }

    // Xuất Excel đơn hàng
    public function export(Request $request)
    {
        try {
            $filters = $request->only([
                'trangthai', 'from_date', 'to_date', 'user_id'
            ]);

            $donhangs = $this->donHangService->getDonHangList($filters);
            
            // TODO: Implement Excel export
            return response()->json([
                'success' => true,
                'message' => 'Tính năng xuất Excel đang được phát triển'
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi xuất Excel đơn hàng: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xuất Excel'
            ], 500);
        }
    }

    // In hóa đơn
    public function print($id)
    {
        try {
            $donhang = $this->donHangService->getDonHangById($id);
            
            return view('backend.donhang.print', [
                'donhang' => $donhang
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi in hóa đơn: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Không thể in hóa đơn');
        }
    }

    // Lấy dữ liệu cho biểu đồ
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
                'message' => 'Có lỗi xảy ra khi tải dữ liệu biểu đồ'
            ], 500);
        }
    }
}
