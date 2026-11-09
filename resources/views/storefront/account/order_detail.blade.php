@extends('layouts.storefront')

@section('title', 'Chi Tiết Đơn Hàng ' . $order->order_code . ' – TechStore')

@section('content')
<div class="space-y-6">

    <!-- Breadcrumb -->
    <nav class="flex text-xs text-slate-500 gap-2 items-center">
        <a href="{{ route('home') }}" class="hover:text-blue-600 transition-colors">Trang chủ</a>
        <span>/</span>
        <a href="{{ route('account.orders') }}" class="hover:text-blue-600 transition-colors">Lịch sử đơn hàng</a>
        <span>/</span>
        <span class="text-slate-900 font-semibold font-mono">{{ $order->order_code }}</span>
    </nav>

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-black text-slate-900 tracking-tight font-mono">{{ $order->order_code }}</h1>
                @php
                    $statusBadges = [
                        'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                        'confirmed' => 'bg-blue-50 text-blue-700 border-blue-200',
                        'processing' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                        'shipping' => 'bg-purple-50 text-purple-700 border-purple-200',
                        'delivered' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        'cancelled' => 'bg-rose-50 text-rose-700 border-rose-200',
                    ];
                    $statusLabels = [
                        'pending' => 'Chờ xử lý',
                        'confirmed' => 'Đã xác nhận',
                        'processing' => 'Đang đóng gói',
                        'shipping' => 'Đang vận chuyển',
                        'delivered' => 'Giao thành công',
                        'cancelled' => 'Đã hủy',
                    ];
                @endphp
                <span class="px-2.5 py-1 rounded-md text-xs font-semibold border {{ $statusBadges[$order->order_status] ?? 'bg-slate-100' }}">
                    {{ $statusLabels[$order->order_status] ?? $order->order_status }}
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">Đặt lúc {{ $order->created_at->format('d/m/Y H:i:s') }}</p>
        </div>

        @if($order->canBeCancelled())
            <button 
                type="button" 
                onclick="document.getElementById('cancel-order-modal').classList.remove('hidden')" 
                class="px-4 py-2 bg-rose-50 hover:bg-rose-600 hover:text-white text-rose-700 text-xs font-semibold rounded-xl border border-rose-200 transition-colors"
            >
                Hủy Đơn Hàng Này
            </button>
        @endif
    </div>

    <!-- Status Timeline / Step Indicator -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
        <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-6">Tiến Trình Đơn Hàng</h3>
        
        @if($order->isCancelled())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-800">
                <p class="font-bold">Đơn hàng này đã bị hủy</p>
                <p class="mt-1">Lý do: {{ $order->cancelled_reason ?? 'Khách hàng yêu cầu hủy đơn.' }}</p>
            </div>
        @else
            @php
                $steps = [
                    'pending' => 'Chờ xử lý',
                    'confirmed' => 'Đã xác nhận',
                    'processing' => 'Đóng gói kho',
                    'shipping' => 'Đang giao hàng',
                    'delivered' => 'Đã giao thành công'
                ];
                $stepKeys = array_keys($steps);
                $currentIndex = array_search($order->order_status, $stepKeys);
                if ($currentIndex === false) $currentIndex = 0;
            @endphp
            <div class="grid grid-cols-5 gap-2 text-center text-xs">
                @foreach($steps as $key => $label)
                    @php $idx = array_search($key, $stepKeys); @endphp
                    <div class="space-y-2">
                        <div class="w-8 h-8 rounded-full mx-auto flex items-center justify-center font-bold text-xs {{ $idx <= $currentIndex ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-400' }}">
                            {{ $idx + 1 }}
                        </div>
                        <span class="block {{ $idx <= $currentIndex ? 'font-bold text-slate-900' : 'text-slate-400' }}">
                            {{ $label }}
                        </span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Delivery & Summary Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs space-y-3 text-xs">
            <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2">Địa Chỉ Giao Hàng</h3>
            <p class="font-bold text-slate-800">{{ $order->customer_name }}</p>
            <p class="text-slate-600">Số điện thoại: <strong class="text-slate-800">{{ $order->customer_phone }}</strong></p>
            <p class="text-slate-600">Email: {{ $order->customer_email }}</p>
            <p class="text-slate-700 mt-2 bg-slate-50 p-2.5 rounded-lg border border-slate-100">{{ $order->shipping_address }}</p>
            @if($order->note)
                <p class="text-slate-500 italic">Ghi chú: "{{ $order->note }}"</p>
            @endif
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs space-y-3 text-xs">
            <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2">Thông Tin Thanh Toán</h3>
            <p class="text-slate-600">Phương thức: <strong class="text-blue-700">Thanh toán khi nhận hàng (COD)</strong></p>
            <p class="text-slate-600">Trạng thái thanh toán: 
                <span class="font-bold {{ $order->payment_status === 'paid' ? 'text-emerald-600' : 'text-amber-600' }}">
                    {{ $order->payment_status === 'paid' ? 'ĐÃ THANH TOÁN' : 'CHƯA THANH TOÁN (THU COD)' }}
                </span>
            </p>
            <div class="pt-3 border-t border-slate-100 space-y-1.5">
                <div class="flex justify-between text-slate-500">
                    <span>Tạm tính:</span>
                    <span class="text-slate-800 font-semibold">{{ number_format($order->subtotal, 0, ',', '.') }}đ</span>
                </div>
                @if($order->discount_amount > 0)
                    <div class="flex justify-between text-emerald-600 font-semibold">
                        <span>Giảm giá voucher:</span>
                        <span>-{{ number_format($order->discount_amount, 0, ',', '.') }}đ</span>
                    </div>
                @endif
                <div class="flex justify-between text-slate-500">
                    <span>Phí vận chuyển COD:</span>
                    <span class="text-emerald-600 font-semibold">Miễn phí</span>
                </div>
                <div class="flex justify-between text-sm font-bold text-slate-900 pt-2 border-t border-slate-100">
                    <span>Tổng tiền:</span>
                    <span class="text-rose-600 text-base font-black">{{ number_format($order->total_amount, 0, ',', '.') }}đ</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Items Table & Review Option -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-900">Chi Tiết Sản Phẩm Trong Đơn</h3>
        </div>

        <div class="divide-y divide-slate-100">
            @foreach($order->items as $item)
                <div class="p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="space-y-1 flex-1">
                        <span class="text-[10px] font-mono text-slate-400">SKU: {{ $item->product_sku }}</span>
                        <h4 class="text-sm font-bold text-slate-900">
                            {{ $item->product_name }}
                        </h4>
                        <p class="text-xs text-slate-500">
                            Đơn giá: {{ number_format($item->unit_price, 0, ',', '.') }}đ × {{ $item->quantity }} cái
                        </p>
                    </div>

                    <div class="text-right flex flex-col items-end gap-2 shrink-0">
                        <span class="text-base font-black text-slate-900">
                            {{ number_format($item->total_price, 0, ',', '.') }}đ
                        </span>

                        @if($order->isDelivered())
                            @php
                                $hasReviewed = $order->reviews->where('product_id', $item->product_id)->isNotEmpty();
                            @endphp
                            @if($hasReviewed)
                                <span class="text-[11px] font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg">
                                    ✓ Đã đánh giá
                                </span>
                            @else
                                <button 
                                    type="button" 
                                    onclick="openReviewModal({{ $item->product_id }}, '{{ addslashes($item->product_name) }}')"
                                    class="px-3 py-1 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-xs font-semibold shadow-xs transition-colors"
                                >
                                    ★ Đánh giá sản phẩm
                                </button>
                            @endif
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>

<!-- Cancel Order Modal -->
<div id="cancel-order-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
        <h3 class="text-base font-bold text-slate-900 mb-2">Hủy Đơn Hàng</h3>
        <p class="text-xs text-slate-500 mb-4">Bạn có chắc chắn muốn hủy đơn hàng này không? Số lượng giữ chỗ sẽ được hoàn lại kho ngay lập tức.</p>
        
        <form action="{{ route('account.orders.cancel', $order->order_code) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="cancel-reason" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Lý do hủy đơn *</label>
                <textarea 
                    id="cancel-reason" 
                    name="reason" 
                    rows="3" 
                    required 
                    placeholder="Ví dụ: Đổi ý, muốn đổi sang sản phẩm khác, đặt nhầm..." 
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:outline-none focus:border-rose-600"
                ></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('cancel-order-modal').classList.add('hidden')" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl">
                    Đóng
                </button>
                <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-xs">
                    Xác Nhận Hủy Đơn
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Review Modal -->
<div id="review-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
        <h3 class="text-base font-bold text-slate-900 mb-1">Đánh Giá Sản Phẩm</h3>
        <p id="review-product-name" class="text-xs text-slate-500 mb-4 truncate font-semibold"></p>

        <form action="{{ route('account.reviews.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="order_id" value="{{ $order->id }}">
            <input type="hidden" name="product_id" id="modal-product-id" value="">

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Số sao đánh giá *</label>
                <select name="rating" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold text-amber-500 focus:outline-none focus:border-blue-600">
                    <option value="5">★★★★★ – Cực kỳ hài lòng (5 sao)</option>
                    <option value="4">★★★★☆ – Hài lòng (4 sao)</option>
                    <option value="3">★★★☆☆ – Bình thường (3 sao)</option>
                    <option value="2">★★☆☆☆ – Chưa hài lòng (2 sao)</option>
                    <option value="1">★☆☆☆☆ – Rất tệ (1 sao)</option>
                </select>
            </div>

            <div>
                <label for="review-comment" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Cảm nhận của bạn *</label>
                <textarea 
                    id="review-comment" 
                    name="comment" 
                    rows="3" 
                    required 
                    placeholder="Chia sẻ trải nghiệm về hiệu năng, thiết kế, đóng gói của sản phẩm..." 
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:outline-none focus:border-blue-600"
                ></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('review-modal').classList.add('hidden')" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl">
                    Hủy bỏ
                </button>
                <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-xs">
                    Gửi Đánh Giá
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openReviewModal(productId, productName) {
        document.getElementById('modal-product-id').value = productId;
        document.getElementById('review-product-name').textContent = productName;
        document.getElementById('review-modal').classList.remove('hidden');
    }
</script>
@endpush
@endsection

