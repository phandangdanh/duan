<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SanPham extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sanpham';
    public $timestamps = false;

    protected $fillable = [
        'maSP',
        'tenSP',
        'id_danhmuc',
        'moTa',
        'trangthai',
        'base_price',
        'base_sale_price',
        'soLuong',
    ];

    protected $casts = [
        'id_danhmuc' => 'integer',
        'trangthai' => 'boolean',
        'base_price' => 'decimal:2',
        'base_sale_price' => 'decimal:2',
        'soLuong' => 'integer',
    ];

    // Accessor để chuyển đổi trangthai từ 0/1 sang boolean
    public function getTrangthaiAttribute($value)
    {
        return (bool) $value;
    }

    // Mutator để chuyển đổi boolean sang 0/1
    public function setTrangthaiAttribute($value)
    {
        $this->attributes['trangthai'] = $value ? 1 : 0;
    }

    // Scope để lọc sản phẩm đã xóa mềm
    public function scopeTrashed($query)
    {
        return $query->onlyTrashed();
    }

    // Scope để lọc sản phẩm chưa xóa
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    // Scope để lọc sản phẩm đang kinh doanh và chưa xóa
    public function scopeKinhDoanh($query)
    {
        return $query->where('trangthai', 1)->whereNull('deleted_at');
    }

    // Scope để lọc sản phẩm ngừng kinh doanh và chưa xóa
    public function scopeNgungKinhDoanh($query)
    {
        return $query->where('trangthai', 0)->whereNull('deleted_at');
    }

    public function danhmuc()
    {
        return $this->belongsTo(DanhMuc::class, 'id_danhmuc');
    }

    public function hinhanh()
    {
        return $this->hasMany(SanPhamHinhanh::class, 'sanpham_id');
    }

    public function sanphamMausacImages()
    {
        return $this->hasMany(SanPhamMausacImage::class, 'sanpham_id');
    }

    public function chitietsanpham()
    {
        return $this->hasMany(ChiTietSanPham::class, 'id_sp');
    }

    public function binhluan()
    {
        return $this->hasMany(BinhLuan::class, 'id_sp');
    }

    /**
     * Tính tổng tồn kho = Số lượng sản phẩm chính + Tổng số lượng tất cả variant
     */
    public function getTotalStockAttribute()
    {
        // Số lượng sản phẩm chính
        $mainStock = $this->soLuong ?? 0;
        
        // Tổng số lượng tất cả variant
        $variantStock = $this->chitietsanpham()
            ->whereNull('deleted_at')
            ->sum('soLuong');
        
        return $mainStock + $variantStock;
    }

    /**
     * Kiểm tra có đủ tồn kho không
     */
    public function hasEnoughStock($quantity)
    {
        return $this->total_stock >= $quantity;
    }
}


