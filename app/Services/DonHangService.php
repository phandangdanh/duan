<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use App\Models\DonHang;
use App\Models\ChiTietDonHang;
use App\Models\User;

class DonHangService
{
    public function getDonHangStats()
    {
        $stats = [
            'total_orders' => DonHang::count(),
            'pending_orders' => DonHang::where('trangthai', DonHang::TRANGTHAI_CHO_XAC_NHAN)->count(),
            'confirmed_orders' => DonHang::where('trangthai', DonHang::TRANGTHAI_DA_XAC_NHAN)->count(),
            'shipping_orders' => DonHang::where('trangthai', DonHang::TRANGTHAI_DANG_GIAO)->count(),
            'delivered_orders' => DonHang::where('trangthai', DonHang::TRANGTHAI_DA_GIAO)->count(),
            'cancelled_orders' => DonHang::where('trangthai', DonHang::TRANGTHAI_DA_HUY)->count(),
            'returned_orders' => DonHang::where('trangthai', DonHang::TRANGTHAI_HOAN_TRA)->count(),
        ];

        // Tính tổng doanh thu
        $stats['total_revenue'] = DonHang::whereIn('trangthai', [
            DonHang::TRANGTHAI_DA_GIAO,
            DonHang::TRANGTHAI_DANG_GIAO
        ])->sum('tongtien');

        // Tính doanh thu hôm nay
        $stats['today_revenue'] = DonHang::whereIn('trangthai', [
            DonHang::TRANGTHAI_DA_GIAO,
            DonHang::TRANGTHAI_DANG_GIAO
        ])->whereDate('ngaytao', today())->sum('tongtien');

        // Tính doanh thu tháng này
        $stats['month_revenue'] = DonHang::whereIn('trangthai', [
            DonHang::TRANGTHAI_DA_GIAO,
            DonHang::TRANGTHAI_DANG_GIAO
        ])->whereMonth('ngaytao', now()->month)
          ->whereYear('ngaytao', now()->year)
          ->sum('tongtien');

        // Thống kê voucher
        $stats['orders_with_voucher'] = DonHang::whereHas('donHangVoucher')->count();
        $stats['orders_without_voucher'] = DonHang::whereDoesntHave('donHangVoucher')->count();
        $stats['voucher_usage_rate'] = $stats['total_orders'] > 0 
            ? round(($stats['orders_with_voucher'] / $stats['total_orders']) * 100, 1) 
            : 0;

        return $stats;
    }

    public function getDonHangList($filters = [])
    {
        $query = DonHang::with(['user', 'chiTietDonHang', 'donHangVoucher.voucher'])
            ->orderBy('ngaytao', 'desc');

        // Filter by status
        if (!empty($filters['trangthai'])) {
            $query->where('trangthai', $filters['trangthai']);
        }

        // Filter by date range
        if (!empty($filters['from_date'])) {
            $query->whereDate('ngaytao', '>=', $filters['from_date']);
        }
        if (!empty($filters['to_date'])) {
            $query->whereDate('ngaytao', '<=', $filters['to_date']);
        }

        // Filter by user
        if (!empty($filters['user_id'])) {
            $query->where('id_user', $filters['user_id']);
        }

        // Filter by voucher
        if (!empty($filters['voucher_filter'])) {
            if ($filters['voucher_filter'] === 'has_voucher') {
                $query->whereHas('donHangVoucher');
            } elseif ($filters['voucher_filter'] === 'no_voucher') {
                $query->whereDoesntHave('donHangVoucher');
            }
        }

        // Search by order ID or customer name
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        return $query->paginate($filters['per_page'] ?? 15);
    }

    public function getDonHangById($id)
    {
        return DonHang::with([
            'user',
            'chiTietDonHang.chiTietSanPham',
            'donHangVoucher.voucher'
        ])->findOrFail($id);
    }

    public function updateTrangThai($id, $trangthai, $nhanvien = null)
    {
        $donhang = DonHang::findOrFail($id);
        
        $oldTrangThai = $donhang->trangthai;
        $donhang->trangthai = $trangthai;
        
        if ($nhanvien) {
            $donhang->nhanvien = $nhanvien;
        }

        // Cập nhật lịch sử trạng thái
        $lichSuTrangThai = json_decode($donhang->lichsutrangthai ?? '[]', true);
        $lichSuTrangThai[] = [
            'trangthai_cu' => $oldTrangThai,
            'trangthai_moi' => $trangthai,
            'thoi_gian' => now()->toDateTimeString(),
            'nhan_vien' => $nhanvien ?? auth()->user()->name ?? 'System'
        ];
        
        $donhang->lichsutrangthai = json_encode($lichSuTrangThai);
        
        // Cập nhật ngày thanh toán nếu đã giao hàng
        if ($trangthai === DonHang::TRANGTHAI_DA_GIAO) {
            $donhang->ngaythanhtoan = now();
        }

        $donhang->save();

        return $donhang;
    }

    public function getDonHangChartData($period = 'month')
    {
        $query = DonHang::select(
            DB::raw('DATE(ngaytao) as date'),
            DB::raw('COUNT(*) as total_orders'),
            DB::raw('SUM(tongtien) as total_revenue')
        )->whereIn('trangthai', [
            DonHang::TRANGTHAI_DA_GIAO,
            DonHang::TRANGTHAI_DANG_GIAO
        ]);

        switch ($period) {
            case 'week':
                $query->where('ngaytao', '>=', now()->subWeek());
                break;
            case 'month':
                $query->where('ngaytao', '>=', now()->subMonth());
                break;
            case 'year':
                $query->where('ngaytao', '>=', now()->subYear());
                break;
        }

        return $query->groupBy('date')
                    ->orderBy('date')
                    ->get();
    }

    public function getTopCustomers($limit = 10)
    {
        return User::select('users.*', DB::raw('COUNT(donhang.id) as total_orders'), DB::raw('SUM(donhang.tongtien) as total_spent'))
            ->join('donhang', 'users.id', '=', 'donhang.id_user')
            ->whereIn('donhang.trangthai', [
                DonHang::TRANGTHAI_DA_GIAO,
                DonHang::TRANGTHAI_DANG_GIAO
            ])
            ->groupBy('users.id')
            ->orderBy('total_spent', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getTopProducts($limit = 10)
    {
        return ChiTietDonHang::select(
            'tensanpham',
            DB::raw('SUM(soluong) as total_quantity'),
            DB::raw('SUM(thanhtien) as total_revenue')
        )
        ->join('donhang', 'chitietdonhang.id_donhang', '=', 'donhang.id')
        ->whereIn('donhang.trangthai', [
            DonHang::TRANGTHAI_DA_GIAO,
            DonHang::TRANGTHAI_DANG_GIAO
        ])
        ->groupBy('tensanpham')
        ->orderBy('total_quantity', 'desc')
        ->limit($limit)
        ->get();
    }
}
