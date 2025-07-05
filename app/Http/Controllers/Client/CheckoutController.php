<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\OrderOrderStatus;

class CheckoutController extends Controller
{
    public function index()
    {
        try {
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục!');
            }

            $cart = Cart::where('user_id', Auth::id())->first();

            if (!$cart) {
                return redirect()->route('shopping-cart.index')->with('error', 'Giỏ hàng không tồn tại!');
            }

            $cartItems = $cart->items;
            $cartTotal = $cartItems->sum(function ($item) {
                return $item->price * $item->quantity;
            });

            return view('client.checkout', compact('cart', 'cartItems', 'cartTotal'));
        } catch (\Exception $e) {
            Log::error('Checkout index error: ' . $e->getMessage());
            return redirect()->route('shopping-cart.index')->with('error', 'Đã xảy ra lỗi, vui lòng thử lại!');
        }
    }

    public function process(Request $request)
    {
        try {
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để đặt hàng!');
            }

            Log::info('Validating checkout form', $request->all());

            $request->validate([
                'fullname' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'phone_number' => 'required|string|regex:/^[0-9]{10}$/',
                'email' => 'required|email|max:255',
                'payment_method' => 'required|in:cod,vnpay',
            ], [
                'phone_number.regex' => 'Số điện thoại phải là 10 số.',
            ]);

            $cart = Cart::where('user_id', Auth::id())
                ->with('items')
                ->first();

            if (!$cart || $cart->items->isEmpty()) {
                Log::warning('Cart is empty or not found', ['user_id' => Auth::id()]);
                return redirect()->route('shopping-cart.index')->with('error', 'Giỏ hàng trống!');
            }

            Log::info('Cart found', ['cart_id' => $cart->id, 'items_count' => $cart->items->count()]);

            $subtotal = $cart->items->sum(function ($item) {
                return ($item->price_at_time ?? 0) * ($item->quantity ?? 0);
            });
            $shippingFee = 20000;
            $total = $subtotal + $shippingFee;

            $paymentId = $request->payment_method === 'cod' ? 1 : 2; // Điều chỉnh theo bảng payments

            $order = Order::create([
                'user_id' => Auth::id(),
                'code' => Str::upper('ORD-' . Str::random(8)),
                'payment_id' => $paymentId,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'fullname' => $request->fullname,
                'address' => $request->address,
                'note' => $request->order_notes,
                'total_amount' => $total,
                'is_paid' => 0,
                'is_refunded' => 0,
                'coupon_id' => null,
                'is_refunded_canceled' => 0,
                'check_refunded_canceled' => 0,
                'img_refunded_money' => null,
            ]);

            Log::info('Order created', ['order_id' => $order->id, 'code' => $order->code]);

            // Thêm trạng thái mặc định vào bảng order_order_status
            OrderOrderStatus::create([
                'order_id' => $order->id,
                'order_status_id' => 1, // Trạng thái mặc định: Chờ xác nhận
                'modified_by' => Auth::id(), // Người dùng hiện tại (hoặc có thể là null nếu là hệ thống)
                'note' => 'Đơn hàng mới được tạo, đang chờ xác nhận.',
                'employee_evidence' => null, // Không có minh chứng ban đầu
                'customer_confirmation' => null, // Chưa có xác nhận từ khách hàng
                'is_current' => 1, // Đánh dấu là trạng thái hiện tại
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::info('Order status created', [
                'order_id' => $order->id,
                'order_status_id' => 1,
                'modified_by' => Auth::id(),
            ]);

            // Thêm OrderItem với cách thủ công
            foreach ($cart->items as $item) {
                try {
                    Log::debug('Attempting to create OrderItem with new instance', $item->toArray());
                    $orderItem = new OrderItem();
                    $orderItem->order_id = $order->id;
                    $orderItem->product_id = $item->product_id;
                    $orderItem->product_variant_id = $item->product_variant_id;
                    $orderItem->name = 'Sản phẩm ' . $item->product_id;
                    $orderItem->price = $item->price_at_time ?? 0;
                    $orderItem->quantity = $item->quantity ?? 0;
                    $orderItem->save();

                    Log::info('OrderItem saved successfully', [
                        'order_item_id' => $orderItem->id,
                        'order_id' => $order->id,
                        'product_id' => $item->product_id,
                        'product_variant_id' => $item->product_variant_id,
                        'price' => $item->price_at_time,
                        'quantity' => $item->quantity,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to save OrderItem', [
                        'exception' => $e,
                        'item' => $item->toArray(),
                        'order_id' => $order->id,
                        'message' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                    throw new \Exception('Lỗi khi lưu chi tiết đơn hàng: ' . $e->getMessage());
                }
            }

            // Xóa dữ liệu trong giỏ hàng
            $cart->items()->delete();
            $cart->delete();
            Log::info('Cart and items deleted', ['cart_id' => $cart->id]);

            if ($request->payment_method === 'cod') {
                Log::info('COD payment processed', ['code' => $order->code]);
                return redirect()->route('checkout.success', $order->code)
                    ->with('success', 'Đơn hàng của bạn đã được đặt thành công!');
            } else {
                Log::info('Redirecting to VNPay', ['code' => $order->code]);
                return $this->vnpayPayment($order);
            }
        } catch (\Exception $e) {
            Log::error('Checkout process error: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('checkout')
                ->withInput()
                ->with('error', 'Đã xảy ra lỗi khi xử lý đơn hàng, vui lòng kiểm tra lại thông tin!');
        }
    }

    protected function vnpayPayment(Order $order)
    {
        try {
            $vnp_TmnCode = '3ANN0P8R';
            $vnp_HashSecret = '63J7LYXN9JWZ7BRLGXUXYJ7UH5Q1TA6Y';
            if (!$vnp_TmnCode || !$vnp_HashSecret) {
                throw new \Exception('Cấu hình VNPay không hợp lệ.');
            }

            $vnp_Url = 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html';
            $vnp_Returnurl = route('vnpay.return');

            $vnp_TxnRef = $order->code;
            $vnp_OrderInfo = 'Thanh toán đơn hàng test ' . $order->code;
            $vnp_Amount = $order->total_amount * 100;
            $vnp_Locale = 'vn';
            $vnp_IpAddr = request()->ip();
            $vnp_CreateDate = now()->format('YmdHis');

            $inputData = [
                'vnp_Version' => '2.1.0',
                'vnp_TmnCode' => $vnp_TmnCode,
                'vnp_Amount' => $vnp_Amount,
                'vnp_Command' => 'pay',
                'vnp_CreateDate' => $vnp_CreateDate,
                'vnp_CurrCode' => 'VND',
                'vnp_IpAddr' => $vnp_IpAddr,
                'vnp_Locale' => $vnp_Locale,
                'vnp_OrderInfo' => $vnp_OrderInfo,
                'vnp_OrderType' => '250000', // Hỗ trợ mã QR
                'vnp_ReturnUrl' => $vnp_Returnurl,
                'vnp_TxnRef' => $vnp_TxnRef,
                'vnp_BankCode' => 'NCB', // Sử dụng ngân hàng test NCB
            ];

            ksort($inputData);
            $query = http_build_query($inputData);
            $vnpSecureHash = hash_hmac('sha512', $query, $vnp_HashSecret);
            $vnp_Url = $vnp_Url . '?' . $query . '&vnp_SecureHash=' . $vnpSecureHash;

            Log::info('VNPay test URL: ' . $vnp_Url);
            return redirect($vnp_Url);
        } catch (\Exception $e) {
            Log::error('VNPay test error: ' . $e->getMessage());
            return redirect()->route('checkout')
                ->withInput()
                ->with('error', 'Lỗi khi tạo thanh toán test: ' . $e->getMessage());
        }
    }

    public function vnpayReturn(Request $request)
    {
        try {
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để hoàn tất thanh toán!');
            }

            $vnp_HashSecret = '63J7LYXN9JWZ7BRLGXUXYJ7UH5Q1TA6Y';
            $vnp_SecureHash = $request->vnp_SecureHash;
            $inputData = $request->all();
            unset($inputData['vnp_SecureHash']);
            unset($inputData['vnp_SecureHashType']);

            ksort($inputData);
            $query = http_build_query($inputData);
            $secureHash = hash_hmac('sha512', $query, $vnp_HashSecret);

            Log::info('VNPay return data: ' . json_encode($inputData));
            Log::info('VNPay secure hash: ' . $vnp_SecureHash . ' | Calculated: ' . $secureHash);

            $order = Order::where('code', $request->vnp_TxnRef)->first();

            if ($secureHash === $vnp_SecureHash && $order) {
                if ($request->vnp_ResponseCode === '00') {
                    // ✅ 1. Cập nhật đơn hàng
                    $order->update(['status' => 'paid', 'is_paid' => 1]);

                    // ✅ 2. Ghi log thanh toán vào bảng `payment_logs`
                    \App\Models\PaymentLog::create([
                        'order_id'       => $order->id,
                        'txn_ref'        => $request->vnp_TxnRef,
                        'response_code'  => $request->vnp_ResponseCode,
                        'transaction_no' => $request->vnp_TransactionNo ?? null,
                        'amount'         => $request->vnp_Amount / 100,
                        'bank_code'      => $request->vnp_BankCode ?? null,
                        'response_data'  => json_encode($inputData),
                    ]);

                    // ✅ 3. Ghi trạng thái đơn hàng vào bảng `order_order_status`
                    \App\Models\OrderOrderStatus::create([
                        'order_id'           => $order->id,
                        'order_status_id'    => 2, // Ví dụ: 2 là "Đã thanh toán"
                        'modified_by'        => Auth::id(),
                        'note'               => 'Thanh toán VNPay thành công',
                        'employee_evidence'  => null,
                        'customer_confirmation' => null,
                        'is_current'         => 1,
                    ]);

                    // ✅ 4. Thêm sản phẩm từ cart vào bảng `order_items`
                    $cart = Cart::where('user_id', Auth::id())->first();
                    if ($cart) {
                        foreach ($cart->items as $item) {
                            \App\Models\OrderItem::create([
                                'order_id'         => $order->id,
                                'product_id'       => $item->product_id,
                                'product_variant_id' => $item->product_variant_id,
                                'name'             => $item->product->name,
                                'price'            => $item->price,
                                'old_price'        => $item->product->price,
                                'old_price_variant' => $item->variant_price ?? null,
                                'quantity'         => $item->quantity,
                                'name_variant'     => $item->variant_name ?? null,
                                'attributes_variant' => $item->variant_attributes ?? null,
                                'price_variant'    => $item->variant_price ?? null,
                                'quantity_variant' => $item->quantity ?? null,
                            ]);
                        }

                        // ✅ 5. Xoá giỏ hàng
                        $cart->items()->delete();
                        $cart->delete();
                    }

                    return redirect()->route('checkout.success', $order->code)
                        ->with('success', 'Thanh toán VNPay thành công!');
                } else {
                    $order->update(['status' => 'failed', 'is_paid' => 0]);

                    Log::error('VNPay failed: ResponseCode = ' . $request->vnp_ResponseCode);
                    return redirect()->route('checkout')
                        ->withInput()
                        ->with('error', 'Thanh toán VNPay thất bại với mã lỗi: ' . $request->vnp_ResponseCode);
                }
            }

            Log::error('VNPay verification failed: Invalid secure hash or order not found');
            return redirect()->route('checkout')
                ->withInput()
                ->with('error', 'Lỗi xác thực thanh toán VNPay!');
        } catch (\Exception $e) {
            Log::error('VNPay return error: ' . $e->getMessage());
            return redirect()->route('checkout')
                ->withInput()
                ->with('error', 'Lỗi xử lý thanh toán VNPay: ' . $e->getMessage());
        }
    }


    public function success($order_number)
    {
        try {
            $order = Order::where('user_id', Auth::id())->where('code', $order_number)->firstOrFail();
            return view('client.checkout-success', compact('order'));
        } catch (\Exception $e) {
            Log::error('Checkout success error: ' . $e->getMessage());
            return redirect()->route('shopping-cart.index')->with('error', 'Không tìm thấy đơn hàng!');
        }
    }
}
