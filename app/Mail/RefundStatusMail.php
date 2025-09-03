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
    public $refundInfo;

    public function __construct(Refund $refund, ?array $refundInfo = null)
    {
        $this->refund = $refund;
        $this->refundInfo = $refundInfo;
    }

    public function build()
    {
        $email = $this->subject('Cập Nhật Trạng Thái Yêu Cầu Hoàn Tiền')
            ->view('emails.refund_status_notification');


        // Đính kèm reason_image
        if ($this->refund->reason_image && Storage::disk('public')->exists($this->refund->reason_image)) {
            $filePath = Storage::disk('public')->path($this->refund->reason_image);
            $email->attach($filePath, [
                'as' => 'reason_image.' . pathinfo($this->refund->reason_image, PATHINFO_EXTENSION),
                'mime' => mime_content_type($filePath),
                'cid' => 'reason_image',
            ]);
            \Log::info('Attached reason_image', ['refund_id' => $this->refund->id, 'file' => $filePath]);
        } else {
            \Log::warning('reason_image does not exist', [
                'refund_id' => $this->refund->id,
                'path' => $this->refund->reason_image,
            ]);
        }

        // Đính kèm admin_reason_image hoặc img_refunded_money
        $adminImagePath = $this->refund->admin_reason_image ?? $this->refund->order->img_refunded_money;
        if ($this->refund->status === 'completed' && $adminImagePath && Storage::disk('public')->exists($adminImagePath)) {
            $filePath = Storage::disk('public')->path($adminImagePath);
            $email->attach($filePath, [
                'as' => 'admin_reason_image.' . pathinfo($adminImagePath, PATHINFO_EXTENSION),
                'mime' => mime_content_type($filePath),
                'cid' => 'admin_reason_image',
            ]);
            \Log::info('Attached admin_reason_image', ['refund_id' => $this->refund->id, 'file' => $filePath]);
        } else {
            \Log::warning('admin_reason_image or img_refunded_money does not exist', [
                'refund_id' => $this->refund->id,
                'path' => $adminImagePath,
            ]);
        }

        return $email;
    }
}
