<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\ViewErrorBag;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Models\Cart;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        // View Composer cho header
        View::composer('*', function ($view) {
            $view->with('headerCategories', Category::where('is_active', 1)->get());
            $view->with('latestCategories', Category::where('is_active', 1)->orderByDesc('id')->take(3)->get());
        });

        // View Composer cho mini-cart
        View::composer('client.shopping-cart.mini-cart', function ($view) {
            if (Auth::check()) {
                $userId = Auth::id();
                $sessionCart = session("cart_{$userId}", []);
                $miniCartItems = collect();
                $miniCartSubtotal = 0;
                
                foreach ($sessionCart as $itemData) {
                    $product = \App\Models\Product::find($itemData['product_id']);
                    if (!$product) continue;
                    
                    $variant = null;
                    if (isset($itemData['product_variant_id']) && $itemData['product_variant_id']) {
                        $variant = \App\Models\ProductVariant::with('attributeValues.attribute')->find($itemData['product_variant_id']);
                    }
                    
                    $currentPrice = $variant ? $variant->current_price : $product->current_price;
                    
                    $item = (object) [
                        'id' => $itemData['id'],
                        'product_id' => $product->id,
                        'product_variant_id' => $variant ? $variant->id : null,
                        'quantity' => $itemData['quantity'],
                        'product' => $product,
                        'productVariant' => $variant,
                        'price_at_time' => $currentPrice,
                    ];
                    
                    $miniCartItems->push($item);
                    $miniCartSubtotal += $currentPrice * $itemData['quantity'];
                }

                $view->with(compact('miniCartItems', 'miniCartSubtotal'));
            } else {
                $view->with([
                    'miniCartItems' => collect(),
                    'miniCartSubtotal' => 0
                ]);
            }
        });

        // View Composer cho header
        View::composer('client.layouts.partials.header', function ($view) {
            $view->with('categories', \App\Models\Category::where('is_active', 1)->get());
        });
        Paginator::useBootstrap();
    }
}
