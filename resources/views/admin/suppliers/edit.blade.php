@extends('layouts.admin')

@section('title', 'Chỉnh Sửa Nhà Cung Cấp – TechStore Admin')
@section('header_title', 'Chỉnh Sửa NCC: ' . $supplier->name)

@section('content')
<div class="max-w-2xl bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-xs space-y-6">

    <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
        <h3 class="text-sm font-bold text-slate-900">Cập Nhật Nhà Cung Cấp</h3>
        <a href="{{ route('admin.suppliers.index') }}" class="text-xs text-slate-500 hover:text-slate-800">← Quay lại danh sách</a>
    </div>

    <form action="{{ route('admin.suppliers.update', $supplier->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Tên nhà cung cấp *</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name', $supplier->name) }}" 
                    required 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600"
                >
            </div>

            <div>
                <label for="code" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Mã NCC *</label>
                <input 
                    type="text" 
                    id="code" 
                    name="code" 
                    value="{{ old('code', $supplier->code) }}" 
                    required 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-mono uppercase focus:outline-none focus:border-blue-600"
                >
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label for="contact_name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Người đại diện</label>
                <input 
                    type="text" 
                    id="contact_name" 
                    name="contact_name" 
                    value="{{ old('contact_name', $supplier->contact_name) }}" 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600"
                >
            </div>

            <div>
                <label for="phone" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Số điện thoại</label>
                <input 
                    type="text" 
                    id="phone" 
                    name="phone" 
                    value="{{ old('phone', $supplier->phone) }}" 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600"
                >
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Email liên hệ</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email', $supplier->email) }}" 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600"
                >
            </div>
        </div>

        <div>
            <label for="address" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Địa chỉ trụ sở / kho</label>
            <input 
                type="text" 
                id="address" 
                name="address" 
                value="{{ old('address', $supplier->address) }}" 
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600"
            >
        </div>

        <div>
            <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $supplier->is_active) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                <span>Kích hoạt hợp tác nhà cung cấp</span>
            </label>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
            <a href="{{ route('admin.suppliers.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl">Hủy</a>
            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs shadow-xs transition-colors">
                Cập Nhật Nhà Cung Cấp
            </button>
        </div>
    </form>

</div>
@endsection

