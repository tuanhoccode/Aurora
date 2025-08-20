<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'product_id',
        'order_id',
        'user_id',
        'order_item_id',
        'rating',
        'review_text',
        'review_id',
        'reason',
        'is_active',
        'has_replies',
        'deleted_at',
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
    public function order(){
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
    public function orderItemId()
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }
    public function images(){
        return $this->hasMany(ReviewImage::class);
    }
    //Thêm biến thể ở bình luận 
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id', 'id');
    }
    
}
