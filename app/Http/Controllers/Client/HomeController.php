<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;

class HomeController extends Controller
{
public function shop()
{
    // Lấy 8 sản phẩm mới nhất
    $products = Product::with('brand')
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

    return view('client.home', compact('products', 'categories'));
}


}
