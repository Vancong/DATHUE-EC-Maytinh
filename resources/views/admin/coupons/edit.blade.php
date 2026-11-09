@extends('layouts.admin')

@section('title', 'Chỉnh Sửa Mã Giảm Giá – TechStore Admin')
@section('header_title', 'Chỉnh Sửa Mã Giảm Giá: ' . $coupon->code)

@section('content')
<div class="max-w-2xl bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-xs space-y-6">

    <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
        <h3 class="text-sm font-bold text-slate-900">Cập Nhật Mã Giảm Giá</h3>
        <a href="{{ route('admin.coupons.index') }}" class="text-xs text-slate-500 hover:text-slate-800">← Quay lại danh sách</a>
    </div>

    <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="code" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Mã Coupon *</label>
                <input 
                    type="text" 
                    id="code" 
                    name="code" 
                    value="{{ old('code', $coupon->code) }}" 
                    required 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-mono font-bold uppercase focus:outline-none focus:border-blue-600"
                >
            </div>

            <div>
                <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Tên chương trình khuyến mãi *</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name', $coupon->name) }}" 
                    required 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600"
                >
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label for="discount_type" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Loại giảm giá *</label>
                <select id="discount_type" name="discount_type" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600">
                    <option value="fixed" {{ old('discount_type', $coupon->discount_type) === 'fixed' ? 'selected' : '' }}>Số tiền cố định (VNĐ)</option>
                    <option value="percentage" {{ old('discount_type', $coupon->discount_type) === 'percentage' ? 'selected' : '' }}>Theo phần trăm (%)</option>
                </select>
            </div>

            <div>
                <label for="discount_value" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Giá trị giảm *</label>
                <input 
                    type="number" 
                    id="discount_value" 
                    name="discount_value" 
                    value="{{ old('discount_value', $coupon->discount_value) }}" 
                    required 
                    min="1"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-bold text-rose-600 focus:outline-none focus:border-blue-600"
                >
            </div>

            <div>
                <label for="max_discount" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Giảm tối đa (Nếu %)</label>
                <input 
                    type="number" 
                    id="max_discount" 
                    name="max_discount" 
                    value="{{ old('max_discount', $coupon->max_discount) }}" 
                    min="0"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600"
                >
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="min_order_amount" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Giá trị đơn hàng tối thiểu (VNĐ)</label>
                <input 
                    type="number" 
                    id="min_order_amount" 
                    name="min_order_amount" 
                    value="{{ old('min_order_amount', $coupon->min_order_amount) }}" 
                    min="0"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600"
                >
            </div>

            <div>
                <label for="usage_limit" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Giới hạn số lượt dùng (Đã dùng: {{ $coupon->used_count }})</label>
                <input 
                    type="number" 
                    id="usage_limit" 
                    name="usage_limit" 
                    value="{{ old('usage_limit', $coupon->usage_limit) }}" 
                    min="1"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600"
                >
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="start_date" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Ngày bắt đầu</label>
                <input 
                    type="date" 
                    id="start_date" 
                    name="start_date" 
                    value="{{ old('start_date', $coupon->start_date ? $coupon->start_date->format('Y-m-d') : '') }}" 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600"
                >
            </div>

            <div>
                <label for="end_date" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Ngày kết thúc</label>
                <input 
                    type="date" 
                    id="end_date" 
                    name="end_date" 
                    value="{{ old('end_date', $coupon->end_date ? $coupon->end_date->format('Y-m-d') : '') }}" 
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600"
                >
            </div>
        </div>

        <div>
            <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $coupon->is_active) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                <span>Kích hoạt mã giảm giá ngay</span>
            </label>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
            <a href="{{ route('admin.coupons.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl">Hủy</a>
            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs shadow-xs transition-colors">
                Cập Nhật Mã Giảm Giá
            </button>
        </div>
    </form>

</div>
@endsection

