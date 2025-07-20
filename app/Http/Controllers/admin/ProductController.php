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

        $brands = Brand::where('is_active', 1)->get(); // admin vẫn lấy tất cả brand đang hoạt động, không lọc is_visible
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
        $attributes = \App\Models\Attribute::with('values')->where('is_active', 1)->get();
        $stocks = \App\Models\Stock::all();

        return view('admin.products.create', compact('brands', 'categories', 'trashedCount', 'attributes', 'stocks'));
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
            
            // Xử lý SKU - luôn tạo tự động với 5 ký tự random phía sau PRD-
            $data['sku'] = 'PRD-' . strtoupper(Str::random(5));

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

            // Lưu biến thể nếu là sản phẩm biến thể và có dữ liệu variants
            if ($data['type'] === 'variant' && $request->has('variants') && !empty($request->input('variants'))) {
                $usedSkus = []; // Mảng để theo dõi SKU đã sử dụng
                
                foreach ($request->input('variants') as $idx => $variantData) {
                    // Kiểm tra SKU trùng lặp
                    $sku = $variantData['sku'] ?? null;
                    if ($sku) {
                        // Kiểm tra SKU đã tồn tại trong session này
                        if (in_array($sku, $usedSkus)) {
                            \Log::warning('SKU duplicate in session', [
                                'sku' => $sku,
                                'request' => $request->all(),
                                'product_id' => $product->id ?? null,
                                'variant_data' => $variantData ?? null,
                            ]);
                            return redirect()->back()
                                ->withInput()
                                ->withErrors(['variants' => "SKU '{$sku}' bị trùng lặp. Vui lòng kiểm tra lại."]);
                        }
                        
                        // Kiểm tra SKU đã tồn tại trong database
                        $existingVariant = \App\Models\ProductVariant::where('sku', $sku)->first();
                        if ($existingVariant) {
                            // Lấy câu SQL cuối cùng
                            $sql = optional(DB::getQueryLog())[count(DB::getQueryLog())-1]['query'] ?? null;
                            \Log::warning('SKU duplicate in database', [
                                'sku' => $sku,
                                'request' => $request->all(),
                                'product_id' => $product->id ?? null,
                                'variant_data' => $variantData ?? null,
                                'existing_variant_id' => $existingVariant->id,
                                'sql' => $sql,
                            ]);
                            return redirect()->back()
                                ->withInput()
                                ->withErrors(['variants' => "SKU '{$sku}' đã tồn tại trong hệ thống. Vui lòng chọn SKU khác."]);
                        }
                        
                        $usedSkus[] = $sku;
                    }
                    
                    $variant = $product->variants()->create([
                        'sku' => $sku,
                        'regular_price' => $variantData['price'] ?? null,
                        'sale_price' => $variantData['sale_price'] ?? null,
                        'stock' => $variantData['stock'] ?? 0,
                    ]);
                    
                    // Lưu thuộc tính cho biến thể
                    if (!empty($variantData['attributes'])) {
                        $variant->attributeValues()->sync($variantData['attributes']);
                    }
                    
                    // Lưu ảnh cho biến thể nếu có
                    if ($request->hasFile("variants.$idx.image")) {
                        $file = $request->file("variants.$idx.image");
                        $filename = 'variant-' . $variant->id . '-' . time() . '.' . $file->getClientOriginalExtension();
                        $path = $file->storeAs('products/variants', $filename, 'public');
                        $variant->img = $path;
                        $variant->save();
                    }
                }
            }

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
            $attributes = \App\Models\Attribute::with('values')->where('is_active', 1)->get();
            $stocks = \App\Models\Stock::all();

            // Luôn trả về view edit sản phẩm, không chuyển sang select
            return view('admin.products.edit', compact('product', 'brands', 'categories', 'trashedCount', 'attributes', 'stocks'));
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
            
            // Log toàn bộ request để kiểm tra
            \Log::info('Update product request data:', [
                'all' => $request->all(),
                'variants' => $request->input('variants'),
                'variants_old' => $request->input('variants_old'),
                'files' => $request->allFiles()
            ]);
            
            $data = $request->validated();
            
            // Log dữ liệu đã validate
            \Log::info('Validated data:', $data);
            
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
            } else {
                // Nếu không đổi tên, giữ nguyên slug cũ
                $data['slug'] = $product->slug;
            }

            // Xử lý SKU - luôn giữ nguyên SKU hiện tại, không cho phép thay đổi
            $data['sku'] = $product->sku;

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

            // Xử lý cập nhật biến thể nếu là sản phẩm biến thể
            if ($data['type'] === 'variant') {
                $usedSkus = [];
                $processedVariants = [];

                // 1. Log thông tin biến thể hiện tại
                \Log::info('Current product variants:', [
                    'count' => $product->variants->count(),
                    'variants' => $product->variants->toArray()
                ]);

                // 2. Xử lý thêm mới biến thể nếu có
                if ($request->has('variants') && is_array($request->input('variants'))) {
                    \Log::info('Processing new variants:', $request->input('variants'));
                    
                    foreach ($request->input('variants') as $index => $variantData) {
                        try {
                            // Kiểm tra SKU trùng lặp
                            $sku = $variantData['sku'] ?? null;
                            if ($sku) {
                                // Kiểm tra SKU đã tồn tại trong database
                                $existingVariant = \App\Models\ProductVariant::where('sku', $sku)->first();
                                if ($existingVariant) {
                                    throw new \Exception("SKU '{$sku}' đã tồn tại trong hệ thống.");
                                }
                                
                                // Kiểm tra SKU trùng trong cùng request
                                if (in_array($sku, $usedSkus)) {
                                    throw new \Exception("SKU '{$sku}' bị trùng lặp trong form.");
                                }
                                $usedSkus[] = $sku;
                            } else {
                                // Tạo SKU tự động nếu không có
                                $sku = 'VAR-' . strtoupper(Str::random(8));
                            }

                            // Tạo biến thể mới
                            $variant = $product->variants()->create([
                                'sku' => $sku,
                                'regular_price' => $variantData['price'] ?? 0,
                                'sale_price' => $variantData['sale_price'] ?? null,
                                'stock' => $variantData['stock'] ?? 0,
                            ]);

                            \Log::info('Created new variant:', [
                                'variant_id' => $variant->id,
                                'sku' => $sku,
                                'data' => $variantData
                            ]);

                            // Lưu thuộc tính cho biến thể
                            if (!empty($variantData['attributes'])) {
                                $variant->attributeValues()->sync($variantData['attributes']);
                                \Log::info('Synced attributes for variant ' . $variant->id, [
                                    'attributes' => $variantData['attributes']
                                ]);
                            }

                            // Xử lý ảnh biến thể nếu có
                            if ($request->hasFile("variants.{$index}.image")) {
                                $file = $request->file("variants.{$index}.image");
                                $filename = 'variant-' . $variant->id . '-' . time() . '.' . $file->getClientOriginalExtension();
                                $path = $file->storeAs('products/variants', $filename, 'public');
                                $variant->img = $path;
                                $variant->save();
                                \Log::info('Uploaded image for variant ' . $variant->id, [
                                    'path' => $path
                                ]);
                            }

                        } catch (\Exception $e) {
                            \Log::error('Error creating variant:', [
                                'error' => $e->getMessage(),
                                'variant_data' => $variantData,
                                'trace' => $e->getTraceAsString()
                            ]);
                            
                            // Nếu có lỗi khi tạo biến thể, rollback và trả về lỗi
                            DB::rollBack();
                            return redirect()->back()
                                ->withInput()
                                ->with('error', 'Lỗi khi tạo biến thể: ' . $e->getMessage());
                        }
                    }
                }

                // 3. Xử lý cập nhật biến thể cũ (nếu có)
                if ($request->has('variants_old')) {
                    $variantsOld = $request->input('variants_old');
                    \Log::info('Updating old variants:', $variantsOld);
                    
                    foreach ($variantsOld as $variantId => $variantData) {
                        try {
                            $variant = $product->variants()->find($variantId);
                            if (!$variant) continue;
                            
                            // Cập nhật thông tin cơ bản
                            $updateData = [
                                'regular_price' => $variantData['price'] ?? $variant->regular_price,
                                'sale_price' => $variantData['sale_price'] ?? $variant->sale_price,
                                'stock' => $variantData['stock'] ?? $variant->stock,
                            ];
                            
                            // Xử lý ảnh nếu có
                            if ($request->hasFile("variants_old.{$variantId}.image")) {
                                $file = $request->file("variants_old.{$variantId}.image");
                                $filename = 'variant-' . $variant->id . '-' . time() . '.' . $file->getClientOriginalExtension();
                                $path = $file->storeAs('products/variants', $filename, 'public');
                                
                                // Xóa ảnh cũ nếu có
                                if ($variant->img && Storage::disk('public')->exists($variant->img)) {
                                    Storage::disk('public')->delete($variant->img);
                                }
                                
                                $updateData['img'] = $path;
                            }
                            
                            // Cập nhật biến thể
                            $variant->update($updateData);
                            
                            // Log thông tin cập nhật
                            \Log::info('Updated variant:', [
                                'variant_id' => $variant->id,
                                'data' => $updateData
                            ]);
                            
                        } catch (\Exception $e) {
                            \Log::error('Error updating variant:', [
                                'variant_id' => $variantId,
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString()
                            ]);
                            
                            // Ghi log lỗi nhưng không dừng quá trình xử lý
                            continue;
                        }
                    }
                }
            }

            // Sync categories
            if ($request->has('categories')) {
                $product->categories()->sync($request->input('categories'));
            }

            DB::commit();
            \Log::info('Product updated successfully', ['product_id' => $product->id]);
            
            return redirect()->route('admin.products.index')
                ->with('success', 'Cập nhật sản phẩm thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating product: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật sản phẩm: ' . $e->getMessage());
        }
    }

    public function destroy(Product $product)
    {
        // Kiểm tra sản phẩm có trong đơn hàng
        $hasOrder = $product->orderItems()->exists();
        // Kiểm tra sản phẩm có trong giỏ hàng
        $hasCart = \App\Models\CartItem::where('product_id', $product->id)->exists();
        if ($hasOrder || $hasCart) {
            return redirect()->back()->with('error', 'Không thể xoá sản phẩm đã có đơn hàng hoặc giỏ hàng');
        }
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
