<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'order_id' => ['required', 'integer', 'exists:orders,id'],
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['required', 'string', 'min:5', 'max:1000'],
        ], [
            'rating.required' => 'Vui lòng chọn số sao đánh giá (1-5 sao).',
            'comment.required' => 'Vui lòng chia sẻ cảm nhận của bạn về sản phẩm.',
            'comment.min' => 'Nội dung đánh giá cần ít nhất 5 ký tự.',
        ]);

        $order = Order::where('id', $data['order_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($order->order_status !== 'delivered') {
            return back()->with('error', 'Bạn chỉ có thể đánh giá sản phẩm sau khi đơn hàng đã được giao thành công.');
        }

        $itemExists = $order->items()->where('product_id', $data['product_id'])->exists();
        if (! $itemExists) {
            return back()->with('error', 'Sản phẩm này không thuộc đơn hàng của bạn.');
        }

        $existingReview = Review::where('order_id', $order->id)
            ->where('product_id', $data['product_id'])
            ->where('user_id', Auth::id())
            ->first();

        if ($existingReview) {
            return back()->with('error', 'Bạn đã gửi đánh giá cho sản phẩm này trong đơn hàng này rồi.');
        }

        Review::create([
            'order_id' => $order->id,
            'product_id' => $data['product_id'],
            'user_id' => Auth::id(),
            'rating' => $data['rating'],
            'comment' => $data['comment'],
            'status' => 'approved', // Tự động duyệt hoặc admin có thể quản lý duyệt/ẩn
        ]);

        return back()->with('success', 'Cảm ơn bạn đã gửi đánh giá sản phẩm!');
    }
}
