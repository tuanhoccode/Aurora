<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderStatusHistory extends Model
{
    protected $table = 'order_status_histories';

    protected $fillable = [
        'order_id',
        'order_status_id',
        'modifier_id',
        'note',
        'employee_evidence',
        'customer_confirmation',
        'is_current',
    ];

    protected $casts = [
        'is_current' => 'boolean',
        'customer_confirmation' => 'boolean',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class, 'order_status_id');
    }

    public function modifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'modifier_id');
    }
}
