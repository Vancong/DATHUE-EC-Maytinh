<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductStockImport;
use App\Models\Supplier;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function __construct(
        protected InventoryService $inventoryService
    ) {}

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

        if ($request->query('status') === 'low') {
            $query->whereColumn('stock_quantity', '<=', 'min_stock')->where('stock_quantity', '>', 0);
        } elseif ($request->query('status') === 'out') {
            $query->where('stock_quantity', '<=', 0);
        }

        $products = $query->paginate(20)->withQueryString();
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $suppliers = Supplier::where('is_active', true)->whereNull('deleted_at')->orderBy('name')->get();
        $recentImports = ProductStockImport::with(['product', 'supplier', 'creator'])->latest()->take(10)->get();

        // Summary counts
        $totalStock = (int) Product::sum('stock_quantity');
        $totalSold = (int) Product::sum('sold_quantity');
        $lowStockCount = Product::where('is_active', true)->whereColumn('stock_quantity', '<=', 'min_stock')->where('stock_quantity', '>', 0)->count();
        $outOfStockCount = Product::where('is_active', true)->where('stock_quantity', '<=', 0)->count();

        return view('admin.inventory.index', compact(
            'products',
            'categories',
            'suppliers',
            'recentImports',
            'totalStock',
            'totalSold',
            'lowStockCount',
            'outOfStockCount'
        ));
    }

    /**
     * Add stock quantity (Nhập thêm hàng)
     */
    public function addStock(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'supplier_id' => [
                'required',
                'integer',
                Rule::exists('suppliers', 'id')
                    ->where('is_active', true)
                    ->whereNull('deleted_at'),
            ],
            'quantity' => ['required', 'integer', 'min:1'],
            'note' => ['nullable', 'string', 'max:500'],
        ], [
            'supplier_id.required' => 'Vui lòng chọn nhà cung cấp.',
            'supplier_id.exists' => 'Nhà cung cấp đã chọn không tồn tại hoặc đã ngừng hoạt động.',
            'quantity.required' => 'Vui lòng nhập số lượng hàng cần bổ sung.',
            'quantity.min' => 'Số lượng nhập thêm phải tối thiểu là 1.',
        ]);

        $this->inventoryService->addStock(
            $product,
            (int) $data['quantity'],
            (int) $data['supplier_id'],
            $data['note'] ?? null,
            Auth::id()
        );

        return back()->with('success', 'Đã nhập thêm +'.$data['quantity'].' sản phẩm "'.$product->name.'" vào kho thành công!');
    }

    /**
     * Update stock and minimum stock threshold directly
     */
    public function updateStock(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'min_stock' => ['nullable', 'integer', 'min:0'],
        ], [
            'stock_quantity.required' => 'Vui lòng nhập số lượng tồn kho.',
            'stock_quantity.min' => 'Số lượng tồn không thể nhỏ hơn 0.',
        ]);

        $this->inventoryService->updateStock(
            $product,
            (int) $data['stock_quantity'],
            isset($data['min_stock']) ? (int) $data['min_stock'] : null
        );

        return back()->with('success', 'Đã cập nhật tồn kho cho sản phẩm "'.$product->name.'"!');
    }
}
