<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogComment;
use Illuminate\Http\Request;

class BlogCommentController extends Controller
{
    public function index(Request $request)
    {
        $query = BlogComment::with(['post', 'user'])
            ->latest();

        // Lọc theo trạng thái
        if ($request->has('status') && in_array($request->status, ['pending', 'approved'])) {
            $query->where('is_active', $request->status === 'approved');
        }

        // Tìm kiếm
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('content', 'like', "%{$search}%")
                  ->orWhere('user_name', 'like', "%{$search}%")
                  ->orWhere('user_email', 'like', "%{$search}%");
            });
        }

        $comments = $query->paginate(15);
        
        return view('admin.blog.comments.index', compact('comments'));
    }

    public function approve(BlogComment $comment)
    {
        $comment->update(['is_active' => true]);
        return back()->with('success', 'Đã duyệt bình luận thành công');
    }

    public function destroy(BlogComment $comment)
    {
        $comment->delete();
        return back()->with('success', 'Đã xóa bình luận thành công');
    }

    /**
     * Trả lời một bình luận
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BlogComment  $comment
     * @return \Illuminate\Http\Response
     */
    public function reply(Request $request, BlogComment $comment)
    {
        $request->validate([
            'content' => 'required|string|min:3|max:1000',
        ]);

        // Tạo bình luận mới với parent_id là ID của bình luận đang được trả lời
        $reply = new BlogComment([
            'post_id' => $comment->post_id,
            'user_id' => auth()->id(),
            'content' => $request->content,
            'parent_id' => $comment->id,
            'is_active' => true, // Admin reply luôn được active
            'user_name' => auth()->user()->name,
            'user_email' => auth()->user()->email,
        ]);

        $reply->save();

        return back()->with('success', 'Đã gửi trả lời thành công');
    }
}
