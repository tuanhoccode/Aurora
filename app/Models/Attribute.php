<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attribute extends Model
{
    use SoftDeletes;

    protected $table = 'attributes';

    protected $fillable = [
        'name',
        'is_variant',
        'is_active',
    ];

    protected $casts = [
        'is_variant' => 'boolean',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function values()
    {
        return $this->hasMany(AttributeValue::class);
    }
    public function attributeValues()
    {
        return $this->hasMany(AttributeValue::class);
    }
}