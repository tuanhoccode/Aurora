<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Review;

class HomeController extends Controller
{
    public function shop()
    {
        // Lấy 8 sản phẩm còn hàng mới nhất
        $products = Product::with(['brand', 'reviews' => function($q){
            $q->where('is_active', 1);
        }])
            ->where(function($q) {
                $q->where('stock', '>', 0)
                  ->orWhere(function($q2) {
                      $q2->where('type', 'variant')
                         ->whereHas('variants', function($q3) {
                             $q3->where('stock', '>', 0);
                         });
                  });
            })
            ->where('is_active', 1)
            ->latest()
            ->take(8)
            ->get();

        // Lấy các danh mục + mỗi danh mục 4 sản phẩm active mới nhất
        $categories = Category::with(['products' => function ($q) {
            $q->where('is_active', 1)
                ->latest()
                ->take(4);
        }])->where('is_active', 1)->get();

        
        //Lấy 8 đánh giá 5 sao mới nhất đã duyệt
        $topReviews = Review::with('user')
        ->where('is_active', 1)
        ->where('rating', 5)
        ->latest()->take(8)->get();
        
        return view('client.home', compact('products', 'categories', 'topReviews'));
    }
}
