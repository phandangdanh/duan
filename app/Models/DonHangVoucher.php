<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonHangVoucher extends Model
{
    use HasFactory;

    protected $table = 'donhang_voucher';
    public $timestamps = false;

    protected $fillable = [
        'id_donhang',
        'id_voucher',
        'ngayapdung',
    ];

    protected $casts = [
        'id_donhang' => 'integer',
        'id_voucher' => 'integer',
        'ngayapdung' => 'datetime',
    ];

    // Relationships
    public function donHang()
    {
        return $this->belongsTo(DonHang::class, 'id_donhang');
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class, 'id_voucher');
    }
}
