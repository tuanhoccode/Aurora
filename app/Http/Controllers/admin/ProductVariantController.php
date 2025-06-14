<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductVariantController extends Controller
{
    public function create(Product $product)
    {
        // Lấy các thuộc tính và giá trị của chúng
        $attributes = Attribute::with(['values' => function($query) {
            $query->where('is_active', 1);
        }])->where('is_active', 1)
          ->whereIn('name', ['Size', 'Color', 'Màu sắc', 'Kích thước'])
          ->get();

        return view('admin.products.variants.create', compact('product', 'attributes'));
    }

    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'sku' => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
            'regular_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'attribute_values' => 'required|array|min:1',
            'attribute_values.*' => 'required|exists:attribute_values,id'
        ]);

        try {
            DB::beginTransaction();

            Log::info('Attempting to create variant with data:', [
                'product_id' => $product->id,
                'validated_data' => $validated
            ]);

            // Xử lý upload ảnh
            $imgPath = null;
            if ($request->hasFile('img')) {
                $img = $request->file('img');
                $imgPath = $img->store('products/variants', 'public');
            }

            // Xử lý SKU
            $baseSku = strtoupper(Str::random(6));
            $sku = $baseSku;
            $counter = 1;
            
            // Kiểm tra và tạo SKU duy nhất
            while (ProductVariant::where('sku', $sku)->exists()) {
                $sku = $baseSku . '-' . $counter;
                $counter++;
            }

            // Tạo variant
            $variant = $product->variants()->create([
                'sku' => $validated['sku'],
                'stock' => $validated['stock'],
                'regular_price' => $validated['regular_price'],
                'sale_price' => $validated['sale_price'] ?? 0,
                'img' => $imgPath
            ]);

            Log::info('Variant created:', ['variant_id' => $variant->id]);

            // Liên kết với các giá trị thuộc tính
            if (!empty($validated['attribute_values'])) {
                try {
                    // Xóa các liên kết cũ nếu có
                    DB::table('attribute_value_product')
                        ->where('product_id', $product->id)
                        ->delete();

                    // Thêm các liên kết mới
                    foreach ($validated['attribute_values'] as $value_id) {
                        DB::table('attribute_value_product')->insert([
                            'product_id' => $product->id,
                            'attribute_value_id' => $value_id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                    Log::info('Attribute values updated successfully');
                } catch (\Exception $e) {
                    Log::error('Error updating attribute values:', [
                        'error' => $e->getMessage()
                    ]);
                    throw $e;
                }
            }

            DB::commit();

            return redirect()->route('admin.products.edit', $product)
                ->with('success', 'Biến thể đã được tạo thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating variant:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withInput()
                ->with('error', 'Có lỗi xảy ra khi tạo biến thể: ' . $e->getMessage());
        }
    }

    public function edit(Product $product, ProductVariant $variant)
    {
        // Lấy các thuộc tính và giá trị của chúng
        $attributes = Attribute::with(['values' => function($query) {
            $query->where('is_active', 1);
        }])->where('is_active', 1)
          ->whereIn('name', ['Size', 'Color', 'Màu sắc', 'Kích thước'])
          ->get();

        // Lấy các giá trị thuộc tính đã chọn từ bảng attribute_value_product
        $selectedValues = DB::table('attribute_value_product')
            ->where('product_id', $product->id)
            ->pluck('attribute_value_id')
            ->toArray();

        // Load lại variant với thuộc tính
        $variant->load('attributeValues.attribute');

        return view('admin.products.variants.edit', compact('product', 'variant', 'attributes', 'selectedValues'));
    }

    public function update(Request $request, Product $product, ProductVariant $variant)
    {
        $validated = $request->validate([
            'sku' => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
            'regular_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'attribute_values' => 'required|array|min:1',
            'attribute_values.*' => 'required|exists:attribute_values,id'
        ]);

        try {
            DB::beginTransaction();

            Log::info('Attempting to update variant with data:', [
                'variant_id' => $variant->id,
                'validated_data' => $validated
            ]);

            // Xử lý upload ảnh
            $imgPath = $variant->img;
            if ($request->hasFile('img')) {
                $img = $request->file('img');
                $imgPath = $img->store('products/variants', 'public');
            }

            // Cập nhật variant
            $variant->update([
                'sku' => $validated['sku'],
                'stock' => $validated['stock'],
                'regular_price' => $validated['regular_price'],
                'sale_price' => $validated['sale_price'] ?? 0,
                'img' => $imgPath
            ]);

            Log::info('Variant updated');

            // Cập nhật liên kết với các giá trị thuộc tính
            if (!empty($validated['attribute_values'])) {
                try {
                    // Lấy danh sách các giá trị thuộc tính hiện tại
                    $currentValues = DB::table('attribute_value_product')
                        ->where('product_id', $product->id)
                        ->pluck('attribute_value_id')
                        ->toArray();

                    // Tạo danh sách các giá trị cần thêm và cần xóa
                    $newValues = array_diff($validated['attribute_values'], $currentValues);
                    $valuesToRemove = array_diff($currentValues, $validated['attribute_values']);

                    // Xóa các liên kết không cần thiết
                    if (!empty($valuesToRemove)) {
                        DB::table('attribute_value_product')
                            ->where('product_id', $product->id)
                            ->whereIn('attribute_value_id', $valuesToRemove)
                            ->delete();
                    }

                    // Thêm các liên kết mới
                    if (!empty($newValues)) {
                        foreach ($newValues as $value_id) {
                            DB::table('attribute_value_product')->insert([
                                'product_id' => $product->id,
                                'attribute_value_id' => $value_id,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                        }
                    }
                    Log::info('Attribute values updated successfully');
                } catch (\Exception $e) {
                    Log::error('Error updating attribute values:', [
                        'error' => $e->getMessage()
                    ]);
                    throw $e;
                }
            }

            DB::commit();

            return redirect()->route('admin.products.edit', $product)
                ->with('success', 'Biến thể đã được cập nhật thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating variant:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật biến thể: ' . $e->getMessage());
        }
    }

    public function destroy(Product $product, ProductVariant $variant)
    {
        try {
            DB::beginTransaction();

            Log::info('Attempting to delete variant:', ['variant_id' => $variant->id]);

            // Xóa các liên kết thuộc tính
            DB::table('attribute_value_product')
                ->where('product_id', $product->id)
                ->delete();

            Log::info('Attribute values deleted');

            // Xóa biến thể
            $variant->delete();

            Log::info('Variant deleted');

            DB::commit();

            return redirect()->route('admin.products.edit', $product)
                ->with('success', 'Biến thể đã được xóa thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting variant:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Có lỗi xảy ra khi xóa biến thể: ' . $e->getMessage());
        }
    }

    public function show(Product $product)
    {
        // Load lại variants với thuộc tính
        $product->load('variants.attributeValues.attribute');
        
        return view('admin.products.show', compact('product'));
    }
}