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
        if ($request->has('on_sale')) {
            $query->where('is_sale', true);
        }
        if ($request->has('in_stock')) {
            $query->where('stock', '>', 0);
        }

        // Lọc theo nhiều thương hiệu
        if ($request->filled('brands')) {
            $query->whereIn('brand_id', (array)$request->brands);
        }

        // Lọc theo nhiều khoảng giá
        $priceRanges = [
            ['min' => 0, 'max' => 200000],
            ['min' => 200000, 'max' => 500000],
            ['min' => 500000, 'max' => 800000],
            ['min' => 800000, 'max' => 1000000],
        ];
        if ($request->filled('prices')) {
            $query->where(function($q) use ($request, $priceRanges) {
                foreach ((array)$request->prices as $idx) {
                    if (isset($priceRanges[$idx])) {
                        $q->orWhere(function($sub) use ($priceRanges, $idx) {
                            $sub->where('price', '>=', $priceRanges[$idx]['min'])
                                ->where('price', '<=', $priceRanges[$idx]['max']);
                        });
                    }
                }
            });
        }

        // Lọc theo danh mục
        if ($request->filled('category')) {
            $query->whereHas('categories', function($q) use ($request) {
                $q->where('categories.id', $request->category);
            });
        }

        $products = $query->get();
        $categories = Category::all();
        $brands = Brand::all();

        return view('client.list-product', compact('products', 'categories', 'brands'));
    }
} 