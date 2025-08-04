<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Mail\OrderConfirmationMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestOrderEmailWithImage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:order-email-image {order_id} {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test sending order confirmation email with product images';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orderId = $this->argument('order_id');
        $testEmail = $this->argument('email');
        
        $order = Order::with(['items.product', 'user'])->find($orderId);
        
        if (!$order) {
            $this->error("Order with ID {$orderId} not found!");
            return 1;
        }

        $emailTo = $testEmail ?: $order->email;
        
        $this->info("Sending order confirmation email for order #{$order->code}");
        $this->info("From: {$order->email}");
        $this->info("To: {$emailTo}");
        
        // Hiển thị thông tin sản phẩm
        $this->info("\nProducts in order:");
        foreach ($order->items as $item) {
            $this->line("- {$item->name} (Qty: {$item->quantity})");
            if ($item->product && $item->product->thumbnail) {
                $this->line("  Image: {$item->product->image_url}");
            } else {
                $this->line("  Image: No image available");
            }
        }

        try {
            Mail::to($emailTo)->send(new OrderConfirmationMail($order));
            $this->info('✅ Email sent successfully!');
            $this->info('📧 Check your email or log file for the email content.');
            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Failed to send email: ' . $e->getMessage());
            return 1;
        }
    }
} 