<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    /**
     * Create an order from cart with DB transaction and lockForUpdate
     * Deducts stock_quantity immediately upon order creation to prevent overselling
     *
     * @throws Exception
     */
    public function createOrder(array $customerData, Cart $cart, ?Coupon $coupon = null): Order
    {
        return DB::transaction(function () use ($customerData, $cart, $coupon) {
            if ($cart->items->isEmpty()) {
                throw new Exception('Giỏ hàng của bạn đang trống.');
            }

            $subtotal = 0.0;
            $itemsToProcess = [];

            // 1. Verify and lock product for each cart item, then deduct stock immediately
            foreach ($cart->items as $cartItem) {
                $product = Product::where('id', $cartItem->product_id)
                    ->lockForUpdate()
                    ->first();

                if (! $product || ! $product->is_active) {
                    throw new Exception('Sản phẩm "'.($product?->name ?? 'N/A').'" hiện không còn kinh doanh.');
                }

                if ($product->stock_quantity < $cartItem->quantity) {
                    throw new Exception('Sản phẩm "'.$product->name.'" chỉ còn '.$product->stock_quantity.' sản phẩm trong kho.');
                }

                // Deduct stock immediately
                $product->decrement('stock_quantity', $cartItem->quantity);

                $unitPrice = (float) ($product->sale_price ?? $product->price);
                $itemTotal = $unitPrice * $cartItem->quantity;
                $subtotal += $itemTotal;

                $itemsToProcess[] = [
                    'product' => $product,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $itemTotal,
                ];
            }

            // 2. Calculate Coupon discount
            $discountAmount = 0.0;
            if ($coupon) {
                $lockedCoupon = Coupon::where('id', $coupon->id)->lockForUpdate()->first();
                if ($lockedCoupon && $lockedCoupon->isValidForAmount($subtotal)) {
                    $discountAmount = $lockedCoupon->calculateDiscount($subtotal);
                    $lockedCoupon->increment('used_count');
                } else {
                    $coupon = null;
                }
            }

            $shippingFee = 0.0; // Miễn phí giao hàng COD toàn quốc
            $totalAmount = max(0, ($subtotal - $discountAmount) + $shippingFee);

            // 3. Generate Unique Order Code (ORD-YYYYMMDD-XXXX)
            $orderCode = 'ORD-'.date('Ymd').'-'.strtoupper(Str::random(5));

            // 4. Create Order
            $order = Order::create([
                'order_code' => $orderCode,
                'user_id' => $customerData['user_id'] ?? null,
                'coupon_id' => $coupon?->id,
                'customer_name' => $customerData['customer_name'],
                'customer_email' => $customerData['customer_email'],
                'customer_phone' => $customerData['customer_phone'],
                'shipping_address' => $customerData['shipping_address'],
                'note' => $customerData['note'] ?? null,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'shipping_fee' => $shippingFee,
                'total_amount' => $totalAmount,
                'payment_method' => 'cod',
                'payment_status' => 'unpaid',
                'order_status' => 'pending',
                'inventory_settled' => false,
                'restocked_at' => null,
            ]);

            // 5. Create Order Items snapshot
            foreach ($itemsToProcess as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'product_name' => $item['product']->name,
                    'product_sku' => $item['product']->sku,
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                    'total_price' => $item['total_price'],
                ]);
            }

            // 6. Clear cart
            $cart->items()->delete();

            return $order;
        });
    }

    /**
     * Update order status following the lifecycle:
     * pending -> confirmed -> processing -> shipping -> delivered / cancelled
     *
     * @throws Exception
     */
    public function updateStatus(Order $order, string $newStatus, ?string $reason = null, ?int $userId = null): Order
    {
        return DB::transaction(function () use ($order, $newStatus, $reason) {
            $order = Order::where('id', $order->id)->lockForUpdate()->firstOrFail();
            $oldStatus = $order->order_status;

            if ($oldStatus === $newStatus) {
                return $order;
            }

            if ($oldStatus === 'delivered') {
                throw new Exception('Đơn hàng đã giao thành công và thu tiền hoàn tất, không thể thay đổi trạng thái.');
            }

            if ($oldStatus === 'cancelled' && $order->inventory_settled) {
                throw new Exception('Đơn hàng đã bị hủy và tất toán, không thể thay đổi trạng thái.');
            }

            // 1. Handling Delivered: Mark payment as paid, increment sold_quantity (stock was already deducted at checkout)
            if ($newStatus === 'delivered') {
                $order->payment_status = 'paid';
                $order->inventory_settled = true;
                foreach ($order->items as $item) {
                    Product::where('id', $item->product_id)->increment('sold_quantity', $item->quantity);
                }
            }

            // 2. Handling Cancellation
            if ($newStatus === 'cancelled') {
                // If cancelled before shipping (pending, confirmed, processing):
                // Stock was deducted at checkout, so restore stock immediately once!
                if (in_array($oldStatus, ['pending', 'confirmed', 'processing'])) {
                    if (! $order->inventory_settled) {
                        foreach ($order->items as $item) {
                            Product::where('id', $item->product_id)->lockForUpdate()->increment('stock_quantity', $item->quantity);
                        }
                        $order->inventory_settled = true;
                        $order->restocked_at = now();
                    }
                }
                // Note: If old status was shipping, stock is NOT restocked automatically here.
                // The goods are with courier/carrier; Admin must physically receive them back and call restockOrder()!

                // If coupon was used, decrement used_count
                if ($order->coupon_id) {
                    Coupon::where('id', $order->coupon_id)->decrement('used_count');
                }

                $order->cancelled_reason = $reason ?? 'Hủy đơn hàng';
            }

            $order->order_status = $newStatus;
            $order->save();

            return $order;
        });
    }

    /**
     * Admin explicitly confirms receipt of returned items at warehouse & restocks inventory
     * Only callable once (inventory_settled = true) to prevent double restock
     *
     * @throws Exception
     */
    public function restockOrder(Order $order, ?string $note = null): Order
    {
        return DB::transaction(function () use ($order, $note) {
            $order = Order::where('id', $order->id)->lockForUpdate()->firstOrFail();

            if ($order->inventory_settled) {
                throw new Exception('Đơn hàng này đã được tất toán hoặc hoàn tồn kho trước đó, không thể hoàn kho lần hai.');
            }

            if ($order->order_status === 'delivered') {
                throw new Exception('Không thể hoàn kho đơn hàng đã giao thành công.');
            }

            // Restock each item
            foreach ($order->items as $item) {
                Product::where('id', $item->product_id)->lockForUpdate()->increment('stock_quantity', $item->quantity);
            }

            $order->inventory_settled = true;
            $order->restocked_at = now();
            $order->order_status = 'cancelled';
            $order->cancelled_reason = $note ?? 'Hàng giao thất bại đã được vận chuyển hoàn về kho và Admin xác nhận nhập kho thành công.';
            $order->save();

            return $order;
        });
    }
}
