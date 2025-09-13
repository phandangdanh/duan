<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonHang extends Model
{
    use HasFactory;

    protected $table = 'donhang';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'trangthai',
        'ngaytao',
        'ngaythanhtoan',
        'nhanvien',
        'tensp',
        'tongtien',
        'ghichu',
        'lichsutrangthai',
        'phuongthucthanhtoan',
        'trangthaithanhtoan',
        'diachigiaohang',
        'sodienthoai',
        'email',
        'hoten',
    ];

    protected $casts = [
        'id_user' => 'integer',
        'ngaytao' => 'datetime',
        'ngaythanhtoan' => 'datetime',
        'tongtien' => 'decimal:2',
    ];

    // Trạng thái đơn hàng
    const TRANGTHAI_CHO_XAC_NHAN = 'cho_xac_nhan';
    const TRANGTHAI_DA_XAC_NHAN = 'da_xac_nhan';
    const TRANGTHAI_DANG_GIAO = 'dang_giao';
    const TRANGTHAI_DA_GIAO = 'da_giao';
    const TRANGTHAI_DA_HUY = 'da_huy';
    const TRANGTHAI_HOAN_TRA = 'hoan_tra';

    public static function getTrangThaiOptions()
    {
        return [
            self::TRANGTHAI_CHO_XAC_NHAN => 'Chờ xác nhận',
            self::TRANGTHAI_DA_XAC_NHAN => 'Đã xác nhận',
            self::TRANGTHAI_DANG_GIAO => 'Đang giao',
            self::TRANGTHAI_DA_GIAO => 'Đã giao',
            self::TRANGTHAI_DA_HUY => 'Đã hủy',
            self::TRANGTHAI_HOAN_TRA => 'Hoàn trả',
        ];
    }

    public function getTrangThaiTextAttribute()
    {
        $options = self::getTrangThaiOptions();
        return $options[$this->trangthai] ?? 'Không xác định';
    }

    public function getTrangThaiBadgeClassAttribute()
    {
        switch ($this->trangthai) {
            case self::TRANGTHAI_CHO_XAC_NHAN:
                return 'badge-warning';
            case self::TRANGTHAI_DA_XAC_NHAN:
                return 'badge-info';
            case self::TRANGTHAI_DANG_GIAO:
                return 'badge-primary';
            case self::TRANGTHAI_DA_GIAO:
                return 'badge-success';
            case self::TRANGTHAI_DA_HUY:
                return 'badge-danger';
            case self::TRANGTHAI_HOAN_TRA:
                return 'badge-secondary';
            default:
                return 'badge-light';
        }
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function chiTietDonHang()
    {
        return $this->hasMany(ChiTietDonHang::class, 'id_donhang');
    }

    public function donHangVoucher()
    {
        return $this->hasMany(DonHangVoucher::class, 'id_donhang');
    }

    // Accessor cho tổng tiền
    public function getTongTienFormattedAttribute()
    {
        return number_format($this->tongtien, 0, ',', '.') . ' VNĐ';
    }

    // Scope cho filter
    public function scopeByTrangThai($query, $trangthai)
    {
        if ($trangthai) {
            return $query->where('trangthai', $trangthai);
        }
        return $query;
    }

    public function scopeByDateRange($query, $from, $to)
    {
        if ($from) {
            $query->where('ngaytao', '>=', $from);
        }
        if ($to) {
            $query->where('ngaytao', '<=', $to . ' 23:59:59');
        }
        return $query;
    }

    public function scopeByUser($query, $userId)
    {
        if ($userId) {
            return $query->where('id_user', $userId);
        }
        return $query;
    }
}