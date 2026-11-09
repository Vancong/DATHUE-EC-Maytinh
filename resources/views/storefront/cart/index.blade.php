@extends('layouts.storefront')

@section('title', 'Giỏ Hàng Của Bạn – TechStore')

@section('content')
<div class="space-y-6">

    <div class="border-b border-slate-200 pb-4">
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Giỏ Hàng Mua Sắm</h1>
        <p class="text-xs text-slate-500 mt-0.5">Quản lý các sản phẩm máy tính và linh kiện bạn đã chọn</p>
    </div>

    @if($cart->items->isEmpty())
        <div class="bg-white rounded-2xl p-12 text-center border border-slate-200 shadow-xs max-w-lg mx-auto my-8">
            <div class="w-20 h-20 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
            <h2 class="text-lg font-bold text-slate-900">Giỏ hàng của bạn đang trống</h2>
            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Chưa có sản phẩm nào được thêm. Hãy dạo qua cửa hàng và chọn ngay chiếc máy tính ưng ý nhé!</p>
            <a href="{{ route('products.index') }}" class="inline-block mt-6 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md shadow-blue-500/20 transition-all">
                Khám Phá Sản Phẩm Ngay
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Items Table (8 cols) -->
            <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="p-4 sm:p-6 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900">Danh Sách Sản Phẩm ({{ $cart->total_quantity }})</h3>
                    <form action="{{ route('cart.clear') }}" method="POST">
                        @csrf
                        <button type="submit" onclick="return confirm('Bạn có chắc chắn muốn xóa toàn bộ sản phẩm khỏi giỏ hàng?')" class="text-xs text-rose-600 hover:underline font-semibold">
                            Xóa tất cả
                        </button>
                    </form>
                </div>

                <div class="divide-y divide-slate-100">
                    @foreach($cart->items as $item)
                        <div class="p-4 sm:p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <!-- Thumb & Info -->
                            <div class="flex items-center gap-4 flex-1">
                                <a href="{{ route('products.show', $item->product->slug) }}" class="w-20 h-20 rounded-xl bg-slate-50 border border-slate-100 p-2 shrink-0 overflow-hidden block">
                                    <img src="{{ $item->product->thumbnail }}" alt="{{ $item->product->name }}" class="w-full h-full object-contain">
                                </a>
                                <div class="space-y-1">
                                    <p class="text-[10px] font-bold text-blue-600 uppercase tracking-wider">{{ $item->product->brand->name ?? 'TechStore' }}</p>
                                    <h4 class="text-xs sm:text-sm font-bold text-slate-900 hover:text-blue-600 transition-colors">
                                        <a href="{{ route('products.show', $item->product->slug) }}">{{ $item->product->name }}</a>
                                    </h4>
                                    <span class="text-xs font-black text-rose-600 block">
                                        {{ number_format($item->product->sale_price ?? $item->product->price, 0, ',', '.') }}đ
                                    </span>
                                </div>
                            </div>

                            <!-- Quantity Form & Total -->
                            <div class="flex items-center justify-between sm:justify-end gap-6 w-full sm:w-auto">
                                <form action="{{ route('cart.update', $item->id) }}" method="POST" class="flex items-center border border-slate-300 rounded-xl overflow-hidden bg-slate-50">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" name="quantity" value="{{ $item->quantity - 1 }}" class="px-2.5 py-1.5 text-slate-600 hover:bg-slate-200 transition-colors font-bold text-xs">−</button>
                                    <span class="w-10 text-center text-xs font-semibold bg-white border-x border-slate-300 py-1.5 text-slate-800">
                                        {{ $item->quantity }}
                                    </span>
                                    <button type="submit" name="quantity" value="{{ $item->quantity + 1 }}" class="px-2.5 py-1.5 text-slate-600 hover:bg-slate-200 transition-colors font-bold text-xs">+</button>
                                </form>

                                <div class="text-right min-w-[100px]">
                                    <span class="text-sm font-black text-slate-900 block">
                                        {{ number_format($item->subtotal, 0, ',', '.') }}đ
                                    </span>
                                    <form action="{{ route('cart.remove', $item->id) }}" method="POST" class="mt-1">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-[11px] text-slate-400 hover:text-rose-600 transition-colors">Xóa</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Summary Box (4 cols) -->
            <div class="lg:col-span-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-xs space-y-6">
                <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Tóm Tắt Đơn Hàng</h3>

                <div class="space-y-3 text-xs">
                    <div class="flex justify-between text-slate-600">
                        <span>Tạm tính hàng:</span>
                        <span class="font-bold text-slate-800">{{ number_format($cart->subtotal, 0, ',', '.') }}đ</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Phí vận chuyển COD:</span>
                        <span class="font-bold text-emerald-600">Miễn phí</span>
                    </div>
                    <div class="border-t border-slate-100 pt-3 flex justify-between items-baseline text-sm">
                        <span class="font-bold text-slate-900">Tổng cộng:</span>
                        <span class="text-2xl font-black text-rose-600">{{ number_format($cart->subtotal, 0, ',', '.') }}đ</span>
                    </div>
                </div>

                <a href="{{ route('checkout.index') }}" class="w-full block py-3 px-6 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md shadow-blue-500/20 text-center transition-all text-sm">
                    Tiến Hành Đặt Hàng COD
                </a>

                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-[11px] text-slate-500 leading-relaxed space-y-1">
                    <p class="font-semibold text-slate-700 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Thanh toán tiền mặt khi nhận hàng (COD)
                    </p>
                    <p>Khách hàng được quyền đồng kiểm tra niêm phong kiện hàng cùng bưu tá trước khi thanh toán.</p>
                </div>
            </div>

        </div>
    @endif

</div>
@endsection

