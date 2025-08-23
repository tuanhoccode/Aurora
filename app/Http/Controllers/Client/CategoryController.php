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

        // Lấy danh sách thuộc tính dùng làm biến thể
        $variantAttributes = \App\Models\Attribute::with(['attributeValues' => function($q) {
            $q->select('id', 'attribute_id', 'value', 'color_code', 'is_active')
              ->where('is_active', true);
        }])
        ->where('is_variant', true)
        ->where('is_active', true)
        ->get();

        // Tìm kiếm tên sản phẩm
        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        // Lọc theo nhiều khoảng giá (checkbox)
        if ($request->filled('price_ranges')) {
            $query->where(function ($q) use ($request) {
                $hasValidRange = false;
                foreach ((array)$request->price_ranges as $range) {
                    if (preg_match('/^(\d+)-(\d+)$/', $range, $matches)) {
                        $min = (int)$matches[1];
                        $max = (int)$matches[2];
                        $q->orWhereBetween('price', [$min, $max]);
                        $hasValidRange = true;
                    }
                }
                
                if (!$hasValidRange) {
                    $q->whereRaw('1=0');
                }
            });
        }

        // Lọc theo biến thể nếu có
        if ($request->filled('variant_attributes')) {
            foreach ($request->variant_attributes as $attributeId => $valueIds) {
                if (!empty($valueIds)) {
                    $query->whereHas('variants', function($q) use ($valueIds) {
                        $q->whereHas('attributeValues', function($q) use ($valueIds) {
                            $q->whereIn('attribute_value_id', (array)$valueIds);
                        });
                    });
                }
            }
        }

        // Lọc theo thương hiệu
        if ($request->filled('brands')) {
            $brandIds = collect($request->input('brands', []))->filter(function($value) {
                return is_numeric($value) && $value > 0;
            })->unique()->values()->all();
            
            if (!empty($brandIds)) {
                $query->whereIn('brand_id', $brandIds);
            }
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

        $products = $query->with(['brand', 'categories'])->paginate(12);
        $brands = Brand::where('is_active', 1)->get();
        $statuses = [
            'active' => 'Đang bán',
            'sale' => 'Đang khuyến mãi',
        ];

        return view('client.categories.show', compact('category', 'products', 'brands', 'variantAttributes', 'statuses'));
    }
}