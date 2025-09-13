<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DanhGia extends Model
{
    use HasFactory;

    protected $table = 'danhgia';
    public $timestamps = true;

    protected $fillable = [
        'id_chitietdonhang',
        'sao',
        'noidung',
        'thoigian',
    ];

    protected $casts = [
        'id_chitietdonhang' => 'integer',
        'sao' => 'integer',
        'thoigian' => 'datetime',
    ];

    public function chitietdonhang()
    {
        return $this->belongsTo(ChiTietDonHang::class, 'id_chitietdonhang');
    }
}
