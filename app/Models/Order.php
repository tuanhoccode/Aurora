<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'code',
        'user_id',
        'payment_id',
        'phone_number',
        'email',
        'fullname',
        'address',
        'note',
        'total_amount',
        'is_paid',
        'is_refunded',
        'coupon_id',
        'is_refunded_canceled',
        'check_refunded_canceled',
        'img_refunded_money'
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'is_refunded' => 'boolean',
        'is_refunded_canceled' => 'boolean',
        'check_refunded_canceled' => 'boolean',
        'total_amount' => 'float'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->code)) {
                $order->code = 'ORD-' . Str::random(8);
            }
        });
    }

    public function getTotalAmountAttribute($value): float
    {
        return number_format($value, 0, ',', '.');
    }
}