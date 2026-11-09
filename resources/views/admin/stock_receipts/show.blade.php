@extends('layouts.admin')

@section('title', 'Chi Tiết Phiếu Nhập ' . $stockReceipt->receipt_code . ' – TechStore Admin')
@section('header_title', 'Chi Tiết Phiếu Nhập Kho: ' . $stockReceipt->receipt_code)

@section('content')
<div class="space-y-6 max-w-4xl">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-xl font-bold font-mono text-slate-900">{{ $stockReceipt->receipt_code }}</h2>
                @if($stockReceipt->status === 'draft')
                    <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                        Bản Nháp (DRAFT)
                    </span>
                @elseif($stockReceipt->status === 'confirmed')
                    <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        ✓ Đã Nhập Vào Kho
                    </span>
                @else
                    <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                        Đã Hủy
                    </span>
                @endif
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Tạo ngày {{ $stockReceipt->created_at->format('d/m/Y H:i:s') }} bởi <strong>{{ $stockReceipt->user->name ?? 'Admin' }}</strong>
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.stock-receipts.index') }}" class="px-3.5 py-2 rounded-xl border border-slate-300 text-slate-600 text-xs font-semibold hover:bg-slate-50">
                ← Quay lại
            </a>

            @if($stockReceipt->isDraft())
                <form action="{{ route('admin.stock-receipts.confirm', $stockReceipt->id) }}" method="POST">
                    @csrf
                    <button type="submit" onclick="return confirm('Xác nhận duyệt nhập kho? Tồn kho thực tế sẽ được cộng vào hệ thống ngay lập tức.')" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-xs transition-colors">
                        ✓ Xác Nhận Duyệt Nhập Kho
                    </button>
                </form>

                <form action="{{ route('admin.stock-receipts.cancel', $stockReceipt->id) }}" method="POST">
                    @csrf
                    <button type="submit" onclick="return confirm('Bạn có chắc chắn muốn hủy phiếu nhập kho này?')" class="px-3.5 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-xl text-xs font-semibold border border-rose-200 transition-colors">
                        Hủy Phiếu
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Info Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-2 text-xs">
            <h4 class="font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">Nhà Cung Cấp</h4>
            <p class="text-sm font-bold text-slate-800">{{ $stockReceipt->supplier->name }}</p>
            <p class="text-slate-500">Mã NCC: <strong class="text-slate-700 font-mono">{{ $stockReceipt->supplier->code }}</strong></p>
            <p class="text-slate-500">Người liên hệ: {{ $stockReceipt->supplier->contact_name ?? '—' }} ({{ $stockReceipt->supplier->phone ?? '—' }})</p>
            <p class="text-slate-500">Địa chỉ: {{ $stockReceipt->supplier->address ?? '—' }}</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-2 text-xs">
            <h4 class="font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">Kho Tiếp Nhận & Trạng Thái</h4>
            <p class="text-sm font-bold text-slate-800">{{ $stockReceipt->warehouse->name }}</p>
            <p class="text-slate-500">Mã kho: <strong class="text-slate-700 font-mono">{{ $stockReceipt->warehouse->code }}</strong></p>
            @if($stockReceipt->confirmed_at)
                <p class="text-emerald-700 font-medium">Thời điểm duyệt nhập: {{ $stockReceipt->confirmed_at->format('d/m/Y H:i:s') }}</p>
            @endif
            @if($stockReceipt->note)
                <p class="text-slate-600 italic">Ghi chú: "{{ $stockReceipt->note }}"</p>
            @endif
        </div>
    </div>

    <!-- Items Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-900">Danh Sách Mặt Hàng Nhập Kho</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3">Sản phẩm</th>
                        <th class="px-5 py-3">Mã SKU</th>
                        <th class="px-5 py-3 text-center">Số lượng</th>
                        <th class="px-5 py-3 text-right">Đơn giá nhập</th>
                        <th class="px-5 py-3 text-right">Thành tiền</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($stockReceipt->items as $item)
                        <tr>
                            <td class="px-5 py-3.5 font-bold text-slate-900">{{ $item->product->name ?? '—' }}</td>
                            <td class="px-5 py-3.5 font-mono text-slate-500">{{ $item->product->sku ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-center font-bold text-slate-800 text-sm">{{ $item->quantity }}</td>
                            <td class="px-5 py-3.5 text-right font-medium">{{ number_format($item->unit_price, 0, ',', '.') }}đ</td>
                            <td class="px-5 py-3.5 text-right font-black text-slate-900">{{ number_format($item->total_price, 0, ',', '.') }}đ</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50 border-t border-slate-200 text-xs font-semibold">
                    <tr>
                        <td colspan="4" class="px-5 py-3 text-right text-slate-600 font-bold uppercase">Tổng Giá Trị Phiếu Nhập:</td>
                        <td class="px-5 py-3 text-right font-black text-blue-600 text-sm">{{ number_format($stockReceipt->total_amount, 0, ',', '.') }}đ</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>
@endsection

