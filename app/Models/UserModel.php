<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserModel extends Model
{
    use HasFactory;
    protected $table = 'users';
    protected $fillable = [
        'name',
        'phone',
        'province_id',
        'district_id',
        'ward_id',
        'address',
        'birthday',
        'image',
        'description',
        'user_agent',
        'ip',
        'email',
        'email_verified_at',
        'password',
        'status',
        'user_catalogue_id',
    ];

    protected $hidden = ['password', 'remember_token'];
    protected $casts = [
        'birthday' => 'date',
        'email_verified_at' => 'datetime',
    ];

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id', 'code');
    }
    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'code');
    }
    public function ward()
    {
        return $this->belongsTo(Ward::class, 'ward_id', 'code');
    }
}