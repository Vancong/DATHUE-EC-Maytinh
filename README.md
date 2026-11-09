# TechStore – Hệ Thống Thương Mại Điện Tử Máy Tính & Gaming Gear

[![Tests](https://img.shields.io/badge/PHPUnit-26%20Passed-success?style=flat-square&logo=php)](docs/09_TESTING_AND_DEVELOPER_GUIDE.md)
[![Code Style](https://img.shields.io/badge/Code%20Style-PSR--12%20(Pint)-blue?style=flat-square)](docs/09_TESTING_AND_DEVELOPER_GUIDE.md)
[![Framework](https://img.shields.io/badge/Laravel-12.x-red?style=flat-square&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3%2B-indigo?style=flat-square&logo=php)](https://php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind-3.x-38bdf8?style=flat-square&logo=tailwindcss)](https://tailwindcss.com)

**TechStore** là nền tảng thương mại điện tử chuyên cung cấp thiết bị công nghệ: Laptop Gaming cao cấp, PC Master Builder, Linh kiện phần cứng và Phụ kiện Gaming Gear. Dự án được thiết kế theo kiến trúc chuẩn mực của Laravel 12, tối ưu hóa tối đa cho các đồ án tốt nghiệp chuyên ngành CNTT / Thương mại điện tử và dễ dàng tích hợp, phát triển cùng các trợ lý AI.

---

## 📑 HỆ THỐNG TÀI LIỆU NGHIỆP VỤ (BUSINESS DOCS)

Toàn bộ nghiệp vụ của hệ thống đã được phân tích và văn bản hóa chi tiết trong thư mục [`docs/`](docs/README.md) để lập trình viên và các trợ lý AI dễ dàng theo dõi:

| Tài Liệu Nghiệp Vụ | Nội Dung Trọng Tâm |
|:---|:---|
| 📖 [**Tổng Quan Kiến Trúc**](docs/README.md) | Triết lý thiết kế hệ thống, Tech Stack, quy ước lập trình chung. |
| 🔐 [**01. Xác Thực & Phân Quyền**](docs/01_AUTHENTICATION_AND_ACCOUNTS.md) | Phân quyền Admin (`AdminMiddleware`), Khách hàng, cơ chế khóa tài khoản `is_active`. |
| 💻 [**02. Danh Mục & Sản Phẩm**](docs/02_CATALOG_AND_PRODUCTS.md) | Quản lý Category, Brand, Sản phẩm, Thông số kỹ thuật (Specs) và Bộ sưu tập ảnh. |
| 📦 [**03. Quản Lý Tồn Kho**](docs/03_INVENTORY_MANAGEMENT.md) | Mô hình tồn kho trực tiếp `stock_quantity`, Nhập hàng từ Nhà cung cấp, Nhật ký `product_stock_imports`. |
| 🛒 [**04. Giỏ Hàng & Đặt Hàng**](docs/04_CART_AND_CHECKOUT.md) | Giỏ hàng, Mã giảm giá Coupon, Trừ tồn kho tức thời (Immediate Deduction) và chống bán vượt (Locking). |
| 🚚 [**05. Vòng Đời Đơn & Hoàn Kho**](docs/05_ORDER_LIFECYCLE_AND_RESTOCK.md) | Vòng đời đơn từ `pending` đến `delivered`, Cờ `inventory_settled`, Giao thất bại và Quy trình Admin hoàn kho an toàn. |
| ⭐ [**06. Đánh Giá & Kiểm Duyệt**](docs/06_REVIEWS_AND_MODERATION.md) | Đánh giá sản phẩm đã mua thành công (`delivered`), Admin duyệt / ẩn đánh giá. |
| 📊 [**07. Dashboard & Thống Kê**](docs/07_ADMIN_DASHBOARD_AND_ANALYTICS.md) | Biểu đồ doanh thu 12 tháng, Cơ cấu trạng thái đơn hàng (Chart.js), Cảnh báo kho sắp hết. |
| 🗄️ [**08. Cơ Sở Dữ Liệu & ERD**](docs/08_DATABASE_SCHEMA.md) | Sơ đồ thực thể quan hệ (Mermaid ERD), danh sách bảng và ràng buộc khóa ngoại an toàn. |
| 🛠️ [**09. Hướng Dẫn Phát Triển**](docs/09_TESTING_AND_DEVELOPER_GUIDE.md) | Chạy kiểm thử PHPUnit, chuẩn hóa Laravel Pint, các quy tắc an toàn bắt buộc khi AI làm việc. |

---

## ⚡ TÍNH NĂNG NỔI BẬT

### 1. Dành Cho Khách Hàng (Storefront)
- **Duyệt & Tìm kiếm:** Lọc sản phẩm theo danh mục, thương hiệu, khoảng giá, sắp xếp theo giá và độ phổ biến.
- **Chi tiết sản phẩm:** Trực quan hóa thông số kỹ thuật (Specs), thư viện ảnh Gallery, trạng thái tồn kho thực tế.
- **Giỏ hàng & Khuyến mãi:** Hỗ trợ áp dụng mã giảm giá (Coupon theo % hoặc số tiền cố định). Chặn tự động nếu đặt vượt số lượng tồn kho.
- **Đặt hàng:** Thanh toán COD hoặc Chuyển khoản, trừ tồn kho ngay lập tức để bảo vệ hàng hóa.
- **Trang cá nhân:** Quản lý thông tin, theo dõi lịch sử và tiến độ trạng thái từng đơn hàng, viết đánh giá cho sản phẩm đã nhận.

### 2. Dành Cho Quản Trị Viên (Admin Portal)
- **Bảng điều khiển (Dashboard):** Thống kê doanh thu thời gian thực, biểu đồ doanh thu theo tháng, biểu đồ tỷ lệ đơn hàng, danh sách cảnh báo hàng sắp hết.
- **Quản lý danh mục & thương hiệu:** Thêm, sửa, xóa, tải logo và quản lý trạng thái hiển thị.
- **Quản lý sản phẩm:** Tạo/chỉnh sửa sản phẩm linh hoạt với trình nhập thông số kỹ thuật động (Repeater) và thư viện ảnh.
- **Quản lý tồn kho tinh gọn:** 
  - Xem số lượng tồn và tình trạng (Đầy đủ / Sắp hết / Hết hàng).
  - Modal **Nhập Thêm Hàng Nhanh**: Chọn nhà cung cấp (`suppliers`), nhập số lượng, ghi chú. Tự động lưu nhật ký nhập `product_stock_imports`.
  - Modal **Điều Chỉnh Tồn Kho**: Sửa nhanh số lượng tồn và ngưỡng báo động `min_stock`.
- **Quản lý đơn hàng & Hoàn kho:**
  - Chuyển trạng thái: `pending` -> `confirmed` -> `processing` -> `shipping` -> `delivered` / `cancelled`.
  - **Nghiệp vụ hoàn kho chuẩn:** Đơn giao thất bại sau khi `shipping` không tự hoàn tồn; Admin kiểm tra hàng về tới kho và bấm nút **Xác nhận nhập hoàn kho** để cộng lại tồn kho đúng một lần duy nhất.
- **Kiểm duyệt đánh giá:** Duyệt hoặc từ chối các nhận xét từ khách hàng trước khi hiển thị công khai.
- **Tài khoản quản trị:** Phân quyền, bật/tắt quyền truy cập của các tài khoản Admin.

---

## 🚀 HƯỚNG DẪN CÀI ĐẶT & CHẠY LOCAL

### 1. Yêu Cầu Môi Trường
- PHP >= 8.3
- MySQL >= 8.0
- Composer >= 2.x
- Node.js >= 18.x & npm

### 2. Các Bước Khởi Chạy
```bash
# 1. Clone repository
git clone https://github.com/Vancong/DATHUE-EC-Maytinh.git
cd DATHUE-EC-Maytinh

# 2. Cài đặt các thư viện PHP
composer install

# 3. Cài đặt các thư viện Frontend & Build assets
npm install
npm run build

# 4. Cấu hình môi trường (.env)
cp .env.example .env
php artisan key:generate

# Cập nhật thông tin Database trong file .env:
# DB_DATABASE=techstore
# DB_USERNAME=root
# DB_PASSWORD=

# 5. Khởi tạo cơ sở dữ liệu & nạp dữ liệu mẫu
php artisan migrate
php artisan db:seed

# 6. Khởi động Web Server
php artisan serve
```

Truy cập website tại: `http://127.0.0.1:8000`  
Truy cập trang Quản trị tại: `http://127.0.0.1:8000/admin`  
- **Tài khoản Admin mặc định:** `admin@techstore.vn` / Mật khẩu: `password123`

---

## 🧪 CHẠY KIỂM THỬ TỰ ĐỘNG (AUTOMATED TESTING)

Dự án có sẵn bộ Feature Test bao phủ toàn diện mọi kịch bản vận hành:
```bash
# Chạy bộ test tính năng thương mại điện tử
php artisan test tests/Feature/EcommerceWorkflowTest.php --compact

# Chạy toàn bộ test suite (26/26 Tests Passed)
php artisan test --compact

# Kiểm tra và định dạng chuẩn mã nguồn PSR-12
vendor/bin/pint --format agent
```

---

## 🔒 AN TOÀN & BẢO VỆ DỮ LIỆU
- **Transaction & Concurrency:** Mọi thao tác biến động kho và đặt hàng sử dụng `DB::transaction()` kết hợp `Product::lockForUpdate()` triệt tiêu hoàn toàn Race Condition.
- **Idempotent Restocking:** Cờ `inventory_settled` ngăn chặn tuyệt đối lỗi hoàn kho lặp lại (double-restock).
- **Ràng buộc toàn vẹn:** Khóa ngoại lịch sử nhập hàng áp dụng `restrictOnDelete()` đảm bảo dữ liệu không bị xóa ngoài ý muốn.
- **Bảo mật Git:** File cấu hình nhạy cảm `.env` và các bản sao lưu database (`database/backups/`) đã được loại trừ khỏi kho mã nguồn.

---

## 📄 LICENSE
Dự án được phát triển phục vụ mục đích học tập, nghiên cứu và làm đồ án tốt nghiệp công nghệ phần mềm.
Phát hành theo giấy phép [MIT License](LICENSE).
