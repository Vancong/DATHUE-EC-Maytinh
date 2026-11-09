<?php

namespace Tests\Feature\Storefront;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogFilterTest extends TestCase
{
    use RefreshDatabase;

    protected Category $laptopCategory;

    protected Category $gamingCategory;

    protected Brand $asusBrand;

    protected Brand $dellBrand;

    protected function setUp(): void
    {
        parent::setUp();

        $this->laptopCategory = Category::create([
            'name' => 'Laptop Văn Phòng',
            'slug' => 'laptop-van-phong',
            'is_active' => true,
        ]);

        $this->gamingCategory = Category::create([
            'name' => 'Laptop Gaming',
            'slug' => 'laptop-gaming',
            'is_active' => true,
        ]);

        $this->asusBrand = Brand::create([
            'name' => 'ASUS',
            'slug' => 'asus',
            'is_active' => true,
        ]);

        $this->dellBrand = Brand::create([
            'name' => 'Dell',
            'slug' => 'dell',
            'is_active' => true,
        ]);
    }

    public function test_guest_can_search_products_by_keyword(): void
    {
        Product::create([
            'name' => 'ASUS Zenbook 14 OLED UX3405',
            'slug' => 'asus-zenbook-14-oled',
            'sku' => 'ZENBOOK-14',
            'category_id' => $this->laptopCategory->id,
            'brand_id' => $this->asusBrand->id,
            'price' => 28000000,
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Dell XPS 13 Plus 9320',
            'slug' => 'dell-xps-13-plus',
            'sku' => 'DELL-XPS-13',
            'category_id' => $this->laptopCategory->id,
            'brand_id' => $this->dellBrand->id,
            'price' => 35000000,
            'stock_quantity' => 5,
            'is_active' => true,
        ]);

        // Search by keyword "Zenbook"
        $response = $this->get(route('products.index', ['keyword' => 'Zenbook']));
        $response->assertStatus(200);
        $response->assertSee('ASUS Zenbook 14 OLED UX3405');
        $response->assertDontSee('Dell XPS 13 Plus 9320');

        // Search by SKU "XPS-13"
        $responseSku = $this->get(route('products.index', ['keyword' => 'XPS-13']));
        $responseSku->assertStatus(200);
        $responseSku->assertSee('Dell XPS 13 Plus 9320');
        $responseSku->assertDontSee('ASUS Zenbook 14 OLED UX3405');
    }

    public function test_guest_can_filter_products_by_category(): void
    {
        Product::create([
            'name' => 'ASUS ROG Strix Scar 16',
            'slug' => 'asus-rog-strix-scar-16',
            'sku' => 'ROG-SCAR-16',
            'category_id' => $this->gamingCategory->id,
            'brand_id' => $this->asusBrand->id,
            'price' => 60000000,
            'stock_quantity' => 4,
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Dell Inspiron 15 3520',
            'slug' => 'dell-inspiron-15-3520',
            'sku' => 'DELL-INSP-15',
            'category_id' => $this->laptopCategory->id,
            'brand_id' => $this->dellBrand->id,
            'price' => 14000000,
            'stock_quantity' => 15,
            'is_active' => true,
        ]);

        // Filter by laptop-gaming
        $response = $this->get(route('products.index', ['category' => 'laptop-gaming']));
        $response->assertStatus(200);
        $response->assertSee('ASUS ROG Strix Scar 16');
        $response->assertDontSee('Dell Inspiron 15 3520');
    }

    public function test_guest_can_filter_products_by_brand(): void
    {
        Product::create([
            'name' => 'ASUS Vivobook 15X',
            'slug' => 'asus-vivobook-15x',
            'sku' => 'VIVO-15X',
            'category_id' => $this->laptopCategory->id,
            'brand_id' => $this->asusBrand->id,
            'price' => 16000000,
            'stock_quantity' => 8,
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Dell Latitude 7420',
            'slug' => 'dell-latitude-7420',
            'sku' => 'DELL-LAT-7420',
            'category_id' => $this->laptopCategory->id,
            'brand_id' => $this->dellBrand->id,
            'price' => 22000000,
            'stock_quantity' => 3,
            'is_active' => true,
        ]);

        $response = $this->get(route('products.index', ['brand' => 'asus']));
        $response->assertStatus(200);
        $response->assertSee('ASUS Vivobook 15X');
        $response->assertDontSee('Dell Latitude 7420');
    }

    public function test_guest_can_filter_products_by_price_range(): void
    {
        Product::create([
            'name' => 'Cheap Laptop',
            'slug' => 'cheap-laptop',
            'sku' => 'CHEAP-01',
            'category_id' => $this->laptopCategory->id,
            'brand_id' => $this->asusBrand->id,
            'price' => 10000000,
            'stock_quantity' => 5,
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Midrange Laptop',
            'slug' => 'midrange-laptop',
            'sku' => 'MID-01',
            'category_id' => $this->laptopCategory->id,
            'brand_id' => $this->dellBrand->id,
            'price' => 25000000,
            'stock_quantity' => 5,
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Premium Laptop',
            'slug' => 'premium-laptop',
            'sku' => 'PREM-01',
            'category_id' => $this->gamingCategory->id,
            'brand_id' => $this->asusBrand->id,
            'price' => 55000000,
            'stock_quantity' => 5,
            'is_active' => true,
        ]);

        // Range 15,000,000 -> 30,000,000
        $response = $this->get(route('products.index', [
            'min_price' => 15000000,
            'max_price' => 30000000,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Midrange Laptop');
        $response->assertDontSee('Cheap Laptop');
        $response->assertDontSee('Premium Laptop');
    }

    public function test_guest_can_view_active_product_detail(): void
    {
        $product = Product::create([
            'name' => 'ASUS ROG Zephyrus G14',
            'slug' => 'asus-rog-zephyrus-g14',
            'sku' => 'ROG-G14-2024',
            'category_id' => $this->gamingCategory->id,
            'brand_id' => $this->asusBrand->id,
            'price' => 45000000,
            'stock_quantity' => 7,
            'is_active' => true,
        ]);

        $response = $this->get(route('products.show', $product->slug));
        $response->assertStatus(200);
        $response->assertSee('ASUS ROG Zephyrus G14');
        $response->assertSee('ROG-G14-2024');
    }

    public function test_inactive_or_deleted_product_returns_404(): void
    {
        $inactiveProduct = Product::create([
            'name' => 'Hidden Laptop',
            'slug' => 'hidden-laptop',
            'sku' => 'HIDDEN-01',
            'category_id' => $this->laptopCategory->id,
            'brand_id' => $this->asusBrand->id,
            'price' => 20000000,
            'stock_quantity' => 5,
            'is_active' => false, // Inactive
        ]);

        $response = $this->get(route('products.show', $inactiveProduct->slug));
        $response->assertStatus(404);

        $deletedProduct = Product::create([
            'name' => 'Deleted Laptop',
            'slug' => 'deleted-laptop',
            'sku' => 'DEL-01',
            'category_id' => $this->laptopCategory->id,
            'brand_id' => $this->dellBrand->id,
            'price' => 20000000,
            'stock_quantity' => 5,
            'is_active' => true,
        ]);
        $deletedProduct->delete(); // Soft delete

        $responseDeleted = $this->get(route('products.show', $deletedProduct->slug));
        $responseDeleted->assertStatus(404);
    }

    public function test_search_by_unaccented_keyword(): void
    {
        Product::create([
            'name' => 'Bàn phím cơ DareU EK87',
            'slug' => 'ban-phim-co-dareu-ek87',
            'sku' => 'DAREU-EK87',
            'category_id' => $this->laptopCategory->id,
            'brand_id' => $this->asusBrand->id,
            'price' => 650000,
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Chuột Gaming Logitech G102',
            'slug' => 'chuot-gaming-logitech-g102',
            'sku' => 'LOGI-G102',
            'category_id' => $this->laptopCategory->id,
            'brand_id' => $this->asusBrand->id,
            'price' => 450000,
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        // Search unaccented "ban phim"
        $response = $this->get(route('products.index', ['keyword' => 'ban phim']));
        $response->assertStatus(200);
        $response->assertSee('Bàn phím cơ DareU EK87');
        $response->assertDontSee('Chuột Gaming Logitech G102');
    }

    public function test_sort_products_by_price_asc_and_desc(): void
    {
        $pLow = Product::create([
            'name' => 'Low Price Item',
            'slug' => 'low-price-item',
            'sku' => 'LOW-01',
            'category_id' => $this->laptopCategory->id,
            'brand_id' => $this->asusBrand->id,
            'price' => 5000000,
            'stock_quantity' => 5,
            'is_active' => true,
        ]);

        $pMid = Product::create([
            'name' => 'Mid Price Item',
            'slug' => 'mid-price-item',
            'sku' => 'MID-01',
            'category_id' => $this->laptopCategory->id,
            'brand_id' => $this->asusBrand->id,
            'price' => 25000000,
            'stock_quantity' => 5,
            'is_active' => true,
        ]);

        $pHigh = Product::create([
            'name' => 'High Price Item',
            'slug' => 'high-price-item',
            'sku' => 'HIGH-01',
            'category_id' => $this->laptopCategory->id,
            'brand_id' => $this->asusBrand->id,
            'price' => 50000000,
            'sale_price' => 45000000,
            'stock_quantity' => 5,
            'is_active' => true,
        ]);

        // Sort price_asc: Low (5m) -> Mid (25m) -> High (45m sale)
        $responseAsc = $this->get(route('products.index', ['sort' => 'price_asc']));
        $responseAsc->assertStatus(200);
        $ascIds = $responseAsc->viewData('products')->pluck('id')->toArray();
        $this->assertEquals([$pLow->id, $pMid->id, $pHigh->id], $ascIds);

        // Sort price_desc: High (45m sale) -> Mid (25m) -> Low (5m)
        $responseDesc = $this->get(route('products.index', ['sort' => 'price_desc']));
        $responseDesc->assertStatus(200);
        $descIds = $responseDesc->viewData('products')->pluck('id')->toArray();
        $this->assertEquals([$pHigh->id, $pMid->id, $pLow->id], $descIds);
    }
}
