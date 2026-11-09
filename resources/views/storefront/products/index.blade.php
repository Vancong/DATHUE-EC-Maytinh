@extends('layouts.storefront')

@section('title', 'Danh Sách Sản Phẩm – TechStore')

@section('content')
<div class="space-y-6">

    <!-- Breadcrumbs -->
    <nav class="flex text-xs text-slate-500 gap-2 items-center">
        <a href="{{ route('home') }}" class="hover:text-blue-600 transition-colors">Trang chủ</a>
        <span>/</span>
        <span class="text-slate-900 font-semibold">Tất cả sản phẩm</span>
        @if(request('category'))
            <span>/</span>
            <span class="text-blue-600 font-bold capitalize">{{ str_replace('-', ' ', request('category')) }}</span>
        @endif
    </nav>

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Máy Tính & Linh Kiện Chính Hãng</h1>
            <p class="text-xs text-slate-500 mt-0.5">Tìm thấy <strong class="text-slate-800">{{ $products->total() }}</strong> sản phẩm phù hợp</p>
        </div>

        <!-- Sorting Selector -->
        <form action="{{ route('products.index') }}" method="GET" class="flex items-center gap-2 self-start sm:self-auto">
            @foreach(request()->except('sort', 'page') as $key => $val)
                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
            @endforeach
            <label for="sort-select" class="text-xs font-semibold text-slate-600 shrink-0">Sắp xếp:</label>
            <select 
                id="sort-select" 
                name="sort" 
                onchange="this.form.submit()" 
                class="text-xs font-medium bg-white border border-slate-300 rounded-xl px-3 py-2 text-slate-700 focus:outline-none focus:border-blue-600"
            >
                <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Mới nhất</option>
                <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Giá: Thấp đến cao</option>
                <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Giá: Cao đến thấp</option>
                <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Tên: A - Z</option>
            </select>
        </form>
    </div>

    <!-- Main Grid: Left Filters, Right Products -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <!-- Sidebar Filters -->
        <div class="space-y-6">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-bold text-slate-900">Bộ Lọc Tìm Kiếm</h3>
                    <a href="{{ route('products.index') }}" class="text-xs text-rose-600 hover:underline">Xóa tất cả</a>
                </div>

                <!-- Category Filter -->
                <div class="space-y-2">
                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Danh Mục</h4>
                    <div class="space-y-1 max-h-56 overflow-y-auto scrollbar-none text-xs">
                        <a href="{{ route('products.index', array_merge(request()->except('category', 'page'), [])) }}" class="block px-2.5 py-1.5 rounded-lg {{ !request('category') ? 'bg-blue-50 text-blue-600 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                            Tất cả danh mục
                        </a>
                        @foreach($categories as $cat)
                            <a href="{{ route('products.index', array_merge(request()->except('page'), ['category' => $cat->slug])) }}" class="block px-2.5 py-1.5 rounded-lg {{ request('category') === $cat->slug ? 'bg-blue-50 text-blue-600 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                                {{ $cat->name }}
                            </a>
                            @foreach($cat->children as $sub)
                                <a href="{{ route('products.index', array_merge(request()->except('page'), ['category' => $sub->slug])) }}" class="block pl-6 pr-2.5 py-1 rounded-lg {{ request('category') === $sub->slug ? 'bg-blue-50 text-blue-600 font-bold' : 'text-slate-500 hover:bg-slate-50' }}">
                                    — {{ $sub->name }}
                                </a>
                            @endforeach
                        @endforeach
                    </div>
                </div>

                <!-- Brand Filter -->
                <div class="space-y-2 pt-4 border-t border-slate-100">
                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Thương Hiệu</h4>
                    <div class="space-y-1 max-h-48 overflow-y-auto scrollbar-none text-xs">
                        <a href="{{ route('products.index', array_merge(request()->except('brand', 'page'), [])) }}" class="block px-2.5 py-1.5 rounded-lg {{ !request('brand') ? 'bg-blue-50 text-blue-600 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                            Tất cả thương hiệu
                        </a>
                        @foreach($brands as $brand)
                            <a href="{{ route('products.index', array_merge(request()->except('page'), ['brand' => $brand->slug])) }}" class="block px-2.5 py-1.5 rounded-lg {{ request('brand') === $brand->slug ? 'bg-blue-50 text-blue-600 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                                {{ $brand->name }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Price Range Filter Form -->
                <div class="space-y-2 pt-4 border-t border-slate-100">
                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Khoảng Giá (VNĐ)</h4>
                    <form action="{{ route('products.index') }}" method="GET" class="space-y-3">
                        @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                        @if(request('brand')) <input type="hidden" name="brand" value="{{ request('brand') }}"> @endif
                        @if(request('keyword')) <input type="hidden" name="keyword" value="{{ request('keyword') }}"> @endif
                        @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif

                        <div class="grid grid-cols-2 gap-2">
                            <input 
                                type="number" 
                                name="min_price" 
                                value="{{ request('min_price') }}" 
                                placeholder="Từ" 
                                class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 text-xs focus:outline-none focus:border-blue-600"
                            >
                            <input 
                                type="number" 
                                name="max_price" 
                                value="{{ request('max_price') }}" 
                                placeholder="Đến" 
                                class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 text-xs focus:outline-none focus:border-blue-600"
                            >
                        </div>
                        <button type="submit" class="w-full py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-semibold shadow-xs transition-colors">
                            Áp Dụng Giá
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Products Listing -->
        <div class="lg:col-span-3 space-y-6">
            @if($products->isEmpty())
                <div class="bg-white rounded-2xl p-12 text-center border border-slate-200">
                    <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4 text-slate-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Không tìm thấy sản phẩm nào</h3>
                    <p class="text-xs text-slate-500 mt-1">Hãy thử xóa bớt bộ lọc hoặc tìm kiếm với từ khóa khác.</p>
                    <a href="{{ route('products.index') }}" class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white text-xs font-bold rounded-xl shadow-xs hover:bg-blue-700 transition-colors">
                        Xóa Bộ Lọc
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($products as $product)
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
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
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
                if (window.showToast) window.showToast(data.message, 'success');
                const badge = document.getElementById('cart-counter-badge');
                if (badge && data.cart_count !== undefined) badge.textContent = data.cart_count;
            } else {
                if (window.showToast) window.showToast(data.message, 'error');
            }
        })
        .catch(err => {
            console.error(err);
            if (window.showToast) window.showToast('Không thể kết nối máy chủ giỏ hàng.', 'error');
        });
    }
</script>
@endpush
@endsection

