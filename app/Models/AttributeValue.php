<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttributeValue extends Model
{
    use SoftDeletes;

    protected $table = 'attribute_values';

    protected $fillable = [
        'attribute_id',
        'value',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

   public function productVariants()
    {
        return $this->belongsToMany(ProductVariant::class, 'attribute_value_product_variant', 'attribute_value_id', 'product_variant_id');
    }
}