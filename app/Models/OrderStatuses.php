<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatuses extends Model
{
    protected $table = 'order_statuses';

    protected $fillable = [
        'name',
    ];

    public function orderStatuses()
    {
        return $this->hasMany(OrderStatus::class);
    }
}

?>
