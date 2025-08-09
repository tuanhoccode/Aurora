<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'image',
        'link',
        'position',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return asset('assets2/img/banner/banner-slider-1.png');
        }
        
        // Nếu ảnh đã có đường dẫn đầy đủ
        if (strpos($this->image, 'http') === 0) {
            return $this->image;
        }
        
        // Nếu ảnh được lưu trong storage
        if (Storage::disk('public')->exists($this->image)) {
            return asset('storage/' . $this->image);
        }
        
        // Fallback
        return asset('storage/banners/' . $this->image);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByPosition($query, $position)
    {
        return $query->where('position', $position);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('created_at', 'desc');
    }

    /**
     * Kiểm tra xem sort_order có bị trùng không
     */
    public static function isSortOrderUnique($sortOrder, $excludeId = null)
    {
        $query = static::where('sort_order', $sortOrder);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return !$query->exists();
    }

    /**
     * Lấy sort_order tiếp theo có sẵn
     */
    public static function getNextAvailableSortOrder()
    {
        $maxSortOrder = static::max('sort_order');
        return $maxSortOrder ? $maxSortOrder + 1 : 1;
    }
} 