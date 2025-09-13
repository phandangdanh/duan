<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChiTietSanPham extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'chitietsanpham';
    public $timestamps = false;

    protected $fillable = [
        'id_sp',
        'id_mausac',
        'id_size',
        'soLuong',
        'tenSp',
        'gia',
        'gia_khuyenmai',
    ];

    protected $casts = [
        'id_sp' => 'integer',
        'id_mausac' => 'integer',
        'id_size' => 'integer',
        'soLuong' => 'integer',
        'gia' => 'decimal:2',
        'gia_khuyenmai' => 'decimal:2',
    ];

    public function sanpham()
    {
        return $this->belongsTo(SanPham::class, 'id_sp');
    }

    public function mausac()
    {
        return $this->belongsTo(MauSac::class, 'id_mausac');
    }

    public function size()
    {
        return $this->belongsTo(Size::class, 'id_size');
    }

    public function chitietdonhang()
    {
        return $this->hasMany(ChiTietDonHang::class, 'id_chitietsanpham');
    }
}
