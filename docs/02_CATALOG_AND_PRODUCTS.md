# 02. QUẢN LÝ DANH MỤC, THƯƠNG HIỆU & SẢN PHẨM (CATALOG & PRODUCTS)

## 1. Cấu Trúc Thực Thể

### Danh Mục Sản Phẩm (Categories)
- **Model:** `App\Models\Category`
- **Controller:** `App\Http\Controllers\Admin\CategoryController`
- **Các trường chính:** `id`, `name`, `slug` (unique), `description`, `image`, `is_active`.
- **Quan hệ:** `hasMany(Product::class)`
- **Nghiệp vụ:** Hỗ trợ lọc sản phẩm theo danh mục ở cả giao diện Storefront và Admin. Khi xóa danh mục có sản phẩm, hệ thống áp dụng `restrictOnDelete()` để bảo vệ dữ liệu.

### Thương Hiệu (Brands)
- **Model:** `App\Models\Brand`
- **Controller:** `App\Http\Controllers\Admin\BrandController`
- **Các trường chính:** `id`, `name`, `slug` (unique), `logo`, `website`, `is_active`.
- **Quan hệ:** `hasMany(Product::class)`

---

## 2. Quản Lý Sản Phẩm (Products)

### Cấu Trúc Bảng `products`
| Tên Cột | Kiểu Dữ Liệu | Mục Đích |
|:---|:---|:---|
| `id` | bigint (PK) | Định danh sản phẩm |
| `name` | varchar(255) | Tên sản phẩm đầy đủ |
| `slug` | varchar(255) | Đường dẫn thân thiện SEO (unique) |
| `sku` | varchar(100) | Mã định danh duy nhất của sản phẩm (unique) |
| `category_id` | foreignId | Khóa ngoại danh mục (`categories`) |
| `brand_id` | foreignId (nullable) | Khóa ngoại thương hiệu (`brands`) |
| `price` | decimal(12,2) | Giá niêm yết chính thức |
| `sale_price` | decimal(12,2) (nullable) | Giá khuyến mãi (nếu có) |
| `cost_price` | decimal(12,2) (nullable) | Giá vốn nhập hàng |
| `stock_quantity` | int | **Số lượng tồn kho thực tế khả dụng** |
| `min_stock` | int | Ngưỡng báo động tồn kho thấp (mặc định: 5) |
| `sold_quantity` | int | Tổng số lượng sản phẩm đã bán thành công |
| `thumbnail` | varchar(500) (nullable) | Ảnh đại diện sản phẩm |
| `short_description`| text (nullable) | Mô tả ngắn / điểm nổi bật |
| `description` | longtext (nullable) | Mô tả chi tiết HTML |
| `is_featured` | boolean | Đánh dấu hiển thị tại trang chủ |
| `is_active` | boolean | Trạng thái hiển thị mở bán |

### Các Thuộc Tính Accessor Quan Trọng (`Product.php`)
- `current_price`: Lấy `sale_price` nếu có và nhỏ hơn `price`, ngược lại lấy `price`.
- `is_on_sale`: Kiểm tra sản phẩm có đang giảm giá hay không.
- `available_stock`: `max(0, stock_quantity)` – đảm bảo không bao giờ trả về số âm cho giao diện.
- `is_low_stock`: `stock_quantity <= min_stock`.
- `is_out_of_stock`: `stock_quantity <= 0`.

### Scopes Hỗ Trợ Truy Vấn
- `scopeActive($query)`: `is_active = true`
- `scopeFeatured($query)`: `is_featured = true`
- `scopeLowStock($query)`: `whereColumn('stock_quantity', '<=', 'min_stock')`
- `scopeOutOfStock($query)`: `stock_quantity <= 0`

---

## 3. Thành Phần Mở Rộng Của Sản Phẩm

### Thông Số Kỹ Thuật (Product Specifications)
- **Model:** `App\Models\ProductSpecification`
- **Bảng:** `product_specifications` (`product_id`, `spec_key`, `spec_value`)
- **Giao diện:** Dynamic Repeater cho phép thêm/xóa linh hoạt không giới hạn các thông số kỹ thuật (CPU, RAM, VGA, Màn hình, Pin,...).

### Bộ Sưu Tập Ảnh Phụ (Product Images)
- **Model:** `App\Models\ProductImage`
- **Bảng:** `product_images` (`product_id`, `image_path`, `sort_order`)
- **Giao diện:** Gallery Repeater cho phép nhập các URL ảnh chi tiết sản phẩm.

