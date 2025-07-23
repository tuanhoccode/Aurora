<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('variants')->latest()->take(6)->get();
        return view('client.product-list', compact('products'));
    }

    public function show($slug)
    {
        $product = Product::with([
            'variants.images',
            'images',
            'reviews',
            'categories',
            'variants.attributeValues.attribute',
        ])->where('slug', $slug)->firstOrFail();

        $productVariants = $product->variants;

        // Map các biến thể và lấy chất liệu (attribute_id = 3)
        $variantsWithImages = $productVariants->map(function ($variant) {
            $material = $variant->attributeValues
                ->where('attribute_id', 3) // 3 là ID của "Chất liệu"
                ->pluck('value')
                ->first();

            return [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'stock' => $variant->stock,
                'regular_price' => $variant->regular_price,
                'sale_price' => $variant->sale_price,
                'img' => $variant->img,
                'material' => $material, //  Gán chất liệu lấy từ attribute value
                'images' => $variant->images->map(function ($image) {
                    return ['url' => $image->url];
                }),
            ];
        });

        $averageRating = $product->reviews()->where('is_active', 1)->where('rating', '>', 0)->avg('rating');
        $reviewCount = $product->reviews()->where('is_active', 1)->where('rating', '>', 0)->count();

        $relatedProducts = Product::where('id', '!=', $product->id)
            ->where('is_active', 1)
            ->inRandomOrder()
            ->take(5)
            ->get();

        return view('client.product-detail', [
            'product' => $product,
            'productVariants' => $productVariants,
            'averageRating' => $averageRating,
            'reviewCount' => $reviewCount,
            'relatedProducts' => $relatedProducts,
            'variantsWithImages' => $variantsWithImages,
            'variants' => $productVariants,
        ]);
    }

    public function quickView($id)
    {
        $product = Product::findOrFail($id);
        return view('client.partials.quickview', compact('product'))->render();
    }
}
