<?php

namespace App\Models;

use Carbon\Carbon;
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
        'discount_amount',
        'shipping_type',
        'shipping_fee',
        'is_paid',
        'img_refunded_money',
        'cancel_reason',
        'cancel_note',
        'cancelled_at',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    
    // Alias for items() for compatibility
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function orderStatuses()
    {
        return $this->hasMany(OrderOrderStatus::class, 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Lịch sử trạng thái nhiều bản ghi
    public function statusHistory()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    // Trạng thái hiện tại (is_current = true)
    public function currentStatus()
    {
        return $this->hasOne(OrderStatusHistory::class)
            ->where('is_current', true)
            ->with('status')
            ->orderByDesc('created_at');
    }

    // Trạng thái hiện tại từ order_order_status (is_current = true)
    public function currentOrderStatus()
    {
        return $this->hasOne(\App\Models\OrderOrderStatus::class)
            ->where('is_current', true)
            ->with('status')
            ->orderByDesc('created_at');
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

    public function getPaymentStatusBadgeAttribute()
    {
        return $this->is_paid
            ? '<span class="badge bg-success">Đã thanh toán</span>'
            : '<span class="badge bg-warning text-dark">Chưa thanh toán</span>';
    }


    public function getDeliveryTypeFullInfoAttribute()
    {
        return match ($this->shipping_type) {
            'thường' => 'Giao hàng thường',
            'nhanh'  => 'Giao hàng nhanh',
            default  => 'Không rõ hình thức',
        };
    }

    /**
     * Kiểm tra xem đơn hàng có thể hủy được không
     */
    public function canBeCancelled()
    {
        $currentStatusName = optional(optional($this->currentStatus)->status)->name;
        // Nếu không có trạng thái, mặc định cho phép hủy
        return $currentStatusName === 'Chờ xác nhận' || is_null($currentStatusName);
    }

    /**
     * Kiểm tra xem đơn hàng đã bị hủy chưa
     */
    public function isCancelled()
    {
        $currentStatusName = optional(optional($this->currentStatus)->status)->name;
        return $currentStatusName === 'Đã hủy';
    }

    public function getShippingFeeFormattedAttribute()
    {
        return number_format($this->shipping_fee ?? 0, 0, ',', '.') . 'đ';
    }
    public function isEligibleForRefund()
    {
        $currentStatus = $this->orderStatuses()->where('is_current', 1)->first();
        if (!$currentStatus || !in_array($currentStatus->order_status->name, ['Giao hàng thành công', 'Gửi hàng'])) {
            return false;
        }

        $completedDate = $this->orderStatuses()->whereHas('order_status', function ($query) {
            $query->where('name', 'Giao hàng thành công');
        })->first()?->created_at;

        if (!$completedDate || Carbon::now()->diffInDays($completedDate) > 7) {
            return false;
        }

        return $this->is_paid || ($this->payment && $this->payment->name == 'COD');
    }
    public function refund()
    {
        return $this->hasOne(Refund::class);
    }
    public function statusHistories()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }
}
