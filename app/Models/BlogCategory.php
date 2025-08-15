<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogCategory extends Model
{
    use SoftDeletes;
    
    protected $dates = ['deleted_at'];
    protected $table = 'blog_categories';

    protected $fillable = [
        'name',
        'parent_id',
        'is_active',
        'created_at',
        'updated_at'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
    ];
    
    public const IS_ACTIVE = 1;
    public const IS_INACTIVE = 0;
    
    public static function getStatuses()
    {
        return [
            self::IS_ACTIVE => 'Hoạt động',
            self::IS_INACTIVE => 'Không hoạt động',
        ];
    }

    public function posts()
    {
        return $this->hasMany(BlogPost::class, 'category_id');
    }

    public function children()
    {
        return $this->hasMany(BlogCategory::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(BlogCategory::class, 'parent_id');
    }
}
