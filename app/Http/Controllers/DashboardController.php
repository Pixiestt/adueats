<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with key metrics.
     */
    public function index()
    {
        // Get today's sales
        $todaySales = Order::whereDate('created_at', Carbon::today())
            ->where('status', 'completed')
            ->sum('total_amount');
            
        // Get total orders today
        $todayOrders = Order::whereDate('created_at', Carbon::today())->count();
        
        // Get top selling products
        $topProducts = OrderItem::select('product_name', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('product_name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();
            
        // Get low stock items
        $lowStock = Inventory::with('product')
            ->whereRaw('quantity <= minimum_stock')
            ->get();
            
        return view('dashboard.index', compact('todaySales', 'todayOrders', 'topProducts', 'lowStock'));
    }
    
    /**
     * Display sales analytics.
     */
    public function salesAnalytics()
    {
        // Get daily sales for the past 30 days
        $dailySales = Order::where('status', 'completed')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        // Get sales by payment method
        $salesByPayment = Order::where('status', 'completed')
            ->select('payment_method', DB::raw('SUM(total_amount) as total'))
            ->groupBy('payment_method')
            ->get();
            
        return view('analytics.sales', compact('dailySales', 'salesByPayment'));
    }
    
    /**
     * Display product analytics.
     */
    public function productAnalytics()
    {
        // Get top selling products
        $topProducts = OrderItem::select('product_name', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(subtotal) as total_revenue'))
            ->groupBy('product_name')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();
            
        // Get sales by category
        $salesByCategory = OrderItem::join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('SUM(order_items.subtotal) as total'))
            ->groupBy('categories.name')
            ->get();
            
        return view('analytics.products', compact('topProducts', 'salesByCategory'));
    }
    
    /**
     * Display inventory analytics.
     */
    public function inventoryAnalytics()
    {
        // Get inventory status
        $inventoryStatus = Inventory::with('product')
            ->select('product_id', 'quantity', 'minimum_stock')
            ->get();
            
        // Get products that need restocking
        $needRestock = Inventory::with('product')
            ->whereRaw('quantity <= minimum_stock')
            ->get();
            
        // Get inventory turnover rate
        $inventoryTurnover = OrderItem::select('product_id', DB::raw('SUM(quantity) as sold_quantity'))
            ->whereMonth('created_at', Carbon::now()->month)
            ->groupBy('product_id')
            ->get();
            
        return view('analytics.inventory', compact('inventoryStatus', 'needRestock', 'inventoryTurnover'));
    }
}
