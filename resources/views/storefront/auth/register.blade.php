@extends('layouts.storefront')

@section('title', 'Đăng ký tài khoản – TechStore')

@section('content')
<div class="max-w-md mx-auto my-10 bg-white p-8 rounded-2xl border border-slate-200 shadow-sm">
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Tạo Tài Khoản Mới</h2>
        <p class="text-sm text-slate-500 mt-1">Trở thành thành viên TechStore để nhận ngập tràn ưu đãi</p>
    </div>

    <form action="{{ route('register.post') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Họ và tên *</label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                value="{{ old('name') }}" 
                required 
                placeholder="Nguyễn Văn A" 
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100"
            >
        </div>

        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Địa chỉ Email *</label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                value="{{ old('email') }}" 
                required 
                placeholder="name@example.com" 
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100"
            >
        </div>

        <div>
            <label for="phone" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Số điện thoại *</label>
            <input 
                type="text" 
                id="phone" 
                name="phone" 
                value="{{ old('phone') }}" 
                required 
                placeholder="0901234567" 
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100"
            >
        </div>

        <div>
            <label for="address" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Địa chỉ nhận hàng mặc định</label>
            <input 
                type="text" 
                id="address" 
                name="address" 
                value="{{ old('address') }}" 
                placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành" 
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100"
            >
        </div>

        <div>
            <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Mật khẩu (ít nhất 6 ký tự) *</label>
            <input 
                type="password" 
                id="password" 
                name="password" 
                required 
                placeholder="••••••••" 
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100"
            >
        </div>

        <div>
            <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Xác nhận mật khẩu *</label>
            <input 
                type="password" 
                id="password_confirmation" 
                name="password_confirmation" 
                required 
                placeholder="••••••••" 
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100"
            >
        </div>

        <button type="submit" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-xs transition-colors text-sm">
            Tạo Tài Khoản
        </button>
    </form>

    <div class="mt-6 pt-6 border-t border-slate-100 text-center text-xs text-slate-600">
        Đã có tài khoản? 
        <a href="{{ route('login') }}" class="font-bold text-blue-600 hover:text-blue-700">Đăng nhập</a>
    </div>
</div>
@endsection

