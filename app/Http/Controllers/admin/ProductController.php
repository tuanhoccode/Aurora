<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Admin\ProductRequest;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand']);

        // Tìm kiếm theo tên hoặc mã sản phẩm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Lọc theo danh mục
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('is_active', $request->status == '1');
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        $categories = Category::where('is_active', 1)->get();
        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', 1)->count();
        $outOfStockProducts = Product::where('stock', 0)->count();
        $lowStockProducts = Product::where('stock', '>', 0)
            ->where('stock', '<=', 5)
            ->count();
        $trashedCount = Product::onlyTrashed()->count();
        $totalStock = Product::sum('stock');

        return view('admin.products.index', compact(
            'products',
            'categories',
            'totalProducts',
            'activeProducts',
            'outOfStockProducts',
            'lowStockProducts',
            'trashedCount',
            'totalStock'
        ));
    }

    public function create()
    {
        $categories = Category::where('is_active', 1)->get();
        $brands = Brand::where('is_active', 1)->get();
        $attributes = Attribute::with('values')->where('is_active', 1)->get();

        // Debug logging
        \Log::info('Debug attributes in create method:', [
            'attributes_count' => $attributes->count(),
            'attributes' => $attributes->toArray()
        ]);

        $trashedCount = Product::onlyTrashed()->count();

        return view('admin.products.create', compact('categories', 'brands', 'attributes', 'trashedCount'));
    }

    public function store(ProductRequest $request)
    {
        try {
            // Lấy dữ liệu đã được validate
            $validated = $request->validated();

            // Xử lý trạng thái is_active
            $validated['is_active'] = $request->has('is_active');

            // Tạo slug từ tên sản phẩm và thêm số ngẫu nhiên
            $baseSlug = Str::slug($validated['name']);
            $slug = $baseSlug;
            $counter = 1;

            // Kiểm tra và tạo slug duy nhất
            while (Product::withTrashed()->where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
            $validated['slug'] = $slug;

            // Xử lý thumbnail nếu có
            if ($request->hasFile('thumbnail')) {
                $file = $request->file('thumbnail');
                $filename = 'PRD-' . $validated['slug'] . '-' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('products', $filename, 'public');
                $validated['thumbnail'] = $path;
            }

            // Tự động tạo SKU nếu không được cung cấp hoặc đảm bảo SKU có tiền tố PRD-
            if (empty($validated['sku'])) {
                $validated['sku'] = 'PRD-' . strtoupper(Str::random(8));
            } else {
                // Nếu SKU được cung cấp nhưng không có tiền tố PRD-, thêm vào
                if (!Str::startsWith(strtoupper($validated['sku']), 'PRD-')) {
                    $validated['sku'] = 'PRD-' . strtoupper($validated['sku']);
                } else {
                    $validated['sku'] = strtoupper($validated['sku']);
                }
            }

            // Tạo sản phẩm mới
            $product = Product::create($validated);

            // Xử lý thuộc tính biến thể nếu là sản phẩm có biến thể
            if ($request->type === 'variant' && $request->has('variant_attributes')) {
                $sizes = [];
                $colors = [];

                // Lưu các thuộc tính được chọn
                $variantAttributes = $request->input('variant_attributes', []);
                if (!empty($variantAttributes)) {
                    foreach ($variantAttributes as $attributeId) {
                        $attribute = Attribute::find($attributeId);
                        if ($attribute) {
                            // Lấy giá trị thuộc tính được chọn
                            $values = $request->input("attribute_values.{$attributeId}", []);

                            if ($attribute->type === 'select') {
                                // Nếu là select, values là mảng id của attribute_values
                                $product->attributes()->attach($attributeId, [
                                    'values' => json_encode($values)
                                ]);

                                // Nếu là thuộc tính size hoặc color, lưu vào mảng tương ứng
                                if (strtolower($attribute->name) === 'size') {
                                    $sizeValues = AttributeValue::whereIn('id', $values)->pluck('value')->toArray();
                                    $sizes = array_merge($sizes, $sizeValues);
                                } elseif (strtolower($attribute->name) === 'color' || strtolower($attribute->name) === 'màu sắc') {
                                    $colorValues = AttributeValue::whereIn('id', $values)->pluck('value')->toArray();
                                    $colors = array_merge($colors, $colorValues);
                                }
                            } else {
                                // Nếu là text, values là string các giá trị phân cách bằng dấu phẩy
                                $textValues = is_array($values) ? $values : explode(',', $values);
                                $textValues = array_map('trim', $textValues);
                                $product->attributes()->attach($attributeId, [
                                    'values' => json_encode($textValues)
                                ]);

                                // Nếu là thuộc tính size hoặc color, lưu vào mảng tương ứng
                                if (strtolower($attribute->name) === 'size') {
                                    $sizes = array_merge($sizes, $textValues);
                                } elseif (strtolower($attribute->name) === 'color' || strtolower($attribute->name) === 'màu sắc') {
                                    $colors = array_merge($colors, $textValues);
                                }
                            }
                        }
                    }
                }

                // Cập nhật sizes và colors trong bảng products
                $product->update([
                    'sizes' => array_unique($sizes),
                    'colors' => array_unique($colors)
                ]);

                // Tạo các biến thể
                if ($request->has('variants')) {
                    foreach ($request->variants as $variantData) {
                        // Tạo biến thể
                        $variant = $product->variants()->create([
                            'sku' => $variantData['sku'],
                            'price' => $variantData['price'],
                            'sale_price' => $variantData['sale_price'] ?? null,
                            'stock' => $variantData['stock'],
                            'is_active' => true
                        ]);

                        // Liên kết với các giá trị thuộc tính
                        if (isset($variantData['attributes'])) {
                            foreach ($variantData['attributes'] as $attributeId => $valueId) {
                                $variant->attributeValues()->attach($valueId);
                            }
                        }
                    }
                }
            }

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Sản phẩm đã được tạo thành công!');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Đã có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function show(Product $product)
    {
        $product->load([
            'category',
            'brand',
            'attributes.values',
            'variants.attributeValues'
        ]);

        // Debug logging
        \Log::info('Product sizes debug:', [
            'product_id' => $product->id,
            'sizes_raw' => $product->getRawOriginal('sizes'),
            'sizes_cast' => $product->sizes,
            'has_sizes' => !empty($product->sizes),
            'sizes_count' => is_array($product->sizes) ? count($product->sizes) : 0
        ]);

        $trashedCount = Product::onlyTrashed()->count();

        return view('admin.products.show', compact('product', 'trashedCount'));
    }

    public function edit($id)
    {
        try {
            // Load the product manually instead of using route model binding
            $product = Product::with(['category', 'brand', 'attributes'])->findOrFail($id);

            // Debug the loaded product
            \Log::info('Product loaded:', [
                'id' => $product->id,
                'name' => $product->name,
                'category_id' => $product->category_id,
                'brand_id' => $product->brand_id,
                'has_category' => $product->category !== null,
                'has_brand' => $product->brand !== null
            ]);

            $categories = Category::where('is_active', 1)->get();
            $brands = Brand::where('is_active', 1)->get();
            $attributes = Attribute::with('values')->where('is_active', 1)->get();
            $trashedCount = Product::onlyTrashed()->count();

            return view('admin.products.edit', compact('product', 'categories', 'brands', 'attributes', 'trashedCount'));
        } catch (\Exception $e) {
            \Log::error('Error loading product:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->route('admin.products.index')
                ->with('error', 'Không thể tải thông tin sản phẩm. ' . $e->getMessage());
        }
    }

    public function update(ProductRequest $request, Product $product)
    {
        try {
            // Lấy dữ liệu đã được validate
            $validated = $request->validated();

            // Xử lý trạng thái is_active
            $validated['is_active'] = $request->has('is_active');

            // Xử lý SKU nếu được cung cấp
            if (isset($validated['sku'])) {
                // Nếu SKU không có tiền tố PRD-, thêm vào
                if (!Str::startsWith(strtoupper($validated['sku']), 'PRD-')) {
                    $validated['sku'] = 'PRD-' . strtoupper($validated['sku']);
                } else {
                    $validated['sku'] = strtoupper($validated['sku']);
                }
            }

            // Xử lý thumbnail nếu có
            if ($request->hasFile('thumbnail')) {
                // Xóa ảnh cũ nếu có
                if ($product->thumbnail) {
                    Storage::disk('public')->delete($product->thumbnail);
                }

                $file = $request->file('thumbnail');
                $filename = 'PRD-' . Str::slug($validated['name']) . '-' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('products', $filename, 'public');
                $validated['thumbnail'] = $path;
            }

            // Cập nhật slug nếu tên thay đổi
            if ($product->name !== $validated['name']) {
                $validated['slug'] = Str::slug($validated['name']);

                // Kiểm tra và tạo slug duy nhất
                $counter = 1;
                $originalSlug = $validated['slug'];
                while (Product::where('slug', $validated['slug'])
                    ->where('id', '!=', $product->id)
                    ->exists()) {
                    $validated['slug'] = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }

            // Cập nhật sản phẩm
            $product->update($validated);

            // Xử lý thuộc tính biến thể nếu là sản phẩm có biến thể
            if ($request->type === 'variant') {
                // Xóa tất cả thuộc tính cũ
                $product->attributes()->detach();

                $sizes = [];
                $colors = [];

                // Thêm lại các thuộc tính mới được chọn
                $variantAttributes = $request->input('variant_attributes', []);
                if (!empty($variantAttributes)) {
                    foreach ($variantAttributes as $attributeId) {
                        $attribute = Attribute::find($attributeId);
                        if ($attribute) {
                            // Lấy giá trị thuộc tính được chọn
                            $values = $request->input("attribute_values.{$attributeId}", []);

                            if ($attribute->type === 'select') {
                                // Nếu là select, values là mảng id của attribute_values
                                $product->attributes()->attach($attributeId, [
                                    'values' => json_encode($values)
                                ]);

                                // Nếu là thuộc tính size hoặc color, lưu vào mảng tương ứng
                                if (strtolower($attribute->name) === 'size') {
                                    $sizeValues = AttributeValue::whereIn('id', $values)->pluck('value')->toArray();
                                    $sizes = array_merge($sizes, $sizeValues);
                                } elseif (strtolower($attribute->name) === 'color' || strtolower($attribute->name) === 'màu sắc') {
                                    $colorValues = AttributeValue::whereIn('id', $values)->pluck('value')->toArray();
                                    $colors = array_merge($colors, $colorValues);
                                }
                            } else {
                                // Nếu là text, values là string các giá trị phân cách bằng dấu phẩy
                                $textValues = is_array($values) ? $values : explode(',', $values);
                                $textValues = array_map('trim', $textValues);
                                $product->attributes()->attach($attributeId, [
                                    'values' => json_encode($textValues)
                                ]);

                                // Nếu là thuộc tính size hoặc color, lưu vào mảng tương ứng
                                if (strtolower($attribute->name) === 'size') {
                                    $sizes = array_merge($sizes, $textValues);
                                } elseif (strtolower($attribute->name) === 'color' || strtolower($attribute->name) === 'màu sắc') {
                                    $colors = array_merge($colors, $textValues);
                                }
                            }
                        }
                    }
                }

                // Cập nhật sizes và colors trong bảng products
                $product->update([
                    'sizes' => array_unique($sizes),
                    'colors' => array_unique($colors)
                ]);

                // Cập nhật biến thể
                if ($request->has('variants')) {
                    // Lấy danh sách ID biến thể hiện tại
                    $currentVariantIds = $product->variants->pluck('id')->toArray();
                    $updatedVariantIds = [];

                    foreach ($request->variants as $variantData) {
                        if (isset($variantData['id'])) {
                            // Cập nhật biến thể hiện có
                            $variant = $product->variants()->find($variantData['id']);
                            if ($variant) {
                                $variant->update([
                                    'sku' => $variantData['sku'],
                                    'price' => $variantData['price'],
                                    'sale_price' => $variantData['sale_price'] ?? null,
                                    'stock' => $variantData['stock']
                                ]);
                                $updatedVariantIds[] = $variant->id;
                            }
                        } else {
                            // Tạo biến thể mới
                            $variant = $product->variants()->create([
                                'sku' => $variantData['sku'],
                                'price' => $variantData['price'],
                                'sale_price' => $variantData['sale_price'] ?? null,
                                'stock' => $variantData['stock'],
                                'is_active' => true
                            ]);

                            // Liên kết với các giá trị thuộc tính
                            if (isset($variantData['attributes'])) {
                                foreach ($variantData['attributes'] as $attributeId => $valueId) {
                                    $variant->attributeValues()->attach($valueId);
                                }
                            }

                            $updatedVariantIds[] = $variant->id;
                        }
                    }

                    // Xóa các biến thể không còn được sử dụng
                    $variantsToDelete = array_diff($currentVariantIds, $updatedVariantIds);
                    if (!empty($variantsToDelete)) {
                        $product->variants()->whereIn('id', $variantsToDelete)->delete();
                    }
                } else {
                    // Nếu không có biến thể nào được gửi lên, xóa tất cả biến thể cũ
                    $product->variants()->delete();
                }
            } else {
                // Nếu chuyển từ variant sang simple, xóa hết thuộc tính và reset sizes, colors
                $product->attributes()->detach();
                $product->update([
                    'sizes' => [],
                    'colors' => []
                ]);
            }

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Cập nhật sản phẩm thành công!');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Đã có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function trash()
    {
        $trashedProducts = Product::onlyTrashed()
            ->with(['category', 'brand'])
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        $trashedCount = Product::onlyTrashed()->count();

        return view('admin.products.trash', compact('trashedProducts', 'trashedCount'));
    }

    public function destroy(Product $product)
    {
        try {
            $product->delete();
            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Đã chuyển sản phẩm vào thùng rác');
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
                    'error' => 'Vui lòng chọn ít nhất một sản phẩm'
                ], 400);
            }

            Product::whereIn('id', $ids)->delete();

            return response()->json([
                'success' => 'Đã chuyển các sản phẩm đã chọn vào thùng rác'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Đã có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkToggleStatus(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            $status = $request->input('status');

            if (empty($ids)) {
                return response()->json([
                    'error' => 'Vui lòng chọn ít nhất một sản phẩm'
                ], 400);
            }

            Product::whereIn('id', $ids)->update(['is_active' => $status]);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Đã có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function restore($id)
    {
        try {
            $product = Product::onlyTrashed()->findOrFail($id);
            $product->restore();

            return redirect()
                ->route('admin.products.trash')
                ->with('success', 'Khôi phục sản phẩm thành công');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Đã có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function bulkRestore(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            if (empty($ids)) {
                return response()->json([
                    'error' => 'Vui lòng chọn ít nhất một sản phẩm'
                ], 400);
            }

            Product::onlyTrashed()
                ->whereIn('id', $ids)
                ->restore();

            return response()->json([
                'success' => true,
                'message' => 'Khôi phục các sản phẩm thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Đã có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function forceDelete($id)
    {
        try {
            $product = Product::onlyTrashed()->findOrFail($id);

            // Xóa ảnh sản phẩm nếu có
            if ($product->thumbnail) {
                Storage::disk('public')->delete($product->thumbnail);
            }

            $product->forceDelete();

            return redirect()
                ->route('admin.products.trash')
                ->with('success', 'Xóa sản phẩm vĩnh viễn thành công');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Đã có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
