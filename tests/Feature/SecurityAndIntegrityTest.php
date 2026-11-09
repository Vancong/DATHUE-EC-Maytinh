<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SecurityAndIntegrityTest extends TestCase
{
    use RefreshDatabase;

    protected User $customerA;

    protected User $customerB;

    protected User $admin;

    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customerA = User::create([
            'name' => 'Alice Customer',
            'email' => 'alice@test.com',
            'phone' => '0901112233',
            'role' => 'customer',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $this->customerB = User::create([
            'name' => 'Bob Customer',
            'email' => 'bob@test.com',
            'phone' => '0904445566',
            'role' => 'customer',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $this->admin = User::create([
            'name' => 'Site Administrator',
            'email' => 'admin@techstore.vn',
            'role' => 'admin',
            'password' => Hash::make('adminpassword'),
            'is_active' => true,
        ]);

        $category = Category::create([
            'name' => 'CPU Vi xử lý',
            'slug' => 'cpu-vi-xu-ly',
            'is_active' => true,
        ]);

        $brand = Brand::create([
            'name' => 'AMD',
            'slug' => 'amd',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'name' => 'AMD Ryzen 7 7800X3D',
            'slug' => 'amd-ryzen-7-7800x3d',
            'sku' => 'AMD-7800X3D',
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'price' => 11500000,
            'sale_price' => 10990000,
            'stock_quantity' => 5,
            'is_active' => true,
        ]);
    }

    public function test_customer_cannot_tamper_product_price_in_checkout(): void
    {
        // Alice adds product to cart (sale_price is 10,990,000)
        $this->actingAs($this->customerA)->post(route('cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        // Malicious client tries to post total_amount = 1000 and price = 500
        $response = $this->actingAs($this->customerA)->post(route('checkout.process'), [
            'customer_name' => 'Alice Customer',
            'customer_email' => 'alice@test.com',
            'customer_phone' => '0901112233',
            'shipping_address' => 'District 3, HCMC',
            'payment_method' => 'cod',
            'total_amount' => 1000, // Price tampering attempt
            'price' => 500,
        ]);

        $order = Order::where('user_id', $this->customerA->id)->first();
        $this->assertNotNull($order);

        // System must have ignored tampered 1000 and used actual product sale_price (10,990,000)
        $this->assertEquals(10990000.0, (float) $order->subtotal);
        $this->assertEquals(10990000.0, (float) $order->total_amount);

        $orderItem = $order->items->first();
        $this->assertEquals(10990000.0, (float) $orderItem->unit_price);
        $this->assertEquals(10990000.0, (float) $orderItem->total_price);
    }

    public function test_customer_cannot_cancel_another_customer_order_idor(): void
    {
        // Order belongs to Bob
        $bobsOrder = Order::create([
            'order_code' => 'ORD-BOB-001',
            'user_id' => $this->customerB->id,
            'customer_name' => $this->customerB->name,
            'customer_email' => $this->customerB->email,
            'customer_phone' => $this->customerB->phone,
            'shipping_address' => 'Da Nang',
            'subtotal' => 10990000,
            'total_amount' => 10990000,
            'payment_method' => 'cod',
            'payment_status' => 'unpaid',
            'order_status' => 'confirmed',
            'inventory_settled' => false,
        ]);

        // Alice tries to cancel Bob's order
        $response = $this->actingAs($this->customerA)->post(
            route('account.orders.cancel', $bobsOrder->order_code),
            ['reason' => 'Malicious cancellation attempt']
        );

        $response->assertStatus(404); // ModelNotFoundException via where('user_id', Auth::id())

        $bobsOrder->refresh();
        $this->assertEquals('confirmed', $bobsOrder->order_status, 'Bobs order status must remain confirmed.');
    }

    public function test_locked_admin_is_rejected_on_admin_login(): void
    {
        $this->admin->update(['is_active' => false]);

        $response = $this->post(route('admin.login.post'), [
            'email' => 'admin@techstore.vn',
            'password' => 'adminpassword',
        ]);

        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_checkout_fails_and_rolls_back_if_product_out_of_stock_during_process(): void
    {
        $this->actingAs($this->customerA)->post(route('cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        // Stock suddenly dropped to 0 before checkout completes
        $this->product->update(['stock_quantity' => 0]);

        $response = $this->actingAs($this->customerA)->post(route('checkout.process'), [
            'customer_name' => 'Alice Customer',
            'customer_email' => 'alice@test.com',
            'customer_phone' => '0901112233',
            'shipping_address' => 'District 3, HCMC',
            'payment_method' => 'cod',
        ]);

        $response->assertSessionHas('error');

        // Verify transaction rolled back: no orders or items created
        $this->assertEquals(0, Order::where('user_id', $this->customerA->id)->count());
        $this->assertEquals(0, OrderItem::count());
    }
}
