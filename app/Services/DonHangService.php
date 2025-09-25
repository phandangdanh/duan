<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Collection;
use App\Models\DonHang;
use App\Models\ChiTietDonHang;
use App\Models\User;
use App\Mail\OrderConfirmationEmail;

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
         $stats['voucher_usage_rate'] = ($stats['total_orders'] > 0) 
            ? round(($stats['orders_with_voucher'] / $stats['total_orders']) * 100, 1) 
            : 0;

        return $stats;
    }

    /**
     * Tạo đơn hàng mới
     */
    public function createOrder(array $data)
    {
        // Log dữ liệu đầu vào để debug
        Log::info('DonHangService createOrder input:', $data);
        
        // Validate dữ liệu đầu vào
        if (empty($data['id_user']) || empty($data['tongtien']) || empty($data['hoten'])) {
            throw new \Exception('Dữ liệu đơn hàng không hợp lệ');
        }
        
        if ($data['tongtien'] <= 0) {
            throw new \Exception('Tổng tiền đơn hàng phải lớn hơn 0');
        }
        
        return DB::transaction(function () use ($data) {
            // Tạo đơn hàng
            $order = DonHang::create([
                'id_user' => $data['id_user'],
                'trangthai' => DonHang::TRANGTHAI_CHO_XAC_NHAN,
                'ngaytao' => now()->setTimezone('Asia/Ho_Chi_Minh'),
                'tongtien' => floatval($data['tongtien']),
                'hoten' => $data['hoten'],
                'email' => $data['email'],
                'sodienthoai' => $data['sodienthoai'],
                'diachigiaohang' => $data['diachigiaohang'],
                'phuongthucthanhtoan' => $data['phuongthucthanhtoan'] ?? 'cod',
                'trangthaithanhtoan' => 'chua_thanh_toan',
                'ghichu' => $data['ghichu'] ?? null,
                'tensp' => 'Đơn hàng #' . (DonHang::max('id') + 1), // Tạo tên sản phẩm mặc định
            ]);

            // Tạo chi tiết đơn hàng và trừ tồn kho
            foreach ($data['chi_tiet_don_hang'] as $detail) {
                ChiTietDonHang::create([
                    'id_donhang' => $order->id,
                    'id_chitietsanpham' => $detail['id_chitietsanpham'],
                    'tensanpham' => $detail['tensanpham'],
                    'dongia' => $detail['dongia'],
                    'soluong' => $detail['soluong'],
                    'thanhtien' => $detail['thanhtien'],
                    'ghichu' => $detail['ghichu'],
                ]);

                // Trừ tồn kho
                $this->updateInventory($detail['id_chitietsanpham'], $detail['soluong']);
            }

            // Tạo DonHangVoucher nếu có voucher
            if (!empty($data['vouchers'])) {
                foreach ($data['vouchers'] as $voucherData) {
                    \App\Models\DonHangVoucher::create([
                        'id_donhang' => $order->id,
                        'id_voucher' => $voucherData['id_voucher'],
                        'ngayapdung' => now()->setTimezone('Asia/Ho_Chi_Minh'),
                    ]);
                }
                
                Log::info('Vouchers applied to order:', [
                    'order_id' => $order->id,
                    'vouchers' => $data['vouchers']
                ]);
            }

            // Log đơn hàng đã tạo thành công
            Log::info('Order created successfully:', [
                'order_id' => $order->id,
                'user_id' => $order->id_user,
                'total' => $order->tongtien,
                'created_at' => $order->ngaytao
            ]);

            // Gửi email xác nhận đơn hàng
            try {
                $this->sendOrderConfirmationEmail($order, $data['chi_tiet_don_hang']);
                Log::info('Order confirmation email sent successfully', ['order_id' => $order->id]);
            } catch (\Exception $e) {
                Log::error('Failed to send order confirmation email', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage()
                ]);
                // Không throw exception để không làm fail việc tạo đơn hàng
            }

            return $order;
        });
    }

    /**
     * Cập nhật tồn kho khi tạo đơn hàng
     */
    private function updateInventory($chiTietSanPhamId, $soluong)
    {
        try {
            // Tìm chi tiết sản phẩm với lock để tránh race condition
            $chiTietSanPham = \App\Models\ChiTietSanPham::where('id', $chiTietSanPhamId)->lockForUpdate()->first();
            
            if (!$chiTietSanPham) {
                Log::warning('Chi tiết sản phẩm không tồn tại:', ['id' => $chiTietSanPhamId]);
                return;
            }

            // Tìm sản phẩm chính
            $sanPham = \App\Models\SanPham::where('id', $chiTietSanPham->id_sp)->lockForUpdate()->first();
            
            if (!$sanPham) {
                Log::warning('Sản phẩm chính không tồn tại:', ['id' => $chiTietSanPham->id_sp]);
                return;
            }

            // Kiểm tra tồn kho hiện tại của biến thể
            $tonKhoBienThe = $chiTietSanPham->soLuong ?? 0;
            $tonKhoSanPham = $sanPham->soLuong ?? 0;
            
            Log::info('Kiểm tra tồn kho:', [
                'chi_tiet_san_pham_id' => $chiTietSanPhamId,
                'san_pham_id' => $sanPham->id,
                'ten_san_pham' => $chiTietSanPham->tenSp ?? 'N/A',
                'ton_kho_bien_the_hien_tai' => $tonKhoBienThe,
                'ton_kho_san_pham_hien_tai' => $tonKhoSanPham,
                'so_luong_mua' => $soluong,
                'ton_kho_bien_the_sau_khi_trừ' => $tonKhoBienThe - $soluong,
                'ton_kho_san_pham_sau_khi_trừ' => $tonKhoSanPham - $soluong
            ]);
            
            // Kiểm tra xem sản phẩm có biến thể hay không
            $hasVariants = \App\Models\ChiTietSanPham::where('id_sp', $sanPham->id)
                ->where('id', '!=', $chiTietSanPhamId)
                ->exists();
            
            Log::info('Kiểm tra biến thể:', [
                'san_pham_id' => $sanPham->id,
                'chi_tiet_san_pham_id' => $chiTietSanPhamId,
                'co_bien_the_khac' => $hasVariants
            ]);
            
            if ($hasVariants) {
                // Sản phẩm có biến thể khác -> chỉ trừ tồn kho biến thể
                Log::info('Sản phẩm có biến thể, chỉ trừ tồn kho biến thể');
                
                if ($tonKhoBienThe < $soluong) {
                    Log::warning('Tồn kho biến thể không đủ, nhưng vẫn cho phép đặt hàng:', [
                        'chi_tiet_san_pham_id' => $chiTietSanPhamId,
                        'ten_san_pham' => $chiTietSanPham->tenSp ?? 'N/A',
                        'ton_kho_bien_the_hien_tai' => $tonKhoBienThe,
                        'so_luong_mua' => $soluong,
                        'thieu' => $soluong - $tonKhoBienThe
                    ]);
                    
                    $chiTietSanPham->soLuong = 0;
                    $chiTietSanPham->save();
                } else {
                    $chiTietSanPham->decrement('soLuong', $soluong);
                }
            } else {
                // Sản phẩm không có biến thể khác -> chỉ trừ tồn kho sản phẩm chính
                Log::info('Sản phẩm không có biến thể, chỉ trừ tồn kho sản phẩm chính');
                
                if ($tonKhoSanPham < $soluong) {
                    Log::warning('Tồn kho sản phẩm chính không đủ, nhưng vẫn cho phép đặt hàng:', [
                        'san_pham_id' => $sanPham->id,
                        'ten_san_pham' => $sanPham->tenSP ?? 'N/A',
                        'ton_kho_san_pham_hien_tai' => $tonKhoSanPham,
                        'so_luong_mua' => $soluong,
                        'thieu' => $soluong - $tonKhoSanPham
                    ]);
                    
                    $sanPham->soLuong = 0;
                    $sanPham->save();
                } else {
                    $sanPham->decrement('soLuong', $soluong);
                }
            }
            
            Log::info('Đã cập nhật tồn kho:', [
                'chi_tiet_san_pham_id' => $chiTietSanPhamId,
                'san_pham_id' => $sanPham->id,
                'so_luong_trừ' => $soluong,
                'ton_kho_bien_the_sau_khi_trừ' => $chiTietSanPham->fresh()->soLuong,
                'ton_kho_san_pham_sau_khi_trừ' => $sanPham->fresh()->soLuong
            ]);

        } catch (\Exception $e) {
            Log::error('Lỗi khi cập nhật tồn kho:', [
                'chi_tiet_san_pham_id' => $chiTietSanPhamId,
                'so_luong' => $soluong,
                'error' => $e->getMessage()
            ]);
            // Không throw exception nữa, chỉ log lỗi
            Log::warning('Tiếp tục tạo đơn hàng mặc dù có lỗi tồn kho');
        }
    }

    /**
     * Hoàn lại tồn kho khi hủy đơn hàng
     */
    public function restoreInventory($orderId)
    {
        try {
            $order = DonHang::with('chiTietDonHang')->find($orderId);
            
            if (!$order) {
                throw new \Exception('Không tìm thấy đơn hàng');
            }

            foreach ($order->chiTietDonHang as $item) {
                $chiTietSanPham = \App\Models\ChiTietSanPham::find($item->id_chitietsanpham);
                
                if ($chiTietSanPham) {
                    $sanPham = \App\Models\SanPham::find($chiTietSanPham->id_sp);
                    
                    if ($sanPham) {
                        // Kiểm tra xem sản phẩm có biến thể hay không
                        $hasVariants = \App\Models\ChiTietSanPham::where('id_sp', $sanPham->id)
                            ->where('id', '!=', $item->id_chitietsanpham)
                            ->exists();
                        
                        if ($hasVariants) {
                            // Sản phẩm có biến thể khác -> chỉ hoàn lại tồn kho biến thể
                            $chiTietSanPham->increment('soLuong', $item->soluong);
                            Log::info('Hoàn lại tồn kho biến thể:', [
                                'chi_tiet_san_pham_id' => $item->id_chitietsanpham,
                                'so_luong_hoàn_lại' => $item->soluong,
                                'ton_kho_bien_the_sau_khi_hoàn_lại' => $chiTietSanPham->fresh()->soLuong
                            ]);
                        } else {
                            // Sản phẩm không có biến thể khác -> chỉ hoàn lại tồn kho sản phẩm chính
                            $sanPham->increment('soLuong', $item->soluong);
                            Log::info('Hoàn lại tồn kho sản phẩm chính:', [
                                'san_pham_id' => $sanPham->id,
                                'so_luong_hoàn_lại' => $item->soluong,
                                'ton_kho_san_pham_sau_khi_hoàn_lại' => $sanPham->fresh()->soLuong
                            ]);
                        }
                    }
                }
            }

        } catch (\Exception $e) {
            Log::error('Lỗi khi hoàn lại tồn kho:', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Lấy đơn hàng theo ID
     */
    public function getOrderById($id)
    {
        return DonHang::with(['chiTietDonHang.chiTietSanPham', 'user'])->find($id);
    }

    public function getDonHangList($filters = [])
    {
        try {
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
        } catch (\Exception $e) {
            Log::error('Error in getDonHangList: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
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

        // Hoàn lại tồn kho nếu hủy đơn hàng
        if ($trangthai === DonHang::TRANGTHAI_DA_HUY && $oldTrangThai !== DonHang::TRANGTHAI_DA_HUY) {
            $this->restoreInventory($id);
        }

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

    /**
     * Gửi email xác nhận đơn hàng
     */
    private function sendOrderConfirmationEmail(DonHang $order, array $orderDetails)
    {
        // Kiểm tra email có tồn tại không
        if (empty($order->email)) {
            Log::warning('Order has no email address', ['order_id' => $order->id]);
            return;
        }

        // Gửi email
        Mail::to($order->email)->send(new OrderConfirmationEmail($order, $orderDetails));
        
        Log::info('Order confirmation email queued', [
            'order_id' => $order->id,
            'email' => $order->email,
            'customer_name' => $order->hoten
        ]);
    }
}
