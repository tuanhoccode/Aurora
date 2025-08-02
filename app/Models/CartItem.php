<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id', 'product_id', 'product_variant_id', 'quantity', 'price_at_time'
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class, 'product_id');
    }

    public function productVariant()
    {
        return $this->belongsTo(\App\Models\ProductVariant::class, 'product_variant_id');
    }
}
