<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Brand extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public $timestamps = true;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($brand) {
            if (empty($brand->slug)) {
                $brand->slug = Str::slug($brand->name);
            }
        });

        static::updating(function ($brand) {
            if ($brand->isDirty('name')) {
                $brand->slug = Str::slug($brand->name);
            }
        });
    }

    // Trả về URL đầy đủ cho logo
    public function getLogoUrlAttribute()
    {
        if (!$this->logo) {
            return null;
        }

        // Kiểm tra xem logo có tồn tại trong storage không
        if (Storage::disk('public')->exists($this->logo)) {
            return asset('storage/' . $this->logo);
        }

        // Nếu không tìm thấy trong storage, trả về null
        return null;
    }
}
