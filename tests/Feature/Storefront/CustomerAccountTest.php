<?php

namespace Tests\Feature\Storefront;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerAccountTest extends TestCase
{
    use RefreshDatabase;

    protected User $customerA;

    protected User $customerB;

    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customerA = User::create([
            'name' => 'Nguyen Van A',
            'email' => 'vana@test.com',
            'phone' => '0912345678',
            'address' => '123 Nguyen Trai, Q1',
            'role' => 'customer',
            'password' => Hash::make('secret123'),
            'is_active' => true,
        ]);

        $this->customerB = User::create([
            'name' => 'Tran Thi B',
            'email' => 'thib@test.com',
            'phone' => '0987654321',
            'address' => '456 Le Loi, Q1',
            'role' => 'customer',
            'password' => Hash::make('secret123'),
            'is_active' => true,
        ]);

        $category = Category::create([
            'name' => 'Bàn phím cơ',
            'slug' => 'ban-phim-co',
            'is_active' => true,
        ]);

        $brand = Brand::create([
            'name' => 'Akko',
            'slug' => 'akko',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'name' => 'Akko 3098B Plus Multi-modes',
            'slug' => 'akko-3098b-plus',
            'sku' => 'AKKO-3098B',
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'price' => 1990000,
            'stock_quantity' => 10,
            'is_active' => true,
        ]);
    }

    public function test_customer_registration_success(): void
    {
        $response = $this->post(route('register.post'), [
            'name' => 'Le Van Cuong',
            'email' => 'cuong@test.com',
            'phone' => '0933112233',
            'address' => '789 CMT8, Q10',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertDatabaseHas('users', [
            'email' => 'cuong@test.com',
            'role' => 'customer',
            'is_active' => true,
        ]);
        $this->assertAuthenticated();
    }

    public function test_customer_registration_fails_on_duplicate_email_or_invalid_phone(): void
    {
        // Duplicate email
        $responseDup = $this->post(route('register.post'), [
            'name' => 'Duplicate User',
            'email' => 'vana@test.com', // Already taken by customerA
            'phone' => '0911223344',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $responseDup->assertSessionHasErrors(['email']);

        // Invalid phone number
        $responsePhone = $this->post(route('register.post'), [
            'name' => 'Invalid Phone User',
            'email' => 'invalidphone@test.com',
            'phone' => '12345', // Not Vietnamese phone regex
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $responsePhone->assertSessionHasErrors(['phone']);
    }

    public function test_customer_login_success_and_logout(): void
    {
        $response = $this->post(route('login.post'), [
            'email' => 'vana@test.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($this->customerA);

        $logoutResponse = $this->post(route('logout'));
        $logoutResponse->assertRedirect(route('home'));
        $this->assertGuest();
    }

    public function test_customer_login_fails_for_locked_account(): void
    {
        $this->customerA->update(['is_active' => false]);

        $response = $this->post(route('login.post'), [
            'email' => 'vana@test.com',
            'password' => 'secret123',
        ]);

        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_customer_can_update_profile_and_change_password(): void
    {
        $response = $this->actingAs($this->customerA)->post(route('account.profile.update'), [
            'name' => 'Nguyen Van A Updated',
            'phone' => '0988776655',
            'address' => '999 Nguyen Hue, Q1',
            'current_password' => 'secret123',
            'new_password' => 'newsecret456',
            'new_password_confirmation' => 'newsecret456',
        ]);

        $response->assertSessionHas('success');

        $this->customerA->refresh();
        $this->assertEquals('Nguyen Van A Updated', $this->customerA->name);
        $this->assertEquals('0988776655', $this->customerA->phone);
        $this->assertTrue(Hash::check('newsecret456', $this->customerA->password));
    }

    public function test_customer_can_view_own_orders_and_order_details(): void
    {
        $order = Order::create([
            'order_code' => 'ORD-CUSTA-001',
            'user_id' => $this->customerA->id,
            'customer_name' => $this->customerA->name,
            'customer_email' => $this->customerA->email,
            'customer_phone' => $this->customerA->phone,
            'shipping_address' => $this->customerA->address,
            'subtotal' => 1990000,
            'total_amount' => 1990000,
            'payment_method' => 'cod',
            'payment_status' => 'unpaid',
            'order_status' => 'confirmed',
            'inventory_settled' => false,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'product_sku' => $this->product->sku,
            'quantity' => 1,
            'unit_price' => 1990000,
            'total_price' => 1990000,
        ]);

        $ordersList = $this->actingAs($this->customerA)->get(route('account.orders'));
        $ordersList->assertStatus(200);
        $ordersList->assertSee('ORD-CUSTA-001');

        $orderDetail = $this->actingAs($this->customerA)->get(route('account.orders.show', 'ORD-CUSTA-001'));
        $orderDetail->assertStatus(200);
        $orderDetail->assertSee('Akko 3098B Plus Multi-modes');
    }

    public function test_idor_protection_customer_cannot_view_another_customers_order(): void
    {
        // Order belongs to Customer B
        Order::create([
            'order_code' => 'ORD-CUSTB-SECRET',
            'user_id' => $this->customerB->id,
            'customer_name' => $this->customerB->name,
            'customer_email' => $this->customerB->email,
            'customer_phone' => $this->customerB->phone,
            'shipping_address' => $this->customerB->address,
            'subtotal' => 1990000,
            'total_amount' => 1990000,
            'payment_method' => 'cod',
            'payment_status' => 'unpaid',
            'order_status' => 'confirmed',
            'inventory_settled' => false,
        ]);

        // Customer A attempts to access Customer B's order
        $response = $this->actingAs($this->customerA)->get(route('account.orders.show', 'ORD-CUSTB-SECRET'));
        $response->assertStatus(404); // Blocked by where('user_id', Auth::id())->firstOrFail()
    }

    public function test_customer_can_review_delivered_product(): void
    {
        $deliveredOrder = Order::create([
            'order_code' => 'ORD-DELIVERED-001',
            'user_id' => $this->customerA->id,
            'customer_name' => $this->customerA->name,
            'customer_email' => $this->customerA->email,
            'customer_phone' => $this->customerA->phone,
            'shipping_address' => $this->customerA->address,
            'subtotal' => 1990000,
            'total_amount' => 1990000,
            'payment_method' => 'cod',
            'payment_status' => 'paid',
            'order_status' => 'delivered',
            'inventory_settled' => true,
        ]);

        OrderItem::create([
            'order_id' => $deliveredOrder->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'product_sku' => $this->product->sku,
            'quantity' => 1,
            'unit_price' => 1990000,
            'total_price' => 1990000,
        ]);

        $response = $this->actingAs($this->customerA)->post(route('account.reviews.store'), [
            'order_id' => $deliveredOrder->id,
            'product_id' => $this->product->id,
            'rating' => 5,
            'comment' => 'Bàn phím gõ rất êm, switch mượt mà, build rất chắc chắn!',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('reviews', [
            'order_id' => $deliveredOrder->id,
            'product_id' => $this->product->id,
            'user_id' => $this->customerA->id,
            'rating' => 5,
            'status' => 'approved',
        ]);
    }

    public function test_customer_cannot_review_undelivered_order(): void
    {
        $pendingOrder = Order::create([
            'order_code' => 'ORD-PENDING-001',
            'user_id' => $this->customerA->id,
            'customer_name' => $this->customerA->name,
            'customer_email' => $this->customerA->email,
            'customer_phone' => $this->customerA->phone,
            'shipping_address' => $this->customerA->address,
            'subtotal' => 1990000,
            'total_amount' => 1990000,
            'payment_method' => 'cod',
            'payment_status' => 'unpaid',
            'order_status' => 'pending', // NOT delivered
            'inventory_settled' => false,
        ]);

        OrderItem::create([
            'order_id' => $pendingOrder->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'product_sku' => $this->product->sku,
            'quantity' => 1,
            'unit_price' => 1990000,
            'total_price' => 1990000,
        ]);

        $response = $this->actingAs($this->customerA)->post(route('account.reviews.store'), [
            'order_id' => $pendingOrder->id,
            'product_id' => $this->product->id,
            'rating' => 5,
            'comment' => 'Chưa nhận được nhưng vẫn muốn đánh giá',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('reviews', [
            'order_id' => $pendingOrder->id,
            'product_id' => $this->product->id,
        ]);
    }
}
