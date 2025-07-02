<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{

    protected $table = 'order_status_histories';

    protected $fillable = [
        'order_id',
        'order_status_id',
        'modifier_id',
        'note',
        'is_current',
    ];

    protected $casts = [
        'is_current' => 'boolean',
    ];

    /**
     * Quan hệ với model Order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Quan hệ với model OrderStatus
     */
    public function status()
    {
        return $this->belongsTo(OrderStatus::class, 'order_status_id');
    }

    /**
     * Quan hệ với model User (người cập nhật)
     */
    public function modifier()
    {
        return $this->belongsTo(User::class, 'modifier_id');
    }
}