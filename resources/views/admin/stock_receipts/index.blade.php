@extends('layouts.admin')

@section('title', 'Quản Lý Nhập Kho – TechStore Admin')
@section('header_title', 'Phiếu Nhập Kho (Stock Receipts)')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <p class="text-xs text-slate-500">Quản lý các phiếu nhập hàng từ nhà cung cấp vào kho hệ thống</p>
        <a href="{{ route('admin.stock-receipts.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-xs transition-colors flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tạo Phiếu Nhập Kho Mới
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">Mã phiếu</th>
                        <th class="px-5 py-3.5">Nhà cung cấp</th>
                        <th class="px-5 py-3.5">Kho nhập</th>
                        <th class="px-5 py-3.5">Người tạo</th>
                        <th class="px-5 py-3.5 text-center">Số món</th>
                        <th class="px-5 py-3.5 text-right">Tổng tiền nhập</th>
                        <th class="px-5 py-3.5">Trạng thái</th>
                        <th class="px-5 py-3.5 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($receipts as $rc)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3.5 font-mono font-bold text-blue-600">{{ $rc->receipt_code }}</td>
                            <td class="px-5 py-3.5 font-bold text-slate-900">{{ $rc->supplier->name ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $rc->warehouse->name ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-slate-500">{{ $rc->user->name ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-center font-bold text-slate-800">{{ $rc->items_count }}</td>
                            <td class="px-5 py-3.5 text-right font-black text-slate-900">{{ number_format($rc->total_amount, 0, ',', '.') }}đ</td>
                            <td class="px-5 py-3.5">
                                @if($rc->status === 'draft')
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">Bản nháp (DRAFT)</span>
                                @elseif($rc->status === 'confirmed')
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Đã nhập kho</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">Đã hủy</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-2">
                                <a href="{{ route('admin.stock-receipts.show', $rc->id) }}" class="text-blue-600 hover:text-blue-800 font-semibold">Xem chi tiết</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-8 text-center text-slate-400">Chưa có phiếu nhập kho nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $receipts->links() }}
        </div>
    </div>

</div>
@endsection

