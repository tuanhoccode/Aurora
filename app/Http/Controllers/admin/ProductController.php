<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Admin\ProductRequest;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['brand']);

        // Tìm kiếm theo tên hoặc mã sản phẩm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Lọc theo thương hiệu
        if ($request->filled('brand')) {
            $query->where('brand_id', $request->brand);
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('is_active', $request->status == '1');
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        $brands = Brand::where('is_active', 1)->get();
        $categories = Category::where('is_active', 1)->get();
        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', 1)->count();
        $saleProducts = Product::where('is_sale', 1)->count();
        $trashedCount = Product::onlyTrashed()->count();

        return view('admin.products.index', compact(
            'products',
            'brands',
            'categories',
            'totalProducts',
            'activeProducts',
            'saleProducts',
            'trashedCount'
        ));
    }

    public function create()
    {
        $brands = Brand::where('is_active', 1)->get();
        $categories = Category::where('is_active', 1)->get();
        $trashedCount = Product::onlyTrashed()->count();

        return view('admin.products.create', compact('brands', 'categories', 'trashedCount'));
    }

    public function store(ProductRequest $request)
    {
        $data = $request->validated();
        
        // Handle slug
        $baseSlug = Str::slug($data['name']);
        $slug = $baseSlug;
        $counter = 1;
        
        // Kiểm tra và tạo slug duy nhất
        while (Product::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        $data['slug'] = $slug;
        
        // Handle SKU - tự động tạo nếu không có hoặc kiểm tra trùng lặp
        if (empty($data['sku'])) {
            // Tạo SKU tự động
            $data['sku'] = 'PRD-' . strtoupper(Str::random(5));
        } else {
            // Kiểm tra SKU có trùng không
            $existingProduct = Product::where('sku', $data['sku'])->first();
            if ($existingProduct) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['sku' => 'SKU đã tồn tại. Vui lòng chọn SKU khác.']);
            }
            
            // Đảm bảo SKU có prefix PRD-
            if (!Str::startsWith(strtoupper($data['sku']), 'PRD-')) {
                $data['sku'] = 'PRD-' . strtoupper($data['sku']);
            } else {
                $data['sku'] = strtoupper($data['sku']);
            }
        }
        
        // Handle sale price
        $data['is_sale'] = !empty($data['sale_price']);
        if (!$data['is_sale']) {
            $data['sale_price'] = null;
        }

        // Handle stock for non-variant products
        if ($data['type'] !== 'variant') {
            $data['stock'] = $request->input('stock', 0);
        }

        // Handle thumbnail
        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = 'product-' . $slug . '-' . time() . '.' . $file->getClientOriginalExtension();
            $data['thumbnail'] = $file->storeAs('products', $filename, 'public');
        }

        // Handle gallery
        if ($request->hasFile('gallery')) {
            $gallery = [];
            foreach ($request->file('gallery') as $file) {
                $filename = 'gallery-' . $slug . '-' . time() . '-' . Str::random(5) . '.' . $file->getClientOriginalExtension();
                $gallery[] = $file->storeAs('products/gallery', $filename, 'public');
            }
            $data['gallery'] = json_encode($gallery);
        }

        // Create product
        $product = Product::create($data);

        // Sync categories
        if (!empty($data['categories'])) {
            $product->categories()->sync($data['categories']);
        }

        // Redirect based on the button clicked
        if ($request->has('redirect_to_variant')) {
            return redirect()->route('admin.products.variants.create', $product)
                ->with('success', 'Sản phẩm đã được tạo thành công. Bây giờ bạn có thể tạo biến thể.');
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Sản phẩm đã được tạo thành công.');
    }

    public function show(Product $product)
    {
        // Load lại variants với thuộc tính và giá trị thuộc tính
        $product->load([
            'variants.attributeValues.attribute',
            'variants.attributeValues' => function($query) {
                $query->with('attribute');
            }
        ]);
        
        return view('admin.products.show', compact('product'));
    }

    public function edit($id)
    {
        try {
            // Nạp luôn các biến thể và thuộc tính cho view edit
            $product = Product::with([
                'brand',
                'categories',
                'variants.attributeValues.attribute'
            ])->findOrFail($id);

            $brands = Brand::where('is_active', 1)->get();
            $categories = Category::where('is_active', 1)->get();
            $trashedCount = Product::onlyTrashed()->count();

            // Luôn trả về view edit sản phẩm, không chuyển sang select
            return view('admin.products.edit', compact('product', 'brands', 'categories', 'trashedCount'));
        } catch (\Exception $e) {
            Log::error('Error loading product:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->route('admin.products.index')
                ->with('error', 'Không thể tải thông tin sản phẩm. ' . $e->getMessage());
        }
    }

    public function update(Request $request, Product $product)
    {
        try {
            // Lấy chỉ các trường cho phép
            $data = $request->only([
                'name', 'slug', 'brand_id', 'description', 'short_description',
                'sku', 'price', 'sale_price', 'type', 'thumbnail', 'is_active',
                'is_sale', 'views', 'stock', 'gallery', 'digital_file'
            ]);

            // Đảm bảo không có trường deleted_at
            unset($data['deleted_at']);

            // Xử lý checkbox is_active
            $data['is_active'] = $request->has('is_active') ? 1 : 0;

            // Nếu là sản phẩm biến thể thì không update các trường stock, price, sale_price
            if ($data['type'] === 'variant') {
                unset($data['stock'], $data['price'], $data['sale_price']);
            }

            // Xử lý slug nếu tên sản phẩm thay đổi
            if (isset($data['name']) && $data['name'] !== $product->name) {
                $baseSlug = Str::slug($data['name']);
                $slug = $baseSlug;
                $counter = 1;
                while (Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }
                $data['slug'] = $slug;
            }

            // Xử lý SKU - giữ nguyên SKU hiện tại, không cho phép thay đổi
            if (empty($data['sku'])) {
                $data['sku'] = $product->sku;
            } else {
                if ($data['sku'] !== $product->sku) {
                    $existingProduct = Product::where('sku', $data['sku'])->where('id', '!=', $product->id)->first();
                    if ($existingProduct) {
                        return redirect()->back()
                            ->withInput()
                            ->withErrors(['sku' => 'SKU đã tồn tại. Vui lòng chọn SKU khác.']);
                    }
                }
                if (!Str::startsWith(strtoupper($data['sku']), 'PRD-')) {
                    $data['sku'] = 'PRD-' . strtoupper($data['sku']);
                } else {
                    $data['sku'] = strtoupper($data['sku']);
                }
            }

            // Xử lý sale price
            $data['is_sale'] = !empty($data['sale_price']);
            if (!$data['is_sale']) {
                $data['sale_price'] = null;
            }

            // Xử lý stock chỉ cho sản phẩm không phải biến thể
            if ($data['type'] !== 'variant') {
                $data['stock'] = $request->input('stock', $product->stock);
            } else {
                unset($data['stock']);
            }

            // Xử lý thumbnail upload
            if ($request->hasFile('thumbnail')) {
                if ($product->thumbnail && Storage::disk('public')->exists($product->thumbnail)) {
                    Storage::disk('public')->delete($product->thumbnail);
                }
                $file = $request->file('thumbnail');
                $filename = 'product-' . $data['slug'] . '-' . time() . '.' . $file->getClientOriginalExtension();
                $data['thumbnail'] = $file->storeAs('products', $filename, 'public');
            }

            // Xử lý gallery upload
            if ($request->hasFile('gallery')) {
                $gallery = [];
                foreach ($request->file('gallery') as $file) {
                    $filename = 'gallery-' . $data['slug'] . '-' . time() . '-' . Str::random(5) . '.' . $file->getClientOriginalExtension();
                    $gallery[] = $file->storeAs('products/gallery', $filename, 'public');
                }
                $data['gallery'] = json_encode($gallery);
            }

            $product->fill($data);
            $product->save();

            if ($request->has('categories')) {
                $product->categories()->sync($request->input('categories'));
            }

            return redirect()->route('admin.products.index')
                ->with('success', 'Cập nhật sản phẩm thành công!');
        } catch (\Exception $e) {
            Log::error('Error updating product:', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function destroy(Product $product)
    {
        try {
            $product->delete();
            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Sản phẩm đã được xóa thành công!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Đã có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function bulkToggleStatus(Request $request)
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:products,id',
                'status' => 'required|boolean'
            ]);

            Product::whereIn('id', $validated['ids'])->update([
                'is_active' => $validated['status']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:products,id'
            ]);

            Product::whereIn('id', $validated['ids'])->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa sản phẩm thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function trash()
    {
        $trashedProducts = Product::onlyTrashed()
            ->with(['brand', 'categories'])
            ->latest()
            ->paginate(10);

        return view('admin.products.trash', compact('trashedProducts'));
    }

    public function restore($id)
    {
        try {
            $product = Product::onlyTrashed()->findOrFail($id);
            $product->restore();

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Khôi phục sản phẩm thành công!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function forceDelete($id)
    {
        try {
            $product = Product::onlyTrashed()->findOrFail($id);
            
            // Xóa ảnh sản phẩm
            if ($product->thumbnail) {
                Storage::disk('public')->delete($product->thumbnail);
            }

            // Xóa các liên kết
            $product->categories()->detach();
            
            // Xóa vĩnh viễn sản phẩm
            $product->forceDelete();

            return redirect()
                ->route('admin.products.trash')
                ->with('success', 'Đã xóa vĩnh viễn sản phẩm!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function bulkRestore(Request $request)
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:products,id'
            ]);

            Product::onlyTrashed()
                ->whereIn('id', $validated['ids'])
                ->restore();

            return response()->json([
                'success' => true,
                'message' => 'Khôi phục sản phẩm thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkForceDelete(Request $request)
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:products,id'
            ]);

            $products = Product::onlyTrashed()->whereIn('id', $validated['ids'])->get();

            foreach ($products as $product) {
                // Xóa ảnh sản phẩm
                if ($product->thumbnail) {
                    Storage::disk('public')->delete($product->thumbnail);
                }

                // Xóa các liên kết
                $product->categories()->detach();
                
                // Xóa vĩnh viễn sản phẩm
                $product->forceDelete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Xóa vĩnh viễn sản phẩm thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}
