<?php

namespace Tests\Feature\Admin;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductStockImport;
use App\Models\StockReceipt;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminSupplierManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Manager',
            'email' => 'admin@techstore.vn',
            'role' => 'admin',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $this->customer = User::create([
            'name' => 'Regular Customer',
            'email' => 'cust@techstore.vn',
            'role' => 'customer',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);
    }

    public function test_admin_can_view_suppliers_list(): void
    {
        Supplier::create([
            'name' => 'FPT Synnex',
            'code' => 'SUP-FPT-01',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.suppliers.index'));
        $response->assertStatus(200);
        $response->assertSee('FPT Synnex');
        $response->assertSee('SUP-FPT-01');
    }

    public function test_admin_can_create_supplier(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.suppliers.store'), [
            'name' => 'Viễn Sơn Distribution',
            'code' => 'sup-vienson',
            'contact_name' => 'Nguyen Van B',
            'email' => 'vienson@distrib.vn',
            'phone' => '02838383838',
            'address' => '175 Bui Thi Xuan, Q1',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('admin.suppliers.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('suppliers', [
            'name' => 'Viễn Sơn Distribution',
            'code' => 'SUP-VIENSON', // Automatically converted to uppercase
            'email' => 'vienson@distrib.vn',
            'is_active' => true,
        ]);
    }

    public function test_admin_create_supplier_validation_errors(): void
    {
        Supplier::create([
            'name' => 'Existing Supplier',
            'code' => 'SUP-EXISTING',
            'is_active' => true,
        ]);

        // Missing name and duplicate code
        $response = $this->actingAs($this->admin)->post(route('admin.suppliers.store'), [
            'name' => '',
            'code' => 'SUP-EXISTING',
        ]);

        $response->assertSessionHasErrors(['name', 'code']);
    }

    public function test_admin_can_update_supplier(): void
    {
        $supplier = Supplier::create([
            'name' => 'Original Supplier Name',
            'code' => 'SUP-ORIGINAL',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.suppliers.update', $supplier->id), [
            'name' => 'Updated Supplier Name Pro',
            'code' => 'SUP-ORIGINAL',
            'contact_name' => 'Tran Thi C',
            'phone' => '0909999888',
            'is_active' => false,
        ]);

        $response->assertRedirect(route('admin.suppliers.index'));
        $response->assertSessionHas('success');

        $supplier->refresh();
        $this->assertEquals('Updated Supplier Name Pro', $supplier->name);
        $this->assertFalse($supplier->is_active);
    }

    public function test_admin_can_delete_supplier_without_receipts_or_imports(): void
    {
        $supplier = Supplier::create([
            'name' => 'Unused Supplier',
            'code' => 'SUP-UNUSED',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.suppliers.destroy', $supplier->id));
        $response->assertRedirect(route('admin.suppliers.index'));
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('suppliers', ['id' => $supplier->id]);
    }

    public function test_admin_cannot_delete_supplier_with_stock_receipts(): void
    {
        $supplier = Supplier::create([
            'name' => 'Supplier With Receipts',
            'code' => 'SUP-RECEIPTS',
            'is_active' => true,
        ]);

        $warehouse = Warehouse::create([
            'name' => 'Warehouse HCM',
            'code' => 'WH-HCM',
            'is_active' => true,
        ]);

        StockReceipt::create([
            'receipt_code' => 'RCP-TEST-001',
            'warehouse_id' => $warehouse->id,
            'supplier_id' => $supplier->id,
            'user_id' => $this->admin->id,
            'status' => 'confirmed',
            'total_amount' => 50000000,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.suppliers.destroy', $supplier->id));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('suppliers', ['id' => $supplier->id, 'deleted_at' => null]);
    }

    public function test_admin_cannot_delete_supplier_with_stock_imports(): void
    {
        $supplier = Supplier::create([
            'name' => 'Supplier With Imports',
            'code' => 'SUP-IMPORTS',
            'is_active' => true,
        ]);

        $category = Category::create([
            'name' => 'Phụ kiện',
            'slug' => 'phu-kien',
            'is_active' => true,
        ]);

        $brand = Brand::create([
            'name' => 'Razer',
            'slug' => 'razer',
            'is_active' => true,
        ]);

        $product = Product::create([
            'name' => 'Razer DeathAdder V3',
            'slug' => 'razer-deathadder-v3',
            'sku' => 'RAZER-DA-V3',
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'price' => 1800000,
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        ProductStockImport::create([
            'product_id' => $product->id,
            'supplier_id' => $supplier->id,
            'user_id' => $this->admin->id,
            'quantity' => 15,
            'note' => 'Nhập lô hàng Razer chính hãng',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.suppliers.destroy', $supplier->id));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('suppliers', ['id' => $supplier->id, 'deleted_at' => null]);
    }

    public function test_customer_or_guest_cannot_access_supplier_routes(): void
    {
        // Guest
        $guestResp = $this->get(route('admin.suppliers.index'));
        $guestResp->assertRedirect(route('admin.login'));

        // Customer
        $custResp = $this->actingAs($this->customer)->get(route('admin.suppliers.index'));
        $custResp->assertRedirect(route('admin.login'));
    }
}
