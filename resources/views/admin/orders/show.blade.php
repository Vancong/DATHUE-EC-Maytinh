@extends('layouts.admin')

@section('title', 'Xử Lý Đơn Hàng ' . $order->order_code . ' – TechStore Admin')
@section('header_title', 'Chi Tiết Đơn Hàng: ' . $order->order_code)

@section('content')
<div class="space-y-6 max-w-5xl">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-xl font-bold font-mono text-slate-900">{{ $order->order_code }}</h2>
                @php
                    $labels = [
                        'pending' => ['Chờ xử lý', 'bg-amber-50 text-amber-700 border-amber-200'],
                        'confirmed' => ['Đã xác nhận', 'bg-blue-50 text-blue-700 border-blue-200'],
                        'processing' => ['Đang đóng gói', 'bg-indigo-50 text-indigo-700 border-indigo-200'],
                        'shipping' => ['Đang giao hàng (Đã xuất kho)', 'bg-purple-50 text-purple-700 border-purple-200'],
                        'delivered' => ['Giao thành công (Đã thu COD)', 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                        'cancelled' => ['Đã hủy (Đã hoàn tồn)', 'bg-rose-50 text-rose-700 border-rose-200'],
                    ];
                    $st = $labels[$order->order_status] ?? ['N/A', 'bg-slate-100 text-slate-700'];
                @endphp
                <span class="px-2.5 py-1 rounded-md text-xs font-semibold border {{ $st[1] }}">
                    {{ $st[0] }}
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">Đặt lúc {{ $order->created_at->format('d/m/Y H:i:s') }}</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.orders.index') }}" class="px-3.5 py-2 rounded-xl border border-slate-300 text-slate-600 text-xs font-semibold hover:bg-slate-50">
                ← Quay lại danh sách
            </a>
            <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold shadow-xs transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                In Hóa Đơn / Phiếu Giao
            </a>
        </div>
    </div>

    <!-- Status Transition Card -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs space-y-4">
        <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Cập Nhật Tiến Trình Đơn Hàng</h3>

        @if($order->isDelivered())
            <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 font-semibold">
                ✓ Đơn hàng đã giao thành công và thu tiền COD hoàn tất. Không thể chỉnh sửa thêm.
            </div>
        @elseif($order->isCancelled())
            <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-800">
                <p class="font-bold">Đơn hàng này đã bị hủy.</p>
                <p class="mt-0.5">Lý do: {{ $order->cancelled_reason ?? 'Không có' }}</p>
                @if($order->restocked_at)
                    <p class="mt-1 text-emerald-700 font-semibold">✓ Đã hoàn trả tồn kho vào lúc {{ $order->restocked_at->format('d/m/Y H:i') }}.</p>
                @endif
            </div>
            @if($order->canBeRestocked())
                <div class="mt-4 p-4 rounded-xl bg-amber-50 border border-amber-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                        <h4 class="font-bold text-amber-900 text-xs flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            Hàng chưa được hoàn về kho
                        </h4>
                        <p class="text-[11px] text-amber-700 mt-0.5">Khi bưu tá thực tế đã bàn giao gói hàng hoàn về kho TechStore, bấm nút bên cạnh để nhập trả số lượng vào tồn kho sản phẩm.</p>
                    </div>
                    <form action="{{ route('admin.orders.restock', $order->id) }}" method="POST" onsubmit="return confirm('Bạn xác nhận kiện hàng đã thực sự được kiểm tra và nhận lại vào kho?')">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-xl shadow-xs transition-colors shrink-0">
                            Xác Nhận Hàng Về Kho & Hoàn Tồn
                        </button>
                    </form>
                </div>
            @endif
        @else
            <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                @csrf
                @method('PATCH')
                
                <div class="sm:col-span-5">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Chuyển sang trạng thái tiếp theo</label>
                    <select name="order_status" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-bold focus:outline-none focus:border-blue-600">
                        @if($order->isPending())
                            <option value="confirmed">1. Đã xác nhận đơn hàng (Confirmed)</option>
                            <option value="cancelled">Hủy đơn hàng (Cancelled - Hoàn tồn ngay)</option>
                        @elseif($order->isConfirmed())
                            <option value="processing">2. Đang đóng gói kho (Processing)</option>
                            <option value="shipping">3. Xuất kho giao hàng (Shipping)</option>
                            <option value="cancelled">Hủy đơn hàng (Cancelled - Hoàn tồn ngay)</option>
                        @elseif($order->isProcessing())
                            <option value="shipping">3. Xuất kho giao hàng (Shipping)</option>
                            <option value="cancelled">Hủy đơn hàng (Cancelled - Hoàn tồn ngay)</option>
                        @elseif($order->isShipping())
                            <option value="delivered">4. Giao hàng thành công (Delivered - Ghi nhận đã bán)</option>
                            <option value="failed_delivery">5. Giao thất bại / Chuyển hoàn (Chưa hoàn tồn)</option>
                            <option value="cancelled">Hủy đơn hàng (Cancelled)</option>
                        @elseif($order->isFailedDelivery())
                            <option value="shipping">Giao lại lần 2 (Shipping)</option>
                            <option value="cancelled">Hủy đơn hàng (Cancelled)</option>
                        @endif
                    </select>
                </div>

                <div class="sm:col-span-5">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Ghi chú / Lý do nếu hủy hoặc thất bại</label>
                    <input type="text" name="reason" placeholder="Nhập lý do nếu chọn hủy hoặc giao thất bại..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:outline-none focus:border-blue-600">
                </div>

                <div class="sm:col-span-2">
                    <button type="submit" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-xs transition-colors">
                        Cập Nhật
                    </button>
                </div>
            </form>

            @if($order->canBeRestocked())
                <div class="mt-4 p-4 rounded-xl bg-amber-50 border border-amber-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                        <h4 class="font-bold text-amber-900 text-xs flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            Hàng chưa được hoàn về kho
                        </h4>
                        <p class="text-[11px] text-amber-700 mt-0.5">Khi bưu tá thực tế đã bàn giao gói hàng hoàn về kho TechStore, bấm nút bên cạnh để nhập trả số lượng vào tồn kho sản phẩm.</p>
                    </div>
                    <form action="{{ route('admin.orders.restock', $order->id) }}" method="POST" onsubmit="return confirm('Bạn xác nhận kiện hàng đã thực sự được kiểm tra và nhận lại vào kho?')">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-xl shadow-xs transition-colors shrink-0">
                            Xác Nhận Hàng Về Kho & Hoàn Tồn
                        </button>
                    </form>
                </div>
            @endif
        @endif
    </div>

    <!-- Details Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-2 text-xs">
            <h4 class="font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">Thông Tin Khách Hàng</h4>
            <p class="text-sm font-bold text-slate-800">{{ $order->customer_name }}</p>
            <p class="text-slate-600">Số điện thoại: <strong class="text-slate-900 font-mono">{{ $order->customer_phone }}</strong></p>
            <p class="text-slate-600">Email: {{ $order->customer_email }}</p>
            <p class="text-slate-700 mt-2 bg-slate-50 p-2.5 rounded-lg border border-slate-100">{{ $order->shipping_address }}</p>
            @if($order->note)
                <p class="text-slate-500 italic">Khách ghi chú: "{{ $order->note }}"</p>
            @endif
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs space-y-2 text-xs">
            <h4 class="font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">Thanh Toán & Mã Ưu Đãi</h4>
            <p class="text-slate-600">Hình thức: <strong class="text-blue-700 font-bold">Thanh toán khi nhận hàng (COD)</strong></p>
            <p class="text-slate-600">Trạng thái: 
                <span class="font-bold {{ $order->payment_status === 'paid' ? 'text-emerald-600' : 'text-amber-600' }}">
                    {{ $order->payment_status === 'paid' ? 'ĐÃ THANH TOÁN' : 'CHƯA THANH TOÁN (THU COD)' }}
                </span>
            </p>
            @if($order->coupon)
                <p class="text-emerald-700 font-semibold">Voucher áp dụng: {{ $order->coupon->code }} (-{{ number_format($order->discount_amount, 0, ',', '.') }}đ)</p>
            @endif
            <div class="pt-2 border-t border-slate-100 space-y-1">
                <div class="flex justify-between text-slate-500">
                    <span>Tạm tính:</span>
                    <span class="text-slate-800 font-semibold">{{ number_format($order->subtotal, 0, ',', '.') }}đ</span>
                </div>
                <div class="flex justify-between text-slate-500">
                    <span>Phí giao hàng:</span>
                    <span class="text-emerald-600 font-semibold">Miễn phí</span>
                </div>
                <div class="flex justify-between text-sm font-black text-slate-900 pt-1 border-t border-slate-100">
                    <span>Tổng tiền thu COD:</span>
                    <span class="text-rose-600 text-base">{{ number_format($order->total_amount, 0, ',', '.') }}đ</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Items Snapshot Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-900">Chi Tiết Sản Phẩm (Bản Snapshot)</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3">Tên sản phẩm</th>
                        <th class="px-5 py-3">Mã SKU</th>
                        <th class="px-5 py-3 text-right">Đơn giá chụp</th>
                        <th class="px-5 py-3 text-center">Số lượng</th>
                        <th class="px-5 py-3 text-right">Thành tiền</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($order->items as $item)
                        <tr>
                            <td class="px-5 py-3.5 font-bold text-slate-900">{{ $item->product_name }}</td>
                            <td class="px-5 py-3.5 font-mono text-slate-500">{{ $item->product_sku }}</td>
                            <td class="px-5 py-3.5 text-right font-medium">{{ number_format($item->unit_price, 0, ',', '.') }}đ</td>
                            <td class="px-5 py-3.5 text-center font-bold text-slate-900 text-sm">{{ $item->quantity }}</td>
                            <td class="px-5 py-3.5 text-right font-black text-slate-900">{{ number_format($item->total_price, 0, ',', '.') }}đ</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

