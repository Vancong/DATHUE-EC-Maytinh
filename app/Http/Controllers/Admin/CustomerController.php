<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::where('role', 'customer')
            ->withCount('orders')
            ->withSum(['orders' => fn ($q) => $q->where('payment_status', 'paid')], 'total_amount')
            ->latest();

        if ($request->filled('keyword')) {
            $kw = trim($request->query('keyword'));
            $query->where(function ($q) use ($kw) {
                $q->where('name', 'like', "%{$kw}%")
                    ->orWhere('email', 'like', "%{$kw}%")
                    ->orWhere('phone', 'like', "%{$kw}%");
            });
        }

        $customers = $query->paginate(15)->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function show(User $customer): View
    {
        abort_if($customer->role !== 'customer', 404);

        $customer->load(['orders' => fn ($q) => $q->latest()]);

        return view('admin.customers.show', compact('customer'));
    }

    public function toggleActive(User $customer): RedirectResponse
    {
        abort_if($customer->role !== 'customer', 404);

        $customer->is_active = ! $customer->is_active;
        $customer->save();

        $statusStr = $customer->is_active ? 'mở khóa' : 'khóa';

        return back()->with('success', "Đã {$statusStr} tài khoản khách hàng {$customer->name}.");
    }
}
