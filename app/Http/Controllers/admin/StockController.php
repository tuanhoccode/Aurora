<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $filter = $request->input('filter'); // 'product', 'variant', 'stock'
        $stockLevel = $request->input('stock_level'); // 'low', 'normal', 'out'
        $sort = $request->input('sort'); // 'name', 'stock', 'updated_at'
        $order = $request->input('order', 'asc');
        $perPage = 15;

        $products = collect();
        if (!$filter || $filter === 'product') {
            $products = Product::doesntHave('variants')
                ->when($search, fn($q) => $q->where('name', 'like', "%$search%"))
                ->get()
                ->filter(fn($p) => $this->stockLevelMatch($p->stock, $stockLevel))
                ->map(fn($p) => [
                    'name' => $p->name,
                    'variant_info' => null,
                    'stock' => $p->stock,
                    'type' => 'product',
                    'updated_at' => $p->updated_at,
                ]);
        }

        $variants = collect();
        if (!$filter || $filter === 'variant') {
            $variants = ProductVariant::with('product')
                ->when($search, fn($q) => $q->whereHas('product', fn($q) => $q->where('name', 'like', "%$search%")))
                ->get()
                ->filter(fn($v) => $this->stockLevelMatch($v->stock, $stockLevel))
                ->map(fn($v) => $v->product ? [
                    'name' => $v->product->name,
                    'variant_info' => $v->sku,
                    'stock' => $v->stock,
                    'type' => 'variant',
                    'updated_at' => $v->updated_at,
                ] : null)->filter();
        }

        $stocks = collect();
        if (!$filter || $filter === 'stock') {
            $stocks = Stock::with(['product', 'variant'])
                ->when($search, fn($q) => $q->whereHas('product', fn($q) => $q->where('name', 'like', "%$search%")))
                ->get()
                ->filter(fn($s) => $this->stockLevelMatch($s->stock, $stockLevel))
                ->map(fn($s) => $s->product ? [
                    'name' => $s->product->name,
                    'variant_info' => $s->variant?->sku ?? 'Không có',
                    'stock' => $s->stock,
                    'type' => 'stock',
                    'updated_at' => $s->updated_at,
                ] : null)->filter();
        }

        $allStocks = $products->concat($variants)->concat($stocks);

        // Sorting
        if ($sort === 'name') {
            $allStocks = $allStocks->sortBy('name', SORT_REGULAR, $order === 'desc');
        } elseif ($sort === 'stock') {
            $allStocks = $allStocks->sortBy('stock', SORT_REGULAR, $order === 'desc');
        } elseif ($sort === 'updated_at') {
            $allStocks = $allStocks->sortBy('updated_at', SORT_REGULAR, $order === 'desc');
        }

        // Pagination
        $page = $request->input('page', 1);
        $paginated = new LengthAwarePaginator(
            $allStocks->forPage($page, $perPage)->values(),
            $allStocks->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.stocks.index', [
            'allStocks' => $paginated,
            'search' => $search,
            'filter' => $filter,
            'sort' => $sort,
            'order' => $order,
            'stock_level' => $stockLevel,
        ]);
    }

    private function stockLevelMatch($stock, $level)
    {
        return match($level) {
            'low' => $stock > 0 && $stock <= 10,
            'normal' => $stock > 10,
            'out' => $stock <= 0,
            default => true,
        };
    }
}
