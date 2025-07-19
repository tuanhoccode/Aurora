<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\admin\RejectCommentRequest;
use App\Http\Requests\Admin\ReplyRequest;
use App\Models\Comment;
use App\Models\Product;
use App\Models\Review;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    public function index()
{
    // Lấy reviews
    $reviews = Review::with(['user', 'product'])
        ->select('id', 'user_id', 'product_id', 'review_text as content', 'rating', 'is_active', 'reason', 'created_at')
        ->get()
        ->map(function ($comment) {
            $comment->type = 'review';
            return $comment;
        });

    // Lấy comments
    $comments = Comment::with(['user', 'product'])
        ->select('id', 'user_id', 'product_id', 'content', 'is_active', 'reason', 'created_at')
        ->get()
        ->map(function ($comment) {
            $comment->type = 'comment';
            $comment->rating = null;
            return $comment;
        });

    // Gộp và sắp xếp
    $merged = $reviews->concat($comments)
        ->sortByDesc('created_at')
        ->values(); // reset key

    // Phân trang
    $perPage = 10;
    $currentPage = LengthAwarePaginator::resolveCurrentPage();
    $pagedData = $merged->forPage($currentPage, $perPage);
    $pagination = new LengthAwarePaginator($pagedData, $merged->count(), $perPage, $currentPage, [
        'path' => request()->url(),
        'query' => request()->query(),
    ]);

    return view('admin.reviews.index', [
        'mergedList' => $pagination,
        'trashComments' => Comment::onlyTrashed()->count(),
    ]);
}

    public function showComment($type, $id)
    {
        if ($type ==='review') {
            $item = Review::with(['user', 'product'])->findOrFail($id);
        } else {
            $item = Comment::with(['user', 'product'])->findOrFail($id);
        }
        
        return view('admin.reviews.show', ['comment' => $item, 'type' => $type]);
    }
    public function approve($type, $id)
    {
        if ($type === 'review') {
            $comment = Review::findOrFail($id);
        } else {
            $comment= Comment::findOrFail($id);
        }
        
        $comment->is_active = 1;
        $comment->reason = null;
        $comment->save();
        return redirect()->route('admin.reviews.comments')->with('success', 'Duyệt bình luận thành công');
    }
    public function reject(RejectCommentRequest $req, $type, $id)
    {
        if ($type === 'review') {
            $comment = Review::findOrFail($id);
        } else {
            $comment= Comment::findOrFail($id);
        }
        $comment->is_active = 0;
        $comment->reason = $req->reason;
        $comment->save();
        return redirect()->route('admin.reviews.comments')
            ->with('success', 'Đã từ chối bình luận và lưu lý do');
    }

    public function destroyComment($id){
        $comment = Comment::findOrFail($id);
        $comment->delete(); 
        return redirect()->back()->with('success', 'Đã xóa bình luận(mềm)');
    }

    public function trashComments()
    {
        $trashComments = Comment::onlyTrashed()->with(['user', 'product'])->paginate(10);
        return view('admin.reviews.trashComment', compact('trashComments'));
    }
    
    // Khôi phục 1 sản phẩm
    public function restore($id)
    {
        $comment = Comment::withTrashed()->findOrFail($id);
        $comment->restore();
        return redirect()->route('admin.reviews.comments')->with('success', 'Đã khôi phục sản phẩm');
    }
    // Xóa vĩnh viễn 1 sản phẩm
    public function forceDelete($id){
        try {
            $comment = Comment::withTrashed()->findOrFail($id);
            Comment::withTrashed()->where('parent_id', $id)->orderByDesc('deleted_at')->forceDelete();
            $comment->forceDelete();
            return redirect()->route('admin.reviews.trashComments')->with('success', 'Đã xõa vĩnh viễn sản phẩm');
        } catch (\Exception $e) {
            \Log::error("Lỗi xóa bình luận:". $e->getMessage());
            return redirect()->back()->with('error', 'Không thể xóa bình luận. Có thể vì bình luận có phản hồi.');
        }
    }
    // Khôi phục hàng loạt
    public function bulkRestore(Request $request)
    {
        $ids = $request->ids;
        Comment::withTrashed()->whereIn('id', $ids)->restore();

        return redirect()->route('admin.reviews.trash')->with('success', 'Đã khôi phục tất cả');
    }
    // Xóa hàng loạt vĩnh viễn
    public function bulkForceDelete(Request $request)
    {
        $ids = $request->ids;
        Comment::withTrashed()->whereIn('id', $ids)->forceDelete();

        return redirect()->route('admin.reviews.trash')->with('success', 'Đã xóa vĩnh viễn thành công');
    }

    //phản hồi comment
    public function reply(ReplyRequest $req, $type, $id){
        try {
            
            if ($type === 'review') {
                $parent = Review::findOrFail($id);
                if (!$parent->is_active) {
                    return redirect()->back()->with('error', 'Không thể trả lời vì đánh giá này đã bị từ chối.');
                }
                if ($parent -> has_replies) {
                    //Kiểm tra review đã có phản hồi chx
                    return redirect()->back()->with('error', 'Đánh giá này đã đc trả lời');
                }
                Review::create([
                    'user_id' =>Auth::id(),
                    'product_id' => $parent->product_id,
                    'order_id' => $parent->order_id,
                    'review_id' => $id,
                    'review_text' => $req ->content,
                    'rating' => 0,
                    'is_active' => 1,
                ]);
            } else {
                $parent = Comment::findOrFail($id);
                
                if (!$parent->is_active) {
                    return redirect()->back()->with('error', 'Không thể trả lời vì bình luận này đã bị từ chối.');
                }
                if ($parent -> has_replies) {
                    //Kiểm tra review đã có phản hồi chx
                    return redirect()->back()->with('error', 'Bình luận này đã đc trả lời');
                }
                Comment::create([
                    'user_id' => Auth::id(),
                    'product_id' => $parent->product_id,
                    'parent_id' => $id,
                    'content' => $req->content,
                    'is_active' => 1,
        
                ]);
            }
            return redirect()->back()->with('success', 'Đã trả lời bình luận của khách hàng.');
        } catch (\Exception $e) {
            Log::error('Reply error: Type='. $type . ', ID=' . $id . ',Error=' . $e->getMessage());
            return redirect()->back()->with('error', 'Đã xảy ra lỗi khi gửi phản hồi.');
        }
        
    }

    public function showStar($id){
        $product = Product::findOrFail($id);
        $reviews  = $product->reviews()
        ->where('is_active', 1)
        ->where('review_id', null)
        ->where('rating', '>=', 1)
        ->get();
        //Tinh điểm trung bình
        $averageRating = $reviews->count() > 0 ? $reviews -> avg('rating') :0;
        //Tính số lượng đánh giá
        $reviewCount = $reviews->count();
        return view('product-details', compact('product', 'averageRating', 'reviewCount', 'hasVariant')) ;
    }
}
