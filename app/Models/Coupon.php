<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'title',
        'description',
        'discount_type',
        'discount_value',
        'usage_limit',
        'usage_count',
        'is_active',
        'is_notified',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date'     => 'datetime',
        'end_date'       => 'datetime',
        'is_active'      => 'boolean',
        'is_notified'    => 'boolean',
        'usage_limit'    => 'integer',
        'usage_count'    => 'integer',
        'discount_value' => 'float',
    ];

    protected $dates = ['start_date', 'end_date', 'deleted_at'];
}
