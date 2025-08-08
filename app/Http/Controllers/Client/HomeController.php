<?php


namespace App\Http\Controllers\Client;


use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Review;
use App\Models\Banner;
use Carbon\Carbon;


class HomeController extends Controller
{
    public function index()
    {
        return $this->shop();
    }

    public function shop()
    {
        $products = Product::select('id', 'name', 'slug', 'price', 'sale_price', 'thumbnail', 'stock', 'type', 'brand_id', 'is_active')
            ->with([
                'brand:id,name',
                'reviews' => function($q) {
                    $q->select('id', 'product_id', 'rating', 'is_active')
                      ->where('is_active', 1);
                },
                'variants' => function($q) {
                    $q->select('id', 'product_id', 'regular_price', 'sale_price')
                      ->where('stock', '>', 0)
                      ->inRandomOrder()
                      ->limit(1);
                }
            ])
            ->where(function($q) {
                $q->where('stock', '>', 0)
                  ->orWhere(function($q2) {
                      $q2->where('type', 'variant')
                         ->whereHas('variants', function($q3) {
                             $q3->select('id', 'product_id', 'stock')
                                ->where('stock', '>', 0);
                         });
                  });
            })
            ->where('is_active', 1)
            ->latest()
            ->take(8)
            ->get();

        // Xử lý giá cho sản phẩm có biến thể
        $products->each(function ($product) {
            if ($product->type === 'variant' && $product->variants->isNotEmpty()) {
                $randomVariant = $product->variants->first();
                $product->display_price = $randomVariant->sale_price ?: $randomVariant->regular_price;
                $product->display_original_price = $randomVariant->regular_price;
            } else {
                $product->display_price = $product->sale_price ?: $product->price;
                $product->display_original_price = $product->price;
            }
        });

        // Tối ưu query categories - chỉ lấy 4 sản phẩm đầu tiên cho mỗi category
        $categories = Category::select('id', 'name', 'icon', 'is_active')
            ->with(['products' => function ($q) {
                $q->select('id', 'name', 'slug', 'price', 'sale_price', 'thumbnail', 'category_id', 'is_active')
                  ->where('is_active', 1)
                  ->latest()
                  ->take(4);
            }])
            ->where('is_active', 1)
            ->get();

        // Lấy banner từ database - chỉ lấy những banner có trạng thái active
        $banners = Banner::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Debug: Log số lượng banner được lấy
        if (config('app.debug')) {
            \Log::info('Banners loaded: ' . $banners->count());
            foreach ($banners as $banner) {
                \Log::info("Banner ID: {$banner->id}, Title: {$banner->title}, Active: " . ($banner->is_active ? 'Yes' : 'No'));
            }
        }

        // Tối ưu query reviews
        $topReviews = Review::select('id', 'user_id', 'product_id', 'rating', 'review_text', 'created_at', 'is_active')
            ->with(['user:id,fullname,avatar'])
            ->where('is_active', 1)
            ->where('rating', 5)
            ->latest()
            ->take(8)
            ->get();

        // Tối ưu query featured products
        $featuredThisWeek = Product::select('id', 'name', 'slug', 'price', 'sale_price', 'thumbnail', 'views', 'is_active', 'type')
            ->with(['variants' => function($q) {
                $q->select('id', 'product_id', 'regular_price', 'sale_price')
                  ->where('stock', '>', 0)
                  ->inRandomOrder()
                  ->limit(1);
            }])
            ->where('is_active', 1)
            ->where('created_at', '>=', Carbon::now()->subWeek())
            ->orderBy('views', 'desc')
            ->take(4)
            ->get();

        // Xử lý giá cho featured products có biến thể
        $featuredThisWeek->each(function ($product) {
            if ($product->type === 'variant' && $product->variants->isNotEmpty()) {
                $randomVariant = $product->variants->first();
                $product->display_price = $randomVariant->sale_price ?: $randomVariant->regular_price;
                $product->display_original_price = $randomVariant->regular_price;
            } else {
                $product->display_price = $product->sale_price ?: $product->price;
                $product->display_original_price = $product->price;
            }
        });

        return view('client.home', compact('products', 'categories', 'topReviews', 'featuredThisWeek', 'banners'));
    }
}



