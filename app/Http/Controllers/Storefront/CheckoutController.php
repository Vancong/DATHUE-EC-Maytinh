<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected OrderService $orderService
    ) {}

    public function index(): View|RedirectResponse
    {
        $cart = $this->cartService->getCart();

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng đang trống. Vui lòng chọn sản phẩm để thanh toán.');
        }

        $couponCode = Session::get('applied_coupon');
        $coupon = null;
        $discountAmount = 0.0;

        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)->where('is_active', true)->first();
            if ($coupon && $coupon->isValidForAmount($cart->subtotal)) {
                $discountAmount = $coupon->calculateDiscount($cart->subtotal);
            } else {
                Session::forget('applied_coupon');
                $coupon = null;
            }
        }

        $shippingFee = 0.0;
        $totalAmount = max(0, ($cart->subtotal - $discountAmount) + $shippingFee);

        return view('storefront.checkout.index', compact('cart', 'coupon', 'discountAmount', 'shippingFee', 'totalAmount'));
    }

    public function applyCoupon(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string'],
        ], [
            'code.required' => 'Vui lòng nhập mã giảm giá.',
        ]);

        $code = strtoupper(trim($request->input('code')));
        $cart = $this->cartService->getCart();

        $coupon = Coupon::where('code', $code)->where('is_active', true)->first();

        if (! $coupon) {
            return back()->with('error', 'Mã giảm giá không tồn tại hoặc đã hết hiệu lực.');
        }

        if (! $coupon->isValidForAmount($cart->subtotal)) {
            $minAmount = number_format($coupon->min_order_amount, 0, ',', '.').'đ';

            return back()->with('error', 'Mã giảm giá này yêu cầu đơn hàng tối thiểu từ '.$minAmount.' hoặc đã hết lượt sử dụng.');
        }

        Session::put('applied_coupon', $coupon->code);

        return back()->with('success', 'Áp dụng mã giảm giá '.$coupon->code.' thành công!');
    }

    public function removeCoupon(): RedirectResponse
    {
        Session::forget('applied_coupon');

        return back()->with('success', 'Đã hủy áp dụng mã giảm giá.');
    }

    public function process(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'string', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'regex:/^(0[3|5|7|8|9])+([0-9]{8})$/'],
            'shipping_address' => ['required', 'string', 'max:500'],
            'note' => ['nullable', 'string', 'max:500'],
        ], [
            'customer_name.required' => 'Vui lòng nhập họ và tên người nhận.',
            'customer_email.required' => 'Vui lòng nhập địa chỉ email nhận thông báo.',
            'customer_phone.required' => 'Vui lòng nhập số điện thoại liên hệ giao hàng.',
            'customer_phone.regex' => 'Số điện thoại không hợp lệ (Ví dụ: 0901234567).',
            'shipping_address.required' => 'Vui lòng nhập địa chỉ nhận hàng chi tiết.',
        ]);

        $cart = $this->cartService->getCart();

        $couponCode = Session::get('applied_coupon');
        $coupon = null;
        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)->where('is_active', true)->first();
        }

        if (Auth::check()) {
            $data['user_id'] = Auth::id();
        }

        try {
            $order = $this->orderService->createOrder($data, $cart, $coupon);
            Session::forget('applied_coupon');

            return redirect()->route('checkout.success', $order->order_code);
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Đặt hàng không thành công: '.$e->getMessage());
        }
    }

    public function success(string $orderCode): View
    {
        $order = Order::where('order_code', $orderCode)->with('items')->firstOrFail();

        return view('storefront.checkout.success', compact('order'));
    }
}
