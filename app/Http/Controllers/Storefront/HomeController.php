<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featuredProducts = Product::active()
            ->featured()
            ->with(['category', 'brand'])
            ->latest()
            ->take(8)
            ->get();

        $latestProducts = Product::active()
            ->with(['category', 'brand'])
            ->latest()
            ->take(8)
            ->get();

        $onSaleProducts = Product::active()
            ->whereNotNull('sale_price')
            ->whereColumn('sale_price', '<', 'price')
            ->with(['category', 'brand'])
            ->take(4)
            ->get();

        $popularBrands = Brand::where('is_active', true)->take(8)->get();

        return view('storefront.home', compact(
            'featuredProducts',
            'latestProducts',
            'onSaleProducts',
            'popularBrands'
        ));
    }
}
