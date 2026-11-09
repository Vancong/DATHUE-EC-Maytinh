<?php

namespace Tests\Feature\Storefront;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CartManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;

    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::create([
            'name' => 'Nguyen Van A',
            'email' => 'vana@test.com',
            'phone' => '0912345678',
            'role' => 'customer',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $category = Category::create([
            'name' => 'Linh kiện PC',
            'slug' => 'linh-kien-pc',
            'is_active' => true,
        ]);

        $brand = Brand::create([
            'name' => 'Corsair',
            'slug' => 'corsair',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'name' => 'RAM Corsair Vengeance 32GB RGB',
            'slug' => 'ram-corsair-vengeance-32gb',
            'sku' => 'RAM-COR-32G',
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'price' => 3200000,
            'sale_price' => 2950000,
            'stock_quantity' => 5,
            'is_active' => true,
        ]);
    }

    public function test_add_product_to_cart_and_view_cart(): void
    {
        $response = $this->actingAs($this->customer)->post(route('cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        $response->assertSessionHas('success');

        $viewCart = $this->actingAs($this->customer)->get(route('cart.index'));
        $viewCart->assertStatus(200);
        $viewCart->assertSee('RAM Corsair Vengeance 32GB RGB');
    }

    public function test_cannot_add_more_than_available_stock_to_cart(): void
    {
        $response = $this->actingAs($this->customer)->post(route('cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 6, // Stock is 5
        ]);

        $response->assertSessionHas('error');
    }

    public function test_update_cart_quantity_within_and_exceeding_stock(): void
    {
        // First add 2
        $this->actingAs($this->customer)->post(route('cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        $cart = app(CartService::class)->getCart();
        $itemId = $cart->items->first()->id;

        // Valid update to 4
        $validUpdate = $this->actingAs($this->customer)->patch(route('cart.update', $itemId), [
            'quantity' => 4,
        ]);
        $validUpdate->assertSessionHas('success');

        // Exceeding update to 10 (stock is 5)
        $invalidUpdate = $this->actingAs($this->customer)->patch(route('cart.update', $itemId), [
            'quantity' => 10,
        ]);
        $invalidUpdate->assertSessionHas('error');
    }

    public function test_remove_single_item_from_cart(): void
    {
        $this->actingAs($this->customer)->post(route('cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        $cart = app(CartService::class)->getCart();
        $itemId = $cart->items->first()->id;

        $remove = $this->actingAs($this->customer)->delete(route('cart.remove', $itemId));
        $remove->assertSessionHas('success');

        $updatedCart = app(CartService::class)->getCart();
        $this->assertEquals(0, $updatedCart->items()->count());
    }

    public function test_clear_entire_cart(): void
    {
        $this->actingAs($this->customer)->post(route('cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        $clear = $this->actingAs($this->customer)->post(route('cart.clear'));
        $clear->assertSessionHas('success');

        $cart = app(CartService::class)->getCart();
        $this->assertEquals(0, $cart->items()->count());
    }

    public function test_apply_valid_coupon_and_verify_discount(): void
    {
        Coupon::create([
            'code' => 'DISCOUNT200K',
            'name' => 'Giảm 200k',
            'discount_type' => 'fixed',
            'discount_value' => 200000,
            'min_order_amount' => 1000000,
            'is_active' => true,
        ]);

        // Add 1 item = 2,950,000 > 1,000,000
        $this->actingAs($this->customer)->post(route('cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($this->customer)->post(route('checkout.apply-coupon'), [
            'code' => 'DISCOUNT200K',
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals('DISCOUNT200K', session('applied_coupon'));

        // Checkout page shows coupon
        $checkout = $this->actingAs($this->customer)->get(route('checkout.index'));
        $checkout->assertStatus(200);
        $checkout->assertSee('DISCOUNT200K');
    }

    public function test_apply_coupon_fails_for_invalid_code_or_below_min_order_amount(): void
    {
        Coupon::create([
            'code' => 'BIGBUY',
            'name' => 'Giảm đơn từ 10tr',
            'discount_type' => 'fixed',
            'discount_value' => 500000,
            'min_order_amount' => 10000000,
            'is_active' => true,
        ]);

        // Add 1 item = 2,950,000 (< 10,000,000)
        $this->actingAs($this->customer)->post(route('cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        // Test non-existent coupon
        $notFound = $this->actingAs($this->customer)->post(route('checkout.apply-coupon'), [
            'code' => 'NONEXISTENT',
        ]);
        $notFound->assertSessionHas('error');

        // Test below min amount
        $belowMin = $this->actingAs($this->customer)->post(route('checkout.apply-coupon'), [
            'code' => 'BIGBUY',
        ]);
        $belowMin->assertSessionHas('error');
    }

    public function test_remove_coupon_from_checkout(): void
    {
        session(['applied_coupon' => 'TESTCODE']);

        $response = $this->actingAs($this->customer)->post(route('checkout.remove-coupon'));
        $response->assertSessionHas('success');
        $this->assertNull(session('applied_coupon'));
    }
}
