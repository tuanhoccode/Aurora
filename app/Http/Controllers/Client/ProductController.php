<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $variantsWithImages = $productVariants->map(function ($variant) {
            return [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'stock' => $variant->stock,
                'regular_price' => $variant->regular_price,
                'sale_price' => $variant->sale_price,
                'img' => $variant->img,
                'images' => $variant->images->map(function ($image) {
                    return ['url' => $image->url];
                }),
            ];
        });

        $averageRating = $product->reviews()->where('is_active', 1)->avg('rating');
        $reviewCount = $product->reviews()->where('is_active', 1)->count();

        $relatedProducts = Product::where('id', '!=', $product->id)
            ->where('is_active', 1)
            ->inRandomOrder()
            ->take(5)
            ->get();

        // Lấy danh sách sản phẩm/biến thể đã có trong giỏ hàng
        $cartProductIds = [];
        $cartVariantIds = [];
        if (Auth::check()) {
            $cart = \App\Models\Cart::with('items')->where('user_id', Auth::id())->where('status', 'pending')->first();
            if ($cart) {
                $cartProductIds = $cart->items->pluck('product_id')->toArray();
                $cartVariantIds = $cart->items->pluck('product_variant_id')->filter()->toArray();
            }
        }

        return view('client.product-detail', compact(
            'product',
            'productVariants',
            'averageRating',
            'reviewCount',
            'relatedProducts',
            'variantsWithImages',
            'cartProductIds',
            'cartVariantIds'
        ));
    }

    public function quickView($id)
    {
        $product = Product::findOrFail($id);
        return view('client.partials.quickview', compact('product'))->render();
    }
}
