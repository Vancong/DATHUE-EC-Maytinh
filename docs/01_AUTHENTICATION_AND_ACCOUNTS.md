# 01. XÁC THỰC, PHÂN QUYỀN & TÀI KHOẢN (AUTHENTICATION & ROLES)

## 1. Mô Hình Người Dùng
Bảng `users` quản lý toàn bộ tài khoản trong hệ thống thông qua cột phân quyền `role`:
- `role = 'admin'`: Quản trị viên, toàn quyền truy cập khu vực `/admin`.
- `role = 'customer'`: Khách hàng, truy cập mua hàng, quản lý đơn hàng cá nhân, viết đánh giá.
- Cột `is_active` (boolean): Cờ kích hoạt/khóa tài khoản. Khi `is_active = false`, tài khoản không thể đăng nhập.

---

## 2. Phân Quyền Quản Trị (Admin Panel)

### Middleware Bảo Vệ
- **Class:** `App\Http\Middleware\AdminMiddleware`
- **Áp dụng:** Toàn bộ route có tiền tố `admin/` (trừ các route đăng nhập/đăng xuất công khai).
- **Quy tắc kiểm tra:**
  1. Nếu chưa đăng nhập: Chuyển hướng về route `admin.login` kèm thông báo `Vui lòng đăng nhập với tài khoản quản trị.`
  2. Nếu người dùng đăng nhập nhưng `role !== 'admin'` hoặc `is_active == false`:
     - Tự động gọi `Auth::logout()`
     - Hủy session (`$request->session()->invalidate()`)
     - Sinh lại CSRF token (`$request->session()->regenerateToken()`)
     - Chuyển hướng về `admin.login` kèm lỗi `Bạn không có quyền truy cập trang quản trị.`

### Quản Lý Tài Khoản Quản Trị (Admin Accounts)
- **Controller:** `App\Http\Controllers\Admin\AdminAccountController`
- **Chức năng:**
  - Danh sách tài khoản Admin (`admin.accounts.index`)
  - Tạo mới tài khoản Admin (`admin.accounts.create`, `admin.accounts.store`)
  - Bật / Tắt trạng thái hoạt động (`admin.accounts.toggle-active`)
  - Không cho phép tự khóa tài khoản của chính mình để tránh mất quyền quản trị.

---

## 3. Xác Thực Khách Hàng (Storefront Auth)

### Đăng Ký & Đăng Nhập
- **Controller:** `App\Http\Controllers\Storefront\CustomerAuthController`
- **Đăng ký (`register`):**
  - Nhập họ tên, email (unique), số điện thoại, địa chỉ, mật khẩu (tối thiểu 6 ký tự).
  - Tự động gán `role = 'customer'`, `is_active = true`.
  - Mã hóa mật khẩu bằng `Hash::make()`.
  - Tự động đăng nhập sau khi tạo tài khoản thành công.
- **Đăng nhập (`login`):**
  - Xác thực qua email và password.
  - Kiểm tra điều kiện `is_active == true`.
  - Hỗ trợ ghi nhớ đăng nhập (`Remember Me`).

### Khu Vực Khách Hàng (Customer Account)
- **Controller:** `App\Http\Controllers\Storefront\AccountController`
- **Middleware:** `auth`, `App\Http\Middleware\CustomerMiddleware`
- **Chức năng:**
  - `account.profile`: Xem và cập nhật thông tin cá nhân (họ tên, số điện thoại, địa chỉ nhận hàng).
  - `account.orders`: Lịch sử đơn hàng của cá nhân, lọc theo trạng thái.
  - `account.orders.show`: Chi tiết đơn hàng, theo dõi tiến độ vận chuyển và nút đánh giá sản phẩm khi đơn đã `delivered`.

