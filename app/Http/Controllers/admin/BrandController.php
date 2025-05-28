<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\Rule;

class BrandController extends Controller
{
    protected function validateBrand(Request $request, Brand $brand = null)
    {
        $rules = [
            'name' => [
                'required',
                'max:100',
                Rule::unique('brands', 'name')->ignore($brand?->id),
            ],
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_active' => 'boolean',
        ];

        $messages = [
            'name.required' => 'Bắt buộc nhập tên thương hiệu',
            'name.max' => 'Tên thương hiệu không được vượt quá 100 ký tự',
            'name.unique' => 'Tên thương hiệu đã tồn tại',
            'logo.image' => 'Logo phải là tệp hình ảnh',
            'logo.mimes' => 'Logo phải thuộc định dạng: jpeg, png, jpg, gif, webp',
            'logo.max' => 'Logo không được vượt quá 2MB',
            'is_active.boolean' => 'Trạng thái phải là true hoặc false',
        ];

        return $request->validate($rules, $messages);
    }

    protected function handleLogoUpload(Request $request, Brand $brand = null)
    {
        if (!$request->hasFile('logo')) {
            return null;
        }

        // Xóa logo cũ nếu có
        if ($brand && $brand->logo) {
            $this->deleteLogoFileIfExists($brand->logo);
        }

        $file = $request->file('logo');
        $filename = (string) Str::uuid() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('brands', $filename, 'public');
        return 'brands/' . $filename;
    }

    protected function deleteLogoFileIfExists($path)
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    public function index(Request $request)
    {
        $query = Brand::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('status') && in_array($request->status, ['0', '1'], true)) {
            $query->where('is_active', $request->status);
        }

        $sortableColumns = ['id', 'name', 'is_active', 'created_at'];
        $sortBy = in_array($request->get('sort_by'), $sortableColumns) ? $request->get('sort_by') : 'id';
        $sortDir = in_array($request->get('sort_dir'), ['asc', 'desc']) ? $request->get('sort_dir') : 'asc';

        $query->orderBy($sortBy, $sortDir);

        $brands = $query->get();

        return view('admin.brands.index', compact('brands', 'sortBy', 'sortDir'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $this->validateBrand($request);

            DB::beginTransaction();

            $data = $request->only(['name', 'is_active']);
            $logoPath = $this->handleLogoUpload($request);
            if ($logoPath) {
                $data['logo'] = $logoPath;
            }

            Brand::create($data);
            DB::commit();

            return redirect()->route('admin.brands.index')
                ->with('success', 'Thêm thương hiệu thành công!');
        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($logoPath)) {
                $this->deleteLogoFileIfExists($logoPath);
            }

            return back()->with('error', 'Có lỗi xảy ra khi tạo thương hiệu: ' . $e->getMessage())
                ->withInput();
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

            DB::beginTransaction();

            $data = $request->only(['name', 'is_active']);
            $logoPath = $this->handleLogoUpload($request, $brand);
            if ($logoPath) {
                $data['logo'] = $logoPath;
            }

            $brand->update($data);
            DB::commit();

            return redirect()->route('admin.brands.index')
                ->with('success', 'Cập nhật thương hiệu thành công!');
        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($logoPath)) {
                $this->deleteLogoFileIfExists($logoPath);
            }

            return back()->with('error', 'Có lỗi xảy ra khi cập nhật thương hiệu: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Brand $brand)
    {
        try {
            $brand->delete();
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

            $this->deleteLogoFileIfExists($brand->logo);
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

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('status') && in_array($request->status, ['0', '1'], true)) {
            $query->where('is_active', $request->status);
        }

        $sortableColumns = ['id', 'name', 'is_active', 'deleted_at'];
        $sortBy = in_array($request->get('sort_by'), $sortableColumns) ? $request->get('sort_by') : 'deleted_at';
        $sortDir = in_array($request->get('sort_dir'), ['asc', 'desc']) ? $request->get('sort_dir') : 'desc';

        $query->orderBy($sortBy, $sortDir);

        $brands = $query->get();

        return view('admin.brands.trash', compact('brands', 'sortBy', 'sortDir'));
    }
}
