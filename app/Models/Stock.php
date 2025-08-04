<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    // Khai báo bảng đúng với tên thật
    protected $table = 'product_stocks';

    protected $fillable = [
        'product_id',
        'product_variant_id',
        'stock'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
