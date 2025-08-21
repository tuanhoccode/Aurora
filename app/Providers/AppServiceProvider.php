<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\ViewErrorBag;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Models\Cart;
use App\Models\Comment;
use App\Models\Review;
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
                $cart = Cart::where('user_id', $userId)
                    ->where('status', 'pending')
                    ->with([
                        'items.product',
                        'items.productVariant.attributeValues.attribute',
                    ])
                    ->first();

                $miniCartItems = $cart ? $cart->items : collect();
                $miniCartSubtotal = $miniCartItems->sum(function($item) {
                    return $item->price_at_time * $item->quantity;
                });

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

        View::composer('admin.layouts.sidebar', function ($view){
            $pendingReviews= Review::where('is_active', 0)->count();
            $view->with('hasPendingFeedbacks',  $pendingReviews > 0);
        });
    }
}
