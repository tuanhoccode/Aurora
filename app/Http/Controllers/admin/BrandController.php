<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::all();
        return view('admin.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brands.create');  // use the same form for create/edit
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:brands,name|max:100',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_active' => 'required|boolean',
        ]);

        $data = $request->only(['name', 'is_active']);

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = Str::slug($request->name) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/brands', $filename);
            $data['logo'] = $filename;
        }

        Brand::create($data);

        return redirect()->route('admin.brands.index')->with('success', 'Brand created successfully!');
    }

    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    public function show(Brand $brand)
    {
        return view('admin.brands.show', compact('brand'));
    }


    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => 'required|unique:brands,name,' . $brand->id . '|max:100',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_active' => 'required|boolean',
        ]);

        $data = $request->only(['name', 'is_active']);

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($brand->logo && Storage::exists('public/brands/' . $brand->logo)) {
                Storage::delete('public/brands/' . $brand->logo);
            }

            $file = $request->file('logo');
            $filename = Str::slug($request->name) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/brands', $filename);
            $data['logo'] = $filename;
        }

        $brand->update($data);

        return redirect()->route('admin.brands.index')->with('success', 'Brand updated successfully!');
    }

    public function destroy(Brand $brand)
    {
        try {
            // Delete logo if exists
            if ($brand->logo && Storage::exists('public/brands/' . $brand->logo)) {
                Storage::delete('public/brands/' . $brand->logo);
            }
            $brand->delete();
            return redirect()->route('admin.brands.index')->with('success', 'Brand deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('admin.brands.index')->with('error', 'Error deleting brand: ' . $e->getMessage());
        }
    }

    /**
     * Force delete (xóa vĩnh viễn) một brand đã bị xóa mềm
     */
    public function forceDelete($id)
    {
        $brand = Brand::withTrashed()->findOrFail($id);
        try {
            // Xóa file logo nếu có
            if ($brand->logo && Storage::exists('public/brands/' . $brand->logo)) {
                Storage::delete('public/brands/' . $brand->logo);
            }
            $brand->forceDelete();
            return redirect()->route('admin.brands.index')->with('success', 'Brand permanently deleted!');
        } catch (\Exception $e) {
            return redirect()->route('admin.brands.index')->with('error', 'Error force deleting brand: ' . $e->getMessage());
        }
    }
}
