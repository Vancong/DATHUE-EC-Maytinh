# 03. QUẢN LÝ TỒN KHO & NHẬP HÀNG (INVENTORY MANAGEMENT)

## 1. Triết Lý Thiết Kế Mới (Simplified Inventory Architecture)
Để phù hợp với đồ án thương mại điện tử sinh viên nhưng vẫn đảm bảo tính toàn vẹn dữ liệu chuẩn doanh nghiệp, TechStore áp dụng mô hình quản lý kho tinh gọn:
- **Lưu trữ trực tiếp:** Số lượng tồn khả dụng được quản lý trực tiếp tại trường `stock_quantity` trên bảng `products`.
- **Ngưỡng cảnh báo:** Cột `min_stock` giúp hệ thống tự động phát hiện sản phẩm sắp hết hàng.
- **Loại bỏ tính phức tạp không cần thiết:** Không sử dụng mô hình đa kho (`warehouses`), không dùng phiếu kiểm kê (`stock counts`), không chuyển kho (`transfers`), không dùng cơ chế giữ chỗ tách rời (`reserved_quantity`).

---

## 2. Nghiệp Vụ Nhập Hàng Từ Nhà Cung Cấp (Stock In)

### Quy Trình Thực Hiện
Khi Admin bấm nút **+ Nhập Hàng** tại giao diện quản lý tồn kho:
1. Mở modal nhập hàng nhanh với thông tin sản phẩm tiếp nhận.
2. Bắt buộc chọn **Nhà cung cấp** (lấy từ bảng `suppliers`).
3. Nhập số lượng cộng thêm (`quantity > 0`).
4. Nhập ghi chú lô hàng (tùy chọn).
5. Bấm **Xác Nhận Nhập Hàng**.

### Cơ Chế Xử Lý Backend (`InventoryController` & `InventoryService`)
- **Validation:**
  ```php
  'supplier_id' => [
      'required',
      'integer',
      Rule::exists('suppliers', 'id')
          ->where('is_active', true)
          ->whereNull('deleted_at'),
  ],
  'quantity' => ['required', 'integer', 'min:1'],
  'note' => ['nullable', 'string', 'max:500'],
  ```
- **Xử lý giao dịch nguyên tử (`DB::transaction`):**
  1. Khóa sản phẩm chống Race Condition:
     ```php
     $lockedProduct = Product::where('id', $product->id)->lockForUpdate()->firstOrFail();
     ```
  2. Tăng số lượng tồn:
     ```php
     $lockedProduct->increment('stock_quantity', $quantity);
     ```
  3. Ghi nhật ký vào bảng `product_stock_imports`:
     ```php
     ProductStockImport::create([
         'product_id' => $lockedProduct->id,
         'supplier_id' => $supplierId,
         'quantity' => $quantity,
         'note' => $note,
         'created_by' => Auth::id(),
     ]);
     ```
  4. **Rollback an toàn:** Nếu việc ghi nhật ký hoặc truy vấn cơ sở dữ liệu gặp lỗi, transaction sẽ rollback 100%, tồn kho không bị tăng sai.

---

## 3. Cấu Trúc Bảng Lịch Sử Nhập Hàng (`product_stock_imports`)

| Cột | Kiểu Dữ Liệu | Ràng Buộc | Ý Nghĩa |
|:---|:---|:---|:---|
| `id` | bigint (PK) | Auto increment | Mã bản ghi nhập hàng |
| `product_id` | foreignId | `restrictOnDelete()` | Sản phẩm được nhập (không cho xóa sản phẩm nếu đã có lịch sử) |
| `supplier_id` | foreignId | `restrictOnDelete()` | Nhà cung cấp xuất hàng |
| `quantity` | unsignedInteger | `min: 1` | Số lượng hàng bổ sung vào kho |
| `note` | varchar(500) | Nullable | Ghi chú đợt nhập (số hợp đồng, lô hàng,...) |
| `created_by` | foreignId | `nullOnDelete()` | Admin thực hiện thao tác nhập |
| `created_at` | timestamp | Current timestamp | Thời điểm nhập hàng vào kho |

---

## 4. Điều Chỉnh Tồn Kho Trực Tiếp (Stock Adjustment)
Admin có thể sử dụng modal **Sửa Tồn** để điều chỉnh trực tiếp số lượng tồn và ngưỡng cảnh báo:
- **Route:** `PATCH admin/inventory/{product}/update-stock`
- **Method:** `InventoryService::updateStock(Product $product, int $newQuantity, ?int $minStock)`
- Thao tác này cũng được bảo vệ bằng `lockForUpdate()` và cập nhật trực tiếp `stock_quantity`, `min_stock`.

---

## 5. Cảnh Báo Tồn Kho Thấp (Low Stock Alert)
Hệ thống phân chia 3 cấp độ tồn kho hiển thị trực quan:
1. **Hết hàng (`stock_quantity <= 0`):** Badge màu đỏ, khách không thể đặt mua.
2. **Cảnh báo sắp hết (`stock_quantity <= min_stock`):** Badge màu vàng cam, xuất hiện trên Dashboard cảnh báo và bộ lọc kho.
3. **Đầy đủ (`stock_quantity > min_stock`):** Badge màu xanh lục.

