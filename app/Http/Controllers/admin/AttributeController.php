<?php


namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Attribute;
use Illuminate\Http\Request;


class AttributeController extends Controller
{
    /**
     * Hiển thị danh sách thuộc tính
     */
    public function index(Request $request)
    {
        $query = Attribute::query()->withCount(['attributeValues' => function ($query) {
            $query->whereNull('deleted_at'); // Chỉ đếm attribute_values chưa xóa mềm
        }]);

        // Tìm kiếm theo tên
        if ($request->filled('search')) {
            $searchTerm = trim($request->search);
            $query->where('name', 'like', '%' . $searchTerm . '%');
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        // Sắp xếp
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');
        $allowedSortFields = ['name', 'is_variant', 'is_active', 'created_at', 'attribute_values_count'];

        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortDir);
        }

        $attributes = $query->paginate(10)->withQueryString();

        return view('admin.attributes.index', [
            'attributes' => $attributes,
            'sortBy' => $sortBy,
            'sortDir' => $sortDir
        ]);
    }


    /**
     * Hiển thị form tạo thuộc tính mới
     */
    public function create()
    {
        return view('admin.attributes.create');
    }


    /**
     * Lưu thuộc tính mới
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'is_variant' => 'nullable|integer|in:0,1',
                'is_active' => 'nullable|integer|in:0,1',
            ]);


            Attribute::create([
                'name' => $validated['name'],
                'is_variant' => (int) ($validated['is_variant'] ?? 0),
                'is_active' => (int) ($validated['is_active'] ?? 0),
            ]);


            return redirect()->route('admin.attributes.index')->with('success', 'Thuộc tính đã được tạo.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Không thể tạo thuộc tính: ' . $e->getMessage());
        }
    }


    /**
     * Hiển thị chi tiết thuộc tính
     */
    public function show($id)
    {
        $attribute = Attribute::findOrFail($id);
        return view('admin.attributes.show', compact('attribute'));
    }


    /**
     * Hiển thị form chỉnh sửa thuộc tính
     */
    public function edit($id)
    {
        $attribute = Attribute::findOrFail($id);
        return view('admin.attributes.edit', compact('attribute'));
    }


    /**
     * Cập nhật thuộc tính
     */
    public function update(Request $request, $id)
    {
        try {
            $attribute = Attribute::findOrFail($id);


            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'is_variant' => 'nullable|integer|in:0,1',
                'is_active' => 'nullable|integer|in:0,1',
            ]);


            $attribute->update([
                'name' => $validated['name'],
                'is_variant' => (int) ($validated['is_variant'] ?? 0),
                'is_active' => (int) ($validated['is_active'] ?? 0),
            ]);


            return redirect()->route('admin.attributes.index')->with('success', 'Thuộc tính đã được cập nhật.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Không thể cập nhật thuộc tính: ' . $e->getMessage());
        }
    }


    /**
     * Xóa mềm thuộc tính
     */
    public function destroy($id)
    {
        try {
            // Tải Attribute cùng với AttributeValues (chưa xóa mềm) và các sản phẩm liên kết
            $attribute = Attribute::with(['attributeValues' => function ($query) {
                $query->whereNull('deleted_at'); // Chỉ lấy attribute_values chưa xóa mềm
            }, 'attributeValues.products'])->findOrFail($id);

            // Kiểm tra xem có AttributeValue nào liên kết với sản phẩm hay không
            foreach ($attribute->attributeValues as $attributeValue) {
                if ($attributeValue->products()->exists()) {
                    return redirect()->back()->with('error', 'Không thể xóa thuộc tính "' . $attribute->name . '" vì có giá trị đang được liên kết với sản phẩm.');
                }
            }

            // Xóa mềm Attribute nếu không có liên kết
            $attribute->delete();

            return redirect()->route('admin.attributes.index')->with('success', 'Thuộc tính đã được xóa mềm.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Không thể xóa thuộc tính: ' . $e->getMessage());
        }
    }
    public function trashed(Request $request)
    {
        $query = Attribute::onlyTrashed();


        // Tìm kiếm theo tên
        if ($request->filled('search')) {
            $searchTerm = trim($request->search);
            $query->where('name', 'like', '%' . $searchTerm . '%');
        }


        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }


        // Sắp xếp
        $sortBy = $request->get('sort_by', 'deleted_at');
        $sortDir = $request->get('sort_dir', 'desc');
        $allowedSortFields = ['id', 'name', 'is_variant', 'is_active', 'deleted_at'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortDir);
        }


        $attributes = $query->paginate(10)->withQueryString();


        return view('admin.attributes.trashed', [
            'attributes' => $attributes,
            'sortBy' => $sortBy,
            'sortDir' => $sortDir
        ]);
    }
    public function restore($id)
    {
        try {
            $attribute = Attribute::withTrashed()->findOrFail($id);
            if ($attribute->trashed()) {
                $attribute->restore();
                return redirect()->route('admin.attributes.trashed')->with('success', 'Thuộc tính đã được khôi phục.');
            }
            return redirect()->route('admin.attributes.trashed')->with('error', 'Thuộc tính không bị xóa mềm.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Không thể khôi phục thuộc tính: ' . $e->getMessage());
        }
    }


    /**
     * Xóa vĩnh viễn thuộc tính
     */
    public function forceDelete($id)
    {
        try {
            $attribute = Attribute::withTrashed()->findOrFail($id);
            $attribute->forceDelete();
            return redirect()->route('admin.attributes.index')->with('success', 'Thuộc tính đã được xóa vĩnh viễn.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Không thể xóa vĩnh viễn thuộc tính: ' . $e->getMessage());
        }
    }


    /**
     * Hiển thị danh sách thuộc tính biến thể
     */
    public function variants()
    {
        $variants = Attribute::where('is_variant', true)
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->get();


        return view('admin.attributes.variants', compact('variants'));
    }


    /**
     * Xóa hàng loạt thuộc tính
     */
    public function bulkDelete(Request $request)
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'required|integer|exists:attributes,id'
            ]);


            $count = Attribute::whereIn('id', $validated['ids'])->delete();


            return response()->json([
                'success' => true,
                'message' => "Đã xóa thành công {$count} thuộc tính."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Không thể xóa thuộc tính: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Thay đổi trạng thái hàng loạt
     */
    public function bulkToggle(Request $request)
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'required|integer|exists:attributes,id',
                'status' => 'required|boolean'
            ]);


            $count = Attribute::whereIn('id', $validated['ids'])
                ->update(['is_active' => $validated['status']]);


            return response()->json([
                'success' => true,
                'message' => "Đã cập nhật trạng thái {$count} thuộc tính."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Không thể cập nhật trạng thái: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Khôi phục hàng loạt thuộc tính
     */
    public function bulkRestore(Request $request)
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'required|integer|exists:attributes,id'
            ]);


            $count = Attribute::withTrashed()
                ->whereIn('id', $validated['ids'])
                ->whereNotNull('deleted_at')
                ->restore();


            return response()->json([
                'success' => true,
                'message' => "Đã khôi phục thành công {$count} thuộc tính."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Không thể khôi phục thuộc tính: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Xóa vĩnh viễn hàng loạt thuộc tính
     */
    public function bulkForceDelete(Request $request)
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'required|integer|exists:attributes,id'
            ]);


            $count = Attribute::withTrashed()
                ->whereIn('id', $validated['ids'])
                ->forceDelete();


            return response()->json([
                'success' => true,
                'message' => "Đã xóa vĩnh viễn thành công {$count} thuộc tính."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Không thể xóa vĩnh viễn thuộc tính: ' . $e->getMessage()
            ], 500);
        }
    }
}
