<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefundItem extends Model
{
    protected $guarded = []; // Cho phép mass assignment

    public function refund()
    {
        return $this->belongsTo(Refund::class);
    }
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}
