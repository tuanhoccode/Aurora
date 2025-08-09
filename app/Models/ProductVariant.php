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
        // Đã xóa sale_price khỏi đây để cho phép null
    ];

    protected $casts = [
        'stock' => 'integer',
        'regular_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'img' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
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
        )->select(['attribute_values.id', 'attribute_values.attribute_id', 'attribute_values.value', 'attribute_values.is_active'])
         ->with('attribute');
    }

    public function orderItems()
    {
        return $this->hasMany(\App\Models\OrderItem::class, 'product_variant_id');
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
        return $this->hasMany(ProductImage::class, 'product_variant_id');
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /**
     * Kiểm tra biến thể có nằm trong đơn hàng đang xử lý không
     * (order_status_id = 1, 2, 3)
     */
    public function isInProcessingOrder()
    {
        return $this->orderItems()->whereHas('order.statusHistory', function($q) {
            $q->where('is_current', 1)
              ->whereIn('order_status_id', [1, 2, 3]);
        })->exists();
    }

    /**
     * Lấy danh sách mã đơn hàng mà biến thể đang thuộc các trạng thái xử lý (1,2,3)
     */
    public function getProcessingOrderCodes()
    {
        return $this->orderItems()->whereHas('order.statusHistory', function($q) {
            $q->where('is_current', 1)
              ->whereIn('order_status_id', [1, 2, 3]);
        })->with(['order' => function($q) {
            $q->select('id', 'code');
        }])->get()->pluck('order.code')->unique()->toArray();
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($variant) {
            // Xóa toàn bộ ảnh phụ của biến thể này
            $variant->images()->delete();
        });
    }
}

