<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SanPhamMausacImage extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sanpham_mausac_images';
    
    protected $fillable = [
        'sanpham_id',
        'mausac_id',
        'url',
        'is_default',
        'mota',
    ];

    protected $casts = [
        'sanpham_id' => 'integer',
        'mausac_id' => 'integer',
        'is_default' => 'boolean',
    ];

    public function sanpham()
    {
        return $this->belongsTo(SanPham::class, 'sanpham_id');
    }

    public function mausac()
    {
        return $this->belongsTo(MauSac::class, 'mausac_id');
    }

    // Accessor để lấy URL ảnh đầy đủ
    public function getImageUrlAttribute()
    {
        if ($this->url) {
            return asset('uploads/' . $this->url);
        }
        return asset('backend/img/default-product.png');
    }
}
