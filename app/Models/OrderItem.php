<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $table = 'order_items';

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
        'quantity_variant',
    ];
    public $timestamps = false; // Tắt timestamps để tránh thêm updated_at

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}