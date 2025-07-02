<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'brand_id',
        'description',
        'short_description',
        'sku',
        'price',
        'sale_price',
        'type',
        'thumbnail',
        'is_active',
        'is_sale',
        'views',
        'stock',
        'digital_file',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'is_sale' => 'boolean',
        'is_active' => 'boolean',
        'views' => 'integer',
        'stock' => 'integer',
    ];

    public function galleries()
    {
        return $this->hasMany(ProductGallery::class);
    }

    protected static function boot()
    {
        parent::boot();

        // Tạo slug khi tạo mới sản phẩm
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
                // Kiểm tra xem slug đã tồn tại chưa
                $count = 1;
                while (Product::where('slug', $product->slug)->exists()) {
                    $product->slug = Str::slug($product->name) . '-' . $count;
                    $count++;
                }
            }
        });

        // Cập nhật slug khi tên sản phẩm thay đổi
        static::updating(function ($product) {
            if ($product->isDirty('name')) {
                $originalSlug = $product->getOriginal('slug');
                $newSlug = Str::slug($product->name);

                // Kiểm tra xem slug đã tồn tại chưa
                $count = 1;
                while (Product::where('slug', $newSlug)->where('id', '!=', $product->id)->exists()) {
                    $newSlug = Str::slug($product->name) . '-' . $count;
                    $count++;
                }

                $product->slug = $newSlug;
            }
        });

        // Đảm bảo không xóa mềm sản phẩm khi cập nhật
        static::updating(function ($product) {
            if ($product->trashed()) {
                $product->restore();
            }
        });

        // Ngăn chặn xóa mềm khi cập nhật
        static::updated(function ($product) {
            if ($product->trashed()) {
                $product->restore();
            }
        });
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function attributeValues()
    {
        return $this->belongsToMany(AttributeValue::class, 'attribute_value_product');
    }

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'product_attribute')
            ->withPivot('values')
            ->withTimestamps();
    }

    public function getIsOnSaleAttribute()
    {
        return $this->sale_price && $this->sale_price < $this->price;
    }

    public function getCurrentPriceAttribute()
    {
        return $this->is_on_sale ? $this->sale_price : $this->price;
    }

    public function getDiscountPercentAttribute()
    {
        if ($this->is_on_sale) {
            return round((($this->price - $this->sale_price) / $this->price) * 100);
        }
        return 0;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeLowStock($query, $threshold = 10)
    {
        return $query->where('stock', '<=', $threshold)
                    ->where('stock', '>', 0);
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('stock', 0);
    }

    public function scopeOnSale($query)
    {
        return $query->where('is_sale', true);
    }

    public function getImageUrlAttribute()
    {
        if (!$this->thumbnail) {
            return asset('assets2/img/product/2/default.png');
        }
        if (strpos($this->thumbnail, 'products/') === 0) {
            return asset('storage/' . $this->thumbnail);
        }
        return asset('storage/products/' . $this->thumbnail);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }


}
