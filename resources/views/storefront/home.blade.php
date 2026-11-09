@extends('layouts.storefront')

@section('title', 'TechStore – Hệ Thống Bán Lẻ Máy Tính, Laptop & Gaming Gear')

@section('content')
<div class="space-y-12">

    <!-- Hero Section Banner -->
    <section class="relative rounded-3xl overflow-hidden bg-gradient-to-r from-slate-900 via-blue-950 to-slate-900 text-white p-8 md:p-14 shadow-xl border border-slate-800">
        <div class="max-w-2xl relative z-10 space-y-5">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-blue-500/20 text-blue-300 border border-blue-500/30">
                <span class="w-2 h-2 rounded-full bg-blue-400 animate-pulse"></span>
                Thế Hệ Laptop & PC Đồ Họa 2026 Mới Nhất
            </span>
            <h1 class="text-3xl sm:text-5xl font-black tracking-tight leading-tight">
                Hiệu Năng Vô Song.<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-300">Khám Phá Giới Hạn Mới.</span>
            </h1>
            <p class="text-sm sm:text-base text-slate-300 leading-relaxed">
                Đỉnh cao laptop gaming ASUS ROG, MacBook Pro M3 Max và dàn PC đồ họa trang bị card NVIDIA GeForce RTX 4000 series chính hãng, bảo hành 3 năm tận nơi.
            </p>
            <div class="flex flex-wrap items-center gap-3 pt-2">
                <a href="{{ route('products.index') }}" class="px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm shadow-lg shadow-blue-600/30 transition-all transform hover:-translate-y-0.5">
                    Mua Sắm Ngay
                </a>
                <a href="{{ route('products.index', ['category' => 'laptop-gaming']) }}" class="px-6 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-sm border border-slate-700 transition-colors">
                    Xem Laptop Gaming
                </a>
            </div>
        </div>

        <div class="absolute right-0 top-0 bottom-0 w-1/2 opacity-20 lg:opacity-30 pointer-events-none hidden md:block">
            <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                <path d="M0,0 L100,0 L100,100 L50,100 Z" fill="url(#grad)" />
                <defs>
                    <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#3b82f6" />
                        <stop offset="100%" stop-color="#1e1b4b" />
                    </linearGradient>
                </defs>
            </svg>
        </div>
    </section>

    <!-- Value Props / Cam kết thương hiệu -->
    <section class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <div>
                <h4 class="text-sm font-bold text-slate-900">100% Chính Hãng</h4>
                <p class="text-xs text-slate-500 mt-0.5">Xuất hóa đơn VAT & tem bảo hành</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <div>
                <h4 class="text-sm font-bold text-slate-900">Thanh Toán COD</h4>
                <p class="text-xs text-slate-500 mt-0.5">Kiểm tra máy rồi mới trả tiền</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </div>
            <div>
                <h4 class="text-sm font-bold text-slate-900">Đổi Mới 30 Ngày</h4>
                <p class="text-xs text-slate-500 mt-0.5">Nếu phát sinh lỗi từ nhà sản xuất</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <div>
                <h4 class="text-sm font-bold text-slate-900">Hỗ Trợ Kỹ Thuật 24/7</h4>
                <p class="text-xs text-slate-500 mt-0.5">Cài đặt phần mềm & vệ sinh máy</p>
            </div>
        </div>
    </section>

    <!-- Featured Products Showcase -->
    <section class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">Sản Phẩm Nổi Bật</h2>
                <p class="text-xs text-slate-500 mt-0.5">Tuyển chọn các dòng máy tính & linh kiện bán chạy nhất</p>
            </div>
            <a href="{{ route('products.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                Xem tất cả
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($featuredProducts as $product)
                <div class="bg-white rounded-2xl border border-slate-200/90 overflow-hidden shadow-xs hover:shadow-md transition-all flex flex-col group">
                    <a href="{{ route('products.show', $product->slug) }}" class="relative aspect-4/3 bg-slate-100 overflow-hidden block">
                        <img 
                            src="{{ $product->thumbnail ?? 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?auto=format&fit=crop&w=600&q=80' }}" 
                            alt="{{ $product->name }}" 
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                        >
                        @if($product->is_on_sale)
                            <span class="absolute top-3 left-3 bg-rose-600 text-white text-[11px] font-bold px-2 py-0.5 rounded-md shadow-xs">
                                GIẢM GIÁ
                            </span>
                        @endif
                        <span class="absolute top-3 right-3 bg-slate-900/80 backdrop-blur-xs text-white text-[10px] font-semibold px-2 py-0.5 rounded-md">
                            {{ $product->category->name ?? 'Linh kiện' }}
                        </span>
                    </a>

                    <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                        <div>
                            <p class="text-[11px] font-semibold text-blue-600 uppercase tracking-wider mb-1">
                                {{ $product->brand->name ?? 'TechStore' }}
                            </p>
                            <h3 class="text-sm font-bold text-slate-900 line-clamp-2 hover:text-blue-600 transition-colors leading-snug">
                                <a href="{{ route('products.show', $product->slug) }}">
                                    {{ $product->name }}
                                </a>
                            </h3>
                            <p class="text-xs text-slate-500 mt-1 line-clamp-2 leading-relaxed">
                                {{ $product->short_description }}
                            </p>
                        </div>

                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                            <div>
                                <span class="text-base font-black text-rose-600 block">
                                    {{ number_format($product->current_price, 0, ',', '.') }}đ
                                </span>
                                @if($product->is_on_sale)
                                    <span class="text-xs text-slate-400 line-through">
                                        {{ number_format($product->price, 0, ',', '.') }}đ
                                    </span>
                                @endif
                            </div>

                            <button 
                                type="button" 
                                onclick="quickAddToCart({{ $product->id }})" 
                                class="p-2.5 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors"
                                title="Thêm vào giỏ hàng"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-4 text-center py-12 text-slate-500 text-sm">
                    Đang cập nhật danh mục sản phẩm...
                </div>
            @endforelse
        </div>
    </section>

    <!-- Brands Carousel / Showcase -->
    @if(isset($popularBrands) && $popularBrands->isNotEmpty())
        <section class="bg-white p-6 rounded-2xl border border-slate-200/80">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 text-center">Thương Hiệu Đồng Hành Chính Hãng</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-4 items-center">
                @foreach($popularBrands as $b)
                    <a href="{{ route('products.index', ['brand' => $b->slug]) }}" class="px-3 py-2 rounded-xl text-center font-bold text-xs text-slate-600 hover:text-blue-600 hover:bg-slate-50 border border-slate-100 transition-colors">
                        {{ $b->name }}
                    </a>
                @endforeach
            </div>
        </section>
    @endif

</div>

@push('scripts')
<script>
    function quickAddToCart(productId) {
        fetch('{{ route("cart.add") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ product_id: productId, quantity: 1 })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (window.showToast) {
                    window.showToast(data.message, 'success');
                }
                const badge = document.getElementById('cart-counter-badge');
                if (badge && data.cart_count !== undefined) {
                    badge.textContent = data.cart_count;
                }
            } else {
                if (window.showToast) {
                    window.showToast(data.message, 'error');
                }
            }
        })
        .catch(err => {
            console.error(err);
            if (window.showToast) {
                window.showToast('Không thể kết nối máy chủ giỏ hàng.', 'error');
            }
        });
    }
</script>
@endpush
@endsection

