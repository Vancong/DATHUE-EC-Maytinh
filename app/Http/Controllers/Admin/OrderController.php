<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

    public function index(Request $request): View
    {
        $query = Order::with(['user'])->latest();

        if ($request->filled('keyword')) {
            $kw = trim($request->query('keyword'));
            $query->where(function ($q) use ($kw) {
                $q->where('order_code', 'like', "%{$kw}%")
                    ->orWhere('customer_name', 'like', "%{$kw}%")
                    ->orWhere('customer_phone', 'like', "%{$kw}%")
                    ->orWhere('customer_email', 'like', "%{$kw}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('order_status', $request->query('status'));
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->query('payment_status'));
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $order->load(['items.product', 'user', 'coupon']);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'order_status' => ['required', 'string', 'in:pending,confirmed,processing,shipping,delivered,failed_delivery,cancelled'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $this->orderService->updateStatus(
                $order,
                $request->input('order_status'),
                $request->input('reason'),
                Auth::id()
            );

            return back()->with('success', 'Đã chuyển trạng thái đơn hàng thành công!');
        } catch (Exception $e) {
            return back()->with('error', 'Lỗi cập nhật trạng thái: '.$e->getMessage());
        }
    }

    public function restock(Request $request, Order $order): RedirectResponse
    {
        try {
            $this->orderService->restockOrder($order, $request->input('note'));

            return back()->with('success', 'Đã xác nhận nhận lại hàng về kho và hoàn trả tồn kho thành công!');
        } catch (Exception $e) {
            return back()->with('error', 'Lỗi hoàn kho: '.$e->getMessage());
        }
    }

    public function invoice(Order $order): View
    {
        $order->load(['items', 'coupon']);

        return view('admin.orders.invoice', compact('order'));
    }
}
