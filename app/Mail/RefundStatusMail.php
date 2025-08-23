<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use App\Models\Refund;

class RefundStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $refund;

    public function __construct(Refund $refund)
    {
        $this->refund = $refund;
    }

    public function build()
    {
        $email = $this->subject('Cập Nhật Trạng Thái Yêu Cầu Hoàn Tiền')
                    ->view('emails.refund_status_notification');

        // Đính kèm reason_image nếu có
        if ($this->refund->reason_image && Storage::disk('public')->exists($this->refund->reason_image)) {
            $email->attach(Storage::disk('public')->path($this->refund->reason_image), [
                'as' => 'reason_image.' . pathinfo($this->refund->reason_image, PATHINFO_EXTENSION),
                'mime' => mime_content_type(Storage::disk('public')->path($this->refund->reason_image)),
            ]);
        }

        // Đính kèm admin_reason_image nếu trạng thái là completed
        if ($this->refund->status === 'completed' && $this->refund->admin_reason_image && Storage::disk('public')->exists($this->refund->admin_reason_image)) {
            $email->attach(Storage::disk('public')->path($this->refund->admin_reason_image), [
                'as' => 'admin_reason_image.' . pathinfo($this->refund->admin_reason_image, PATHINFO_EXTENSION),
                'mime' => mime_content_type(Storage::disk('public')->path($this->refund->admin_reason_image)),
            ]);
        }

        return $email;
    }
}