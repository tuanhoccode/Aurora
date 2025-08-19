<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    protected $fillable = [
        'user_id',
        'address',
        'province',
        'district',
        'ward',
        'street',
        'latitude',
        'longitude',
        'address_type',
        'email',
        'phone_number',
        'fullname',
        'is_default',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}