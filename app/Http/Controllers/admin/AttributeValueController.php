<?php

namespace App\Http\Controllers\Admin;

use App\Models\Attribute;
use Illuminate\Http\Request;
use App\Models\AttributeValue;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class AttributeValueController extends Controller
{
    public function index(Request $request, $attributeId)
    {
        $attribute = Attribute::findOrFail($attributeId);

        $query = AttributeValue::where('attribute_id', $attributeId)
            ->withCount('products'); // Đếm số biến thể sản phẩm liên kết

        // Tìm kiếm theo tên
        if ($request->filled('search')) {
            $searchTerm = trim($request->search);
            $query->where('value', 'like', '%' . $searchTerm . '%');
        }

        // Lọc theo trạng thái
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status);
        }

        // Sắp xếp
        $sortBy = $request->get('sort_by', 'id');
        $sortDir = $request->get('sort_dir', 'desc');
        $allowedSortFields = ['id', 'value', 'is_active', 'created_at', 'products_count'];

        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortDir);
        }

        $values = $query->paginate(10)->withQueryString();

        return view('admin.attribute_values.index', compact('attribute', 'values', 'sortBy', 'sortDir'));
    }

    public function create($attributeId)
    {
        $attribute = Attribute::findOrFail($attributeId);
        return view('admin.attribute_values.create', compact('attribute'));
    }

    public function store(Request $request, $attributeId)
    {
        $attribute = Attribute::findOrFail($attributeId);

        // Xác thực dữ liệu
        $request->validate([
            'value' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        // Kiểm tra giá trị trùng lặp (không phân biệt chữ hoa/thường)
        $valueExists = AttributeValue::where('attribute_id', $attributeId)
            ->whereRaw('LOWER(value) = ?', [strtolower($request->value)])
            ->exists();

        if ($valueExists) {
            throw ValidationException::withMessages([
                'value' => 'Giá trị "' . $request->value . '" đã tồn tại cho thuộc tính này (không phân biệt chữ hoa/thường).',
            ]);
        }

        // Tạo AttributeValue
        AttributeValue::create([
            'attribute_id' => $attributeId,
            'value' => $request->value,
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('admin.attribute_values.index', $attributeId)
            ->with('success', 'Giá trị thuộc tính đã được thêm.');
    }

    public function edit($attributeId, $id)
    {
        $attribute = Attribute::findOrFail($attributeId);
        $value = AttributeValue::findOrFail($id);
        return view('admin.attribute_values.edit', compact('attribute', 'value'));
    }

    public function update(Request $request, $attributeId, $id)
    {
        $request->validate([
            'value' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        $value = AttributeValue::findOrFail($id);
        $value->update([
            'value' => $request->value,
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('admin.attribute_values.index', $attributeId)
            ->with('success', 'Giá trị thuộc tính đã được cập nhật.');
    }

    public function destroy($attributeId, $id)
    {
        $value = AttributeValue::findOrFail($id);

        // Kiểm tra xem AttributeValue có liên kết với biến thể sản phẩm không
        if ($value->products()->count() > 0) {
            return redirect()->route('admin.attribute_values.index', $attributeId)
                ->with('error', 'Không thể xóa giá trị thuộc tính "' . $value->value . '" vì nó đang được sử dụng bởi một hoặc nhiều biến thể sản phẩm.');
        }

        $value->delete();

        return redirect()->route('admin.attribute_values.index', $attributeId)
            ->with('success', 'Giá trị thuộc tính "' . $value->value . '" đã được xóa mềm.');
    }

    public function trashed(Request $request, $attributeId)
    {
        $attribute = Attribute::findOrFail($attributeId);
        
        $query = AttributeValue::onlyTrashed()
                              ->where('attribute_id', $attributeId);

        // Tìm kiếm
        if ($request->has('search')) {
            $query->where('value', 'like', '%' . $request->search . '%');
        }

        // Sắp xếp
        $sortBy = $request->get('sort_by', 'value');
        $sortDir = $request->get('sort_dir', 'desc');
        $allowedSortFields = ['value', 'created_at', 'deleted_at'];

        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortDir);
        }

        $values = $query->paginate(10)->withQueryString();

        return view('admin.attribute_values.trashed', compact('attribute', 'values', 'sortBy', 'sortDir'));
    }

    public function restore($attributeId, $id)
    {
        $value = AttributeValue::onlyTrashed()->findOrFail($id);
        $value->restore();

        return redirect()->route('admin.attribute_values.trashed', $attributeId)
            ->with('success', 'Giá trị thuộc tính "' . $value->value . '" đã được khôi phục.');
    }

    public function forceDelete($attributeId, $id)
    {
        $value = AttributeValue::onlyTrashed()->findOrFail($id);
        $value->forceDelete();

        return redirect()->route('admin.attribute_values.trashed', $attributeId)
            ->with('success', 'Giá trị thuộc tính "' . $value->value . '" đã được xóa vĩnh viễn.');
    }

    /**
     * Xóa hàng loạt các giá trị thuộc tính
     */
    public function bulkDelete(Request $request, $attributeId): JsonResponse
    {
        $ids = $request->ids;

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'error' => 'Không có giá trị nào được chọn'
            ]);
        }

        try {
            AttributeValue::whereIn('id', $ids)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Các giá trị đã được xóa thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Có lỗi xảy ra khi xóa các giá trị'
            ]);
        }
    }

    /**
     * Thay đổi trạng thái hàng loạt các giá trị thuộc tính
     */
    public function bulkToggle(Request $request, $attributeId): JsonResponse
    {
        $ids = $request->ids;
        $status = $request->status;

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'error' => 'Không có giá trị nào được chọn'
            ]);
        }

        try {
            AttributeValue::whereIn('id', $ids)->update(['is_active' => $status]);

            return response()->json([
                'success' => true,
                'message' => 'Trạng thái các giá trị đã được cập nhật thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Có lỗi xảy ra khi cập nhật trạng thái'
            ]);
        }
    }
}
