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
        'img_refunded_money',
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

    // Lịch sử trạng thái nhiều bản ghi
    public function statusHistory()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    // Trạng thái hiện tại (is_current = true)
    public function currentStatus()
    {
        return $this->hasOne(OrderStatusHistory::class)
            ->where('is_current', true)
            ->with('status')
            ->orderByDesc('created_at');
    }

    // Trạng thái hiện tại từ order_order_status (is_current = true)
    public function currentOrderStatus()
    {
        return $this->hasOne(\App\Models\OrderOrderStatus::class)
            ->where('is_current', true)
            ->with('status')
            ->orderByDesc('created_at');
    }
}
