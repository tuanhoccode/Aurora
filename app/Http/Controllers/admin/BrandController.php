<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\SoftDeletes;

class BrandController extends Controller
{
    protected function validateBrand(Request $request, Brand $brand = null)
    {
        $rules = [
            'name' => 'required|max:100',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_active' => 'boolean',
        ];

        if ($brand) {
            $rules['name'] .= ',name,' . $brand->id;
        } else {
            $rules['name'] .= '|unique:brands,name';
        }

        return $request->validate($rules);
    }

    protected function handleLogoUpload(Request $request, Brand $brand = null)
    {
        if (!$request->hasFile('logo')) {
            return null;
        }

        // Xóa logo cũ nếu có
        if ($brand && $brand->logo && Storage::disk('public')->exists($brand->logo)) {
            Storage::disk('public')->delete($brand->logo);
        }

        $file = $request->file('logo');
        $filename = Str::slug($request->name) . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('brands', $filename, 'public');
        return 'brands/' . $filename;
    }

    public function index(Request $request)
    {
        $query = Brand::query();

       // Tìm kiếm theo tên (nếu có)
       if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    // Lọc theo trạng thái (nếu hợp lệ)
    if ($request->has('status') && in_array($request->status, ['0', '1'], true)) {
        $query->where('is_active', $request->status);
    }


        // Sorting
        $sortableColumns = ['id', 'name', 'is_active', 'created_at'];
        $sortBy = $request->get('sort_by', 'id'); // Mặc định sắp xếp theo ID
        $sortDir = $request->get('sort_dir', 'asc'); // Mặc định sắp xếp tăng dần

        if (!in_array($sortBy, $sortableColumns)) {
            $sortBy = 'id'; // Đảm bảo cột sắp xếp hợp lệ
        }
        if (!in_array($sortDir, ['asc', 'desc'])) {
            $sortDir = 'asc'; // Đảm bảo hướng sắp xếp hợp lệ
        }

        $query->orderBy($sortBy, $sortDir);

        // Per-page selection
        $perPage = $request->get('per_page', 10); // Mặc định 10 items/trang
        $validPerPage = [10, 25, 50, 100];
        if (!in_array($perPage, $validPerPage)) {
            $perPage = 10; // Đảm bảo per_page hợp lệ
        }

        $brands = $query->paginate($perPage)->withQueryString();

        return view('admin.brands.index', compact('brands', 'sortBy', 'sortDir', 'perPage'));
    }

    

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $this->validateBrand($request);
            $data = $request->only(['name', 'is_active']);

            if ($logoPath = $this->handleLogoUpload($request)) {
                $data['logo'] = $logoPath;
            }

            Brand::create($data);
            return redirect()->route('admin.brands.index')
                ->with('success', 'Thêm thương hiệu thành công!');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra khi tạo thương hiệu: ' . $e->getMessage());
        }
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
        try {
            $validated = $this->validateBrand($request, $brand);
            $data = $request->only(['name', 'is_active']);

            if ($logoPath = $this->handleLogoUpload($request, $brand)) {
                $data['logo'] = $logoPath;
            }

            $brand->update($data);
            return redirect()->route('admin.brands.index')
                ->with('success', 'Cập nhật thương hiệu thành công!');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra khi cập nhật thương hiệu: ' . $e->getMessage());
        }
    }

    public function destroy(Brand $brand)
    {
        try {
            // Không xóa file logo khi soft delete
            // if ($brand->logo && Storage::disk('public')->exists($brand->logo)) {
            //     Storage::disk('public')->delete($brand->logo);
            // }
            $brand->delete(); // <-- Dòng soft delete
            return redirect()->route('admin.brands.index')
                ->with('success', 'Xóa thương hiệu thành công!');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.brands.index')
                ->with('error', 'Không tìm thấy thương hiệu!');
        } catch (\Exception $e) {
            return redirect()->route('admin.brands.index')
                ->with('error', 'Có lỗi xảy ra khi xóa thương hiệu: ' . $e->getMessage());
        }
    }

    public function forceDelete($id)
    {
        try {
            if (!is_numeric($id)) {
                throw new ValidationException('ID không hợp lệ');
            }

            $brand = Brand::withTrashed()->findOrFail($id);
            if ($brand->logo && Storage::disk('public')->exists($brand->logo)) {
                Storage::disk('public')->delete($brand->logo);
            }
            $brand->forceDelete();
            return redirect()->route('admin.brands.trash')
                ->with('success', 'Xóa vĩnh viễn thương hiệu thành công!');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.brands.trash')
                ->with('error', 'Không tìm thấy thương hiệu!');
        } catch (\Exception $e) {
            return redirect()->route('admin.brands.trash')
                ->with('error', 'Có lỗi xảy ra khi xóa vĩnh viễn thương hiệu: ' . $e->getMessage());
        }
    }

    public function restore($id)
    {
        try {
            if (!is_numeric($id)) {
                throw new ValidationException('ID không hợp lệ');
            }

            $brand = Brand::withTrashed()->findOrFail($id);
            $brand->restore();
            return redirect()->route('admin.brands.index')
                ->with('success', 'Khôi phục thương hiệu thành công!');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.brands.trash')
                ->with('error', 'Không tìm thấy thương hiệu!');
        } catch (\Exception $e) {
            return redirect()->route('admin.brands.trash')
                ->with('error', 'Có lỗi xảy ra khi khôi phục thương hiệu: ' . $e->getMessage());
        }
    }

    public function trash(Request $request)
    {
        $query = Brand::onlyTrashed();

        // Tìm kiếm theo tên (nếu có)
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Lọc theo trạng thái (nếu hợp lệ)
        if ($request->has('status') && in_array($request->status, ['0', '1'], true)) {
            $query->where('is_active', $request->status);
        }

        // Sorting
        $sortableColumns = ['id', 'name', 'is_active', 'deleted_at'];
        $sortBy = $request->get('sort_by', 'deleted_at'); // Mặc định sắp xếp theo ngày xóa
        $sortDir = $request->get('sort_dir', 'desc'); // Mặc định sắp xếp giảm dần

         if (!in_array($sortBy, $sortableColumns)) {
            $sortBy = 'deleted_at'; // Đảm bảo cột sắp xếp hợp lệ
        }
        if (!in_array($sortDir, ['asc', 'desc'])) {
            $sortDir = 'desc'; // Đảm bảo hướng sắp xếp hợp lệ
        }

        $query->orderBy($sortBy, $sortDir);

        // Per-page selection
        $perPage = $request->get('per_page', 10); // Mặc định 10 items/trang
        $validPerPage = [10, 25, 50, 100];
         if (!in_array($perPage, $validPerPage)) {
            $perPage = 10; // Đảm bảo per_page hợp lệ
        }

        $brands = $query->paginate($perPage)->withQueryString();

        return view('admin.brands.trash', compact('brands', 'sortBy', 'sortDir', 'perPage'));
    }

    // Batch Actions // Removed batch action methods

    /*
    public function batchAction(Request $request)
    {
        // ... logic ...
    }

    public function batchRestore(Request $request)
    {
        // ... logic ...
    }

    public function batchForceDelete(Request $request)
    {
        // ... logic ...
    }
    */
}