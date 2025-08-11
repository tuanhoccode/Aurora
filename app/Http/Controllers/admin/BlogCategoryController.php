<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BlogCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Nếu là trang thùng rác, chuyển hướng đến trang trash
        if ($request->has('status') && $request->status === 'trashed') {
            return redirect()->route('admin.blog.categories.trash');
        }
        
        // Khởi tạo query
        $query = BlogCategory::with('parent')
            ->withCount('posts')
            ->whereNull('deleted_at');
        
        // Tìm kiếm theo tên
        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }
        
        // Lọc theo trạng thái
        if ($status = $request->input('status')) {
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }
        
        // Lọc theo danh mục cha
        if ($parentId = $request->input('parent_id')) {
            if ($parentId === 'none') {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $parentId);
            }
        }
        
        // Sắp xếp
        $query->latest();
        
        // Phân trang
        $perPage = $request->input('per_page', 10);
        $categories = $query->paginate($perPage)->withQueryString();
        
        // Lấy danh sách danh mục cha cho dropdown
        $parentCategories = BlogCategory::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
            
        // Thống kê
        $stats = [
            'all' => BlogCategory::count(),
            'active' => BlogCategory::where('is_active', true)->count(),
            'inactive' => BlogCategory::where('is_active', false)->count(),
            'trashed' => BlogCategory::onlyTrashed()->count(),
        ];
            
        return view('admin.blog.categories.index', compact('categories', 'parentCategories', 'stats'));
    }
    
    /**
     * Hiển thị trang thùng rác
     */
    public function trash()
    {
        $categories = BlogCategory::onlyTrashed()
            ->withCount('posts')
            ->latest('deleted_at')
            ->paginate(10);
            
        $trashedCount = BlogCategory::onlyTrashed()->count();
            
        return view('admin.blog.categories.trash', compact('categories', 'trashedCount'));
    }
    
    /**
     * Làm trống thùng rác
     */
    public function emptyTrash()
    {
        $trashedCount = BlogCategory::onlyTrashed()->count();
        
        if ($trashedCount > 0) {
            // Lấy tất cả danh mục đã xóa
            $categories = BlogCategory::onlyTrashed()->get();
            
            // Xóa vĩnh viễn từng danh mục để xử lý các ràng buộc
            foreach ($categories as $category) {
                // Xóa ảnh đại diện nếu có
                if ($category->image) {
                    Storage::delete('public/' . $category->image);
                }
                $category->forceDelete();
            }
            
            return redirect()->route('admin.blog.categories.trash')
                ->with('success', 'Đã xóa vĩnh viễn ' . $trashedCount . ' danh mục khỏi thùng rác');
        }
        
        return redirect()->route('admin.blog.categories.trash')
            ->with('info', 'Không có danh mục nào trong thùng rác');
    }
    
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $category = BlogCategory::withTrashed()
            ->with(['parent', 'children', 'posts' => function($query) {
                $query->where('is_active', true);
            }])
            ->withCount(['posts' => function($query) {
                $query->where('is_active', true);
            }])
            ->findOrFail($id);
            
        return view('admin.blog.categories.show', compact('category'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $parentCategories = BlogCategory::where('is_active', BlogCategory::IS_ACTIVE)
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get();
            
        return view('admin.blog.categories.create', compact('parentCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:blog_categories,name',
            'parent_id' => 'nullable|exists:blog_categories,id',
            'is_active' => 'required|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('uploads/blog/categories', 'public');
            $validated['image'] = 'storage/' . $imagePath;
        }



        $category = BlogCategory::create($validated);

        return redirect()
            ->route('admin.blog.categories.index')
            ->with('success', 'Đã thêm danh mục mới thành công');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $category = BlogCategory::withTrashed()->findOrFail($id);
        
        // Lấy danh sách danh mục cha dưới dạng collection thay vì mảng đơn giản
        $parentCategories = BlogCategory::whereNull('parent_id')
            ->where('id', '!=', $id)
            ->orderBy('name')
            ->get();
        
        return view('admin.blog.categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $category = BlogCategory::withTrashed()->findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:blog_categories,name,' . $id,
            'parent_id' => 'nullable|exists:blog_categories,id|not_in:' . $id,
            'is_active' => 'required|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'remove_image' => 'nullable|boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($category->image) {
                $oldImage = str_replace('storage/', '', $category->image);
                Storage::disk('public')->delete($oldImage);
            }
            
            $imagePath = $request->file('image')->store('uploads/blog/categories', 'public');
            $validated['image'] = 'storage/' . $imagePath;
        } elseif (isset($validated['remove_image']) && $validated['remove_image']) {
            // Remove order from validated data as we don't use it anymore
            if (isset($validated['order'])) {
                unset($validated['order']);
            }
        
            // Remove image if checkbox is checked
            if ($category->image) {
                $oldImage = str_replace('storage/', '', $category->image);
                Storage::disk('public')->delete($oldImage);
                $validated['image'] = null;
            }
            unset($validated['remove_image']);
        }

        $category->update($validated);

        return redirect()
            ->route('admin.blog.categories.index')
            ->with('success', 'Đã cập nhật danh mục thành công');
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy($id)
    {
        $category = BlogCategory::findOrFail($id);
        $category->delete();

        return redirect()
            ->route('admin.blog.categories.index')
            ->with('success', 'Đã chuyển danh mục vào thùng rác');
    }
    
    /**
     * Force delete the specified resource from storage.
     */
    public function forceDelete($id)
    {
        $category = BlogCategory::withTrashed()->findOrFail($id);
        
        // Delete image if exists
        if ($category->image) {
            $imagePath = str_replace('storage/', '', $category->image);
            Storage::disk('public')->delete($imagePath);
        }
        
        $category->forceDelete();
        
        return redirect()
            ->route('admin.blog.categories.index', ['status' => 'trashed'])
            ->with('success', 'Đã xóa vĩnh viễn danh mục');
    }
    
    /**
     * Restore the specified resource from trash.
     */
    public function restore($id)
    {
        $category = BlogCategory::withTrashed()->findOrFail($id);
        $category->restore();
        
        return redirect()
            ->route('admin.blog.categories.index', ['status' => 'trashed'])
            ->with('success', 'Đã khôi phục danh mục thành công');
    }
    
    /**
     * Get parent categories for dropdown.
     */
    private function getParentCategories($excludeId = null)
    {
        $query = BlogCategory::whereNull('parent_id');
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->pluck('name', 'id');
    }
}
