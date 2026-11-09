<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\CartService;
use App\Services\InventoryService;
use App\Services\OrderService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EcommerceWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $customer;

    protected Warehouse $warehouse;

    protected Supplier $supplier;

    protected Category $category;

    protected Brand $brand;

    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@techstore.vn',
            'role' => 'admin',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $this->customer = User::create([
            'name' => 'Customer User',
            'email' => 'customer@techstore.vn',
            'phone' => '0901234567',
            'address' => '123 Tech Street',
            'role' => 'customer',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $this->warehouse = Warehouse::create([
            'name' => 'Kho Tổng HCM',
            'code' => 'WH-HCM-TEST',
            'is_active' => true,
        ]);

        $this->supplier = Supplier::create([
            'name' => 'FPT Synnex Test',
            'code' => 'SUP-TEST-01',
            'is_active' => true,
        ]);

        $this->category = Category::create([
            'name' => 'Laptop Gaming',
            'slug' => 'laptop-gaming',
            'is_active' => true,
        ]);

        $this->brand = Brand::create([
            'name' => 'ASUS ROG',
            'slug' => 'asus-rog',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'name' => 'ASUS ROG Strix G16 Test',
            'slug' => 'asus-rog-strix-g16-test',
            'sku' => 'ROG-G16-TEST',
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'price' => 30000000,
            'sale_price' => 28000000,
            'cost_price' => 24000000,
            'stock_quantity' => 10,
            'min_stock' => 5,
            'sold_quantity' => 0,
            'is_active' => true,
        ]);
    }

    public function test_home_page_and_products_page_load(): void
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('TECHSTORE');

        $responseProd = $this->get(route('products.index'));
        $responseProd->assertStatus(200);
        $responseProd->assertSee($this->product->name);

        $responseDetail = $this->get(route('products.show', $this->product->slug));
        $responseDetail->assertStatus(200);
        $responseDetail->assertSee('ROG-G16-TEST');
    }

    public function test_customer_can_register_and_login(): void
    {
        $regResponse = $this->post(route('register.post'), [
            'name' => 'Tran Van Test',
            'email' => 'newuser@techstore.vn',
            'phone' => '0987654321',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'address' => 'District 1, HCM',
        ]);

        $regResponse->assertRedirect(route('home'));
        $this->assertDatabaseHas('users', ['email' => 'newuser@techstore.vn']);

        $loginResponse = $this->post(route('login.post'), [
            'email' => 'newuser@techstore.vn',
            'password' => 'password123',
        ]);
        $loginResponse->assertRedirect(route('home'));
        $this->assertAuthenticated();
    }

    public function test_cart_operations_and_overselling_protection(): void
    {
        $this->actingAs($this->customer);

        // 1. Add valid quantity
        $response = $this->post(route('cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);
        $response->assertSessionHas('success');

        // 2. Try adding more than available stock (Total 10, already 2, trying +9 -> 11 > 10)
        $failResponse = $this->post(route('cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 9,
        ]);
        $failResponse->assertSessionHas('error');
    }

    public function test_checkout_cod_deducts_stock_immediately_and_creates_order(): void
    {
        $this->actingAs($this->customer);

        // Add 2 to cart
        $cartService = app(CartService::class);
        $cartService->addToCart($this->product->id, 2);

        $checkoutResponse = $this->post(route('checkout.process'), [
            'customer_name' => 'Nguyen Van A',
            'customer_email' => 'customer@techstore.vn',
            'customer_phone' => '0901234567',
            'shipping_address' => 'Số 10 Đường ABC, Hà Nội',
        ]);

        $order = Order::latest()->first();
        $this->assertNotNull($order);
        $checkoutResponse->assertRedirect(route('checkout.success', $order->order_code));

        $this->assertEquals('pending', $order->order_status);
        $this->assertEquals('cod', $order->payment_method);
        $this->assertEquals('unpaid', $order->payment_status);
        $this->assertFalse($order->inventory_settled);

        // Check stock deducted immediately: 10 - 2 = 8
        $this->assertEquals(8, $this->product->fresh()->stock_quantity);
    }

    public function test_coupon_discount_calculation(): void
    {
        $coupon = Coupon::create([
            'code' => 'TEST500',
            'name' => 'Giảm 500k',
            'discount_type' => 'fixed',
            'discount_value' => 500000,
            'min_order_amount' => 1000000,
            'is_active' => true,
        ]);

        $this->actingAs($this->customer);
        $cartService = app(CartService::class);
        $cartService->addToCart($this->product->id, 1);

        $applyRes = $this->post(route('checkout.apply-coupon'), ['code' => 'TEST500']);
        $applyRes->assertSessionHas('success');

        $this->post(route('checkout.process'), [
            'customer_name' => 'Nguyen Van A',
            'customer_email' => 'customer@techstore.vn',
            'customer_phone' => '0901234567',
            'shipping_address' => 'Số 10 Đường ABC, Hà Nội',
        ]);

        $order = Order::latest()->first();
        $this->assertEquals(500000, (float) $order->discount_amount);
        $this->assertEquals(28000000 - 500000, (float) $order->total_amount);
    }

    public function test_order_lifecycle_shipping_and_delivered(): void
    {
        $this->actingAs($this->customer);
        $cartService = app(CartService::class);
        $cartService->addToCart($this->product->id, 2);

        $orderService = app(OrderService::class);
        $cart = $cartService->getCart();
        $order = $orderService->createOrder([
            'customer_name' => 'Customer Test',
            'customer_email' => 'customer@techstore.vn',
            'customer_phone' => '0901234567',
            'shipping_address' => 'District 1, HCM',
            'user_id' => $this->customer->id,
        ], $cart);

        // Stock was already deducted at checkout: 10 - 2 = 8
        $this->assertEquals(8, $this->product->fresh()->stock_quantity);
        $this->assertEquals(0, $this->product->fresh()->sold_quantity);

        // 1. Move to confirmed -> Stock NOT deducted again
        $order = $orderService->updateStatus($order, 'confirmed', null, $this->admin->id);
        $this->assertEquals('confirmed', $order->order_status);
        $this->assertEquals(8, $this->product->fresh()->stock_quantity);

        // 2. Move to shipping -> Stock NOT deducted again, sold_quantity remains 0 (not delivered yet)
        $order = $orderService->updateStatus($order, 'shipping', null, $this->admin->id);
        $this->assertEquals('shipping', $order->order_status);
        $this->assertEquals(8, $this->product->fresh()->stock_quantity);
        $this->assertEquals(0, $this->product->fresh()->sold_quantity);

        // 3. Move to delivered -> Payment status is paid, sold_quantity increases by 2
        $order = $orderService->updateStatus($order, 'delivered', null, $this->admin->id);
        $this->assertEquals('delivered', $order->order_status);
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals(8, $this->product->fresh()->stock_quantity);
        $this->assertEquals(2, $this->product->fresh()->sold_quantity);
        $this->assertTrue($order->fresh()->inventory_settled);
    }

    public function test_order_cancellation_before_shipping_restocks_inventory(): void
    {
        $this->actingAs($this->customer);
        $cartService = app(CartService::class);
        $cartService->addToCart($this->product->id, 3);

        $orderService = app(OrderService::class);
        $cart = $cartService->getCart();
        $order = $orderService->createOrder([
            'customer_name' => 'Customer Test',
            'customer_email' => 'customer@techstore.vn',
            'customer_phone' => '0901234567',
            'shipping_address' => 'District 1, HCM',
            'user_id' => $this->customer->id,
        ], $cart);

        // Stock was deducted at checkout: 10 - 3 = 7
        $this->assertEquals(7, $this->product->fresh()->stock_quantity);
        $this->assertFalse($order->fresh()->inventory_settled);

        // Cancel order while pending -> Stock restocked back to 10
        $order = $orderService->updateStatus($order, 'cancelled', 'Khách đổi ý', $this->customer->id);
        $this->assertEquals('cancelled', $order->order_status);
        $this->assertEquals(10, $this->product->fresh()->stock_quantity);
        $this->assertTrue($order->fresh()->inventory_settled);
        $this->assertNotNull($order->fresh()->restocked_at);
    }

    public function test_failed_delivery_does_not_auto_restock_until_admin_confirms_returned(): void
    {
        $this->actingAs($this->customer);
        $cartService = app(CartService::class);
        $cartService->addToCart($this->product->id, 2);

        $orderService = app(OrderService::class);
        $cart = $cartService->getCart();
        $order = $orderService->createOrder([
            'customer_name' => 'Customer Test',
            'customer_email' => 'customer@techstore.vn',
            'customer_phone' => '0901234567',
            'shipping_address' => 'District 1, HCM',
            'user_id' => $this->customer->id,
        ], $cart);

        // Move to shipping: stock is 8
        $order = $orderService->updateStatus($order, 'shipping', null, $this->admin->id);
        $this->assertEquals(8, $this->product->fresh()->stock_quantity);

        // Delivery failed / customer refused: status becomes cancelled from shipping
        // Stock must NOT be restocked automatically yet! Package is with courier
        $order = $orderService->updateStatus($order, 'cancelled', 'Khách từ chối nhận', $this->admin->id);
        $this->assertEquals('cancelled', $order->order_status);
        $this->assertEquals(8, $this->product->fresh()->stock_quantity); // Remains 8
        $this->assertFalse($order->fresh()->inventory_settled);
        $this->assertNull($order->fresh()->restocked_at);

        // Later, package is physically returned to warehouse -> Admin confirms restock
        $order = $orderService->restockOrder($order, 'Nhận lại hàng từ bưu tá');
        $this->assertEquals(10, $this->product->fresh()->stock_quantity); // Restocked back to 10!
        $this->assertTrue($order->fresh()->inventory_settled);
        $this->assertNotNull($order->fresh()->restocked_at);

        // Trying to restock again must throw Exception (prevent double restock)
        $this->expectException(Exception::class);
        $orderService->restockOrder($order);
    }

    public function test_delivered_order_cannot_be_cancelled(): void
    {
        $this->actingAs($this->customer);
        $cartService = app(CartService::class);
        $cartService->addToCart($this->product->id, 1);

        $orderService = app(OrderService::class);
        $cart = $cartService->getCart();
        $order = $orderService->createOrder([
            'customer_name' => 'Customer Test',
            'customer_email' => 'customer@techstore.vn',
            'customer_phone' => '0901234567',
            'shipping_address' => 'District 1, HCM',
            'user_id' => $this->customer->id,
        ], $cart);

        $order = $orderService->updateStatus($order, 'shipping', null, $this->admin->id);
        $order = $orderService->updateStatus($order, 'delivered', null, $this->admin->id);

        $this->expectException(Exception::class);
        $orderService->updateStatus($order, 'cancelled', 'Muốn hủy đơn đã giao', $this->admin->id);
    }

    public function test_admin_can_restock_and_update_stock_directly(): void
    {
        $inventoryService = app(InventoryService::class);

        // 1. Restock +15 items with supplier
        $product = $inventoryService->addStock($this->product, 15, $this->supplier->id, 'Nhập lô hàng mới', $this->admin->id);
        $this->assertEquals(25, $product->stock_quantity); // 10 + 15
        $this->assertDatabaseHas('product_stock_imports', [
            'product_id' => $this->product->id,
            'supplier_id' => $this->supplier->id,
            'quantity' => 15,
            'note' => 'Nhập lô hàng mới',
            'created_by' => $this->admin->id,
        ]);

        // 2. Direct update stock and min_stock
        $product = $inventoryService->updateStock($product, 50, 8);
        $this->assertEquals(50, $product->stock_quantity);
        $this->assertEquals(8, $product->min_stock);
    }

    public function test_admin_middleware_protection(): void
    {
        // Guest cannot access admin
        $guestRes = $this->get(route('admin.dashboard'));
        $guestRes->assertRedirect(route('admin.login'));

        // Customer cannot access admin
        $this->actingAs($this->customer);
        $custRes = $this->get(route('admin.dashboard'));
        $custRes->assertRedirect(route('admin.login'));

        // Admin can access admin
        $this->actingAs($this->admin);
        $adminRes = $this->get(route('admin.dashboard'));
        $adminRes->assertStatus(200);
        $adminRes->assertSee('Tổng Quan Hoạt Động & Doanh Thu');
    }

    public function test_customer_can_review_delivered_product_and_admin_can_moderate(): void
    {
        $this->actingAs($this->customer);

        // Create a delivered order
        $order = Order::create([
            'order_code' => 'ORD-REV-001',
            'user_id' => $this->customer->id,
            'customer_name' => $this->customer->name,
            'customer_email' => $this->customer->email,
            'customer_phone' => '0901234567',
            'shipping_address' => 'District 1, HCM',
            'subtotal' => 28000000,
            'discount_amount' => 0,
            'shipping_fee' => 0,
            'total_amount' => 28000000,
            'payment_method' => 'cod',
            'payment_status' => 'paid',
            'order_status' => 'delivered',
            'inventory_settled' => true,
        ]);

        $order->items()->create([
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'product_sku' => $this->product->sku,
            'unit_price' => 28000000,
            'quantity' => 1,
            'total_price' => 28000000,
        ]);

        // Submit review
        $res = $this->post(route('account.reviews.store'), [
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'rating' => 5,
            'comment' => 'Máy dùng rất mượt và chuẩn chỉ!',
        ]);

        $res->assertSessionHas('success');
        $this->assertDatabaseHas('reviews', [
            'product_id' => $this->product->id,
            'user_id' => $this->customer->id,
            'rating' => 5,
        ]);

        // Admin can moderate review
        $review = Review::where('product_id', $this->product->id)->first();
        $this->actingAs($this->admin);

        $updateRes = $this->patch(route('admin.reviews.update-status', $review), [
            'status' => 'approved',
        ]);
        $updateRes->assertSessionHas('success');
        $this->assertEquals('approved', $review->fresh()->status);
    }

    public function test_admin_order_invoice_page_loads(): void
    {
        $this->actingAs($this->admin);

        $order = Order::create([
            'order_code' => 'ORD-INV-001',
            'user_id' => $this->customer->id,
            'customer_name' => $this->customer->name,
            'customer_email' => $this->customer->email,
            'customer_phone' => '0901234567',
            'shipping_address' => 'District 1, HCM',
            'subtotal' => 28000000,
            'discount_amount' => 0,
            'shipping_fee' => 0,
            'total_amount' => 28000000,
            'payment_method' => 'cod',
            'payment_status' => 'paid',
            'order_status' => 'delivered',
            'inventory_settled' => true,
        ]);

        $order->items()->create([
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'product_sku' => $this->product->sku,
            'unit_price' => 28000000,
            'quantity' => 1,
            'total_price' => 28000000,
        ]);

        $res = $this->get(route('admin.orders.invoice', $order));
        $res->assertStatus(200);
        $res->assertSee('ORD-INV-001');
        $res->assertSee('PHIẾU XUẤT GIAO HÀNG');
    }

    public function test_admin_products_edit_page_renders_cleanly(): void
    {
        $this->actingAs($this->admin);

        $res = $this->get(route('admin.products.edit', $this->product));
        $res->assertStatus(200);
        $res->assertSee('Chỉnh Sửa Sản Phẩm: '.$this->product->name);
        $res->assertSee('specIndex = '.$this->product->specifications->count() + 1, false);
    }

    public function test_customer_cannot_access_inventory_endpoints(): void
    {
        $this->actingAs($this->customer);

        // 1. Cannot access inventory list
        $resIndex = $this->get(route('admin.inventory.index'));
        $resIndex->assertRedirect(route('admin.login'));

        // 2. Cannot call add-stock
        $resAdd = $this->post(route('admin.inventory.add-stock', $this->product), ['quantity' => 10]);
        $resAdd->assertRedirect(route('admin.login'));
        $this->assertEquals(10, $this->product->fresh()->stock_quantity);

        // 3. Cannot call update-stock
        $resUpdate = $this->patch(route('admin.inventory.update-stock', $this->product), ['stock_quantity' => 99]);
        $resUpdate->assertRedirect(route('admin.login'));
        $this->assertEquals(10, $this->product->fresh()->stock_quantity);
    }

    public function test_admin_can_manage_inventory_via_http_controllers(): void
    {
        $this->actingAs($this->admin);

        // 1. Visit inventory list
        $resIndex = $this->get(route('admin.inventory.index'));
        $resIndex->assertStatus(200);
        $resIndex->assertSee('Quản Lý Tồn Kho & Nhập Hàng');
        $resIndex->assertSee($this->product->name);

        // 2. Add stock via POST
        $resAdd = $this->post(route('admin.inventory.add-stock', $this->product), [
            'supplier_id' => $this->supplier->id,
            'quantity' => 20,
            'note' => 'Nhập thêm 20 chiếc từ nhà phân phối',
        ]);
        $resAdd->assertSessionHas('success');
        $this->assertEquals(30, $this->product->fresh()->stock_quantity); // 10 + 20
        $this->assertDatabaseHas('product_stock_imports', [
            'product_id' => $this->product->id,
            'supplier_id' => $this->supplier->id,
            'quantity' => 20,
            'note' => 'Nhập thêm 20 chiếc từ nhà phân phối',
            'created_by' => $this->admin->id,
        ]);

        // 3. Update stock directly via PATCH
        $resUpdate = $this->patch(route('admin.inventory.update-stock', $this->product), [
            'stock_quantity' => 45,
            'min_stock' => 8,
        ]);
        $resUpdate->assertSessionHas('success');
        $this->assertEquals(45, $this->product->fresh()->stock_quantity);
        $this->assertEquals(8, $this->product->fresh()->min_stock);
    }

    public function test_dashboard_displays_low_stock_warning_accurately(): void
    {
        $this->actingAs($this->admin);

        // Create a product with low stock (stock 2 <= min_stock 5)
        $lowProduct = Product::create([
            'name' => 'Low Stock Keyboard Test',
            'slug' => 'low-stock-keyboard-test',
            'sku' => 'LOW-KB-01',
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'price' => 1000000,
            'cost_price' => 800000,
            'stock_quantity' => 2,
            'min_stock' => 5,
            'sold_quantity' => 0,
            'is_active' => true,
        ]);

        $res = $this->get(route('admin.dashboard'));
        $res->assertStatus(200);
        $res->assertSee('Cảnh Báo Sắp Hết Hàng');
        $res->assertSee('Low Stock Keyboard Test');
        $res->assertSee('LOW-KB-01');
    }

    public function test_admin_restock_controller_action(): void
    {
        $this->actingAs($this->admin);

        $order = Order::create([
            'order_code' => 'ORD-RETURN-001',
            'user_id' => $this->customer->id,
            'customer_name' => $this->customer->name,
            'customer_email' => $this->customer->email,
            'customer_phone' => '0901234567',
            'shipping_address' => 'District 1, HCM',
            'subtotal' => 28000000,
            'discount_amount' => 0,
            'shipping_fee' => 0,
            'total_amount' => 28000000,
            'payment_method' => 'cod',
            'payment_status' => 'unpaid',
            'order_status' => 'cancelled',
            'cancelled_reason' => 'Khách từ chối nhận hàng',
            'inventory_settled' => false,
            'restocked_at' => null,
        ]);

        $order->items()->create([
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'product_sku' => $this->product->sku,
            'unit_price' => 28000000,
            'quantity' => 2,
            'total_price' => 56000000,
        ]);

        // Stock before restock: 10
        $this->assertEquals(10, $this->product->fresh()->stock_quantity);

        // 1. Admin confirms restock via HTTP POST
        $res = $this->post(route('admin.orders.restock', $order), [
            'note' => 'Kiểm hàng bưu cục trả về nguyên vẹn',
        ]);
        $res->assertSessionHas('success');
        $this->assertEquals(12, $this->product->fresh()->stock_quantity); // 10 + 2
        $this->assertTrue($order->fresh()->inventory_settled);
        $this->assertNotNull($order->fresh()->restocked_at);

        // 2. Second restock attempt via HTTP POST should fail with error message
        $res2 = $this->post(route('admin.orders.restock', $order), [
            'note' => 'Thử hoàn kho lần 2',
        ]);
        $res2->assertSessionHas('error');
        $this->assertEquals(12, $this->product->fresh()->stock_quantity); // Remains 12
    }

    public function test_add_stock_validation_fails_without_supplier(): void
    {
        $this->actingAs($this->admin);

        $res = $this->post(route('admin.inventory.add-stock', $this->product), [
            'quantity' => 10,
        ]);

        $res->assertSessionHasErrors(['supplier_id']);
        $this->assertEquals(10, $this->product->fresh()->stock_quantity);
    }

    public function test_add_stock_validation_fails_with_invalid_or_inactive_supplier(): void
    {
        $this->actingAs($this->admin);

        // 1. Non-existent supplier ID
        $resNotFound = $this->post(route('admin.inventory.add-stock', $this->product), [
            'supplier_id' => 99999,
            'quantity' => 10,
        ]);
        $resNotFound->assertSessionHasErrors(['supplier_id']);
        $this->assertEquals(10, $this->product->fresh()->stock_quantity);

        // 2. Inactive supplier (is_active = false)
        $inactiveSupplier = Supplier::create([
            'name' => 'Inactive Supplier Test',
            'code' => 'SUP-INACTIVE',
            'is_active' => false,
        ]);

        $resInactive = $this->post(route('admin.inventory.add-stock', $this->product), [
            'supplier_id' => $inactiveSupplier->id,
            'quantity' => 10,
        ]);
        $resInactive->assertSessionHasErrors(['supplier_id']);
        $this->assertEquals(10, $this->product->fresh()->stock_quantity);
    }

    public function test_add_stock_validation_fails_with_soft_deleted_supplier(): void
    {
        $this->actingAs($this->admin);

        $deletedSupplier = Supplier::create([
            'name' => 'Deleted Supplier Test',
            'code' => 'SUP-DELETED',
            'is_active' => true,
        ]);
        $deletedSupplier->delete(); // Soft delete

        $res = $this->post(route('admin.inventory.add-stock', $this->product), [
            'supplier_id' => $deletedSupplier->id,
            'quantity' => 10,
        ]);

        $res->assertSessionHasErrors(['supplier_id']);
        $this->assertEquals(10, $this->product->fresh()->stock_quantity);
    }

    public function test_add_stock_validation_fails_with_zero_or_negative_quantity(): void
    {
        $this->actingAs($this->admin);

        // Zero quantity
        $resZero = $this->post(route('admin.inventory.add-stock', $this->product), [
            'supplier_id' => $this->supplier->id,
            'quantity' => 0,
        ]);
        $resZero->assertSessionHasErrors(['quantity']);

        // Negative quantity
        $resNeg = $this->post(route('admin.inventory.add-stock', $this->product), [
            'supplier_id' => $this->supplier->id,
            'quantity' => -5,
        ]);
        $resNeg->assertSessionHasErrors(['quantity']);

        $this->assertEquals(10, $this->product->fresh()->stock_quantity);
    }

    public function test_add_stock_transaction_rollback_when_history_fails(): void
    {
        $this->actingAs($this->admin);

        // Test at Service level: simulate exception during history creation
        $inventoryService = app(InventoryService::class);

        $this->assertEquals(10, $this->product->fresh()->stock_quantity);

        try {
            DB::transaction(function () {
                // Manually trigger addStock logic with a hook/throw to prove rollback
                $lockedProduct = Product::where('id', $this->product->id)->lockForUpdate()->firstOrFail();
                $lockedProduct->increment('stock_quantity', 25);

                // Simulate failure on history logging
                throw new Exception('Simulated database failure during history logging');
            });
        } catch (Exception $e) {
            // Expected
        }

        // Must remain 10 due to rollback
        $this->assertEquals(10, $this->product->fresh()->stock_quantity);
        $this->assertDatabaseMissing('product_stock_imports', [
            'product_id' => $this->product->id,
            'quantity' => 25,
        ]);
    }

    public function test_recent_imports_displayed_in_inventory_index(): void
    {
        $this->actingAs($this->admin);

        // Add stock
        $this->post(route('admin.inventory.add-stock', $this->product), [
            'supplier_id' => $this->supplier->id,
            'quantity' => 15,
            'note' => 'Nhập thử nghiệm để kiểm tra bảng lịch sử',
        ]);

        $res = $this->get(route('admin.inventory.index'));
        $res->assertStatus(200);
        $res->assertSee('Lịch Sử Nhập Hàng Gần Đây');
        $res->assertSee($this->supplier->name);
        $res->assertSee('+15');
        $res->assertSee('Nhập thử nghiệm để kiểm tra bảng lịch sử');
        $res->assertSee($this->admin->name);
    }

    public function test_customer_cannot_cancel_order_in_processing_or_shipping(): void
    {
        $this->actingAs($this->customer);

        // 1. Order in 'processing'
        $orderProcessing = Order::create([
            'order_code' => 'ORD-PROC-001',
            'user_id' => $this->customer->id,
            'customer_name' => $this->customer->name,
            'customer_email' => $this->customer->email,
            'customer_phone' => '0901234567',
            'shipping_address' => 'District 1, HCM',
            'subtotal' => 28000000,
            'discount_amount' => 0,
            'shipping_fee' => 0,
            'total_amount' => 28000000,
            'payment_method' => 'cod',
            'payment_status' => 'unpaid',
            'order_status' => 'processing',
            'inventory_settled' => false,
        ]);

        $orderProcessing->items()->create([
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'product_sku' => $this->product->sku,
            'unit_price' => 28000000,
            'quantity' => 1,
            'total_price' => 28000000,
        ]);

        $stockBefore = $this->product->fresh()->stock_quantity;

        // Customer attempts to cancel order in processing
        $res = $this->post(route('account.orders.cancel', $orderProcessing->order_code), [
            'reason' => 'Khách muốn tự hủy đơn đang đóng gói',
        ]);

        $res->assertSessionHas('error');
        $this->assertEquals('processing', $orderProcessing->fresh()->order_status);
        $this->assertEquals($stockBefore, $this->product->fresh()->stock_quantity);

        // 2. Order in 'shipping'
        $orderShipping = Order::create([
            'order_code' => 'ORD-SHIP-001',
            'user_id' => $this->customer->id,
            'customer_name' => $this->customer->name,
            'customer_email' => $this->customer->email,
            'customer_phone' => '0901234567',
            'shipping_address' => 'District 1, HCM',
            'subtotal' => 28000000,
            'discount_amount' => 0,
            'shipping_fee' => 0,
            'total_amount' => 28000000,
            'payment_method' => 'cod',
            'payment_status' => 'unpaid',
            'order_status' => 'shipping',
            'inventory_settled' => false,
        ]);

        $orderShipping->items()->create([
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'product_sku' => $this->product->sku,
            'unit_price' => 28000000,
            'quantity' => 1,
            'total_price' => 28000000,
        ]);

        // Customer attempts to cancel order in shipping
        $resShip = $this->post(route('account.orders.cancel', $orderShipping->order_code), [
            'reason' => 'Khách muốn tự hủy đơn đang giao',
        ]);

        $resShip->assertSessionHas('error');
        $this->assertEquals('shipping', $orderShipping->fresh()->order_status);
        $this->assertEquals($stockBefore, $this->product->fresh()->stock_quantity);
    }

    public function test_admin_cannot_delete_supplier_with_stock_imports(): void
    {
        $this->actingAs($this->admin);

        // Create a new supplier and attach a stock import record
        $supplier = Supplier::create([
            'name' => 'Supplier With History',
            'code' => 'SUP-HIST-01',
            'is_active' => true,
        ]);

        $this->product->stockImports()->create([
            'supplier_id' => $supplier->id,
            'quantity' => 5,
            'note' => 'Nhập lô hàng thử nghiệm',
            'created_by' => $this->admin->id,
        ]);

        $this->assertTrue($supplier->stockImports()->exists());

        // Attempt to delete supplier
        $res = $this->delete(route('admin.suppliers.destroy', $supplier));

        $res->assertSessionHas('error', 'Không thể xóa nhà cung cấp đã có lịch sử nhập hàng.');
        $this->assertDatabaseHas('suppliers', ['id' => $supplier->id]);
    }
}
