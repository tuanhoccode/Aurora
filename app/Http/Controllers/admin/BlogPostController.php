<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BlogPostRequest;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;

class BlogPostController extends Controller
{

    public function index(Request $request)
    {
        $query = BlogPost::with([
                'category', 
                'author' => function($q) {
                    $q->select('id', 'fullname as name', 'email');
                }
            ])
            ->whereNull('deleted_at')
            ->latest();
        
        // Apply search filter
        if ($request->has('search') && $search = $request->search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }
        
        // Apply category filter
        if ($request->has('category') && $categoryId = $request->category) {
            $query->where('category_id', $categoryId);
        }
        
        // Apply status filter
        if ($request->has('is_active') && in_array($request->is_active, ['0', '1'])) {
            $query->where('is_active', (bool)$request->is_active);
        }
        
        $posts = $query->paginate(10);
        $trashedCount = BlogPost::onlyTrashed()->count();
        $authors = \App\Models\User::whereHas('posts')->get();
        $categories = BlogCategory::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.blog.posts.index', compact('posts', 'trashedCount', 'authors', 'categories'));
    }

    public function trash()
    {
        $trashedPosts = BlogPost::onlyTrashed()
            ->with([
                'category', 
                'author' => function($q) {
                    $q->select('id', 'fullname as name', 'email');
                }
            ])
            ->latest('deleted_at')
            ->paginate(10);
            
        $trashedCount = BlogPost::onlyTrashed()->count();
        
        return view('admin.blog.posts.trash', compact('trashedPosts', 'trashedCount'));
    }
    
    public function show(BlogPost $post)
    {
        // Load các mối quan hệ cần thiết, đảm bảo tải cả thông tin tác giả
        $post->load([
            'category', 
            'author' => function($query) {
                $query->select('id', 'fullname as name', 'email');
            }, 
            'comments' => function($query) {
                $query->with(['user' => function($q) {
                    $q->select('id', 'fullname as name', 'email');
                }])->latest();
            }
        ]);
        
        return view('admin.blog.posts.show', compact('post'));
    }
    
    public function create()
    {
        $categories = BlogCategory::where('is_active', true)
            ->orderBy('name')
            ->pluck('name', 'id');
            
        return view('admin.blog.posts.create', compact('categories'));
    }

    public function store(BlogPostRequest $request)
    {
        try {
            $data = $request->validated();
            
            // Đảm bảo category_id là số nguyên
            $data['category_id'] = (int)$data['category_id'];
            $data['author_id'] = auth()->id();
            $data['is_active'] = isset($data['is_active']) ? (bool)$data['is_active'] : false;
            $data['allow_comments'] = isset($data['allow_comments']) ? (bool)$data['allow_comments'] : false;
            
            // Xử lý slug: loại bỏ http/https và các ký tự đặc biệt
            $slug = $data['slug'] ?? $data['title'];
            $slug = preg_replace('~^https?://~', '', $slug); // Loại bỏ http/https
            $slug = Str::slug($slug);
            
            // Kiểm tra và tạo slug duy nhất
            $originalSlug = $slug;
            $count = 1;
            while (BlogPost::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
            $data['slug'] = $slug;
            
            if ($request->hasFile('thumbnail')) {
                $data['thumbnail'] = $this->uploadThumbnail($request->file('thumbnail'));
            }

            // Tạo bài viết mới
            $post = BlogPost::create($data);

            return redirect()->route('admin.blog.posts.index')
                ->with('success', 'Thêm bài viết thành công');
                
        } catch (\Exception $e) {
            // Nếu có lỗi, trả về view với dữ liệu cũ và thông báo lỗi
            $categories = BlogCategory::where('is_active', true)
                ->orderBy('name')
                ->pluck('name', 'id');
                
            return back()
                ->withInput()
                ->withErrors(['error' => 'Có lỗi xảy ra khi lưu bài viết: ' . $e->getMessage()])
                ->with(compact('categories'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BlogPost  $post
     * @return \Illuminate\View\View
     */
    public function edit(BlogPost $post)
    {
        // Lấy danh sách danh mục đang hoạt động
        $categories = BlogCategory::where('is_active', true)
            ->orderBy('name')
            ->pluck('name', 'id');
            
        return view('admin.blog.posts.edit', compact('post', 'categories'));
    }

    public function update(BlogPostRequest $request, BlogPost $post)
    {
        $data = $request->validated();
        
        // Xử lý trường allow_comments
        $data['allow_comments'] = isset($data['allow_comments']) ? (bool)$data['allow_comments'] : false;
        
        // Xử lý slug: loại bỏ http/https và các ký tự đặc biệt
        $slug = $data['slug'] ?? $data['title'];
        $slug = preg_replace('~^https?://~', '', $slug); // Loại bỏ http/https
        $slug = Str::slug($slug);
        
        // Kiểm tra và tạo slug duy nhất nếu thay đổi
        if ($slug !== $post->slug) {
            $originalSlug = $slug;
            $count = 1;
            while (BlogPost::where('slug', $slug)->where('id', '!=', $post->id)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
            $data['slug'] = $slug;
        }
        
        if ($request->hasFile('thumbnail')) {
            // Xóa ảnh cũ nếu có
            if ($post->thumbnail) {
                Storage::delete('public/' . $post->thumbnail);
            }
            $data['thumbnail'] = $this->uploadThumbnail($request->file('thumbnail'));
        }

        $post->update($data);

        return redirect()->route('admin.blog.posts.index')
            ->with('success', 'Cập nhật bài viết thành công');
    }

    /**
     * Display a listing of the trashed posts.
     *
     * @return \Illuminate\View\View
     */
   
    /**
     * Move the specified post to trash.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = BlogPost::findOrFail($id);
        
        // Xóa mềm bài viết
        $post->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã chuyển bài viết vào thùng rác'
            ]);
        }

        return redirect()->route('admin.blog.posts.index')
            ->with('success', 'Đã chuyển bài viết vào thùng rác thành công');
    }

    /**
     * Restore the specified post from trash.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
        $post = BlogPost::onlyTrashed()->findOrFail($id);
        $post->restore();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã khôi phục bài viết thành công'
            ]);
        }

        return redirect()->route('admin.blog.posts.trash')
            ->with('success', 'Đã khôi phục bài viết thành công');
    }

    /**
     * Permanently delete the specified post.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function forceDelete($id)
    {
        $post = BlogPost::onlyTrashed()->findOrFail($id);
        
        // Xóa ảnh đại diện nếu có
        if ($post->thumbnail) {
            Storage::delete('public/' . $post->thumbnail);
        }
        
        // Xóa vĩnh viễn
        $post->forceDelete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa vĩnh viễn bài viết'
            ]);
        }

        return redirect()->route('admin.blog.posts.trash')
            ->with('success', 'Đã xóa vĩnh viễn bài viết');
    }

    /**
     * Empty the trash.
     *
     * @return \Illuminate\Http\Response
     */
    public function emptyTrash()
    {
        // Lấy tất cả bài viết đã xóa mềm
        $posts = BlogPost::onlyTrashed()->get();
        
        // Xóa ảnh đại diện và xóa vĩnh viễn từng bài
        foreach ($posts as $post) {
            if ($post->thumbnail) {
                Storage::delete('public/' . $post->thumbnail);
            }
            $post->forceDelete();
        }

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa vĩnh viễn tất cả bài viết trong thùng rác'
            ]);
        }

        return redirect()->route('admin.blog.posts.trash')
            ->with('success', 'Đã xóa vĩnh viễn tất cả bài viết trong thùng rác');
    }
    
    /**
     * Publish a single post
     */
    public function publish(BlogPost $post)
    {
        $post->update(['status' => 'published', 'published_at' => now()]);
        return response()->json(['success' => true]);
    }
    
    /**
     * Mark a single post as draft
     */
    public function draft(BlogPost $post)
    {
        $post->update(['status' => 'draft']);
        return response()->json(['success' => true]);
    }
    
    /**
     * Bulk publish posts
     */
    public function bulkPublish(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Không có bài viết nào được chọn']);
        }
        
        BlogPost::whereIn('id', $ids)
            ->update([
                'status' => 'published',
                'published_at' => now(),
                'updated_at' => now()
            ]);
            
        return response()->json(['success' => true, 'message' => 'Đã xuất bản ' . count($ids) . ' bài viết']);
    }
    
    /**
     * Bulk mark posts as draft
     */
    public function bulkDraft(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Không có bài viết nào được chọn']);
        }
        
        BlogPost::whereIn('id', $ids)
            ->update([
                'status' => 'draft',
                'updated_at' => now()
            ]);
            
        return response()->json(['success' => true, 'message' => 'Đã chuyển ' . count($ids) . ' bài viết thành bản nháp']);
    }
    
    /**
     * Bulk delete posts
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Vui lòng chọn ít nhất một bài viết');
        }

        $count = BlogPost::destroy($ids);

        return redirect()->back()->with('success', "Đã xóa {$count} bài viết vào thùng rác");
    }
    
    /**
     * Bulk activate posts
     */
    public function bulkActivate(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Vui lòng chọn ít nhất một bài viết');
        }
        
        $count = BlogPost::whereIn('id', $ids)
            ->update([
                'is_active' => true,
                'updated_at' => now()
            ]);
            
        return redirect()->back()->with('success', 'Đã kích hoạt ' . $count . ' bài viết');
    }
    
    /**
     * Bulk deactivate posts
     */
    public function bulkDeactivate(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Vui lòng chọn ít nhất một bài viết');
        }
        
        $count = BlogPost::whereIn('id', $ids)
            ->update([
                'is_active' => false,
                'updated_at' => now()
            ]);
            
        return redirect()->back()->with('success', 'Đã tắt ' . $count . ' bài viết');
    }

    protected function uploadThumbnail($file)
    {
        $fileName = time() . '_' . $file->getClientOriginalName();
        return $file->storeAs('uploads/blog/thumbnails', $fileName, 'public');
    }

    /**
     * Bulk restore posts from trash
     */
    public function bulkRestore(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Vui lòng chọn ít nhất một bài viết');
        }
        
        $count = BlogPost::onlyTrashed()
            ->whereIn('id', $ids)
            ->restore();
            
        return redirect()->back()->with('success', 'Đã khôi phục ' . $count . ' bài viết');
    }
    
    /**
     * Bulk force delete posts
     */
    public function bulkForceDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Vui lòng chọn ít nhất một bài viết');
        }
        
        $count = 0;
        $posts = BlogPost::onlyTrashed()->whereIn('id', $ids)->get();
        
        foreach ($posts as $post) {
            // Xóa ảnh đại diện nếu có
            if ($post->thumbnail) {
                Storage::disk('public')->delete($post->thumbnail);
            }
            
            // Xóa bài viết vĩnh viễn
            if ($post->forceDelete()) {
                $count++;
            }
        }
            
        return redirect()->back()->with('success', 'Đã xóa vĩnh viễn ' . $count . ' bài viết');
    }
}
