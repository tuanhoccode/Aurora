<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = ['name', 'logo', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public $timestamps = true;

    // Trả về URL đầy đủ cho logo
    public function getLogoUrlAttribute()
    {
        return $this->logo ? asset('storage/brands/' . $this->logo) : null;
    }
}
