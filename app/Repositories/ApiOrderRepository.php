<?php

namespace App\Repositories;

use App\Models\DonHang;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ApiOrderRepository
{
    /**
     * Get all orders with pagination and filters
     */
    public function getOrders(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = DonHang::with([
            'user:id,name,email,phone',
            'chiTietDonHang' => function($q) {
                $q->with(['chiTietSanPham' => function($subQ) {
                    $subQ->with(['mausac:id,ten', 'size:id,ten']);
                }]);
            },
            'donHangVoucher' => function($q) {
                $q->with('voucher:id,ma_voucher,ten_voucher,gia_tri,loai_giam_gia');
            }
        ]);
        
        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('hoten', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('sodienthoai', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        if (isset($filters['status'])) {
            $query->where('trangthai', $filters['status']);
        }
        
        if (isset($filters['user_id'])) {
            $query->where('id_user', $filters['user_id']);
        }
        
        if (isset($filters['date_from'])) {
            $query->where('ngaytao', '>=', $filters['date_from']);
        }
        
        if (isset($filters['date_to'])) {
            $query->where('ngaytao', '<=', $filters['date_to'] . ' 23:59:59');
        }
        
        if (isset($filters['min_amount'])) {
            $query->where('tongtien', '>=', $filters['min_amount']);
        }
        
        if (isset($filters['max_amount'])) {
            $query->where('tongtien', '<=', $filters['max_amount']);
        }
        
        if (isset($filters['payment_method'])) {
            $query->where('phuongthucthanhtoan', $filters['payment_method']);
        }
        
        if (isset($filters['payment_status'])) {
            $query->where('trangthaithanhtoan', $filters['payment_status']);
        }
        
        // Order by creation date (newest first)
        $query->orderBy('ngaytao', 'desc');
        
        return $query->paginate($perPage);
    }

    /**
     * Get order by ID
     */
    public function getOrderById(int $id): ?DonHang
    {
        return DonHang::with([
            'user:id,name,email,phone,address',
            'chiTietDonHang' => function($q) {
                $q->with(['chiTietSanPham' => function($subQ) {
                    $subQ->with(['mausac:id,ten', 'size:id,ten', 'sanpham:id,tenSP,maSP']);
                }]);
            },
            'donHangVoucher' => function($q) {
                $q->with('voucher:id,ma_voucher,ten_voucher,gia_tri,loai_giam_gia,mota');
            }
        ])->find($id);
    }

    /**
     * Get order statistics
     */
    public function getOrderStatistics(array $filters = []): array
    {
        $query = DonHang::query();
        
        // Apply date filters
        if (isset($filters['date_from'])) {
            $query->where('ngaytao', '>=', $filters['date_from']);
        }
        
        if (isset($filters['date_to'])) {
            $query->where('ngaytao', '<=', $filters['date_to'] . ' 23:59:59');
        }
        
        // Total orders
        $totalOrders = $query->count();
        
        // Orders by status
        $ordersByStatus = $query->select('trangthai', DB::raw('count(*) as count'))
            ->groupBy('trangthai')
            ->pluck('count', 'trangthai')
            ->toArray();
        
        // Total revenue
        $totalRevenue = $query->sum('tongtien');
        
        // Average order value
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        
        // Orders by payment method
        $ordersByPaymentMethod = $query->select('phuongthucthanhtoan', DB::raw('count(*) as count'))
            ->groupBy('phuongthucthanhtoan')
            ->pluck('count', 'phuongthucthanhtoan')
            ->toArray();
        
        // Orders by payment status
        $ordersByPaymentStatus = $query->select('trangthaithanhtoan', DB::raw('count(*) as count'))
            ->groupBy('trangthaithanhtoan')
            ->pluck('count', 'trangthaithanhtoan')
            ->toArray();
        
        // Daily orders (last 30 days)
        $dailyOrders = $query->select(
                DB::raw('DATE(ngaytao) as date'),
                DB::raw('count(*) as count'),
                DB::raw('sum(tongtien) as revenue')
            )
            ->where('ngaytao', '>=', now()->subDays(30))
            ->groupBy(DB::raw('DATE(ngaytao)'))
            ->orderBy('date', 'desc')
            ->get()
            ->toArray();
        
        return [
            'total_orders' => $totalOrders,
            'total_revenue' => $totalRevenue,
            'average_order_value' => round($avgOrderValue, 2),
            'orders_by_status' => $ordersByStatus,
            'orders_by_payment_method' => $ordersByPaymentMethod,
            'orders_by_payment_status' => $ordersByPaymentStatus,
            'daily_orders' => $dailyOrders,
        ];
    }

    /**
     * Get top selling products
     */
    public function getTopSellingProducts(int $limit = 10, array $filters = []): Collection
    {
        $query = DB::table('chitietdonhang')
            ->join('chitietsanpham', 'chitietdonhang.id_chitietsanpham', '=', 'chitietsanpham.id')
            ->join('sanpham', 'chitietsanpham.id_sp', '=', 'sanpham.id')
            ->join('donhang', 'chitietdonhang.id_donhang', '=', 'donhang.id')
            ->select(
                'sanpham.id',
                'sanpham.tenSP',
                'sanpham.maSP',
                DB::raw('SUM(chitietdonhang.soluong) as total_sold'),
                DB::raw('SUM(chitietdonhang.thanhtien) as total_revenue')
            )
            ->whereNull('sanpham.deleted_at')
            ->whereNull('chitietsanpham.deleted_at')
            ->groupBy('sanpham.id', 'sanpham.tenSP', 'sanpham.maSP')
            ->orderBy('total_sold', 'desc')
            ->limit($limit);
        
        // Apply date filters
        if (isset($filters['date_from'])) {
            $query->where('donhang.ngaytao', '>=', $filters['date_from']);
        }
        
        if (isset($filters['date_to'])) {
            $query->where('donhang.ngaytao', '<=', $filters['date_to'] . ' 23:59:59');
        }
        
        return $query->get();
    }

    /**
     * Get orders by date range
     */
    public function getOrdersByDateRange(string $from, string $to, int $perPage = 10): LengthAwarePaginator
    {
        return DonHang::with([
            'user:id,name,email',
            'chiTietDonHang' => function($q) {
                $q->with(['chiTietSanPham' => function($subQ) {
                    $subQ->with(['mausac:id,ten', 'size:id,ten']);
                }]);
            }
        ])
        ->whereBetween('ngaytao', [$from, $to . ' 23:59:59'])
        ->orderBy('ngaytao', 'desc')
        ->paginate($perPage);
    }

    /**
     * Get orders by user
     */
    public function getOrdersByUser(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return DonHang::with([
            'chiTietDonHang' => function($q) {
                $q->with(['chiTietSanPham' => function($subQ) {
                    $subQ->with(['mausac:id,ten', 'size:id,ten', 'sanpham:id,tenSP,maSP']);
                }]);
            },
            'donHangVoucher' => function($q) {
                $q->with('voucher:id,ma_voucher,ten_voucher,gia_tri,loai_giam_gia');
            }
        ])
        ->where('id_user', $userId)
        ->orderBy('ngaytao', 'desc')
        ->paginate($perPage);
    }

    /**
     * Get order count by status
     */
    public function getOrderCountByStatus(): array
    {
        return DonHang::select('trangthai', DB::raw('count(*) as count'))
            ->groupBy('trangthai')
            ->pluck('count', 'trangthai')
            ->toArray();
    }

    /**
     * Get revenue by date range
     */
    public function getRevenueByDateRange(string $from, string $to): array
    {
        return DonHang::select(
                DB::raw('DATE(ngaytao) as date'),
                DB::raw('count(*) as orders_count'),
                DB::raw('sum(tongtien) as revenue')
            )
            ->whereBetween('ngaytao', [$from, $to . ' 23:59:59'])
            ->groupBy(DB::raw('DATE(ngaytao)'))
            ->orderBy('date', 'asc')
            ->get()
            ->toArray();
    }
}
