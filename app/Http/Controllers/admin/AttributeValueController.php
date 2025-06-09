<?php

namespace App\Http\Controllers\Admin;

use App\Models\Attribute;
use Illuminate\Http\Request;
use App\Models\AttributeValue;
use App\Http\Controllers\Controller;

class AttributeValueController extends Controller
{
        public function index($attributeId)
    {
        $attribute = Attribute::findOrFail($attributeId);
        $values = AttributeValue::where('attribute_id', $attributeId)->withTrashed()->paginate(10);
        return view('admin.attribute_values.index', compact('attribute', 'values'));
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
}