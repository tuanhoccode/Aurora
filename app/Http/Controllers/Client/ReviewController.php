<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ReviewRequest;
use App\Models\Comment;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class ReviewController extends Controller
{
    public function store(ReviewRequest $req, Product $product)
    {

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập.');
        }
        // Admin không được đánh giá và bình luận 
        if ($user->role === 'admin' || $user->role === 'employee') {
            return back()->with('error', 'Admin và nhân viên không thể tạo đánh giá và bình luận');
        }
        if ($req->filled('rating')) {

            //Kiểm tra người dùng mua sản phẩm chưa
            $orders = Order::where('user_id', $user->id)
                 ->whereHas('currentStatus', function ($q) {
            $q->where('order_status_id', 4); 
            })
                ->whereHas('orderDetail', function ($q) use ($product) {
                    $q->where('product_id', $product->id);
                })->get();
            if ($orders->isEmpty()) {
                return back()->with('error', 'Bạn chỉ có thể đánh giá khi bạn đã mua sản phẩm và nhận hàng thành công.');
            }

            //Lấy danh sách order_id user đã đánh giá cho sản phẩm này
            $reviewedOrderIds = Review::where('user_id', $user->id)
            -> where('product_id', $product->id)
            ->pluck('order_id')
            ->toArray();

            //Tìm xem ddown hàng nào chưa được đánh giá
            $orderToReview = $orders->first(function ($order) use ($reviewedOrderIds){
                return !in_array($order->id, $reviewedOrderIds);
            });

            if (!$orderToReview) {
                return back()->with('error', 'Bạn đã đánh giá hết tất cả các lần mua sản phẩm này rồi');
            }

            Review::create([
                'product_id' => $product->id,
                'order_id' => $orderToReview?->id,
                'user_id' => $user->id,
                'rating' => $req->rating,
                'review_text' => $req->review_text,
                'is_active' =>  0,
            ]);
            return back()->with('success', 'Đánh giá của bạn đang chờ kiểm duyệt và sẽ hiển thị sau khi được duyệt.');
        } else {
            Comment::create([
                'product_id' => $product->id,
                'user_id' => $user->id,
                'content' => $req->review_text,
                'is_active' =>  0,
            ]);
        }
        return back()-> with('success', 'Bình luận của bạn đang chờ kiểm duyệt và sẽ hiển thị sau khi được duyệt.');
    }   
}
