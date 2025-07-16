<?php

namespace App\Models;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'code',
        'user_id',
        'payment_id',
        'phone_number',
        'email',
        'fullname',
        'address',
        'note',
        'total_amount',
        'is_paid',
        'is_refunded',
        'coupon_id',
        'is_refunded_canceled',
        'check_refunded_canceled',
        'img_refunded_money'
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function statusHistory()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function currentStatus()
    {
        return $this->hasOne(OrderStatusHistory::class)
            ->where('is_current', true)
            ->with('status');
    }

    public function status()
    {
        return $this->belongsTo(OrderStatus::class, 'status_id', 'id');
    }
    public function orderDetail()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }
    public function getFulfilmentStatusBadgeAttribute()
    {
        $status = optional(optional($this->currentStatus)->status)->name;

        return match ($status) {
            'Chờ xác nhận'     => '<span class="badge bg-warning text-dark">Chờ xác nhận</span>',
            'Chờ lấy hàng'     => '<span class="badge bg-info text-dark">Chờ lấy hàng</span>',
            'Đang giao'        => '<span class="badge bg-primary">Đang giao</span>',
            'Giao hàng thành công' => '<span class="badge bg-success">Đã giao</span>',
            'Đã hủy'           => '<span class="badge bg-danger">Đã huỷ</span>',
            'Chờ trả hàng'     => '<span class="badge bg-secondary">Chờ trả hàng</span>',
            'Đã trả hàng'      => '<span class="badge bg-secondary">Đã trả hàng</span>',
            'Gửi hàng'         => '<span class="badge bg-info">Gửi hàng</span>',
            'Hoàn tiền'        => '<span class="badge bg-dark text-white">Đã hoàn tiền</span>',
            default            => '<span class="badge bg-secondary">Không xác định</span>',
        };
    }
}
