<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Refund;
use App\Models\RefundItem;
use App\Models\Notification;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RefundController extends Controller
{
    public function store(Request $request)
    {
        // Log request data for debugging
        Log::info('Refund Request Data:', $request->all());

        // Validate request
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'reason' => 'required|string|max:500',
            'custom_reason' => 'required_if:reason,Khác|string|max:500|nullable',
            'items' => 'required|array|min:1',
            'items.*' => 'exists:order_items,id',
            'bank_name' => 'required_if:bank_info_missing,true|string|max:100',
            'user_bank_name' => 'required_if:bank_info_missing,true|string|max:100',
            'bank_account' => 'required_if:bank_info_missing,true|string|max:50',
        ]);

        $user = Auth::user();
        $order = Order::findOrFail($request->order_id);

        // Log order eligibility check
        Log::info('Order Eligibility Check', [
            'order_id' => $order->id,
            'has_latest_status' => !empty($order->latestStatus),
            'order_status_id' => $order->latestStatus ? $order->latestStatus->order_status_id : null,
            'has_refund' => !empty($order->refund),
        ]);

        // Check if order is eligible for refund
        if (!$order->latestStatus || $order->latestStatus->order_status_id != 4 || $order->refund) {
            return back()->with('error', 'Đơn hàng không đủ điều kiện yêu cầu hoàn tiền.');
        }

        // Check if bank info is missing
        $bankInfoMissing = !$user->bank_name || !$user->user_bank_name || !$user->bank_account;
        if ($bankInfoMissing) {
            $request->merge(['bank_info_missing' => true]);
        }

        // Combine reason and custom_reason
        $reason = $request->reason === 'Khác' ? $request->custom_reason : $request->reason;

        DB::beginTransaction();
        try {
            // Update user bank info if missing
            if ($bankInfoMissing) {
                Log::info('Updating user bank info', [
                    'bank_name' => $request->bank_name,
                    'user_bank_name' => $request->user_bank_name,
                    'bank_account' => $request->bank_account,
                ]);
                $user->update([
                    'bank_name' => $request->bank_name,
                    'user_bank_name' => $request->user_bank_name,
                    'bank_account' => $request->bank_account,
                ]);
            }

            // Create refund record
            Log::info('Creating refund record', ['reason' => $reason]);
            $refund = Refund::create([
                'order_id' => $order->id,
                'user_id' => $user->id,
                'reason' => $reason,
                'status' => 'pending',
                'bank_account_status' => $bankInfoMissing ? 'new' : 'existing',
                'is_send_money' => false,
            ]);

            // Create refund items
            Log::info('Creating refund items', ['items' => $request->items]);
            foreach ($request->items as $itemId) {
                $orderItem = $order->orderItems()->where('id', $itemId)->firstOrFail();
                Log::info('Creating refund item for order_item_id: ' . $itemId);
                RefundItem::create([
                    'refund_id' => $refund->id,
                    'product_id' => $orderItem->product_id,
                    'variant_id' => $orderItem->variant_id,
                    'quantity' => $orderItem->quantity,
                    'price_at_time' => $orderItem->price_at_time,
                ]);
            }

            // Create notification for admin
            Log::info('Creating notification for admin');
            Notification::create([
                'user_id' => null,
                'order_id' => $order->id,
                'type' => 3,
                'message' => "Yêu cầu hoàn tiền mới cho đơn hàng #{$order->code} từ khách hàng {$user->fullname}",
                'read' => false,
            ]);

            DB::commit();
            Log::info('Refund created successfully for order: ' . $order->id);
            return redirect()->route('client.orders.show', $order->id)
                           ->with('success', 'Yêu cầu hoàn tiền đã được gửi thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Refund creation failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Có lỗi xảy ra khi gửi yêu cầu hoàn tiền: ' . $e->getMessage());
        }
    }
}
