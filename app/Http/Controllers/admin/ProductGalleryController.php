<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductGalleryController extends Controller
{
    // Hiển thị danh sách ảnh
    public function all()
    {
        // Load thêm cả variant nếu có
        $images = ProductGallery::with(['product', 'variant'])->get();

        return view('admin.product_galleries.all', compact('images'));
    }

    // Form thêm ảnh
    public function createGeneral()
    {
        $products = Product::with('variants')->get();

        return view('admin.product_galleries.create_general', compact('products'));
    }

    // Form sửa ảnh
    public function edit($id)
    {
        $image = ProductGallery::findOrFail($id);
        $products = Product::with('variants')->get();

        return view('admin.product_galleries.edit', compact('image', 'products'));
    }

    // Xử lý lưu cập nhật ảnh
    public function update(Request $request, $id)
    {
        $image = ProductGallery::findOrFail($id);

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'image' => 'nullable|image|max:2048',
        ]);

        // Nếu upload ảnh mới, lưu và xóa ảnh cũ
        if ($request->hasFile('image')) {
            if ($image->url && Storage::disk('public')->exists($image->url)) {
                Storage::disk('public')->delete($image->url);
            }
            $path = $request->file('image')->store('product_galleries', 'public');
            $image->url = $path;
        }

        $image->product_id = $request->product_id;
        $image->product_variant_id = $request->product_variant_id ?: null;
        $image->save();

        return redirect()->route('admin.product-images.all')->with('success', 'Cập nhật ảnh thành công');
    }

    // Xử lý lưu ảnh
    public function storeGeneral(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'image' => 'required|image|max:2048',
            'product_variant_id' => 'nullable|exists:product_variants,id',
        ]);

        $path = $request->file('image')->store('product_galleries', 'public');

        ProductGallery::create([
            'product_id' => $request->product_id,
            'product_variant_id' => $request->product_variant_id ?: null,
            'url' => $path,
        ]);

        return redirect()->route('admin.product-images.all')->with('success', 'Đã thêm ảnh thành công');
    }

    // Xoá ảnh
    public function destroy($id)
    {
        $image = ProductGallery::findOrFail($id);

        if ($image->url && Storage::disk('public')->exists($image->url)) {
            Storage::disk('public')->delete($image->url);
        }

        $image->delete();

        return redirect()->route('admin.product-images.all')->with('success', 'Xóa ảnh thành công');
    }
}
