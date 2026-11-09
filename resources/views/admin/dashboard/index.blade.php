@extends('layouts.admin')

@section('title', 'Bảng Điều Khiển Tổng Quan – TechStore Admin')
@section('header_title', 'Tổng Quan Hoạt Động & Doanh Thu')

@section('content')
<div class="space-y-6">

    <!-- KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Doanh thu -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Doanh Thu Đã Thu</p>
                    <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($totalRevenue, 0, ',', '.') }}đ</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="text-[11px] text-slate-500 mt-3 flex items-center gap-1">
                <span class="text-emerald-600 font-semibold">✓ Đơn thanh toán hoàn tất</span>
            </p>
        </div>

        <!-- Đơn hàng -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Tổng Đơn Hàng</p>
                    <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($totalOrders) }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
            </div>
            <p class="text-[11px] text-slate-500 mt-3 flex items-center gap-1">
                <span>Bao gồm tất cả trạng thái đơn</span>
            </p>
        </div>

        <!-- Sản phẩm -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Tổng Sản Phẩm</p>
                    <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($totalProducts) }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>
            <p class="text-[11px] text-slate-500 mt-3">Đang mở bán trên toàn hệ thống</p>
        </div>

        <!-- Khách hàng -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Khách Hàng</p>
                    <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($totalCustomers) }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>
            <p class="text-[11px] text-slate-500 mt-3">Thành viên TechStore</p>
        </div>
    </div>

    <!-- Charts Section (Chart.js) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Monthly Revenue Chart (8 cols) -->
        <div class="lg:col-span-8 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Biểu Đồ Doanh Thu Năm {{ date('Y') }}</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Thống kê doanh thu thực tế theo 12 tháng (VNĐ)</p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg">12 Tháng</span>
            </div>
            <div class="h-72">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Orders Status Breakdown (4 cols) -->
        <div class="lg:col-span-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="mb-4">
                <h3 class="text-sm font-bold text-slate-900">Tỷ Lệ Trạng Thái Đơn Hàng</h3>
                <p class="text-xs text-slate-500 mt-0.5">Phân bổ các giai đoạn xử lý đơn</p>
            </div>
            <div class="h-72 flex items-center justify-center">
                <canvas id="orderStatusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Lower Grid: Recent Orders & Low Stock Warning -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Recent Orders (7 cols) -->
        <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Đơn Hàng Mới Nhất</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Các đơn hàng vừa được khách đặt</p>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-blue-600 hover:underline">
                    Xem tất cả →
                </a>
            </div>

            <div class="divide-y divide-slate-100 overflow-x-auto">
                <table class="w-full text-xs text-left text-slate-700">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider">
                        <tr>
                            <th class="px-5 py-3">Mã đơn</th>
                            <th class="px-5 py-3">Khách hàng</th>
                            <th class="px-5 py-3">Tổng tiền</th>
                            <th class="px-5 py-3">Trạng thái</th>
                            <th class="px-5 py-3 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentOrders as $order)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-5 py-3.5 font-mono font-bold text-blue-600">
                                    {{ $order->order_code }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="font-semibold text-slate-900">{{ $order->customer_name }}</p>
                                    <span class="text-[11px] text-slate-500">{{ $order->customer_phone }}</span>
                                </td>
                                <td class="px-5 py-3.5 font-bold text-slate-900">
                                    {{ number_format($order->total_amount, 0, ',', '.') }}đ
                                </td>
                                <td class="px-5 py-3.5">
                                    @php
                                        $labels = [
                                            'pending' => ['Chờ xử lý', 'bg-amber-50 text-amber-700 border-amber-200'],
                                            'confirmed' => ['Đã xác nhận', 'bg-blue-50 text-blue-700 border-blue-200'],
                                            'processing' => ['Đang đóng gói', 'bg-indigo-50 text-indigo-700 border-indigo-200'],
                                            'shipping' => ['Đang giao', 'bg-purple-50 text-purple-700 border-purple-200'],
                                            'delivered' => ['Thành công', 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                                            'cancelled' => ['Đã hủy', 'bg-rose-50 text-rose-700 border-rose-200'],
                                        ];
                                        $st = $labels[$order->order_status] ?? ['N/A', 'bg-slate-100 text-slate-700'];
                                    @endphp
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold border {{ $st[1] }}">
                                        {{ $st[0] }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="text-xs font-semibold text-blue-600 hover:underline">Chi tiết</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-slate-400">Chưa có đơn hàng nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Low Stock Warning (5 cols) -->
        <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                        Cảnh Báo Sắp Hết Hàng
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Sản phẩm có số lượng tồn ≤ Ngưỡng báo</p>
                </div>
                <a href="{{ route('admin.inventory.index') }}" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow-xs transition-colors">
                    + Nhập Kho
                </a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($lowStockItems as $p)
                    <div class="p-4 flex items-center justify-between text-xs">
                        <div class="space-y-0.5 max-w-[220px]">
                            <p class="font-bold text-slate-900 truncate">{{ $p->name }}</p>
                            <span class="text-[10px] font-mono text-slate-400">SKU: {{ $p->sku }}</span>
                        </div>
                        <div class="text-right">
                            <span class="font-bold {{ $p->stock_quantity <= 0 ? 'text-rose-600' : 'text-amber-600' }}">
                                Tồn kho: {{ $p->stock_quantity }}
                            </span>
                            <span class="block text-[10px] text-slate-400">(Báo động khi ≤ {{ $p->min_stock }})</span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-xs text-slate-400">
                        ✓ Tồn kho các sản phẩm hiện đều đạt mức an toàn.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // 1. Revenue Line/Bar Chart
        const revCtx = document.getElementById('revenueChart');
        if (revCtx && window.Chart) {
            new Chart(revCtx, {
                type: 'bar',
                data: {
                    labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
                    datasets: [{
                        label: 'Doanh thu (VNĐ)',
                        data: @json($monthlyRevenue),
                        backgroundColor: '#2563eb',
                        borderRadius: 6,
                        hoverBackgroundColor: '#1d4ed8'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return new Intl.NumberFormat('vi-VN').format(context.raw) + ' VNĐ';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    if (value >= 1000000) return (value / 1000000) + 'M';
                                    return value;
                                }
                            }
                        }
                    }
                }
            });
        }

        // 2. Order Status Doughnut Chart
        const statusCtx = document.getElementById('orderStatusChart');
        if (statusCtx && window.Chart) {
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Chờ xử lý', 'Đã xác nhận', 'Đang đóng gói', 'Đang giao', 'Thành công', 'Đã hủy'],
                    datasets: [{
                        data: [
                            {{ $statusData['pending'] }},
                            {{ $statusData['confirmed'] }},
                            {{ $statusData['processing'] }},
                            {{ $statusData['shipping'] }},
                            {{ $statusData['delivered'] }},
                            {{ $statusData['cancelled'] }}
                        ],
                        backgroundColor: [
                            '#f59e0b', // amber
                            '#3b82f6', // blue
                            '#6366f1', // indigo
                            '#a855f7', // purple
                            '#10b981', // emerald
                            '#f43f5e'  // rose
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                font: { size: 10 }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection
