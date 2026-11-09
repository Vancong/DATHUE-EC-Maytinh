<?php

namespace Tests\Feature\Admin;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminProductManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $customer;

    protected Category $category;

    protected Brand $brand;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Boss',
            'email' => 'admin@techstore.vn',
            'role' => 'admin',
            'password' => Hash::make('admin123'),
            'is_active' => true,
        ]);

        $this->customer = User::create([
            'name' => 'Ordinary Customer',
            'email' => 'cust@techstore.vn',
            'role' => 'customer',
            'password' => Hash::make('cust123'),
            'is_active' => true,
        ]);

        $this->category = Category::create([
            'name' => 'Màn hình máy tính',
            'slug' => 'man-hinh-may-tinh',
            'is_active' => true,
        ]);

        $this->brand = Brand::create([
            'name' => 'LG Electronics',
            'slug' => 'lg-electronics',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_view_products_list(): void
    {
        Product::create([
            'name' => 'LG UltraGear 27GR95QE-B OLED 240Hz',
            'slug' => 'lg-ultragear-27gr95qe',
            'sku' => 'LG-27GR95QE',
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'price' => 19900000,
            'stock_quantity' => 4,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.products.index'));
        $response->assertStatus(200);
        $response->assertSee('LG UltraGear 27GR95QE-B');
    }

    public function test_admin_can_create_product_with_specifications(): void
    {
        $payload = [
            'name' => 'LG UltraWide 34WP65C-B',
            'slug' => 'lg-ultrawide-34wp65c-b',
            'sku' => 'LG-34WP65C',
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'price' => 9500000,
            'sale_price' => 8990000,
            'cost_price' => 7500000,
            'stock_quantity' => 12,
            'min_stock' => 3,
            'short_description' => 'Màn hình cong 34 inch WQHD 160Hz',
            'is_active' => true,
            'specs' => [
                ['key' => 'Kích thước', 'value' => '34 inch'],
                ['key' => 'Độ phân giải', 'value' => '3440 x 1440 WQHD'],
                ['key' => 'Tần số quét', 'value' => '160Hz'],
            ],
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.products.store'), $payload);
        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'sku' => 'LG-34WP65C',
            'name' => 'LG UltraWide 34WP65C-B',
            'stock_quantity' => 12,
        ]);

        $this->assertDatabaseHas('product_specifications', [
            'spec_key' => 'Tần số quét',
            'spec_value' => '160Hz',
        ]);
    }

    public function test_admin_create_product_validation_errors(): void
    {
        // 1. Missing required SKU & Category
        $responseMissing = $this->actingAs($this->admin)->post(route('admin.products.store'), [
            'name' => 'Incomplete Monitor',
            'price' => 5000000,
        ]);
        $responseMissing->assertSessionHasErrors(['sku', 'category_id']);

        // 2. sale_price must be less than price (lt:price)
        $responseInvalidPrice = $this->actingAs($this->admin)->post(route('admin.products.store'), [
            'name' => 'Invalid Price Monitor',
            'sku' => 'MON-INV-01',
            'category_id' => $this->category->id,
            'price' => 5000000,
            'sale_price' => 6000000, // Invalid: higher than price
        ]);
        $responseInvalidPrice->assertSessionHasErrors(['sale_price']);
    }

    public function test_admin_can_update_product_and_specs(): void
    {
        $product = Product::create([
            'name' => 'Original Monitor Name',
            'slug' => 'original-monitor-name',
            'sku' => 'MON-ORIGINAL',
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'price' => 5000000,
            'stock_quantity' => 10,
            'min_stock' => 2,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.products.update', $product->id), [
            'name' => 'Updated Monitor Name Pro',
            'sku' => 'MON-ORIGINAL',
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'price' => 5500000,
            'stock_quantity' => 15,
            'min_stock' => 5,
            'is_active' => true,
            'specs' => [
                ['key' => 'Bảo hành', 'value' => '24 tháng chính hãng'],
            ],
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHas('success');

        $product->refresh();
        $this->assertEquals('Updated Monitor Name Pro', $product->name);
        $this->assertEquals(15, $product->stock_quantity);
        $this->assertDatabaseHas('product_specifications', [
            'product_id' => $product->id,
            'spec_key' => 'Bảo hành',
            'spec_value' => '24 tháng chính hãng',
        ]);
    }

    public function test_admin_can_soft_delete_product(): void
    {
        $product = Product::create([
            'name' => 'Monitor To Delete',
            'slug' => 'monitor-to-delete',
            'sku' => 'MON-DEL-01',
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'price' => 4000000,
            'stock_quantity' => 3,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.products.destroy', $product->id));
        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_customer_or_guest_cannot_access_admin_product_management(): void
    {
        // Guest
        $guestIndex = $this->get(route('admin.products.index'));
        $guestIndex->assertRedirect(route('admin.login'));

        // Customer
        $customerIndex = $this->actingAs($this->customer)->get(route('admin.products.index'));
        $customerIndex->assertRedirect(route('admin.login'));
    }
}
