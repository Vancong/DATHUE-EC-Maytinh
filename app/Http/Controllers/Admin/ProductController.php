<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSpecification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with(['category', 'brand'])->latest();

        if ($request->filled('keyword')) {
            $kw = trim($request->query('keyword'));
            $query->where(function ($q) use ($kw) {
                $q->where('name', 'like', "%{$kw}%")
                    ->orWhere('sku', 'like', "%{$kw}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->query('category_id'));
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->query('brand_id'));
        }

        $products = $query->paginate(15)->withQueryString();
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $brands = Brand::where('is_active', true)->orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories', 'brands'));
    }

    public function create(): View
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $brands = Brand::where('is_active', true)->orderBy('name')->get();

        return view('admin.products.create', compact('categories', 'brands'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:products,slug'],
            'sku' => ['required', 'string', 'max:100', 'unique:products,sku'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'stock_quantity' => ['nullable', 'integer', 'min:0'],
            'min_stock' => ['nullable', 'integer', 'min:0'],
            'thumbnail' => ['nullable', 'string', 'max:1000'],
            'short_description' => ['nullable', 'string', 'max:1000'],
            'description' => ['nullable', 'string'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'specs' => ['nullable', 'array'],
            'specs.*.key' => ['nullable', 'string', 'max:100'],
            'specs.*.value' => ['nullable', 'string', 'max:255'],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['nullable', 'string', 'max:1000'],
        ], [
            'name.required' => 'Tên sản phẩm không được để trống.',
            'sku.required' => 'Mã SKU không được để trống.',
            'sku.unique' => 'Mã SKU này đã tồn tại trong hệ thống.',
            'category_id.required' => 'Vui lòng chọn danh mục.',
            'price.required' => 'Vui lòng nhập giá bán.',
            'sale_price.lt' => 'Giá khuyến mãi phải nhỏ hơn giá gốc.',
        ]);

        $data['slug'] = ! empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($data['name']);
        $data['sku'] = strtoupper(trim($data['sku']));
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active', true);
        $data['cost_price'] = $data['cost_price'] ?? 0;
        $data['stock_quantity'] = (int) ($data['stock_quantity'] ?? 0);
        $data['min_stock'] = (int) ($data['min_stock'] ?? 5);

        $product = Product::create($data);

        // Specifications
        if (! empty($data['specs'])) {
            $order = 1;
            foreach ($data['specs'] as $spec) {
                if (! empty($spec['key']) && ! empty($spec['value'])) {
                    ProductSpecification::create([
                        'product_id' => $product->id,
                        'spec_key' => trim($spec['key']),
                        'spec_value' => trim($spec['value']),
                        'sort_order' => $order++,
                    ]);
                }
            }
        }

        // Gallery Images
        if (! empty($data['gallery'])) {
            $order = 1;
            foreach ($data['gallery'] as $imgUrl) {
                if (! empty($imgUrl)) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => trim($imgUrl),
                        'sort_order' => $order++,
                        'is_primary' => false,
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Thêm sản phẩm mới thành công!');
    }

    public function edit(Product $product): View
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $brands = Brand::where('is_active', true)->orderBy('name')->get();
        $product->load(['specifications', 'images']);

        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:products,slug,'.$product->id],
            'sku' => ['required', 'string', 'max:100', 'unique:products,sku,'.$product->id],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'thumbnail' => ['nullable', 'string', 'max:1000'],
            'short_description' => ['nullable', 'string', 'max:1000'],
            'description' => ['nullable', 'string'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'specs' => ['nullable', 'array'],
            'specs.*.key' => ['nullable', 'string', 'max:100'],
            'specs.*.value' => ['nullable', 'string', 'max:255'],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['nullable', 'string', 'max:1000'],
        ]);

        $data['slug'] = ! empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($data['name']);
        $data['sku'] = strtoupper(trim($data['sku']));
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active');
        $data['cost_price'] = $data['cost_price'] ?? 0;
        $data['stock_quantity'] = (int) $data['stock_quantity'];
        $data['min_stock'] = (int) $data['min_stock'];

        $product->update($data);

        // Update Specs: delete old and recreate
        if ($request->has('specs')) {
            ProductSpecification::where('product_id', $product->id)->delete();
            $order = 1;
            foreach ($data['specs'] ?? [] as $spec) {
                if (! empty($spec['key']) && ! empty($spec['value'])) {
                    ProductSpecification::create([
                        'product_id' => $product->id,
                        'spec_key' => trim($spec['key']),
                        'spec_value' => trim($spec['value']),
                        'sort_order' => $order++,
                    ]);
                }
            }
        }

        // Update Gallery
        if ($request->has('gallery')) {
            ProductImage::where('product_id', $product->id)->delete();
            $order = 1;
            foreach ($data['gallery'] ?? [] as $imgUrl) {
                if (! empty($imgUrl)) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => trim($imgUrl),
                        'sort_order' => $order++,
                        'is_primary' => false,
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công!');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->orderItems()->exists()) {
            // Soft delete preserves order history
            $product->delete();

            return redirect()->route('admin.products.index')->with('success', 'Đã lưu trữ (soft delete) sản phẩm vì đã có đơn hàng.');
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Đã xóa sản phẩm.');
    }
}
