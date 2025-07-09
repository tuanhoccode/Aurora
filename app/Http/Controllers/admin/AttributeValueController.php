<?php

namespace App\Http\Controllers\Admin;

use App\Models\Attribute;
use Illuminate\Http\Request;
use App\Models\AttributeValue;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class AttributeValueController extends Controller
{
    public function index(Request $request, $attributeId)
    {
        $attribute = Attribute::findOrFail($attributeId);
        
        $query = AttributeValue::where('attribute_id', $attributeId);

        // Tìm kiếm
        if ($request->has('search')) {
            $query->where('value', 'like', '%' . $request->search . '%');
        }

        // Lọc theo trạng thái
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status);
        }

        // Sắp xếp
        $sortBy = $request->get('sort_by', 'id');
        $sortDir = $request->get('sort_dir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        $values = $query->withTrashed()->paginate(10);
        $values->appends($request->all());

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

        $request->validate([
            'value' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);

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
        $value->delete();

        return redirect()->route('admin.attribute_values.index', $attributeId)
                         ->with('success', 'Giá trị thuộc tính đã được xóa mềm.');
    }

    public function restore($attributeId, $id)
    {
        $value = AttributeValue::withTrashed()->findOrFail($id);
        $value->restore();

        return redirect()->route('admin.attribute_values.index', $attributeId)
                         ->with('success', 'Giá trị thuộc tính đã được khôi phục.');
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