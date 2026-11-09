@extends('layouts.admin')

@section('title', 'Quản Lý Danh Mục – TechStore Admin')
@section('header_title', 'Danh Mục Sản Phẩm')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <p class="text-xs text-slate-500">Phân cấp và cấu trúc danh mục kinh doanh máy tính & phụ kiện</p>
        <a href="{{ route('admin.categories.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-xs transition-colors flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Thêm Danh Mục Mới
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">Thứ tự</th>
                        <th class="px-5 py-3.5">Tên danh mục</th>
                        <th class="px-5 py-3.5">Đường dẫn (Slug)</th>
                        <th class="px-5 py-3.5">Danh mục cha</th>
                        <th class="px-5 py-3.5 text-center">Số sản phẩm</th>
                        <th class="px-5 py-3.5">Trạng thái</th>
                        <th class="px-5 py-3.5 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($categories as $cat)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3.5 font-bold text-slate-500">{{ $cat->sort_order }}</td>
                            <td class="px-5 py-3.5 font-bold text-slate-900">
                                @if($cat->parent_id)
                                    <span class="text-slate-400 mr-1.5">↳</span>
                                @endif
                                {{ $cat->name }}
                            </td>
                            <td class="px-5 py-3.5 font-mono text-slate-500">{{ $cat->slug }}</td>
                            <td class="px-5 py-3.5 text-slate-600">
                                {{ $cat->parent->name ?? '— Danh mục gốc' }}
                            </td>
                            <td class="px-5 py-3.5 text-center font-bold text-slate-800">
                                {{ $cat->products_count }}
                            </td>
                            <td class="px-5 py-3.5">
                                @if($cat->is_active)
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Hiển thị
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-600">
                                        Ẩn
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-2">
                                <a href="{{ route('admin.categories.edit', $cat->id) }}" class="text-blue-600 hover:text-blue-800 font-semibold">Sửa</a>
                                <button 
                                    type="button" 
                                    onclick="openConfirmModal({
                                        title: 'Xóa Danh Mục',
                                        message: 'Bạn có chắc chắn muốn xóa danh mục &quot;{{ addslashes($cat->name) }}&quot; không?',
                                        actionUrl: '{{ route('admin.categories.destroy', $cat->id) }}',
                                        method: 'DELETE'
                                    })" 
                                    class="text-rose-600 hover:text-rose-800 font-semibold"
                                >
                                    Xóa
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-slate-400">Chưa có danh mục nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $categories->links() }}
        </div>
    </div>

</div>
@endsection

