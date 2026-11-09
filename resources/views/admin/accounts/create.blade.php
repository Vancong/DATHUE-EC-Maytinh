@extends('layouts.admin')

@section('title', 'Thêm Quản Trị Viên – TechStore Admin')
@section('header_title', 'Tạo Mới Tài Khoản Quản Trị')

@section('content')
<div class="max-w-xl bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-xs space-y-6">

    <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
        <h3 class="text-sm font-bold text-slate-900">Thông Tin Quản Trị Viên</h3>
        <a href="{{ route('admin.accounts.index') }}" class="text-xs text-slate-500 hover:text-slate-800">← Quay lại danh sách</a>
    </div>

    <form action="{{ route('admin.accounts.store') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Họ và tên *</label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                value="{{ old('name') }}" 
                required 
                placeholder="Nguyễn Văn Quản Trị" 
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600"
            >
        </div>

        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Email quản trị (Dùng đăng nhập) *</label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                value="{{ old('email') }}" 
                required 
                placeholder="admin2@techstore.vn" 
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600"
            >
        </div>

        <div>
            <label for="phone" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Số điện thoại liên hệ</label>
            <input 
                type="text" 
                id="phone" 
                name="phone" 
                value="{{ old('phone') }}" 
                placeholder="0912345678" 
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600"
            >
        </div>

        <div>
            <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Mật khẩu khởi tạo *</label>
            <input 
                type="password" 
                id="password" 
                name="password" 
                required 
                placeholder="Tối thiểu 6 ký tự" 
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600"
            >
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
            <a href="{{ route('admin.accounts.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl">Hủy</a>
            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs shadow-xs transition-colors">
                Tạo Tài Khoản Admin
            </button>
        </div>
    </form>

</div>
@endsection

