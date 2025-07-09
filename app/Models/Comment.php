<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'user_id',
        'parent_id',
        'reason',
        'is_active',
        'content',
    ];
    public function product(){
        return $this->belongsTo(Product::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function parent(){
        return $this->belongsTo(Comment::class);
    }
    public function children(){
        return $this->hasMany(Comment::class, 'parent_id');
    }
}
