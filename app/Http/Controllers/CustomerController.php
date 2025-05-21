<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('customer');
    }

    /**
     * Display the customer dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Get featured products
        $featuredProducts = Product::where('available', true)
            ->inRandomOrder()
            ->limit(4)
            ->get();
            
        // Get user's recent orders
        $recentOrders = Order::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        return view('customer.dashboard', compact('featuredProducts', 'recentOrders'));
    }
    
    /**
     * Show the order form.
     *
     * @return \Illuminate\View\View
     */
    public function orderForm()
    {
        $categories = Category::all();
        $products = Product::where('available', true)->get();
        
        return view('customer.order', compact('categories', 'products'));
    }
    
    /**
     * Store a new order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeOrder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,card,e-wallet',
            'notes' => 'nullable|string'
        ]);
        
        $user = Auth::user();
        $totalAmount = 0;
        
        // Calculate total amount
        foreach ($request->items as $item) {
            $product = Product::findOrFail($item['product_id']);
            $totalAmount += $product->price * $item['quantity'];
        }
        
        // Create order
        $order = Order::create([
            'user_id' => $user->id,
            'customer_name' => $user->name,
            'total_amount' => $totalAmount,
            'payment_method' => $request->payment_method,
            'status' => 'pending',
            'notes' => $request->notes
        ]);
        
        // Create order items
        foreach ($request->items as $item) {
            $product = Product::findOrFail($item['product_id']);
            
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => $item['quantity'],
                'unit_price' => $product->price,
                'subtotal' => $product->price * $item['quantity']
            ]);
            
            // Update inventory
            if ($product->inventory) {
                $product->inventory->decrement('quantity', $item['quantity']);
            }
        }
        
        return response()->json([
            'success' => true,
            'order_id' => $order->id,
            'total' => $order->total_amount
        ]);
    }
    
    /**
     * Show all user orders.
     *
     * @return \Illuminate\View\View
     */
    public function orderHistory()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('customer.orders', compact('orders'));
    }
    
    /**
     * Show a specific order.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function showOrder($id)
    {
        $user = Auth::user();
        $order = Order::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();
            
        return view('customer.order-details', compact('order'));
    }
    
    /**
     * Cancel a customer order.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancelOrder($id)
    {
        $user = Auth::user();
        $order = Order::where('id', $id)
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->firstOrFail();
            
        $order->status = 'cancelled';
        $order->save();
        
        // Return items to inventory
        foreach ($order->orderItems as $item) {
            if ($product = Product::find($item->product_id)) {
                if ($product->inventory) {
                    $product->inventory->increment('quantity', $item->quantity);
                }
            }
        }
        
        return redirect()->route('customer.orders')
            ->with('success', 'Order #' . $order->id . ' has been cancelled.');
    }
    
    /**
     * Show user profile page.
     *
     * @return \Illuminate\View\View
     */
    public function profile()
    {
        $user = Auth::user();
        
        return view('customer.profile', compact('user'));
    }
    
    /**
     * Update user profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        
        $user->name = $request->name;
        $user->email = $request->email;
        
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }
        
        $user->save();
        
        return redirect()->route('customer.profile')
            ->with('success', 'Profile updated successfully');
    }
}
