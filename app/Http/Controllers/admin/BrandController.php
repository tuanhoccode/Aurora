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

        // Tìm kiếm theo tên
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%");
        }

        // Lọc theo trạng thái
        if ($request->has('status') && $request->get('status') !== '') {
            $query->where('is_active', $request->get('status'));
        }

        // Sắp xếp mặc định theo ID tăng dần
        $query->orderBy('id', 'asc');

        // Phân trang với 10 items mỗi trang
        $brands = $query->paginate(10)->withQueryString();

        return view('admin.brands.index', compact('brands'));
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

        // Tìm kiếm theo tên
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%");
        }

        // Lọc theo trạng thái
        if ($request->has('status') && $request->get('status') !== '') {
            $query->where('is_active', $request->get('status'));
        }

        // Sắp xếp mặc định theo ngày xóa giảm dần
        $query->orderBy('deleted_at', 'desc');

        // Phân trang với 10 items mỗi trang
        $brands = $query->paginate(10)->withQueryString();

        return view('admin.brands.trash', compact('brands'));
    }
}
