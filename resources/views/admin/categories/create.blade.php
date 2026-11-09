@extends('layouts.admin')

@section('title', 'Thêm Danh Mục Mới – TechStore Admin')
@section('header_title', 'Tạo Mới Danh Mục')

@section('content')
<div class="max-w-2xl bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-xs space-y-6">

    <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
        <h3 class="text-sm font-bold text-slate-900">Thông Tin Danh Mục</h3>
        <a href="{{ route('admin.categories.index') }}" class="text-xs text-slate-500 hover:text-slate-800">← Quay lại danh sách</a>
    </div>

    <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Tên danh mục *</label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                value="{{ old('name') }}" 
                required 
                placeholder="Ví dụ: Laptop Gaming, Card Màn Hình..." 
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600"
            >
        </div>

        <div>
            <label for="slug" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Đường dẫn tĩnh (Slug - Để trống để tự tạo)</label>
            <input 
                type="text" 
                id="slug" 
                name="slug" 
                value="{{ old('slug') }}" 
                placeholder="laptop-gaming" 
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-mono focus:outline-none focus:border-blue-600"
            >
        </div>

        <div>
            <label for="parent_id" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Danh mục cha (Tùy chọn)</label>
            <select id="parent_id" name="parent_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600">
                <option value="">— Là danh mục gốc</option>
                @foreach($parents as $parent)
                    <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                        {{ $parent->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="sort_order" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Thứ tự hiển thị (Số nhỏ đứng trước)</label>
            <input 
                type="number" 
                id="sort_order" 
                name="sort_order" 
                value="{{ old('sort_order', 0) }}" 
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600"
            >
        </div>

        <div>
            <label for="description" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Mô tả ngắn</label>
            <textarea 
                id="description" 
                name="description" 
                rows="3" 
                placeholder="Giới thiệu về nhóm danh mục này..." 
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600"
            >{{ old('description') }}</textarea>
        </div>

        <div>
            <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
                <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                <span>Kích hoạt hiển thị trên website</span>
            </label>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
            <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl">Hủy</a>
            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs shadow-xs transition-colors">
                Lưu Danh Mục
            </button>
        </div>
    </form>

</div>
@endsection

