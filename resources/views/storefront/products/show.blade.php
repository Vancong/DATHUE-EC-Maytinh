@extends('layouts.storefront')

@section('title', $product->name . ' – TechStore')

@section('content')
<div class="space-y-10">

    <!-- Breadcrumbs -->
    <nav class="flex text-xs text-slate-500 gap-2 items-center flex-wrap">
        <a href="{{ route('home') }}" class="hover:text-blue-600 transition-colors">Trang chủ</a>
        <span>/</span>
        <a href="{{ route('products.index') }}" class="hover:text-blue-600 transition-colors">Sản phẩm</a>
        @if($product->category)
            <span>/</span>
            <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="hover:text-blue-600 transition-colors">
                {{ $product->category->name }}
            </a>
        @endif
        <span>/</span>
        <span class="text-slate-900 font-semibold truncate max-w-xs">{{ $product->name }}</span>
    </nav>

    <!-- Product Main Intro: Gallery + Quick Buy Details -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left: Image Gallery (5 cols) -->
        <div class="lg:col-span-5 space-y-4">
            <div class="aspect-4/3 bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs relative flex items-center justify-center p-4">
                <img 
                    id="main-product-image" 
                    src="{{ $product->thumbnail ?? 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?auto=format&fit=crop&w=800&q=80' }}" 
                    alt="{{ $product->name }}" 
                    class="max-h-full max-w-full object-contain transition-all duration-300"
                >
                @if($product->is_on_sale)
                    <span class="absolute top-4 left-4 bg-rose-600 text-white text-xs font-bold px-2.5 py-1 rounded-md shadow-xs">
                        GIẢM GIÁ
                    </span>
                @endif
            </div>

            <!-- Thumbnails List -->
            @if($product->images->isNotEmpty())
                <div class="flex items-center gap-3 overflow-x-auto pb-2 scrollbar-none">
                    <button 
                        type="button" 
                        onclick="switchMainImage('{{ $product->thumbnail }}', this)" 
                        class="thumb-btn w-16 h-16 rounded-xl border-2 border-blue-600 overflow-hidden shrink-0 bg-white p-1"
                    >
                        <img src="{{ $product->thumbnail }}" alt="thumb" class="w-full h-full object-contain">
                    </button>
                    @foreach($product->images as $img)
                        <button 
                            type="button" 
                            onclick="switchMainImage('{{ $img->image_path }}', this)" 
                            class="thumb-btn w-16 h-16 rounded-xl border-2 border-transparent hover:border-slate-300 overflow-hidden shrink-0 bg-white p-1 transition-all"
                        >
                            <img src="{{ $img->image_path }}" alt="thumb" class="w-full h-full object-contain">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Right: Information & Action (7 cols) -->
        <div class="lg:col-span-7 bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-xs space-y-6">
            <div>
                <div class="flex items-center gap-3 mb-2 flex-wrap">
                    @if($product->brand)
                        <span class="text-xs font-bold px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 uppercase tracking-wider">
                            {{ $product->brand->name }}
                        </span>
                    @endif
                    <span class="text-xs text-slate-400">SKU: <strong class="text-slate-700 font-mono">{{ $product->sku }}</strong></span>
                    <span class="text-slate-300">|</span>
                    @if($product->available_stock > 0)
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Còn hàng (Khả dụng: {{ $product->available_stock }} chiếc)
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                            Tạm hết hàng
                        </span>
                    @endif
                </div>

                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight leading-snug">
                    {{ $product->name }}
                </h1>
            </div>

            <!-- Price Box -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 flex items-baseline gap-4 flex-wrap">
                <span class="text-3xl font-black text-rose-600">
                    {{ number_format($product->current_price, 0, ',', '.') }}đ
                </span>
                @if($product->is_on_sale)
                    <span class="text-base text-slate-400 line-through">
                        {{ number_format($product->price, 0, ',', '.') }}đ
                    </span>
                    <span class="text-xs font-bold text-rose-700 bg-rose-100 px-2 py-0.5 rounded-md">
                        Tiết kiệm {{ number_format($product->price - $product->sale_price, 0, ',', '.') }}đ
                    </span>
                @endif
            </div>

            <!-- Short description -->
            @if($product->short_description)
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    {{ $product->short_description }}
                </p>
            @endif

            <!-- Add to Cart Form -->
            <form action="{{ route('cart.add') }}" method="POST" class="space-y-4 pt-2">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                <div class="flex items-center gap-4">
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Số lượng:</label>
                    <div class="flex items-center border border-slate-300 rounded-xl overflow-hidden bg-slate-50">
                        <button type="button" onclick="decrementQty()" class="px-3 py-2 text-slate-600 hover:bg-slate-200 transition-colors font-bold">−</button>
                        <input 
                            type="number" 
                            id="buy-qty" 
                            name="quantity" 
                            value="1" 
                            min="1" 
                            max="{{ $product->available_stock }}" 
                            class="w-12 text-center text-sm font-semibold bg-white border-x border-slate-300 py-2 focus:outline-none"
                        >
                        <button type="button" onclick="incrementQty({{ $product->available_stock }})" class="px-3 py-2 text-slate-600 hover:bg-slate-200 transition-colors font-bold">+</button>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-3 pt-2">
                    <button 
                        type="submit" 
                        {{ $product->available_stock <= 0 ? 'disabled' : '' }}
                        class="w-full sm:flex-1 py-3 px-6 bg-blue-600 hover:bg-blue-700 disabled:bg-slate-300 text-white font-bold rounded-xl shadow-md shadow-blue-500/20 transition-all flex items-center justify-center gap-2 text-sm"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        Thêm Vào Giỏ Hàng
                    </button>
                    
                    <a 
                        href="{{ route('checkout.index') }}" 
                        onclick="event.preventDefault(); buyNow({{ $product->id }});"
                        class="w-full sm:flex-1 py-3 px-6 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl shadow-md shadow-rose-500/20 transition-all text-center text-sm"
                    >
                        Mua Ngay COD
                    </a>
                </div>
            </form>

            <!-- Store Guarantee Perks -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-4 border-t border-slate-100 text-xs text-slate-600">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Bảo hành chính hãng 36T</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Giao COD kiểm tra máy</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>1 đổi 1 trong 30 ngày</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Specifications Table & Detailed Content -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left: Specifications Table (7 cols) -->
        <div class="lg:col-span-7 bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-xs space-y-6">
            <div class="border-b border-slate-100 pb-3">
                <h3 class="text-lg font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Bảng Thông Số Kỹ Thuật Chi Tiết
                </h3>
            </div>

            @if($product->specifications->isNotEmpty())
                <div class="overflow-hidden rounded-xl border border-slate-200">
                    <table class="w-full text-xs text-left text-slate-700">
                        <tbody class="divide-y divide-slate-100">
                            @foreach($product->specifications as $spec)
                                <tr class="{{ $loop->odd ? 'bg-slate-50/60' : 'bg-white' }}">
                                    <td class="px-4 py-3 font-bold text-slate-900 w-1/3 border-r border-slate-100">{{ $spec->spec_key }}</td>
                                    <td class="px-4 py-3 text-slate-700 font-medium">{{ $spec->spec_value }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-xs text-slate-400 italic">Đang cập nhật thông số chi tiết...</p>
            @endif

            <!-- Full Article Description -->
            <div class="pt-6 border-t border-slate-100 space-y-3">
                <h4 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Đặc Điểm Nổi Bật</h4>
                <div class="prose prose-sm max-w-none text-slate-700 leading-relaxed text-xs sm:text-sm">
                    {!! $product->description !!}
                </div>
            </div>
        </div>

        <!-- Right: Customer Reviews & Ratings (5 cols) -->
        <div class="lg:col-span-5 bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-xs space-y-6">
            <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    Đánh Giá Khách Hàng ({{ $product->approvedReviews->count() }})
                </h3>
            </div>

            <!-- List of Reviews -->
            <div class="space-y-4 max-h-96 overflow-y-auto scrollbar-none">
                @forelse($product->approvedReviews as $rev)
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-xs space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-800">{{ $rev->user->name ?? 'Khách Hàng' }}</span>
                            <div class="flex text-amber-400 text-xs">
                                @for($i = 1; $i <= 5; $i++)
                                    <span>{{ $i <= $rev->rating ? '★' : '☆' }}</span>
                                @endfor
                            </div>
                        </div>
                        <p class="text-slate-600 leading-relaxed">{{ $rev->comment }}</p>
                        <span class="text-[10px] text-slate-400 block">{{ $rev->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                @empty
                    <div class="text-center py-6 text-slate-400 text-xs">
                        Chưa có đánh giá nào cho sản phẩm này. Khách hàng đã mua hàng và hoàn tất đơn có thể đánh giá từ trang Lịch sử đơn hàng.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if(isset($relatedProducts) && $relatedProducts->isNotEmpty())
        <div class="space-y-4 pt-6">
            <h3 class="text-xl font-bold text-slate-900 tracking-tight">Sản Phẩm Cùng Phân Khúc</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedProducts as $rel)
                    <div class="bg-white rounded-2xl border border-slate-200/90 overflow-hidden shadow-xs hover:shadow-md transition-all flex flex-col group">
                        <a href="{{ route('products.show', $rel->slug) }}" class="relative aspect-4/3 bg-slate-100 overflow-hidden block">
                            <img src="{{ $rel->thumbnail }}" alt="{{ $rel->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                        </a>
                        <div class="p-4 flex-1 flex flex-col justify-between">
                            <h4 class="text-xs font-bold text-slate-900 line-clamp-2 hover:text-blue-600 transition-colors">
                                <a href="{{ route('products.show', $rel->slug) }}">{{ $rel->name }}</a>
                            </h4>
                            <div class="mt-3 flex items-center justify-between">
                                <span class="text-sm font-black text-rose-600">{{ number_format($rel->current_price, 0, ',', '.') }}đ</span>
                                <a href="{{ route('products.show', $rel->slug) }}" class="text-xs font-semibold text-blue-600 hover:underline">Chi tiết</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>

@push('scripts')
<script>
    function switchMainImage(url, btn) {
        document.getElementById('main-product-image').src = url;
        document.querySelectorAll('.thumb-btn').forEach(b => {
            b.classList.remove('border-blue-600');
            b.classList.add('border-transparent');
        });
        btn.classList.add('border-blue-600');
        btn.classList.remove('border-transparent');
    }

    function decrementQty() {
        const input = document.getElementById('buy-qty');
        let val = parseInt(input.value) || 1;
        if (val > 1) input.value = val - 1;
    }

    function incrementQty(max) {
        const input = document.getElementById('buy-qty');
        let val = parseInt(input.value) || 1;
        if (val < max) input.value = val + 1;
    }

    function buyNow(productId) {
        const qty = parseInt(document.getElementById('buy-qty').value) || 1;
        fetch('{{ route("cart.add") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ product_id: productId, quantity: qty })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.href = '{{ route("checkout.index") }}';
            } else {
                if (window.showToast) window.showToast(data.message, 'error');
            }
        });
    }
</script>
@endpush
@endsection

