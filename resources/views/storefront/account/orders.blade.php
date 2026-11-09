@extends('layouts.storefront')

@section('title', 'Lịch Sử Đơn Hàng – TechStore')

@section('content')
<div class="space-y-6">

    <div class="border-b border-slate-200 pb-4 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Lịch Sử Đơn Hàng</h1>
            <p class="text-xs text-slate-500 mt-0.5">Theo dõi hành trình đơn hàng và lịch sử mua sắm của bạn</p>
        </div>
        <a href="{{ route('account.profile') }}" class="text-xs font-semibold text-blue-600 hover:underline">
            Thông tin cá nhân →
        </a>
    </div>

    @if($orders->isEmpty())
        <div class="bg-white rounded-2xl p-12 text-center border border-slate-200 shadow-xs max-w-md mx-auto my-8">
            <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4 text-slate-400">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
            <h3 class="text-base font-bold text-slate-900">Bạn chưa có đơn hàng nào</h3>
            <p class="text-xs text-slate-500 mt-1">Khi bạn đặt mua sản phẩm, đơn hàng sẽ hiển thị tại đây.</p>
            <a href="{{ route('products.index') }}" class="inline-block mt-4 px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-xs transition-colors">
                Khám Phá Cửa Hàng
            </a>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left text-slate-700">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3.5">Mã đơn hàng</th>
                            <th class="px-5 py-3.5">Ngày đặt</th>
                            <th class="px-5 py-3.5 text-center">Số món</th>
                            <th class="px-5 py-3.5">Tổng tiền</th>
                            <th class="px-5 py-3.5">Trạng thái đơn</th>
                            <th class="px-5 py-3.5">Thanh toán</th>
                            <th class="px-5 py-3.5 text-right">Chi tiết</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($orders as $order)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-5 py-4 font-mono font-bold text-blue-600">
                                    {{ $order->order_code }}
                                </td>
                                <td class="px-5 py-4 text-slate-500">
                                    {{ $order->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-5 py-4 text-center font-semibold">
                                    {{ $order->items_count }}
                                </td>
                                <td class="px-5 py-4 font-black text-slate-900">
                                    {{ number_format($order->total_amount, 0, ',', '.') }}đ
                                </td>
                                <td class="px-5 py-4">
                                    @php
                                        $statusBadges = [
                                            'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'confirmed' => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'processing' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                            'shipping' => 'bg-purple-50 text-purple-700 border-purple-200',
                                            'delivered' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'cancelled' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        ];
                                        $statusLabels = [
                                            'pending' => 'Chờ xử lý',
                                            'confirmed' => 'Đã xác nhận',
                                            'processing' => 'Đang đóng gói',
                                            'shipping' => 'Đang vận chuyển',
                                            'delivered' => 'Giao thành công',
                                            'cancelled' => 'Đã hủy',
                                        ];
                                    @endphp
                                    <span class="inline-block px-2.5 py-1 rounded-md text-[11px] font-semibold border {{ $statusBadges[$order->order_status] ?? 'bg-slate-100 text-slate-700' }}">
                                        {{ $statusLabels[$order->order_status] ?? $order->order_status }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    @if($order->payment_status === 'paid')
                                        <span class="text-emerald-600 font-semibold flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Đã thanh toán
                                        </span>
                                    @else
                                        <span class="text-slate-500 font-medium">Chưa trả (Thu COD)</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('account.orders.show', $order->order_code) }}" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-600 hover:text-white text-blue-700 rounded-lg text-xs font-semibold transition-colors">
                                        Xem chi tiết
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $orders->links() }}
            </div>
        </div>
    @endif

</div>
@endsection

