<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    protected $table = 'order_order_status';

    protected $fillable = [
        'order_id',
        'order_status_id',
        'modified_by',
        'note',
        'employee_evidence',
        'customer_confirmation',
        'is_current',
    ];

    protected $casts = [
        'is_current' => 'boolean',
        'customer_confirmation' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function status()
    {
        return $this->belongsTo(OrderStatus::class, 'order_status_id');
    }

    public function modifier()
    {
        return $this->belongsTo(User::class, 'modified_by');
    }

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('customer_confirmation', true);
    }
}

?>
