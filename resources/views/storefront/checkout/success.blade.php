@extends('layouts.storefront')

@section('title', 'Đặt Hàng Thành Công – TechStore')

@section('content')
<div class="max-w-3xl mx-auto space-y-8 my-6">

    <!-- Success Header Card -->
    <div class="bg-white p-8 rounded-3xl border border-slate-200 text-center shadow-xs space-y-4">
        <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto shadow-inner">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
        </div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Cảm Ơn Bạn Đã Đặt Hàng Tại TechStore!</h1>
        <p class="text-sm text-slate-500 max-w-md mx-auto">
            Đơn hàng của bạn đã được tiếp nhận và nhân viên sẽ liên hệ xác nhận trước khi đóng gói gửi đi.
        </p>

        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-50 border border-slate-200 font-mono text-sm text-slate-800">
            <span>Mã đơn hàng:</span>
            <strong class="text-blue-600 font-bold">{{ $order->order_code }}</strong>
        </div>
    </div>

    <!-- Order Details Card -->
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-xs space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs border-b border-slate-100 pb-6">
            <div>
                <h4 class="font-bold text-slate-900 uppercase tracking-wider mb-2">Thông Tin Giao Nhận</h4>
                <p class="font-semibold text-slate-800">{{ $order->customer_name }}</p>
                <p class="text-slate-500">{{ $order->customer_phone }}</p>
                <p class="text-slate-500">{{ $order->customer_email }}</p>
                <p class="text-slate-600 mt-1">{{ $order->shipping_address }}</p>
            </div>

            <div>
                <h4 class="font-bold text-slate-900 uppercase tracking-wider mb-2">Phương Thức Thanh Toán</h4>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-blue-50 text-blue-700 font-semibold mb-2">
                    Thanh toán tiền mặt khi nhận hàng (COD)
                </span>
                <p class="text-slate-500">Trạng thái thanh toán: <strong class="text-amber-600">Chưa thanh toán (Thu COD)</strong></p>
                <p class="text-slate-500">Trạng thái đơn: <strong class="text-blue-600 font-bold uppercase">{{ $order->order_status }}</strong></p>
            </div>
        </div>

        <!-- Order Items Snapshot Table -->
        <div>
            <h4 class="font-bold text-slate-900 uppercase tracking-wider text-xs mb-3">Sản Phẩm Đã Mua</h4>
            <div class="overflow-hidden rounded-xl border border-slate-200">
                <table class="w-full text-xs text-left text-slate-700">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3">Sản phẩm</th>
                            <th class="px-4 py-3">SKU</th>
                            <th class="px-4 py-3 text-right">Đơn giá</th>
                            <th class="px-4 py-3 text-center">SL</th>
                            <th class="px-4 py-3 text-right">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($order->items as $item)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-slate-900">{{ $item->product_name }}</td>
                                <td class="px-4 py-3 font-mono text-slate-500">{{ $item->product_sku }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format($item->unit_price, 0, ',', '.') }}đ</td>
                                <td class="px-4 py-3 text-center font-bold">{{ $item->quantity }}</td>
                                <td class="px-4 py-3 text-right font-black text-slate-900">{{ number_format($item->total_price, 0, ',', '.') }}đ</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50 border-t border-slate-200 text-xs font-semibold">
                        <tr>
                            <td colspan="4" class="px-4 py-2 text-right text-slate-500">Tạm tính:</td>
                            <td class="px-4 py-2 text-right text-slate-800">{{ number_format($order->subtotal, 0, ',', '.') }}đ</td>
                        </tr>
                        @if($order->discount_amount > 0)
                            <tr>
                                <td colspan="4" class="px-4 py-2 text-right text-emerald-600">Giảm giá voucher:</td>
                                <td class="px-4 py-2 text-right text-emerald-600">-{{ number_format($order->discount_amount, 0, ',', '.') }}đ</td>
                            </tr>
                        @endif
                        <tr>
                            <td colspan="4" class="px-4 py-2 text-right text-slate-500">Phí vận chuyển COD:</td>
                            <td class="px-4 py-2 text-right text-emerald-600">Miễn phí</td>
                        </tr>
                        <tr class="text-sm font-black text-slate-900 border-t border-slate-200">
                            <td colspan="4" class="px-4 py-3 text-right">Tổng thanh toán:</td>
                            <td class="px-4 py-3 text-right text-rose-600 text-base">{{ number_format($order->total_amount, 0, ',', '.') }}đ</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('products.index') }}" class="px-6 py-2.5 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 text-xs font-semibold transition-colors">
                ← Tiếp Tục Mua Sắm
            </a>
            @auth
                <a href="{{ route('account.orders') }}" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-xs transition-colors">
                    Xem Quản Lý Đơn Hàng Của Bạn
                </a>
            @endauth
        </div>
    </div>

</div>
@endsection

