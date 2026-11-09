@extends('layouts.admin')

@section('title', 'Quản Lý Sản Phẩm – TechStore Admin')
@section('header_title', 'Danh Sách Sản Phẩm')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <p class="text-xs text-slate-500">Quản lý toàn bộ danh mục máy tính, laptop gaming và linh kiện</p>
        <a href="{{ route('admin.products.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-xs transition-colors flex items-center gap-1.5 self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Thêm Sản Phẩm Mới
        </a>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
        <form action="{{ route('admin.products.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <input 
                type="text" 
                name="keyword" 
                value="{{ request('keyword') }}" 
                placeholder="Tìm tên sản phẩm hoặc mã SKU..." 
                class="px-3.5 py-2 rounded-xl border border-slate-300 text-xs focus:outline-none focus:border-blue-600"
            >

            <select name="category_id" class="px-3 py-2 rounded-xl border border-slate-300 text-xs focus:outline-none focus:border-blue-600">
                <option value="">— Tất cả danh mục</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>

            <select name="brand_id" class="px-3 py-2 rounded-xl border border-slate-300 text-xs focus:outline-none focus:border-blue-600">
                <option value="">— Tất cả thương hiệu</option>
                @foreach($brands as $b)
                    <option value="{{ $b->id }}" {{ request('brand_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                @endforeach
            </select>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-4 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-semibold shadow-xs transition-colors">
                    Lọc Dữ Liệu
                </button>
                <a href="{{ route('admin.products.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold">
                    Xóa
                </a>
            </div>
        </form>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">Sản phẩm</th>
                        <th class="px-5 py-3.5">SKU</th>
                        <th class="px-5 py-3.5">Danh mục & Hãng</th>
                        <th class="px-5 py-3.5 text-right">Giá bán</th>
                        <th class="px-5 py-3.5 text-center">Tồn / Giữ / Khả dụng</th>
                        <th class="px-5 py-3.5">Trạng thái</th>
                        <th class="px-5 py-3.5 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($products as $p)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3.5 flex items-center gap-3">
                                <img src="{{ $p->thumbnail ?? 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?auto=format&fit=crop&w=100&q=80' }}" alt="thumb" class="w-10 h-10 object-contain rounded-lg border border-slate-200 bg-slate-50 p-1 shrink-0">
                                <div>
                                    <h4 class="font-bold text-slate-900 line-clamp-1 max-w-xs">{{ $p->name }}</h4>
                                    @if($p->is_featured)
                                        <span class="inline-block mt-0.5 px-1.5 py-0.2 bg-amber-100 text-amber-800 text-[9px] font-bold rounded">Nổi bật</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-3.5 font-mono text-slate-500 font-bold">{{ $p->sku }}</td>
                            <td class="px-5 py-3.5">
                                <p class="text-slate-800 font-semibold">{{ $p->category->name ?? '—' }}</p>
                                <span class="text-[10px] text-blue-600 font-medium">{{ $p->brand->name ?? '—' }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-right font-black text-slate-900">
                                {{ number_format($p->current_price, 0, ',', '.') }}đ
                                @if($p->is_on_sale)
                                    <span class="block text-[10px] text-slate-400 line-through font-normal">{{ number_format($p->price, 0, ',', '.') }}đ</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="font-bold {{ $p->available_stock <= 5 ? 'text-rose-600' : 'text-slate-800' }}">
                                    {{ $p->total_quantity }} / {{ $p->reserved_quantity }} / <strong>{{ $p->available_stock }}</strong>
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                @if($p->is_active)
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Kinh doanh</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-600">Ngừng bán</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-2">
                                <a href="{{ route('admin.products.edit', $p->id) }}" class="text-blue-600 hover:text-blue-800 font-semibold">Sửa</a>
                                <button 
                                    type="button" 
                                    onclick="openConfirmModal({
                                        title: 'Xóa Sản Phẩm',
                                        message: 'Bạn có chắc chắn muốn xóa sản phẩm &quot;{{ addslashes($p->name) }}&quot;?',
                                        actionUrl: '{{ route('admin.products.destroy', $p->id) }}',
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
                            <td colspan="7" class="px-5 py-8 text-center text-slate-400">Không tìm thấy sản phẩm nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $products->links() }}
        </div>
    </div>

</div>
@endsection

