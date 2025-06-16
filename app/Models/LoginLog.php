<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    protected $fillable = [
        'user_id', 'session_id', 'ip_address', 'user_agent', 'logged_in_at', 'is_current'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
