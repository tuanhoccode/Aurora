<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index(Request $request)
{
    $sortBy = $request->input('sort_by', 'created_at');
    $sortDir = $request->input('sort_dir', 'desc');

    $categories = Category::query()
        ->when($request->filled('search'), function ($query) use ($request) {
            $query->where('name', 'like', "%{$request->search}%");
        })
        ->when($request->filled('status'), function ($query) use ($request) {
            $query->where('is_active', $request->status);
        })
        ->with(['parent']) // Load quan hệ
        ->withCount('products') //  Thêm dòng này
        ->orderBy($sortBy, $sortDir)
        ->paginate(10);

    return view('admin.categories.index', compact('categories', 'sortBy', 'sortDir'));
}

    public function create()
    {
        if (Auth::user()->role !== 'admin') {
                abort(403, 'Bạn không có quyền Thêm danh mục.');
        }
        $categories = Category::active()->get();
        return view('admin.categories.create', compact('categories'));
    }

    public function show(Category $category)
    {
        return view('admin.categories.show', compact('category'));
    }

    public function store(CategoryRequest $request)
    {
        try {
            if (Auth::user()->role !== 'admin') {
                abort(403, 'Bạn không có quyền xóa danh mục.');
            }
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
        if (Auth::user()->role !== 'admin') {
        abort(403, 'Bạn không có quyền xóa danh mục.');
        }
        $categories = Category::active()
            ->where('id', '!=', $category->id)
            ->get();
        return view('admin.categories.edit', compact('category', 'categories'));
    }

    public function update(CategoryRequest $request, Category $category)
    {
        try {
            if (Auth::user()->role !== 'admin') {
                abort(403, 'Bạn không có quyền xóa danh mục.');
            }
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
            if(Auth::user()->role !== 'admin'){
                abort(403, 'Bạn không có quyền xóa danh mục');
            }
            // Không cho xóa nếu có sản phẩm
            if ($category->products()->count() > 0) {
                return redirect()
                    ->back()
                    ->with('error', 'Không thể xóa vì danh mục đang chứa sản phẩm.');
            }

            // Không cho xóa nếu có danh mục con
            if ($category->children()->count() > 0) {
                return redirect()
                    ->back()
                    ->with('error', 'Không thể xóa vì danh mục này đang có danh mục con.');
            }

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
        //K cho quyền cho nhân viên 
        if (Auth::user()->role !== 'admin') {
                abort(403, 'Bạn không có quyền xóa danh mục.');
        }
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

        return view('admin.categories.trash', compact('trashedCategories', 'sortBy', 'sortDir'));
    }

    public function restore($id)
    {
        try {
            if (Auth::user()->role !== 'admin') {
                abort(403, 'Bạn không có quyền xóa danh mục.');
            }
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
            //Không cho nhân viên vào
            if (Auth::user()->role !== 'admin') {
                abort(403, 'Bạn không có quyền xóa danh mục.');
            }
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
                'error' => 'Vui lòng chọn ít nhất một danh mục'
            ], 400);
        }

        // Lấy danh mục có đếm số sản phẩm
        $categories = Category::withCount('products')
            ->whereIn('id', $ids)
            ->get();

        // Danh mục có thể xóa (không có sản phẩm)
        $deletable = $categories->filter(fn($cat) => $cat->products_count == 0);

        // Danh mục không thể xóa (có sản phẩm)
        $nonDeletable = $categories->filter(fn($cat) => $cat->products_count > 0);

        // Xóa các danh mục không có sản phẩm
        foreach ($deletable as $category) {
            $category->delete();
        }

        return response()->json([
            'success' => $deletable->isNotEmpty()
                ? 'Đã chuyển các danh mục không chứa sản phẩm vào thùng rác.'
                : null,
            'warning' => $nonDeletable->isNotEmpty()
                ? 'Một số danh mục không thể xóa vì đang chứa sản phẩm: ' . $nonDeletable->pluck('name')->join(', ')
                : null,
            'deleted_ids' => $deletable->pluck('id'),
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
