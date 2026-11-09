@extends('layouts.admin')

@section('title', 'Quản Lý Đơn Hàng – TechStore Admin')
@section('header_title', 'Danh Sách Đơn Hàng')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <p class="text-xs text-slate-500">Tiếp nhận, xử lý, xuất kho và đối soát đơn hàng COD</p>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
        <form action="{{ route('admin.orders.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <input 
                type="text" 
                name="keyword" 
                value="{{ request('keyword') }}" 
                placeholder="Mã đơn, tên hoặc SĐT khách..." 
                class="px-3.5 py-2 rounded-xl border border-slate-300 text-xs focus:outline-none focus:border-blue-600"
            >

            <select name="status" class="px-3 py-2 rounded-xl border border-slate-300 text-xs focus:outline-none focus:border-blue-600">
                <option value="">— Tất cả trạng thái đơn</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ xử lý (Pending)</option>
                <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Đã xác nhận (Confirmed)</option>
                <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Đang đóng gói (Processing)</option>
                <option value="shipping" {{ request('status') === 'shipping' ? 'selected' : '' }}>Đang giao (Shipping - Đã xuất kho)</option>
                <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Giao thành công (Delivered)</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Đã hủy (Cancelled)</option>
            </select>

            <select name="payment_status" class="px-3 py-2 rounded-xl border border-slate-300 text-xs focus:outline-none focus:border-blue-600">
                <option value="">— Trạng thái thanh toán</option>
                <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>Chưa thanh toán (Thu COD)</option>
                <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Đã thanh toán (Paid)</option>
            </select>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-4 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-semibold shadow-xs transition-colors">
                    Lọc Đơn Hàng
                </button>
                <a href="{{ route('admin.orders.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold">
                    Xóa
                </a>
            </div>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">Mã đơn</th>
                        <th class="px-5 py-3.5">Khách hàng</th>
                        <th class="px-5 py-3.5">Ngày đặt</th>
                        <th class="px-5 py-3.5">Tổng tiền</th>
                        <th class="px-5 py-3.5">Thanh toán</th>
                        <th class="px-5 py-3.5">Trạng thái đơn</th>
                        <th class="px-5 py-3.5 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($orders as $order)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3.5 font-mono font-bold text-blue-600">
                                {{ $order->order_code }}
                            </td>
                            <td class="px-5 py-3.5">
                                <p class="font-bold text-slate-900">{{ $order->customer_name }}</p>
                                <span class="text-[11px] text-slate-500">{{ $order->customer_phone }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-500">
                                {{ $order->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-5 py-3.5 font-black text-slate-900 text-sm">
                                {{ number_format($order->total_amount, 0, ',', '.') }}đ
                            </td>
                            <td class="px-5 py-3.5">
                                @if($order->payment_status === 'paid')
                                    <span class="text-emerald-600 font-bold flex items-center gap-1">✓ Đã thanh toán</span>
                                @else
                                    <span class="text-amber-600 font-semibold">Thu COD</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                @php
                                    $labels = [
                                        'pending' => ['Chờ xử lý', 'bg-amber-50 text-amber-700 border-amber-200'],
                                        'confirmed' => ['Đã xác nhận', 'bg-blue-50 text-blue-700 border-blue-200'],
                                        'processing' => ['Đang đóng gói', 'bg-indigo-50 text-indigo-700 border-indigo-200'],
                                        'shipping' => ['Đang giao', 'bg-purple-50 text-purple-700 border-purple-200'],
                                        'delivered' => ['Thành công', 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                                        'cancelled' => ['Đã hủy', 'bg-rose-50 text-rose-700 border-rose-200'],
                                    ];
                                    $st = $labels[$order->order_status] ?? ['N/A', 'bg-slate-100 text-slate-700'];
                                @endphp
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold border {{ $st[1] }}">
                                    {{ $st[0] }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-2">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="text-blue-600 hover:text-blue-800 font-semibold">Xử lý</a>
                                <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" class="text-slate-500 hover:text-slate-800 font-medium">In đơn</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-slate-400">Không tìm thấy đơn hàng nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $orders->links() }}
        </div>
    </div>

</div>
@endsection

