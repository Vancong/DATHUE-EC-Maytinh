<?php

namespace Database\Seeders;

use App\Models\InventoryStock;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\StockReceipt;
use App\Models\StockReceiptItem;
use App\Models\StockTransaction;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoTransactionSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $customers = User::where('role', 'customer')->get();
        $customerDefault = $customers->first();

        $warehouseHcm = Warehouse::where('code', 'WH-HCM-01')->first() ?? Warehouse::first();
        $warehouseHn = Warehouse::where('code', 'WH-HN-01')->first() ?? Warehouse::first();
        $supplierFpt = Supplier::where('code', 'SUP-FPT-01')->first() ?? Supplier::first();
        $supplierDgw = Supplier::where('code', 'SUP-DGW-02')->first() ?? Supplier::first();

        $products = Product::all();
        if ($products->isEmpty() || ! $admin || ! $customerDefault || ! $warehouseHcm) {
            return;
        }

        // Clean previous demo transactions if re-running
        Order::query()->delete();
        StockReceipt::query()->delete();
        StockTransaction::query()->delete();
        Review::query()->delete();

        // Reset reserved quantities
        InventoryStock::query()->update(['reserved_quantity' => 0]);

        // =========================================================================
        // 1. PHIẾU NHẬP KHO LỊCH SỬ (CONFIRMED) & PHIẾU CHỜ DUYỆT (DRAFT)
        // =========================================================================

        // Phiếu 1: Nhập hàng đợt 1 từ FPT Synnex (Tháng 2) - Đã xác nhận
        $dateReceipt1 = Carbon::create(date('Y'), 2, 10, 9, 30, 0);
        $receipt1 = StockReceipt::create([
            'receipt_code' => 'PN-'.$dateReceipt1->format('Ymd').'-001',
            'supplier_id' => $supplierFpt->id,
            'warehouse_id' => $warehouseHcm->id,
            'user_id' => $admin->id,
            'status' => 'confirmed',
            'total_amount' => 0,
            'note' => 'Nhập lô laptop ROG Strix & MacBook Pro đầu năm từ Synnex FPT',
            'confirmed_at' => $dateReceipt1,
            'created_at' => $dateReceipt1,
            'updated_at' => $dateReceipt1,
        ]);

        $receipt1Total = 0;
        $itemsReceipt1 = [
            'ROG-SCAR16-01' => 10,
            'MBP-16-M3MAX' => 8,
            'VGA-ROG-4090-OC' => 5,
            'GEAR-LOGI-SL2' => 20,
        ];

        foreach ($itemsReceipt1 as $sku => $qty) {
            $p = $products->where('sku', $sku)->first();
            if (! $p) {
                continue;
            }
            $unitCost = (float) ($p->cost_price ?: ($p->price * 0.8));
            $lineTotal = $qty * $unitCost;
            $receipt1Total += $lineTotal;

            StockReceiptItem::create([
                'stock_receipt_id' => $receipt1->id,
                'product_id' => $p->id,
                'quantity' => $qty,
                'unit_price' => $unitCost,
                'total_price' => $lineTotal,
            ]);

            $stock = InventoryStock::firstOrCreate(
                ['product_id' => $p->id, 'warehouse_id' => $warehouseHcm->id],
                ['quantity' => 0, 'reserved_quantity' => 0]
            );
            $before = $stock->quantity;
            $after = $before + $qty;
            $stock->update(['quantity' => $after]);

            StockTransaction::create([
                'product_id' => $p->id,
                'warehouse_id' => $warehouseHcm->id,
                'user_id' => $admin->id,
                'reference_type' => 'stock_receipt',
                'reference_id' => $receipt1->id,
                'transaction_type' => 'in',
                'quantity' => $qty,
                'balance_before' => $before,
                'balance_after' => $after,
                'note' => 'Nhập kho lô hàng đầu năm theo phiếu #'.$receipt1->receipt_code,
                'created_at' => $dateReceipt1,
                'updated_at' => $dateReceipt1,
            ]);
        }
        $receipt1->update(['total_amount' => $receipt1Total]);

        // Phiếu 2: Nhập hàng linh kiện & màn hình từ Digiworld (Tháng 5) - Đã xác nhận
        $dateReceipt2 = Carbon::create(date('Y'), 5, 18, 14, 0, 0);
        $receipt2 = StockReceipt::create([
            'receipt_code' => 'PN-'.$dateReceipt2->format('Ymd').'-002',
            'supplier_id' => $supplierDgw->id,
            'warehouse_id' => $warehouseHn->id,
            'user_id' => $admin->id,
            'status' => 'confirmed',
            'total_amount' => 0,
            'note' => 'Nhập bổ sung màn hình Samsung & CPU AMD/Intel kho Hà Nội',
            'confirmed_at' => $dateReceipt2,
            'created_at' => $dateReceipt2,
            'updated_at' => $dateReceipt2,
        ]);

        $receipt2Total = 0;
        $itemsReceipt2 = [
            'MON-SS-NEOG8' => 8,
            'CPU-AMD-R7-7800X3D' => 15,
            'CPU-INTEL-I914900K' => 12,
            'SSD-SS-990PRO-2TB' => 25,
            'GEAR-ROG-AZOTH' => 10,
        ];

        foreach ($itemsReceipt2 as $sku => $qty) {
            $p = $products->where('sku', $sku)->first();
            if (! $p) {
                continue;
            }
            $unitCost = (float) ($p->cost_price ?: ($p->price * 0.8));
            $lineTotal = $qty * $unitCost;
            $receipt2Total += $lineTotal;

            StockReceiptItem::create([
                'stock_receipt_id' => $receipt2->id,
                'product_id' => $p->id,
                'quantity' => $qty,
                'unit_price' => $unitCost,
                'total_price' => $lineTotal,
            ]);

            $stock = InventoryStock::firstOrCreate(
                ['product_id' => $p->id, 'warehouse_id' => $warehouseHn->id],
                ['quantity' => 0, 'reserved_quantity' => 0]
            );
            $before = $stock->quantity;
            $after = $before + $qty;
            $stock->update(['quantity' => $after]);

            StockTransaction::create([
                'product_id' => $p->id,
                'warehouse_id' => $warehouseHn->id,
                'user_id' => $admin->id,
                'reference_type' => 'stock_receipt',
                'reference_id' => $receipt2->id,
                'transaction_type' => 'in',
                'quantity' => $qty,
                'balance_before' => $before,
                'balance_after' => $after,
                'note' => 'Nhập kho chi nhánh Hà Nội theo phiếu #'.$receipt2->receipt_code,
                'created_at' => $dateReceipt2,
                'updated_at' => $dateReceipt2,
            ]);
        }
        $receipt2->update(['total_amount' => $receipt2Total]);

        // Phiếu 3: Phiếu nhập kho NHÁP (DRAFT) để Admin demo nút Duyệt Phiếu
        $dateReceipt3 = Carbon::now()->subDays(2);
        $draftReceipt = StockReceipt::create([
            'receipt_code' => 'PN-'.$dateReceipt3->format('Ymd').'-003',
            'supplier_id' => $supplierFpt->id,
            'warehouse_id' => $warehouseHcm->id,
            'user_id' => $admin->id,
            'status' => 'draft',
            'total_amount' => 45000000,
            'note' => 'Lô hàng phụ kiện & tai nghe SteelSeries chờ bộ phận kho kiểm đếm thực tế',
            'created_at' => $dateReceipt3,
            'updated_at' => $dateReceipt3,
        ]);

        $headset = $products->where('sku', 'GEAR-STEEL-NOVAPRO')->first() ?? $products->first();
        StockReceiptItem::create([
            'stock_receipt_id' => $draftReceipt->id,
            'product_id' => $headset->id,
            'quantity' => 5,
            'unit_price' => 6900000,
            'total_price' => 34500000,
        ]);

        $ram = $products->where('sku', 'RAM-DOMINATOR-32G')->first() ?? $products->first();
        StockReceiptItem::create([
            'stock_receipt_id' => $draftReceipt->id,
            'product_id' => $ram->id,
            'quantity' => 3,
            'unit_price' => 3800000,
            'total_price' => 11400000,
        ]);

        // =========================================================================
        // 2. TẠO CÁC ĐƠN HÀNG LỊCH SỬ QUA 12 THÁNG (DOANH THU & TRẠNG THÁI)
        // =========================================================================

        $orderTemplates = [
            // Tháng 1: 1 Đơn lớn đầu năm (Delivered, Paid)
            [
                'month' => 1, 'day' => 15,
                'customer_idx' => 0,
                'items' => [['ROG-SCAR16-01', 1], ['GEAR-LOGI-SL2', 1]],
                'status' => 'delivered', 'payment_status' => 'paid',
                'discount' => 500000,
                'note' => 'Giao hàng hỏa tốc trong ngày khai xuân',
            ],
            // Tháng 2: 1 Đơn dàn PC Gaming (Delivered, Paid)
            [
                'month' => 2, 'day' => 22,
                'customer_idx' => 1,
                'items' => [['PC-ULTRA-4090', 1]],
                'status' => 'delivered', 'payment_status' => 'paid',
                'discount' => 1000000,
                'note' => 'Kiểm tra kỹ hệ thống tản nhiệt nước trước khi giao',
            ],
            // Tháng 3: 2 Đơn đồ họa và văn phòng (Delivered, Paid)
            [
                'month' => 3, 'day' => 8,
                'customer_idx' => 2,
                'items' => [['MBP-16-M3MAX', 1], ['MON-DELL-U2724DE', 1]],
                'status' => 'delivered', 'payment_status' => 'paid',
                'discount' => 1000000,
                'note' => 'Xuất hóa đơn VAT cho công ty',
            ],
            [
                'month' => 3, 'day' => 25,
                'customer_idx' => 3,
                'items' => [['MBA-13-M3', 1], ['GEAR-LOGI-SL2', 1]],
                'status' => 'delivered', 'payment_status' => 'paid',
                'discount' => 0,
                'note' => 'Giao giờ hành chính tại cơ quan',
            ],
            // Tháng 4: 1 Đơn VGA & SSD (Delivered, Paid)
            [
                'month' => 4, 'day' => 14,
                'customer_idx' => 4,
                'items' => [['VGA-MSI-4070TIS', 1], ['SSD-SS-990PRO-2TB', 2]],
                'status' => 'delivered', 'payment_status' => 'paid',
                'discount' => 500000,
                'note' => 'Bọc chống sốc cẩn thận linh kiện nhạy cảm',
            ],
            // Tháng 5: 1 Đơn PC Esport (Delivered, Paid)
            [
                'month' => 5, 'day' => 20,
                'customer_idx' => 0,
                'items' => [['PC-ESPORT-7800X3D', 1], ['MON-SS-NEOG8', 1]],
                'status' => 'delivered', 'payment_status' => 'paid',
                'discount' => 1000000,
                'note' => 'Lắp ráp sẵn và test game trước khi bàn giao',
            ],
            // Tháng 6: 1 Đơn Predator Gaming Laptop (Delivered, Paid)
            [
                'month' => 6, 'day' => 11,
                'customer_idx' => 1,
                'items' => [['ACER-HELIOS16', 1], ['GEAR-ROG-AZOTH', 1]],
                'status' => 'delivered', 'payment_status' => 'paid',
                'discount' => 500000,
                'note' => 'Tặng kèm balo Predator chống nước',
            ],
            // Tháng 7: 1 Đơn Màn hình ROG OLED & Gear (Delivered, Paid)
            [
                'month' => 7, 'day' => 19,
                'customer_idx' => 2,
                'items' => [['MON-ROG-OLED27', 1], ['GEAR-STEEL-NOVAPRO', 1]],
                'status' => 'delivered', 'payment_status' => 'paid',
                'discount' => 0,
                'note' => 'Kiểm tra điểm chết màn hình kỹ càng',
            ],
            // Tháng 8: 1 Đơn Nâng cấp CPU & RAM (Delivered, Paid)
            [
                'month' => 8, 'day' => 5,
                'customer_idx' => 3,
                'items' => [['CPU-AMD-R7-7800X3D', 1], ['RAM-DOMINATOR-32G', 1], ['SSD-SS-990PRO-2TB', 1]],
                'status' => 'delivered', 'payment_status' => 'paid',
                'discount' => 500000,
                'note' => 'Khách tự lắp đặt tại nhà',
            ],
            // Tháng 9: 1 Đơn Dell XPS 14 (Delivered, Paid)
            [
                'month' => 9, 'day' => 12,
                'customer_idx' => 4,
                'items' => [['DELL-XPS14-9440', 1]],
                'status' => 'delivered', 'payment_status' => 'paid',
                'discount' => 1000000,
                'note' => 'Cài đặt sẵn Windows bản quyền và Office 365',
            ],
            // Tháng 10: 2 Đơn Mua sắm mùa tựu trường (Delivered, Paid)
            [
                'month' => 10, 'day' => 8,
                'customer_idx' => 0,
                'items' => [['VGA-ROG-4090-OC', 1], ['CPU-INTEL-I914900K', 1]],
                'status' => 'delivered', 'payment_status' => 'paid',
                'discount' => 1000000,
                'note' => 'Khách hàng VIP phòng máy đồ họa',
            ],
            [
                'month' => 10, 'day' => 28,
                'customer_idx' => 1,
                'items' => [['ROG-SCAR16-01', 1]],
                'status' => 'delivered', 'payment_status' => 'paid',
                'discount' => 500000,
                'note' => 'Giao hàng tận tay khách hàng',
            ],
            // Tháng 11: Đa dạng trạng thái gần đây (Gần thời điểm hiện tại)
            // Đơn 13: Vừa giao tuần này (Delivered, Paid)
            [
                'month' => 11, 'day' => 2,
                'customer_idx' => 2,
                'items' => [['MBA-13-M3', 1]],
                'status' => 'delivered', 'payment_status' => 'paid',
                'discount' => 0,
                'note' => 'Giao trước 17h chiều',
            ],
            // Đơn 14: Đang vận chuyển (Shipping, Unpaid COD)
            [
                'month' => 11, 'day' => 7,
                'customer_idx' => 3,
                'items' => [['VGA-MSI-4070TIS', 1], ['GEAR-LOGI-SL2', 1]],
                'status' => 'shipping', 'payment_status' => 'unpaid',
                'discount' => 500000,
                'note' => 'Đang trên xe trung chuyển liên tỉnh',
            ],
            // Đơn 15: Đang xử lý đóng gói (Processing, Paid)
            [
                'month' => 11, 'day' => 8,
                'customer_idx' => 4,
                'items' => [['MON-SS-NEOG8', 1]],
                'status' => 'processing', 'payment_status' => 'paid',
                'discount' => 500000,
                'note' => 'Kho đang dán tem niêm phong và đóng kiện gỗ',
            ],
            // Đơn 16: Đã xác nhận chờ xuất kho (Confirmed, Unpaid)
            [
                'month' => 11, 'day' => 8,
                'customer_idx' => 0,
                'items' => [['GEAR-ROG-AZOTH', 1], ['GEAR-STEEL-NOVAPRO', 1]],
                'status' => 'confirmed', 'payment_status' => 'unpaid',
                'discount' => 0,
                'note' => 'Khách hẹn lấy tại showroom hoặc nhận COD vào ngày mai',
            ],
            // Đơn 17: Mới đặt chờ duyệt (Pending, Unpaid - Giữ chỗ tồn kho)
            [
                'month' => 11, 'day' => 9,
                'customer_idx' => 1,
                'items' => [['MBP-16-M3MAX', 1]],
                'status' => 'pending', 'payment_status' => 'unpaid',
                'discount' => 1000000,
                'note' => 'Khách đặt qua điện thoại, chờ điện thoại xác nhận đơn hàng',
            ],
            // Đơn 18: Đơn hàng khách yêu cầu hủy do đổi ý (Cancelled, Unpaid)
            [
                'month' => 11, 'day' => 4,
                'customer_idx' => 2,
                'items' => [['PC-ULTRA-4090', 1]],
                'status' => 'cancelled', 'payment_status' => 'unpaid',
                'discount' => 0,
                'note' => 'Khách đổi sang mua Laptop thay vì máy bàn để đi công tác',
            ],
        ];

        $currentYear = (int) date('Y');
        $createdOrders = [];

        foreach ($orderTemplates as $idx => $tmpl) {
            $customer = $customers[$tmpl['customer_idx'] % $customers->count()] ?? $customerDefault;
            $orderDate = Carbon::create($currentYear, $tmpl['month'], $tmpl['day'], rand(8, 18), rand(10, 50), 0);

            $orderCode = sprintf('ORD-%s-%04d', $orderDate->format('Ymd'), $idx + 101);

            $subtotal = 0;
            $orderItemsData = [];

            foreach ($tmpl['items'] as [$itemSku, $itemQty]) {
                $product = $products->where('sku', $itemSku)->first();
                if (! $product) {
                    continue;
                }
                $unitPrice = (float) $product->current_price;
                $lineTotal = $unitPrice * $itemQty;
                $subtotal += $lineTotal;

                $orderItemsData[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'unit_price' => $unitPrice,
                    'quantity' => $itemQty,
                    'total_price' => $lineTotal,
                ];
            }

            $discountAmount = min($tmpl['discount'], $subtotal);
            $totalAmount = max(0, $subtotal - $discountAmount);

            $order = Order::create([
                'order_code' => $orderCode,
                'user_id' => $customer->id,
                'customer_name' => $customer->name,
                'customer_email' => $customer->email,
                'customer_phone' => $customer->phone ?? '0901234567',
                'shipping_address' => $customer->address ?? 'Quận 1, TP. Hồ Chí Minh',
                'note' => $tmpl['note'],
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'shipping_fee' => 0,
                'total_amount' => $totalAmount,
                'payment_method' => 'cod',
                'payment_status' => $tmpl['payment_status'],
                'order_status' => $tmpl['status'],
                'created_at' => $orderDate,
                'updated_at' => $tmpl['status'] === 'delivered' ? $orderDate->copy()->addDays(2) : $orderDate,
            ]);

            foreach ($orderItemsData as $oi) {
                $oi['order_id'] = $order->id;
                OrderItem::create($oi);

                // If pending/confirmed, reserve inventory stock
                if (in_array($tmpl['status'], ['pending', 'confirmed'])) {
                    $stock = InventoryStock::where('product_id', $oi['product_id'])
                        ->where('warehouse_id', $warehouseHcm->id)
                        ->first();
                    if ($stock) {
                        $stock->increment('reserved_quantity', $oi['quantity']);
                    }
                }

                // If shipping/delivered, log stock transaction "out"
                if (in_array($tmpl['status'], ['shipping', 'delivered'])) {
                    $stock = InventoryStock::where('product_id', $oi['product_id'])
                        ->where('warehouse_id', $warehouseHcm->id)
                        ->first();
                    if ($stock) {
                        $before = $stock->quantity;
                        $stockQty = max(1, $before - $oi['quantity']);
                        $stock->update(['quantity' => $stockQty]);

                        StockTransaction::create([
                            'product_id' => $oi['product_id'],
                            'warehouse_id' => $warehouseHcm->id,
                            'user_id' => $admin->id,
                            'reference_type' => 'order',
                            'reference_id' => $order->id,
                            'transaction_type' => 'out',
                            'quantity' => $oi['quantity'],
                            'balance_before' => $before,
                            'balance_after' => $stockQty,
                            'note' => 'Xuất kho cho đơn hàng #'.$order->order_code,
                            'created_at' => $orderDate,
                            'updated_at' => $orderDate,
                        ]);
                    }
                }
            }

            $createdOrders[] = $order;
        }

        // =========================================================================
        // 3. ĐÁNH GIÁ SẢN PHẨM TỪ KHÁCH HÀNG THỰC TẾ (REVIEWS)
        // =========================================================================

        $reviewData = [
            [
                'sku' => 'ROG-SCAR16-01',
                'rating' => 5,
                'comment' => 'Máy chiến mượt mà mọi game nặng như Cyberpunk 2077 và Black Myth Wukong ở thiết lập đồ họa Max Setting. Màn hình Mini-LED độ sáng cao, tương phản đen sâu cực đỉnh!',
            ],
            [
                'sku' => 'MBP-16-M3MAX',
                'rating' => 5,
                'comment' => 'MacBook Pro M3 Max quá đỉnh cho nhu cầu dựng phim 8K và Premiere/After Effects. Máy chạy liên tục mà nhiệt độ chỉ ấm nhẹ, pin dùng 15 tiếng không cần cắm sạc. Màu Space Black cực kỳ sang trọng!',
            ],
            [
                'sku' => 'MBA-13-M3',
                'rating' => 5,
                'comment' => 'Mỏng nhẹ di chuyển cực tiện, gập mở một tay mượt mà. Bàn phím gõ êm, màn hình Retina sắc nét, loa nghe nhạc hay bất ngờ.',
            ],
            [
                'sku' => 'VGA-ROG-4090-OC',
                'rating' => 5,
                'comment' => 'Quái vật đồ họa không có đối thủ! Nhiệt độ full load khi render 3D chỉ quanh quẩn 62 độ C nhờ tản buồng hơi hơi nước khổng lồ của ASUS ROG.',
            ],
            [
                'sku' => 'CPU-AMD-R7-7800X3D',
                'rating' => 5,
                'comment' => 'CPU gaming tốt nhất hiện tại, FPS trong CS2 và Valorant ổn định ở mức 600+ không hề drop giật lag, tiêu thụ điện chỉ 80W.',
            ],
            [
                'sku' => 'MON-SS-NEOG8',
                'rating' => 5,
                'comment' => 'Màn hình cong 1000R ôm trọn tầm mắt, độ phân giải 4K kết hợp 240Hz cho chuyển động siêu mượt mà. Xem phim HDR và chơi game đỉnh cao.',
            ],
            [
                'sku' => 'MON-ROG-OLED27',
                'rating' => 5,
                'comment' => 'Tấm nền OLED phản hồi 0.03ms bắn súng không có bóng ma. Màu sắc rực rỡ và độ tương phản vô cực, rất đáng đồng tiền bát gạo.',
            ],
            [
                'sku' => 'GEAR-LOGI-SL2',
                'rating' => 5,
                'comment' => 'Chuột siêu nhẹ chỉ 60g, cầm vẩy tâm cực kỳ chính xác. Switch quang lai cơ bấm nảy đanh, pin dùng 2 tuần mới phải sạc 1 lần.',
            ],
            [
                'sku' => 'GEAR-ROG-AZOTH',
                'rating' => 5,
                'comment' => 'Bàn phím custom chất lượng build đỉnh cao, gõ đầm tay âm thock nghe rất sướng. Màn hình OLED phụ hiển thị GIF động siêu vui mắt!',
            ],
            [
                'sku' => 'GEAR-STEEL-NOVAPRO',
                'rating' => 4,
                'comment' => 'Âm thanh vòm nghe rõ tiếng bước chân địch từ xa. Hệ thống 2 pin tráo đổi liên tục rất tiện lợi không bao giờ lo hết pin giữa trận đấu.',
            ],
            [
                'sku' => 'SSD-SS-990PRO-2TB',
                'rating' => 5,
                'comment' => 'Tốc độ load game và copy file nặng tính bằng giây! Đạt đúng thông số 7400MB/s như nhà sản xuất công bố.',
            ],
            [
                'sku' => 'PC-ULTRA-4090',
                'rating' => 5,
                'comment' => 'Đội ngũ kỹ thuật TechStore đi dây nguồn cực kỳ gọn gàng thẩm mỹ. Máy chạy êm ru, hiệu năng vượt ngoài mong đợi!',
            ],
        ];

        // Link reviews with delivered orders
        $deliveredOrders = collect($createdOrders)->where('order_status', 'delivered')->values();

        foreach ($reviewData as $rIndex => $rItem) {
            $product = $products->where('sku', $rItem['sku'])->first();
            if (! $product) {
                continue;
            }

            $order = $deliveredOrders[$rIndex % $deliveredOrders->count()] ?? $deliveredOrders->first();
            $customer = $order ? $order->user : $customerDefault;

            Review::create([
                'product_id' => $product->id,
                'user_id' => $customer->id,
                'order_id' => $order?->id,
                'rating' => $rItem['rating'],
                'comment' => $rItem['comment'],
                'status' => 'approved',
                'created_at' => $order ? $order->created_at->addDays(3) : now()->subDays(10),
                'updated_at' => $order ? $order->created_at->addDays(3) : now()->subDays(10),
            ]);
        }
    }
}
