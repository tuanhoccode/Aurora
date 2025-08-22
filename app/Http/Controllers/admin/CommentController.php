<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Requests\admin\RejectCommentRequest;
use App\Http\Requests\Admin\ReplyRequest;
use App\Models\Comment;
use App\Models\Product;
use App\Models\Review;
use App\Notifications\AdminRepliedNotification;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    public function index()
    {
        $reviews = Review::with(['user', 'product'])
            ->select('id', 'user_id', 'product_id', 'review_text', 'rating', 'is_active', 'has_replies', 'reason', 'created_at')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        foreach($reviews as $review){
            $review->type = $review ->review_id ? 'reply' : 'review';
        }
        return view('admin.reviews.index', [
            'mergedList' => $reviews,   // vẫn dùng biến mergedList cho view
            'trashComments' => Review::onlyTrashed()->count(), // nếu muốn vẫn theo dõi thùng rác
        ]);
    }

    public function showComment($id)
    {
        $review = Review::with(['user', 'product', 'images'])->findOrFail($id);

        return view('admin.reviews.show', ['review' => $review]);
    }
    public function approve( $id)
    {
        $comment = Review::findOrFail($id);
        $comment->is_active = 1;
        $comment->reason = null;
        $comment->save();
        return redirect()->route('admin.reviews.comments')->with('success', 'Duyệt bình luận thành công');
    }
    public function reject(RejectCommentRequest $req, $type, $id)
    {
        $comment = Review::findOrFail($id);
        $comment->is_active = 0;
        $comment->reason = $req->reason;
        $comment->save();
        return redirect()->route('admin.reviews.comments')
            ->with('success', 'Đã từ đánh giá và lưu lý do');
    }
    public function destroyComment($id)
    {
        $comment = Review::findOrFail($id);
        $comment->delete();
        return redirect()->back()->with('success', 'Đã xóa mềm đánh giá');
    }
    public function trashComments()
    {
        $trashComments = Review::onlyTrashed()->with(['user', 'product'])->orderByDesc('deleted_at')->paginate(10);
        return view('admin.reviews.trashComment', compact('trashComments'));
    }

    // Khôi phục 1 sản phẩm
    public function restore($id)
    {
        $review = Review::withTrashed()->findOrFail($id); 
        $review->restore();
        return redirect()->route('admin.reviews.comments')->with('success', 'Đã khôi phục sản phẩm');
    }
    // Xóa vĩnh viễn 1 sản phẩm
    public function forceDelete($id)
    {
        try {
            $review = Review::withTrashed()->findOrFail($id);
            Review::withTrashed()->where('review_id', $id)->orderByDesc('deleted_at')->forceDelete();
            $review->forceDelete();
            return redirect()->route('admin.reviews.trashComments')->with('success', 'Đã xõa vĩnh viễn sản phẩm');
        } catch (\Exception $e) {
            \Log::error("Lỗi xóa bình luận:" . $e->getMessage());
            return redirect()->back()->with('error', 'Không thể xóa bình luận. Có thể vì bình luận có phản hồi.');
        }
    }
    // Khôi phục hàng loạt

    //phản hồi review
    public function reply(ReplyRequest $req, $type, $id)
    {
        try {
            $parent = Review::findOrFail($id);
            if (!$parent->is_active) {
                return redirect()->back()->with('error', 'Không thể trả lời vì đánh giá này đã bị từ chối.');
            }
            if ($parent->has_replies) {
                //Kiểm tra review đã có phản hồi chx
                return redirect()->back()->with('error', 'Đánh giá này đã đc trả lời');
            }
            if ($parent->review_id !== null) {
                return redirect()->back()->with('error', 'Đây là phản hồi của admin');
            }   
            Review::create([
                'user_id' => Auth::id(),
                'product_id' => $parent->product_id,
                'order_id' => $parent->order_id,
                'review_id' => $id,
                'review_text' => $req->content,
                'rating' => 0,
                'is_active' => 1,
            ]);
            $parent->has_replies = true;
            $parent->save();
        //Gử thông báo email cho khách hàng viết đánh giá
        $user = $parent->user;
        if ($user && $user->email) {
            $user->notify(new AdminRepliedNotification($parent, $parent->review_text, $req->content));
        }
        
        return redirect()->back()->with('success', 'Đã trả lời bình luận của khách hàng.');
        } catch (\Exception $e) {
            Log::error('Reply error: Type='. $type . ', ID=' . $id . ',Error=' . $e->getMessage());
            return redirect()->back()->with('error', 'Đã xảy ra lỗi khi gửi phản hồi.');
        }

    }

    public function showStar($id)
    {
        $product = Product::findOrFail($id);
        $reviews  = $product->reviews()
            ->where('is_active', 1)
            ->where('review_id', null)
            ->where('rating', '>=', 1)
            ->get();
        //Tinh điểm trung bình
        $averageRating = $reviews->count() > 0 ? $reviews->avg('rating') : 0;
        //Tính số lượng đánh giá
        $reviewCount = $reviews->count();
        return view('product-details', compact('product', 'averageRating', 'reviewCount', 'hasVariant'));
    }

    public function searchComment(Request $req)
    {
        $search = $req->input('search');
        $reviews = Review::with(['user', 'product'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search){
                    $q->where('review_text', 'like', "%$search%")
                    ->orWhereHas('user', function ($q2) use ($search) {
                        $q2->where('fullname', 'like', "%$search%");
                    })
                    ->orWhereRaw("DATE_FORMAT(created_at, '%d-%m-%Y') LIKE ? 
                    OR DATE_FORMAT(created_at, '%d/%m/%Y') LIKE ? ", ["%$search%", "%$search%"]);
                });
            })
            ->orderByDesc('created_at')
            ->paginate(10);
        // gán type  cho mỗi comment

        foreach ($reviews as $review) {
            $review->type = $review->review_id ? 'comment' : 'review';
        }

        return view('admin.reviews.index', [
            'mergedList' => $reviews,
            'trashComments' => Review::onlyTrashed()->count(),
            'search' => $search,
        ]);
    }
}
