<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewRejectedNotification extends Notification
{
    use Queueable;

    protected $review;
    protected $reason;
    /**
     * Create a new notification instance.
     */
    public function __construct(Review $review, $reason)
    {
        $this->review = $review;
        $this->reason = $reason;

    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Đánh giá của bạn đã bị từ chối')
            ->greeting('Xin chào ' . $notifiable->fullname)
            ->line('Đánh giá của bạn cho sản phẩm "' . $this->review->product->name . '" đã bị từ chối.')
            ->line('Lý do: ' . $this->reason)
            ->line('Nội dung bạn đã đánh giá:')
            ->line('"' . $this->review->review_text . '"')
            ->line('Cảm ơn bạn đã mua sắm tại cửa hàng của chúng tôi.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
