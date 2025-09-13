<?php

namespace App\Services;

use App\Models\DonHang;
use App\Models\UserModel;
use App\Models\SanPham;
use App\Models\DanhMuc;
use App\Models\ChiTietDonHang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class StatisticsService
{
    /**
     * Lấy thống kê tổng quan
     */
    public function getOverviewStats($startDate = null, $endDate = null)
    {
        try {
            $today = Carbon::today();
            $thisMonth = Carbon::now()->startOfMonth();
            $lastMonth = Carbon::now()->subMonth()->startOfMonth();
            $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();
            
            // Nếu có filter theo ngày
            if ($startDate && $endDate) {
                $startDate = Carbon::parse($startDate)->startOfDay();
                $endDate = Carbon::parse($endDate)->endOfDay();
            }

            // Thống kê đơn hàng
            $orderQuery = DonHang::query();
            if ($startDate && $endDate) {
                $orderQuery->whereBetween('ngaytao', [$startDate, $endDate]);
            }
            
            $orderStats = [
                'total_orders' => $startDate && $endDate ? $orderQuery->count() : DonHang::count(),
                'today_orders' => DonHang::whereDate('ngaytao', $today)->count(),
                'this_month_orders' => DonHang::where('ngaytao', '>=', $thisMonth)->count(),
                'last_month_orders' => DonHang::whereBetween('ngaytao', [$lastMonth, $lastMonthEnd])->count(),
                'pending_orders' => (clone $orderQuery)->whereIn('trangthai', ['cho_xac_nhan', 'da_xac_nhan'])->count(),
                'delivered_orders' => (clone $orderQuery)->where('trangthai', 'da_giao')->count(),
                'cancelled_orders' => (clone $orderQuery)->where('trangthai', 'da_huy')->count(),
            ];

            // Thống kê doanh thu
            $revenueQuery = DonHang::where('trangthai', 'da_giao');
            if ($startDate && $endDate) {
                $revenueQuery->whereBetween('ngaytao', [$startDate, $endDate]);
            }
            
            $revenueStats = [
                'total_revenue' => $startDate && $endDate ? $revenueQuery->sum('tongtien') ?? 0 : DonHang::where('trangthai', 'da_giao')->sum('tongtien') ?? 0,
                'today_revenue' => DonHang::whereDate('ngaytao', $today)
                    ->where('trangthai', 'da_giao')->sum('tongtien') ?? 0,
                'this_month_revenue' => DonHang::where('ngaytao', '>=', $thisMonth)
                    ->where('trangthai', 'da_giao')->sum('tongtien') ?? 0,
                'last_month_revenue' => DonHang::whereBetween('ngaytao', [$lastMonth, $lastMonthEnd])
                    ->where('trangthai', 'da_giao')->sum('tongtien') ?? 0,
            ];

            // Thống kê người dùng
            $userStats = [
                'total_users' => UserModel::count(),
                'active_users' => UserModel::where('status', 1)->count(),
                'inactive_users' => UserModel::where('status', 0)->count(),
                'admin_users' => UserModel::where('user_catalogue_id', 1)->count(),
                'new_users_today' => UserModel::whereDate('created_at', $today)->count(),
                'new_users_this_month' => UserModel::where('created_at', '>=', $thisMonth)->count(),
            ];

            // Thống kê sản phẩm
            $productStats = [
                'total_products' => SanPham::count(),
                'active_products' => SanPham::where('trangthai', 1)->count(),
                'inactive_products' => SanPham::where('trangthai', 0)->count(),
                'total_categories' => DanhMuc::count(),
                'active_categories' => DanhMuc::where('status', 'active')->count(),
                'inactive_categories' => DanhMuc::where('status', 'inactive')->count(),
            ];

            // Tính phần trăm tăng trưởng
            $growthStats = [
                'order_growth' => $this->calculateGrowthRate($orderStats['this_month_orders'], $orderStats['last_month_orders']),
                'revenue_growth' => $this->calculateGrowthRate($revenueStats['this_month_revenue'], $revenueStats['last_month_revenue']),
                'user_growth' => $this->calculateGrowthRate($userStats['new_users_this_month'], UserModel::whereBetween('created_at', [$lastMonth, $lastMonthEnd])->count()),
            ];

            return [
                'orders' => $orderStats,
                'revenue' => $revenueStats,
                'users' => $userStats,
                'products' => $productStats,
                'growth' => $growthStats,
            ];

        } catch (\Exception $e) {
            Log::error('StatisticsService getOverviewStats error: ' . $e->getMessage());
            return $this->getDefaultStats();
        }
    }

    /**
     * Lấy dữ liệu biểu đồ doanh thu theo tháng (12 tháng gần nhất)
     */
    public function getRevenueChartData($startDate = null, $endDate = null)
    {
        try {
            $months = [];
            $revenues = [];
            
            if ($startDate && $endDate) {
                // Nếu có filter theo ngày, tạo biểu đồ theo ngày
                $start = Carbon::parse($startDate);
                $end = Carbon::parse($endDate);
                $daysDiff = $start->diffInDays($end);
                
                if ($daysDiff <= 31) {
                    // Nếu <= 31 ngày, hiển thị theo ngày
                    for ($i = 0; $i <= $daysDiff; $i++) {
                        $date = $start->copy()->addDays($i);
                        $revenue = DonHang::whereDate('ngaytao', $date)
                            ->where('trangthai', 'da_giao')
                            ->sum('tongtien') ?? 0;
                        
                        $months[] = $date->format('d/m');
                        $revenues[] = (float) $revenue;
                    }
                } else {
                    // Nếu > 31 ngày, hiển thị theo tuần
                    $weeks = $start->diffInWeeks($end);
                    for ($i = 0; $i <= $weeks; $i++) {
                        $weekStart = $start->copy()->addWeeks($i)->startOfWeek();
                        $weekEnd = $start->copy()->addWeeks($i)->endOfWeek();
                        
                        $revenue = DonHang::whereBetween('ngaytao', [$weekStart, $weekEnd])
                            ->where('trangthai', 'da_giao')
                            ->sum('tongtien') ?? 0;
                        
                        $months[] = 'Tuần ' . ($i + 1);
                        $revenues[] = (float) $revenue;
                    }
                }
            } else {
                // Mặc định: 12 tháng gần nhất
                for ($i = 11; $i >= 0; $i--) {
                    $date = Carbon::now()->subMonths($i);
                    $startOfMonth = $date->copy()->startOfMonth();
                    $endOfMonth = $date->copy()->endOfMonth();
                    
                    $revenue = DonHang::whereBetween('ngaytao', [$startOfMonth, $endOfMonth])
                        ->where('trangthai', 'da_giao')
                        ->sum('tongtien') ?? 0;
                    
                    $months[] = $date->format('M Y');
                    $revenues[] = (float) $revenue;
                }
            }
            
            return [
                'labels' => $months,
                'data' => $revenues
            ];
        } catch (\Exception $e) {
            Log::error('StatisticsService getRevenueChartData error: ' . $e->getMessage());
            return [
                'labels' => [],
                'data' => []
            ];
        }
    }

    /**
     * Lấy dữ liệu biểu đồ đơn hàng theo tháng (12 tháng gần nhất)
     */
    public function getOrderChartData()
    {
        try {
            $months = [];
            $orders = [];
            
            for ($i = 11; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $startOfMonth = $date->copy()->startOfMonth();
                $endOfMonth = $date->copy()->endOfMonth();
                
                $orderCount = DonHang::whereBetween('ngaytao', [$startOfMonth, $endOfMonth])->count();
                
                $months[] = $date->format('M Y');
                $orders[] = $orderCount;
            }
            
            return [
                'labels' => $months,
                'data' => $orders
            ];
        } catch (\Exception $e) {
            Log::error('StatisticsService getOrderChartData error: ' . $e->getMessage());
            return [
                'labels' => [],
                'data' => []
            ];
        }
    }

    /**
     * Lấy top sản phẩm bán chạy
     */
    public function getTopProducts($limit = 10, $startDate = null, $endDate = null)
    {
        try {
            $query = ChiTietDonHang::select('tensanpham', DB::raw('SUM(soluong) as total_sold'), DB::raw('SUM(thanhtien) as total_revenue'))
                ->join('donhang', 'chitietdonhang.id_donhang', '=', 'donhang.id')
                ->where('donhang.trangthai', 'da_giao');

            if ($startDate && $endDate) {
                $start = Carbon::parse($startDate)->startOfDay();
                $end = Carbon::parse($endDate)->endOfDay();
                $query->whereBetween('donhang.ngaytao', [$start, $end]);
            }

            return $query->groupBy('tensanpham')
                ->orderBy('total_sold', 'desc')
                ->limit($limit)
                ->get();
        } catch (\Exception $e) {
            Log::error('StatisticsService getTopProducts error: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Lấy top khách hàng mua nhiều nhất
     */
    public function getTopCustomers($limit = 10, $startDate = null, $endDate = null)
    {
        try {
            $query = DonHang::select('id_user', 'hoten', 'email', DB::raw('COUNT(*) as total_orders'), DB::raw('SUM(tongtien) as total_spent'))
                ->where('trangthai', 'da_giao')
                ->whereNotNull('id_user');

            if ($startDate && $endDate) {
                $start = Carbon::parse($startDate)->startOfDay();
                $end = Carbon::parse($endDate)->endOfDay();
                $query->whereBetween('ngaytao', [$start, $end]);
            }

            return $query->groupBy('id_user', 'hoten', 'email')
                ->orderBy('total_spent', 'desc')
                ->limit($limit)
                ->get();
        } catch (\Exception $e) {
            Log::error('StatisticsService getTopCustomers error: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Lấy thống kê đơn hàng theo trạng thái
     */
    public function getOrderStatusStats()
    {
        try {
            return DonHang::select('trangthai', DB::raw('COUNT(*) as count'))
                ->groupBy('trangthai')
                ->get()
                ->pluck('count', 'trangthai')
                ->toArray();
        } catch (\Exception $e) {
            Log::error('StatisticsService getOrderStatusStats error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Tính phần trăm tăng trưởng
     */
    private function calculateGrowthRate($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        
        return round((($current - $previous) / $previous) * 100, 2);
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
