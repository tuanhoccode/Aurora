<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductGallery extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'image',
        'created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
