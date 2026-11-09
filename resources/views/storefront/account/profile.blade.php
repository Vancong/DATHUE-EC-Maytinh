@extends('layouts.storefront')

@section('title', 'Thông Tin Tài Khoản – TechStore')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="border-b border-slate-200 pb-4 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Thông Tin Tài Khoản</h1>
            <p class="text-xs text-slate-500 mt-0.5">Cập nhật thông tin giao hàng và bảo mật mật khẩu</p>
        </div>
        <a href="{{ route('account.orders') }}" class="text-xs font-semibold text-blue-600 hover:underline">
            Lịch sử đơn hàng →
        </a>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-xs">
        <form action="{{ route('account.profile.update') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Họ và tên *</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name', $user->name) }}" 
                    required 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600"
                >
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Email (Cố định)</label>
                <input 
                    type="email" 
                    id="email" 
                    value="{{ $user->email }}" 
                    disabled 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-500 text-sm"
                >
            </div>

            <div>
                <label for="phone" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Số điện thoại *</label>
                <input 
                    type="text" 
                    id="phone" 
                    name="phone" 
                    value="{{ old('phone', $user->phone) }}" 
                    required 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600"
                >
            </div>

            <div>
                <label for="address" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Địa chỉ giao hàng mặc định</label>
                <textarea 
                    id="address" 
                    name="address" 
                    rows="2" 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600"
                >{{ old('address', $user->address) }}</textarea>
            </div>

            <div class="pt-5 border-t border-slate-100 space-y-4">
                <h3 class="text-sm font-bold text-slate-900">Đổi Mật Khẩu (Để trống nếu không đổi)</h3>
                
                <div>
                    <label for="current_password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Mật khẩu hiện tại</label>
                    <input 
                        type="password" 
                        id="current_password" 
                        name="current_password" 
                        placeholder="••••••••" 
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600"
                    >
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="new_password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Mật khẩu mới</label>
                        <input 
                            type="password" 
                            id="new_password" 
                            name="new_password" 
                            placeholder="Tối thiểu 6 ký tự" 
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600"
                        >
                    </div>
                    <div>
                        <label for="new_password_confirmation" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Xác nhận mật khẩu mới</label>
                        <input 
                            type="password" 
                            id="new_password_confirmation" 
                            name="new_password_confirmation" 
                            placeholder="Nhập lại mật khẩu mới" 
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600"
                        >
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-sm shadow-xs transition-colors">
                    Lưu Thay Đổi
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

