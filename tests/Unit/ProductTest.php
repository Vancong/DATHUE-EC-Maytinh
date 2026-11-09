<?php

namespace Tests\Unit;

use App\Models\Product;
use Tests\TestCase;

class ProductTest extends TestCase
{
    public function test_current_price_returns_sale_price_when_available(): void
    {
        $product = new Product([
            'price' => 25000000,
            'sale_price' => 21990000,
        ]);

        $this->assertEquals(21990000.0, $product->current_price);
    }

    public function test_current_price_returns_regular_price_when_sale_price_is_null(): void
    {
        $product = new Product([
            'price' => 25000000,
            'sale_price' => null,
        ]);

        $this->assertEquals(25000000.0, $product->current_price);
    }

    public function test_is_on_sale_status_conditions(): void
    {
        // On sale: sale_price < price
        $onSale = new Product([
            'price' => 20000000,
            'sale_price' => 18000000,
        ]);
        $this->assertTrue($onSale->is_on_sale);

        // Not on sale: sale_price equal to price
        $equalPrice = new Product([
            'price' => 20000000,
            'sale_price' => 20000000,
        ]);
        $this->assertFalse($equalPrice->is_on_sale);

        // Not on sale: sale_price greater than price
        $higherSale = new Product([
            'price' => 20000000,
            'sale_price' => 22000000,
        ]);
        $this->assertFalse($higherSale->is_on_sale);

        // Not on sale: sale_price is null
        $noSale = new Product([
            'price' => 20000000,
            'sale_price' => null,
        ]);
        $this->assertFalse($noSale->is_on_sale);
    }

    public function test_available_stock_clamping(): void
    {
        // Positive stock
        $productPositive = new Product(['stock_quantity' => 15]);
        $this->assertSame(15, $productPositive->available_stock);

        // Zero stock
        $productZero = new Product(['stock_quantity' => 0]);
        $this->assertSame(0, $productZero->available_stock);

        // Negative stock clamped to 0
        $productNegative = new Product(['stock_quantity' => -5]);
        $this->assertSame(0, $productNegative->available_stock);
    }

    public function test_total_quantity_accessor(): void
    {
        $product = new Product(['stock_quantity' => 42]);
        $this->assertSame(42, $product->total_quantity);
    }

    public function test_is_low_stock_evaluation(): void
    {
        // Explicit min_stock = 10
        $productExplicitLow = new Product([
            'stock_quantity' => 8,
            'min_stock' => 10,
        ]);
        $this->assertTrue($productExplicitLow->is_low_stock);

        // Exact boundary: stock_quantity == min_stock
        $productExactBoundary = new Product([
            'stock_quantity' => 10,
            'min_stock' => 10,
        ]);
        $this->assertTrue($productExactBoundary->is_low_stock);

        // Healthy stock above min_stock
        $productHealthy = new Product([
            'stock_quantity' => 11,
            'min_stock' => 10,
        ]);
        $this->assertFalse($productHealthy->is_low_stock);

        // Default min_stock (fallback to 5) when min_stock is null
        $productDefaultLow = new Product([
            'stock_quantity' => 5,
            'min_stock' => null,
        ]);
        $this->assertTrue($productDefaultLow->is_low_stock);

        $productDefaultHealthy = new Product([
            'stock_quantity' => 6,
            'min_stock' => null,
        ]);
        $this->assertFalse($productDefaultHealthy->is_low_stock);
    }

    public function test_is_out_of_stock_evaluation(): void
    {
        // Positive stock
        $inStock = new Product(['stock_quantity' => 1]);
        $this->assertFalse($inStock->is_out_of_stock);

        // Exactly 0 stock
        $zeroStock = new Product(['stock_quantity' => 0]);
        $this->assertTrue($zeroStock->is_out_of_stock);

        // Negative stock (oversold boundary)
        $negativeStock = new Product(['stock_quantity' => -2]);
        $this->assertTrue($negativeStock->is_out_of_stock);
    }
}
