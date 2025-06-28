<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Models\Cart;

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
    }
}
