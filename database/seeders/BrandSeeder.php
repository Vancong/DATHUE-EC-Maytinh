<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            ['name' => 'ASUS ROG', 'slug' => 'asus-rog', 'description' => 'Republic of Gamers – Thương hiệu Gaming số 1 thế giới với hiệu năng tối thượng'],
            ['name' => 'Dell Alienware', 'slug' => 'dell-alienware', 'description' => 'Laptop và PC doanh nhân cao cấp XPS, chiến game Alienware đỉnh cao'],
            ['name' => 'Apple', 'slug' => 'apple', 'description' => 'MacBook Pro, MacBook Air thiết kế hoàn mỹ và thời lượng pin vượt trội với Apple Silicon'],
            ['name' => 'Acer Predator', 'slug' => 'acer-predator', 'description' => 'Dòng laptop Predator và Nitro hiệu năng bền bỉ tối ưu cho game thủ'],
            ['name' => 'MSI Gaming', 'slug' => 'msi-gaming', 'description' => 'Linh kiện bo mạch chủ, card đồ họa và laptop gaming đẳng cấp rồng đỏ'],
            ['name' => 'Corsair', 'slug' => 'corsair', 'description' => 'RAM DDR5, nguồn chuẩn Platinum, vỏ case và phụ kiện tản nhiệt nước cao cấp'],
            ['name' => 'Logitech G', 'slug' => 'logitech-g', 'description' => 'Chuột không dây LIGHTSPEED, bàn phím và tai nghe Gaming tiêu chuẩn eSports'],
            ['name' => 'Samsung', 'slug' => 'samsung', 'description' => 'Màn hình Odyssey OLED cong siêu thực và ổ cứng SSD NVMe tốc độ số 1 thế giới'],
            ['name' => 'Intel', 'slug' => 'intel', 'description' => 'Bộ vi xử lý Core i-series và Core Ultra kiến trúc đa nhân mạnh mẽ'],
            ['name' => 'AMD', 'slug' => 'amd', 'description' => 'Vi xử lý Ryzen 3D V-Cache đột phá hiệu năng chiến game thế hệ mới'],
            ['name' => 'SteelSeries', 'slug' => 'steelseries', 'description' => 'Gaming Gear Đan Mạch cao cấp với công nghệ âm thanh vòm Arctis Nova'],
        ];

        foreach ($brands as $b) {
            Brand::updateOrCreate(['slug' => $b['slug']], $b);
        }
    }
}
