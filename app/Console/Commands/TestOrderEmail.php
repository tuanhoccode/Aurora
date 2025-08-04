<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Mail\OrderConfirmationMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestOrderEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:order-email {order_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test sending order confirmation email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orderId = $this->argument('order_id');
        
        $order = Order::with(['items.product', 'user'])->find($orderId);
        
        if (!$order) {
            $this->error("Order with ID {$orderId} not found!");
            return 1;
        }

        $this->info("Sending order confirmation email for order #{$order->code} to {$order->email}");

        try {
            Mail::to($order->email)->send(new OrderConfirmationMail($order));
            $this->info('Email sent successfully!');
            return 0;
        } catch (\Exception $e) {
            $this->error('Failed to send email: ' . $e->getMessage());
            return 1;
        }
    }
} 