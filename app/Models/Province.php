<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Province extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'name_en',
        'full_name',
        'full_name_en',
        'code_name',
        'administrative_region_id',
        'administrative_unit_id',
    ];

    protected $table = 'provinces';
    protected $primaryKey = 'code';

    public function district()
    {
        return $this->hasMany(District::class, 'province_code', 'code');
    }
}
