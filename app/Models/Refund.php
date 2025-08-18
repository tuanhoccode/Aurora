<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    protected $table = 'refunds';
    
    protected $fillable = [
        'order_id',
        'user_id',
        'total_amount',
        'bank_account',
        'user_bank_name',
        'bank_name',
        'reason',
        'admin_reason',
        'reason_image',
        'status',
        'bank_account_status',
        'is_send_money',
        'created_at',
        'updated_at',
    ];

    public function items()
    {
        return $this->hasMany(RefundItem::class, 'refund_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
