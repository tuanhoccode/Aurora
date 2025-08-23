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
        'sale_starts_at',
        'sale_ends_at',
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
        'sale_starts_at' => 'datetime',
        'sale_ends_at' => 'datetime',
        'is_sale' => 'boolean',
        'is_active' => 'boolean',
        'views' => 'integer',
        'stock' => 'integer',
    ];


    public function galleries()
    {
        return $this->hasMany(ProductImage::class);
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


    public function orderItems()
    {
        return $this->hasMany(\App\Models\OrderItem::class, 'product_id');
    }


    public function getIsOnSaleAttribute()
    {
        // On sale only if sale_price is valid and current time is within optional window
        if (!$this->sale_price || $this->sale_price >= $this->price) {
            return false;
        }
        $now = now();
        if ($this->sale_starts_at && $now->lt($this->sale_starts_at)) {
            return false;
        }
        if ($this->sale_ends_at && $now->gt($this->sale_ends_at)) {
            return false;
        }
        return true;
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
        $now = now();
        return $query->where('is_sale', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('sale_starts_at')->orWhere('sale_starts_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('sale_ends_at')->orWhere('sale_ends_at', '>=', $now);
            });
    }


    public function getImageUrlAttribute()
    {
        if (!$this->thumbnail) {
            return asset('assets2/img/product/2/prodcut-1.jpg');
        }
        // Kiểm tra xem file có tồn tại không
        if (Storage::disk('public')->exists($this->thumbnail)) {
            return asset('storage/' . $this->thumbnail);
        }
        // Nếu không tồn tại, thử với đường dẫn khác
        if (strpos($this->thumbnail, 'products/') === 0) {
            return asset('storage/' . $this->thumbnail);
        }
        return asset('storage/products/' . $this->thumbnail);
    }


    public function reviews()
    {
        return $this->hasMany(Review::class)->where('is_active', 1);
    }


    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function getDefaultVariantIdAttribute()
    {
        // Lấy biến thể đầu tiên còn hàng, hoặc biến thể đầu tiên nếu không còn hàng
        $variant = $this->variants()->orderBy('id')->where('stock', '>', 0)->first();
        if (!$variant) {
            $variant = $this->variants()->orderBy('id')->first();
        }
        return $variant ? $variant->id : null;
    }


    public function getSuccessfulOrderItems()
    {
        return $this->orderItems()->whereHas('order.currentOrderStatus', function($q) {
            $q->where('order_status_id', 4)->where('is_current', 1);
        });
    }


    public function relatedProducts($limit = 10)
    {
        $categoryIds = $this->categories()->pluck('categories.id');
        $related = Product::where('id', '!=', $this->id)
            ->where('is_active', 1)
            ->where('stock', '>', 0)
            ->whereHas('categories', function($q) use ($categoryIds) {
                $q->whereIn('categories.id', $categoryIds);
            })
            ->inRandomOrder()
            ->take($limit)
            ->get();


        if ($related->count() < $limit) {
            $more = Product::where('id', '!=', $this->id)
                ->where('is_active', 1)
                ->where('stock', '>', 0)
                ->whereNotIn('id', $related->pluck('id')->push($this->id))
                ->inRandomOrder()
                ->take($limit - $related->count())
                ->get();
            $related = $related->concat($more);
        }
        return $related;
    }


    /**
     * Lấy sản phẩm thay thế cho sản phẩm ngừng kinh doanh
     * Ưu tiên sản phẩm cùng thương hiệu, cùng danh mục, có giá tương đương
     */
    public function getReplacementProducts($limit = 10)
    {
        $categoryIds = $this->categories()->pluck('categories.id');
        $brandId = $this->brand_id;
        $priceRange = [
            'min' => $this->price * 0.7, // 70% giá gốc
            'max' => $this->price * 1.3  // 130% giá gốc
        ];


        // Ưu tiên 1: Cùng thương hiệu, cùng danh mục, giá tương đương
        $replacement = Product::where('id', '!=', $this->id)
            ->where('is_active', 1)
            ->where('stock', '>', 0)
            ->where('brand_id', $brandId)
            ->whereHas('categories', function($q) use ($categoryIds) {
                $q->whereIn('categories.id', $categoryIds);
            })
            ->whereBetween('price', [$priceRange['min'], $priceRange['max']])
            ->orderBy('price', 'asc')
            ->take($limit)
            ->get();


        // Nếu chưa đủ, ưu tiên 2: Cùng thương hiệu, giá tương đương
        if ($replacement->count() < $limit) {
            $more = Product::where('id', '!=', $this->id)
                ->where('is_active', 1)
                ->where('stock', '>', 0)
                ->where('brand_id', $brandId)
                ->whereBetween('price', [$priceRange['min'], $priceRange['max']])
                ->whereNotIn('id', $replacement->pluck('id')->push($this->id))
                ->orderBy('price', 'asc')
                ->take($limit - $replacement->count())
                ->get();
            $replacement = $replacement->concat($more);
        }


        // Nếu chưa đủ, ưu tiên 3: Cùng danh mục, giá tương đương
        if ($replacement->count() < $limit) {
            $more = Product::where('id', '!=', $this->id)
                ->where('is_active', 1)
                ->where('stock', '>', 0)
                ->whereHas('categories', function($q) use ($categoryIds) {
                    $q->whereIn('categories.id', $categoryIds);
                })
                ->whereBetween('price', [$priceRange['min'], $priceRange['max']])
                ->whereNotIn('id', $replacement->pluck('id')->push($this->id))
                ->orderBy('price', 'asc')
                ->take($limit - $replacement->count())
                ->get();
            $replacement = $replacement->concat($more);
        }


        // Nếu chưa đủ, ưu tiên 4: Cùng thương hiệu
        if ($replacement->count() < $limit) {
            $more = Product::where('id', '!=', $this->id)
                ->where('is_active', 1)
                ->where('stock', '>', 0)
                ->where('brand_id', $brandId)
                ->whereNotIn('id', $replacement->pluck('id')->push($this->id))
                ->orderBy('price', 'asc')
                ->take($limit - $replacement->count())
                ->get();
            $replacement = $replacement->concat($more);
        }


        // Nếu chưa đủ, ưu tiên 5: Cùng danh mục
        if ($replacement->count() < $limit) {
            $more = Product::where('id', '!=', $this->id)
                ->where('is_active', 1)
                ->where('stock', '>', 0)
                ->whereHas('categories', function($q) use ($categoryIds) {
                    $q->whereIn('categories.id', $categoryIds);
                })
                ->whereNotIn('id', $replacement->pluck('id')->push($this->id))
                ->orderBy('price', 'asc')
                ->take($limit - $replacement->count())
                ->get();
            $replacement = $replacement->concat($more);
        }


        // Cuối cùng, lấy bất kỳ sản phẩm nào còn hoạt động và có hàng
        if ($replacement->count() < $limit) {
            $more = Product::where('id', '!=', $this->id)
                ->where('is_active', 1)
                ->where('stock', '>', 0)
                ->whereNotIn('id', $replacement->pluck('id')->push($this->id))
                ->inRandomOrder()
                ->take($limit - $replacement->count())
                ->get();
            $replacement = $replacement->concat($more);
        }


        return $replacement;
    }


    //đổ sao trung bình ra home
    public function getAverageRatingAttribute(){
        return round($this->reviews()->where('is_active', 1)->avg('rating'),1) ?? 0;
    }
    public function wishlists()
{
    return $this->hasMany(Wishlist::class);
}


}



