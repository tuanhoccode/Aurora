<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'user_id', 'status',
    ];

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class, 'product_id');
    }

    /**
     * Calculate the total price of the cart
     * Note: This doesn't save to database as total_price column doesn't exist
     */
    public function updateTotalPrice()
    {
        // Just return the total price without saving to database
        return $this->items()->with('product')
            ->get()
            ->sum(function ($item) {
                return $item->quantity * $item->price;
            });
    }
}
