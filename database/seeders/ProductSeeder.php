<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\InventoryStock;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSpecification;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $warehouseHcm = Warehouse::where('code', 'WH-HCM-01')->first() ?? Warehouse::first();
        $warehouseHn = Warehouse::where('code', 'WH-HN-01')->first() ?? Warehouse::first();

        // Categories
        $catGaming = Category::where('slug', 'laptop-gaming')->first() ?? Category::first();
        $catOffice = Category::where('slug', 'laptop-van-phong')->first() ?? Category::first();
        $catMac = Category::where('slug', 'macbook-apple')->first() ?? Category::first();
        $catPcUltra = Category::where('slug', 'pc-techstore-ultra')->first() ?? Category::first();
        $catPcEsport = Category::where('slug', 'pc-techstore-esport')->first() ?? Category::first();
        $catVga = Category::where('slug', 'card-man-hinh-vga')->first() ?? Category::first();
        $catCpu = Category::where('slug', 'bo-vi-xu-ly-cpu')->first() ?? Category::first();
        $catRam = Category::where('slug', 'bo-nho-trong-ram')->first() ?? Category::first();
        $catSsd = Category::where('slug', 'o-cung-ssd-hdd')->first() ?? Category::first();
        $catMonitor = Category::where('slug', 'man-hinh-may-tinh')->first() ?? Category::first();
        $catGear = Category::where('slug', 'ban-phim-chuot-gaming-gear')->first() ?? Category::first();

        // Brands
        $brandAsus = Brand::where('slug', 'asus-rog')->first();
        $brandApple = Brand::where('slug', 'apple')->first();
        $brandDell = Brand::where('slug', 'dell-alienware')->first();
        $brandAcer = Brand::where('slug', 'acer-predator')->first();
        $brandMsi = Brand::where('slug', 'msi-gaming')->first();
        $brandCorsair = Brand::where('slug', 'corsair')->first();
        $brandLogitech = Brand::where('slug', 'logitech-g')->first();
        $brandSamsung = Brand::where('slug', 'samsung')->first();
        $brandIntel = Brand::where('slug', 'intel')->first();
        $brandAmd = Brand::where('slug', 'amd')->first();
        $brandSteel = Brand::where('slug', 'steelseries')->first();

        $products = [
            // 1. Laptop Gaming ASUS ROG Strix SCAR 16
            [
                'name' => 'Laptop Gaming ASUS ROG Strix SCAR 16 G634JYR',
                'slug' => 'asus-rog-strix-scar-16-g634jyr',
                'sku' => 'ROG-SCAR16-01',
                'category_id' => $catGaming->id,
                'brand_id' => $brandAsus?->id,
                'price' => 74990000,
                'sale_price' => 69990000,
                'cost_price' => 58000000,
                'thumbnail' => 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?auto=format&fit=crop&w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?auto=format&fit=crop&w=800&q=80',
                ],
                'short_description' => 'Laptop Gaming cao cấp Intel Core i9-14900HX, RTX 4080 12GB, màn hình 2.5K Nebula HDR 240Hz.',
                'description' => '<p>ASUS ROG Strix SCAR 16 mang đến sức mạnh hủy diệt cho mọi game thủ và nhà sáng tạo nội dung. Trang bị vi xử lý Intel Core i9 thế hệ 14 đỉnh cao cùng card đồ họa NVIDIA GeForce RTX 4080 12GB TGP 175W max công suất.</p><p>Hệ thống tản nhiệt thông minh ROG Intelligent Cooling 3 quạt với kim loại lỏng Conductonaut Extreme giữ cho máy luôn mát mẻ dưới tải nặng liên tục.</p>',
                'is_featured' => true,
                'is_active' => true,
                'specs' => [
                    ['CPU', 'Intel Core i9-14900HX (24 nhân, 32 luồng, up to 5.8GHz, 36MB Cache)'],
                    ['RAM', '32GB DDR5 5600MHz (2x 16GB, nâng cấp tối đa 64GB)'],
                    ['Ổ cứng', '1TB PCIe 4.0 NVMe M.2 Performance SSD'],
                    ['Card đồ họa', 'NVIDIA GeForce RTX 4080 12GB GDDR6 (TGP 175W with Dynamic Boost)'],
                    ['Màn hình', '16 inch QHD+ 16:10 (2560 x 1600) ROG Nebula HDR, Mini LED, 240Hz, 100% DCI-P3'],
                    ['Bàn phím', 'Per-Key RGB chiclet keyboard, Aura Sync, NumberPad'],
                    ['Pin', '90WHrs, 4-cell Li-ion, sạc nhanh Type-C 100W PD'],
                    ['Trọng lượng', '2.65 kg'],
                ],
                'stock_hcm' => 12,
                'stock_hn' => 8,
            ],

            // 2. Apple MacBook Pro 16 inch M3 Max
            [
                'name' => 'Apple MacBook Pro 16 inch M3 Max (Space Black)',
                'slug' => 'apple-macbook-pro-16-inch-m3-max',
                'sku' => 'MBP-16-M3MAX',
                'category_id' => $catMac->id,
                'brand_id' => $brandApple?->id,
                'price' => 89990000,
                'sale_price' => 84990000,
                'cost_price' => 72000000,
                'thumbnail' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1611186871348-b1ce696e52c9?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1541807084-5c52b6b3adef?auto=format&fit=crop&w=800&q=80',
                ],
                'short_description' => 'Apple M3 Max 14-core CPU, 30-core GPU, 36GB Unified Memory, màn Liquid Retina XDR 120Hz.',
                'description' => '<p>MacBook Pro 16 inch với chip M3 Max mang lại bước nhảy vọt về hiệu năng đồ họa và render 3D chuyên nghiệp. Thời lượng pin kỷ lục lên đến 22 giờ liên tục. Màu sắc mới Space Black phủ lớp anod hóa chống bám vân tay tinh tế.</p>',
                'is_featured' => true,
                'is_active' => true,
                'specs' => [
                    ['CPU', 'Apple M3 Max 14-core (10 nhân hiệu năng cao + 4 nhân tiết kiệm điện)'],
                    ['GPU', '30-core GPU với Hardware-accelerated ray tracing'],
                    ['RAM', '36GB Unified Memory tốc độ băng thông 300GB/s'],
                    ['Ổ cứng', '1TB SSD siêu tốc độ lên đến 7.4GB/s'],
                    ['Màn hình', '16.2 inch Liquid Retina XDR (3456 x 2234), ProMotion 120Hz, 1600 nits peak HDR'],
                    ['Cổng kết nối', '3x Thunderbolt 4, HDMI, khe thẻ nhớ SDXC, Jack 3.5mm, MagSafe 3'],
                    ['Pin', '100Wh, sạc nhanh qua MagSafe 140W USB-C'],
                    ['Hệ điều hành', 'macOS Sonoma / Sequoia'],
                ],
                'stock_hcm' => 15,
                'stock_hn' => 10,
            ],

            // 3. Apple MacBook Air 13 inch M3
            [
                'name' => 'Apple MacBook Air 13 inch M3 (Midnight)',
                'slug' => 'apple-macbook-air-13-inch-m3',
                'sku' => 'MBA-13-M3',
                'category_id' => $catMac->id,
                'brand_id' => $brandApple?->id,
                'price' => 32990000,
                'sale_price' => 29490000,
                'cost_price' => 25000000,
                'thumbnail' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1541807084-5c52b6b3adef?auto=format&fit=crop&w=800&q=80',
                ],
                'short_description' => 'Thiết kế siêu mỏng nhẹ chỉ 1.24kg, chip Apple M3 mạnh mẽ, hỗ trợ xuất 2 màn hình ngoài.',
                'description' => '<p>MacBook Air 13 M3 là chiếc laptop văn phòng và học tập hoàn hảo nhất. Thiết kế nhôm unibody nguyên khối siêu mỏng 11.3mm, tản nhiệt không quạt hoàn toàn yên tĩnh, thời lượng pin 18 tiếng.</p>',
                'is_featured' => true,
                'is_active' => true,
                'specs' => [
                    ['CPU', 'Apple M3 8 nhân (4 nhân hiệu năng cao + 4 nhân tiết kiệm điện)'],
                    ['GPU', '10 nhân GPU hỗ trợ ray tracing bằng phần cứng'],
                    ['RAM', '16GB Unified Memory'],
                    ['Ổ cứng', '512GB SSD NVMe tốc độ cao'],
                    ['Màn hình', '13.6 inch Liquid Retina (2560 x 1664), 500 nits, Wide Color P3, True Tone'],
                    ['Trọng lượng', '1.24 kg'],
                    ['Thời lượng pin', 'Lên đến 18 giờ phát video'],
                ],
                'stock_hcm' => 20,
                'stock_hn' => 15,
            ],

            // 4. Acer Predator Helios 16 PH16-72
            [
                'name' => 'Laptop Gaming Acer Predator Helios 16 PH16-72',
                'slug' => 'acer-predator-helios-16-ph16-72',
                'sku' => 'ACER-HELIOS16',
                'category_id' => $catGaming->id,
                'brand_id' => $brandAcer?->id,
                'price' => 52990000,
                'sale_price' => 48990000,
                'cost_price' => 41000000,
                'thumbnail' => 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?auto=format&fit=crop&w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1603302576837-37561b2e2302?auto=format&fit=crop&w=800&q=80',
                ],
                'short_description' => 'Intel Core i7-14700HX, RTX 4070 8GB, màn hình 16 inch WQXGA 240Hz 500 nits IPS.',
                'description' => '<p>Predator Helios 16 khẳng định đẳng cấp chiến game đỉnh cao với tản nhiệt quạt kim loại AeroBlade 3D thế hệ thứ 5 kết hợp keo kim loại lỏng. Bàn phím Mini-LED RGB từng phím rực rỡ.</p>',
                'is_featured' => false,
                'is_active' => true,
                'specs' => [
                    ['CPU', 'Intel Core i7-14700HX (20 nhân, 28 luồng, Turbo up to 5.5GHz)'],
                    ['RAM', '16GB DDR5 5600MHz (2 khe cắm, nâng cấp tối đa 64GB)'],
                    ['Ổ cứng', '1TB PCIe NVMe Gen 4 SSD'],
                    ['Card đồ họa', 'NVIDIA GeForce RTX 4070 8GB GDDR6 (TGP 140W Max)'],
                    ['Màn hình', '16 inch WQXGA (2560 x 1600) 240Hz, sRGB 100%, G-Sync, DDS'],
                    ['Tản nhiệt', '2 Quạt AeroBlade 3D thế hệ 5 + Keo tản nhiệt kim loại lỏng'],
                ],
                'stock_hcm' => 10,
                'stock_hn' => 6,
            ],

            // 5. Dell XPS 14 9440 (OLED 3.2K)
            [
                'name' => 'Laptop Dell XPS 14 9440 OLED Touch',
                'slug' => 'dell-xps-14-9440-oled-touch',
                'sku' => 'DELL-XPS14-9440',
                'category_id' => $catOffice->id,
                'brand_id' => $brandDell?->id,
                'price' => 59900000,
                'sale_price' => 56500000,
                'cost_price' => 47000000,
                'thumbnail' => 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?auto=format&fit=crop&w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1541807084-5c52b6b3adef?auto=format&fit=crop&w=800&q=80',
                ],
                'short_description' => 'Intel Core Ultra 7 155H AI Boost, 32GB RAM, màn hình OLED 3.2K Touch 120Hz, vỏ nhôm CNC cao cấp.',
                'description' => '<p>Dell XPS 14 9440 là kiệt tác công nghệ và thẩm mỹ. Bàn di chuột cảm ứng vô cực ẩn liền dưới mặt kính, hàng phím chức năng cảm ứng phát sáng, màn hình OLED 3.2K siêu thực.</p>',
                'is_featured' => true,
                'is_active' => true,
                'specs' => [
                    ['CPU', 'Intel Core Ultra 7 155H (16 nhân, 22 luồng, NPU Intel AI Boost)'],
                    ['RAM', '32GB LPDDR5x 7467MHz Dual Channel'],
                    ['Ổ cứng', '1TB PCIe 4.0 NVMe SSD'],
                    ['Card đồ họa', 'NVIDIA GeForce RTX 4050 6GB GDDR6 (30W)'],
                    ['Màn hình', '14.5 inch 3.2K (3200 x 2000) OLED Touch, 120Hz, 100% DCI-P3, Gorilla Glass Victus'],
                    ['Trọng lượng', '1.68 kg'],
                ],
                'stock_hcm' => 8,
                'stock_hn' => 5,
            ],

            // 6. PC TechStore Ultra Gaming Master
            [
                'name' => 'PC TechStore Ultra Gaming Master i9-14900K | RTX 4090 24GB | 64GB DDR5',
                'slug' => 'pc-techstore-ultra-gaming-master',
                'sku' => 'PC-ULTRA-4090',
                'category_id' => $catPcUltra->id,
                'brand_id' => $brandAsus?->id,
                'price' => 119000000,
                'sale_price' => 112000000,
                'cost_price' => 95000000,
                'thumbnail' => 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?auto=format&fit=crop&w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1550745165-9bc0b252726f?auto=format&fit=crop&w=800&q=80',
                ],
                'short_description' => 'Bộ máy tính Gaming tối thượng: Core i9-14900K, ROG Strix RTX 4090 24GB, Tản nước AIO ROG Ryujin III 360.',
                'description' => '<p>Cỗ máy chiến game đỉnh chóp không đối thủ tại TechStore. Được lắp ráp hoàn mỹ từ các linh kiện cao cấp nhất: Bo mạch ROG Maximus Z790 HERO, Card RTX 4090 OC 24GB, Nguồn Corsair HX1200 Platinum.</p>',
                'is_featured' => true,
                'is_active' => true,
                'specs' => [
                    ['CPU', 'Intel Core i9-14900K (24C/32T, 6.0GHz Turbo)'],
                    ['Mainboard', 'ASUS ROG MAXIMUS Z790 DARK HERO'],
                    ['VGA', 'ASUS ROG Strix GeForce RTX 4090 24GB GDDR6X OC Edition'],
                    ['RAM', '64GB (2x32GB) Corsair Dominator Titanium DDR5 6600MHz RGB'],
                    ['SSD', '2TB Samsung 990 PRO NVMe PCIe Gen 4x4 (Read 7450MB/s)'],
                    ['Tản nhiệt', 'ASUS ROG Ryujin III 360 ARGB màn hình LCD 3.5 inch'],
                    ['Nguồn PSU', 'Corsair HX1200 1200W 80 Plus Platinum Full Modular'],
                    ['Vỏ Case', 'Lian Li O11 Dynamic EVO RGB Black'],
                ],
                'stock_hcm' => 5,
                'stock_hn' => 3,
            ],

            // 7. PC TechStore Esport Pro
            [
                'name' => 'PC TechStore Esport Pro Ryzen 7 7800X3D | RTX 4070 SUPER | 32GB RAM',
                'slug' => 'pc-techstore-esport-pro',
                'sku' => 'PC-ESPORT-7800X3D',
                'category_id' => $catPcEsport->id,
                'brand_id' => $brandMsi?->id,
                'price' => 45990000,
                'sale_price' => 42500000,
                'cost_price' => 35000000,
                'thumbnail' => 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?auto=format&fit=crop&w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?auto=format&fit=crop&w=800&q=80',
                ],
                'short_description' => 'Cấu hình thi đấu Esport tối ưu FPS: Ryzen 7 7800X3D 3D V-Cache, MSI RTX 4070 SUPER Gaming X Slim.',
                'description' => '<p>Dành riêng cho các game thủ chuyên nghiệp CS2, Valorant, Naraka, Apex Legends. Đạt mốc 500+ FPS ổn định, nhiệt độ vận hành mát mẻ và độ trễ hệ thống cực thấp.</p>',
                'is_featured' => true,
                'is_active' => true,
                'specs' => [
                    ['CPU', 'AMD Ryzen 7 7800X3D (8C/16T, 96MB 3D V-Cache Gaming King)'],
                    ['Mainboard', 'MSI MAG B650 TOMAHAWK WIFI'],
                    ['VGA', 'MSI GeForce RTX 4070 SUPER 12G GAMING X SLIM'],
                    ['RAM', '32GB (2x16GB) Corsair Vengeance RGB DDR5 6000MHz'],
                    ['SSD', '1TB Kingston KC3000 PCIe 4.0 NVMe'],
                    ['Tản nhiệt', 'Thermalright Frozen Prism 360 Black ARGB'],
                    ['Nguồn PSU', 'MSI MAG A750GL PCIE5 750W 80 Plus Gold ATX 3.0'],
                ],
                'stock_hcm' => 8,
                'stock_hn' => 6,
            ],

            // 8. Card Màn Hình ASUS ROG Strix GeForce RTX 4090 OC 24GB
            [
                'name' => 'Card Màn Hình ASUS ROG Strix GeForce RTX 4090 24GB GDDR6X OC',
                'slug' => 'asus-rog-strix-geforce-rtx-4090-24gb-oc',
                'sku' => 'VGA-ROG-4090-OC',
                'category_id' => $catVga->id,
                'brand_id' => $brandAsus?->id,
                'price' => 59990000,
                'sale_price' => 56990000,
                'cost_price' => 48000000,
                'thumbnail' => 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?auto=format&fit=crop&w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1550745165-9bc0b252726f?auto=format&fit=crop&w=800&q=80',
                ],
                'short_description' => 'Card đồ họa mạnh nhất hành tinh: Kiến trúc Ada Lovelace, buồng hơi vapor chamber tản nhiệt cực đỉnh.',
                'description' => '<p>ASUS ROG Strix RTX 4090 OC Edition đưa giới hạn hiệu năng đồ họa lên một tầm cao mới. Khung kim loại đúc liền khối nguyên khối chống cong vênh, 3 quạt Axial-tech tăng 23% luồng gió.</p>',
                'is_featured' => true,
                'is_active' => true,
                'specs' => [
                    ['Chip đồ họa', 'NVIDIA GeForce RTX 4090'],
                    ['Bộ nhớ', '24GB GDDR6X, 384-bit, 21 Gbps'],
                    ['Xung nhịp Boost', '2640 MHz (OC Mode) / 2610 MHz (Default Mode)'],
                    ['Nhân CUDA', '16384 Units'],
                    ['Cổng xuất hình', '2x HDMI 2.1a, 3x DisplayPort 1.4a'],
                    ['Công suất nguồn đề nghị', '1000W'],
                ],
                'stock_hcm' => 6,
                'stock_hn' => 4,
            ],

            // 9. Card Màn Hình MSI GeForce RTX 4070 Ti SUPER 16G GAMING X SLIM
            [
                'name' => 'Card Màn Hình MSI GeForce RTX 4070 Ti SUPER 16G GAMING X SLIM',
                'slug' => 'msi-geforce-rtx-4070-ti-super-16g-gaming-x-slim',
                'sku' => 'VGA-MSI-4070TIS',
                'category_id' => $catVga->id,
                'brand_id' => $brandMsi?->id,
                'price' => 24500000,
                'sale_price' => 22990000,
                'cost_price' => 19500000,
                'thumbnail' => 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?auto=format&fit=crop&w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1550745165-9bc0b252726f?auto=format&fit=crop&w=800&q=80',
                ],
                'short_description' => 'VGA 16GB GDDR6X 256-bit, kiến trúc Ada Lovelace, DLSS 3.5 và Ray Tracing thế hệ mới.',
                'description' => '<p>MSI RTX 4070 Ti Super GAMING X SLIM sở hữu thiết kế mỏng nhẹ tinh giản nhưng không hề hy sinh hiệu năng tản nhiệt với quạt TORX FAN 5.0 và lá tản nhiệt Wave Curved 2.0.</p>',
                'is_featured' => true,
                'is_active' => true,
                'specs' => [
                    ['Chip đồ họa', 'NVIDIA GeForce RTX 4070 Ti SUPER'],
                    ['Bộ nhớ', '16GB GDDR6X, 256-bit, 21 Gbps'],
                    ['Xung nhịp Boost', '2670 MHz (Extreme Performance)'],
                    ['Nhân CUDA', '8448 Units'],
                    ['Cổng xuất hình', '3x DisplayPort v1.4a, 1x HDMI 2.1a'],
                    ['Công suất nguồn đề nghị', '700W'],
                ],
                'stock_hcm' => 12,
                'stock_hn' => 8,
            ],

            // 10. CPU Intel Core i9-14900K
            [
                'name' => 'Bộ Vi Xử Lý CPU Intel Core i9-14900K (Box Chính Hãng)',
                'slug' => 'cpu-intel-core-i9-14900k-box',
                'sku' => 'CPU-INTEL-I914900K',
                'category_id' => $catCpu->id,
                'brand_id' => $brandIntel?->id,
                'price' => 15800000,
                'sale_price' => 14890000,
                'cost_price' => 12500000,
                'thumbnail' => 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?auto=format&fit=crop&w=800&q=80',
                'gallery' => [],
                'short_description' => '24 nhân, 32 luồng, xung nhịp Turbo tối đa 6.0GHz với công nghệ Intel Thermal Velocity Boost.',
                'description' => '<p>CPU đầu bảng kiến trúc Raptor Lake Refresh mang lại trải nghiệm chơi game và xử lý đa nhiệm đồ họa mượt mà nhất. Hỗ trợ chuẩn PCIe 5.0 và bộ nhớ RAM DDR5 tốc độ cao.</p>',
                'is_featured' => false,
                'is_active' => true,
                'specs' => [
                    ['Socket', 'LGA1700 (Tương thích Z790, B760)'],
                    ['Số nhân / Số luồng', '24 Nhân (8 P-Core + 16 E-Core) / 32 Luồng'],
                    ['Xung nhịp cơ bản', 'P-Core: 3.20 GHz | E-Core: 2.40 GHz'],
                    ['Xung nhịp tối đa', 'Up to 6.00 GHz (Thermal Velocity Boost)'],
                    ['Bộ nhớ đệm Cache', '36MB Intel Smart Cache (L3) + 32MB L2'],
                    ['Điện năng tiêu thụ (TDP)', '125W Base / 253W Turbo'],
                ],
                'stock_hcm' => 18,
                'stock_hn' => 12,
            ],

            // 11. CPU AMD Ryzen 7 7800X3D
            [
                'name' => 'Bộ Vi Xử Lý CPU AMD Ryzen 7 7800X3D (Box Chính Hãng)',
                'slug' => 'cpu-amd-ryzen-7-7800x3d-box',
                'sku' => 'CPU-AMD-R7-7800X3D',
                'category_id' => $catCpu->id,
                'brand_id' => $brandAmd?->id,
                'price' => 11990000,
                'sale_price' => 10990000,
                'cost_price' => 9200000,
                'thumbnail' => 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?auto=format&fit=crop&w=800&q=80',
                'gallery' => [],
                'short_description' => '8 nhân 16 luồng, bộ nhớ đệm 3D V-Cache khổng lồ 96MB, ông vua vi xử lý gaming hiện nay.',
                'description' => '<p>AMD Ryzen 7 7800X3D với công nghệ AMD 3D V-Cache thế hệ 2 mang lại hiệu năng gaming vượt trội hơn mọi đối thủ trong tầm giá, tiết kiệm điện năng đáng kinh ngạc chỉ 120W TDP.</p>',
                'is_featured' => true,
                'is_active' => true,
                'specs' => [
                    ['Socket', 'AM5 (Tương thích bo mạch chủ X670, B650, A620)'],
                    ['Số nhân / Số luồng', '8 Nhân / 16 Luồng (Kiến trúc Zen 4 5nm)'],
                    ['Xung nhịp cơ bản / Boost', '4.2 GHz / 5.0 GHz Max Boost'],
                    ['Bộ nhớ đệm Cache', '96MB L3 3D V-Cache + 8MB L2 (Tổng 104MB)'],
                    ['Đồ họa tích hợp', 'AMD Radeon Graphics (2 Cores, 2200 MHz)'],
                ],
                'stock_hcm' => 20,
                'stock_hn' => 15,
            ],

            // 12. RAM Corsair Dominator Titanium RGB 32GB DDR5 6000MHz
            [
                'name' => 'Bộ Nhớ Trong RAM Corsair Dominator Titanium RGB 32GB (2x16GB) DDR5 6000MHz',
                'slug' => 'ram-corsair-dominator-titanium-rgb-32gb-ddr5-6000',
                'sku' => 'RAM-DOMINATOR-32G',
                'category_id' => $catRam->id,
                'brand_id' => $brandCorsair?->id,
                'price' => 5490000,
                'sale_price' => 4990000,
                'cost_price' => 3800000,
                'thumbnail' => 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?auto=format&fit=crop&w=800&q=80',
                'gallery' => [],
                'short_description' => 'RAM DDR5 cao cấp nhất của Corsair, vỏ nhôm phay CNC tinh xảo, 11 bóng LED Capellix RGB rực rỡ.',
                'description' => '<p>Dominator Titanium là định nghĩa mới của phong cách và hiệu năng. Sử dụng chip nhớ binned tuyển chọn nghiêm ngặt cho khả năng ép xung tối đa, hỗ trợ Intel XMP 3.0 và AMD EXPO.</p>',
                'is_featured' => false,
                'is_active' => true,
                'specs' => [
                    ['Chuẩn RAM', 'DDR5 U-DIMM 288-pin'],
                    ['Dung lượng', '32GB Kit (2 thanh x 16GB)'],
                    ['Tốc độ Bus', '6000MHz (PC5-48000)'],
                    ['Độ trễ Timing', 'CL30-36-36-76'],
                    ['Điện áp', '1.40V'],
                    ['Tản nhiệt & LED', 'Vỏ nhôm Anodized + LED Capellix RGB điều khiển qua iCUE'],
                ],
                'stock_hcm' => 25,
                'stock_hn' => 15,
            ],

            // 13. SSD Samsung 990 PRO 2TB PCIe 4.0 NVMe M.2
            [
                'name' => 'Ổ Cứng SSD Samsung 990 PRO 2TB PCIe Gen 4.0 x4 M.2 NVMe',
                'slug' => 'ssd-samsung-990-pro-2tb-pcie-4',
                'sku' => 'SSD-SS-990PRO-2TB',
                'category_id' => $catSsd->id,
                'brand_id' => $brandSamsung?->id,
                'price' => 5290000,
                'sale_price' => 4790000,
                'cost_price' => 3900000,
                'thumbnail' => 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?auto=format&fit=crop&w=800&q=80',
                'gallery' => [],
                'short_description' => 'Tốc độ đọc/ghi tuần tự đỉnh cao 7.450 / 6.900 MB/s, công nghệ tản nhiệt mạ niken thông minh.',
                'description' => '<p>Samsung 990 PRO giải phóng hiệu năng tối đa cho gaming nặng và biên tập video 4K/8K. Tiết kiệm điện hơn 50% so với thế hệ 980 PRO trước đó, hỗ trợ phần mềm Samsung Magician giám sát toàn diện.</p>',
                'is_featured' => true,
                'is_active' => true,
                'specs' => [
                    ['Chuẩn kết nối', 'PCIe Gen 4.0 x4, NVMe 2.0'],
                    ['Kích thước', 'M.2 2280'],
                    ['Dung lượng', '2TB (2000GB)'],
                    ['Tốc độ đọc tuần tự', 'Lên đến 7,450 MB/s'],
                    ['Tốc độ ghi tuần tự', 'Lên đến 6,900 MB/s'],
                    ['Độ bền TBW', '1,200 TBW, bảo hành 5 năm'],
                ],
                'stock_hcm' => 30,
                'stock_hn' => 20,
            ],

            // 14. Màn Hình Gaming Samsung Odyssey Neo G8 32 inch 4K 240Hz
            [
                'name' => 'Màn Hình Gaming Samsung Odyssey Neo G8 32 inch 4K 240Hz 1ms',
                'slug' => 'samsung-odyssey-neo-g8-32-inch-4k-240hz',
                'sku' => 'MON-SS-NEOG8',
                'category_id' => $catMonitor->id,
                'brand_id' => $brandSamsung?->id,
                'price' => 26900000,
                'sale_price' => 23490000,
                'cost_price' => 18000000,
                'thumbnail' => 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?auto=format&fit=crop&w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1547082299-de196ea013d6?auto=format&fit=crop&w=800&q=80',
                ],
                'short_description' => 'Màn hình cong Quantum Mini LED 4K UHD đầu tiên trên thế giới với tần số quét 240Hz siêu mượt.',
                'description' => '<p>Đắm chìm vào thế giới game với độ phân giải 4K sắc nét và tần số quét 240Hz dẫn đầu thế giới. Công nghệ Quantum Matrix với đèn nền Quantum Mini LED mang lại độ tương phản tuyệt đối và độ sâu đen vượt trội.</p>',
                'is_featured' => true,
                'is_active' => true,
                'specs' => [
                    ['Kích thước', '32 inch, độ cong 1000R hoàn hảo góc nhìn'],
                    ['Độ phân giải', '4K UHD (3840 x 2160)'],
                    ['Tấm nền & Đèn nền', 'VA cong, Quantum Mini-LED, Quantum HDR 2000'],
                    ['Tần số quét', '240Hz, thời gian phản hồi 1ms (GtG)'],
                    ['Công nghệ đồng bộ', 'AMD FreeSync Premium Pro, tương thích G-Sync'],
                    ['Đèn hậu CoreSync', 'Đồng bộ hiệu ứng ánh sáng sau lưng với màn hình'],
                ],
                'stock_hcm' => 10,
                'stock_hn' => 6,
            ],

            // 15. Màn Hình ASUS ROG Swift OLED PG27AQDM 27 inch 240Hz
            [
                'name' => 'Màn Hình Gaming ASUS ROG Swift OLED PG27AQDM 27 inch 2K 240Hz',
                'slug' => 'asus-rog-swift-oled-pg27aqdm-27-inch',
                'sku' => 'MON-ROG-OLED27',
                'category_id' => $catMonitor->id,
                'brand_id' => $brandAsus?->id,
                'price' => 25900000,
                'sale_price' => 23900000,
                'cost_price' => 19000000,
                'thumbnail' => 'https://images.unsplash.com/photo-1547082299-de196ea013d6?auto=format&fit=crop&w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?auto=format&fit=crop&w=800&q=80',
                ],
                'short_description' => 'Màn hình OLED 2K QHD thời gian phản hồi siêu tốc 0.03ms, tần số quét 240Hz, tản nhiệt tùy chỉnh độc quyền.',
                'description' => '<p>Tận hưởng hình ảnh tương phản vô hạn và màu sắc rực rỡ với tấm nền OLED 27 inch. Tản nhiệt thông minh tùy biến kết hợp khe thông gió trên nắp sau giữ cho tấm nền luôn mát, ngăn ngừa hiện tượng lưu ảnh burn-in.</p>',
                'is_featured' => true,
                'is_active' => true,
                'specs' => [
                    ['Kích thước', '26.5 inch Flat'],
                    ['Độ phân giải', '2K QHD (2560 x 1440) OLED'],
                    ['Tần số quét / Phản hồi', '240Hz, phản hồi 0.03ms (GTG)'],
                    ['Màu sắc', '1.07 tỷ màu, 99% DCI-P3, Delta E < 2'],
                    ['Độ sáng tối đa', '1000 nits (Peak HDR 3% Window)'],
                ],
                'stock_hcm' => 8,
                'stock_hn' => 5,
            ],

            // 16. Màn Hình Đồ Họa Dell UltraSharp U2724DE 27 inch 2K
            [
                'name' => 'Màn Hình Đồ Họa Dell UltraSharp U2724DE 27 inch 2K IPS Black 120Hz',
                'slug' => 'dell-ultrasharp-u2724de-27-inch-2k',
                'sku' => 'MON-DELL-U2724DE',
                'category_id' => $catMonitor->id,
                'brand_id' => $brandDell?->id,
                'price' => 13490000,
                'sale_price' => 12490000,
                'cost_price' => 9800000,
                'thumbnail' => 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?auto=format&fit=crop&w=800&q=80',
                'gallery' => [],
                'short_description' => 'Công nghệ tấm nền IPS Black tương phản 2000:1, tần số quét 120Hz, cổng kết nối Thunderbolt 4 Hub 90W PD.',
                'description' => '<p>Lựa chọn số 1 cho các designer, nhiếp ảnh gia và lập trình viên. Màn hình tích hợp cổng Thunderbolt 4 vừa truyền tín hiệu hình ảnh, dữ liệu mạng LAN RJ45 vừa sạc nhanh laptop chỉ với 1 sợi cáp duy nhất.</p>',
                'is_featured' => false,
                'is_active' => true,
                'specs' => [
                    ['Kích thước', '27 inch tỷ lệ 16:9'],
                    ['Độ phân giải', '2K QHD (2560 x 1440) @ 120Hz'],
                    ['Tấm nền', 'IPS Black, tương phản tĩnh 2000:1'],
                    ['Độ phủ màu', '100% sRGB, 98% DCI-P3, 100% BT.709'],
                    ['Cổng kết nối', 'Thunderbolt 4 Hub (90W PD), DP 1.4, HDMI 2.1, RJ45 LAN 2.5Gbps, USB-C 15W'],
                ],
                'stock_hcm' => 15,
                'stock_hn' => 10,
            ],

            // 17. Chuột Gaming Không Dây Logitech G Pro X Superlight 2
            [
                'name' => 'Chuột Gaming Không Dây Logitech G Pro X Superlight 2 (White)',
                'slug' => 'logitech-g-pro-x-superlight-2-white',
                'sku' => 'GEAR-LOGI-SL2',
                'category_id' => $catGear->id,
                'brand_id' => $brandLogitech?->id,
                'price' => 3890000,
                'sale_price' => 3290000,
                'cost_price' => 2400000,
                'thumbnail' => 'https://images.unsplash.com/photo-1615663245857-ac93bb7c39e7?auto=format&fit=crop&w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?auto=format&fit=crop&w=800&q=80',
                ],
                'short_description' => 'Trọng lượng siêu nhẹ chỉ 60g, switch lai cơ - quang học LIGHTFORCE, cảm biến HERO 2 32.000 DPI.',
                'description' => '<p>Chuột Esports được các vận động viên thể thao điện tử chuyên nghiệp hàng đầu tin dùng nhất thế giới. Kết nối không dây LIGHTSPEED siêu nhanh với polling rate lên tới 4000Hz (0.25ms).</p>',
                'is_featured' => true,
                'is_active' => true,
                'specs' => [
                    ['Cảm biến', 'HERO 2 thế hệ mới, độ phân giải 100 - 32.000 DPI'],
                    ['Switch phím', 'Hybrid optical-mechanical LIGHTFORCE'],
                    ['Trọng lượng', 'Chỉ 60g (Siêu nhẹ không đục lỗ)'],
                    ['Thời lượng pin', 'Lên đến 95 giờ sử dụng liên tục'],
                    ['Cổng sạc', 'USB Type-C tiện lợi'],
                    ['Tương thích', 'Logitech G HUB Software'],
                ],
                'stock_hcm' => 35,
                'stock_hn' => 20,
            ],

            // 18. Bàn Phím Cơ Không Dây ASUS ROG Azoth 75% Custom OLED
            [
                'name' => 'Bàn Phím Cơ Không Dây ASUS ROG Azoth 75% OLED Display',
                'slug' => 'ban-phim-co-asus-rog-azoth-75-oled',
                'sku' => 'GEAR-ROG-AZOTH',
                'category_id' => $catGear->id,
                'brand_id' => $brandAsus?->id,
                'price' => 6990000,
                'sale_price' => 5990000,
                'cost_price' => 4500000,
                'thumbnail' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=800&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1618384887929-16ec33fab9ef?auto=format&fit=crop&w=800&q=80',
                ],
                'short_description' => 'Bàn phím cơ Custom 75% gasket mount, màn hình OLED 2 inch, switch ROG NX pre-lubed mượt mà.',
                'description' => '<p>ASUS ROG Azoth kết hợp giữa bàn phím gaming cao cấp và thế giới bàn phím custom audiophile. Đệm silicon 3 lớp triệt tiêu âm vang, núm xoay điều khiển 3 hướng thông minh, kết nối 3 chế độ (Bluetooth, 2.4GHz SpeedNova, USB-C).</p>',
                'is_featured' => true,
                'is_active' => true,
                'specs' => [
                    ['Layout phím', '75% (81 phím)'],
                    ['Cấu trúc gõ', 'Gasket Mount với đệm silicon 3 lớp tiêu âm'],
                    ['Switch', 'ROG NX Mechanical Switches (Hotswap thay switch nóng)'],
                    ['Màn hình phụ', '2-inch OLED hiển thị thông số CPU, pin, ảnh gif động'],
                    ['Kết nối', 'ROG SpeedNova 2.4GHz / Bluetooth 5.1 / Dây cáp USB-C'],
                    ['Thời lượng pin', 'Lên đến hơn 2.000 giờ (tắt LED & OLED)'],
                ],
                'stock_hcm' => 15,
                'stock_hn' => 10,
            ],

            // 19. Tai Nghe Gaming Không Dây SteelSeries Arctis Nova Pro Wireless
            [
                'name' => 'Tai Nghe Gaming Không Dây SteelSeries Arctis Nova Pro Wireless',
                'slug' => 'steelseries-arctis-nova-pro-wireless',
                'sku' => 'GEAR-STEEL-NOVAPRO',
                'category_id' => $catGear->id,
                'brand_id' => $brandSteel?->id,
                'price' => 9990000,
                'sale_price' => 8890000,
                'cost_price' => 6900000,
                'thumbnail' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=800&q=80',
                'gallery' => [],
                'short_description' => 'Hệ thống âm thanh Hi-Res Nova Pro Acoustic, chống ồn chủ động ANC 4 micro, hệ thống 2 pin tráo đổi liên tục.',
                'description' => '<p>Chiếc tai nghe gaming tối thượng cho game thủ eSports. Hub Base Station OLED cho phép chuyển đổi nhanh giữa PC và PlayStation chỉ với 1 nút bấm. Âm thanh vòm 360 độ Spatial Audio định vị chuẩn xác từng bước chân địch.</p>',
                'is_featured' => false,
                'is_active' => true,
                'specs' => [
                    ['Màng loa', 'Neodymium cao cấp Hi-Res 40mm (10 - 40,000 Hz)'],
                    ['Chống ồn', 'Active Noise Cancellation (ANC) 4 mic hybrid'],
                    ['Thời lượng pin', 'Hệ thống 2 pin kép thay nóng (Vô hạn thời gian chơi game)'],
                    ['Micro', 'ClearCast Gen 2 khử tạp âm AI kéo thụt vào củ tai'],
                    ['Kết nối đồng thời', 'Không dây 2.4GHz Gaming không độ trễ + Bluetooth cho điện thoại'],
                ],
                'stock_hcm' => 10,
                'stock_hn' => 6,
            ],
        ];

        foreach ($products as $pData) {
            $specs = $pData['specs'];
            $stockHcm = $pData['stock_hcm'] ?? 10;
            $stockHn = $pData['stock_hn'] ?? 5;
            $gallery = $pData['gallery'] ?? [];

            unset($pData['specs'], $pData['stock_hcm'], $pData['stock_hn'], $pData['gallery']);

            $product = Product::updateOrCreate(['sku' => $pData['sku']], $pData);

            // Create Specs
            ProductSpecification::where('product_id', $product->id)->delete();
            foreach ($specs as $index => [$key, $val]) {
                ProductSpecification::create([
                    'product_id' => $product->id,
                    'spec_key' => $key,
                    'spec_value' => $val,
                    'sort_order' => $index + 1,
                ]);
            }

            // Create Primary Image
            ProductImage::firstOrCreate(
                ['product_id' => $product->id, 'image_path' => $product->thumbnail],
                ['sort_order' => 1, 'is_primary' => true]
            );

            // Create Gallery Images
            foreach ($gallery as $gIndex => $gUrl) {
                ProductImage::firstOrCreate(
                    ['product_id' => $product->id, 'image_path' => $gUrl],
                    ['sort_order' => $gIndex + 2, 'is_primary' => false]
                );
            }

            // Create Inventory Stock for Warehouse HCM
            if ($warehouseHcm) {
                InventoryStock::updateOrCreate(
                    ['product_id' => $product->id, 'warehouse_id' => $warehouseHcm->id],
                    ['quantity' => $stockHcm, 'reserved_quantity' => 0]
                );
            }

            // Create Inventory Stock for Warehouse HN
            if ($warehouseHn) {
                InventoryStock::updateOrCreate(
                    ['product_id' => $product->id, 'warehouse_id' => $warehouseHn->id],
                    ['quantity' => $stockHn, 'reserved_quantity' => 0]
                );
            }
        }
    }
}
