<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiTietDonHang extends Model
{
    use HasFactory;

    protected $table = 'chitietdonhang';
    public $timestamps = false;

    protected $fillable = [
        'id_donhang',
        'id_chitietsanpham',
        'tensanpham',
        'dongia',
        'soluong',
        'thanhtien',
        'ghichu',
    ];

    protected $casts = [
        'id_donhang' => 'integer',
        'id_chitietsanpham' => 'integer',
        'dongia' => 'decimal:2',
        'soluong' => 'integer',
        'thanhtien' => 'decimal:2',
    ];

    // Relationships
    public function donHang()
    {
        return $this->belongsTo(DonHang::class, 'id_donhang');
    }

    public function chiTietSanPham()
    {
        return $this->belongsTo(ChiTietSanPham::class, 'id_chitietsanpham')->withDefault();
    }

    // Accessor cho giá tiền
    public function getDongiaFormattedAttribute()
    {
        return number_format($this->dongia, 0, ',', '.') . ' VNĐ';
    }

    public function getThanhtienFormattedAttribute()
    {
        return number_format($this->thanhtien, 0, ',', '.') . ' VNĐ';
    }

    // Mutator để tự động tính thành tiền
    public function setSoluongAttribute($value)
    {
        $this->attributes['soluong'] = $value;
        $this->attributes['thanhtien'] = $this->dongia * $value;
    }

    public function setDongiaAttribute($value)
    {
        $this->attributes['dongia'] = $value;
        $this->attributes['thanhtien'] = $value * $this->soluong;
    }
}