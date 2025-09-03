<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Mail\OrderCancellationMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestOrderCancellationMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:order-cancellation-mail {order_id} {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test gửi email thông báo hủy đơn hàng';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orderId = $this->argument('order_id');
        $testEmail = $this->argument('email');
        
        try {
            $order = Order::with(['items.product', 'items.variant', 'user'])->findOrFail($orderId);
            
            $email = $testEmail ?? $order->email;
            
            $this->info("Đang gửi email test đến: {$email}");
            $this->info("Đơn hàng: {$order->code}");
            
            $refundInfo = null;
            
            // Nếu là đơn hàng VNPay đã thanh toán, thêm thông tin hoàn tiền
            if ($order->payment_id == 2 && $order->is_paid) {
                $refundInfo = [
                    'transaction_id' => 'TEST-' . time(),
                    'amount' => $order->total_amount,
                    'status' => 'pending'
                ];
            }
            
            Mail::to($email)->send(new OrderCancellationMail(
                $order, 
                'Test hủy đơn hàng - ' . now()->format('d/m/Y H:i:s'), 
                $refundInfo
            ));
            
            $this->info('✅ Email đã được gửi thành công!');
            $this->info("📧 Email: {$email}");
            $this->info("📦 Đơn hàng: {$order->code}");
            $this->info("💰 Tổng tiền: " . number_format($order->total_amount) . " ₫");
            $this->info("💳 Phương thức: " . ($order->payment_id == 2 ? 'VNPay' : 'COD'));
            
            if ($refundInfo) {
                $this->info("🔄 Thông tin hoàn tiền: " . json_encode($refundInfo));
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Lỗi: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
        }
    }
}
