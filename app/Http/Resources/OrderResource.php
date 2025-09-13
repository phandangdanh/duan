<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'id_user' => $this->id_user,
            'trangthai' => $this->trangthai,
            'trangthai_text' => $this->trangthai_text,
            'trangthai_badge_class' => $this->trangthai_badge_class,
            'ngaytao' => $this->ngaytao,
            'ngaythanhtoan' => $this->ngaythanhtoan,
            'tongtien' => $this->tongtien,
            'tongtien_formatted' => $this->tongtien_formatted,
            'hoten' => $this->hoten,
            'email' => $this->email,
            'sodienthoai' => $this->sodienthoai,
            'diachigiaohang' => $this->diachigiaohang,
            'phuongthucthanhtoan' => $this->phuongthucthanhtoan,
            'phuongthucthanhtoan_text' => $this->getPaymentMethodText(),
            'trangthaithanhtoan' => $this->trangthaithanhtoan,
            'trangthaithanhtoan_text' => $this->getPaymentStatusText(),
            'ghichu' => $this->ghichu,
            'nhanvien' => $this->nhanvien,
            'lichsutrangthai' => $this->lichsutrangthai,
            
            // Relationships
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                    'phone' => $this->user->phone ?? null,
                    'address' => $this->user->address ?? null,
                ];
            }),
            
            'chi_tiet_don_hang' => $this->whenLoaded('chiTietDonHang', function () {
                return $this->chiTietDonHang->map(function ($detail) {
                    return [
                        'id' => $detail->id,
                        'id_chitietsanpham' => $detail->id_chitietsanpham,
                        'tensanpham' => $detail->tensanpham,
                        'dongia' => $detail->dongia,
                        'dongia_formatted' => $detail->dongia_formatted,
                        'soluong' => $detail->soluong,
                        'thanhtien' => $detail->thanhtien,
                        'thanhtien_formatted' => $detail->thanhtien_formatted,
                        'ghichu' => $detail->ghichu,
                        
                        'chi_tiet_san_pham' => $this->when($detail->relationLoaded('chiTietSanPham'), function () use ($detail) {
                            if (!$detail->chiTietSanPham) {
                                return null;
                            }
                            
                            return [
                                'id' => $detail->chiTietSanPham->id,
                                'tenSp' => $detail->chiTietSanPham->tenSp,
                                'gia' => $detail->chiTietSanPham->gia,
                                'gia_khuyenmai' => $detail->chiTietSanPham->gia_khuyenmai,
                                'soLuong' => $detail->chiTietSanPham->soLuong,
                                
                                'mausac' => $this->when($detail->chiTietSanPham->relationLoaded('mausac'), function () use ($detail) {
                                    return $detail->chiTietSanPham->mausac ? [
                                        'id' => $detail->chiTietSanPham->mausac->id,
                                        'tenMau' => $detail->chiTietSanPham->mausac->ten,
                                    ] : null;
                                }),
                                
                                'size' => $this->when($detail->chiTietSanPham->relationLoaded('size'), function () use ($detail) {
                                    return $detail->chiTietSanPham->size ? [
                                        'id' => $detail->chiTietSanPham->size->id,
                                        'tenSize' => $detail->chiTietSanPham->size->ten,
                                    ] : null;
                                }),
                                
                                'sanpham' => $this->when($detail->chiTietSanPham->relationLoaded('sanpham'), function () use ($detail) {
                                    return $detail->chiTietSanPham->sanpham ? [
                                        'id' => $detail->chiTietSanPham->sanpham->id,
                                        'tenSP' => $detail->chiTietSanPham->sanpham->tenSP,
                                        'maSP' => $detail->chiTietSanPham->sanpham->maSP,
                                    ] : null;
                                }),
                            ];
                        }),
                    ];
                });
            }),
            
            'vouchers' => $this->whenLoaded('donHangVoucher', function () {
                return $this->donHangVoucher->map(function ($orderVoucher) {
                    if (!$orderVoucher->voucher) {
                        return null;
                    }
                    
                    return [
                        'id' => $orderVoucher->voucher->id,
                        'ma_voucher' => $orderVoucher->voucher->ma_voucher,
                        'ten_voucher' => $orderVoucher->voucher->ten_voucher,
                        'mota' => $orderVoucher->voucher->mota,
                        'gia_tri' => $orderVoucher->voucher->gia_tri,
                        'gia_tri_formatted' => $orderVoucher->voucher->gia_tri_formatted,
                        'loai_giam_gia' => $orderVoucher->voucher->loai_giam_gia,
                        'loai_giam_gia_text' => $this->getVoucherTypeText($orderVoucher->voucher->loai_giam_gia),
                        'ngayapdung' => $orderVoucher->ngayapdung,
                    ];
                })->filter();
            }),
            
            // Additional computed fields
            'can_cancel' => $this->canCancel(),
            'can_update' => $this->canUpdate(),
            'can_delete' => $this->canDelete(),
            'created_at_human' => $this->ngaytao ? $this->ngaytao->diffForHumans() : null,
            'payment_date_human' => $this->ngaythanhtoan ? $this->ngaythanhtoan->diffForHumans() : null,
        ];
    }

    /**
     * Get payment method text
     */
    private function getPaymentMethodText(): string
    {
        $methods = [
            'cod' => 'Thanh toán khi nhận hàng (COD)',
            'banking' => 'Chuyển khoản ngân hàng',
            'momo' => 'Ví MoMo',
            'zalopay' => 'Ví ZaloPay',
        ];
        
        return $methods[$this->phuongthucthanhtoan] ?? 'Không xác định';
    }

    /**
     * Get payment status text
     */
    private function getPaymentStatusText(): string
    {
        $statuses = [
            'chua_thanh_toan' => 'Chưa thanh toán',
            'da_thanh_toan' => 'Đã thanh toán',
            'hoan_tien' => 'Hoàn tiền',
        ];
        
        return $statuses[$this->trangthaithanhtoan] ?? 'Không xác định';
    }

    /**
     * Get voucher type text
     */
    private function getVoucherTypeText(string $type): string
    {
        $types = [
            'phan_tram' => 'Giảm theo phần trăm',
            'tien_mat' => 'Giảm theo số tiền',
        ];
        
        return $types[$type] ?? 'Không xác định';
    }

    /**
     * Check if order can be cancelled
     */
    private function canCancel(): bool
    {
        return in_array($this->trangthai, [
            \App\Models\DonHang::TRANGTHAI_CHO_XAC_NHAN,
            \App\Models\DonHang::TRANGTHAI_DA_XAC_NHAN,
        ]);
    }

    /**
     * Check if order can be updated
     */
    private function canUpdate(): bool
    {
        return !in_array($this->trangthai, [
            \App\Models\DonHang::TRANGTHAI_DA_HUY,
            \App\Models\DonHang::TRANGTHAI_HOAN_TRA,
        ]);
    }

    /**
     * Check if order can be deleted
     */
    private function canDelete(): bool
    {
        return $this->trangthai === \App\Models\DonHang::TRANGTHAI_CHO_XAC_NHAN;
    }
}
