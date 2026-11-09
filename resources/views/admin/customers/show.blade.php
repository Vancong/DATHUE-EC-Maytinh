@extends('layouts.admin')

@section('title', 'Chi Tiết Khách Hàng: ' . $customer->name . ' – TechStore Admin')
@section('header_title', 'Hồ Sơ Khách Hàng: ' . $customer->name)

@section('content')
<div class="space-y-6 max-w-5xl">

    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-xl font-bold text-slate-900">{{ $customer->name }}</h2>
                @if($customer->is_active)
                    <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Hoạt động</span>
                @else
                    <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">Đã khóa</span>
                @endif
            </div>
            <p class="text-xs text-slate-500 mt-1">Gia nhập ngày {{ $customer->created_at->format('d/m/Y H:i') }}</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.customers.index') }}" class="px-3.5 py-2 rounded-xl border border-slate-300 text-slate-600 text-xs font-semibold hover:bg-slate-50">
                ← Quay lại danh sách
            </a>
            <form action="{{ route('admin.customers.toggle-active', $customer->id) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-white shadow-xs {{ $customer->is_active ? 'bg-rose-600 hover:bg-rose-700' : 'bg-emerald-600 hover:bg-emerald-700' }}">
                    {{ $customer->is_active ? 'Khóa Tài Khoản' : 'Mở Khóa Tài Khoản' }}
                </button>
            </form>
        </div>
    </div>

    <!-- Info Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-1">
            <span class="text-slate-400 uppercase tracking-wider font-bold text-[10px]">Email</span>
            <p class="text-sm font-bold text-slate-900">{{ $customer->email }}</p>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-1">
            <span class="text-slate-400 uppercase tracking-wider font-bold text-[10px]">Số điện thoại</span>
            <p class="text-sm font-bold text-slate-900">{{ $customer->phone ?? 'Chưa cập nhật' }}</p>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-1">
            <span class="text-slate-400 uppercase tracking-wider font-bold text-[10px]">Địa chỉ giao hàng</span>
            <p class="text-sm font-bold text-slate-900 truncate">{{ $customer->address ?? 'Chưa cập nhật' }}</p>
        </div>
    </div>

    <!-- Orders History Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900">Lịch Sử Mua Hàng ({{ $customer->orders->count() }} đơn)</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3">Mã đơn hàng</th>
                        <th class="px-5 py-3">Ngày đặt</th>
                        <th class="px-5 py-3">Tổng thanh toán</th>
                        <th class="px-5 py-3">Thanh toán</th>
                        <th class="px-5 py-3">Trạng thái đơn</th>
                        <th class="px-5 py-3 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($customer->orders as $order)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3.5 font-mono font-bold text-blue-600">{{ $order->order_code }}</td>
                            <td class="px-5 py-3.5 text-slate-500">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-5 py-3.5 font-black text-slate-900">{{ number_format($order->total_amount, 0, ',', '.') }}đ</td>
                            <td class="px-5 py-3.5">
                                @if($order->payment_status === 'paid')
                                    <span class="text-emerald-600 font-bold">✓ Đã thanh toán</span>
                                @else
                                    <span class="text-amber-600 font-semibold">Thu COD</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold uppercase bg-slate-100 text-slate-700">
                                    {{ $order->order_status }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="text-blue-600 hover:text-blue-800 font-semibold">Xem chi tiết</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-400">Khách hàng chưa phát sinh đơn hàng nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

