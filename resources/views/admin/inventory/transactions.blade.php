@extends('layouts.admin')

@section('title', 'Lịch Sử Thẻ Kho (Stock Transactions) – TechStore Admin')
@section('header_title', 'Lịch Sử Biến Động Thẻ Kho')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs text-slate-500">Toàn bộ lịch sử Nhập kho, Xuất hàng và luân chuyển số lượng</p>
        </div>
        <a href="{{ route('admin.inventory.index') }}" class="text-xs font-semibold text-blue-600 hover:underline">
            ← Quay lại báo cáo tồn kho
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">Thời gian</th>
                        <th class="px-5 py-3.5">Sản phẩm</th>
                        <th class="px-5 py-3.5">Kho hàng</th>
                        <th class="px-5 py-3.5 text-center">Loại giao dịch</th>
                        <th class="px-5 py-3.5 text-center">Số lượng</th>
                        <th class="px-5 py-3.5 text-center">Trước → Sau</th>
                        <th class="px-5 py-3.5">Nguồn tham chiếu / Ghi chú</th>
                        <th class="px-5 py-3.5">Người thực hiện</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transactions as $trx)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3.5 text-slate-500 whitespace-nowrap">{{ $trx->created_at->format('d/m/Y H:i:s') }}</td>
                            <td class="px-5 py-3.5">
                                <p class="font-bold text-slate-900 line-clamp-1 max-w-xs">{{ $trx->product->name ?? '—' }}</p>
                                <span class="text-[10px] font-mono text-slate-400">SKU: {{ $trx->product->sku ?? '—' }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $trx->warehouse->name ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-center">
                                @if($trx->transaction_type === 'in')
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        + NHẬP (IN)
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        − XUẤT (OUT)
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-center font-black text-sm {{ $trx->transaction_type === 'in' ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $trx->transaction_type === 'in' ? '+' : '-' }}{{ $trx->quantity }}
                            </td>
                            <td class="px-5 py-3.5 text-center font-mono text-slate-700">
                                {{ $trx->balance_before }} → <strong>{{ $trx->balance_after }}</strong>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">
                                <span class="font-semibold text-slate-800">{{ $trx->reference_type }}: #{{ $trx->reference_id }}</span>
                                <p class="text-[11px] text-slate-500 mt-0.5">{{ $trx->note }}</p>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600 font-medium">{{ $trx->user->name ?? 'Hệ thống' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-8 text-center text-slate-400">Chưa có giao dịch thẻ kho nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $transactions->links() }}
        </div>
    </div>

</div>
@endsection

