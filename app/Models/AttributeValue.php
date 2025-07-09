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

    // Define the many-to-many relationship with Product
    public function products()
    {
        return $this->belongsToMany(Product::class, 'attribute_value_product', 'attribute_value_id', 'product_id')
            ->withTimestamps();
    }

    // Relationship with Attribute
    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }
}
