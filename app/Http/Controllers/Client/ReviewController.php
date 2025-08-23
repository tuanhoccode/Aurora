<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ReviewRequest;
use App\Models\Comment;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\ReviewImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class ReviewController extends Controller
{
   public function store(ReviewRequest $req)
{
    $productId = $req->product_id;
    $user = Auth::user();
    //Lấy tất cả đơn hàng của user với sản phẩm này đã được giao
    $orders = Order::where('user_id', $user->id)
        ->whereHas('items', fn($q)=>$q->where('product_id', $productId))
        ->whereHas('currentStatus.status', fn($q)
        => $q->where('name', 'Nhận hàng thành công'))
        ->get();
    
    if($orders->isEmpty()){
        return back()->with('error', 'Bạn chưa mua hoặc chưa nhận được sản phẩm này, không thể đánh giá');
    }
    //Kiểm tra xem đon hàng này đã được đánh giá chưa 
    $reviewedOrderItemIds  = Review::where('user_id', $user->id)
    ->where('product_id', $productId)
    ->pluck('order_item_id')->toArray();

    //Lấy order_item nào chưa được đánh giá 
    $orderItem = null;
    foreach($orders as $order){
        foreach($order->items as $item){
            if (!in_array($item -> id, $reviewedOrderItemIds)) {
                $orderItem = $item;
                break 2; //Tìm thấy là break luôn 
            }
        }
    }
    if (!$orderItem) {
        return back()->with('error', 'Bạn đánh giá tất cả các đơn hàng trước của sản phẩm này');
    }

    // Tạo review mới
    $review = Review::create([
        'user_id'      => $user->id,
        'product_id'   => $productId,
        'order_id'     => $orderItem->order_id,
        'order_item_id'=> $orderItem->id,
        'rating'       => $req->rating,
        'review_text'  => $req->review_text,
        'is_active'  => 1,
    ]);
    //upload ảnh 
    if ($req->hasFile('images')) {
        foreach ($req->file('images') as $file){
            $path = $file->store('reviews', 'public');
            ReviewImage::create([
                'review_id' => $review->id,
                'image_path' => $path,
            ]);
        }
    }

    return back()->with('success', 'Đánh giá của bạn đã được gửi thành công.');
}
}
