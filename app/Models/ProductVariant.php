<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'sale_price',
        'stock',
        'is_active'
    ];

    protected $casts = [
        'price' => 'float',
        'sale_price' => 'float',
        'stock' => 'integer',
        'is_active' => 'boolean'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function attributeValues()
    {
        return $this->belongsToMany(AttributeValue::class, 'attribute_value_variant');
    }

    public function getIsOnSaleAttribute()
    {
        return $this->sale_price && $this->sale_price < $this->price;
    }

    public function getCurrentPriceAttribute()
    {
        return $this->is_on_sale ? $this->sale_price : $this->price;
    }

    public function getDiscountPercentAttribute()
    {
        if ($this->is_on_sale) {
            return round((($this->price - $this->sale_price) / $this->price) * 100);
        }
        return 0;
    }
}
