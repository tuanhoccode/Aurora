<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminRepliedNotification extends Notification
{
    use Queueable;
    protected $parent;
    protected $originalText;
    protected $replyText;

    /**
     * Create a new notification instance.
     */
    public function __construct($parent,$originalText, $replyText )
    {
        $this->parent = $parent;
        $this->originalText = $originalText;
        $this->replyText = $replyText;
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
            ->subject('Phản hồi từ quản trị viên')
            ->greeting('Xin chào' . ($notifiable->fullname ?? $notifiable->name ?? 'Quý khách'))
            ->line('Bình luận hoặc đánh giá của bạn về sản phẩm '. optional($this->parent->product)->name ?? 'sản phẩm')
            ->line($this->originalText)
            ->line('Nội dung phản hồi: ')
            ->line($this ->replyText)
            ->action('Xem sản phẩm',  route('client.product.show', $this->parent->product->slug ?? optional($this->parent->product)->slug))
            ->line('Cảm ơn bạn đã đóng góp ý kiến để Shop Aurora được hoàn thiện hơn');
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
