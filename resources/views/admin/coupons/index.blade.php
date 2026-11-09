@extends('layouts.admin')

@section('title', 'Quản Lý Mã Giảm Giá – TechStore Admin')
@section('header_title', 'Mã Giảm Giá & Khuyến Mãi (Coupons)')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <p class="text-xs text-slate-500">Tạo voucher giảm giá cố định (VNĐ) hoặc phần trăm (%) theo đơn hàng</p>
        <a href="{{ route('admin.coupons.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-xs transition-colors flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tạo Mã Ưu Đãi Mới
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">Mã Coupon</th>
                        <th class="px-5 py-3.5">Tên chương trình</th>
                        <th class="px-5 py-3.5">Mức giảm</th>
                        <th class="px-5 py-3.5">Đơn tối thiểu</th>
                        <th class="px-5 py-3.5 text-center">Đã dùng / Giới hạn</th>
                        <th class="px-5 py-3.5">Thời hạn</th>
                        <th class="px-5 py-3.5">Trạng thái</th>
                        <th class="px-5 py-3.5 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($coupons as $c)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3.5 font-mono font-black text-blue-600 text-sm">{{ $c->code }}</td>
                            <td class="px-5 py-3.5 font-bold text-slate-900">{{ $c->name }}</td>
                            <td class="px-5 py-3.5 font-black text-rose-600">
                                @if($c->discount_type === 'percentage')
                                    {{ $c->discount_value }}% (Tối đa {{ number_format($c->max_discount ?? 0, 0, ',', '.') }}đ)
                                @else
                                    {{ number_format($c->discount_value, 0, ',', '.') }}đ
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">{{ number_format($c->min_order_amount, 0, ',', '.') }}đ</td>
                            <td class="px-5 py-3.5 text-center font-bold text-slate-800">
                                {{ $c->used_count }} / {{ $c->usage_limit ?? '∞' }}
                            </td>
                            <td class="px-5 py-3.5 text-slate-500">
                                @if($c->end_date)
                                    Đến {{ $c->end_date->format('d/m/Y') }}
                                @else
                                    Vô thời hạn
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                @if($c->is_active)
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Hoạt động</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-600">Tạm tắt</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-2">
                                <a href="{{ route('admin.coupons.edit', $c->id) }}" class="text-blue-600 hover:text-blue-800 font-semibold">Sửa</a>
                                <button 
                                    type="button" 
                                    onclick="openConfirmModal({
                                        title: 'Xóa Mã Giảm Giá',
                                        message: 'Bạn có chắc chắn muốn xóa mã giảm giá &quot;{{ addslashes($c->code) }}&quot;?',
                                        actionUrl: '{{ route('admin.coupons.destroy', $c->id) }}',
                                        method: 'DELETE'
                                    })" 
                                    class="text-rose-600 hover:text-rose-800 font-semibold"
                                >
                                    Xóa
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-8 text-center text-slate-400">Chưa có mã giảm giá nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $coupons->links() }}
        </div>
    </div>

</div>
@endsection

