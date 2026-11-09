<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::active()->with(['category', 'brand']);

        // Search by keyword (name, sku, description, unaccented slug)
        if ($request->filled('keyword')) {
            $keyword = trim($request->query('keyword'));
            $slugKeyword = Str::slug($keyword);
            $query->where(function ($q) use ($keyword, $slugKeyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('sku', 'like', "%{$keyword}%")
                    ->orWhere('short_description', 'like', "%{$keyword}%");

                if (! empty($slugKeyword)) {
                    $q->orWhere('slug', 'like', "%{$slugKeyword}%");
                }
            });
        }

        // Filter by category slug (support parent and child categories)
        if ($request->filled('category')) {
            $categorySlug = $request->query('category');
            $category = Category::where('slug', $categorySlug)->first();

            if ($category) {
                $categoryIds = [$category->id];
                if ($category->children()->exists()) {
                    $categoryIds = array_merge($categoryIds, $category->children()->pluck('id')->toArray());
                }
                $query->whereIn('category_id', $categoryIds);
            }
        }

        // Filter by brand slug
        if ($request->filled('brand')) {
            $brand = Brand::where('slug', $request->query('brand'))->first();
            if ($brand) {
                $query->where('brand_id', $brand->id);
            }
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $minPrice = (float) $request->query('min_price');
            $query->where(function ($q) use ($minPrice) {
                $q->where(function ($sub) use ($minPrice) {
                    $sub->whereNotNull('sale_price')->where('sale_price', '>=', $minPrice);
                })->orWhere(function ($sub) use ($minPrice) {
                    $sub->whereNull('sale_price')->where('price', '>=', $minPrice);
                });
            });
        }

        if ($request->filled('max_price')) {
            $maxPrice = (float) $request->query('max_price');
            $query->where(function ($q) use ($maxPrice) {
                $q->where(function ($sub) use ($maxPrice) {
                    $sub->whereNotNull('sale_price')->where('sale_price', '<=', $maxPrice);
                })->orWhere(function ($sub) use ($maxPrice) {
                    $sub->whereNull('sale_price')->where('price', '<=', $maxPrice);
                });
            });
        }

        // Sorting
        $sort = $request->query('sort', 'latest');
        switch ($sort) {
            case 'price_asc':
                $query->orderByRaw('COALESCE(sale_price, price) + 0 ASC');
                break;
            case 'price_desc':
                $query->orderByRaw('COALESCE(sale_price, price) + 0 DESC');
                break;
            case 'name_asc':
                $query->orderBy('name', 'ASC');
                break;
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12)->withQueryString();

        $categories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->with('children')
            ->orderBy('sort_order')
            ->get();

        $brands = Brand::where('is_active', true)->orderBy('name')->get();

        return view('storefront.products.index', compact('products', 'categories', 'brands'));
    }

    public function show(string $slug): View
    {
        $product = Product::active()
            ->where('slug', $slug)
            ->with([
                'category',
                'brand',
                'images',
                'specifications',
                'approvedReviews.user',
            ])
            ->firstOrFail();

        $relatedProducts = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['brand', 'category'])
            ->take(4)
            ->get();

        return view('storefront.products.show', compact('product', 'relatedProducts'));
    }
}
