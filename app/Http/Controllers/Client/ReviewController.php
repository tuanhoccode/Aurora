<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ReviewRequest;
use App\Models\Comment;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\ReviewImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class ReviewController extends Controller
{
   public function store(ReviewRequest $req,  $productId)
{
       $user = Auth::user();

    // Tìm đơn hàng chứa sản phẩm này và trạng thái hiện tại là "Giao hàng thành công"
    $order = Order::where('user_id', $user->id)
    ->whereHas('items', fn($q) => $q->where('product_id', $productId))
    ->whereHas('currentStatus.status', fn($q) => 
        $q->where('name', 'Giao hàng thành công')
    )
    ->first();
//     dd(
//     Order::where('user_id', $user->id)
//         ->whereHas('items', fn($q) => $q->where('product_id', $productId))
//         ->with(['orderStatuses' => fn($q) => $q->where('is_current', 1)->with('status')])
//         ->get()
//         ->toArray()
// );

    if (!$order) {
        return back()->with('error', 'Đơn hàng không tồn tại hoặc chưa được giao, bạn chưa thể đánh giá.');
    }

    // Kiểm tra đã đánh giá chưa theo order_item
$orderItem = $order->items()->where('product_id', $productId)->first();

$exists = Review::where('user_id', $user->id)
    ->where('product_id', $productId)
    ->where('order_id', $order->id)
    ->where('order_item_id', $orderItem->id ?? null) // cần thêm cột order_item_id trong reviews
    ->exists();

    if ($exists) {
        return back()->with('error', 'Bạn đã đánh giá sản phẩm này trong đơn hàng này rồi.');
    }

    // Lưu đánh giá
    Review::create([
        'user_id'     => $user->id,
        'product_id'  => $productId,
        'order_id'    => $order->id,
        'order_item_id' => $orderItem->id ?? null,
        'rating'      => $req->rating,
        'review_text' => $req->review_text,
    ]);

    return back()->with('success', 'Đánh giá của bạn đã được gửi thành công.');
    
} 
}
