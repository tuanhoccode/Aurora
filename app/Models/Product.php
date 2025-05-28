<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Giả định có sử dụng SoftDeletes

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'sku',
        'short_description',
        'description',
        'price',
        'compare_price',
        'cost',
        'stock',
        'category_id',
        'brand_id',
        'status',
        'thumbnail',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'cost' => 'decimal:2',
        'stock' => 'integer',
        'is_active' => 'boolean', // Giả định status cũng có thể là boolean
        'deleted_at' => 'datetime',
    ];

    // Định nghĩa quan hệ với Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Định nghĩa quan hệ với Brand
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    // Accessor hoặc method cho URL thumbnail nếu cần
    // public function getThumbnailUrlAttribute()
    // {
    //     return $this->thumbnail ? asset('storage/' . $this->thumbnail) : null;
    // }
} 