<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\StatisticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StatisticsController extends Controller
{
    protected $statisticsService;

    public function __construct(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }

    /**
     * Hiển thị trang thống kê tổng quan
     */
    public function index(Request $request)
    {
        try {
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            
            $stats = $this->statisticsService->getOverviewStats($startDate, $endDate);
            $revenueChart = $this->statisticsService->getRevenueChartData($startDate, $endDate);
            $orderChart = $this->statisticsService->getOrderChartData();
            $topProducts = $this->statisticsService->getTopProducts(5, $startDate, $endDate);
            $topCustomers = $this->statisticsService->getTopCustomers(5, $startDate, $endDate);
            $orderStatusStats = $this->statisticsService->getOrderStatusStats();

            return view('backend.statistics.index', compact(
                'stats', 
                'revenueChart', 
                'orderChart', 
                'topProducts', 
                'topCustomers', 
                'orderStatusStats',
                'startDate',
                'endDate'
            ));
        } catch (\Exception $e) {
            Log::error('StatisticsController index error: ' . $e->getMessage());
            return view('backend.statistics.index', [
                'stats' => $this->getDefaultStats(),
                'revenueChart' => ['labels' => [], 'data' => []],
                'orderChart' => ['labels' => [], 'data' => []],
                'topProducts' => collect([]),
                'topCustomers' => collect([]),
                'orderStatusStats' => [],
                'startDate' => null,
                'endDate' => null
            ]);
        }
    }

    /**
     * API endpoint để lấy dữ liệu biểu đồ
     */
    public function getChartData(Request $request)
    {
        try {
            $type = $request->get('type', 'revenue');
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            
            switch ($type) {
                case 'revenue':
                    $data = $this->statisticsService->getRevenueChartData($startDate, $endDate);
                    break;
                case 'orders':
                    $data = $this->statisticsService->getOrderChartData();
                    break;
                default:
                    $data = ['labels' => [], 'data' => []];
            }
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            Log::error('StatisticsController getChartData error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải dữ liệu biểu đồ'
            ], 500);
        }
    }
    
    /**
     * API endpoint để lấy thống kê theo khoảng thời gian
     */
    public function getFilteredStats(Request $request)
    {
        try {
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            
            $stats = $this->statisticsService->getOverviewStats($startDate, $endDate);
            $revenueChart = $this->statisticsService->getRevenueChartData($startDate, $endDate);
            $topProducts = $this->statisticsService->getTopProducts(5, $startDate, $endDate);
            $topCustomers = $this->statisticsService->getTopCustomers(5, $startDate, $endDate);
            
            return response()->json([
                'success' => true,
                'stats' => $stats,
                'revenueChart' => $revenueChart,
                'topProducts' => $topProducts,
                'topCustomers' => $topCustomers
            ]);
        } catch (\Exception $e) {
            Log::error('StatisticsController getFilteredStats error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải dữ liệu thống kê'
            ], 500);
        }
    }

    /**
     * API endpoint để lấy top sản phẩm
     */
    public function getTopProducts(Request $request)
    {
        try {
            $limit = $request->get('limit', 10);
            $products = $this->statisticsService->getTopProducts($limit);
            
            return response()->json([
                'success' => true,
                'data' => $products
            ]);
        } catch (\Exception $e) {
            Log::error('StatisticsController getTopProducts error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải dữ liệu sản phẩm'
            ], 500);
        }
    }

    /**
     * API endpoint để lấy top khách hàng
     */
    public function getTopCustomers(Request $request)
    {
        try {
            $limit = $request->get('limit', 10);
            $customers = $this->statisticsService->getTopCustomers($limit);
            
            return response()->json([
                'success' => true,
                'data' => $customers
            ]);
        } catch (\Exception $e) {
            Log::error('StatisticsController getTopCustomers error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải dữ liệu khách hàng'
            ], 500);
        }
    }
    
    /**
     * Dữ liệu mặc định khi có lỗi
     */
    private function getDefaultStats()
    {
        return [
            'orders' => [
                'total_orders' => 0,
                'today_orders' => 0,
                'this_month_orders' => 0,
                'last_month_orders' => 0,
                'pending_orders' => 0,
                'delivered_orders' => 0,
                'cancelled_orders' => 0,
            ],
            'revenue' => [
                'total_revenue' => 0,
                'today_revenue' => 0,
                'this_month_revenue' => 0,
                'last_month_revenue' => 0,
            ],
            'users' => [
                'total_users' => 0,
                'active_users' => 0,
                'inactive_users' => 0,
                'admin_users' => 0,
                'new_users_today' => 0,
                'new_users_this_month' => 0,
            ],
            'products' => [
                'total_products' => 0,
                'active_products' => 0,
                'inactive_products' => 0,
                'total_categories' => 0,
                'active_categories' => 0,
                'inactive_categories' => 0,
            ],
            'growth' => [
                'order_growth' => 0,
                'revenue_growth' => 0,
                'user_growth' => 0,
            ],
        ];
    }
}
