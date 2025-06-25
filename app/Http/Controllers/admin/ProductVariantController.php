<?php


namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


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
    public function store(Request $request, Product $product)
    {
        // Debug: Log raw request data
        Log::info('Raw request data:', [
            'all_data' => $request->all(),
            'variants_data' => $request->input('variants'),
        ]);


        $validated = $request->validate([
            'variants' => 'required|array|min:1',
            'variants.*.sku' => [
                'nullable',
                'string',
                'max:255',
                'distinct'
            ],
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.regular_price' => 'required|numeric|min:0',
            'variants.*.sale_price' => 'nullable|numeric|min:0',
            'variants.*.img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'variants.*.attribute_values' => 'required|array|min:1',
            'variants.*.attribute_values.*' => 'required|exists:attribute_values,id',
        ]);


        try {
            DB::beginTransaction();


            Log::info('Attempting to create variants for product:', [
                'product_id' => $product->id,
                'validated_data' => $validated,
            ]);


            // Check for duplicate attribute combinations
            $existingCombinations = $product->variants->map(function ($variant) {
                return $variant->attributeValues->pluck('id')->sort()->values()->toArray();
            })->toArray();


            foreach ($validated['variants'] as $index => $variantData) {
                // Debug: Log each variant data
                Log::info('Processing variant:', [
                    'index' => $index,
                    'variant_data' => $variantData,
                    'attribute_values' => $variantData['attribute_values'] ?? 'not set'
                ]);


                $attributeValueIds = array_values($variantData['attribute_values']);
                sort($attributeValueIds);


                // Skip if combination exists
                if (in_array($attributeValueIds, $existingCombinations)) {
                    throw new \Exception("Tổ hợp thuộc tính cho biến thể thứ " . ($index + 1) . " đã tồn tại.");
                }


                // Handle image upload
                $imgPath = null;
                if (isset($variantData['img']) && $variantData['img']) {
                    $imgPath = $variantData['img']->store('products/variants', 'public');
                }


                // Generate unique SKU if needed
                $sku = strtoupper(trim($variantData['sku']));
                if (empty($sku) || ProductVariant::where('sku', $sku)->exists()) {
                    $baseSku = $product->sku . '-VAR';
                    $counter = 1;
                    do {
                        $sku = $baseSku . '-' . $counter;
                        $counter++;
                    } while (ProductVariant::where('sku', $sku)->exists());
                }


                // Create variant
                $variant = $product->variants()->create([
                    'sku' => $sku,
                    'stock' => $variantData['stock'],
                    'regular_price' => $variantData['regular_price'],
                    'sale_price' => $variantData['sale_price'] ?? null,
                    'img' => $imgPath,
                ]);


                Log::info('Variant created:', ['variant_id' => $variant->id]);


                // Attach attribute values
                if (!empty($variantData['attribute_values'])) {
                    Log::info('Attaching attribute values:', [
                        'variant_id' => $variant->id,
                        'attribute_values' => $attributeValueIds
                    ]);
                   
                    foreach ($attributeValueIds as $valueId) {
                        DB::table('attribute_value_product_variant')->insert([
                            'product_variant_id' => $variant->id,
                            'attribute_value_id' => $valueId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                } else {
                    Log::warning('No attribute values found for variant:', ['variant_id' => $variant->id]);
                }
            }


            DB::commit();


            return redirect()->route('admin.products.edit', $product)
                ->with('success', 'Các biến thể đã được tạo thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating variants:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withInput()
                ->with('error', 'Có lỗi xảy ra khi tạo biến thể: ' . $e->getMessage());
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


        return view('admin.products.variants.edit', compact('product', 'variant', 'attributes', 'selectedValues'));
    }


    /**
     * Update a specific variant.
     */
    public function update(Request $request, Product $product, ProductVariant $variant)
    {
        $validated = $request->validate([
            'sku' => 'nullable|string|max:255',
            'stock' => 'required|integer|min:0',
            'regular_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'attribute_values' => 'required|array|min:1',
            'attribute_values.*' => 'required|exists:attribute_values,id',
        ]);


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
     * Delete a specific variant.
     */
    public function destroy(Product $product, ProductVariant $variant)
    {
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

