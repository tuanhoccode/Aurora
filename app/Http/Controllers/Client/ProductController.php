<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::select('id', 'name', 'slug', 'price', 'sale_price', 'thumbnail', 'is_active')
            ->with('variants:id,product_id,sku,stock,regular_price,sale_price')
            ->where('is_active', 1)
            ->latest()
            ->take(6)
            ->get();
        return view('client.product-list', compact('products'));
    }

    public function show($slug)
    {
        $product = Product::select('id', 'name', 'slug', 'description', 'price', 'sale_price', 'stock', 'type', 'views', 'is_active', 'thumbnail')
            ->with([
                'variants:id,product_id,sku,stock,regular_price,sale_price,img',
                'variants.images:id,product_variant_id,url',
                'images:id,product_id,url',
                'reviews:id,product_id,user_id,rating,review_text,created_at,is_active',
                'categories:id,name,icon',
                'variants.attributeValues:id,attribute_id,value',
                'variants.attributeValues.attribute:id,name'
            ])
            ->where('slug', $slug)
            ->where('is_active', 1)
            ->firstOrFail();

        // Tăng lượt xem bất đồng bộ để không ảnh hưởng đến hiệu suất
        dispatch(function() use ($product) {
            $product->increment('views');
        })->afterResponse();

        $productVariants = $product->variants;

        // Tối ưu hóa việc xử lý variants
        $variantsWithImages = $productVariants->map(function ($variant) {
            $material = $variant->attributeValues
                ->where('attribute_id', 3)
                ->pluck('value')
                ->first();

            return [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'stock' => $variant->stock,
                'regular_price' => $variant->regular_price,
                'sale_price' => $variant->sale_price,
                'img' => $variant->img,
                'material' => $material,
                'images' => $variant->images->map(function ($image) {
                    return ['url' => $image->url];
                }),
            ];
        });

        // Tối ưu hóa việc xử lý ảnh
        $allImages = collect();
        
        // Nếu là sản phẩm variant, chỉ lấy ảnh từ variants
        if ($product->type === 'variant' && $productVariants->count() > 0) {
            foreach ($productVariants as $variant) {
                if ($variant->img) {
                    $allImages->push($variant->img);
                }
                $allImages = $allImages->merge($variant->images->pluck('url'));
            }
        } else {
            // Nếu là sản phẩm đơn giản, lấy ảnh từ sản phẩm chính
            $allImages = $allImages->merge($product->images->pluck('url'));
        }

        $defaultImages = $allImages->unique()
            ->map(fn($url) => ['url' => $url])
            ->values();

        // Xác định ảnh chính cho sản phẩm
        $mainImage = null;
        if ($product->thumbnail) {
            $mainImage = $product->thumbnail;
        } elseif ($product->type === 'variant' && $productVariants->count() > 0) {
            // Nếu là variant và có ảnh từ variant đầu tiên
            $firstVariant = $productVariants->first();
            if ($firstVariant && $firstVariant->img) {
                $mainImage = $firstVariant->img;
            }
        } elseif ($product->images->count() > 0) {
            // Nếu có ảnh từ gallery
            $mainImage = $product->images->first()->url;
        }

        // Tối ưu query đánh giá
        $averageRating = $product->reviews()
            ->where('is_active', 1)
            ->where('rating', '>', 0)
            ->avg('rating');
        
        $reviewCount = $product->reviews()
            ->where('is_active', 1)
            ->where('rating', '>', 0)
            ->count();

        // Tối ưu query sản phẩm liên quan
        $relatedProducts = Product::select('id', 'name', 'slug', 'price', 'sale_price', 'thumbnail', 'is_active')
            ->where('id', '!=', $product->id)
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
            'defaultImages' => $defaultImages,
            'mainImage' => $mainImage,
        ]);
    }

    public function quickView($id)
    {
        $product = Product::select('id', 'name', 'slug', 'price', 'sale_price', 'thumbnail', 'description', 'stock', 'is_active')
            ->with('variants:id,product_id,sku,stock,regular_price,sale_price')
            ->where('is_active', 1)
            ->findOrFail($id);
        return view('client.partials.quickview', compact('product'))->render();
    }
}
