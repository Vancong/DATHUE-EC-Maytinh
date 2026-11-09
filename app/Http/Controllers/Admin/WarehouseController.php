<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WarehouseController extends Controller
{
    public function index(): View
    {
        $warehouses = Warehouse::withCount('inventoryStocks')->latest()->paginate(15);

        return view('admin.warehouses.index', compact('warehouses'));
    }

    public function create(): View
    {
        return view('admin.warehouses.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:warehouses,code'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:20'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['code'] = strtoupper(trim($data['code']));
        $data['is_active'] = $request->boolean('is_active', true);

        Warehouse::create($data);

        return redirect()->route('admin.warehouses.index')->with('success', 'Thêm kho hàng mới thành công!');
    }

    public function edit(Warehouse $warehouse): View
    {
        return view('admin.warehouses.edit', compact('warehouse'));
    }

    public function update(Request $request, Warehouse $warehouse): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:warehouses,code,'.$warehouse->id],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:20'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['code'] = strtoupper(trim($data['code']));
        $data['is_active'] = $request->boolean('is_active');

        $warehouse->update($data);

        return redirect()->route('admin.warehouses.index')->with('success', 'Cập nhật kho hàng thành công!');
    }

    public function destroy(Warehouse $warehouse): RedirectResponse
    {
        if ($warehouse->inventoryStocks()->where('quantity', '>', 0)->exists()) {
            return back()->with('error', 'Không thể xóa kho hàng đang còn sản phẩm tồn kho.');
        }

        $warehouse->delete();

        return redirect()->route('admin.warehouses.index')->with('success', 'Đã xóa kho hàng.');
    }
}
