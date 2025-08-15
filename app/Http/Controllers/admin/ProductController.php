<?php


namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Admin\ProductRequest;
use Illuminate\Support\Facades\DB;
use App\Models\ProductGallery;
use Illuminate\Support\Facades\Auth;
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


        // Lọc theo danh mục
        if ($request->filled('category')) {
            $query->whereHas('categories', function($q) use ($request) {
                $q->where('categories.id', $request->category);
            });
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
        if (Auth::user()->role !== 'admin') {
                abort(403, 'Bạn không có quyền thêm sản phẩm.');
        }
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
            if (Auth::user()->role !== 'admin') {
                abort(403, 'Bạn không có quyền thêm sản phẩm.');
            }
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


            // Xử lý sale price và thời gian khuyến mãi
            $data['is_sale'] = !empty($data['sale_price']);
            if (!$data['is_sale']) {
                $data['sale_price'] = null;
                $data['sale_starts_at'] = null;
                $data['sale_ends_at'] = null;
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
                    // Tự động sinh SKU nếu không có hoặc rỗng
                    if (empty($sku)) {
                        $parentSku = preg_replace('/[^A-Za-z0-9]/', '', $product->sku); // loại bỏ ký tự đặc biệt
                        $size = '';
                        $color = '';
                        if (!empty($variantData['attributes'])) {
                            foreach ($variantData['attributes'] as $attrId => $attrValueId) {
                                $attribute = \App\Models\Attribute::find($attrId);
                                $value = \App\Models\AttributeValue::find($attrValueId);
                                if ($attribute && $value) {
                                    if (stripos($attribute->name, 'size') !== false || stripos($attribute->name, 'kích') !== false) {
                                        $size = strtoupper($value->value);
                                    }
                                    if (stripos($attribute->name, 'màu') !== false || stripos($attribute->name, 'color') !== false) {
                                        // Lấy 2 ký tự đầu không dấu, in hoa
                                        $color = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $value->value));
                                        $color = substr($color, 0, 2);
                                    }
                                }
                            }
                        }
                        $sku = $parentSku;
                        if ($size) $sku .= '-' . $size;
                        if ($color) $sku .= '-' . $color;
                    }
                   
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
                        $existingVariant = $product->variants()->where('sku', $sku)->first();
                        if ($existingVariant) {
                            \Log::warning('SKU duplicate in product', [
                                'sku' => $sku,
                                'request' => $request->all(),
                                'product_id' => $product->id ?? null,
                                'variant_data' => $variantData ?? null,
                                'existing_variant_id' => $existingVariant->id,
                            ]);
                            return redirect()->back()
                                ->withInput()
                                ->withErrors(['variants' => "SKU '{$sku}' đã tồn tại trong các biến thể của sản phẩm này. Vui lòng chọn SKU khác."]);
                        }
                       
                        $usedSkus[] = $sku;
                    }
                   
                    // Xử lý giá khuyến mãi
                    $salePrice = null;
                    if (isset($variantData['sale_price']) && $variantData['sale_price'] !== '') {
                        $salePrice = (int)$variantData['sale_price'];
                    }
                   
                    // Chuẩn hóa thời gian khuyến mãi nếu có
                    $saleStartsAt = null;
                    if (!empty($variantData['sale_starts_at'])) {
                        try { $saleStartsAt = Carbon::parse($variantData['sale_starts_at'])->format('Y-m-d H:i:s'); } catch (\Throwable $e) { $saleStartsAt = null; }
                    }
                    $saleEndsAt = null;
                    if (!empty($variantData['sale_ends_at'])) {
                        try { $saleEndsAt = Carbon::parse($variantData['sale_ends_at'])->format('Y-m-d H:i:s'); } catch (\Throwable $e) { $saleEndsAt = null; }
                    }


                    $variant = $product->variants()->create([
                        'sku' => $sku,
                        'regular_price' => (int)($variantData['regular_price'] ?? $variantData['price'] ?? 0),
                        'sale_price' => $salePrice,
                        'sale_starts_at' => $saleStartsAt,
                        'sale_ends_at' => $saleEndsAt,
                        'stock' => (int)($variantData['stock'] ?? 0),
                    ]);
                   
                    // Lưu thuộc tính cho biến thể
                    if (!empty($variantData['attributes'])) {
                        $variant->attributeValues()->sync($variantData['attributes']);
                    }
                   
                    // Lưu ảnh đại diện cho biến thể nếu có
                    if ($request->hasFile("variants.$idx.image")) {
                        $file = $request->file("variants.$idx.image");
                        $filename = 'variant-' . $variant->id . '-' . time() . '.' . $file->getClientOriginalExtension();
                        $path = $file->storeAs('products/variants', $filename, 'public');
                        $variant->img = $path;
                        $variant->save();
                       
                        // Tạo bản ghi ảnh đại diện trong bảng product_images
                        \App\Models\ProductImage::create([
                            'product_id' => $product->id,
                            'product_variant_id' => $variant->id,
                            'url' => $path,
                            'is_default' => true
                        ]);
                    }
                   
                    // Xử lý ảnh gallery cho biến thể nếu có
                    if (!empty($variantData['gallery_images'])) {
                        foreach ($variantData['gallery_images'] as $imageId) {
                            // Kiểm tra xem có phải là ảnh tạm không (bắt đầu bằng 'temp_')
                            if (strpos($imageId, 'temp_') === 0) {
                                // Đây là ảnh tạm, cần di chuyển từ thư mục temp
                                $tempPath = 'products/variants/temp/' . $imageId;
                                if (Storage::disk('public')->exists($tempPath)) {
                                    // Tạo tên file mới
                                    $newFilename = 'variant-' . $variant->id . '-' . time() . '_' . uniqid() . '.' . pathinfo($tempPath, PATHINFO_EXTENSION);
                                    $newPath = 'products/variants/' . $newFilename;
                                   
                                    // Di chuyển file từ thư mục temp sang thư mục chính
                                    Storage::disk('public')->move($tempPath, $newPath);
                                   
                                    // Tạo bản ghi ảnh
                                    \App\Models\ProductImage::create([
                                        'product_id' => $product->id,
                                        'product_variant_id' => $variant->id,
                                        'url' => $newPath,
                                        'is_default' => false
                                    ]);
                                }
                            } else {
                                // Đây là ảnh đã có ID (trường hợp hiếm khi tạo mới)
                                // Cập nhật lại product_variant_id cho ảnh
                                $image = \App\Models\ProductImage::find($imageId);
                                if ($image) {
                                    $image->update(['product_variant_id' => $variant->id]);
                                }
                            }
                        }
                    }
                }
            }


            // Xử lý gallery images (chỉ cho sản phẩm đơn giản)
            if ($data['type'] === 'simple' && $request->hasFile('gallery_images')) {
                $galleryImages = $request->file('gallery_images');
                if (is_array($galleryImages)) {
                    foreach ($galleryImages as $image) {
                        if ($image->isValid()) {
                            $path = $image->store('products/gallery', 'public');
                            \App\Models\ProductImage::create([
                                'product_id' => $product->id,
                                'url' => $path,
                            ]);
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
            //Không cho nhân viên vào
            if (Auth::user()->role !== 'admin') {
                abort(403, 'Bạn không có quyền chỉnh sửa sản phẩm.');
            }
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
            //Không cho nhân viên vào
            if (Auth::user()->role !== 'admin') {
                abort(403, 'Bạn không có quyền chỉnh sửa sản phẩm.');
            }
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


            // Xử lý sale price và thời gian khuyến mãi
            $data['is_sale'] = !empty($data['sale_price']);
            if (!$data['is_sale']) {
                $data['sale_price'] = null;
                $data['sale_starts_at'] = null;
                $data['sale_ends_at'] = null;
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


            // Xử lý gallery images update (chỉ cho sản phẩm đơn giản)
            if ($data['type'] === 'simple' && $request->hasFile('gallery_images')) {
                $galleryImages = $request->file('gallery_images');
                if (is_array($galleryImages)) {
                    foreach ($galleryImages as $image) {
                        if ($image->isValid()) {
                            $path = $image->store('products/gallery', 'public');
                            \App\Models\ProductImage::create([
                                'product_id' => $product->id,
                                'url' => $path,
                            ]);
                        }
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
                                // Kiểm tra SKU đã tồn tại trong các biến thể của sản phẩm hiện tại
                                $existingVariant = $product->variants()->where('sku', $sku)->first();
                                if ($existingVariant) {
                                    throw new \Exception("SKU '{$sku}' đã tồn tại trong các biến thể của sản phẩm này.");
                                }
                               
                                // Kiểm tra SKU trùng trong variants_old (nếu có)
                                if ($request->has('variants_old')) {
                                    $variantsOldSkus = collect($request->input('variants_old'))->pluck('sku')->filter()->toArray();
                                    if (in_array($sku, $variantsOldSkus)) {
                                        throw new \Exception("SKU '{$sku}' đã tồn tại trong các biến thể hiện tại của sản phẩm.");
                                    }
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
                            // Chuẩn hóa thời gian khuyến mãi nếu có
                            $saleStartsAt = null;
                            if (!empty($variantData['sale_starts_at'])) {
                                try { $saleStartsAt = Carbon::parse($variantData['sale_starts_at'])->format('Y-m-d H:i:s'); } catch (\Throwable $e) { $saleStartsAt = null; }
                            }
                            $saleEndsAt = null;
                            if (!empty($variantData['sale_ends_at'])) {
                                try { $saleEndsAt = Carbon::parse($variantData['sale_ends_at'])->format('Y-m-d H:i:s'); } catch (\Throwable $e) { $saleEndsAt = null; }
                            }


                            $variant = $product->variants()->create([
                                'sku' => $sku,
                                'regular_price' => $variantData['price'] ?? 0,
                                'sale_price' => $variantData['sale_price'] ?? null,
                                'sale_starts_at' => $saleStartsAt,
                                'sale_ends_at' => $saleEndsAt,
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


                            // Validate dữ liệu không được để trống
                            $price = $variantData['price'] ?? null;
                            $stock = $variantData['stock'] ?? null;
                            $sku = $variantData['sku'] ?? null;
                            $errors = [];
                            if ($price === null || $price === '' || $price < 0) {
                                $errors[] = "Giá gốc của biến thể (ID: $variantId) không được để trống hoặc nhỏ hơn 0.";
                            }
                            if ($stock === null || $stock === '' || $stock < 0) {
                                $errors[] = "Tồn kho của biến thể (ID: $variantId) không được để trống hoặc nhỏ hơn 0.";
                            }
                            if ($sku === null || $sku === '') {
                                $errors[] = "SKU của biến thể (ID: $variantId) không được để trống.";
                            }
                            $salePrice = $variantData['sale_price'] ?? null;
                            if ($salePrice !== null && $salePrice !== '' && $price !== null && $price !== '' && $salePrice >= $price) {
                                $errors[] = "Giá khuyến mãi của biến thể (ID: $variantId) phải nhỏ hơn giá gốc.";
                            }
                           
                            // Kiểm tra SKU trùng lặp trong phạm vi sản phẩm hiện tại
                            if ($sku) {
                                $existingVariant = $product->variants()
                                    ->where('sku', $sku)
                                    ->where('id', '!=', $variantId)
                                    ->first();
                                if ($existingVariant) {
                                    $errors[] = "SKU '{$sku}' đã tồn tại trong biến thể khác của sản phẩm này.";
                                }
                            }
                           
                            if (!empty($errors)) {
                                DB::rollBack();
                                return redirect()->back()->withInput()->withErrors(['variants_old' => $errors]);
                            }


                            // Cập nhật thông tin cơ bản
                            $salePrice = null;
                            if (isset($variantData['sale_price']) && $variantData['sale_price'] !== '') {
                                $salePrice = (int)$variantData['sale_price'];
                            }
                           
                            // Chuẩn hóa thời gian khuyến mãi nếu có
                            $saleStartsAt = null;
                            if (!empty($variantData['sale_starts_at'])) {
                                try { $saleStartsAt = Carbon::parse($variantData['sale_starts_at'])->format('Y-m-d H:i:s'); } catch (\Throwable $e) { $saleStartsAt = null; }
                            }
                            $saleEndsAt = null;
                            if (!empty($variantData['sale_ends_at'])) {
                                try { $saleEndsAt = Carbon::parse($variantData['sale_ends_at'])->format('Y-m-d H:i:s'); } catch (\Throwable $e) { $saleEndsAt = null; }
                            }


                            $updateData = [
                                'sku' => $sku,
                                'regular_price' => $price,
                                'sale_price' => $variantData['sale_price'] ?? $variant->sale_price,
                                'sale_starts_at' => $saleStartsAt,
                                'sale_ends_at' => $saleEndsAt,
                                'stock' => $stock,
                            ];
                           
                            // Chỉ cập nhật sale_price nếu có giá trị hoặc được set là null
                            $updateData['sale_price'] = $salePrice;
                           
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
        if (Auth::user()->role !== 'admin') {
                abort(403, 'Bạn không có quyền xóa sản phẩm.');
        }
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
            if (Auth::user()->role !== 'admin') {
                abort(403, 'Bạn không có quyền cập nhật trạng thái.');
            }
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
            if (Auth::user()->role !== 'admin') {
                abort(403, 'Bạn không có quyền xóa sản phẩm.');
            }
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
        if (Auth::user()->role !== 'admin') {
                abort(403, 'Bạn không có quyền vào thùng giác.');
        }
        $trashedProducts = Product::onlyTrashed()
            ->with(['brand', 'categories'])
            ->latest()
            ->paginate(10);


        return view('admin.products.trash', compact('trashedProducts'));
    }


    public function restore($id)
    {
        try {
            if (Auth::user()->role !== 'admin') {
                abort(403, 'Bạn không có quyền khôi phục sản phẩm.');
            }
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
                if (Auth::user()->role !== 'admin') {
                    abort(403, 'Bạn không có quyền xóa vĩnh viễn sản phẩm.');
            }
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
            if (Auth::user()->role !== 'admin') {
                abort(403, 'Bạn không có quyền xóa sản phẩm.');
            }
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
            if (Auth::user()->role !== 'admin') {
                abort(403, 'Bạn không có quyền xóa hình ảnh.');
            }
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
