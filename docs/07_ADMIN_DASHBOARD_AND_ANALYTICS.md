# 07. TRANG QUẢN TRỊ, THỐNG KÊ & BÁO CÁO (DASHBOARD & ANALYTICS)

## 1. Tổng Quan Dashboard (`Admin\DashboardController`)
Trang chủ quản trị (`GET /admin`) tổng hợp các chỉ số tài chính, vận hành và cảnh báo thời gian thực của toàn bộ hệ thống:

### Các Chỉ Số Thẻ KPI Đầu Trang
1. **Tổng doanh thu:** Tính tổng tiền từ các đơn hàng có `payment_status = 'paid'`.
2. **Tổng đơn hàng:** Số lượng đơn hàng đã phát sinh trên hệ thống.
3. **Tổng sản phẩm:** Số lượng sản phẩm đang có trong cơ sở dữ liệu.
4. **Tổng khách hàng:** Số tài khoản có `role = 'customer'`.

---

## 2. Biểu Đồ Trực Quan Hóa (Chart.js)

### Biểu Đồ Doanh Thu 12 Tháng Trong Năm
- **Kiểu:** Biểu đồ cột (Bar Chart).
- **Dữ liệu:** Doanh thu thực tế từng tháng từ tháng 1 đến tháng 12 của năm hiện tại (`whereYear` & `whereMonth`), lọc theo các đơn hàng đã thanh toán (`paid`).
- **Định dạng hiển thị:** Đơn vị triệu VNĐ (M), tooltip hiển thị đầy đủ số tiền theo định dạng tiền tệ Việt Nam.

### Biểu Đồ Cơ Cấu Trạng Thái Đơn Hàng
- **Kiểu:** Biểu đồ bánh tròn Donut (Doughnut Chart).
- **Phân loại 6 màu đại diện:**
  - Chờ xử lý (`pending`): Vàng hổ phách (#f59e0b)
  - Đã xác nhận (`confirmed`): Xanh dương (#3b82f6)
  - Đang đóng gói (`processing`): Xanh chàm (#6366f1)
  - Đang giao hàng (`shipping`): Tím (#a855f7)
  - Hoàn thành (`delivered`): Xanh ngọc lục bảo (#10b981)
  - Đã hủy (`cancelled`): Đỏ hoa hồng (#f43f5e)

---

## 3. Khối Nghiệp Vụ Cảnh Báo & Hoạt Động Gần Đây

### Top 5 Sản Phẩm Bán Chạy Nhất
- Truy vấn tổng hợp từ `order_items` kết hợp `orders`:
  ```php
  $topProducts = OrderItem::select('product_id', 'product_name', DB::raw('SUM(quantity) as total_sold'), DB::raw('SUM(total_price) as total_sales'))
      ->groupBy('product_id', 'product_name')
      ->orderByDesc('total_sold')
      ->take(5)
      ->get();
  ```

### Khối Cảnh Báo Sắp Hết Hàng (Low Stock Alert)
- Truy vấn trực tiếp từ bảng `products`:
  ```php
  $lowStockItems = Product::where('is_active', true)
      ->whereColumn('stock_quantity', '<=', 'min_stock')
      ->orderBy('stock_quantity')
      ->take(6)
      ->get();
  ```
- Hiển thị chấm đỏ nhấp nháy (Ping Animation), cảnh báo số lượng tồn và nút bấm tắt **+ Nhập Kho** dẫn thẳng tới trang Quản Lý Tồn Kho.

### Khối Đơn Hàng Mới Tiếp Nhận
- Hiển thị 6 đơn hàng mới nhất kèm mã đơn, họ tên khách hàng, số điện thoại, tổng tiền, badge trạng thái và link xem chi tiết.

