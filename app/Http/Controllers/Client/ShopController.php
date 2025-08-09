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
        $query = Product::query()->with(['brand', 'categories']);

        // Lọc theo trạng thái
        if ($request->boolean('is_sale')) {
            $query->where('is_sale', true);
        }
        if ($request->boolean('in_stock')) {
            $query->where('stock', '>', 0);
        }

        // Lọc theo nhiều thương hiệu
        if ($request->filled('brands')) {
            $brandIds = collect($request->input('brands', []))->filter(function($value) {
                return is_numeric($value) && $value > 0;
            })->unique()->values()->all();
            
            if (!empty($brandIds)) {
                $query->whereIn('brand_id', $brandIds);
            }
        }

        // Lọc theo nhiều khoảng giá
        if ($request->filled('price_ranges')) {
            $priceRanges = is_array($request->price_ranges) 
                ? $request->price_ranges 
                : explode(',', $request->price_ranges);
            
            $query->where(function($q) use ($priceRanges) {
                $hasValidRange = false;
                
                foreach ($priceRanges as $range) {
                    if (preg_match('/^(\d+)-(\d+)$/', $range, $matches)) {
                        $min = (int)$matches[1];
                        $max = (int)$matches[2];
                        if ($min >= 0 && $max > $min) {
                            $q->orWhereBetween('price', [$min, $max]);
                            $hasValidRange = true;
                        }
                    }
                }
                
                // Nếu không có khoảng giá hợp lệ nào, trả về không có sản phẩm
                if (!$hasValidRange) {
                    $q->whereRaw('1=0');
                }
            });
        }

        // Lọc theo nhiều danh mục (many-to-many) - hiển thị sản phẩm thuộc bất kỳ danh mục nào được chọn
        if ($request->has('category_ids')) {
            $categoryIds = collect($request->input('category_ids', []))->filter(function($value) {
                return is_numeric($value) && $value > 0;
            })->unique()->values()->all();
            
            if (!empty($categoryIds)) {
                $query->whereHas('categories', function($q) use ($categoryIds) {
                    $q->whereIn('categories.id', $categoryIds);
                });
            }
        }

        $products = $query->get();
        $categories = Category::all();
        $brands = Brand::where('is_active', 1)->where('is_visible', 1)->get();

        return view('client.list-product', compact('products', 'categories', 'brands'));
    }
} 