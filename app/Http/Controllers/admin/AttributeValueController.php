<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAttributeValueRequest;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttributeValueController extends Controller
{
    public function index(Request $request, $attributeId)
    {
        $attribute = Attribute::findOrFail($attributeId);

        $query = AttributeValue::where('attribute_id', $attributeId)
            ->withCount('products'); // Count products using the defined relationship

        // Tìm kiếm
        if ($request->has('search')) {
            $query->where('value', 'like', '%' . $request->search . '%');
        }

        // Lọc theo trạng thái
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status);
        }

        // Sắp xếp
        $sortBy = $request->get('sort_by', 'value');
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

    public function store(StoreAttributeValueRequest $request, $attributeId)
    {
        try {
            $attribute = Attribute::findOrFail($attributeId);

            AttributeValue::create([
                'attribute_id' => $attributeId,
                'value' => $request->validated()['value'],
                'is_active' => $request->validated()['is_active'],
            ]);

            return redirect()->route('admin.attribute_values.index', $attributeId)
                ->with('success', 'Giá trị thuộc tính đã được thêm.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Không thể thêm giá trị thuộc tính: ' . $e->getMessage());
        }
    }

    public function edit($attributeId, $id)
    {
        $attribute = Attribute::findOrFail($attributeId);
        $value = AttributeValue::findOrFail($id);
        return view('admin.attribute_values.edit', compact('attribute', 'value'));
    }

    public function update(StoreAttributeValueRequest $request, $attributeId, $id)
    {
        try {
            $value = AttributeValue::findOrFail($id);
            $value->update($request->validated());

            return redirect()->route('admin.attribute_values.index', $attributeId)
                ->with('success', 'Giá trị thuộc tính đã được cập nhật.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Không thể cập nhật giá trị thuộc tính: ' . $e->getMessage());
        }
    }

    public function destroy($attributeId, $id)
    {
        try {
            $value = AttributeValue::findOrFail($id);
            $value->delete();

            return redirect()->route('admin.attribute_values.index', $attributeId)
                ->with('success', 'Giá trị thuộc tính đã được xóa mềm.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Không thể xóa giá trị thuộc tính: ' . $e->getMessage());
        }
    }

    public function restore($attributeId, $id)
    {
        try {
            $value = AttributeValue::withTrashed()->findOrFail($id);
            $value->restore();

            return redirect()->route('admin.attribute_values.index', $attributeId)
                ->with('success', 'Giá trị thuộc tính đã được khôi phục.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Không thể khôi phục giá trị thuộc tính: ' . $e->getMessage());
        }
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