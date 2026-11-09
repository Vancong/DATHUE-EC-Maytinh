<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class MigrateInventoryDataCommand extends Command
{
    protected $signature = 'inventory:migrate-data {--dry-run : Only verify and display audit table without updating}';

    protected $description = 'Safely migrate inventory stocks data into direct product stock columns within a strict DB transaction';

    public function handle(): int
    {
        $this->info('=== BẮT ĐẦU QUY TRÌNH CHUYỂN DỊCH DỮ LIỆU TỒN KHO (DML TRANSACTION) ===');

        $isDryRun = $this->option('dry-run');
        if ($isDryRun) {
            $this->warn('[CHẾ ĐỘ DRY-RUN]: Chỉ kiểm tra và hiển thị số liệu, không thay đổi database.');
        }

        try {
            DB::transaction(function () use ($isDryRun) {
                $products = DB::table('products')->orderBy('id')->get();
                $auditRows = [];

                foreach ($products as $p) {
                    $physical = (int) DB::table('inventory_stocks')->where('product_id', $p->id)->sum('quantity');
                    $reserved = (int) DB::table('inventory_stocks')->where('product_id', $p->id)->sum('reserved_quantity');

                    // Yêu cầu 1: Nếu reserved > physical thì ném ngoại lệ dừng migration và rollback ngay lập tức
                    if ($reserved > $physical) {
                        throw new RuntimeException(
                            "LỖI TOÀN VẸN DỮ LIỆU: Sản phẩm [ID: {$p->id}, SKU: {$p->sku}] có reserved_quantity ({$reserved}) lớn hơn physical quantity ({$physical}). Dừng và rollback ngay lập tức!"
                        );
                    }

                    // Phép trừ thuần túy, tuyệt đối không dùng GREATEST ép 0
                    $stockQuantity = $physical - $reserved;

                    // Tính tổng số lượng đã bán từ các đơn DELIVERED
                    $soldQuantity = (int) DB::table('order_items')
                        ->join('orders', 'order_items.order_id', '=', 'orders.id')
                        ->where('order_items.product_id', $p->id)
                        ->where('orders.order_status', 'delivered')
                        ->sum('order_items.quantity');

                    $auditRows[] = [
                        $p->id,
                        $p->sku,
                        mb_substr($p->name, 0, 32),
                        $physical,
                        $reserved,
                        $stockQuantity,
                        $soldQuantity,
                        'HỢP LỆ',
                    ];

                    if (! $isDryRun) {
                        DB::table('products')->where('id', $p->id)->update([
                            'stock_quantity' => $stockQuantity,
                            'min_stock' => 5,
                            'sold_quantity' => $soldQuantity,
                        ]);
                    }
                }

                // Xử lý đơn hàng lịch sử
                if (! $isDryRun) {
                    // Đơn CANCELLED lịch sử: inventory_settled = true (đã xử lý tồn kho), restocked_at = null (không hiểu là hàng thực tế nhập kho)
                    DB::table('orders')
                        ->where('order_status', 'cancelled')
                        ->update([
                            'inventory_settled' => true,
                            'restocked_at' => null,
                        ]);

                    // Đơn DELIVERED: inventory_settled = true, restocked_at = null
                    DB::table('orders')
                        ->where('order_status', 'delivered')
                        ->update([
                            'inventory_settled' => true,
                            'restocked_at' => null,
                        ]);

                    // Đơn đang lưu hành (chưa tất toán tồn kho)
                    DB::table('orders')
                        ->whereIn('order_status', ['pending', 'confirmed', 'processing', 'shipping'])
                        ->update([
                            'inventory_settled' => false,
                            'restocked_at' => null,
                        ]);
                }

                $this->table(
                    ['ID', 'SKU', 'Tên sản phẩm', 'Tồn kho cũ', 'Giữ chỗ cũ', 'Tồn mới', 'Đã bán', 'Trạng thái'],
                    $auditRows
                );

                if ($isDryRun) {
                    $this->info('Kiểm tra Dry-run hoàn tất. 100% dữ liệu đạt chuẩn.');
                } else {
                    $this->info('Chuyển dịch dữ liệu thành công! DB Transaction đã commit an toàn.');
                }
            });

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('THẤT BẠI: '.$e->getMessage());
            $this->error('Toàn bộ transaction đã được tự động rollback.');

            return Command::FAILURE;
        }
    }
}
