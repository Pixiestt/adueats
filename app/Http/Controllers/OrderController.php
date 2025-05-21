<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Category;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::orderBy('created_at', 'desc')->paginate(10);
        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('orders.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'payment_method' => 'required|in:cash,card,e-wallet',
            'notes' => 'nullable|string',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Create the order
            $order = Order::create([
                'user_id' => auth()->id(),
                'customer_name' => $validated['customer_name'],
                'total_amount' => 0, // Will be updated after adding items
                'payment_method' => $validated['payment_method'],
                'status' => 'pending',
                'notes' => $validated['notes'],
            ]);
            
            $totalAmount = 0;
            
            // Add order items
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $subtotal = $product->price * $item['quantity'];
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'subtotal' => $subtotal,
                ]);
                
                // Update inventory
                $inventory = Inventory::where('product_id', $product->id)->first();
                if ($inventory) {
                    $inventory->quantity -= $item['quantity'];
                    $inventory->save();
                }
                
                $totalAmount += $subtotal;
            }
            
            // Update order total
            $order->total_amount = $totalAmount;
            $order->status = 'completed';
            $order->save();
            
            DB::commit();
            
            return redirect()->route('orders.show', $order->id)
                ->with('success', 'Order created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create order: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = Order::with('items')->findOrFail($id);
        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $order = Order::with('items')->findOrFail($id);
        return view('orders.edit', compact('order'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'payment_method' => 'required|in:cash,card,e-wallet',
            'status' => 'required|in:pending,completed,cancelled',
            'notes' => 'nullable|string',
        ]);
        
        $order = Order::findOrFail($id);
        $order->update($validated);
        
        return redirect()->route('orders.show', $order->id)
            ->with('success', 'Order updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $order = Order::findOrFail($id);
        
        // If order is already completed, don't allow deletion
        if ($order->status === 'completed') {
            return back()->withErrors(['error' => 'Cannot delete a completed order.']);
        }
        
        $order->delete();
        
        return redirect()->route('orders.index')
            ->with('success', 'Order deleted successfully.');
    }
    
    /**
     * Show the POS interface.
     */
    public function createPOS()
    {
        $categories = Category::with('products')->get();
        $products = Product::where('available', true)->get();
        
        return view('pos.index', compact('categories', 'products'));
    }
    
    /**
     * Process a POS order.
     */
    public function storePOS(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'payment_method' => 'required|in:cash,card,e-wallet',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Create the order
            $order = Order::create([
                'user_id' => auth()->id(),
                'customer_name' => $validated['customer_name'],
                'total_amount' => 0,
                'payment_method' => $validated['payment_method'],
                'status' => 'completed', // POS orders are completed immediately
                'notes' => $request->notes,
            ]);
            
            $totalAmount = 0;
            
            // Add order items
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $subtotal = $product->price * $item['quantity'];
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'subtotal' => $subtotal,
                ]);
                
                // Update inventory
                $inventory = Inventory::where('product_id', $product->id)->first();
                if ($inventory) {
                    $inventory->quantity -= $item['quantity'];
                    $inventory->save();
                }
                
                $totalAmount += $subtotal;
            }
            
            // Update order total
            $order->total_amount = $totalAmount;
            $order->save();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'total' => $totalAmount,
                'message' => 'Order completed successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to process order: ' . $e->getMessage()
            ], 500);
        }
    }
}
