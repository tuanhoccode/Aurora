<?php

namespace App\Models;

use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements CanResetPassword, MustVerifyEmail
{
    use HasFactory, Notifiable, CanResetPasswordTrait;

    protected $fillable = [
        'fullname',
        'email',
        'password',
        'phone_number',
        'avatar',
        'gender',
        'birthday',
        'role',
        'status',
        'bank_name',
        'user_bank_name',
        'bank_account',
        'reason_lock',
        'is_change_password',
        'email_verified_at',
        'google_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birthday' => 'datetime',
        ];
    }

    public function address()
    {
        return $this->hasOne(UserAddress::class)->where('is_default', 1);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }
    // Lấy danh sách sản phẩm đã yêu thích
    public function favoriteProducts()
    {
        return $this->belongsToMany(Product::class, 'wishlists', 'user_id', 'product_id');
    }
}
