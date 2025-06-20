<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
     use HasFactory;

    protected $fillable = [
        'user_id',
        'fullname',
        'phone_number',
        'address',
        'is_default',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
