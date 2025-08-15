<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\BlogComment;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = BlogPost::with([
                'category',
                'author' => function($q) {
                    $q->select('id', 'fullname as name', 'email', 'avatar');
                }
            ])
            ->where('is_active', true)
            ->withCount(['comments' => function($q) {
                $q->where('is_active', true);
            }])
            ->orderBy('created_at', 'desc');

        // Tìm kiếm theo từ khóa
        if ($request->has('search') && $search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Lọc theo danh mục
        if ($request->has('category') && $categorySlug = $request->input('category')) {
            $query->whereHas('category', function($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        // Lọc theo tag (tạm thời bỏ qua vì chưa cần thiết)
        // if ($request->has('tag') && $tagSlug = $request->input('tag')) {
        //     $query->whereHas('tags', function($q) use ($tagSlug) {
        //         $q->where('slug', $tagSlug);
        //     });
        // }

        // Lọc theo tháng/năm
        if ($request->has('month') && $request->has('year')) {
            $query->whereYear('created_at', $request->year)
                  ->whereMonth('published_at', $request->month);
        }

        $posts = $query->paginate(10)->withQueryString();

        // Lấy danh sách các tháng có bài viết để hiển thị trong archive
        $archives = BlogPost::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
            ->where('is_active', true)
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // Lấy các bài viết phổ biến
        $popularPosts = BlogPost::with('category')
            ->where('is_active', true)
            ->orderBy('views', 'desc')
            ->take(5)
            ->get();

        $categories = BlogCategory::withCount(['posts' => function($query) {
                $query->where('is_active', true);
            }])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $recentComments = BlogComment::with(['post', 'user'])
            ->where('is_active', true)
            ->whereHas('post', function($q) {
                $q->where('is_active', true);
            })
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('client.blog.index', compact(
            'posts', 
            'categories', 
            'popularPosts', 
            'recentComments',
            'archives'
        ));
    }

    public function show($slug)
    {
        $post = BlogPost::with([
                'category',
                'author' => function($q) {
                    $q->select('id', 'fullname as name', 'email', 'avatar');
                },
                'comments' => function($query) {
                    $query->where('is_active', true)
                          ->orderBy('created_at', 'desc');
                },
                'comments.user' => function($q) {
                    $q->select('id', 'fullname as name', 'email', 'avatar');
                }
            ])
            ->withCount(['comments' => function($query) {
                $query->where('is_active', true);
            }])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Tăng view count
        $post->increment('views');

        // Lấy các bài viết liên quan (cùng danh mục)
        $relatedPosts = BlogPost::where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        // Lấy các bài viết phổ biến
        $popularPosts = BlogPost::with('category')
            ->where('is_active', true)
            ->where('id', '!=', $post->id)
            ->orderBy('views', 'desc')
            ->take(5)
            ->get();

        // Lấy các bình luận gần đây
        $recentComments = BlogComment::with('post')
            ->where('is_active', true)
            ->where('post_id', '!=', $post->id)
            ->whereHas('post', function($q) {
                $q->where('is_active', true);
            })
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Lấy danh sách categories với số lượng bài viết
        $categories = BlogCategory::withCount(['posts' => function($query) {
                $query->where('is_active', true);
            }])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('client.blog.show', compact(
            'post', 
            'relatedPosts', 
            'popularPosts', 
            'recentComments',
            'categories'
        ));
    }

    public function storeComment(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required|string|min:5|max:1000',
            'name' => 'required_if:user_id,null|string|max:255',
            'email' => 'required_if:user_id,null|email|max:255',
            'parent_id' => 'nullable|exists:blog_comments,id',
        ]);

        $post = BlogPost::findOrFail($postId);
        
        // Kiểm tra xem bài viết có cho phép bình luận không
        if (!$post->allow_comments) {
            return back()->with('error', 'Bài viết này đã tắt chức năng bình luận.');
        }

        // Tạo bình luận mới với is_active = false
        $comment = new BlogComment([
            'content' => $request->content,
            'is_active' => false, // Luôn đặt là false khi tạo mới
            'parent_id' => $request->parent_id,
            'user_agent' => $request->userAgent(),
            'ip_address' => $request->ip(),
        ]);

        if (Auth::check()) {
            $comment->user_id = Auth::id();
            $comment->user_name = Auth::user()->name;
            $comment->user_email = Auth::user()->email;
            
            // Nếu là admin hoặc tác giả bài viết, tự động duyệt bình luận
            if (Auth::user()->hasRole('admin') || Auth::id() === $post->user_id) {
                $comment->is_active = true;
            }
        } else {
            $comment->user_name = $request->name;
            $comment->user_email = $request->email;
        }

        $post->comments()->save($comment);

        // Gửi thông báo cho admin nếu cần
        if (!$comment->is_active) {
            // TODO: Gửi thông báo cho admin có bình luận mới cần duyệt
        } elseif ($comment->parent_id) {
            // TODO: Gửi thông báo cho người được phản hồi
        }

        $message = $comment->is_active 
            ? 'Bình luận của bạn đã được đăng.' 
            : 'Bình luận của bạn đã được gửi và đang chờ duyệt.';

        return back()->with('success', $message);
    }

    public function showByCategory($slug)
    {
        $category = BlogCategory::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $posts = $category->posts()
            ->where('is_active', true)
            ->with('author')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('client.blog.category', compact('category', 'posts'));
    }

    public function showByTag($slug)
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();

        $posts = $tag->blogPosts()
            ->where('is_active', true)
            ->with('author')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('client.blog.tag', compact('tag', 'posts'));
    }

    public function archive($year, $month = null)
    {
        $query = BlogPost::where('is_active', true)
            ->whereYear('created_at', $year);

        if ($month) {
            $query->whereMonth('created_at', $month);
        }

        $posts = $query->orderBy('created_at', 'desc')
            ->paginate(10);

        $period = $month 
            ? \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y')
            : $year;

        return view('client.blog.archive', compact('posts', 'period'));
    }
}
