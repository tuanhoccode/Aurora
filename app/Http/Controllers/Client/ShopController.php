<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Support\Facades\Cache;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::select('id', 'name', 'slug', 'price', 'sale_price', 'thumbnail', 'stock', 'type', 'brand_id', 'is_active', 'created_at')
            ->with(['brand:id,name', 'categories:id,name,icon'])
            ->where('is_active', 1);

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

        // Sắp xếp
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'views':
                $query->orderBy('views', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        // Phân trang
        $perPage = $request->get('per_page', 12);
        $products = $query->paginate($perPage);

        // Lấy categories và brands cho filter
        $categories = Category::select('id', 'name', 'icon', 'is_active')
            ->where('is_active', 1)
            ->get();
        
        $brands = Brand::select('id', 'name', 'is_active', 'is_visible')
            ->where('is_active', 1)
            ->where('is_visible', 1)
            ->get();

        return view('client.list-product', compact('products', 'categories', 'brands'));
    }
} 