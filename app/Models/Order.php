<?php

namespace App\Models;
use App\Models\Payment;
use App\Models\User;
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
        'is_refunded_canceled',
        'check_refunded_canceled',
        'img_refunded_money',
        'coupon_id',
        'cancellation_reason',
        'cancellation_note',
        'cancellation_date',
        'cancellation_by',
        'cancellation_status'
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function statusHistory()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function currentStatus()
    {
        return $this->hasOne(OrderStatusHistory::class)
            ->where('is_current', true)
            ->with('status');
    }

    public function status()
    {
        return $this->belongsTo(OrderStatus::class, 'status_id', 'id');
    }
    public function orderDetail()
    {
    return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }
    
}
