<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Refund;
use App\Models\RefundItem;
use App\Models\OrderOrderStatus;
use App\Models\OrderStatus;
use App\Models\OrderStatusHistory;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Mail\OrderCancellationMail;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->input('filter');
        $search = $request->input('search');

        $query = Order::where('user_id', Auth::id())
            ->with([
                'items.product',
                'orderItems',
                'items.review.images',
                'currentOrderStatus.status',
                'payment',
                'statusHistory.status'
            ])
            ->withCount(['statusHistory as current_status' => function ($q) {
                $q->where('is_current', true);
            }])
            ->orderBy('created_at', 'desc');

        // Apply status filter
        if ($filter) {
            switch ($filter) {
                case 'pending_payment':
                    // Chờ thanh toán - đơn chưa thanh toán và có trạng thái chờ xác nhận
                    $query->where('is_paid', false)
                        ->whereHas('statusHistory', function ($q) {
                            $q->where('is_current', true)
                                ->where('order_status_id', 1);
                        });
                    break;

                case 'processing':
                    // Đang xử lý - đơn đã thanh toán và đang chờ xác nhận
                    $query->where('is_paid', true)
                        ->whereHas('statusHistory', function ($q) {
                            $q->where('is_current', true)
                                ->where('order_status_id', 1);
                        });
                    break;

                case 'shipping':
                    // Đang giao hàng - đơn đang trong quá trình vận chuyển
                    $query->whereHas('statusHistory', function ($q) {
                        $q->where('is_current', true)
                            ->whereIn('order_status_id', [2, 3]); // Chờ lấy hàng, Đang giao
                    });
                    break;

                case 'delivered':
                    // Đã giao hàng - đã giao nhưng chưa xác nhận
                    $query->whereHas('statusHistory', function ($q) {
                        $q->where('is_current', true)
                            ->where('order_status_id', 4) // Đã giao
                            ->whereDoesntHave('order.statusHistory', function ($q) {
                                $q->where('order_status_id', 10); // Chưa hoàn thành
                            });
                    });
                    break;

                case 'completed':
                    // Đã hoàn thành - đã xác nhận nhận hàng
                    $query->whereHas('statusHistory', function ($q) {
                        $q->where('is_current', true)
                            ->where('order_status_id', 10); // Hoàn thành
                    });
                    break;

                case 'cancelled':
                    // Đã hủy
                    $query->whereHas('statusHistory', function ($q) {
                        $q->where('is_current', true)
                            ->where('order_status_id', 8); // Đã hủy
                    });
                    break;

                case 'return_refund':
                    // Trả hàng/Hoàn tiền
                    $query->whereHas('statusHistory', function ($q) {
                        $q->where('is_current', true)
                            ->whereIn('order_status_id', [5, 6, 7]); // Đang xử lý hoàn tiền, Đã hoàn tiền, Từ chối hoàn tiền
                    });
                    break;
            }
        }

        // Apply search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', '%' . $search . '%')
                    ->orWhere('id', $search)
                    ->orWhereHas('items.product', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('items.product', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        $orders = $query->paginate(10);

        // Đếm số lượng đơn hàng theo từng trạng thái
        $user = Auth::user();

        // Lấy tất cả đơn hàng của user với trạng thái hiện tại
        $allOrders = $user->orders()
            ->with(['statusHistory' => function ($q) {
                $q->where('is_current', true);
            }])
            ->get();

        // Khởi tạo các biến đếm
        $allCount = $allOrders->count();
        $pendingPaymentCount = 0;
        $processingCount = 0;
        $shippingCount = 0;
        $deliveredCount = 0;
        $completedCount = 0;
        $cancelledCount = 0;
        $returnRefundCount = 0;

        // Đếm số lượng đơn hàng theo từng trạng thái
        foreach ($allOrders as $order) {
            $currentStatus = $order->statusHistory->first();

            if (!$currentStatus) {
                continue;
            }

            $statusId = $currentStatus->order_status_id;

            // Xử lý từng trạng thái đơn hàng
            switch ($statusId) {
                case 1: // Chờ xác nhận
                    if ($order->is_paid) {
                        $processingCount++;
                    } else {
                        $pendingPaymentCount++;
                    }
                    break;

                case 2: // Chờ lấy hàng
                case 3: // Đang giao
                    $shippingCount++;
                    break;

                case 4: // Đã giao
                    // Kiểm tra xem đã có trạng thái hoàn thành (10) chưa
                    $hasCompleted = $order->statusHistory->contains('order_status_id', 10);
                    if (!$hasCompleted) {
                        $deliveredCount++;
                    }
                    break;

                case 10: // Hoàn thành
                    $completedCount++;
                    break;

                case 8: // Đã hủy
                    $cancelledCount++;
                    break;

                case 5: // Đang xử lý hoàn tiền
                case 6: // Đã hoàn tiền
                case 7: // Từ chối hoàn tiền
                    $returnRefundCount++;
                    break;
            }
        }

        // Tạo mảng kết quả để truyền vào view
        $statusCounts = [
            'all' => $allCount,
            'pending_payment' => $pendingPaymentCount,
            'processing' => $processingCount,
            'shipping' => $shippingCount,
            'delivered' => $deliveredCount,
            'completed' => $completedCount,
            'cancelled' => $cancelledCount,
            'return_refund' => $returnRefundCount
        ];

        return view('client.orders.index', compact('orders', 'statusCounts', 'search'));
    }

    /**
     * Hiển thị form tạo địa chỉ mới từ trang đơn hàng
     */
    public function createNewAddress(Order $order)
    {
        try {
            if ($order->user_id !== Auth::id()) {
                abort(404);
            }
            
            $user = Auth::user();
            return view('client.orders.create-address', compact('user', 'order'));
            
        } catch (\Exception $e) {
            Log::error('Error showing create address form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Đã xảy ra lỗi, vui lòng thử lại!');
        }
    }
    
    /**
     * Lưu địa chỉ mới từ trang đơn hàng
     */
    public function storeNewAddress(Request $request, Order $order)
    {
        try {
            if ($order->user_id !== Auth::id()) {
                return redirect()->back()->with('error', 'Bạn không có quyền cập nhật địa chỉ cho đơn hàng này.');
            }

            // Validate dữ liệu
            $validator = Validator::make($request->all(), [
                'fullname' => 'required|string|max:255',
                'phone_number' => 'required|string|max:20',
                'email' => 'required|email|max:255',
                'province' => 'required|string|max:255',
                'district' => 'required|string|max:255',
                'ward' => 'required|string|max:255',
                'address' => 'required|string|max:500',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            // Tạo địa chỉ mới
            $address = UserAddress::create([
                'user_id' => Auth::id(),
                'fullname' => $request->fullname,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'province' => $request->province,
                'district' => $request->district,
                'ward' => $request->ward,
                'address' => $request->address,
                'is_default' => 0,
                'address_type' => 'home',
            ]);

            // Cập nhật địa chỉ cho đơn hàng
            $order->update([
                'fullname' => $address->fullname,
                'phone_number' => $address->phone_number,
                'email' => $address->email,
                'address' => $address->address,
                'province' => $address->province,
                'district' => $address->district,
                'ward' => $address->ward,
                'address_id' => $address->id,
            ]);

            // Ghi log lịch sử
            $this->logStatusChange(
                $order->id,
                $order->current_status,
                'Đã thêm và cập nhật địa chỉ mới: ' . $address->address . ', ' . $address->ward . ', ' . $address->district . ', ' . $address->province,
                false
            );

            return redirect()->route('client.orders.show', $order->id)->with('success', 'Đã thêm địa chỉ mới và cập nhật đơn hàng thành công!');

        } catch (\Exception $e) {
            \Log::error('Lỗi khi thêm địa chỉ mới: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi thêm địa chỉ mới: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(404);
        }
        
        // Lấy danh sách địa chỉ của người dùng hiện tại
        $addresses = UserAddress::where('user_id', Auth::id())
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Load các mối quan hệ cần thiết
        $order->load(['items.product', 'items.review.images', 'statusHistory.status', 'payment']);

        return view('client.orders.show', compact('order', 'addresses'));
    }

    /**
     * Xử lý mua lại đơn hàng
     */
    public function reorder(Order $order, Request $request)
    {
        try {
            // Kiểm tra quyền truy cập
            if ($order->user_id !== Auth::id()) {
                return redirect()->back()->with('error', 'Bạn không có quyền thực hiện thao tác này');
            }

            // Kiểm tra trạng thái đơn hàng
            $allowedStatuses = ['COMPLETED', 'CANCELLED', 'DELIVERED', '10', '8', '4'];
            $currentStatus = $order->currentStatus->status->code ?? $order->currentStatus->order_status_id ?? '';

            if (!in_array($currentStatus, $allowedStatuses)) {
                return redirect()->back()->with('error', 'Không thể mua lại đơn hàng này. Trạng thái hiện tại: ' . $currentStatus);
            }

            DB::beginTransaction();

            // Lấy hoặc tạo giỏ hàng
            $cart = Cart::firstOrCreate(
                ['user_id' => Auth::id(), 'status' => 'pending'],
                ['total_price' => 0, 'created_at' => now(), 'updated_at' => now()]
            );

            $addedItems = 0;
            $unavailableItems = [];
            $outOfStockItems = [];

            // Tải trước các mối quan hệ cần thiết
            $order->load(['items.product', 'items.variant']);

            // Thêm từng sản phẩm vào giỏ
            foreach ($order->items as $item) {
                $product = $item->product;

                // Kiểm tra sản phẩm có tồn tại và đang hoạt động
                if (!$product || !$product->is_active) {
                    $unavailableItems[] = $product ? $product->name : 'Sản phẩm không tồn tại';
                    continue;
                }

                // Xử lý biến thể
                $variant = $item->variant;
                $variantId = $variant->id ?? null;
                $variantName = $variant ? ' - ' . $variant->name : '';

                // Kiểm tra tồn kho
                $availableStock = $variant ? $variant->stock : $product->stock;

                if ($availableStock < 1) {
                    $outOfStockItems[] = $product->name . $variantName;
                    continue;
                }

                // Tính số lượng tối đa có thể thêm
                $quantity = min($availableStock, $item->quantity);

                // Lấy giá hiện tại
                $currentPrice = $item->price_variant ?? $item->price ?? $product->price;

                // Xử lý thuộc tính biến thể
                $attributes = $this->processVariantAttributes($item);

                // Tìm cart item hiện có
                $existingItem = $cart->items()
                    ->where('product_id', $product->id)
                    ->where('product_variant_id', $variantId)
                    ->first();

                if ($existingItem) {
                    $newQuantity = min($existingItem->quantity + $quantity, $availableStock);
                    $existingItem->update([
                        'quantity' => $newQuantity,
                        'price' => $currentPrice,
                        'price_at_time' => $currentPrice,
                        'attributes' => $attributes,
                        'updated_at' => now(),
                    ]);
                } else {
                    $cartItem = new CartItem([
                        'product_id' => $product->id,
                        'product_variant_id' => $variantId,
                        'quantity' => min($quantity, $availableStock),
                        'price' => $currentPrice,
                        'price_at_time' => $currentPrice,
                        'attributes' => $attributes,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $cart->items()->save($cartItem);
                }

                $addedItems++;
            }

            DB::commit();

            // Tạo thông báo
            $messages = [];
            if ($addedItems > 0) {
                $messages[] = "Đã thêm $addedItems sản phẩm vào giỏ hàng.";
            }
            if (count($unavailableItems) > 0) {
                $messages[] = count($unavailableItems) . " sản phẩm không khả dụng: " . implode(', ', array_slice($unavailableItems, 0, 5)) . (count($unavailableItems) > 5 ? '...' : '');
            }
            if (count($outOfStockItems) > 0) {
                $messages[] = count($outOfStockItems) . " sản phẩm đã hết hàng: " . implode(', ', array_slice($outOfStockItems, 0, 5)) . (count($outOfStockItems) > 5 ? '...' : '');
            }

            return redirect()->route('client.shopping-cart.index')
                ->with('success', implode(' ', $messages));
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Lỗi khi mua lại đơn hàng: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'exception' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi xử lý yêu cầu. Vui lòng thử lại sau.');
        }
    }

    /**
     * Xử lý thuộc tính biến thể
     */
    private function processVariantAttributes($item)
    {
        if (empty($item->attributes_variant)) {
            return null;
        }

        if (is_string($item->attributes_variant)) {
            $attributes = json_decode($item->attributes_variant, true);
            return $attributes ? json_encode($attributes) : null;
        }

        return is_array($item->attributes_variant)
            ? json_encode($item->attributes_variant)
            : null;
    }

    public function tracking(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Không có quyền truy cập đơn hàng này');
        }

        $order->load([
            'items.product',
            'items.variant.attributes.attribute',
            'items.variant.attributes.value',
            'payment',
            'currentOrderStatus.status',
            'statusHistory.status',
        ]);

        // Các trạng thái cần cho timeline
        $timelineKeys = [
            'Chờ xác nhận',
            'Chờ lấy hàng',
            'Đang giao',
            'Giao hàng thành công',
            'Đã hủy',
        ];

        // Lấy lịch sử trạng thái từ bảng order_order_status (theo thứ tự thời gian, chỉ lấy các trạng thái thuộc timeline)
        $orderStatusSteps = OrderOrderStatus::where('order_id', $order->id)
            ->whereHas('status', function ($q) use ($timelineKeys) {
                $q->whereIn('name', $timelineKeys);
            })
            ->with('status')
            ->orderBy('created_at')
            ->get();

        return view('client.orders.tracking', compact('order', 'orderStatusSteps'));
    }

    public function cancel(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Không có quyền hủy đơn hàng này');
        }

        // Validate request
        $request->validate([
            'cancel_reason' => 'required|string|max:255',
            'cancel_note' => 'nullable|string|max:500',
        ]);

        $currentStatusRecord = OrderOrderStatus::where('order_id', $order->id)
            ->where('is_current', 1)
            ->first();

        if (!$currentStatusRecord) {
            return redirect()->back()->with('error', 'Không thể xác định trạng thái hiện tại của đơn hàng!');
        }

        $currentStatusId = $currentStatusRecord->order_status_id;

        $cancelStatus = OrderStatus::where('name', 'Đã hủy')->first();
        if (!$cancelStatus) {
            return redirect()->back()->with('error', 'Không tìm thấy trạng thái Đã hủy!');
        }

        $cancelStatusId = $cancelStatus->id;

        // Kiểm tra trạng thái hiện tại có thể hủy không
        $cancelableStatuses = [1, 2, 8]; // Chờ xác nhận, Chờ lấy hàng, Gửi hàng
        if (!in_array($currentStatusId, $cancelableStatuses)) {
            return redirect()->back()->with('error', 'Không thể hủy đơn hàng ở trạng thái hiện tại!');
        }

        DB::transaction(function () use ($order, $cancelStatusId, $currentStatusRecord, $request) {
            // Hoàn trả kho cho từng sản phẩm trong đơn hàng
            foreach ($order->items as $item) {
                // Hoàn trả kho cho biến thể nếu có
                if ($item->product_variant_id) {
                    $variant = $item->variant;
                    if ($variant) {
                        $variant->stock += $item->quantity;
                        $variant->save();
                    }
                }
                // Hoàn trả kho cho sản phẩm gốc
                $product = $item->product;
                if ($product) {
                    $product->stock += $item->quantity;
                    $product->save();
                }
            }

            // Cập nhật trạng thái cũ thành không hiện tại
            DB::table('order_order_status')
                ->where('order_id', $currentStatusRecord->order_id)
                ->where('order_status_id', $currentStatusRecord->order_status_id)
                ->where('is_current', 1)
                ->update(['is_current' => 0]);

            OrderStatusHistory::where('order_id', $order->id)
                ->where('is_current', 1)
                ->update(['is_current' => 0]);

            // Tạo trạng thái mới
            $cancelNote = "Lý do: {$request->cancel_reason}";
            if ($request->cancel_note) {
                $cancelNote .= " - Ghi chú: {$request->cancel_note}";
            }

            OrderOrderStatus::create([
                'order_id' => $order->id,
                'order_status_id' => $cancelStatusId,
                'modified_by' => Auth::id(),
                'note' => $cancelNote,
                'created_at' => now(),
                'updated_at' => now(),
                'is_current' => 1,
            ]);

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'order_status_id' => $cancelStatusId,
                'modifier_id' => Auth::id(),
                'note' => $cancelNote,
                'is_current' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        // Cập nhật thông tin hủy đơn hàng
        $order->update([
            'cancel_reason' => $request->cancel_reason,
            'cancel_note' => $request->cancel_note,
            'cancelled_at' => now(),
        ]);

        // Nếu đơn thanh toán VNPay và đã thanh toán, tự động tạo yêu cầu hoàn tiền cho Admin xử lý
        try {
            if ($order->payment_id == 2 && $order->is_paid) {
                // Tránh tạo trùng
                $existingRefund = Refund::where('order_id', $order->id)->first();
                if (!$existingRefund) {
                    $refund = Refund::create([
                        'order_id' => $order->id,
                        'user_id' => $order->user_id,
                        'total_amount' => $order->total_amount,
                        'bank_account' => null,
                        'user_bank_name' => null,
                        'bank_name' => 'VNPay',
                        'reason' => 'other',
                        'reason_image' => null,
                        'status' => 'pending',
                        'is_send_money' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Tạo các refund items từ order items
                    $order->loadMissing('items');
                    $refundItemsPayload = [];
                    foreach ($order->items as $orderItem) {
                        $refundItemsPayload[] = [
                            'refund_id' => $refund->id,
                            'product_id' => $orderItem->product_id,
                            'variant_id' => $orderItem->product_variant_id,
                            'name' => $orderItem->name ?? ($orderItem->product->name ?? 'Sản phẩm'),
                            'name_variant' => $orderItem->name_variant ?? null,
                            'quantity' => $orderItem->quantity,
                            'price' => $orderItem->price,
                            'price_variant' => $orderItem->price_variant ?? null,
                            'quantity_variant' => $orderItem->quantity_variant ?? null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                    if (!empty($refundItemsPayload)) {
                        RefundItem::insert($refundItemsPayload);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Tạo yêu cầu hoàn tiền tự động khi hủy đơn thất bại', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }

        // Gửi email thông báo hủy đơn hàng
        try {
            $refundInfo = null;

            // Nếu là đơn hàng VNPay đã thanh toán, thêm thông tin hoàn tiền
            if ($order->payment_id == 2 && $order->is_paid) {
                $refundInfo = [
                    'transaction_id' => 'Đang xử lý',
                    'amount' => $order->total_amount,
                    'status' => 'pending'
                ];
            }

            Mail::to($order->email)->send(new OrderCancellationMail(
                $order,
                $request->cancel_reason,
                $refundInfo
            ));

            Log::info('Email thông báo hủy đơn hàng đã được gửi', [
                'order_id' => $order->id,
                'order_code' => $order->code,
                'user_email' => $order->email,
                'payment_method' => $order->payment_id == 2 ? 'VNPay' : 'COD'
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi gửi email thông báo hủy đơn hàng', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return redirect()->route('client.orders')
            ->with('success', 'Đã hủy đơn hàng thành công!');
    }

    public function updateOldOrderStatuses()
    {
        $orders = Order::all();

        foreach ($orders as $order) {
            $hasCurrent = OrderOrderStatus::where('order_id', $order->id)
                ->where('is_current', 1)
                ->exists();

            if (!$hasCurrent) {
                OrderOrderStatus::create([
                    'order_id' => $order->id,
                    'order_status_id' => 1, // Chờ xác nhận
                    'modified_by' => $order->user_id ?? 1,
                    'note' => 'Tự động cập nhật trạng thái cho đơn hàng cũ',
                    'is_current' => 1,
                    'created_at' => $order->created_at ?? Carbon::now(),
                    'updated_at' => $order->updated_at ?? Carbon::now(),
                ]);
                echo "Đã cập nhật trạng thái cho đơn hàng #{$order->id}\n";
            }
        }

        return redirect()->back()->with('success', 'Đã cập nhật trạng thái cho các đơn hàng cũ thành công!');
    }
    
    /**
     * Ghi log thay đổi trạng thái đơn hàng
     *
     * @param int $orderId
     * @param int $statusId
     * @param string $note
     * @param bool $isCurrent
     * @return void
     */
    protected function logStatusChange($orderId, $statusId, $note = '', $isCurrent = true)
    {
        OrderStatusHistory::create([
            'order_id' => $orderId,
            'order_status_id' => $statusId,
            'modifier_id' => Auth::id(),
            'note' => $note,
            'is_current' => $isCurrent,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Xác nhận đã nhận hàng
     */
    public function confirmDelivery(Order $order)
    {
        // Kiểm tra quyền truy cập
        if ($order->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Bạn không có quyền xác nhận đơn hàng này');
        }

        $deliveredStatusId = 4; // ID cho trạng thái Giao hàng thành công
        $completedStatusId = 10; // ID cho trạng thái Hoàn thành

        // Kiểm tra xem đơn hàng đã ở trạng thái giao hàng thành công chưa
        $currentStatus = $order->statusHistory()
            ->where('is_current', 1)
            ->first();

        if (!$currentStatus || $currentStatus->order_status_id !== $deliveredStatusId) {
            return redirect()->back()->with('error', 'Chỉ có thể xác nhận đơn hàng đã được giao thành công');
        }

        DB::beginTransaction();
        try {
            // Cập nhật trạng thái cũ
            $order->statusHistory()
                ->where('is_current', 1)
                ->update(['is_current' => 0]);

            // Tạo trạng thái mới là Hoàn thành (10)
            OrderOrderStatus::create([
                'order_id' => $order->id,
                'order_status_id' => $completedStatusId, // Sử dụng ID 10 cho trạng thái Hoàn thành
                'modified_by' => Auth::id(),
                'note' => 'Khách hàng đã xác nhận nhận hàng',
                'is_current' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'order_status_id' => $completedStatusId, // Sử dụng ID 10 cho trạng thái Hoàn thành
                'modifier_id' => Auth::id(),
                'note' => 'Khách hàng đã xác nhận nhận hàng',
                'is_current' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('client.orders')->with('success', 'Đã xác nhận nhận hàng thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Lỗi khi xác nhận nhận hàng: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xác nhận nhận hàng: ' . $e->getMessage());
        }
    }
    
    /**
     * Cập nhật địa chỉ nhận hàng cho đơn hàng
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * Lưu địa chỉ mới và cập nhật cho đơn hàng
     */
    public function storeAddress(Request $request, Order $order)
    {
        try {
            if ($order->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền cập nhật địa chỉ cho đơn hàng này.'
                ], 403);
            }

            // Validate dữ liệu
            $validator = Validator::make($request->all(), [
                'fullname' => 'required|string|max:255',
                'phone_number' => 'required|string|max:20',
                'email' => 'required|email|max:255',
                'province' => 'required|string|max:255',
                'district' => 'required|string|max:255',
                'ward' => 'required|string|max:255',
                'address' => 'required|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Tạo địa chỉ mới
            $address = UserAddress::create([
                'user_id' => Auth::id(),
                'fullname' => $request->fullname,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'province' => $request->province,
                'district' => $request->district,
                'ward' => $request->ward,
                'address' => $request->address,
                'is_default' => 0,
                'address_type' => 'home',
            ]);

            // Cập nhật địa chỉ cho đơn hàng
            $order->update([
                'fullname' => $address->fullname,
                'phone_number' => $address->phone_number,
                'email' => $address->email,
                'address' => $address->address,
                'province' => $address->province,
                'district' => $address->district,
                'ward' => $address->ward,
                'address_id' => $address->id,
            ]);

            // Ghi log lịch sử
            $this->logStatusChange(
                $order->id,
                $order->current_status,
                'Đã thêm và cập nhật địa chỉ mới: ' . $address->address . ', ' . $address->ward . ', ' . $address->district . ', ' . $address->province,
                false
            );

            return response()->json([
                'success' => true,
                'message' => 'Thêm địa chỉ mới và cập nhật đơn hàng thành công!',
                'data' => [
                    'id' => $address->id,
                    'fullname' => $address->fullname,
                    'phone_number' => $address->phone_number,
                    'email' => $address->email,
                    'address' => $address->address,
                    'province' => $address->province,
                    'district' => $address->district,
                    'ward' => $address->ward,
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Lỗi khi thêm địa chỉ mới: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thêm địa chỉ mới: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật địa chỉ nhận hàng cho đơn hàng
     */
    public function updateAddress(Request $request, Order $order)
    {
        try {
            // Kiểm tra quyền truy cập
            if ($order->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền cập nhật địa chỉ cho đơn hàng này.'
                ], 403);
            }

            // Kiểm tra trạng thái đơn hàng
            if ($order->currentOrderStatus->status->name !== 'Chờ xác nhận') {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ có thể thay đổi địa chỉ khi đơn hàng đang ở trạng thái chờ xác nhận.'
                ], 400);
            }

            // Lấy địa chỉ mới
            $address = UserAddress::where('id', $request->address_id)
                ->where('user_id', Auth::id())
                ->first();

            if (!$address) {
                return response()->json([
                    'success' => false,
                    'message' => 'Địa chỉ không hợp lệ hoặc không tồn tại.'
                ], 404);
            }

            // Cập nhật địa chỉ đơn hàng
            $order->update([
                'fullname' => $address->fullname,
                'phone_number' => $address->phone_number,
                'email' => $address->email,
                'address' => $address->address,
                'province' => $address->province,
                'district' => $address->district,
                'ward' => $address->ward,
                'address_id' => $address->id,
            ]);

            // Ghi log lịch sử
            $this->logStatusChange(
                $order->id,
                $order->current_status ?? 1, // Sử dụng giá trị mặc định là 1 nếu current_status null
                'Đã cập nhật địa chỉ nhận hàng: ' . $address->address . (str_contains($address->address, $address->ward) ? '' : ', ' . $address->ward) . 
                (str_contains($address->address, $address->district) ? '' : ', ' . $address->district) . 
                (str_contains($address->address, $address->province) ? '' : ', ' . $address->province),
                false
            );

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật địa chỉ nhận hàng thành công!',
                'data' => [
                    'fullname' => $order->fresh()->fullname,
                    'phone_number' => $order->fresh()->phone_number,
                    'email' => $order->fresh()->email,
                    'address' => $order->fresh()->address,
                    'province' => $order->fresh()->province,
                    'district' => $order->fresh()->district,
                    'ward' => $order->fresh()->ward,
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Lỗi khi cập nhật địa chỉ đơn hàng: ' . $e->getMessage());
            \Log::error('Trace: ' . $e->getTraceAsString());
            
            $errorMessage = 'Có lỗi xảy ra: ' . $e->getMessage();
            
            // Nếu là lỗi SQL, thêm thông tin chi tiết
            if ($e instanceof \Illuminate\Database\QueryException) {
                $errorMessage .= ' (SQL: ' . $e->getSql() . ')';
                if ($e->errorInfo) {
                    $errorMessage .= ' - Error Info: ' . json_encode($e->errorInfo);
                }
            }
            
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'error_details' => config('app.debug') ? [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ] : null
            ], 500);
        }
    }
}
