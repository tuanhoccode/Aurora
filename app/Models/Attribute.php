<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Attribute extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($attribute) {
            $attribute->slug = $attribute->slug ?? Str::slug($attribute->name);
        });

        static::updating(function ($attribute) {
            if ($attribute->isDirty('name') && !$attribute->isDirty('slug')) {
                $attribute->slug = Str::slug($attribute->name);
            }
        });
    }

    public function values()
    {
        return $this->hasMany(AttributeValue::class);
    }

    public function products()
    {
        return $this->hasManyThrough(
            Product::class,
            AttributeValue::class,
            'attribute_id',
            'id',
            'id',
            'id'
        );
    }
} 