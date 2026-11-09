<?php

namespace Tests\Feature\Admin;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminCatalogManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Manager',
            'email' => 'manager@techstore.vn',
            'role' => 'admin',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);
    }

    public function test_admin_can_crud_category(): void
    {
        // 1. Create
        $responseCreate = $this->actingAs($this->admin)->post(route('admin.categories.store'), [
            'name' => 'Card Đồ Họa VGA',
            'slug' => 'card-do-hoa-vga',
            'description' => 'Các dòng card đồ họa NVIDIA và AMD',
            'is_active' => true,
        ]);
        $responseCreate->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', ['slug' => 'card-do-hoa-vga']);

        $category = Category::where('slug', 'card-do-hoa-vga')->first();

        // 2. Update
        $responseUpdate = $this->actingAs($this->admin)->put(route('admin.categories.update', $category->id), [
            'name' => 'Card Màn Hình VGA Cao Cấp',
            'slug' => 'card-man-hinh-vga-cao-cap',
            'is_active' => true,
        ]);
        $responseUpdate->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', ['slug' => 'card-man-hinh-vga-cao-cap']);

        // 3. Delete
        $category->refresh();
        $responseDelete = $this->actingAs($this->admin)->delete(route('admin.categories.destroy', $category->id));
        $responseDelete->assertRedirect(route('admin.categories.index'));
        $this->assertSoftDeleted('categories', ['id' => $category->id]);
    }

    public function test_admin_cannot_delete_category_with_products(): void
    {
        $category = Category::create([
            'name' => 'Bo mạch chủ Mainboard',
            'slug' => 'bo-mach-chu-mainboard',
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'ASUS ROG Maximus Z790 Hero',
            'slug' => 'asus-rog-maximus-z790-hero',
            'sku' => 'ROG-Z790-HERO',
            'category_id' => $category->id,
            'price' => 17500000,
            'stock_quantity' => 2,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.categories.destroy', $category->id));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_admin_can_crud_brand(): void
    {
        // 1. Create
        $responseCreate = $this->actingAs($this->admin)->post(route('admin.brands.store'), [
            'name' => 'MSI Gaming',
            'slug' => 'msi-gaming',
            'description' => 'Thương hiệu phần cứng máy tính hàng đầu',
            'is_active' => true,
        ]);
        $responseCreate->assertRedirect(route('admin.brands.index'));
        $this->assertDatabaseHas('brands', ['slug' => 'msi-gaming']);

        $brand = Brand::where('slug', 'msi-gaming')->first();

        // 2. Update
        $responseUpdate = $this->actingAs($this->admin)->put(route('admin.brands.update', $brand->id), [
            'name' => 'MSI Dragon Edition',
            'slug' => 'msi-dragon-edition',
            'is_active' => true,
        ]);
        $responseUpdate->assertRedirect(route('admin.brands.index'));
        $this->assertDatabaseHas('brands', ['slug' => 'msi-dragon-edition']);

        // 3. Delete
        $brand->refresh();
        $responseDelete = $this->actingAs($this->admin)->delete(route('admin.brands.destroy', $brand->id));
        $responseDelete->assertRedirect(route('admin.brands.index'));
        $this->assertSoftDeleted('brands', ['id' => $brand->id]);
    }

    public function test_admin_cannot_delete_brand_with_products(): void
    {
        $category = Category::create([
            'name' => 'Nguồn máy tính PSU',
            'slug' => 'nguon-may-tinh-psu',
            'is_active' => true,
        ]);

        $brand = Brand::create([
            'name' => 'Seasonic',
            'slug' => 'seasonic',
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Seasonic Prime TX-1000 Titanium',
            'slug' => 'seasonic-prime-tx-1000',
            'sku' => 'SS-PRIME-1000',
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'price' => 7800000,
            'stock_quantity' => 4,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.brands.destroy', $brand->id));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('brands', ['id' => $brand->id]);
    }

    public function test_admin_can_crud_coupon_with_validation(): void
    {
        // 1. Create
        $responseCreate = $this->actingAs($this->admin)->post(route('admin.coupons.store'), [
            'code' => 'SUMMERSALE',
            'name' => 'Khuyến mãi mùa hè',
            'discount_type' => 'percentage',
            'discount_value' => 10,
            'max_discount' => 500000,
            'min_order_amount' => 1000000,
            'usage_limit' => 50,
            'is_active' => true,
        ]);
        $responseCreate->assertRedirect(route('admin.coupons.index'));
        $this->assertDatabaseHas('coupons', ['code' => 'SUMMERSALE']);

        $coupon = Coupon::where('code', 'SUMMERSALE')->first();

        // 2. Update
        $responseUpdate = $this->actingAs($this->admin)->put(route('admin.coupons.update', $coupon->id), [
            'code' => 'SUMMERSALE',
            'name' => 'Khuyến mãi mùa hè V2',
            'discount_type' => 'percentage',
            'discount_value' => 15, // Raised to 15%
            'max_discount' => 700000,
            'min_order_amount' => 1500000,
            'is_active' => true,
        ]);
        $responseUpdate->assertRedirect(route('admin.coupons.index'));
        $this->assertDatabaseHas('coupons', [
            'code' => 'SUMMERSALE',
            'discount_value' => 15,
        ]);

        // 3. Delete
        $coupon->refresh();
        $responseDelete = $this->actingAs($this->admin)->delete(route('admin.coupons.destroy', $coupon->id));
        $responseDelete->assertRedirect(route('admin.coupons.index'));
        $this->assertSoftDeleted('coupons', ['id' => $coupon->id]);
    }

    public function test_admin_coupon_validation_fails_for_invalid_data(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.coupons.store'), [
            'code' => '', // Required
            'discount_type' => 'invalid_type', // Must be percentage or fixed
            'discount_value' => -50, // Must be min:1
        ]);

        $response->assertSessionHasErrors(['code', 'discount_type', 'discount_value']);
    }
}
