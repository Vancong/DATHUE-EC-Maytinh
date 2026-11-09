<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockReceipt;
use App\Models\StockReceiptItem;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Services\InventoryService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StockReceiptController extends Controller
{
    public function __construct(protected InventoryService $inventoryService) {}

    public function index(): View
    {
        $receipts = StockReceipt::with(['supplier', 'warehouse', 'user'])
            ->withCount('items')
            ->latest()
            ->paginate(15);

        return view('admin.stock_receipts.index', compact('receipts'));
    }

    public function create(): View
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        $products = Product::active()->orderBy('name')->get();

        return view('admin.stock_receipts.create', compact('suppliers', 'warehouses', 'products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
            'warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
            'note' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ], [
            'supplier_id.required' => 'Vui lòng chọn nhà cung cấp.',
            'warehouse_id.required' => 'Vui lòng chọn kho hàng nhập.',
            'items.required' => 'Vui lòng thêm ít nhất 1 sản phẩm vào phiếu nhập.',
        ]);

        try {
            DB::transaction(function () use ($data) {
                $receiptCode = 'PN-'.date('Ymd').'-'.strtoupper(Str::random(4));
                $totalAmount = 0.0;

                $receipt = StockReceipt::create([
                    'receipt_code' => $receiptCode,
                    'supplier_id' => $data['supplier_id'],
                    'warehouse_id' => $data['warehouse_id'],
                    'user_id' => Auth::id(),
                    'status' => 'draft',
                    'note' => $data['note'] ?? null,
                    'total_amount' => 0,
                ]);

                foreach ($data['items'] as $item) {
                    $qty = (int) $item['quantity'];
                    $price = (float) $item['unit_price'];
                    $total = $qty * $price;
                    $totalAmount += $total;

                    StockReceiptItem::create([
                        'stock_receipt_id' => $receipt->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $qty,
                        'unit_price' => $price,
                        'total_price' => $total,
                    ]);
                }

                $receipt->update(['total_amount' => $totalAmount]);
            });

            return redirect()->route('admin.stock-receipts.index')->with('success', 'Tạo phiếu nhập kho DRAFT thành công!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Lỗi tạo phiếu nhập: '.$e->getMessage());
        }
    }

    public function show(StockReceipt $stockReceipt): View
    {
        $stockReceipt->load(['supplier', 'warehouse', 'user', 'items.product']);

        return view('admin.stock_receipts.show', compact('stockReceipt'));
    }

    public function confirm(StockReceipt $stockReceipt): RedirectResponse
    {
        try {
            $this->inventoryService->confirmReceipt($stockReceipt, Auth::id());

            return back()->with('success', 'Xác nhận nhập kho thành công! Số lượng sản phẩm đã được cộng vào tồn kho.');
        } catch (Exception $e) {
            return back()->with('error', 'Xác nhận thất bại: '.$e->getMessage());
        }
    }

    public function cancel(StockReceipt $stockReceipt): RedirectResponse
    {
        try {
            $this->inventoryService->cancelReceipt($stockReceipt);

            return back()->with('success', 'Đã hủy phiếu nhập kho.');
        } catch (Exception $e) {
            return back()->with('error', 'Không thể hủy phiếu nhập: '.$e->getMessage());
        }
    }
}
