<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\admin\RejectCommentRequest;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(){
        $comments = Comment::with(['user', 'product'])
        ->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.reviews.index', compact('comments'));
    }

    public function showComment($id){
        $comment = Comment::with(['user', 'product'])->findOrFail($id);
        return view('admin.reviews.show', compact('comment'));
    }
    public function approve($id){
        $comment = Comment::findOrFail($id);
        $comment->is_active =1;
        $comment->reason = null;
        $comment->save();
        return redirect()->route('admin.reviews.comment.index')->with('success', 'Duyệt bình luận thành công');
    }
    public function reject(RejectCommentRequest $req, $id){
        $comment = Comment::findOrFail($id);
        $comment->is_active = 0;
        $comment->reason = $req->reason;
        $comment->save();
        return redirect()->route('admin.reviews.comment.index')
        ->with('success', 'Đã từ chối bình luận và lưu lý do');
    }
}
