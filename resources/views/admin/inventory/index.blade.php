@extends('layouts.admin')

@section('title', 'Quản Lý Tồn Kho – TechStore Admin')
@section('header_title', 'Quản Lý Tồn Kho & Nhập Hàng')

@section('content')
<div class="space-y-6">

    <!-- KPI Summary Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Tổng Tồn Kho</span>
            <div class="mt-1 flex items-baseline justify-between">
                <span class="text-2xl font-black text-slate-900 font-mono">{{ number_format($totalStock) }}</span>
                <span class="text-xs text-slate-500">chiếc</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Đã Bán Ra</span>
            <div class="mt-1 flex items-baseline justify-between">
                <span class="text-2xl font-black text-blue-600 font-mono">{{ number_format($totalSold) }}</span>
                <span class="text-xs text-slate-500">chiếc</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[11px] font-bold text-amber-500 uppercase tracking-wider block">Cảnh Báo: Sắp Hết</span>
            <div class="mt-1 flex items-baseline justify-between">
                <span class="text-2xl font-black text-amber-600 font-mono">{{ $lowStockCount }}</span>
                <span class="text-xs text-amber-700 font-semibold">≤ Min Stock</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-[11px] font-bold text-rose-500 uppercase tracking-wider block">Đã Hết Hàng</span>
            <div class="mt-1 flex items-baseline justify-between">
                <span class="text-2xl font-black text-rose-600 font-mono">{{ $outOfStockCount }}</span>
                <span class="text-xs text-rose-700 font-semibold">= 0 chiếc</span>
            </div>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
        <form action="{{ route('admin.inventory.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <input 
                type="text" 
                name="keyword" 
                value="{{ request('keyword') }}" 
                placeholder="Tìm tên sản phẩm hoặc SKU..." 
                class="px-3.5 py-2 rounded-xl border border-slate-300 text-xs focus:outline-none focus:border-blue-600"
            >

            <select name="category_id" class="px-3 py-2 rounded-xl border border-slate-300 text-xs focus:outline-none focus:border-blue-600">
                <option value="">— Tất cả danh mục</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>

            <select name="status" class="px-3 py-2 rounded-xl border border-slate-300 text-xs focus:outline-none focus:border-blue-600">
                <option value="">— Tất cả trạng thái tồn</option>
                <option value="low" {{ request('status') === 'low' ? 'selected' : '' }}>Sắp hết hàng (Tồn ≤ Ngưỡng báo)</option>
                <option value="out" {{ request('status') === 'out' ? 'selected' : '' }}>Hết hàng (Tồn = 0)</option>
            </select>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-4 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-semibold shadow-xs transition-colors">
                    Lọc Dữ Liệu
                </button>
                <a href="{{ route('admin.inventory.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold">
                    Xóa
                </a>
            </div>
        </form>
    </div>

    <!-- Products Inventory Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">Sản phẩm</th>
                        <th class="px-5 py-3.5">Mã SKU</th>
                        <th class="px-5 py-3.5 text-right">Giá bán</th>
                        <th class="px-5 py-3.5 text-center">Số lượng tồn</th>
                        <th class="px-5 py-3.5 text-center">Ngưỡng báo</th>
                        <th class="px-5 py-3.5 text-center">Đã bán</th>
                        <th class="px-5 py-3.5">Tình trạng</th>
                        <th class="px-5 py-3.5 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($products as $p)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3.5 flex items-center gap-3">
                                <img src="{{ $p->thumbnail ?? 'https://placehold.co/100x100?text=No+Image' }}" alt="{{ $p->name }}" class="w-10 h-10 rounded-lg object-cover border border-slate-100 shrink-0">
                                <div class="max-w-xs">
                                    <h4 class="font-bold text-slate-900 line-clamp-1">{{ $p->name }}</h4>
                                    <span class="text-[10px] text-blue-600 font-medium">{{ $p->brand->name ?? '—' }} | {{ $p->category->name ?? '—' }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 font-mono text-slate-500 font-bold">{{ $p->sku }}</td>
                            <td class="px-5 py-3.5 text-right font-bold text-slate-900">
                                {{ number_format($p->current_price, 0, ',', '.') }}đ
                            </td>
                            <td class="px-5 py-3.5 text-center font-black text-sm {{ $p->stock_quantity <= $p->min_stock ? ($p->stock_quantity <= 0 ? 'text-rose-600' : 'text-amber-600') : 'text-slate-900' }}">
                                {{ $p->stock_quantity }}
                            </td>
                            <td class="px-5 py-3.5 text-center font-bold text-slate-500">
                                {{ $p->min_stock }}
                            </td>
                            <td class="px-5 py-3.5 text-center font-bold text-blue-600">
                                {{ $p->sold_quantity }}
                            </td>
                            <td class="px-5 py-3.5">
                                @if($p->stock_quantity <= 0)
                                    <span class="px-2.5 py-1 rounded-md text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        Hết hàng
                                    </span>
                                @elseif($p->stock_quantity <= $p->min_stock)
                                    <span class="px-2.5 py-1 rounded-md text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        Cảnh báo: Sắp hết
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-md text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Đầy đủ
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-1 whitespace-nowrap">
                                <button 
                                    type="button" 
                                    onclick="openRestockModal({{ $p->id }}, '{{ addslashes($p->name) }}', '{{ $p->sku }}')" 
                                    class="px-2.5 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg text-xs font-bold transition-colors"
                                >
                                    + Nhập Hàng
                                </button>
                                <button 
                                    type="button" 
                                    onclick="openEditStockModal({{ $p->id }}, '{{ addslashes($p->name) }}', {{ $p->stock_quantity }}, {{ $p->min_stock }})" 
                                    class="px-2.5 py-1.5 bg-slate-100 text-slate-700 hover:bg-slate-200 rounded-lg text-xs font-semibold transition-colors"
                                >
                                    Sửa Tồn
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center text-slate-400">
                                Không tìm thấy sản phẩm nào phù hợp với bộ lọc.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $products->links() }}
            </div>
        @endif
    </div>

    <!-- Recent Stock Imports History Section -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    Lịch Sử Nhập Hàng Gần Đây
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Nhật ký các đợt bổ sung hàng tồn kho từ nhà cung cấp</p>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-blue-50 text-blue-700">
                {{ $recentImports->count() }} lượt gần nhất
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3">Sản phẩm</th>
                        <th class="px-5 py-3">Nhà cung cấp</th>
                        <th class="px-5 py-3 text-center">Số lượng nhập</th>
                        <th class="px-5 py-3">Người thực hiện</th>
                        <th class="px-5 py-3">Ghi chú</th>
                        <th class="px-5 py-3 text-right">Thời gian nhập</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentImports as $import)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3.5">
                                <span class="font-bold text-slate-900 block truncate max-w-xs">{{ $import->product->name ?? '—' }}</span>
                                <span class="text-[10px] font-mono text-slate-500 font-semibold">{{ $import->product->sku ?? '' }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="font-bold text-slate-800">{{ $import->supplier->name ?? '—' }}</span>
                                <span class="text-[10px] font-mono text-blue-600 block">{{ $import->supplier->code ?? '' }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black bg-emerald-50 text-emerald-700 border border-emerald-200 font-mono">
                                    +{{ number_format($import->quantity) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600 font-medium">
                                {{ $import->creator->name ?? 'Hệ thống' }}
                            </td>
                            <td class="px-5 py-3.5 text-slate-500 italic max-w-xs truncate">
                                {{ $import->note ?: '—' }}
                            </td>
                            <td class="px-5 py-3.5 text-right font-mono text-slate-500">
                                {{ $import->created_at ? $import->created_at->format('d/m/Y H:i') : '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-400">
                                Chưa có lượt nhập hàng nào được ghi nhận.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal 1: Nhập Thêm Hàng Nhanh (+Số lượng) -->
<div id="restockModal" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-2xl p-6 shadow-xl border border-slate-200 animate-in fade-in zoom-in-95">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-900">Nhập Thêm Hàng Vào Tồn Kho</h3>
            <button type="button" onclick="closeRestockModal()" class="text-slate-400 hover:text-slate-600 font-bold text-base">✕</button>
        </div>

        <form id="restockForm" method="POST" action="" class="space-y-4 pt-4">
            @csrf
            <div>
                <p class="text-xs font-semibold text-slate-500">Sản phẩm tiếp nhận:</p>
                <p id="restockProdName" class="text-xs font-bold text-slate-900 mt-0.5 truncate"></p>
                <p id="restockProdSku" class="text-[10px] font-mono text-blue-600 mt-0.5"></p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Nhà cung cấp <span class="text-rose-500">*</span>
                </label>
                <select name="supplier_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold focus:outline-none focus:border-blue-600 bg-white">
                    <option value="">— Chọn nhà cung cấp —</option>
                    @foreach($suppliers as $sup)
                        <option value="{{ $sup->id }}">{{ $sup->name }} ({{ $sup->code }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Số lượng cộng thêm (+)</label>
                <input type="number" name="quantity" min="1" value="10" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-black focus:outline-none focus:border-blue-600">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Ghi chú (Tùy chọn)</label>
                <input type="text" name="note" placeholder="Ví dụ: Nhập bổ sung đợt mới từ NPP..." class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs focus:outline-none focus:border-blue-600">
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeRestockModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold">
                    Hủy Bỏ
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-xs">
                    Xác Nhận Nhập Hàng
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Chỉnh Sửa Trực Tiếp Tồn Kho & Ngưỡng Báo Động -->
<div id="editStockModal" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-2xl p-6 shadow-xl border border-slate-200 animate-in fade-in zoom-in-95">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-900">Điều Chỉnh Số Lượng Tồn Kho</h3>
            <button type="button" onclick="closeEditStockModal()" class="text-slate-400 hover:text-slate-600 font-bold text-base">✕</button>
        </div>

        <form id="editStockForm" method="POST" action="" class="space-y-4 pt-4">
            @csrf
            @method('PATCH')
            <div>
                <p class="text-xs font-semibold text-slate-500">Sản phẩm điều chỉnh:</p>
                <p id="editStockProdName" class="text-xs font-bold text-slate-900 mt-0.5 truncate"></p>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Số lượng tồn mới</label>
                    <input type="number" id="editStockQty" name="stock_quantity" min="0" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-black focus:outline-none focus:border-blue-600">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Ngưỡng báo động</label>
                    <input type="number" id="editStockMin" name="min_stock" min="0" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-bold focus:outline-none focus:border-blue-600">
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeEditStockModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold">
                    Hủy Bỏ
                </button>
                <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold shadow-xs">
                    Lưu Thay Đổi
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openRestockModal(id, name, sku) {
        document.getElementById('restockForm').action = `/admin/inventory/${id}/add-stock`;
        document.getElementById('restockProdName').innerText = name;
        document.getElementById('restockProdSku').innerText = 'SKU: ' + sku;
        document.getElementById('restockModal').classList.remove('hidden');
    }

    function closeRestockModal() {
        document.getElementById('restockModal').classList.add('hidden');
    }

    function openEditStockModal(id, name, qty, minStock) {
        document.getElementById('editStockForm').action = `/admin/inventory/${id}/update-stock`;
        document.getElementById('editStockProdName').innerText = name;
        document.getElementById('editStockQty').value = qty;
        document.getElementById('editStockMin').value = minStock;
        document.getElementById('editStockModal').classList.remove('hidden');
    }

    function closeEditStockModal() {
        document.getElementById('editStockModal').classList.add('hidden');
    }
</script>
@endpush
@endsection
