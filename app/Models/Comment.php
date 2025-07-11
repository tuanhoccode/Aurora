<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'product_id',
        'user_id',
        'parent_id',
        'reason',
        'is_active',
        'content',
        'deleted_at',
    ];
    public function product(){
        return $this->belongsTo(Product::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function children(){
        return $this->hasMany(Comment::class, 'parent_id');
    }
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }
    
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }
    public function getHasRepliesAttribute(){
        return $this->replies()->exists();
    }
}
