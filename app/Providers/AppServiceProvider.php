<?php

namespace App\Providers;

use App\Models\Category;
use App\Services\CartService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        View::composer(['layouts.storefront', 'storefront.*'], function ($view): void {
            if (Schema::hasTable('categories')) {
                $headerCategories = Category::whereNull('parent_id')
                    ->where('is_active', true)
                    ->with(['children' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])
                    ->orderBy('sort_order')
                    ->get();
                $view->with('headerCategories', $headerCategories);
            }

            if (Schema::hasTable('carts') && Schema::hasTable('cart_items')) {
                try {
                    $cartService = app(CartService::class);
                    $cart = $cartService->getCart();
                    $view->with('cartCount', $cart->total_quantity);
                } catch (\Throwable $e) {
                    $view->with('cartCount', 0);
                }
            } else {
                $view->with('cartCount', 0);
            }
        });
    }
}
