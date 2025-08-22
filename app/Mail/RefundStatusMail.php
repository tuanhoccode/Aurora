<?php
namespace App\Mail;

use App\Models\Refund;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RefundStatusMail extends Mailable
{
    use SerializesModels;

    public $refund;

    public function __construct($refund)
    {
        $this->refund = $refund;
    }

    public function build()
    {
        return $this->subject('Cập nhật trạng thái hoàn tiền')
                    ->view('emails.refund_status')
                    ->with([
                        'order_code' => $this->refund->order_id, // Truyền biến vào view
                        'status' => $this->refund->status,
                        'admin_reason' => $this->refund->admin_reason,
                    ]);
    }
}