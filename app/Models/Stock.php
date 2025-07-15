<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    // 👉 Rất quan trọng: khai báo đúng tên bảng
    protected $table = 'product_stocks';

    protected $fillable = ['product_id', 'stock'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
