<?php




namespace App\Http\Controllers\Admin;




use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductVariantRequest;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ProductVariantStoreRequest;


/**
 * Class ProductVariantController
 * @package App\Http\Controllers\Admin
 */
class ProductVariantController extends Controller
{
    /**
     * Display the form to create new variants for a product.
     */
    public function create(Product $product)
    {
        $attributes = Attribute::with(['values' => function($query) {
            $query->where('is_active', 1);
        }])->where('is_active', 1)->get();
        return view('admin.products.variants.create', compact('product', 'attributes'));
    }




    /**
     * Store new variants for a product.
     */
    public function store(ProductVariantRequest $request, Product $product)
    {
        try {
            $validatedData = $request->validated();


            foreach ($validatedData['variants'] as $variantData) {
                $variant = new ProductVariant([
                    'sku' => $variantData['sku'],
                    'stock' => $variantData['stock'],
                    'regular_price' => $variantData['regular_price'],
                    'sale_price' => $variantData['sale_price'] ?? null,
                ]);


                // Không lưu ảnh chính vào trường img nữa, chỉ lưu vào bảng product_images
                $product->variants()->save($variant);


                if (isset($variantData['attribute_values']) && !empty($variantData['attribute_values'])) {
                    $variant->attributeValues()->sync($variantData['attribute_values']);
                } else {
                    // Nếu không có thuộc tính, xóa biến thể vừa tạo và báo lỗi
                    $variant->delete();
                    throw new \Exception("Bạn chưa thêm thuộc tính cho biến thể.");
                }


                // Lưu nhiều ảnh vào bảng product_images
                if (isset($variantData['images']) && is_array($variantData['images'])) {
                    foreach ($variantData['images'] as $imageFile) {
                        if ($imageFile && $imageFile->isValid()) {
                            $path = $imageFile->store('products/variants', 'public');
                            \App\Models\ProductImage::create([
                                'product_id' => $product->id,
                                'product_variant_id' => $variant->id,
                                'url' => $path,
                            ]);
                        }
                    }
                }
            }


            return redirect()->route('admin.products.edit', $product)
                ->with('success', 'Đã tạo biến thể thành công');
        } catch (\Exception $e) {
            Log::error('Error creating variants:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->all()
            ]);


            return back()
                ->withInput()
                ->withErrors(['error' => 'Đã xảy ra lỗi khi tạo biến thể']);
        }
    }




    /**
     * Display the form to edit a specific variant.
     */
    public function edit(Product $product, ProductVariant $variant)
    {
        $attributes = Attribute::with(['values' => function($query) {
            $query->where('is_active', 1);
        }])->where('is_active', 1)->get();




        // Thêm dòng này để truyền $selectedValues cho view
        $selectedValues = $variant->attributeValues->pluck('id')->toArray();




        if (request()->has('modal')) {
            // Trả về partial form cho modal
            return view('admin.products.variants._edit_form', compact('product', 'variant', 'attributes', 'selectedValues'));
        }




        return view('admin.products.variants.edit', compact('product', 'variant', 'attributes', 'selectedValues'));
    }


    /**
     * Get all images for a variant
     */
    public function getImages(Product $product, ProductVariant $variant)
    {
        try {
            $images = $variant->images()->orderBy('is_primary', 'desc')->get();


            $formattedImages = $images->map(function($image) {
                return [
                    'id' => $image->id,
                    'url' => asset('storage/' . $image->url),
                    'name' => basename($image->url),
                    'is_primary' => $image->is_primary,
                    'created_at' => $image->created_at->format('Y-m-d H:i:s')
                ];
            });


            return response()->json([
                'success' => true,
                'images' => $formattedImages
            ]);


        } catch (\Exception $e) {
            \Log::error('Error getting variant images:', [
                'error' => $e->getMessage(),
                'variant_id' => $variant->id
            ]);


            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải ảnh biến thể.'
            ], 500);
        }
    }


    /**
     * Upload images for a variant
     */
    public function uploadImages(Request $request, Product $product, ProductVariant $variant)
    {
        try {
            $request->validate([
                'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // Max 10MB per file
            ]);


            $uploadedImages = [];


            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('products/variants', 'public');


                    $imageModel = \App\Models\ProductImage::create([
                        'product_id' => $product->id,
                        'product_variant_id' => $variant->id,
                        'url' => $path,
                        'is_primary' => 0
                    ]);


                    $uploadedImages[] = [
                        'id' => $imageModel->id,
                        'url' => asset('storage/' . $path),
                        'name' => basename($path),
                        'is_primary' => 0,
                        'created_at' => $imageModel->created_at->format('Y-m-d H:i:s')
                    ];
                }
            }


            return response()->json([
                'success' => true,
                'message' => 'Tải ảnh lên thành công',
                'images' => $uploadedImages
            ]);


        } catch (\Exception $e) {
            \Log::error('Error uploading variant images:', [
                'error' => $e->getMessage(),
                'variant_id' => $variant->id
            ]);


            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải ảnh lên: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Xóa ảnh biến thể
     */
    public function deleteImage(Request $request, Product $product, ProductVariant $variant, $imageId)
    {
        try {
            $image = \App\Models\ProductImage::where('product_variant_id', $variant->id)
                ->where('id', $imageId)
                ->firstOrFail();


            if ($image->is_primary) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa ảnh đại diện từ đây. Vui lòng đặt ảnh khác làm ảnh đại diện trước.'
                ], 400);
            }


            // Xóa file ảnh khỏi storage
            if (Storage::disk('public')->exists($image->url)) {
                Storage::disk('public')->delete($image->url);
            }


            $image->delete();


            return response()->json([
                'success' => true,
                'message' => 'Đã xóa ảnh thành công.'
            ]);


        } catch (\Exception $e) {
            \Log::error('Error deleting variant image:', [
                'error' => $e->getMessage(),
                'variant_id' => $variant->id,
                'image_id' => $imageId
            ]);


            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa ảnh: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Update a specific variant.
     */
    public function update(Request $request, Product $product, ProductVariant $variant)
    {
        // Đã bỏ kiểm tra isInProcessingOrder để cho phép chỉnh sửa mọi biến thể
        $validated = $request->validate([
            'sku' => 'nullable|string|max:255',
            'stock' => 'required|integer|min:0',
            'regular_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'img' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'attribute_values' => 'required|array|min:1',
            'attribute_values.*' => 'required|exists:attribute_values,id',
        ], [
            'img.required' => 'Bạn cần chọn ảnh cho sản phẩm biến thể',
            'img.image' => 'File phải là hình ảnh',
            'img.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif',
            'img.max' => 'Kích thước hình ảnh không được vượt quá 2MB',
            'attribute_values.required' => 'Bạn chưa thêm thuộc tính cho biến thể',
            'attribute_values.array' => 'Dữ liệu thuộc tính không hợp lệ',
            'attribute_values.min' => 'Bạn chưa thêm thuộc tính cho biến thể',
            'attribute_values.*.exists' => 'Giá trị thuộc tính không tồn tại',
        ]);


        // Kiểm tra giá khuyến mãi không được lớn hơn giá gốc
        if (isset($validated['sale_price']) && $validated['sale_price'] !== null && $validated['sale_price'] > $validated['regular_price']) {
            return back()->withInput()->withErrors(['sale_price' => 'Giá khuyến mãi không được lớn hơn giá gốc.']);
        }




        try {
            DB::beginTransaction();




            Log::info('Attempting to update variant:', [
                'variant_id' => $variant->id,
                'validated_data' => $validated,
            ]);




            // Check for duplicate attribute combinations (excluding current variant)
            $attributeValueIds = array_values($validated['attribute_values']);
            sort($attributeValueIds);
            $existingCombinations = $product->variants->filter(function ($v) use ($variant) {
                return $v->id !== $variant->id;
            })->map(function ($v) {
                return $v->attributeValues->pluck('id')->sort()->values()->toArray();
            })->toArray();




            if (in_array($attributeValueIds, $existingCombinations)) {
                throw new \Exception("Tổ hợp thuộc tính này đã tồn tại trong một biến thể khác.");
            }




            // Handle image upload
            $imgPath = $variant->img;
            if ($request->hasFile('img')) {
                if ($imgPath && Storage::disk('public')->exists($imgPath)) {
                    Storage::disk('public')->delete($imgPath);
                }
                $imgPath = $request->file('img')->store('products/variants', 'public');
            }




            // Generate unique SKU if needed
            $sku = strtoupper(trim($validated['sku']));
            if (empty($sku) || ProductVariant::where('sku', $sku)->where('id', '!=', $variant->id)->exists()) {
                $baseSku = $product->sku . '-VAR';
                $counter = 1;
                do {
                    $sku = $baseSku . '-' . $counter;
                    $counter++;
                } while (ProductVariant::where('sku', $sku)->where('id', '!=', $variant->id)->exists());
            }




            // Update variant
            $variant->update([
                'sku' => $sku,
                'stock' => $validated['stock'],
                'regular_price' => $validated['regular_price'],
                'sale_price' => $validated['sale_price'] ?? null,
                'img' => $imgPath,
            ]);




            // Sync attribute values
            $variant->attributeValues()->sync($validated['attribute_values']);




            Log::info('Variant updated:', ['variant_id' => $variant->id]);




            DB::commit();




            return redirect()->route('admin.products.edit', $product)
                ->with('success', 'Biến thể đã được cập nhật thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating variant:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật biến thể: ' . $e->getMessage());
        }
    }


    /**
     * Delete the default image of a variant.
     */
    public function deleteDefaultImage(Request $request, Product $product, ProductVariant $variant)
    {
        try {
            if ($variant->img && Storage::disk('public')->exists($variant->img)) {
                Storage::disk('public')->delete($variant->img);
                $variant->update(['img' => null]);
                return response()->json(['success' => true]);
            }
            return response()->json(['success' => false, 'error' => 'Không tìm thấy ảnh mặc định!']);
        } catch (\Exception $e) {
            Log::error('Error deleting default image:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['success' => false, 'error' => 'Xóa ảnh mặc định thất bại!'], 500);
        }
    }


    /**
     * Upload gallery images for a variant.
     * Có thể được gọi từ cả trang tạo mới và chỉnh sửa sản phẩm.
     * Khi tạo mới, variant_id sẽ là null và sử dụng variant_index để xác định biến thể.
     */
    public function uploadGallery(Request $request)
    {
        try {
            $request->validate([
                'variant_id' => 'nullable|exists:product_variants,id',
                'variant_index' => 'nullable|integer',
                'gallery.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
            ]);


            $variantId = $request->variant_id;
            $variantIndex = $request->variant_index;
            $productId = $request->product_id; // Có thể cần cho trường hợp tạo mới


            // Kiểm tra xem có ảnh nào được tải lên không
            if (!$request->hasFile('gallery')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có ảnh nào được tải lên'
                ], 400);
            }


            $uploadedImages = [];
            $temporaryImages = [];


            // Tạo thư mục tạm nếu chưa tồn tại
            $tempDir = 'products/variants/temp';
            if (!Storage::disk('public')->exists($tempDir)) {
                Storage::disk('public')->makeDirectory($tempDir, 0755, true);
            }


            foreach ($request->file('gallery') as $file) {
                // Tạo tên file duy nhất
                $fileName = 'temp_' . ($variantId ?? 'new') . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();


                if ($variantId) {
                    // Nếu có variant_id (trường hợp chỉnh sửa)
                    $path = $file->storeAs('products/variants', $fileName, 'public');


                    // Tạo bản ghi trong database
                    $image = new \App\Models\ProductImage([
                        'product_id' => ProductVariant::find($variantId)->product_id,
                        'product_variant_id' => $variantId,
                        'url' => $path,
                        'is_default' => false
                    ]);


                    $image->save();


                    $uploadedImages[] = [
                        'id' => $image->id,
                        'url' => asset('storage/' . $path)
                    ];
                } else {
                    // Nếu không có variant_id (trường hợp tạo mới)
                    $path = $file->storeAs($tempDir, $fileName, 'public');


                    // Lưu thông tin ảnh tạm để trả về
                    $uploadedImages[] = [
                        'id' => null, // Chưa có ID vì chưa lưu vào database
                        'url' => asset('storage/' . $path),
                        'temp_path' => $path, // Lưu đường dẫn tạm để xử lý sau
                        'variant_index' => $variantIndex
                    ];
                }
            }


            return response()->json([
                'success' => true,
                'message' => 'Tải lên ảnh thành công',
                'images' => $uploadedImages
            ]);


        } catch (\Exception $e) {
            Log::error('Error uploading variant gallery images:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'variant_id' => $request->variant_id ?? null,
                'variant_index' => $request->variant_index ?? null
            ]);


            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải lên ảnh: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Delete a specific image from product_images.
     */
    public function deleteVariantImage(Request $request, Product $product)
    {
        try {
            $image = \App\Models\ProductImage::findOrFail($request->image_id);
            if (Storage::disk('public')->exists($image->url)) {
                Storage::disk('public')->delete($image->url);
            }
            $image->delete();
            return response()->json(['success' => true, 'url' => $image->url]);
        } catch (\Exception $e) {
            Log::error('Error deleting image:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['success' => false, 'error' => 'Xóa ảnh thất bại!'], 500);
        }
    }


    /**
     * Delete a gallery image of a variant.
     */
    public function deleteGalleryImage(Product $product, \App\Models\ProductImage $image)
    {
        try {
            // Kiểm tra xem ảnh có thuộc về sản phẩm này không
            if ($image->product_id !== $product->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có quyền xóa ảnh này.'
                ], 403);
            }


            // Xóa file ảnh từ storage
            if (Storage::disk('public')->exists($image->url)) {
                Storage::disk('public')->delete($image->url);
            }


            // Xóa bản ghi trong database
            $image->delete();


            return response()->json([
                'success' => true,
                'message' => 'Đã xóa ảnh thành công.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting gallery image:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'image_id' => $image->id,
                'product_id' => $product->id
            ]);


            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa ảnh.'
            ], 500);
        }
    }


    /**
     * Delete a specific variant.
     */
    public function destroy(Product $product, ProductVariant $variant)
    {
        // Kiểm tra nếu biến thể đã từng được mua trong đơn hàng hoặc đang có trong giỏ hàng thì không cho xóa
        $hasOrder = $variant->orderItems()->exists();
        $hasCart = \App\Models\CartItem::where('product_variant_id', $variant->id)->exists();
        if ($hasOrder || $hasCart) {
            return redirect()->route('admin.products.edit', $product)
                ->with('error', 'Không thể xoá biến thể đã có đơn hàng hoặc giỏ hàng');
        }
        try {
            DB::beginTransaction();


            Log::info('Attempting to delete variant:', ['variant_id' => $variant->id]);


            // Delete image if exists
            if ($variant->img && Storage::disk('public')->exists($variant->img)) {
                Storage::disk('public')->delete($variant->img);
            }


            // Delete attribute value associations
            DB::table('attribute_value_product_variant')
                ->where('product_variant_id', $variant->id)
                ->delete();


            // Delete variant
            $variant->delete();


            Log::info('Variant deleted:', ['variant_id' => $variant->id]);


            DB::commit();


            return redirect()->route('admin.products.edit', $product)
                ->with('success', 'Biến thể đã được xóa thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting variant:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'Có lỗi xảy ra khi xóa biến thể: ' . $e->getMessage());
        }
    }




    /**
     * Display details of a product and its variants.
     */
    public function show(Product $product)
    {
        $product->load('variants.attributeValues.attribute');
        return view('admin.products.show', compact('product'));
    }
}

