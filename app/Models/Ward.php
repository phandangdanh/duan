<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ward extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'name_en',
        'full_name',
        'full_name_en',
        'code_name',
        'district_code',
        'administrative_unit_id',
    ];

    protected $table = 'wards';
    protected $primaryKey = 'code';
    public function district()
    {
        return $this->belongsTo(District::class, 'district_code', 'code');
    }
    
    public function province()
    {
        return $this->hasOneThrough(Province::class, District::class, 'code', 'code', 'district_code', 'province_code');
    }
}
