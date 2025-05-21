<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    if (Auth::check()) {
        if (Auth::user()->isAdmin()) {
            return redirect()->route('dashboard');
        } else {
            return redirect()->route('customer.dashboard');
        }
    }
    return redirect()->route('login');
});

// Authentication Routes
Auth::routes();

// Admin Routes
Route::middleware(['auth', 'admin'])->group(function () {
    // Dashboard routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // POS routes
    Route::get('/pos', [OrderController::class, 'create'])->name('pos.create');
    Route::post('/pos', [OrderController::class, 'store'])->name('pos.store');

    // Resource routes
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('orders', OrderController::class);
    Route::resource('inventory', InventoryController::class);

    // Inventory management routes
    Route::post('/inventory/restock', [InventoryController::class, 'restock'])->name('inventory.restock');
    Route::post('/inventory/update-stock', [InventoryController::class, 'updateStock'])->name('inventory.update-stock');

    // Analytics routes
    Route::get('/analytics/sales', [AnalyticsController::class, 'sales'])->name('analytics.sales');
    Route::get('/analytics/products', [AnalyticsController::class, 'products'])->name('analytics.products');
    Route::get('/analytics/inventory', [AnalyticsController::class, 'inventory'])->name('analytics.inventory');
});

// Customer Routes
Route::middleware(['auth', 'customer'])->prefix('customer')->group(function () {
    Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('customer.dashboard');
    Route::get('/order', [CustomerController::class, 'orderForm'])->name('customer.order');
    Route::post('/order', [CustomerController::class, 'storeOrder'])->name('customer.order.store');
    Route::get('/orders', [CustomerController::class, 'orderHistory'])->name('customer.orders');
    Route::get('/orders/{id}', [CustomerController::class, 'showOrder'])->name('customer.orders.show');
    Route::put('/orders/{id}/cancel', [CustomerController::class, 'cancelOrder'])->name('customer.orders.cancel');
    Route::get('/profile', [CustomerController::class, 'profile'])->name('customer.profile');
    Route::put('/profile', [CustomerController::class, 'updateProfile'])->name('customer.profile.update');
});

// Home route
Route::get('/home', [HomeController::class, 'index'])->name('home');
