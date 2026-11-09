<?php

namespace Tests\Feature\Admin;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminSecurityAndAccessControlTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'TechStore Admin',
            'email' => 'admin@techstore.vn',
            'role' => 'admin',
            'password' => Hash::make('admin123'),
            'is_active' => true,
        ]);

        $this->customer = User::create([
            'name' => 'Regular Customer',
            'email' => 'customer@techstore.vn',
            'role' => 'customer',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);
    }

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $protectedRoutes = [
            route('admin.dashboard'),
            route('admin.orders.index'),
            route('admin.customers.index'),
            route('admin.inventory.index'),
            route('admin.suppliers.index'),
            route('admin.reviews.index'),
        ];

        foreach ($protectedRoutes as $route) {
            $response = $this->get($route);
            $response->assertRedirect(route('admin.login'));
        }
    }

    public function test_customer_is_blocked_from_admin_endpoints(): void
    {
        $protectedRoutes = [
            route('admin.dashboard'),
            route('admin.orders.index'),
            route('admin.customers.index'),
            route('admin.inventory.index'),
            route('admin.suppliers.index'),
            route('admin.reviews.index'),
        ];

        foreach ($protectedRoutes as $route) {
            $response = $this->actingAs($this->customer)->get($route);
            $response->assertRedirect(route('admin.login'));
        }
    }

    public function test_admin_can_toggle_customer_status(): void
    {
        $this->assertTrue($this->customer->is_active);

        // 1. Lock customer account
        $lockResponse = $this->actingAs($this->admin)->post(
            route('admin.customers.toggle-active', $this->customer->id)
        );
        $lockResponse->assertSessionHas('success');

        $this->customer->refresh();
        $this->assertFalse($this->customer->is_active);

        // 2. Unlock customer account
        $unlockResponse = $this->actingAs($this->admin)->post(
            route('admin.customers.toggle-active', $this->customer->id)
        );
        $unlockResponse->assertSessionHas('success');

        $this->customer->refresh();
        $this->assertTrue($this->customer->is_active);
    }

    public function test_admin_can_view_and_moderate_reviews(): void
    {
        $category = Category::create([
            'name' => 'Tai nghe',
            'slug' => 'tai-nghe',
            'is_active' => true,
        ]);

        $brand = Brand::create([
            'name' => 'Sony',
            'slug' => 'sony',
            'is_active' => true,
        ]);

        $product = Product::create([
            'name' => 'Sony WH-1000XM5',
            'slug' => 'sony-wh-1000xm5',
            'sku' => 'SONY-WH-XM5',
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'price' => 7990000,
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        $order = Order::create([
            'order_code' => 'ORD-REVIEW-001',
            'user_id' => $this->customer->id,
            'customer_name' => $this->customer->name,
            'customer_email' => $this->customer->email,
            'customer_phone' => '0909112233',
            'shipping_address' => 'District 1, HCMC',
            'subtotal' => 7990000,
            'total_amount' => 7990000,
            'payment_method' => 'cod',
            'payment_status' => 'paid',
            'order_status' => 'delivered',
            'inventory_settled' => true,
        ]);

        $review = Review::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'user_id' => $this->customer->id,
            'rating' => 4,
            'comment' => 'Chất âm rất tốt, chống ồn đỉnh cao.',
            'status' => 'approved',
        ]);

        // 1. Admin can view reviews index
        $viewResponse = $this->actingAs($this->admin)->get(route('admin.reviews.index'));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('Chất âm rất tốt');

        // 2. Admin moderates status to 'rejected'
        $moderateResponse = $this->actingAs($this->admin)->patch(
            route('admin.reviews.update-status', $review->id),
            ['status' => 'rejected']
        );
        $moderateResponse->assertSessionHas('success');

        $review->refresh();
        $this->assertEquals('rejected', $review->status);

        // 3. Admin deletes review
        $deleteResponse = $this->actingAs($this->admin)->delete(
            route('admin.reviews.destroy', $review->id)
        );
        $deleteResponse->assertSessionHas('success');

        $this->assertSoftDeleted('reviews', ['id' => $review->id]);
    }
}
