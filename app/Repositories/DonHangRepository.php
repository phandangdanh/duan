<?php

namespace App\Repositories;

use App\Models\DonHang;
use App\Models\ChiTietDonHang;
use Illuminate\Support\Facades\DB;

class DonHangRepository
{
    public function create(array $data)
    {
        return DonHang::create($data);
    }

    public function findById($id)
    {
        return DonHang::find($id);
    }

    public function findByIdWithRelations($id)
    {
        return DonHang::with(['user', 'chiTietDonHang', 'donHangVoucher'])->find($id);
    }

    public function update($id, array $data)
    {
        return DonHang::where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return DonHang::destroy($id);
    }

    public function getAll($filters = [])
    {
        $query = DonHang::with(['user', 'chiTietDonHang']);

        if (!empty($filters['trangthai'])) {
            $query->where('trangthai', $filters['trangthai']);
        }

        if (!empty($filters['from_date'])) {
            $query->whereDate('ngaytao', '>=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $query->whereDate('ngaytao', '<=', $filters['to_date']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('id_user', $filters['user_id']);
        }

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

        return $query->orderBy('ngaytao', 'desc');
    }

    public function getStats()
    {
        return [
            'total' => DonHang::count(),
            'pending' => DonHang::where('trangthai', DonHang::TRANGTHAI_CHO_XAC_NHAN)->count(),
            'confirmed' => DonHang::where('trangthai', DonHang::TRANGTHAI_DA_XAC_NHAN)->count(),
            'shipping' => DonHang::where('trangthai', DonHang::TRANGTHAI_DANG_GIAO)->count(),
            'delivered' => DonHang::where('trangthai', DonHang::TRANGTHAI_DA_GIAO)->count(),
            'cancelled' => DonHang::where('trangthai', DonHang::TRANGTHAI_DA_HUY)->count(),
            'returned' => DonHang::where('trangthai', DonHang::TRANGTHAI_HOAN_TRA)->count(),
        ];
    }

    public function getRevenueStats()
    {
        return [
            'total_revenue' => DonHang::whereIn('trangthai', [
                DonHang::TRANGTHAI_DA_GIAO,
                DonHang::TRANGTHAI_DANG_GIAO
            ])->sum('tongtien'),
            'today_revenue' => DonHang::whereIn('trangthai', [
                DonHang::TRANGTHAI_DA_GIAO,
                DonHang::TRANGTHAI_DANG_GIAO
            ])->whereDate('ngaytao', today())->sum('tongtien'),
            'month_revenue' => DonHang::whereIn('trangthai', [
                DonHang::TRANGTHAI_DA_GIAO,
                DonHang::TRANGTHAI_DANG_GIAO
            ])->whereMonth('ngaytao', now()->month)
              ->whereYear('ngaytao', now()->year)
              ->sum('tongtien'),
        ];
    }

    public function getChartData($period = 'month')
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
        return DB::table('users')
            ->join('donhang', 'users.id', '=', 'donhang.id_user')
            ->select('users.*', DB::raw('COUNT(donhang.id) as total_orders'), DB::raw('SUM(donhang.tongtien) as total_spent'))
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
