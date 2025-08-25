<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Attribute;
use App\Models\AttributeValue;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->with([
            'brand',
            'categories',
            'variants' => function($q) {
                $q->select('id', 'product_id', 'regular_price', 'sale_price', 'sale_starts_at', 'sale_ends_at', 'stock');
            },
            'reviews' => function($q) {
                $q->select('id', 'product_id', 'rating', 'is_active', 'review_id')
                  ->where('is_active', 1)
                  ->whereNull('review_id')
                  ->where('rating', '>', 0);
            }
        ]);

        // Lọc theo trạng thái
        if ($request->boolean('is_sale')) {
            $query->where(function($q) {
                // Sản phẩm thường đang giảm giá
                $q->where('is_sale', true);

                // Hoặc sản phẩm variant có ít nhất một biến thể đang giảm giá
                $q->orWhere(function($subQuery) {
                    $now = now();
                    $subQuery->where('type', 'variant')
                        ->whereHas('variants', function($variantQuery) use ($now) {
                            $variantQuery->whereNotNull('sale_price')
                                ->where('sale_price', '>', 0)
                                ->where('sale_price', '<', 'regular_price')
                                ->where(function($timeQuery) use ($now) {
                                    $timeQuery->whereNull('sale_starts_at')
                                        ->orWhere('sale_starts_at', '<=', $now);
                                })
                                ->where(function($timeQuery) use ($now) {
                                    $timeQuery->whereNull('sale_ends_at')
                                        ->orWhere('sale_ends_at', '>=', $now);
                                });
                        });
                });
            });
        }
        if ($request->boolean('in_stock')) {
            $query->where(function($q) {
                // Sản phẩm thường còn hàng
                $q->where('stock', '>', 0);

                // Hoặc sản phẩm variant có ít nhất một biến thể còn hàng
                $q->orWhere(function($subQuery) {
                    $subQuery->where('type', 'variant')
                        ->whereHas('variants', function($variantQuery) {
                            $variantQuery->where('stock', '>', 0);
                        });
                });
            });
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
                            // Lọc sản phẩm thường theo giá
                            $q->orWhereBetween('price', [$min, $max]);

                            // Lọc sản phẩm variant theo giá của variants
                            $q->orWhere(function($subQuery) use ($min, $max) {
                                $subQuery->where('type', 'variant')
                                    ->whereHas('variants', function($variantQuery) use ($min, $max) {
                                        $variantQuery->where(function($vq) use ($min, $max) {
                                            // Kiểm tra regular_price
                                            $vq->whereBetween('regular_price', [$min, $max]);

                                            // Hoặc kiểm tra sale_price nếu đang trong thời gian khuyến mãi
                                            $vq->orWhere(function($saleQuery) use ($min, $max) {
                                                $now = now();
                                                $saleQuery->whereNotNull('sale_price')
                                                    ->where('sale_price', '>', 0)
                                                    ->where(function($timeQuery) use ($now) {
                                                        $timeQuery->whereNull('sale_starts_at')
                                                            ->orWhere('sale_starts_at', '<=', $now);
                                                    })
                                                    ->where(function($timeQuery) use ($now) {
                                                        $timeQuery->whereNull('sale_ends_at')
                                                            ->orWhere('sale_ends_at', '>=', $now);
                                                    })
                                                    ->whereBetween('sale_price', [$min, $max]);
                                            });
                                        });
                                    });
                            });

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

        // Lấy danh sách thuộc tính dùng làm biến thể
        $variantAttributes = Attribute::with(['attributeValues' => function($query) {
            $query->select('id', 'attribute_id', 'value', 'color_code', 'is_active')
                  ->where('is_active', true);
        }])
        ->where('is_variant', true)
        ->where('is_active', true)
        ->get();

        // Lấy danh sách ID thuộc tính biến thể
        $variantAttributeIds = $variantAttributes->pluck('id')->toArray();

        // Áp dụng bộ lọc biến thể nếu có
        if ($request->filled('variant_attributes')) {
            $variantFilters = $request->input('variant_attributes');

            // Lọc các thuộc tính có giá trị được chọn
            $activeAttributes = array_filter($variantFilters, function($value) {
                return !empty($value);
            });

            if (!empty($activeAttributes)) {
                // Với mỗi thuộc tính được chọn, đảm bảo sản phẩm có ít nhất một biến thể thỏa mãn
                foreach ($activeAttributes as $attributeId => $valueIds) {
                    if (empty($valueIds)) continue;

                    $query->whereHas('variants', function($q) use ($attributeId, $valueIds) {
                        $q->whereHas('attributeValues', function($subQuery) use ($attributeId, $valueIds) {
                            $subQuery->where('attribute_id', $attributeId)
                                    ->whereIn('attribute_value_id', (array)$valueIds);
                        });
                    });
                }

                // Đảm bảo tất cả các thuộc tính được chọn đều được thỏa mãn bởi cùng một biến thể
                $query->whereHas('variants', function($q) use ($activeAttributes) {
                    $q->where(function($subQuery) use ($activeAttributes) {
                        $subQuery->whereHas('attributeValues', function($q) use ($activeAttributes) {
                            $first = true;
                            foreach ($activeAttributes as $attributeId => $valueIds) {
                                if (empty($valueIds)) continue;

                                if ($first) {
                                    $q->where(function($q) use ($attributeId, $valueIds) {
                                        $q->where('attribute_id', $attributeId)
                                          ->whereIn('attribute_value_id', (array)$valueIds);
                                    });
                                    $first = false;
                                } else {
                                    $q->orWhere(function($q) use ($attributeId, $valueIds) {
                                        $q->where('attribute_id', $attributeId)
                                          ->whereIn('attribute_value_id', (array)$valueIds);
                                    });
                                }
                            }
                        }, '>=', count($activeAttributes));
                    });
                });
            }
        }

        $products = $query->get();
        $categories = Category::all();
        $brands = Brand::where('is_active', 1)->where('is_visible', 1)->get();

        return view('client.list-product', compact(
            'products',
            'categories',
            'brands',
            'variantAttributes'
        ));
    }
}
