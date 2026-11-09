# 06. ĐÁNH GIÁ SẢN PHẨM & KIỂM DUYỆT (REVIEWS & MODERATION)

## 1. Điều Kiện Đánh Giá Của Khách Hàng
Để đảm bảo tính trung thực và khách quan của hệ thống đánh giá (Verified Purchase):
- Khách hàng **chỉ được phép đánh giá sản phẩm khi đã mua và nhận hàng thành công** (`order_status = 'delivered'`).
- Mỗi đơn hàng và mỗi sản phẩm trong đơn chỉ được gửi 1 lần đánh giá.

---

## 2. Cấu Trúc Bảng `reviews`
| Cột | Kiểu Dữ Liệu | Mục Đích |
|:---|:---|:---|
| `id` | bigint (PK) | Mã đánh giá |
| `product_id` | foreignId | Sản phẩm được đánh giá |
| `user_id` | foreignId | Khách hàng thực hiện đánh giá |
| `order_id` | foreignId (nullable) | Đơn hàng mua sản phẩm tương ứng |
| `rating` | tinyint (1 - 5) | Số sao đánh giá (1: Rất tệ -> 5: Xuất sắc) |
| `comment` | text (nullable) | Nội dung nhận xét chi tiết |
| `status` | enum | `'pending'`, `'approved'`, `'rejected'` |
| `created_at` | timestamp | Thời gian gửi đánh giá |

---

## 3. Quy Trình Kiểm Duyệt Phía Admin

### Giao Diện & Điều Khiển
- **Controller:** `App\Http\Controllers\Admin\ReviewController`
- **Route danh sách:** `GET admin/reviews`
- **Thao tác duyệt / từ chối:** `PATCH admin/reviews/{review}/status`
  - Chuyển sang `approved`: Đánh giá được hiển thị công khai ở trang chi tiết sản phẩm ngoài Storefront.
  - Chuyển sang `rejected`: Đánh giá bị ẩn.
- **Thao tác xóa:** `DELETE admin/reviews/{review}` – xóa vĩnh viễn các đánh giá vi phạm tiêu chuẩn cộng đồng (spam, từ ngữ thô tục).

---

## 4. Hiển Thị Ngoài Storefront (`Product.php`)
- Quan hệ `approvedReviews()`:
  ```php
  public function approvedReviews(): HasMany
  {
      return $this->hasMany(Review::class)->where('status', 'approved');
  }
  ```
- Chỉ các nhận xét có `status = 'approved'` mới được tính vào điểm đánh giá trung bình (Rating Average) và hiển thị trên giao diện người dùng.

