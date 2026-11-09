<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        Coupon::updateOrCreate(
            ['code' => 'TECH500'],
            [
                'name' => 'Giảm 500.000đ cho đơn từ 10 triệu',
                'discount_type' => 'fixed',
                'discount_value' => 500000,
                'min_order_amount' => 10000000,
                'max_discount' => 500000,
                'usage_limit' => 100,
                'used_count' => 0,
                'start_date' => now()->subDay(),
                'end_date' => now()->addMonths(3),
                'is_active' => true,
            ]
        );

        Coupon::updateOrCreate(
            ['code' => 'TECH10'],
            [
                'name' => 'Giảm 10% tối đa 1.000.000đ',
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'min_order_amount' => 2000000,
                'max_discount' => 1000000,
                'usage_limit' => 50,
                'used_count' => 0,
                'start_date' => now()->subDay(),
                'end_date' => now()->addMonths(1),
                'is_active' => true,
            ]
        );
    }
}
