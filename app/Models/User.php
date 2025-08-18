<?php

namespace App\Models;

use App\Mail\CustomResetPasswordMail;
use App\Mail\CustomVerifyEmail;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Mail ;
use Illuminate\Support\Facades\Url;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
    
    /**
     * Kiểm tra người dùng có vai trò cụ thể không
     *
     * @param string|array $roles
     * @return bool
     */

    protected static function booted()
    {
        static::deleting(function ($user){
            DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->delete();
        });
    }
    public function hasRole($roles)
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles);
        }
        
        return $this->role === $roles;
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birthday' => 'datetime',
        ];
    }
    //Gửi mail đăng ký
    public function sendEmailVerificationNotification()
    {
        $verificationUrl = \URL::temporarySignedRoute(
            'verification.verify', now()
            ->addMinutes(60),
            [
                'id' => $this->getKey(),
                'hash' =>sha1($this->getEmailForVerification())
            ]
            
        );
        Mail::to($this->email)->send(new CustomVerifyEmail($verificationUrl, $this));
    }
    //Gửi mail reset password
    public function sendPasswordResetNotification($token)
    {
        $resetUrl = Url::temporarySignedRoute(
            'password.reset',
            Carbon::now()->addMinutes(config('auth.passwords.users.expire')),
            ['token' => $token, 'email' => $this->email]
        );
        Mail::to($this->email)->send(new CustomResetPasswordMail($resetUrl, $this));
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
    
    /**
     * Get all blog posts created by this user.
     */
    public function posts()
    {
        return $this->hasMany(BlogPost::class, 'author_id');
    }
}
