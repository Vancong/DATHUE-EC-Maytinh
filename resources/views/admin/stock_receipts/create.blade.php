@extends('layouts.admin')

@section('title', 'Tạo Phiếu Nhập Kho – TechStore Admin')
@section('header_title', 'Tạo Mới Phiếu Nhập Kho (Draft)')

@section('content')
<div class="max-w-4xl bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-xs space-y-6">

    <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
        <div>
            <h3 class="text-sm font-bold text-slate-900">Thông Tin Phiếu Nhập</h3>
            <p class="text-xs text-slate-500 mt-0.5">Phiếu tạo ở trạng thái DRAFT. Chỉ khi xác nhận duyệt nhập thì tồn kho mới được cập nhật.</p>
        </div>
        <a href="{{ route('admin.stock-receipts.index') }}" class="text-xs text-slate-500 hover:text-slate-800">← Quay lại danh sách</a>
    </div>

    <form action="{{ route('admin.stock-receipts.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="supplier_id" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nhà cung cấp *</label>
                <select id="supplier_id" name="supplier_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600">
                    <option value="">— Chọn nhà cung cấp</option>
                    @foreach($suppliers as $sup)
                        <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>
                            {{ $sup->name }} ({{ $sup->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="warehouse_id" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Kho tiếp nhận *</label>
                <select id="warehouse_id" name="warehouse_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600">
                    <option value="">— Chọn kho hàng</option>
                    @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}" {{ old('warehouse_id') == $wh->id ? 'selected' : '' }}>
                            {{ $wh->name }} ({{ $wh->code }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label for="note" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Ghi chú phiếu nhập</label>
            <input 
                type="text" 
                id="note" 
                name="note" 
                value="{{ old('note') }}" 
                placeholder="Ví dụ: Nhập đợt hàng tháng 11, lô VGA RTX 4070..." 
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600"
            >
        </div>

        <!-- Receipt Items Repeater -->
        <div class="pt-4 border-t border-slate-100 space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Danh Sách Sản Phẩm Nhập *</h4>
                    <p class="text-[11px] text-slate-400">Chọn sản phẩm, số lượng và đơn giá nhập từ nhà phân phối</p>
                </div>
                <button type="button" onclick="addReceiptItemRow()" class="px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition-colors">
                    + Thêm sản phẩm
                </button>
            </div>

            <div id="receipt-items-container" class="space-y-3">
                <div class="grid grid-cols-12 gap-2 receipt-item-row bg-slate-50 p-3 rounded-xl border border-slate-200">
                    <div class="col-span-6">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Sản phẩm</label>
                        <select name="items[0][product_id]" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs focus:outline-none focus:border-blue-600">
                            <option value="">— Chọn sản phẩm</option>
                            @foreach($products as $prod)
                                <option value="{{ $prod->id }}">{{ $prod->name }} ({{ $prod->sku }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-2">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Số lượng</label>
                        <input type="number" name="items[0][quantity]" value="5" min="1" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs font-bold text-center">
                    </div>

                    <div class="col-span-3">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Đơn giá nhập (VNĐ)</label>
                        <input type="number" name="items[0][unit_price]" value="10000000" min="0" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs font-bold">
                    </div>

                    <div class="col-span-1 flex items-end justify-center pb-2">
                        <button type="button" onclick="this.closest('.receipt-item-row').remove()" class="text-slate-400 hover:text-rose-600 font-bold">✕</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
            <a href="{{ route('admin.stock-receipts.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl">Hủy</a>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs shadow-md transition-colors">
                Tạo Phiếu Nháp
            </button>
        </div>
    </form>

</div>

@push('scripts')
<script>
    let itemIdx = 1;
    function addReceiptItemRow() {
        const container = document.getElementById('receipt-items-container');
        const row = document.createElement('div');
        row.className = 'grid grid-cols-12 gap-2 receipt-item-row bg-slate-50 p-3 rounded-xl border border-slate-200';
        row.innerHTML = `
            <div class="col-span-6">
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Sản phẩm</label>
                <select name="items[${itemIdx}][product_id]" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs focus:outline-none focus:border-blue-600">
                    <option value="">— Chọn sản phẩm</option>
                    @foreach($products as $prod)
                        <option value="{{ $prod->id }}">{{ addslashes($prod->name) }} ({{ $prod->sku }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-2">
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Số lượng</label>
                <input type="number" name="items[${itemIdx}][quantity]" value="1" min="1" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs font-bold text-center">
            </div>
            <div class="col-span-3">
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Đơn giá nhập (VNĐ)</label>
                <input type="number" name="items[${itemIdx}][unit_price]" value="1000000" min="0" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs font-bold">
            </div>
            <div class="col-span-1 flex items-end justify-center pb-2">
                <button type="button" onclick="this.closest('.receipt-item-row').remove()" class="text-slate-400 hover:text-rose-600 font-bold">✕</button>
            </div>
        `;
        container.appendChild(row);
        itemIdx++;
    }
</script>
@endpush
@endsection

