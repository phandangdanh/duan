<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    use HasFactory;

    protected $table = 'size';
    public $timestamps = false;

    protected $fillable = [
        'ten',
        'mota',
    ];

    public function chitietsanpham()
    {
        return $this->hasMany(ChiTietSanPham::class, 'id_size');
    }
}
