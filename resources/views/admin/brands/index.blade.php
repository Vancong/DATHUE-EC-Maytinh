@extends('layouts.admin')

@section('title', 'Quản Lý Thương Hiệu – TechStore Admin')
@section('header_title', 'Thương Hiệu Phân Phối')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <p class="text-xs text-slate-500">Đối tác và các hãng máy tính, phần cứng chính hãng</p>
        <a href="{{ route('admin.brands.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-xs transition-colors flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Thêm Thương Hiệu Mới
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">Tên thương hiệu</th>
                        <th class="px-5 py-3.5">Slug</th>
                        <th class="px-5 py-3.5">Mô tả</th>
                        <th class="px-5 py-3.5 text-center">Số sản phẩm</th>
                        <th class="px-5 py-3.5">Trạng thái</th>
                        <th class="px-5 py-3.5 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($brands as $brand)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3.5 font-bold text-slate-900">{{ $brand->name }}</td>
                            <td class="px-5 py-3.5 font-mono text-slate-500">{{ $brand->slug }}</td>
                            <td class="px-5 py-3.5 text-slate-600 max-w-xs truncate">{{ $brand->description ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-center font-bold text-slate-800">{{ $brand->products_count }}</td>
                            <td class="px-5 py-3.5">
                                @if($brand->is_active)
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Hiển thị</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-600">Ẩn</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-2">
                                <a href="{{ route('admin.brands.edit', $brand->id) }}" class="text-blue-600 hover:text-blue-800 font-semibold">Sửa</a>
                                <button 
                                    type="button" 
                                    onclick="openConfirmModal({
                                        title: 'Xóa Thương Hiệu',
                                        message: 'Bạn có chắc chắn muốn xóa thương hiệu &quot;{{ addslashes($brand->name) }}&quot;?',
                                        actionUrl: '{{ route('admin.brands.destroy', $brand->id) }}',
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
                            <td colspan="6" class="px-5 py-8 text-center text-slate-400">Chưa có thương hiệu nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $brands->links() }}
        </div>
    </div>

</div>
@endsection

