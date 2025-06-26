<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'stock',
        'regular_price',
        'sale_price',
        'img'
    ];

    protected $attributes = [
        'regular_price' => 0,
        'sale_price' => 0,
    ];

    protected $casts = [
        'stock' => 'integer',
        'regular_price' => 'decimal:2',
        'sale_price' => 'decimal:2'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class, 'attribute_value_product', 'product_id', 'attribute_value_id')
                    ->using(AttributeValue::class)
                    ->withPivot(['attribute_value_id'])
                    ->withTimestamps();
    }

    // Quan hệ giá trị thuộc tính thông qua bảng attribute_value_product_variant (liên kết theo product_variant_id)
    public function attributeValues()
    {
        return $this->belongsToMany(
            \App\Models\AttributeValue::class,
            'attribute_value_product_variant',
            'product_variant_id',
            'attribute_value_id'
        )->with('attribute');
    }

    public function getIsOnSaleAttribute()
    {
        return $this->sale_price && $this->sale_price < $this->regular_price;
    }

    public function getCurrentPriceAttribute()
    {
        return $this->is_on_sale ? $this->sale_price : $this->regular_price;
    }

    public function getDiscountPercentAttribute()
    {
        if ($this->is_on_sale) {
            return round((($this->regular_price - $this->sale_price) / $this->regular_price) * 100);
        }
        return 0;
    }

    public function images()
    {
        return $this->hasMany(ProductGallery::class, 'product_variant_id');
    }

    public function productVariant()
    {
        return $this->belongsTo(\App\Models\ProductVariant::class, 'product_variant_id');
    }

}
