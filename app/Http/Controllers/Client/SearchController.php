<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('query');
        $sort = $request->input('sort', 'newest');
        $productsQuery = Product::query()
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%");
            })
            ->orWhereHas('brand', function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            });
        // Sắp xếp
        if ($sort === 'price_desc') {
            $productsQuery->orderByDesc('price');
        } elseif ($sort === 'price_asc') {
            $productsQuery->orderBy('price');
        } else {
            $productsQuery->orderByDesc('id'); // newest
        }
        $products = $productsQuery->get();
        $categories = Category::where('name', 'like', "%{$query}%")->get();
        return view('client.search', compact('products', 'categories', 'query'));
    }
}