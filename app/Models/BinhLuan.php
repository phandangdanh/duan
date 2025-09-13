<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BinhLuan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'binhluan';
    public $timestamps = true;

    protected $fillable = [
        'id_sp',
        'noidung',
        'hinhanh',
    ];

    protected $casts = [
        'id_sp' => 'integer',
    ];

    public function sanpham()
    {
        return $this->belongsTo(SanPham::class, 'id_sp');
    }
}
