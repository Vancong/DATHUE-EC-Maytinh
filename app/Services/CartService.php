<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    public function getCart(): Cart
    {
        $sessionId = Session::getId();
        $userId = Auth::id();

        if ($userId) {
            $cart = Cart::firstOrCreate(['user_id' => $userId]);

            // Migrate session cart items if any
            $sessionCart = Cart::where('session_id', $sessionId)->whereNull('user_id')->first();
            if ($sessionCart) {
                foreach ($sessionCart->items as $item) {
                    $existingItem = CartItem::where('cart_id', $cart->id)
                        ->where('product_id', $item->product_id)
                        ->first();

                    if ($existingItem) {
                        $existingItem->update(['quantity' => $existingItem->quantity + $item->quantity]);
                    } else {
                        $item->update(['cart_id' => $cart->id]);
                    }
                }
                $sessionCart->delete();
            }

            return $cart->load('items.product');
        }

        $cart = Cart::firstOrCreate(['session_id' => $sessionId]);

        return $cart->load('items.product');
    }

    public function addToCart(int $productId, int $quantity = 1): array
    {
        $product = Product::active()->findOrFail($productId);
        $availableStock = $product->available_stock;

        if ($availableStock <= 0) {
            return [
                'success' => false,
                'message' => 'Sản phẩm "'.$product->name.'" hiện đã hết hàng trong kho.',
            ];
        }

        $cart = $this->getCart();
        $cartItem = CartItem::where('cart_id', $cart->id)->where('product_id', $productId)->first();

        $currentQty = $cartItem ? $cartItem->quantity : 0;
        $newQty = $currentQty + $quantity;

        if ($newQty > $availableStock) {
            return [
                'success' => false,
                'message' => 'Kho chỉ còn '.$availableStock.' sản phẩm khả dụng. Bạn đã có '.$currentQty.' trong giỏ hàng.',
            ];
        }

        if ($cartItem) {
            $cartItem->update(['quantity' => $newQty]);
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
        }

        return [
            'success' => true,
            'message' => 'Đã thêm "'.$product->name.'" vào giỏ hàng thành công.',
            'cart_count' => $cart->fresh()->total_quantity,
        ];
    }

    public function updateQuantity(int $itemId, int $quantity): array
    {
        if ($quantity <= 0) {
            return $this->removeItem($itemId);
        }

        $cart = $this->getCart();
        $cartItem = CartItem::where('cart_id', $cart->id)->findOrFail($itemId);

        $availableStock = $cartItem->product->available_stock;
        if ($quantity > $availableStock) {
            return [
                'success' => false,
                'message' => 'Số lượng vượt quá số lượng tồn kho khả dụng ('.$availableStock.').',
            ];
        }

        $cartItem->update(['quantity' => $quantity]);

        return [
            'success' => true,
            'message' => 'Cập nhật số lượng thành công.',
        ];
    }

    public function removeItem(int $itemId): array
    {
        $cart = $this->getCart();
        $cartItem = CartItem::where('cart_id', $cart->id)->findOrFail($itemId);
        $cartItem->delete();

        return [
            'success' => true,
            'message' => 'Đã xóa sản phẩm khỏi giỏ hàng.',
        ];
    }

    public function clearCart(): void
    {
        $cart = $this->getCart();
        $cart->items()->delete();
    }
}
