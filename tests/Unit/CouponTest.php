<?php

namespace Tests\Unit;

use App\Models\Coupon;
use Tests\TestCase;

class CouponTest extends TestCase
{
    public function test_fixed_discount_calculation(): void
    {
        $coupon = new Coupon([
            'discount_type' => 'fixed',
            'discount_value' => 200000,
            'min_order_amount' => 500000,
            'is_active' => true,
        ]);

        // Valid: subtotal > min_order_amount
        $this->assertEquals(200000.0, $coupon->calculateDiscount(1000000));

        // Boundary: fixed discount exceeds subtotal -> capped at subtotal
        $smallCoupon = new Coupon([
            'discount_type' => 'fixed',
            'discount_value' => 200000,
            'min_order_amount' => 0,
            'is_active' => true,
        ]);
        $this->assertEquals(150000.0, $smallCoupon->calculateDiscount(150000));
    }

    public function test_percentage_discount_calculation(): void
    {
        $coupon = new Coupon([
            'discount_type' => 'percentage',
            'discount_value' => 15, // 15%
            'min_order_amount' => 100000,
            'is_active' => true,
        ]);

        $this->assertEquals(150000.0, $coupon->calculateDiscount(1000000));
        $this->assertEquals(300000.0, $coupon->calculateDiscount(2000000));
    }

    public function test_percentage_discount_capped_by_max_discount(): void
    {
        $coupon = new Coupon([
            'discount_type' => 'percentage',
            'discount_value' => 20, // 20%
            'max_discount' => 150000,
            'min_order_amount' => 100000,
            'is_active' => true,
        ]);

        // 20% of 2,000,000 is 400,000 -> capped at 150,000
        $this->assertEquals(150000.0, $coupon->calculateDiscount(2000000));

        // 20% of 500,000 is 100,000 -> below cap, returns 100,000
        $this->assertEquals(100000.0, $coupon->calculateDiscount(500000));

        // Exact cap boundary: 20% of 750,000 = 150,000 -> returns 150,000
        $this->assertEquals(150000.0, $coupon->calculateDiscount(750000));
    }

    public function test_coupon_validation_with_min_order_amount(): void
    {
        $coupon = new Coupon([
            'discount_type' => 'fixed',
            'discount_value' => 50000,
            'min_order_amount' => 500000,
            'is_active' => true,
        ]);

        // Below min amount by 1 unit
        $this->assertFalse($coupon->isValidForAmount(499999));
        $this->assertEquals(0.0, $coupon->calculateDiscount(499999));

        // Exact boundary
        $this->assertTrue($coupon->isValidForAmount(500000));
        $this->assertEquals(50000.0, $coupon->calculateDiscount(500000));

        // Above min amount
        $this->assertTrue($coupon->isValidForAmount(600000));
        $this->assertEquals(50000.0, $coupon->calculateDiscount(600000));
    }

    public function test_coupon_validation_with_active_status(): void
    {
        $inactiveCoupon = new Coupon([
            'discount_type' => 'fixed',
            'discount_value' => 50000,
            'min_order_amount' => 100000,
            'is_active' => false,
        ]);

        $this->assertFalse($inactiveCoupon->isValidForAmount(200000));
        $this->assertEquals(0.0, $inactiveCoupon->calculateDiscount(200000));
    }

    public function test_coupon_validation_with_date_range(): void
    {
        // Future coupon
        $futureCoupon = new Coupon([
            'discount_type' => 'fixed',
            'discount_value' => 50000,
            'start_date' => now()->addDays(2),
            'end_date' => now()->addDays(10),
            'is_active' => true,
        ]);
        $this->assertFalse($futureCoupon->isValidForAmount(200000));
        $this->assertEquals(0.0, $futureCoupon->calculateDiscount(200000));

        // Expired coupon
        $expiredCoupon = new Coupon([
            'discount_type' => 'fixed',
            'discount_value' => 50000,
            'start_date' => now()->subDays(10),
            'end_date' => now()->subDays(1),
            'is_active' => true,
        ]);
        $this->assertFalse($expiredCoupon->isValidForAmount(200000));
        $this->assertEquals(0.0, $expiredCoupon->calculateDiscount(200000));

        // Valid current coupon
        $validCoupon = new Coupon([
            'discount_type' => 'fixed',
            'discount_value' => 50000,
            'start_date' => now()->subDays(2),
            'end_date' => now()->addDays(2),
            'is_active' => true,
        ]);
        $this->assertTrue($validCoupon->isValidForAmount(200000));
        $this->assertEquals(50000.0, $validCoupon->calculateDiscount(200000));
    }

    public function test_coupon_validation_with_usage_limit(): void
    {
        // Limit reached
        $exhaustedCoupon = new Coupon([
            'discount_type' => 'fixed',
            'discount_value' => 50000,
            'usage_limit' => 5,
            'used_count' => 5,
            'is_active' => true,
        ]);
        $this->assertFalse($exhaustedCoupon->isValidForAmount(200000));
        $this->assertEquals(0.0, $exhaustedCoupon->calculateDiscount(200000));

        // Usage available
        $availableCoupon = new Coupon([
            'discount_type' => 'fixed',
            'discount_value' => 50000,
            'usage_limit' => 5,
            'used_count' => 4,
            'is_active' => true,
        ]);
        $this->assertTrue($availableCoupon->isValidForAmount(200000));
        $this->assertEquals(50000.0, $availableCoupon->calculateDiscount(200000));

        // Unlimited usage
        $unlimitedCoupon = new Coupon([
            'discount_type' => 'fixed',
            'discount_value' => 50000,
            'usage_limit' => null,
            'used_count' => 9999,
            'is_active' => true,
        ]);
        $this->assertTrue($unlimitedCoupon->isValidForAmount(200000));
        $this->assertEquals(50000.0, $unlimitedCoupon->calculateDiscount(200000));
    }
}
