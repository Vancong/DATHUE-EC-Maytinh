<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalRevenue = (float) Order::where('payment_status', 'paid')->sum('total_amount');
        $totalOrders = Order::count();
        $totalProducts = Product::count();
        $totalCustomers = User::where('role', 'customer')->count();

        // 1. Monthly revenue for the current year
        $monthlyRevenue = [];
        $currentYear = date('Y');
        for ($m = 1; $m <= 12; $m++) {
            $rev = Order::where('payment_status', 'paid')
                ->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $m)
                ->sum('total_amount');
            $monthlyRevenue[] = (float) $rev;
        }

        // 2. Order status distribution
        $statusCounts = Order::select('order_status', DB::raw('count(*) as count'))
            ->groupBy('order_status')
            ->pluck('count', 'order_status')
            ->toArray();

        $statusData = [
            'pending' => $statusCounts['pending'] ?? 0,
            'confirmed' => $statusCounts['confirmed'] ?? 0,
            'processing' => $statusCounts['processing'] ?? 0,
            'shipping' => $statusCounts['shipping'] ?? 0,
            'delivered' => $statusCounts['delivered'] ?? 0,
            'cancelled' => $statusCounts['cancelled'] ?? 0,
        ];

        // 3. Top selling products
        $topProducts = OrderItem::select('product_id', 'product_name', DB::raw('SUM(quantity) as total_sold'), DB::raw('SUM(total_price) as total_sales'))
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        // 4. Low stock warning items (stock_quantity <= min_stock)
        $lowStockItems = Product::where('is_active', true)
            ->whereColumn('stock_quantity', '<=', 'min_stock')
            ->orderBy('stock_quantity')
            ->take(6)
            ->get();

        // 5. Recent orders
        $recentOrders = Order::with('user')->latest()->take(6)->get();

        return view('admin.dashboard.index', compact(
            'totalRevenue',
            'totalOrders',
            'totalProducts',
            'totalCustomers',
            'monthlyRevenue',
            'statusData',
            'topProducts',
            'lowStockItems',
            'recentOrders'
        ));
    }
}
