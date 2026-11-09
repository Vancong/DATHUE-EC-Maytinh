<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin account
        User::updateOrCreate(
            ['email' => 'admin@techstore.vn'],
            [
                'name' => 'Quản Trị Viên TechStore',
                'phone' => '0901234567',
                'address' => 'Tòa nhà TechStore, Số 1 Đại Cồ Việt, Hai Bà Trưng, Hà Nội',
                'role' => 'admin',
                'is_active' => true,
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        // Demo customer default
        User::updateOrCreate(
            ['email' => 'customer@techstore.vn'],
            [
                'name' => 'Nguyễn Văn Khách',
                'phone' => '0987654321',
                'address' => 'Số 123 Đường Nguyễn Trãi, Phường Bến Thành, Quận 1, TP. Hồ Chí Minh',
                'role' => 'customer',
                'is_active' => true,
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        // Additional realistic customers
        $customers = [
            [
                'email' => 'hoang.nam@gmail.com',
                'name' => 'Trần Hoàng Nam',
                'phone' => '0912345678',
                'address' => 'Số 45 Phố Huế, Phường Hàng Bài, Quận Hoàn Kiếm, Hà Nội',
            ],
            [
                'email' => 'le.minh.quan@gmail.com',
                'name' => 'Lê Minh Quân',
                'phone' => '0934567890',
                'address' => 'Số 88 Đường Bạch Đằng, Phường Thạch Thang, Quận Hải Châu, Đà Nẵng',
            ],
            [
                'email' => 'pham.thanh.hang@gmail.com',
                'name' => 'Phạm Thanh Hằng',
                'phone' => '0978901234',
                'address' => 'Số 12 Đại lộ Hòa Bình, Phường Tân An, Quận Ninh Kiều, Cần Thơ',
            ],
            [
                'email' => 'vu.duc.thang@gmail.com',
                'name' => 'Vũ Đức Thắng',
                'phone' => '0966778899',
                'address' => 'Số 204 Lạch Tray, Quận Ngô Quyền, Hải Phòng',
            ],
        ];

        foreach ($customers as $c) {
            User::updateOrCreate(
                ['email' => $c['email']],
                [
                    'name' => $c['name'],
                    'phone' => $c['phone'],
                    'address' => $c['address'],
                    'role' => 'customer',
                    'is_active' => true,
                    'password' => Hash::make('password123'),
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
