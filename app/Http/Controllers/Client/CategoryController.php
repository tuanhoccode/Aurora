<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Brand;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Hiển thị tất cả danh mục
    public function index()
    {
        $categories = Category::where('is_active', 1)->get();
        return view('client.categories.index', compact('categories'));
    }

    // Hiển thị sản phẩm theo danh mục
    public function show(Request $request, $id)
{
    $category = Category::where('id', $id)->where('is_active', 1)->firstOrFail();
    $query = $category->products()->where('is_active', 1);

    // Tìm kiếm tên sản phẩm
    if ($request->filled('q')) {
        $query->where('name', 'like', '%' . $request->q . '%');
    }

    //Lọc theo nhiều khoảng giá (checkbox)
    if ($request->filled('price_ranges')) {
        $query->where(function ($q) use ($request) {
            foreach ($request->price_ranges as $range) {
                [$min, $max] = explode('-', $range);
                $q->orWhereBetween('price', [(int)$min, (int)$max]);
            }
        });
    }

    // Lọc theo thương hiệu
    if ($request->filled('brand_ids')) {
        $query->whereIn('brand_id', $request->brand_ids);
    }

    // Lọc trạng thái
    if ($request->filled('is_sale')) {
        $query->where('is_sale', 1);
    }

    if ($request->filled('in_stock')) {
        $query->where('stock', '>', 0);
    }

    // Sắp xếp
    if ($request->filled('sort')) {
        switch ($request->sort) {
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
            default:
                $query->latest();
        }
    } else {
        $query->latest();
    }

    $products = $query->get();
    $brands = Brand::where('is_active', 1)->where('is_visible', 1)->get();
    $statuses = [
        'active' => 'Đang bán',
        'sale' => 'Đang khuyến mãi',
    ];

    return view('client.categories.show', compact('category', 'products', 'brands', 'statuses'));
}

}