<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use Illuminate\Http\Request;

class AttributeController extends Controller
{
    /**
     * Hiển thị danh sách thuộc tính đang hoạt động
     */
    public function index()
    {
        $attributes = Attribute::where('is_active', true)
            ->whereNull('deleted_at')
            ->get();

        return view('admin.attributes.index', compact('attributes'));
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
            $attribute = Attribute::findOrFail($id);
            $attribute->delete();
            return redirect()->route('admin.attributes.index')->with('success', 'Thuộc tính đã được xóa mềm.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Không thể xóa thuộc tính: ' . $e->getMessage());
        }
    }
    public function trashed()
    {
        $attributes = Attribute::onlyTrashed()->get();

        return view('admin.attributes.trashed', compact('attributes'));
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
}
