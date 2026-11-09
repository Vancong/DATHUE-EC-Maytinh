@extends('layouts.storefront')

@section('title', 'Đăng nhập – TechStore')

@section('content')
<div class="max-w-md mx-auto my-10 bg-white p-8 rounded-2xl border border-slate-200 shadow-sm">
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Đăng Nhập Khách Hàng</h2>
        <p class="text-sm text-slate-500 mt-1">Đăng nhập để theo dõi đơn hàng và ưu đãi dành riêng cho bạn</p>
    </div>

    <!-- Demo Account Quick Fill Helper -->
    <div class="mb-6 p-3 rounded-xl bg-blue-50 border border-blue-100 text-xs text-blue-800">
        <p class="font-bold flex items-center gap-1.5 mb-1">
            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Tài khoản khách hàng mẫu:
        </p>
        <div class="flex items-center justify-between">
            <span>Email: <strong>customer@techstore.vn</strong> | Pass: <strong>password123</strong></span>
            <button type="button" onclick="fillCustomerDemo()" class="underline font-semibold text-blue-700 hover:text-blue-900">Điền nhanh</button>
        </div>
    </div>

    <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Địa chỉ Email</label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                value="{{ old('email') }}" 
                required 
                autocomplete="email"
                placeholder="name@example.com" 
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100"
            >
        </div>

        <div>
            <div class="flex items-center justify-between mb-1">
                <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Mật khẩu</label>
            </div>
            <input 
                type="password" 
                id="password" 
                name="password" 
                required 
                autocomplete="current-password"
                placeholder="••••••••" 
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100"
            >
        </div>

        <div class="flex items-center justify-between text-sm">
            <label class="flex items-center gap-2 cursor-pointer text-slate-600">
                <input type="checkbox" name="remember" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                <span>Ghi nhớ đăng nhập</span>
            </label>
        </div>

        <button type="submit" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-xs transition-colors text-sm">
            Đăng Nhập
        </button>
    </form>

    <div class="mt-6 pt-6 border-t border-slate-100 text-center text-xs text-slate-600">
        Chưa có tài khoản TechStore? 
        <a href="{{ route('register') }}" class="font-bold text-blue-600 hover:text-blue-700">Đăng ký ngay</a>
    </div>
</div>

<script>
    function fillCustomerDemo() {
        document.getElementById('email').value = 'customer@techstore.vn';
        document.getElementById('password').value = 'password123';
    }
</script>
@endsection

