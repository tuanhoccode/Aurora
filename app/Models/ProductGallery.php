<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductGallery extends Model
{
    protected $fillable = [
        'product_id',
        'product_variant_id',
        'url',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    // Accessor để lấy URL ảnh
    public function getImageUrlAttribute()
    {
        if (!$this->url) {
            return asset('assets2/img/product/2/default.png');
        }
        return asset('storage/' . $this->url);
    }
}
