<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'order_id',
        'user_id',
        'rating',
        'review_text',
        'review_id',
        'reason',
        'is_active',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function parent()
    {
        return $this->belongsTo(Review::class, 'review_id');
    }
    
    public function replies()
    {
        return $this->hasMany(Review::class, 'review_id')->where('is_active', 1);
    }
    public function getHasRepliesAttribute(){
        return $this->replies()->exists();
    }
}
