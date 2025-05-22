<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Category; // Add this line
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Inventory::with('product.category');
        
        if ($request->has('search') && !empty($request->search)) {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('category') && !empty($request->category)) {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('category_id', $request->category);
            });
        }

        // Get low stock items for the alert
        $lowStockItems = Inventory::with('product')
            ->whereRaw('quantity <= minimum_stock')
            ->get();

        if ($request->has('low_stock')) {
            $query->whereRaw('quantity <= minimum_stock');
        }
        
        $inventory = $query->orderBy('quantity', 'asc')->paginate(10);
        $categories = Category::all();
        
        return view('inventory.index', compact('inventory', 'categories', 'lowStockItems'));
    }

    public function restock(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:inventory,id',
            'items.*.quantity' => 'required|integer|min:1'
        ]);

        foreach ($request->items as $item) {
            $inventory = Inventory::findOrFail($item['id']);
            $inventory->increment('quantity', $item['quantity']);
            $inventory->update(['last_restock_date' => now()]);
        }

        return redirect()->back()
            ->with('success', 'Items restocked successfully');
    }

    public function updateStock(Request $request, $id)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0'
        ]);

        $inventory = Inventory::findOrFail($id);
        $inventory->update([
            'quantity' => $validated['quantity'],
            'minimum_stock' => $validated['minimum_stock']
        ]);

        return redirect()->back()
            ->with('success', 'Inventory updated successfully');
    }

    public function edit($id)
    {
        $inventory = Inventory::with(['product', 'product.category'])->findOrFail($id);
        $products = Product::all();
        return view('inventory.edit', compact('inventory', 'products'));
    }
}
