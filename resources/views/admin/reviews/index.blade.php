@extends('layouts.admin')

@section('title', 'Quản Lý Đánh Giá – TechStore Admin')
@section('header_title', 'Đánh Giá Sản Phẩm Từ Khách Hàng')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <p class="text-xs text-slate-500">Kiểm duyệt và quản lý các phản hồi, đánh giá sao từ khách hàng đã mua và nhận hàng</p>
    </div>

    <!-- Status Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-200 pb-3 text-xs font-semibold">
        <a href="{{ route('admin.reviews.index') }}" class="px-3.5 py-1.5 rounded-xl {{ !request('status') ? 'bg-slate-900 text-white font-bold' : 'text-slate-600 hover:bg-slate-100' }}">
            Tất Cả
        </a>
        <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}" class="px-3.5 py-1.5 rounded-xl {{ request('status') === 'pending' ? 'bg-amber-500 text-white font-bold' : 'text-slate-600 hover:bg-slate-100' }}">
            Chờ Duyệt (Pending)
        </a>
        <a href="{{ route('admin.reviews.index', ['status' => 'approved']) }}" class="px-3.5 py-1.5 rounded-xl {{ request('status') === 'approved' ? 'bg-emerald-600 text-white font-bold' : 'text-slate-600 hover:bg-slate-100' }}">
            Đã Hiển Thị (Approved)
        </a>
        <a href="{{ route('admin.reviews.index', ['status' => 'rejected']) }}" class="px-3.5 py-1.5 rounded-xl {{ request('status') === 'rejected' ? 'bg-rose-600 text-white font-bold' : 'text-slate-600 hover:bg-slate-100' }}">
            Đã Ẩn (Rejected)
        </a>
    </div>

    <!-- Reviews Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">Sản phẩm</th>
                        <th class="px-5 py-3.5">Khách hàng</th>
                        <th class="px-5 py-3.5">Đơn hàng</th>
                        <th class="px-5 py-3.5 text-center">Đánh giá</th>
                        <th class="px-5 py-3.5">Nội dung nhận xét</th>
                        <th class="px-5 py-3.5">Trạng thái</th>
                        <th class="px-5 py-3.5 text-right">Kiểm duyệt</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($reviews as $rev)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3.5">
                                <p class="font-bold text-slate-900 line-clamp-1 max-w-xs">{{ $rev->product->name ?? '—' }}</p>
                                <span class="text-[10px] font-mono text-slate-400">SKU: {{ $rev->product->sku ?? '—' }}</span>
                            </td>
                            <td class="px-5 py-3.5 font-semibold text-slate-800">
                                {{ $rev->user->name ?? 'Khách hàng' }}
                            </td>
                            <td class="px-5 py-3.5 font-mono text-blue-600">
                                {{ $rev->order->order_code ?? '—' }}
                            </td>
                            <td class="px-5 py-3.5 text-center text-amber-500 font-bold text-sm">
                                {{ str_repeat('★', $rev->rating) }}{{ str_repeat('☆', 5 - $rev->rating) }}
                            </td>
                            <td class="px-5 py-3.5 text-slate-600 max-w-sm leading-relaxed">
                                {{ $rev->comment }}
                            </td>
                            <td class="px-5 py-3.5">
                                @if($rev->status === 'approved')
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Hiển thị</span>
                                @elseif($rev->status === 'pending')
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">Chờ duyệt</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">Từ chối</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-2 whitespace-nowrap">
                                @if($rev->status !== 'approved')
                                    <form action="{{ route('admin.reviews.update-status', $rev->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="text-xs font-bold text-emerald-600 hover:text-emerald-800">Duyệt</button>
                                    </form>
                                @endif

                                @if($rev->status !== 'rejected')
                                    <form action="{{ route('admin.reviews.update-status', $rev->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="text-xs font-bold text-amber-600 hover:text-amber-800">Ẩn</button>
                                    </form>
                                @endif

                                <button 
                                    type="button" 
                                    onclick="openConfirmModal({
                                        title: 'Xóa Đánh Giá',
                                        message: 'Bạn có chắc chắn muốn xóa đánh giá này?',
                                        actionUrl: '{{ route('admin.reviews.destroy', $rev->id) }}',
                                        method: 'DELETE'
                                    })" 
                                    class="text-xs font-bold text-rose-600 hover:text-rose-800"
                                >
                                    Xóa
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-slate-400">Không có đánh giá nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $reviews->links() }}
        </div>
    </div>

</div>
@endsection

