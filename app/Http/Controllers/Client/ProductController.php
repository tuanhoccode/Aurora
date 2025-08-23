<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
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
            'reviews' => fn($q) => $q->where('is_active', 1)->whereNull('review_id')->where('rating', '>=', 1),
            'reviews.user',
            'reviews.orderItem',
            'reviews.images',
            'reviews.replies.user',
            'categories',
            'variants.attributeValues.attribute',
        ])->where('slug', $slug)->firstOrFail();

        //Tăng lượt xem lên 1
        $viewed = session()->get('viewed_products', []);

        if (!in_array($product->id, $viewed)) {
            $product->increment('views');
            session()->push('viewed_products', $product->id);
        }

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
                'material' => $material,
                'images' => $variant->images->map(function ($image) {
                    return ['url' => $image->url];
                }),
            ];
        });

        //Lấy tất cả ảnh mặc định và loại trùng
        $allImages = [];

        // 1. Ảnh phụ của sản phẩm chính
        foreach ($product->images as $image) {
            $allImages[] = $image->url;
        }

        // 2. Ảnh chính của các biến thể (nếu có)
        foreach ($productVariants as $variant) {
            if ($variant->img) {
                $allImages[] = $variant->img;
            }

            // 3. Ảnh phụ của biến thể
            foreach ($variant->images as $image) {
                $allImages[] = $image->url;
            }
        }

        // 4. Loại trùng và chuẩn hóa về dạng ['url' => ...]
        $defaultImages = collect(array_unique($allImages))
            ->map(fn($url) => ['url' => $url])
            ->values();

        $averageRating = $product->reviews()->where('is_active', 1)->where('rating', '>', 0)->avg('rating');
        $reviewCount = $product->reviews()->where('is_active', 1)->where('rating', '>', 0)->count();

        $relatedProducts = Product::where('brand_id', $product->brand_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', 1)
            ->inRandomOrder()
            ->take(10)
            ->get();


        // Lấy review kèm biến thể từ order_item
        $reviews = $product->reviews()->where('is_active', 1)->whereNull('review_id')
            ->where('rating', '>=', 1)
            ->with(['user', 'orderItem', 'images', 'replies.user'])
            ->latest()->paginate(6);
        $orderIds = $reviews->pluck('order_id')->unique();
        $orderItems = OrderItem::WhereIn('order_id', $orderIds)
            ->where('product_id', $product->id)->get();

        foreach ($reviews as $review) {
            $review->orderItem = $orderItems->firstWhere(function ($item) use ($review) {
                return $item->order_id == $review->order_id && $item->product_id == $review->product_id;
            });
        }
        //Lấy biến thể đã mua khi user đăng nhập và đã mua
        $orderItem = null;
        if (Auth::check()) {
            $orderItem = OrderItem::where('product_id', $product->id)
                ->whereHas('order', function ($q) {
                    $q->where('user_id', Auth::id())
                        ->where('is_paid', 1);
                })->latest()->first();
        }

        return view('client.product-detail', [
            'product' => $product,
            'productVariants' => $productVariants,
            'averageRating' => $averageRating,
            'reviewCount' => $reviewCount,
            'relatedProducts' => $relatedProducts,
            'variantsWithImages' => $variantsWithImages,
            'variants' => $productVariants,
            'defaultImages' => $defaultImages,
            'orderItem' => $orderItem, //Truyền sang view
            'reviews' => $reviews, //truyền reviews vào view
        ]);
    }

    public function quickView($id)
    {
        $product = Product::findOrFail($id);
        return view('client.partials.quickview', compact('product'))->render();
    }
}
