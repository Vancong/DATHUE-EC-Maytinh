@extends('layouts.admin')

@section('title', 'Thêm Kho Hàng Mới – TechStore Admin')
@section('header_title', 'Tạo Mới Kho Hàng')

@section('content')
<div class="max-w-2xl bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-xs space-y-6">

    <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
        <h3 class="text-sm font-bold text-slate-900">Thông Tin Kho Hàng</h3>
        <a href="{{ route('admin.warehouses.index') }}" class="text-xs text-slate-500 hover:text-slate-800">← Quay lại danh sách</a>
    </div>

    <form action="{{ route('admin.warehouses.store') }}" method="POST" class="space-y-4">
        @csrf
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Tên kho hàng *</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name') }}" 
                    required 
                    placeholder="Kho Tổng TP. Hồ Chí Minh" 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600"
                >
            </div>

            <div>
                <label for="code" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Mã kho *</label>
                <input 
                    type="text" 
                    id="code" 
                    name="code" 
                    value="{{ old('code') }}" 
                    required 
                    placeholder="WH-HCM-01" 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-mono uppercase focus:outline-none focus:border-blue-600"
                >
            </div>
        </div>

        <div>
            <label for="address" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Địa chỉ kho hàng</label>
            <input 
                type="text" 
                id="address" 
                name="address" 
                value="{{ old('address') }}" 
                placeholder="Số nhà, đường, quận/huyện, tỉnh/thành..." 
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600"
            >
        </div>

        <div>
            <label for="phone" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Số điện thoại quản lý kho</label>
            <input 
                type="text" 
                id="phone" 
                name="phone" 
                value="{{ old('phone') }}" 
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600"
            >
        </div>

        <div>
            <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
                <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                <span>Kích hoạt kho hàng lưu trữ</span>
            </label>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
            <a href="{{ route('admin.warehouses.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl">Hủy</a>
            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs shadow-xs transition-colors">
                Lưu Kho Hàng
            </button>
        </div>
    </form>

</div>
@endsection

