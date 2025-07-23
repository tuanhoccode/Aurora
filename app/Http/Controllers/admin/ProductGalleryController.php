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
        $images = ProductGallery::with(['product', 'variant'])->get();
        return view('admin.product_galleries.all', compact('images'));
    }

    // Hiển thị form thêm ảnh
    public function createGeneral()
    {
        $products = Product::with('variants')->get();
        return view('admin.product_galleries.create_general', compact('products'));
    }

    // Hiển thị form chỉnh sửa ảnh
    public function edit($id)
    {
        $image = ProductGallery::findOrFail($id);
        $products = Product::with('variants')->get();
        return view('admin.product_galleries.edit', compact('image', 'products'));
    }

    // Xử lý lưu ảnh mới
    public function storeGeneral(Request $request)
{
    $request->validate([
        'product_id' => 'required|exists:products,id',
        'image' => 'required|image|max:2048',
        'product_variant_id' => 'nullable|exists:product_variants,id',
    ]);

    if (!$request->hasFile('image')) {
        return back()->with('error', 'Không có ảnh nào được gửi lên.');
    }

    $imagePath = $request->file('image')->store('product-images', 'public');

    ProductGallery::create([
        'product_id' => $request->product_id,
        'product_variant_id' => $request->product_variant_id, // <- phải có
        'url' => $imagePath,
    ]);

    return redirect()->route('admin.product-images.all')->with('success', 'Đã thêm ảnh sản phẩm.');
}


    // Xử lý cập nhật ảnh
    public function update(Request $request, $id)
    {
        $image = ProductGallery::findOrFail($id);

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'image' => 'nullable|image|max:2048',
        ]);

        // Nếu có ảnh mới
        if ($request->hasFile('image')) {
            // Xóa ảnh cũ
            if ($image->url && Storage::disk('public')->exists($image->url)) {
                Storage::disk('public')->delete($image->url);
            }

            // Lưu ảnh mới
            $image->url = $request->file('image')->store('product_galleries', 'public');
        }

        $image->product_id = $request->product_id;
        $image->product_variant_id = $request->product_variant_id ?: null;
        $image->save();

        return redirect()->route('admin.product-images.all')->with('success', 'Cập nhật ảnh thành công');
    }

    // Xóa ảnh
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
