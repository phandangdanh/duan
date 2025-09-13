<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Voucher extends Model
{
    use HasFactory;

    protected $table = 'voucher';

    protected $fillable = [
        'ma_voucher',
        'ten_voucher',
        'mota',
        'loai_giam_gia',
        'gia_tri',
        'gia_tri_toi_thieu',
        'gia_tri_toi_da',
        'so_luong',
        'so_luong_da_su_dung',
        'ngay_bat_dau',
        'ngay_ket_thuc',
        'trang_thai',
    ];

    protected $casts = [
        'gia_tri' => 'decimal:2',
        'gia_tri_toi_thieu' => 'decimal:2',
        'gia_tri_toi_da' => 'decimal:2',
        'so_luong' => 'integer',
        'so_luong_da_su_dung' => 'integer',
        'ngay_bat_dau' => 'datetime',
        'ngay_ket_thuc' => 'datetime',
        'trang_thai' => 'boolean',
    ];

    public $timestamps = true;

    /**
     * Relationship với đơn hàng
     */
    public function donHangs(): HasMany
    {
        return $this->hasMany(DonHangVoucher::class, 'id_voucher');
    }

    /**
     * Scope: Lấy voucher đang hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where('trang_thai', 1);
    }

    /**
     * Scope: Lấy voucher còn hạn
     */
    public function scopeValid($query)
    {
        $now = Carbon::now();
        return $query->where('ngay_bat_dau', '<=', $now)
                    ->where('ngay_ket_thuc', '>=', $now);
    }

    /**
     * Scope: Lấy voucher còn số lượng
     */
    public function scopeAvailable($query)
    {
        return $query->whereRaw('so_luong > so_luong_da_su_dung');
    }

    /**
     * Scope: Lấy voucher hợp lệ (active + valid + available)
     */
    public function scopeUsable($query)
    {
        return $query->active()->valid()->available();
    }

    /**
     * Kiểm tra voucher có thể sử dụng không
     */
    public function isUsable(): bool
    {
        $now = Carbon::now();
        
        return $this->trang_thai == 1 
            && $this->ngay_bat_dau <= $now 
            && $this->ngay_ket_thuc >= $now
            && $this->so_luong > $this->so_luong_da_su_dung;
    }

    /**
     * Kiểm tra voucher có áp dụng được cho đơn hàng không
     */
    public function canApplyToOrder(float $orderTotal): bool
    {
        if (!$this->isUsable()) {
            return false;
        }

        return $orderTotal >= $this->gia_tri_toi_thieu;
    }

    /**
     * Tính giá trị giảm giá cho đơn hàng
     */
    public function calculateDiscount(float $orderTotal): float
    {
        if (!$this->canApplyToOrder($orderTotal)) {
            return 0;
        }

        $discount = 0;

        if ($this->loai_giam_gia === 'phan_tram') {
            // Giảm theo phần trăm
            $discount = $orderTotal * ($this->gia_tri / 100);
            
            // Áp dụng giới hạn tối đa nếu có
            if ($this->gia_tri_toi_da && $discount > $this->gia_tri_toi_da) {
                $discount = $this->gia_tri_toi_da;
            }
        } else {
            // Giảm theo tiền mặt
            $discount = $this->gia_tri;
        }

        return min($discount, $orderTotal); // Không giảm quá giá trị đơn hàng
    }

    /**
     * Tăng số lượng đã sử dụng
     */
    public function incrementUsage(): bool
    {
        if ($this->so_luong_da_su_dung >= $this->so_luong) {
            return false;
        }

        $this->increment('so_luong_da_su_dung');
        return true;
    }

    /**
     * Giảm số lượng đã sử dụng (khi hủy đơn hàng)
     */
    public function decrementUsage(): bool
    {
        if ($this->so_luong_da_su_dung <= 0) {
            return false;
        }

        $this->decrement('so_luong_da_su_dung');
        return true;
    }

    /**
     * Lấy trạng thái voucher dạng text
     */
    public function getTrangThaiTextAttribute(): string
    {
        if (!$this->trang_thai) {
            return 'Tạm dừng';
        }

        $now = Carbon::now();
        
        if ($now < $this->ngay_bat_dau) {
            return 'Chưa bắt đầu';
        }
        
        if ($now > $this->ngay_ket_thuc) {
            return 'Đã hết hạn';
        }
        
        if ($this->so_luong_da_su_dung >= $this->so_luong) {
            return 'Đã hết số lượng';
        }
        
        return 'Đang hoạt động';
    }

    /**
     * Lấy loại giảm giá dạng text
     */
    public function getLoaiGiamGiaTextAttribute(): string
    {
        return $this->loai_giam_gia === 'phan_tram' ? 'Phần trăm' : 'Tiền mặt';
    }

    /**
     * Lấy số lượng còn lại
     */
    public function getSoLuongConLaiAttribute(): int
    {
        return max(0, $this->so_luong - $this->so_luong_da_su_dung);
    }
}