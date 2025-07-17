<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        // Lọc theo trạng thái
        if ($request->has('is_sale')) {
            $query->where('is_sale', true);
        }
        if ($request->has('in_stock')) {
            $query->where('stock', '>', 0);
        }

        // Lọc theo nhiều thương hiệu
        if ($request->filled('brand_ids')) {
            $query->whereIn('brand_id', (array)$request->brand_ids);
        }

        // Lọc theo nhiều khoảng giá
        if ($request->filled('price_ranges')) {
            $query->where(function($q) use ($request) {
                foreach ((array)$request->price_ranges as $range) {
                    [$min, $max] = explode('-', $range);
                    $q->orWhereBetween('price', [(int)$min, (int)$max]);
                }
            });
        }

        // Lọc theo nhiều danh mục (many-to-many)
        if ($request->filled('category_ids')) {
            $query->whereHas('categories', function($q) use ($request) {
                $q->whereIn('categories.id', (array)$request->category_ids);
            });
        }

        $products = $query->get();
        $categories = Category::all();
        $brands = Brand::where('is_active', 1)->where('is_visible', 1)->get();

        return view('client.list-product', compact('products', 'categories', 'brands'));
    }
} 