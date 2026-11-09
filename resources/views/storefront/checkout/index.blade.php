@extends('layouts.storefront')

@section('title', 'Thanh Toán Đơn Hàng – TechStore')

@section('content')
<div class="space-y-6">

    <div class="border-b border-slate-200 pb-4">
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Thanh Toán & Đặt Hàng</h1>
        <p class="text-xs text-slate-500 mt-0.5">Xác nhận thông tin giao nhận và phương thức thanh toán COD an toàn</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left: Customer Information & Delivery Address Form (7 cols) -->
        <div class="lg:col-span-7 bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-xs space-y-6">
            <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Thông Tin Nhận Hàng
            </h3>

            <form id="checkout-form" action="{{ route('checkout.process') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="customer_name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Họ và tên người nhận *</label>
                        <input 
                            type="text" 
                            id="customer_name" 
                            name="customer_name" 
                            value="{{ old('customer_name', Auth::user()->name ?? '') }}" 
                            required 
                            placeholder="Nguyễn Văn A" 
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100"
                        >
                    </div>

                    <div>
                        <label for="customer_phone" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Số điện thoại liên hệ *</label>
                        <input 
                            type="text" 
                            id="customer_phone" 
                            name="customer_phone" 
                            value="{{ old('customer_phone', Auth::user()->phone ?? '') }}" 
                            required 
                            placeholder="0901234567" 
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100"
                        >
                    </div>
                </div>

                <div>
                    <label for="customer_email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Địa chỉ Email (Nhận thông báo đơn hàng) *</label>
                    <input 
                        type="email" 
                        id="customer_email" 
                        name="customer_email" 
                        value="{{ old('customer_email', Auth::user()->email ?? '') }}" 
                        required 
                        placeholder="email@example.com" 
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100"
                    >
                </div>

                <div>
                    <label for="shipping_address" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Địa chỉ giao hàng chi tiết *</label>
                    <textarea 
                        id="shipping_address" 
                        name="shipping_address" 
                        rows="3" 
                        required 
                        placeholder="Số nhà, tên đường, thôn/ấp, phường/xã, quận/huyện, tỉnh/thành phố" 
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100"
                    >{{ old('shipping_address', Auth::user()->address ?? '') }}</textarea>
                </div>

                <div>
                    <label for="note" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Ghi chú giao hàng (Tùy chọn)</label>
                    <input 
                        type="text" 
                        id="note" 
                        name="note" 
                        value="{{ old('note') }}" 
                        placeholder="Ví dụ: Giao giờ hành chính, gọi trước khi đến 15 phút..." 
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100"
                    >
                </div>

                <!-- Payment Method Section -->
                <div class="pt-4 border-t border-slate-100 space-y-3">
                    <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Phương Thức Thanh Toán</h4>
                    <div class="p-4 rounded-xl border-2 border-blue-600 bg-blue-50/50 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold text-xs">
                                COD
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-900">Thanh toán khi nhận hàng (Cash On Delivery)</p>
                                <p class="text-xs text-slate-500 mt-0.5">Kiểm tra tem bảo hành và sản phẩm rồi thanh toán tiền mặt cho nhân viên giao hàng</p>
                            </div>
                        </div>
                        <span class="w-5 h-5 rounded-full border-4 border-blue-600 bg-white"></span>
                    </div>
                </div>
            </form>
        </div>

        <!-- Right: Order Summary & Coupon (5 cols) -->
        <div class="lg:col-span-5 space-y-6">
            
            <!-- Items Overview Box -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs space-y-4">
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3">Đơn Hàng ({{ $cart->total_quantity }} món)</h3>
                
                <div class="divide-y divide-slate-100 max-h-60 overflow-y-auto scrollbar-none">
                    @foreach($cart->items as $item)
                        <div class="py-3 flex items-center justify-between gap-3 text-xs">
                            <div class="flex items-center gap-2.5">
                                <span class="w-6 h-6 rounded-md bg-slate-100 font-bold text-slate-600 flex items-center justify-center shrink-0">
                                    {{ $item->quantity }}x
                                </span>
                                <span class="font-semibold text-slate-800 line-clamp-1">{{ $item->product->name }}</span>
                            </div>
                            <span class="font-bold text-slate-900 shrink-0">{{ number_format($item->subtotal, 0, ',', '.') }}đ</span>
                        </div>
                    @endforeach
                </div>

                <!-- Coupon Form -->
                <div class="pt-4 border-t border-slate-100">
                    @if($coupon)
                        <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-between text-xs text-emerald-800">
                            <div>
                                <p class="font-bold">Mã giảm giá: {{ $coupon->code }}</p>
                                <p class="text-[11px] text-emerald-700">{{ $coupon->name }}</p>
                            </div>
                            <form action="{{ route('checkout.remove-coupon') }}" method="POST">
                                @csrf
                                <button type="submit" class="text-rose-600 hover:underline font-bold text-xs">Hủy mã</button>
                            </form>
                        </div>
                    @else
                        <form action="{{ route('checkout.apply-coupon') }}" method="POST" class="flex items-center gap-2">
                            @csrf
                            <input 
                                type="text" 
                                name="code" 
                                placeholder="Nhập mã ưu đãi (TECH500, TECH10...)" 
                                class="flex-1 px-3 py-2 rounded-xl border border-slate-300 text-xs uppercase font-bold focus:outline-none focus:border-blue-600"
                            >
                            <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-semibold shrink-0 transition-colors">
                                Áp Dụng
                            </button>
                        </form>
                    @endif
                </div>

                <!-- Totals Calculation -->
                <div class="space-y-2 pt-4 border-t border-slate-100 text-xs">
                    <div class="flex justify-between text-slate-600">
                        <span>Tạm tính:</span>
                        <span class="font-bold text-slate-800">{{ number_format($cart->subtotal, 0, ',', '.') }}đ</span>
                    </div>

                    @if($discountAmount > 0)
                        <div class="flex justify-between text-emerald-600 font-semibold">
                            <span>Giảm giá khuyến mãi:</span>
                            <span>-{{ number_format($discountAmount, 0, ',', '.') }}đ</span>
                        </div>
                    @endif

                    <div class="flex justify-between text-slate-600">
                        <span>Phí giao hàng:</span>
                        <span class="font-bold text-emerald-600">Miễn phí COD</span>
                    </div>

                    <div class="border-t border-slate-100 pt-3 flex justify-between items-baseline text-sm">
                        <span class="font-bold text-slate-900">Tổng thanh toán:</span>
                        <span class="text-2xl font-black text-rose-600">{{ number_format($totalAmount, 0, ',', '.') }}đ</span>
                    </div>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    form="checkout-form"
                    class="w-full py-3.5 px-6 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-500/25 transition-all text-sm flex items-center justify-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Xác Nhận Đặt Hàng COD
                </button>
            </div>

        </div>

    </div>

</div>
@endsection

