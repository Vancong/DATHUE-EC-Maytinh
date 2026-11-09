<!DOCTYPE html>
<html lang="vi" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TechStore – Hệ Thống Bán Lẻ Máy Tính & Gaming Gear Hàng Đầu')</title>

    <!-- Tailwind CSS & Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex flex-col min-h-screen text-slate-800 antialiased font-sans">

    <!-- Top Announcement Bar -->
    <div class="bg-slate-900 text-slate-300 text-xs py-2 px-4 border-b border-slate-800">
        <div class="max-w-7xl mx-auto flex flex-wrap justify-between items-center gap-2">
            <div class="flex items-center gap-4">
                <span class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Hàng chính hãng 100% – Bảo hành lên tới 36 tháng
                </span>
                <span class="hidden sm:inline text-slate-600">|</span>
                <span class="hidden sm:inline flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Giao hàng hỏa tốc toàn quốc & COD an toàn
                </span>
            </div>
            <div class="flex items-center gap-4 text-xs">
                <span>Hotline: <strong class="text-white hover:text-blue-400 transition-colors">1900 8888</strong></span>
                @auth
                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1 text-amber-400 hover:text-amber-300 font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Vào trang Quản Trị
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </div>

    <!-- Main Navigation Header -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-18 gap-4 lg:gap-8">
                
                <!-- Brand Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 shrink-0 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-700 to-indigo-600 flex items-center justify-center text-white shadow-md shadow-blue-500/20 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <span class="text-xl font-black tracking-tight text-slate-900 group-hover:text-blue-600 transition-colors">TECH<span class="text-blue-600">STORE</span></span>
                        <p class="text-[10px] text-slate-400 font-medium tracking-widest uppercase">Computers & Gaming</p>
                    </div>
                </a>

                <!-- Search Bar -->
                <form action="{{ route('products.index') }}" method="GET" class="flex-1 max-w-2xl hidden md:flex items-center relative">
                    <input 
                        type="text" 
                        name="keyword" 
                        value="{{ request('keyword') }}" 
                        placeholder="Tìm kiếm laptop, RTX 4080, màn hình, bàn phím..." 
                        class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-11 pr-24 py-2.5 text-sm focus:outline-none focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition-all text-slate-800 placeholder-slate-400"
                    >
                    <svg class="w-5 h-5 text-slate-400 absolute left-3.5 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <button type="submit" class="absolute right-1.5 px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow-xs transition-colors">
                        Tìm kiếm
                    </button>
                </form>

                <!-- Actions: Cart & User Account -->
                <div class="flex items-center gap-3">
                    <!-- Cart Pill -->
                    <a href="{{ route('cart.index') }}" class="relative inline-flex items-center gap-2 px-3 py-2 rounded-xl text-slate-700 hover:text-blue-600 hover:bg-slate-50 border border-transparent hover:border-slate-200 transition-all">
                        <div class="relative">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            <span id="cart-counter-badge" class="absolute -top-1.5 -right-2 bg-rose-500 text-white text-[10px] font-bold h-5 min-w-5 px-1 rounded-full flex items-center justify-center border-2 border-white shadow-xs">
                                {{ $cartCount ?? 0 }}
                            </span>
                        </div>
                        <span class="hidden lg:inline text-sm font-semibold">Giỏ hàng</span>
                    </a>

                    <!-- Account Menu -->
                    @auth
                        <div class="relative group" id="user-menu-dropdown">
                            <button type="button" class="flex items-center gap-2 px-3 py-2 rounded-xl text-slate-700 hover:bg-slate-50 border border-slate-200 transition-all text-sm font-medium">
                                <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-xs">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <span class="max-w-[120px] truncate hidden sm:inline">{{ Auth::user()->name }}</span>
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div class="absolute right-0 mt-1 w-56 bg-white rounded-xl shadow-xl border border-slate-100 py-1.5 hidden group-hover:block transition-all z-50">
                                <div class="px-4 py-2 border-b border-slate-100">
                                    <p class="text-xs text-slate-400">Đã đăng nhập với tư cách</p>
                                    <p class="text-sm font-semibold text-slate-800 truncate">{{ Auth::user()->email }}</p>
                                </div>
                                <a href="{{ route('account.orders') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600 transition-colors">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    Lịch sử đơn hàng
                                </a>
                                <a href="{{ route('account.profile') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600 transition-colors">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    Thông tin cá nhân
                                </a>
                                <div class="border-t border-slate-100 my-1"></div>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-sm text-rose-600 hover:bg-rose-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        Đăng xuất
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center gap-2">
                            <a href="{{ route('login') }}" class="px-3.5 py-2 text-sm font-semibold text-slate-700 hover:text-blue-600 hover:bg-slate-50 rounded-xl transition-colors">
                                Đăng nhập
                            </a>
                            <a href="{{ route('register') }}" class="px-3.5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-xs transition-colors hidden sm:inline-block">
                                Đăng ký
                            </a>
                        </div>
                    @endauth
                </div>
            </div>

            <!-- Categories Horizontal Navigation -->
            @if(isset($headerCategories) && $headerCategories->isNotEmpty())
                <nav class="flex items-center gap-1 overflow-x-auto py-2.5 border-t border-slate-100 scrollbar-none text-xs font-semibold text-slate-700">
                    <a href="{{ route('products.index') }}" class="px-3 py-1.5 rounded-lg whitespace-nowrap {{ !request('category') ? 'bg-blue-50 text-blue-600 font-bold' : 'hover:bg-slate-100 hover:text-slate-900' }}">
                        Tất Cả Sản Phẩm
                    </a>
                    @foreach($headerCategories as $cat)
                        <div class="relative group shrink-0">
                            <a href="{{ route('products.index', ['category' => $cat->slug]) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg whitespace-nowrap {{ request('category') === $cat->slug ? 'bg-blue-50 text-blue-600 font-bold' : 'hover:bg-slate-100 hover:text-slate-900' }}">
                                {{ $cat->name }}
                                @if($cat->children->isNotEmpty())
                                    <svg class="w-3 h-3 text-slate-400 group-hover:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                @endif
                            </a>
                            @if($cat->children->isNotEmpty())
                                <div class="absolute left-0 mt-1 w-48 bg-white border border-slate-100 rounded-xl shadow-lg py-1.5 hidden group-hover:block z-50">
                                    @foreach($cat->children as $sub)
                                        <a href="{{ route('products.index', ['category' => $sub->slug]) }}" class="block px-4 py-2 text-xs text-slate-600 hover:bg-blue-50 hover:text-blue-600 transition-colors">
                                            {{ $sub->name }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </nav>
            @endif
        </div>
    </header>

    <!-- Global Flash Alerts -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if(session('success'))
            <div class="p-4 mb-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between text-sm shadow-xs">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">✕</button>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 mb-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between text-sm shadow-xs">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">✕</button>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 mb-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm shadow-xs">
                <div class="font-semibold mb-1 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Vui lòng kiểm tra lại các thông tin sau:
                </div>
                <ul class="list-disc list-inside text-xs space-y-0.5 ml-7 text-amber-700">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Main Page Content -->
    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 w-full">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 text-sm mt-16 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white font-bold">
                            T
                        </div>
                        <span class="text-lg font-bold text-white tracking-tight">TECHSTORE</span>
                    </div>
                    <p class="text-xs leading-relaxed text-slate-400 mb-4">
                        Hệ thống bán lẻ máy tính, laptop gaming, linh kiện và phụ kiện công nghệ hàng đầu Việt Nam. Cam kết 100% hàng chính hãng, hậu mãi chu đáo.
                    </p>
                    <p class="text-xs text-slate-500">Hotline: <span class="text-slate-300 font-semibold">1900 8888</span></p>
                    <p class="text-xs text-slate-500">Email: <span class="text-slate-300 font-semibold">support@techstore.vn</span></p>
                </div>

                <div>
                    <h3 class="text-white text-xs font-semibold uppercase tracking-wider mb-4">Về Chúng Tôi</h3>
                    <ul class="space-y-2 text-xs">
                        <li><a href="#" class="hover:text-white transition-colors">Giới thiệu TechStore</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Hệ thống showroom</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Tuyển dụng</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Tin tức công nghệ</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-white text-xs font-semibold uppercase tracking-wider mb-4">Chính Sách & Hỗ Trợ</h3>
                    <ul class="space-y-2 text-xs">
                        <li><a href="#" class="hover:text-white transition-colors">Chính sách bảo hành đổi trả</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Thanh toán khi nhận hàng (COD)</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Vận chuyển và giao hàng</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Bảo mật thông tin khách hàng</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-white text-xs font-semibold uppercase tracking-wider mb-4">Phương Thức Thanh Toán</h3>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="px-2.5 py-1.5 rounded-md bg-slate-800 text-slate-200 text-xs font-semibold border border-slate-700">COD (Tiền mặt)</span>
                        <span class="px-2.5 py-1.5 rounded-md bg-slate-800 text-slate-200 text-xs font-semibold border border-slate-700">Chuyển khoản</span>
                    </div>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Thanh toán COD: Nhận hàng kiểm tra linh kiện và thanh toán tiền mặt trực tiếp cho nhân viên giao hàng.
                    </p>
                </div>
            </div>

            <div class="border-t border-slate-800 mt-8 pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} TechStore Inc. Tất cả quyền được bảo lưu.</p>
                <p class="mt-2 sm:mt-0">Đồ án E-Commerce Laravel 12 & Tailwind CSS</p>
            </div>
        </div>
    </footer>

    <!-- Toast Notification Container -->
    <div id="toast-container" class="fixed bottom-5 right-5 z-50 flex flex-col gap-2 max-w-sm pointer-events-none"></div>

    @stack('scripts')
</body>
</html>

