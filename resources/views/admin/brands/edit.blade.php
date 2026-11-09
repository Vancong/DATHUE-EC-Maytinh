@extends('layouts.admin')

@section('title', 'Chỉnh Sửa Thương Hiệu – TechStore Admin')
@section('header_title', 'Chỉnh Sửa Thương Hiệu: ' . $brand->name)

@section('content')
<div class="max-w-2xl bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-xs space-y-6">

    <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
        <h3 class="text-sm font-bold text-slate-900">Cập Nhật Thương Hiệu</h3>
        <a href="{{ route('admin.brands.index') }}" class="text-xs text-slate-500 hover:text-slate-800">← Quay lại danh sách</a>
    </div>

    <form action="{{ route('admin.brands.update', $brand->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Tên thương hiệu *</label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                value="{{ old('name', $brand->name) }}" 
                required 
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600"
            >
        </div>

        <div>
            <label for="slug" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Đường dẫn tĩnh (Slug)</label>
            <input 
                type="text" 
                id="slug" 
                name="slug" 
                value="{{ old('slug', $brand->slug) }}" 
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-mono focus:outline-none focus:border-blue-600"
            >
        </div>

        <div>
            <label for="logo" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">URL Logo</label>
            <input 
                type="text" 
                id="logo" 
                name="logo" 
                value="{{ old('logo', $brand->logo) }}" 
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600"
            >
        </div>

        <div>
            <label for="description" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Mô tả thương hiệu</label>
            <textarea 
                id="description" 
                name="description" 
                rows="3" 
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600"
            >{{ old('description', $brand->description) }}</textarea>
        </div>

        <div>
            <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $brand->is_active) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                <span>Kích hoạt thương hiệu</span>
            </label>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
            <a href="{{ route('admin.brands.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl">Hủy</a>
            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs shadow-xs transition-colors">
                Cập Nhật Thương Hiệu
            </button>
        </div>
    </form>

</div>
@endsection

