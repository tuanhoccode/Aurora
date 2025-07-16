<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        // Lấy tham số sắp xếp từ request
        $sortBy = $request->input('sort_by', 'created_at'); // Mặc định sắp xếp theo ngày tạo
        $sortDir = $request->input('sort_dir', 'desc'); // Mặc định sắp xếp giảm dần

        $brands = Brand::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', "%{$request->search}%");
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('is_active', $request->status);
            })
            ->orderBy($sortBy, $sortDir)
            ->latest()
            ->get();

        return view('admin.brands.index', compact('brands', 'sortBy', 'sortDir'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        try {
            $data = $this->validateBrand($request);

            // Xử lý upload logo
            if ($request->hasFile('logo')) {
                $data['logo'] = $request->file('logo')->store('brands', 'public');
            }

            $validated['is_active'] = $request->has('is_active');

            Brand::create($data);

            return redirect()
                ->route('admin.brands.index')
                ->with('success', 'Thêm thương hiệu thành công');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Đã có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function show(Brand $brand)
    {
        return view('admin.brands.show', compact('brand'));
    }

    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        try {
            $data = $this->validateBrand($request, $brand);

            // Xử lý upload logo mới
            if ($request->hasFile('logo')) {
                // Xóa logo cũ nếu có
                if ($brand->logo) {
                    Storage::disk('public')->delete($brand->logo);
                }
                $data['logo'] = $request->file('logo')->store('brands', 'public');
            }

            $validated['is_active'] = $request->has('is_active');

            $brand->update($data);

            return redirect()
                ->route('admin.brands.index')
                ->with('success', 'Cập nhật thương hiệu thành công');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Đã có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function destroy(Brand $brand)
    {
        try {
            $brand->delete();
            return redirect()
                ->route('admin.brands.index')
                ->with('success', 'Đã chuyển thương hiệu vào thùng rác');
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

        $brands = Brand::onlyTrashed()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', "%{$request->search}%");
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('is_active', $request->status);
            })
            ->orderBy($sortBy, $sortDir)
            ->get();

        return view('admin.brands.trash', compact('brands', 'sortBy', 'sortDir'));
    }

    public function restore($id)
    {
        try {
            $brand = Brand::onlyTrashed()->findOrFail($id);
            $brand->restore();

            return redirect()
                ->route('admin.brands.trash')
                ->with('success', 'Khôi phục thương hiệu thành công');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Đã có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function forceDelete($id)
    {
        try {
            $brand = Brand::onlyTrashed()->findOrFail($id);

            // Xóa logo nếu có
            if ($brand->logo) {
                Storage::disk('public')->delete($brand->logo);
            }

            $brand->forceDelete();

            return redirect()
                ->route('admin.brands.trash')
                ->with('success', 'Xóa thương hiệu vĩnh viễn thành công');
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

            Brand::whereIn('id', $ids)->delete();

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

            $brands = Brand::onlyTrashed()->whereIn('id', $ids)->get();

            foreach ($brands as $brand) {
                if ($brand->logo) {
                    Storage::disk('public')->delete($brand->logo);
                }
                $brand->forceDelete();
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
                    'error' => 'Vui lòng chọn ít nhất một thương hiệu'
                ], 400);
            }

            Brand::onlyTrashed()
                ->whereIn('id', $ids)
                ->restore();

            return response()->json([
                'success' => 'Khôi phục các thương hiệu đã chọn thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Đã có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkToggle(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            if (empty($ids)) {
                return response()->json([
                    'error' => 'Vui lòng chọn ít nhất một thương hiệu'
                ], 400);
            }

            $status = $request->input('status');
            if (!isset($status)) {
                return response()->json([
                    'error' => 'Trạng thái không hợp lệ'
                ], 400);
            }

            Brand::whereIn('id', $ids)->update(['is_active' => $status]);

            return response()->json([
                'success' => 'Cập nhật trạng thái các thương hiệu đã chọn thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Đã có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk toggle status for multiple brands
     */
    public function bulkToggleStatus(Request $request)
    {
        try {
            $ids = $request->ids;
            $status = $request->status;

            Brand::whereIn('id', $ids)->update(['is_active' => $status]);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Có lỗi xảy ra khi cập nhật trạng thái'
            ], 500);
        }
    }

    protected function validateBrand(Request $request, Brand $brand = null)
    {
        return $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,' . ($brand ? $brand->id : ''),
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'nullable'
        ]);
    }

    protected function deleteLogoFileIfExists($path)
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
