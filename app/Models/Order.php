<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
     public function product()
    {
        return $this->belongsTo(Product::class);
    }
    // Trong App\Models\Order
public function statusHistory()
{
    return $this->hasMany(OrderStatusHistory::class);
}

public function currentStatus()
{
    return $this->hasOne(OrderStatusHistory::class)->where('is_current', true);
}
}
