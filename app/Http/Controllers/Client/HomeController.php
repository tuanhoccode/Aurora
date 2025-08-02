<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Review;
use App\Models\Banner;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function shop()
    {
        // Tối ưu query sản phẩm - chỉ lấy các trường cần thiết
        $products = Product::select('id', 'name', 'slug', 'price', 'sale_price', 'thumbnail', 'stock', 'type', 'brand_id', 'is_active')
            ->with([
                'brand:id,name',
                'reviews' => function($q) {
                    $q->select('id', 'product_id', 'rating', 'is_active')
                      ->where('is_active', 1);
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

        // Tối ưu query reviews
        $topReviews = Review::select('id', 'user_id', 'product_id', 'rating', 'review_text', 'created_at', 'is_active')
            ->with(['user:id,fullname,avatar'])
            ->where('is_active', 1)
            ->where('rating', 5)
            ->latest()
            ->take(8)
            ->get();

        // Tối ưu query featured products
        $featuredThisWeek = Product::select('id', 'name', 'slug', 'price', 'sale_price', 'thumbnail', 'views', 'is_active')
            ->where('is_active', 1)
            ->where('created_at', '>=', Carbon::now()->subWeek())
            ->orderBy('views', 'desc')
            ->take(4)
            ->get();

        // Lấy banner cho slider chính
        $mainSliders = Banner::where('is_active', 1)
            ->where('position', 'slider')
            ->orderBy('sort_order')
            ->get();

        // Lấy banner cho banner area
        $bannerArea = Banner::where('is_active', 1)
            ->where('position', 'banner')
            ->orderBy('sort_order')
            ->get();

        return view('client.home', compact('products', 'categories', 'topReviews', 'featuredThisWeek', 'mainSliders', 'bannerArea'));
    }
}
