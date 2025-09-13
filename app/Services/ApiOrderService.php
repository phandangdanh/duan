<?php

namespace App\Services;

use App\Repositories\ApiOrderRepository;
use App\Models\DonHang;
use App\Models\ChiTietDonHang;
use App\Models\ChiTietSanPham;
use App\Models\Voucher;
use App\Models\DonHangVoucher;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApiOrderService
{
    protected $apiOrderRepository;

    public function __construct(ApiOrderRepository $apiOrderRepository)
    {
        $this->apiOrderRepository = $apiOrderRepository;
    }

    /**
     * Get orders with pagination and filters
     */
    public function getOrders(array $filters = [], int $perPage = 10): array
    {
        $result = $this->apiOrderRepository->getOrders($filters, $perPage);
        
        $currentPage = $result->currentPage();
        $lastPage = $result->lastPage();
        
        return [
            'data' => $result->items(),
            'pagination' => [
                'current_page' => $currentPage,
                'per_page' => $result->perPage(),
                'total' => $result->total(),
                'last_page' => $lastPage,
                'prev_page' => $currentPage > 1 ? $currentPage - 1 : null,
                'next_page' => $currentPage < $lastPage ? $currentPage + 1 : null,
                'prev_url' => $currentPage > 1 ? $result->url($currentPage - 1) : null,
                'next_url' => $currentPage < $lastPage ? $result->url($currentPage + 1) : null,
            ],
        ];
    }

    /**
     * Get order by ID
     */
    public function getOrderById(int $id): ?DonHang
    {
        return $this->apiOrderRepository->getOrderById($id);
    }

    /**
     * Create new order
     */
    public function createOrder(array $data): DonHang
    {
        return DB::transaction(function () use ($data) {
            // Validate product details and calculate total
            $totalAmount = 0;
            $orderDetails = [];
            
            foreach ($data['chi_tiet_don_hang'] as $detail) {
                $chiTietSanPham = ChiTietSanPham::find($detail['id_chitietsanpham']);
                
                if (!$chiTietSanPham) {
                    throw new \Exception("Không tìm thấy chi tiết sản phẩm với ID: " . $detail['id_chitietsanpham']);
                }
                
                // Check stock
                if ($chiTietSanPham->soLuong < $detail['soluong']) {
                    throw new \Exception("Sản phẩm '{$chiTietSanPham->tenSp}' không đủ số lượng. Còn lại: {$chiTietSanPham->soLuong}");
                }
                
                // Use provided price or get from product detail
                $dongia = $detail['dongia'] ?? $chiTietSanPham->gia_khuyenmai ?? $chiTietSanPham->gia;
                $thanhtien = $dongia * $detail['soluong'];
                $totalAmount += $thanhtien;
                
                $orderDetails[] = [
                    'id_chitietsanpham' => $detail['id_chitietsanpham'],
                    'tensanpham' => $chiTietSanPham->tenSp,
                    'dongia' => $dongia,
                    'soluong' => $detail['soluong'],
                    'thanhtien' => $thanhtien,
                    'ghichu' => $detail['ghichu'] ?? null,
                ];
            }
            
            // Apply vouchers if any
            $voucherDiscount = 0;
            if (!empty($data['vouchers'])) {
                foreach ($data['vouchers'] as $voucherData) {
                    $voucher = Voucher::active()->find($voucherData['id_voucher']);
                    
                    if (!$voucher) {
                        throw new \Exception("Voucher không hợp lệ hoặc đã hết hạn");
                    }
                    
                    // Calculate discount
                    if ($voucher->loai_giam_gia === Voucher::LOAI_PHAN_TRAM) {
                        $discount = ($totalAmount * $voucher->gia_tri) / 100;
                        // Apply max discount if set
                        if ($voucher->gia_tri_toi_da && $discount > $voucher->gia_tri_toi_da) {
                            $discount = $voucher->gia_tri_toi_da;
                        }
                    } else {
                        $discount = $voucher->gia_tri;
                    }
                    
                    // Apply min amount condition
                    if ($voucher->gia_tri_toi_thieu && $totalAmount < $voucher->gia_tri_toi_thieu) {
                        throw new \Exception("Đơn hàng phải có giá trị tối thiểu " . number_format($voucher->gia_tri_toi_thieu, 0, ',', '.') . " VNĐ để sử dụng voucher này");
                    }
                    
                    $voucherDiscount += $discount;
                }
            }
            
            $finalAmount = max(0, $totalAmount - $voucherDiscount);
            
            // Create order
            $order = DonHang::create([
                'id_user' => $data['id_user'],
                'trangthai' => DonHang::TRANGTHAI_CHO_XAC_NHAN,
                'ngaytao' => now(),
                'tongtien' => $finalAmount,
                'hoten' => $data['hoten'],
                'email' => $data['email'],
                'sodienthoai' => $data['sodienthoai'],
                'diachigiaohang' => $data['diachigiaohang'],
                'phuongthucthanhtoan' => $data['phuongthucthanhtoan'] ?? 'cod',
                'trangthaithanhtoan' => 'chua_thanh_toan',
                'ghichu' => $data['ghichu'] ?? null,
            ]);
            
            // Create order details
            foreach ($orderDetails as $detail) {
                ChiTietDonHang::create([
                    'id_donhang' => $order->id,
                    'id_chitietsanpham' => $detail['id_chitietsanpham'],
                    'tensanpham' => $detail['tensanpham'],
                    'dongia' => $detail['dongia'],
                    'soluong' => $detail['soluong'],
                    'thanhtien' => $detail['thanhtien'],
                    'ghichu' => $detail['ghichu'],
                ]);
                
                // Update stock
                $chiTietSanPham = ChiTietSanPham::find($detail['id_chitietsanpham']);
                $chiTietSanPham->decrement('soLuong', $detail['soluong']);
            }
            
            // Apply vouchers
            if (!empty($data['vouchers'])) {
                foreach ($data['vouchers'] as $voucherData) {
                    DonHangVoucher::create([
                        'id_donhang' => $order->id,
                        'id_voucher' => $voucherData['id_voucher'],
                        'ngayapdung' => now(),
                    ]);
                    
                    // Update voucher usage count
                    Voucher::where('id', $voucherData['id_voucher'])
                        ->increment('so_luong_da_su_dung');
                }
            }
            
            // Load relationships for response
            $order->load(['user', 'chiTietDonHang.chiTietSanPham.mausac', 'chiTietDonHang.chiTietSanPham.size', 'donHangVoucher.voucher']);
            
            Log::info('Order created successfully', ['order_id' => $order->id, 'total_amount' => $finalAmount]);
            
            return $order;
        });
    }

    /**
     * Update order
     */
    public function updateOrder(int $id, array $data): ?DonHang
    {
        return DB::transaction(function () use ($id, $data) {
            $order = DonHang::find($id);
            
            if (!$order) {
                return null;
            }
            
            // Update order fields
            $updateData = [];
            
            if (isset($data['trangthai'])) {
                $updateData['trangthai'] = $data['trangthai'];
                
                // Set payment date if order is completed
                if ($data['trangthai'] === DonHang::TRANGTHAI_DA_GIAO) {
                    $updateData['ngaythanhtoan'] = now();
                }
            }
            
            if (isset($data['hoten'])) {
                $updateData['hoten'] = $data['hoten'];
            }
            
            if (isset($data['email'])) {
                $updateData['email'] = $data['email'];
            }
            
            if (isset($data['sodienthoai'])) {
                $updateData['sodienthoai'] = $data['sodienthoai'];
            }
            
            if (isset($data['diachigiaohang'])) {
                $updateData['diachigiaohang'] = $data['diachigiaohang'];
            }
            
            if (isset($data['phuongthucthanhtoan'])) {
                $updateData['phuongthucthanhtoan'] = $data['phuongthucthanhtoan'];
            }
            
            if (isset($data['trangthaithanhtoan'])) {
                $updateData['trangthaithanhtoan'] = $data['trangthaithanhtoan'];
            }
            
            if (isset($data['ghichu'])) {
                $updateData['ghichu'] = $data['ghichu'];
            }
            
            if (isset($data['nhanvien'])) {
                $updateData['nhanvien'] = $data['nhanvien'];
            }
            
            if (!empty($updateData)) {
                $order->update($updateData);
            }
            
            // Load relationships for response
            $order->load(['user', 'chiTietDonHang.chiTietSanPham.mausac', 'chiTietDonHang.chiTietSanPham.size', 'donHangVoucher.voucher']);
            
            Log::info('Order updated successfully', ['order_id' => $order->id, 'updated_fields' => array_keys($updateData)]);
            
            return $order;
        });
    }

    /**
     * Delete order (soft delete)
     */
    public function deleteOrder(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $order = DonHang::find($id);
            
            if (!$order) {
                return false;
            }
            
            // Check if order can be deleted (only pending orders)
            if ($order->trangthai !== DonHang::TRANGTHAI_CHO_XAC_NHAN) {
                throw new \Exception("Chỉ có thể xóa đơn hàng đang chờ xác nhận");
            }
            
            // Restore stock
            foreach ($order->chiTietDonHang as $detail) {
                $chiTietSanPham = ChiTietSanPham::find($detail->id_chitietsanpham);
                if ($chiTietSanPham) {
                    $chiTietSanPham->increment('soLuong', $detail->soluong);
                }
            }
            
            // Restore voucher usage
            foreach ($order->donHangVoucher as $orderVoucher) {
                Voucher::where('id', $orderVoucher->id_voucher)
                    ->decrement('so_luong_da_su_dung');
            }
            
            // Delete order details and vouchers
            ChiTietDonHang::where('id_donhang', $id)->delete();
            DonHangVoucher::where('id_donhang', $id)->delete();
            
            // Delete order
            $order->delete();
            
            Log::info('Order deleted successfully', ['order_id' => $id]);
            
            return true;
        });
    }

    /**
     * Get order statistics
     */
    public function getOrderStatistics(array $filters = []): array
    {
        return $this->apiOrderRepository->getOrderStatistics($filters);
    }

    /**
     * Get orders by user
     */
    public function getOrdersByUser(int $userId, array $filters = [], int $perPage = 10): array
    {
        $filters['user_id'] = $userId;
        return $this->getOrders($filters, $perPage);
    }
}
