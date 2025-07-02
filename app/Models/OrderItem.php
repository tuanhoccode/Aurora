<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_variant_id',
        'name',
        'price',
        'old_price',
        'old_price_variant',
        'quantity',
        'name_variant',
        'attributes_variant',
        'price_variant',
        'quantity_variant'
    ];

    protected $casts = [
        'price' => 'float',
        'old_price' => 'float',
        'old_price_variant' => 'float',
        'price_variant' => 'float',
        'quantity' => 'integer',
        'quantity_variant' => 'integer',
        'attributes_variant' => 'array'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function getPriceAttribute($value): float
    {
        return number_format($value, 0, ',', '.');
    }

    public function getOldPriceAttribute($value): float
    {
        return number_format($value, 0, ',', '.');
    }

    public function getPriceVariantAttribute($value): float
    {
        return number_format($value, 0, ',', '.');
    }

    public function getOldPriceVariantAttribute($value): float
    {
        return number_format($value, 0, ',', '.');
    }
}
