<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * Display the sales analytics dashboard.
     */
    public function sales()
    {
        // Get today's sales
        $todaySales = Order::whereDate('created_at', Carbon::today())
            ->where('status', 'completed')
            ->sum('total_amount');
            
        // Get this week's sales
        $weeklySales = Order::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->where('status', 'completed')
            ->sum('total_amount');
            
        // Get this month's sales
        $monthlySales = Order::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->where('status', 'completed')
            ->sum('total_amount');
            
        // Get average order value
        $avgOrderValue = Order::where('status', 'completed')->avg('total_amount') ?? 0;
        
        // Get daily sales for the last 30 days
        $dailySales = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as total')
            )
            ->where('status', 'completed')
            ->whereBetween('created_at', [Carbon::now()->subDays(30), Carbon::now()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        // Get sales by payment method
        $salesByPayment = Order::select(
                'payment_method',
                DB::raw('SUM(total_amount) as total')
            )
            ->where('status', 'completed')
            ->groupBy('payment_method')
            ->get();
            
        return view('analytics.sales', compact(
            'todaySales', 
            'weeklySales', 
            'monthlySales', 
            'avgOrderValue', 
            'dailySales', 
            'salesByPayment'
        ));
    }
    
    /**
     * Display the product analytics dashboard.
     */
    public function products()
    {
        // Get top selling products
        $topProducts = OrderItem::select(
                'product_id',
                'product_name',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(subtotal) as total_sales')
            )
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();
            
        // Get sales by category
        $salesByCategory = OrderItem::select(
                'categories.id as category_id',
                'categories.name as category_name',
                DB::raw('SUM(order_items.subtotal) as total_sales')
            )
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('orders.status', 'completed')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_sales')
            ->get();
            
        // Get sales by time of day
        $salesByHour = Order::select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('SUM(total_amount) as total')
            )
            ->where('status', 'completed')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
            
        return view('analytics.products', compact(
            'topProducts',
            'salesByCategory',
            'salesByHour'
        ));
    }
    
    /**
     * Display the inventory analytics dashboard.
     */
    public function inventory()
    {
        // Get low stock items
        $lowStockItems = Inventory::with('product')
            ->whereRaw('quantity <= minimum_stock')
            ->orderBy('quantity')
            ->get();
            
        // Get inventory value by category
        $inventoryByCategory = Inventory::select(
                'categories.id as category_id',
                'categories.name as category_name',
                DB::raw('SUM(inventory.quantity * products.price) as total_value'),
                DB::raw('COUNT(products.id) as product_count')
            )
            ->join('products', 'inventory.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->groupBy('categories.id', 'categories.name')
            ->get();
            
        // Get products that need restocking
        $restockNeeded = Inventory::with('product')
            ->orderByRaw('(quantity / minimum_stock)')
            ->limit(10)
            ->get();
            
        return view('analytics.inventory', compact(
            'lowStockItems',
            'inventoryByCategory',
            'restockNeeded'
        ));
    }
}
