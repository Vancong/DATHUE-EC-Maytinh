@extends('layouts.admin')

@section('title', 'Quản Lý Khách Hàng – TechStore Admin')
@section('header_title', 'Danh Sách Khách Hàng')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <p class="text-xs text-slate-500">Quản lý tài khoản khách hàng, lịch sử mua sắm và khóa/mở khóa tài khoản</p>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
        <form action="{{ route('admin.customers.index') }}" method="GET" class="flex items-center gap-3">
            <input 
                type="text" 
                name="keyword" 
                value="{{ request('keyword') }}" 
                placeholder="Tìm theo họ tên, email hoặc số điện thoại..." 
                class="flex-1 px-3.5 py-2 rounded-xl border border-slate-300 text-xs focus:outline-none focus:border-blue-600"
            >
            <button type="submit" class="py-2 px-5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-semibold shadow-xs transition-colors">
                Tìm Kiếm
            </button>
            <a href="{{ route('admin.customers.index') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold">
                Xóa
            </a>
        </form>
    </div>

    <!-- Customers Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">Khách hàng</th>
                        <th class="px-5 py-3.5">Điện thoại / Địa chỉ</th>
                        <th class="px-5 py-3.5 text-center">Số đơn hàng</th>
                        <th class="px-5 py-3.5 text-right">Tổng chi tiêu (Paid)</th>
                        <th class="px-5 py-3.5">Ngày tham gia</th>
                        <th class="px-5 py-3.5">Trạng thái</th>
                        <th class="px-5 py-3.5 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($customers as $cust)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3.5">
                                <p class="font-bold text-slate-900">{{ $cust->name }}</p>
                                <span class="text-[11px] text-slate-500">{{ $cust->email }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <p class="font-semibold text-slate-800">{{ $cust->phone ?? '—' }}</p>
                                <span class="text-[11px] text-slate-500 max-w-xs truncate block">{{ $cust->address ?? 'Chưa cập nhật' }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-center font-bold text-slate-800 text-sm">
                                {{ $cust->orders_count }}
                            </td>
                            <td class="px-5 py-3.5 text-right font-black text-slate-900">
                                {{ number_format($cust->orders_sum_total_amount ?? 0, 0, ',', '.') }}đ
                            </td>
                            <td class="px-5 py-3.5 text-slate-500">
                                {{ $cust->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-5 py-3.5">
                                @if($cust->is_active)
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Hoạt động</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">Đã khóa</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-2">
                                <a href="{{ route('admin.customers.show', $cust->id) }}" class="text-blue-600 hover:text-blue-800 font-semibold">Xem chi tiết</a>
                                <form action="{{ route('admin.customers.toggle-active', $cust->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Bạn có chắc chắn muốn thay đổi trạng thái tài khoản khách hàng này?')" class="text-xs font-semibold {{ $cust->is_active ? 'text-rose-600 hover:text-rose-800' : 'text-emerald-600 hover:text-emerald-800' }}">
                                        {{ $cust->is_active ? 'Khóa' : 'Mở khóa' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-slate-400">Không tìm thấy khách hàng nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $customers->links() }}
        </div>
    </div>

</div>
@endsection

