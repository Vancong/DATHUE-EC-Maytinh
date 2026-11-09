<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        Supplier::updateOrCreate(
            ['code' => 'SUP-FPT-01'],
            [
                'name' => 'Công ty Cổ phần Phân phối Synnex FPT',
                'contact_name' => 'Trần Minh Khang',
                'email' => 'contact@synnexfpt.com.vn',
                'phone' => '02873006666',
                'address' => 'Tòa nhà FPT, Phố Duy Tân, Cầu Giấy, Hà Nội',
                'is_active' => true,
            ]
        );

        Supplier::updateOrCreate(
            ['code' => 'SUP-DGW-02'],
            [
                'name' => 'Công ty Cổ phần Thế Giới Số (Digiworld)',
                'contact_name' => 'Lê Thanh Hoàng',
                'email' => 'info@digiworld.com.vn',
                'phone' => '02839290059',
                'address' => 'Số 195 Điện Biên Phủ, Phường 15, Bình Thạnh, TP. Hồ Chí Minh',
                'is_active' => true,
            ]
        );
    }
}
