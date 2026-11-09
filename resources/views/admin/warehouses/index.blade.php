@extends('layouts.admin')

@section('title', 'Quản Lý Kho Hàng – TechStore Admin')
@section('header_title', 'Hệ Thống Kho Hàng')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <p class="text-xs text-slate-500">Các điểm kho lưu trữ, đóng gói và xuất hàng toàn quốc</p>
        <a href="{{ route('admin.warehouses.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-xs transition-colors flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Thêm Kho Hàng Mới
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">Mã kho</th>
                        <th class="px-5 py-3.5">Tên kho hàng</th>
                        <th class="px-5 py-3.5">Địa chỉ</th>
                        <th class="px-5 py-3.5">Số điện thoại</th>
                        <th class="px-5 py-3.5 text-center">Sản phẩm có tồn</th>
                        <th class="px-5 py-3.5">Trạng thái</th>
                        <th class="px-5 py-3.5 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($warehouses as $wh)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3.5 font-mono font-bold text-blue-600">{{ $wh->code }}</td>
                            <td class="px-5 py-3.5 font-bold text-slate-900">{{ $wh->name }}</td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $wh->address ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $wh->phone ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-center font-bold text-slate-800">{{ $wh->inventory_stocks_count }}</td>
                            <td class="px-5 py-3.5">
                                @if($wh->is_active)
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Hoạt động</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-600">Đóng</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-2">
                                <a href="{{ route('admin.warehouses.edit', $wh->id) }}" class="text-blue-600 hover:text-blue-800 font-semibold">Sửa</a>
                                <button 
                                    type="button" 
                                    onclick="openConfirmModal({
                                        title: 'Xóa Kho Hàng',
                                        message: 'Bạn có chắc chắn muốn xóa kho &quot;{{ addslashes($wh->name) }}&quot;?',
                                        actionUrl: '{{ route('admin.warehouses.destroy', $wh->id) }}',
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
                            <td colspan="7" class="px-5 py-8 text-center text-slate-400">Chưa có kho hàng nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $warehouses->links() }}
        </div>
    </div>

</div>
@endsection

