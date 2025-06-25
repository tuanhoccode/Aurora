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
use App\Models\ProductGallery;
use Illuminate\Validation\ValidationException;

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
        try {
            DB::beginTransaction();
            
            $data = $request->validated();
            
            // Xử lý slug
            $baseSlug = Str::slug($data['name']);
            $slug = $baseSlug;
            $counter = 1;
            
            // Kiểm tra và tạo slug duy nhất
            while (Product::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
            $data['slug'] = $slug;
            
            // Xử lý SKU - tự động tạo nếu không có hoặc kiểm tra trùng lặp
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
            }
            
            // Đảm bảo SKU có prefix PRD-
            if (!Str::startsWith(strtoupper($data['sku']), 'PRD-')) {
                $data['sku'] = 'PRD-' . strtoupper($data['sku']);
            } else {
                $data['sku'] = strtoupper($data['sku']);
            }

            // Xử lý sale price
            $data['is_sale'] = !empty($data['sale_price']);
            if (!$data['is_sale']) {
                $data['sale_price'] = null;
            }

            // Xử lý stock cho sản phẩm không phải biến thể
            if ($data['type'] !== 'variant') {
                $data['stock'] = $request->input('stock', 0);
            }

            // Xử lý thumbnail
            if ($request->hasFile('thumbnail')) {
                $file = $request->file('thumbnail');
                $filename = 'product-' . $slug . '-' . time() . '.' . $file->getClientOriginalExtension();
                $data['thumbnail'] = $file->storeAs('products', $filename, 'public');
            }

            // Tạo sản phẩm
            $product = Product::create($data);

            // Xử lý gallery images
            if ($request->hasFile('gallery_images')) {
                Log::info('Processing gallery images upload');
                
                $galleryImages = $request->file('gallery_images');
                Log::info('Number of images: ' . (is_array($galleryImages) ? count($galleryImages) : 0));
                
                if (is_array($galleryImages)) {
                    foreach ($galleryImages as $image) {
                        try {
                            Log::info('Processing image: ' . $image->getClientOriginalName());
                            
                            // Kiểm tra xem file có phải là hình ảnh không
                            if (!$image->isValid()) {
                                Log::error('Invalid image: ' . $image->getClientOriginalName());
                                continue;
                            }
                            
                            // Lưu file vào storage
                            $path = $image->store('products/gallery', 'public');
                            Log::info('Stored image at: ' . $path);
                            
                            // Tạo record trong bảng product_galleries
                            $gallery = $product->galleries()->create([
                                'image' => $path
                            ]);
                            Log::info('Created gallery record with ID: ' . $gallery->id);
                        } catch (\Exception $e) {
                            Log::error('Error storing gallery image: ' . $e->getMessage());
                            continue; // Skip this image and continue with others
                        }
                    }
                }
            }

            // Sync categories
            if (!empty($data['categories'])) {
                $product->categories()->sync($data['categories']);
            }

            DB::commit();

            // Redirect dựa vào nút được nhấn
            if ($request->has('redirect_to_variant')) {
                return redirect()->route('admin.product-variants.create', $product->id)
                    ->with('success', 'Sản phẩm đã được tạo thành công. Bây giờ hãy tạo biến thể.');
            }

            return redirect()->route('admin.products.index')
                ->with('success', 'Sản phẩm đã được tạo thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating product: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Có lỗi xảy ra khi tạo sản phẩm. Vui lòng thử lại.');
        }
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

    public function update(ProductRequest $request, Product $product)
    {
        try {
            DB::beginTransaction();
            
            $data = $request->validated();
            
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
                while (Product::where('slug', $slug)
                    ->where('id', '!=', $product->id)
                    ->exists()) {
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
                    $existingProduct = Product::where('sku', $data['sku'])
                        ->where('id', '!=', $product->id)
                        ->first();
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

            // Xử lý thumbnail update
            if ($request->hasFile('thumbnail')) {
                if ($product->thumbnail && Storage::disk('public')->exists($product->thumbnail)) {
                    Storage::disk('public')->delete($product->thumbnail);
                }
                $file = $request->file('thumbnail');
                $filename = 'product-' . $data['slug'] . '-' . time() . '.' . $file->getClientOriginalExtension();
                $data['thumbnail'] = $file->storeAs('products', $filename, 'public');
            }

            // Xử lý gallery images update
            if ($request->hasFile('gallery_images')) {
                $galleryImages = $request->file('gallery_images');
                
                if (is_array($galleryImages)) {
                    $successImages = [];
                    $failedImages = [];
                    
                    foreach ($galleryImages as $image) {
                        try {
                            // Kiểm tra xem file có phải là hình ảnh không
                            if (!$image->isValid()) {
                                $failedImages[] = $image->getClientOriginalName();
                                continue;
                            }
                            
                            // Lưu file vào storage
                            $path = $image->store('products/gallery', 'public');
                            
                            // Tạo record trong bảng product_galleries
                            $gallery = new ProductGallery([
                                'product_id' => $product->id,
                                'image' => $path
                            ]);
                            $gallery->save();
                            
                            $successImages[] = $image->getClientOriginalName();
                        } catch (\Exception $e) {
                            $failedImages[] = $image->getClientOriginalName();
                            Log::error('Error storing gallery image: ' . $e->getMessage());
                            continue;
                        }
                    }
                    
                    // Thêm thông báo về kết quả upload
                    if (!empty($successImages)) {
                        session()->flash('success', 'Đã thêm ' . count($successImages) . ' hình ảnh thành công');
                    }
                    if (!empty($failedImages)) {
                        session()->flash('warning', 'Có ' . count($failedImages) . ' hình ảnh không thể thêm: ' . implode(', ', $failedImages));
                    }
                }
            }

            // Update product
            $product->update($data);

            // Sync categories
            if ($request->has('categories')) {
                $product->categories()->sync($request->input('categories'));
            }

            DB::commit();

            // Thêm thông báo chi tiết
            $message = 'Cập nhật sản phẩm thành công!';
            if (!empty($successImages)) {
                $message .= ' Đã thêm ' . count($successImages) . ' hình ảnh mới.';
            }
            
            return redirect()->route('admin.products.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating product: ' . $e->getMessage());
            
            // Thêm thông báo lỗi chi tiết
            $message = 'Có lỗi xảy ra khi cập nhật sản phẩm.';
            if ($e instanceof ValidationException) {
                $message .= ' Vui lòng kiểm tra lại dữ liệu nhập.';
            }
            
            return back()->withInput()
                ->with('error', $message);
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

    public function deleteGalleryImage(Request $request, Product $product)
    {
        try {
            $path = $request->json('path');
            
            // Tìm và xóa gallery image
            $gallery = $product->galleries()->where('image', $path)->first();
            
            if (!$gallery) {
                return response()->json(['success' => false, 'message' => 'Hình ảnh không tồn tại'], 404);
            }

            // Xóa file từ storage
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            // Xóa record từ database
            $gallery->delete();

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Error deleting gallery image: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra khi xóa hình ảnh'], 500);
        }
    }
}
