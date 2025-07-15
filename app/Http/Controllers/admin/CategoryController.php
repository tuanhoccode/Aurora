<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        // Lấy tham số sắp xếp từ request
        $sortBy = $request->input('sort_by', 'created_at'); // Mặc định sắp xếp theo ngày tạo
        $sortDir = $request->input('sort_dir', 'desc'); // Mặc định sắp xếp giảm dần

        $categories = Category::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', "%{$request->search}%");
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('is_active', $request->status);
            })
            ->orderBy($sortBy, $sortDir)
            ->with('parent') // Eager load quan hệ parent
            ->latest()
            ->paginate(10);

        return view('admin.category.index', compact('categories', 'sortBy', 'sortDir'));
    }

    public function create()
    {
        $categories = Category::active()->get();
        return view('admin.category.create', compact('categories'));
    }

    public function show(Category $category)
    {
        return view('admin.category.show', compact('category'));
    }

    public function store(CategoryRequest $request)
    {
        try {
            $data = $request->validated();

            // Xử lý upload ảnh
            if ($request->hasFile('icon')) {
                $data['icon'] = $request->file('icon')->store('categories', 'public');
            }

            Category::create($data);

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'Thêm danh mục thành công');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Đã có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function edit(Category $category)
    {
        $categories = Category::active()
            ->where('id', '!=', $category->id)
            ->get();
        return view('admin.category.edit', compact('category', 'categories'));
    }

    public function update(CategoryRequest $request, Category $category)
    {
        try {
            $data = $request->validated();

            // Xử lý upload ảnh mới
            if ($request->hasFile('icon')) {
                // Xóa ảnh cũ nếu có
                if ($category->icon) {
                    Storage::disk('public')->delete($category->icon);
                }
                $data['icon'] = $request->file('icon')->store('categories', 'public');
            }

            $category->update($data);

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'Cập nhật danh mục thành công');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Đã có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function destroy(Category $category)
    {
        try {
            $category->delete();
            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'Đã chuyển danh mục vào thùng rác');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Đã có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function trash(Request $request)
    {
        // Lấy tham số sắp xếp từ request
        $sortBy = $request->input('sort_by', 'deleted_at'); // Mặc định sắp xếp theo ngày xóa
        $sortDir = $request->input('sort_dir', 'desc'); // Mặc định sắp xếp giảm dần

        $trashedCategories = Category::onlyTrashed()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', "%{$request->search}%");
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('is_active', $request->status);
            })
            ->orderBy($sortBy, $sortDir)
            ->paginate(10);

        return view('admin.category.trash', compact('trashedCategories', 'sortBy', 'sortDir'));
    }

    public function restore($id)
    {
        try {
            $category = Category::onlyTrashed()->findOrFail($id);
            $category->restore();

            return redirect()
                ->route('admin.categories.trash')
                ->with('success', 'Khôi phục danh mục thành công');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Đã có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function forceDelete($id)
    {
        try {
            $category = Category::onlyTrashed()->findOrFail($id);
            
            // Xóa ảnh nếu có
            if ($category->icon) {
                Storage::disk('public')->delete($category->icon);
            }
            
            $category->forceDelete();

            return redirect()
                ->route('admin.categories.trash')
                ->with('success', 'Xóa danh mục vĩnh viễn thành công');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Đã có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function bulkDelete(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            if (empty($ids)) {
                return response()->json([
                    'error' => 'Vui lòng chọn ít nhất một thương hiệu'
                ], 400);
            }

            Category::whereIn('id', $ids)->delete();

            return response()->json([
                'success' => 'Đã chuyển các thương hiệu đã chọn vào thùng rác'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Đã có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkForceDelete(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            if (empty($ids)) {
                return response()->json([
                    'error' => 'Vui lòng chọn ít nhất một thương hiệu'
                ], 400);
            }

            $categories = Category::onlyTrashed()->whereIn('id', $ids)->get();
            
            foreach ($categories as $category) {
                if ($category->icon) {
                    Storage::disk('public')->delete($category->icon);
                }
                $category->forceDelete();
            }

            return response()->json([
                'success' => 'Đã xóa vĩnh viễn các thương hiệu đã chọn'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Đã có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkRestore(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            if (empty($ids)) {
                return response()->json([
                    'error' => 'Vui lòng chọn ít nhất một danh mục'
                ], 400);
            }

            Category::onlyTrashed()
                ->whereIn('id', $ids)
                ->restore();

            return response()->json([
                'success' => 'Khôi phục các danh mục đã chọn thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Đã có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    // Thêm phương thức toggle status cho một danh mục
    public function toggleStatus(Category $category)
    {
        try {
            $category->update([
                'is_active' => !$category->is_active
            ]);

            return response()->json([
                'success' => 'Cập nhật trạng thái thành công',
                'new_status' => $category->is_active
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Đã có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    // Thêm phương thức bulk toggle status
    public function bulkToggle(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            if (empty($ids)) {
                return response()->json([
                    'error' => 'Vui lòng chọn ít nhất một danh mục'
                ], 400);
            }

            $status = $request->input('status');
            if (!isset($status)) {
                return response()->json([
                    'error' => 'Trạng thái không hợp lệ'
                ], 400);
            }

            Category::whereIn('id', $ids)->update(['is_active' => $status]);

            return response()->json([
                'success' => 'Cập nhật trạng thái các danh mục đã chọn thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Đã có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
} 