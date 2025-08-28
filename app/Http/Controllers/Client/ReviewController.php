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
    $orderItemId = $req->order_item_id;
    $user = Auth::user();
    //Admin và nhân viên k đươjc đánh giá sp
    if (in_array($user->role, ['admin', 'employee'])) {
        return back()->with('error', 'Quản trị viên và nhân viên không được đánh giá sản phẩm');
    }
    // Lấy tất cả đơn hàng của user với sản phẩm này đã được giao
    $orderItem = OrderItem::where('id', $orderItemId)
        ->where('product_id', $productId)
        ->whereHas('order', fn($q)=>$q->where('user_id', $user->id)
        ->whereHas('currentStatus.status', fn($q)
        => $q->where('name', 'Nhận hàng thành công'))
        )->first();
    
    if(!$orderItem){
        return back()->with('error', 'Bạn chưa mua hoặc chưa nhận được sản phẩm này');
    }
    // Kiểm tra nếu order_item đã có review
    if (Review::where('order_item_id', $orderItemId)->where('user_id', $user->id)->exists()) {
        return back()->with('error', 'Bạn đã đánh giá sản phẩm này rồi.');
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
