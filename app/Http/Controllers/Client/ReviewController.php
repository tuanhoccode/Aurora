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
        //Admin không được đánh giá và bình luận 
        if ($user->role === 'admin') {
            return back()->with('error', 'Admin không thể tạo đánh giá và bình luận');
        }
        if ($req->filled('rating')) {

            //Kiểm tra người dùng mua sản phẩm chưa
            $hasPurchased  = Order::where('user_id', Auth::id())
                 ->whereHas('currentStatus', function ($q) {
            $q->where('order_status_id', 4); // hoặc dùng status->code nếu có
            })
                ->whereHas('orderDetail', function ($q) use ($product) {
                    $q->where('product_id', $product->id);
                })->exists();
            if (!$hasPurchased) {
                return back()->with('error', 'Bạn chỉ có thể đánh giá khi bạn đã mua sản phẩm và nhận hàng thành công.');
            }

            //Kiểm tra đã đánh giá trước đó chưa
            $existingReview = Review::where('user_id', Auth::id())
                ->where('product_id', $product->id)->first();

            if ($existingReview) {
                return back()->with('error', 'Bạn đã đánh giá sản phẩm này rồi');
            }

            //Tìm đơn hàng để lưu vào order
            $order  = Order::where('user_id', $user->id)
                ->whereHas('orderDetail', function ($q) use ($product) {
                    $q->where('product_id', $product->id);
                })->latest()->first();


            Review::create([
                'product_id' => $product->id,
                'order_id' => $order?->id,
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
