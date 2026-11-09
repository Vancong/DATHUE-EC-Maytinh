# 09. HƯỚNG DẪN PHÁT TRIỂN & KIỂM THỬ (DEVELOPER & AI GUIDE)

Tài liệu này quy định các chuẩn mực bắt buộc cho lập trình viên và các mô hình AI khi làm việc trên codebase **TechStore**.

---

## 1. Môi Trường & Khởi Chạy Dự Án

### Yêu Cầu Cài Đặt
- PHP 8.3+ với các extension: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `curl`.
- Node.js 18+ & npm.
- MySQL 8.0+.
- Composer 2+.

### Lệnh Cài Đặt Ban Đầu
```bash
# Cài đặt PHP dependencies
composer install

# Cài đặt Node modules & build assets
npm install
npm run build

# Thiết lập file môi trường
cp .env.example .env
php artisan key:generate

# Chạy migration (KHÔNG dùng migrate:fresh nếu đang có DB thật)
php artisan migrate

# Nạp dữ liệu mẫu
php artisan db:seed
```

---

## 2. Quy Chuẩn Kiểm Thử (Testing Guidelines)

### Bộ Test Tự Động (PHPUnit)
File kiểm thử trọng tâm: [`tests/Feature/EcommerceWorkflowTest.php`](file:///e:/DoanThue/tests/Feature/EcommerceWorkflowTest.php)
Bao phủ 100% các nghiệp vụ chính:
- Đăng ký, đăng nhập tài khoản.
- Giỏ hàng & chống bán vượt tồn kho.
- Áp dụng mã giảm giá Coupon.
- Trừ tồn kho tức thời khi checkout COD.
- Chuyển trạng thái đơn không bị trừ trùng lặp.
- Tăng `sold_quantity` khi `delivered`.
- Hủy đơn trước khi giao hoàn kho 1 lần.
- Đơn giao thất bại sau khi `shipping` không tự hoàn tồn; chỉ hoàn khi Admin xác nhận.
- Chống hoàn kho 2 lần (`inventory_settled = true`).
- Xác thực nhà cung cấp hợp lệ khi nhập hàng (`product_stock_imports`).
- Kiểm tra tính nguyên tử (Rollback khi ghi lịch sử nhập hàng lỗi).
- Phân quyền AdminMiddleware.

### Lệnh Chạy Test
```bash
# Chạy bộ test tính năng thương mại điện tử
php artisan test tests/Feature/EcommerceWorkflowTest.php --compact

# Chạy toàn bộ test suite
php artisan test --compact
```
> [!IMPORTANT]
> Mọi thay đổi mã nguồn logic nghiệp vụ phải đảm bảo lệnh `php artisan test --compact` trả về **100% Passed**.

---

## 3. Quy Chuẩn Định Dạng Mã Nguồn (Laravel Pint)
Dự án tuân thủ nghiêm ngặt chuẩn PSR-12 thông qua Laravel Pint:
```bash
# Định dạng và sửa lỗi style cho các file đã thay đổi
vendor/bin/pint --format agent
```
*Lưu ý: Không dùng `vendor/bin/pint --test`, luôn dùng flag `--format agent` để tự động sửa lỗi formatting.*

---

## 4. Nguyên Tắc An Toàn Dành Cho AI Khi Làm Việc
1. **Không chạy `migrate:fresh`:** Tuyệt đối không reset database vì sẽ xóa sạch dữ liệu sản phẩm, đơn hàng và khách hàng thật.
2. **Không tự ý chạy lệnh Git:** Không tự ý `git commit`, `git push`, `git reset`, `git checkout` trừ khi người dùng ra lệnh trực tiếp trong prompt.
3. **Bảo tồn tính toàn vẹn tài liệu:** Giữ nguyên các comment và docstring hiện có không liên quan đến phạm vi sửa đổi.
4. **Tránh lỗi mở rộng biến trong PowerShell trên Windows:** Khi chạy script kiểm tra bằng PHP, không truyền inline string chứa ký tự `$` trong terminal PowerShell; hãy tạo file script tạm trong thư mục scratch để thực thi.
5. **Kiểm tra Blade & JavaScript:** Khi viết mã nhúng PHP/Blade bên trong thẻ `<script>`, luôn viết gọn trên 1 dòng (ví dụ: `let index = {{ $product->count() }};`) để tránh việc công cụ format HTML tách ngoặc nhọn gây lỗi cú pháp.

