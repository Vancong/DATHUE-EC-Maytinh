<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hóa Đơn Bán Hàng #{{ $order->order_code }} – TechStore</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 p-8 text-slate-800 font-sans antialiased text-xs">
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
        
        <!-- Print Trigger -->
        <div class="no-print flex justify-end gap-2 mb-6">
            <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-bold text-xs shadow-xs hover:bg-blue-700">
                In Phiếu / Xuất PDF
            </button>
            <button onclick="window.close()" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg font-bold text-xs hover:bg-slate-200">
                Đóng
            </button>
        </div>

        <!-- Header -->
        <div class="flex justify-between items-start border-b border-slate-200 pb-6 mb-6">
            <div>
                <h1 class="text-xl font-black text-slate-900 tracking-tight">TECHSTORE COMPUTERS</h1>
                <p class="text-slate-500 mt-1">Hệ thống phân phối máy tính & Gaming Gear chính hãng</p>
                <p class="text-slate-500">Hotline: 1900 8888 | Website: techstore.vn</p>
            </div>
            <div class="text-right">
                <h2 class="text-lg font-mono font-bold text-blue-600">PHIẾU XUẤT GIAO HÀNG</h2>
                <p class="font-mono font-bold text-slate-800 text-sm mt-0.5">#{{ $order->order_code }}</p>
                <p class="text-slate-400 mt-1">Ngày in: {{ now()->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <!-- Recipient & Order Metadata -->
        <div class="grid grid-cols-2 gap-6 mb-6 pb-6 border-b border-slate-200">
            <div>
                <h3 class="font-bold text-slate-900 uppercase tracking-wider mb-2">Người Nhận Hàng:</h3>
                <p class="font-bold text-slate-800 text-sm">{{ $order->customer_name }}</p>
                <p class="text-slate-600 font-mono font-semibold">{{ $order->customer_phone }}</p>
                <p class="text-slate-600 mt-1">{{ $order->shipping_address }}</p>
                @if($order->note)
                    <p class="text-slate-500 italic mt-1">Ghi chú: {{ $order->note }}</p>
                @endif
            </div>
            <div class="text-right">
                <h3 class="font-bold text-slate-900 uppercase tracking-wider mb-2">Thông Tin Vận Đơn:</h3>
                <p class="text-slate-600">Ngày đặt hàng: <strong class="text-slate-800">{{ $order->created_at->format('d/m/Y H:i') }}</strong></p>
                <p class="text-slate-600">Hình thức: <strong class="text-blue-700">Giao hàng & Thu COD</strong></p>
                <p class="text-slate-600">Thu tiền COD: <strong class="text-rose-600 text-sm">{{ number_format($order->total_amount, 0, ',', '.') }}đ</strong></p>
            </div>
        </div>

        <!-- Items Table -->
        <table class="w-full text-left mb-6 border border-slate-200">
            <thead class="bg-slate-50 border-b border-slate-200 uppercase text-[10px] text-slate-500">
                <tr>
                    <th class="p-3">Sản phẩm</th>
                    <th class="p-3">SKU</th>
                    <th class="p-3 text-right">Đơn giá</th>
                    <th class="p-3 text-center">SL</th>
                    <th class="p-3 text-right">Thành tiền</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($order->items as $item)
                    <tr>
                        <td class="p-3 font-bold text-slate-900">{{ $item->product_name }}</td>
                        <td class="p-3 font-mono text-slate-500">{{ $item->product_sku }}</td>
                        <td class="p-3 text-right">{{ number_format($item->unit_price, 0, ',', '.') }}đ</td>
                        <td class="p-3 text-center font-bold">{{ $item->quantity }}</td>
                        <td class="p-3 text-right font-bold text-slate-900">{{ number_format($item->total_price, 0, ',', '.') }}đ</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-slate-50 border-t border-slate-200 font-semibold">
                <tr>
                    <td colspan="4" class="p-2.5 text-right text-slate-500">Tạm tính:</td>
                    <td class="p-2.5 text-right">{{ number_format($order->subtotal, 0, ',', '.') }}đ</td>
                </tr>
                @if($order->discount_amount > 0)
                    <tr>
                        <td colspan="4" class="p-2.5 text-right text-emerald-600">Giảm giá voucher:</td>
                        <td class="p-2.5 text-right text-emerald-600">-{{ number_format($order->discount_amount, 0, ',', '.') }}đ</td>
                    </tr>
                @endif
                <tr>
                    <td colspan="4" class="p-2.5 text-right text-slate-500">Phí giao hàng COD:</td>
                    <td class="p-2.5 text-right text-emerald-600">0đ</td>
                </tr>
                <tr class="text-sm font-black text-slate-900 border-t border-slate-300">
                    <td colspan="4" class="p-3 text-right uppercase">Tổng Thu Của Khách:</td>
                    <td class="p-3 text-right text-rose-600 text-base">{{ number_format($order->total_amount, 0, ',', '.') }}đ</td>
                </tr>
            </tfoot>
        </table>

        <!-- Signatures -->
        <div class="grid grid-cols-3 gap-4 text-center mt-12 pt-6 border-t border-slate-200">
            <div>
                <p class="font-bold text-slate-800">Người Lập Phiếu</p>
                <p class="text-[10px] text-slate-400 mt-0.5">(Ký, họ tên)</p>
            </div>
            <div>
                <p class="font-bold text-slate-800">Nhân Viên Giao Hàng</p>
                <p class="text-[10px] text-slate-400 mt-0.5">(Ký, họ tên)</p>
            </div>
            <div>
                <p class="font-bold text-slate-800">Khách Hàng Nhận</p>
                <p class="text-[10px] text-slate-400 mt-0.5">(Xác nhận máy nguyên vẹn)</p>
            </div>
        </div>

    </div>
</body>
</html>

