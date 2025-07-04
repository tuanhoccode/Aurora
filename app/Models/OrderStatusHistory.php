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

    /**
     * Đơn hàng liên quan
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Trạng thái tương ứng (ví dụ: Đang giao, Giao thành công)
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class, 'order_status_id');
    }

    /**
     * Người thay đổi trạng thái (nhân viên hoặc hệ thống)
     */
    public function modifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'modifier_id');
    }
}