<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SanPhamHinhanh extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sanpham_hinhanh';
    public $timestamps = true;

    protected $fillable = [
        'sanpham_id',
        'url',
        'is_default',
        'mota',
    ];

    protected $casts = [
        'sanpham_id' => 'integer',
        'is_default' => 'boolean',
    ];

    public function sanpham()
    {
        return $this->belongsTo(SanPham::class, 'sanpham_id');
    }
}
