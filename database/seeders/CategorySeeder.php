<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Laptop',
                'slug' => 'laptop',
                'description' => 'Laptop Gaming, Laptop Văn phòng, MacBook cao cấp',
                'sort_order' => 1,
                'children' => [
                    ['name' => 'Laptop Gaming', 'slug' => 'laptop-gaming', 'description' => 'Laptop chơi game cấu hình khủng, màn hình tần số quét cao'],
                    ['name' => 'Laptop Văn Phòng - Mỏng Nhẹ', 'slug' => 'laptop-van-phong', 'description' => 'Thiết kế tinh tế, pin trâu, mỏng nhẹ di động'],
                    ['name' => 'MacBook & Apple Laptop', 'slug' => 'macbook-apple', 'description' => 'MacBook Air, MacBook Pro sử dụng chip Apple Silicon M series'],
                ],
            ],
            [
                'name' => 'PC Gaming & Đồ Họa',
                'slug' => 'pc-gaming-do-hoa',
                'description' => 'Dàn máy tính chơi game và đồ họa render chuyên nghiệp',
                'sort_order' => 2,
                'children' => [
                    ['name' => 'PC TechStore Esport', 'slug' => 'pc-techstore-esport', 'description' => 'Cấu hình tối ưu cho LMHT, Valorant, CS2, FIFA'],
                    ['name' => 'PC TechStore Ultra Gaming', 'slug' => 'pc-techstore-ultra', 'description' => 'Chiến mượt mọi tựa game AAA ở độ phân giải 4K'],
                ],
            ],
            [
                'name' => 'Linh Kiện Máy Tính',
                'slug' => 'linh-kien-may-tinh',
                'description' => 'CPU, VGA, RAM, Ổ cứng SSD, Mainboard, Nguồn PSU, Tản nhiệt',
                'sort_order' => 3,
                'children' => [
                    ['name' => 'Card Màn Hình (VGA)', 'slug' => 'card-man-hinh-vga', 'description' => 'NVIDIA GeForce RTX, AMD Radeon RX series'],
                    ['name' => 'Bộ Vi Xử Lý (CPU)', 'slug' => 'bo-vi-xu-ly-cpu', 'description' => 'Intel Core Gen 13/14, AMD Ryzen 7000/9000 Series'],
                    ['name' => 'Bộ Nhớ Trong (RAM)', 'slug' => 'bo-nho-trong-ram', 'description' => 'RAM DDR4, DDR5 tản nhiệt LED RGB'],
                    ['name' => 'Ổ Cứng SSD & HDD', 'slug' => 'o-cung-ssd-hdd', 'description' => 'SSD NVMe M.2 PCIe Gen 4/5 tốc độ đọc ghi siêu cao'],
                ],
            ],
            [
                'name' => 'Màn Hình Máy Tính',
                'slug' => 'man-hinh-may-tinh',
                'description' => 'Màn hình Gaming 144Hz - 360Hz, màn hình đồ họa 4K IPS',
                'sort_order' => 4,
            ],
            [
                'name' => 'Bàn Phím, Chuột & Gaming Gear',
                'slug' => 'ban-phim-chuot-gaming-gear',
                'description' => 'Bàn phím cơ, chuột gaming wireless, tai nghe không dây',
                'sort_order' => 5,
            ],
        ];

        foreach ($categories as $catData) {
            $children = $catData['children'] ?? [];
            unset($catData['children']);

            $cat = Category::updateOrCreate(['slug' => $catData['slug']], $catData);

            foreach ($children as $subOrder => $childData) {
                $childData['parent_id'] = $cat->id;
                $childData['sort_order'] = $subOrder + 1;
                Category::updateOrCreate(['slug' => $childData['slug']], $childData);
            }
        }
    }
}
