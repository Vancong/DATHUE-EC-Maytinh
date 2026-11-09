<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        Warehouse::updateOrCreate(
            ['code' => 'WH-HCM-01'],
            [
                'name' => 'Kho Tổng TP. Hồ Chí Minh',
                'address' => 'Khu Công Nghệ Cao, TP. Thủ Đức, TP. Hồ Chí Minh',
                'phone' => '02838999999',
                'is_active' => true,
            ]
        );

        Warehouse::updateOrCreate(
            ['code' => 'WH-HN-01'],
            [
                'name' => 'Kho Trung Chuyển Hà Nội',
                'address' => 'Số 88 Duy Tân, Cầu Giấy, Hà Nội',
                'phone' => '02437999999',
                'is_active' => true,
            ]
        );
    }
}
