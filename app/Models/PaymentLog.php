<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentLog extends Model
{
    protected $table = 'payment_logs';

    protected $fillable = [
        'order_id',
        'txn_ref',
        'response_code',
        'transaction_no',
        'amount',
        'bank_code',
        'response_data',
    ];

    /**
     * Quan hệ đến đơn hàng
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}