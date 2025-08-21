<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefundItem extends Model
{
    protected $table = 'refund_items';

    protected $fillable = [
        'refund_id',
        'product_id',
        'variant_id',
        'name',
        'name_variant',
        'quantity',
        'price',
        'price_variant',
        'quantity_variant',
        'created_at',
        'updated_at',
    ];

    public function refund()
    {
        return $this->belongsTo(Refund::class);
    }
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
