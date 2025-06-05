<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttributeController extends Controller
{
    public function index()
    {
        $attributes = Attribute::withCount('values')->latest()->paginate(10);
        return view('admin.attributes.index', compact('attributes'));
    }

    public function create()
    {
        return view('admin.attributes.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:attributes',
                'type' => 'required|in:select,text',
                'is_active' => 'boolean'
            ]);

            $validated['slug'] = Str::slug($validated['name']);
            $validated['is_active'] = (bool) $request->input('is_active', false);

            \Log::info('Validated data:', $validated);

            $attribute = Attribute::create($validated);
            
            \Log::info('Created attribute:', $attribute->toArray());

            return redirect()
                ->route('admin.attributes.index')
                ->with('success', 'Thuộc tính đã được tạo thành công!');
        } catch (\Exception $e) {
            \Log::error('Error creating attribute: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi tạo thuộc tính: ' . $e->getMessage());
        }
    }

    public function edit(Attribute $attribute)
    {
        return view('admin.attributes.edit', compact('attribute'));
    }

    public function update(Request $request, Attribute $attribute)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:attributes,name,' . $attribute->id,
            'type' => 'required|in:select,text',
            'is_active' => 'boolean'
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = (bool) $request->input('is_active', false);

        $attribute->update($validated);

        return redirect()
            ->route('admin.attributes.index')
            ->with('success', 'Thuộc tính đã được cập nhật thành công!');
    }

    public function destroy(Attribute $attribute)
    {
        $attribute->delete();

        return redirect()
            ->route('admin.attributes.index')
            ->with('success', 'Thuộc tính đã được xóa thành công!');
    }

    public function values(Attribute $attribute)
    {
        $values = $attribute->values()->paginate(10);
        return view('admin.attributes.values', compact('attribute', 'values'));
    }

    public function storeValue(Request $request, Attribute $attribute)
    {
        $validated = $request->validate([
            'value' => 'required|string|max:255',
            'color_code' => 'nullable|string|max:7'
        ]);

        $validated['slug'] = Str::slug($validated['value']);

        $attribute->values()->create($validated);

        return redirect()
            ->route('admin.attributes.values', $attribute)
            ->with('success', 'Giá trị thuộc tính đã được thêm thành công!');
    }

    public function destroyValue(Attribute $attribute, $valueId)
    {
        $attribute->values()->findOrFail($valueId)->delete();

        return redirect()
            ->route('admin.attributes.values', $attribute)
            ->with('success', 'Giá trị thuộc tính đã được xóa thành công!');
    }
} 