<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class OrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;
    
    public $order;
    public $user;
    
    /**
     * Create a new message instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->user = $order->user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Xác nhận đơn hàng #' . $this->order->code,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'client.emails.order-confirmation',
        );
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $email = $this->subject('Xác nhận đơn hàng #' . $this->order->code)
                     ->view('client.emails.order-confirmation');

        foreach ($this->order->items as $item) {
            $imagePath = null;
            if ($item->variant && $item->variant->img) {
                $imagePath = $item->variant->img; // Ưu tiên ảnh biến thể
                Log::info('Sử dụng ảnh biến thể cho sản phẩm', [
                    'order_id' => $this->order->id,
                    'item_id' => $item->id,
                    'image_path' => $imagePath,
                ]);
            } elseif ($item->product && $item->product->thumbnail) {
                $imagePath = $item->product->thumbnail; // Fallback về ảnh sản phẩm
                Log::info('Sử dụng ảnh sản phẩm (thumbnail) cho sản phẩm', [
                    'order_id' => $this->order->id,
                    'item_id' => $item->id,
                    'image_path' => $imagePath,
                ]);
            }

            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                $filePath = Storage::disk('public')->path($imagePath);
                $email->attach($filePath, [
                    'as' => 'product_' . $item->id . '.' . pathinfo($imagePath, PATHINFO_EXTENSION),
                    'mime' => mime_content_type($filePath),
                ]);
                Log::info('Đính kèm hình ảnh sản phẩm trong email xác nhận đơn hàng', [
                    'order_id' => $this->order->id,
                    'item_id' => $item->id,
                    'image_path' => $imagePath,
                ]);
            } else {
                Log::warning('Hình ảnh sản phẩm không tồn tại', [
                    'order_id' => $this->order->id,
                    'item_id' => $item->id,
                    'image_path' => $imagePath,
                ]);
            }
        }

        return $email;
    }
}
