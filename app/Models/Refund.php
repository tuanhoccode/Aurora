<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    protected $guarded = []; // Cho phép mass assignment

    protected $casts = [
        'status' => 'string',
        'bank_account_status' => 'string',
        'is_send_money' => 'boolean',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function refundItems()
    {
        return $this->hasMany(RefundItem::class);
    }
}