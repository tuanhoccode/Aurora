<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AttributeValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'attribute_id',
        'value',
        'slug',
        'color_code'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($value) {
            $value->slug = $value->slug ?? Str::slug($value->value);
        });

        static::updating(function ($value) {
            if ($value->isDirty('value') && !$value->isDirty('slug')) {
                $value->slug = Str::slug($value->value);
            }
        });
    }

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'attribute_value_product');
    }

    public function productVariants()
    {
        return $this->belongsToMany(ProductVariant::class, 'attribute_value_product_variant');
    }
} 