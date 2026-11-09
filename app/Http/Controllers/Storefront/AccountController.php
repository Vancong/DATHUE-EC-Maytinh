<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

    public function profile(): View
    {
        $user = Auth::user();

        return view('storefront.account.profile', compact('user'));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^(0[3|5|7|8|9])+([0-9]{8})$/'],
            'address' => ['nullable', 'string', 'max:500'],
            'current_password' => ['nullable', 'string'],
            'new_password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required' => 'Họ tên không được để trống.',
            'phone.required' => 'Số điện thoại không được để trống.',
            'phone.regex' => 'Số điện thoại không đúng định dạng.',
            'new_password.min' => 'Mật khẩu mới tối thiểu 6 ký tự.',
            'new_password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
        ]);

        if (! empty($data['current_password'])) {
            if (! Hash::check($data['current_password'], $user->password)) {
                return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không chính xác.']);
            }
            if (! empty($data['new_password'])) {
                $user->password = Hash::make($data['new_password']);
            }
        }

        $user->name = $data['name'];
        $user->phone = $data['phone'];
        $user->address = $data['address'] ?? null;
        $user->save();

        return back()->with('success', 'Cập nhật thông tin tài khoản thành công!');
    }

    public function orders(): View
    {
        $orders = Order::where('user_id', Auth::id())
            ->withCount('items')
            ->latest()
            ->paginate(10);

        return view('storefront.account.orders', compact('orders'));
    }

    public function orderDetail(string $orderCode): View
    {
        $order = Order::where('order_code', $orderCode)
            ->where('user_id', Auth::id())
            ->with(['items.product', 'reviews'])
            ->firstOrFail();

        return view('storefront.account.order_detail', compact('order'));
    }

    public function cancelOrder(Request $request, string $orderCode): RedirectResponse
    {
        $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ], [
            'reason.required' => 'Vui lòng cung cấp lý do hủy đơn hàng.',
        ]);

        $order = Order::where('order_code', $orderCode)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        try {
            $this->orderService->updateStatus($order, 'cancelled', $request->input('reason'), Auth::id());

            return back()->with('success', 'Đơn hàng '.$orderCode.' đã được hủy thành công.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
