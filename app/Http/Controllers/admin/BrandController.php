<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Admin\BrandRequest;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDir = $request->input('sort_dir', 'desc');

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

    public function store(BrandRequest $request)
    {
        try {
            $data = $request->validated();
            unset($data['slug']);

            // Xử lý upload logo
            if ($request->hasFile('logo')) {
                $data['logo'] = $request->file('logo')->store('brands', 'public');
            }

            $data['is_active'] = $request->has('is_active');

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

    public function update(BrandRequest $request, Brand $brand)
    {
        try {
            $data = $request->validated();
            unset($data['slug']);

            if ($request->hasFile('logo')) {
                if ($brand->logo) {
                    Storage::disk('public')->delete($brand->logo);
                }
                $data['logo'] = $request->file('logo')->store('brands', 'public');
            }

            $data['is_active'] = $request->input('is_active', 0);
            if (isset($data['is_active']) && !$data['is_active']) {
                $data['is_visible'] = 0;
            }

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
            // Prevent delete if brand has products
            if ($brand->products()->count() > 0) {
                return redirect()
                    ->back()
                    ->with('error', 'Không thể xóa thương hiệu này vì vẫn còn sản phẩm liên kết.');
            }
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
        $sortBy = $request->input('sort_by', 'deleted_at');
        $sortDir = $request->input('sort_dir', 'desc');

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

            if ($brand->products()->count() > 0) {
                return redirect()
                    ->back()
                    ->with('error', 'Không thể xóa vĩnh viễn thương hiệu này vì vẫn còn sản phẩm liên kết.');
            }

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

            $brands = Brand::whereIn('id', $ids)->get();
            $notDeleted = [];
            $deleted = [];
            foreach ($brands as $brand) {
                if ($brand->products()->count() > 0) {
                    $notDeleted[] = $brand->name;
                    continue;
                }
                $brand->delete();
                $deleted[] = $brand->id;
            }
            $response = [];
            if (count($notDeleted) > 0) {
                $response['error'] = 'Không thể xóa các thương hiệu sau vì vẫn còn sản phẩm liên kết: ' . implode(', ', $notDeleted);
            }
            if (count($deleted) > 0) {
                $response['deleted_ids'] = $deleted;
            }
            if (count($response) > 0) {
                return response()->json($response, count($notDeleted) > 0 ? 400 : 200);
            }
            return response()->json([
                'error' => 'Không có thương hiệu nào được xóa.'
            ], 400);
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
            $notDeleted = [];

            foreach ($brands as $brand) {
                if ($brand->products()->count() > 0) {
                    $notDeleted[] = $brand->name;
                    continue;
                }
                if ($brand->logo) {
                    Storage::disk('public')->delete($brand->logo);
                }
                $brand->forceDelete();
            }

            if (count($notDeleted) > 0) {
                return response()->json([
                    'error' => 'Không thể xóa vĩnh viễn các thương hiệu sau vì vẫn còn sản phẩm liên kết: ' . implode(', ', $notDeleted)
                ], 400);
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

    /**
     * Toggle visible status for a brand
     */
    public function toggleVisible(Request $request, $id)
    {
        $brand = Brand::findOrFail($id);
        if (!$brand->is_active) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Không thể chuyển trạng thái hiển thị khi thương hiệu không hoạt động.'
                ], 400);
            }
            return redirect()->route('admin.brands.index')
                ->with('error', 'Không thể chuyển trạng thái hiển thị khi thương hiệu không hoạt động.');
        }
        $brand->is_visible = !$brand->is_visible;
        $brand->save();
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'is_visible' => $brand->is_visible
            ]);
        }
        return redirect()->route('admin.brands.index')
            ->with('success', 'Đã chuyển trạng thái hiển thị thương hiệu thành công!');
    }

    /**
     * Toggle active status for a brand
     */
    public function toggleActive(Request $request, $id)
    {
        $brand = Brand::findOrFail($id);
        $brand->is_active = !$brand->is_active;
        // Nếu chuyển sang không hoạt động thì tự động ẩn hiển thị
        if (!$brand->is_active) {
            $brand->is_visible = 0;
        }
        $brand->save();
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $brand->is_active,
                'is_visible' => $brand->is_visible
            ]);
        }
        return redirect()->route('admin.brands.index')
            ->with('success', 'Đã chuyển trạng thái hoạt động thương hiệu thành công!');
    }

    protected function deleteLogoFileIfExists($path)
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
