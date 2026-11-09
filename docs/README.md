# TECHSTORE – TÀI LIỆU KIẾN TRÚC & NGHIỆP VỤ HỆ THỐNG

Tài liệu này được biên soạn nhằm chuẩn hóa toàn bộ nghiệp vụ, kiến trúc dữ liệu và quy trình xử lý của dự án **TechStore**. Đây là kim chỉ nam giúp lập trình viên và các trợ lý AI dễ dàng tra cứu, mở rộng và bảo trì mã nguồn mà không làm sai lệch logic cốt lõi.

---

## 1. TỔNG QUAN HỆ THỐNG
- **Dự án:** TechStore (Website thương mại điện tử chuyên kinh doanh Laptop Gaming, PC Master, Linh kiện & Gear cao cấp).
- **Mục tiêu kiến trúc:** Hệ thống tinh gọn, hiệu năng cao, tối ưu cho đồ án tốt nghiệp sinh viên và triển khai thực tế.
- **Tech Stack:**
  - **Backend Framework:** Laravel 12 (PHP 8.3+)
  - **Database:** MySQL 8.0+
  - **Frontend:** Blade Templates, Tailwind CSS 3+, Vanilla JavaScript, Chart.js
  - **Asset Bundler:** Vite
  - **Testing:** PHPUnit (Feature Tests)
  - **Code Standard:** PSR-12, Laravel Pint

---

## 2. SITEMAP TÀI LIỆU THEO NGHIỆP VỤ

| STT | Tài liệu chuyên sâu | Mô tả nghiệp vụ chính |
|:---:|:---|:---|
| 01 | [Xác Thực & Phân Quyền](01_AUTHENTICATION_AND_ACCOUNTS.md) | Quản lý phiên, Admin Middleware, Customer Auth, bảo mật mật khẩu. |
| 02 | [Danh Mục & Sản Phẩm](02_CATALOG_AND_PRODUCTS.md) | Quản lý Danh mục, Thương hiệu, Thông số kỹ thuật (Specs), Gallery ảnh. |
| 03 | [Quản Lý Tồn Kho](03_INVENTORY_MANAGEMENT.md) | Tồn kho trực tiếp trên sản phẩm, Nhập hàng từ Nhà cung cấp, Nhật ký nhập hàng `product_stock_imports`, Cảnh báo tồn thấp. |
| 04 | [Giỏ Hàng & Đặt Hàng](04_CART_AND_CHECKOUT.md) | Xử lý Giỏ hàng, Mã giảm giá Coupon, Trừ tồn kho tức thời (Immediate Deduction) và chống bán vượt (Locking). |
| 05 | [Vòng Đời Đơn Hàng & Hoàn Kho](05_ORDER_LIFECYCLE_AND_RESTOCK.md) | Vòng đời đơn từ `pending` tới `delivered`, Cờ `inventory_settled`, Nghiệp vụ Hủy đơn, Giao thất bại và Hoàn kho an toàn. |
| 06 | [Đánh Giá & Kiểm Duyệt](06_REVIEWS_AND_MODERATION.md) | Khách hàng đánh giá sản phẩm sau khi nhận hàng (`delivered`), Admin duyệt/từ chối đánh giá. |
| 07 | [Dashboard & Thống Kê](07_ADMIN_DASHBOARD_AND_ANALYTICS.md) | Thống kê doanh thu theo tháng, Biểu đồ trạng thái đơn hàng, Top sản phẩm bán chạy, Cảnh báo kho. |
| 08 | [Cơ Sở Dữ Liệu & Thực Thể](08_DATABASE_SCHEMA.md) | Chi tiết bảng, quan hệ (Relations), ràng buộc khóa ngoại (Foreign Keys) và quy tắc bảo toàn dữ liệu. |
| 09 | [Hướng Dẫn Kiểm Thử & Lập Trình](09_TESTING_AND_DEVELOPER_GUIDE.md) | Chạy bộ test PHPUnit, Định dạng Pint, Seed dữ liệu mẫu, Hướng dẫn cho AI khi sửa đổi mã nguồn. |

---

## 3. NGUYÊN TẮC CỐT LÕI KHI LÀM VIỆC VỚI HỆ THỐNG
1. **Kiến trúc Tồn kho Tinh gọn:** Số lượng tồn kho được quản lý trực tiếp tại cột `stock_quantity` trên bảng `products`. Tuyệt đối không phục hồi mô hình kho đa tầng (`warehouses`, `inventory_stocks`, `reserved_quantity`) gây phức tạp hóa đồ án.
2. **Giao dịch Nguyên tử (Atomic Transactions):** Toàn bộ thao tác thay đổi số dư kho hoặc trạng thái đơn hàng đều phải được bọc trong `DB::transaction()` và sử dụng `lockForUpdate()` để triệt tiêu Race Condition.
3. **An toàn Dữ liệu:** Không bao giờ chạy `migrate:fresh` trên môi trường đang có dữ liệu thật. Mọi thay đổi cấu trúc bảng phải tạo migration mới với ràng buộc toàn vẹn dữ liệu (`restrictOnDelete()` cho các thực thể lịch sử).
4. **Kiểm thử Đồng hành:** Mỗi khi cập nhật nghiệp vụ, phải cập nhật và chạy bộ test tương ứng trong `tests/Feature/EcommerceWorkflowTest.php` để đảm bảo 100% test case luôn Passed.

