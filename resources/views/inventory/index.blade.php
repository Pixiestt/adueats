@extends('layouts.app')

@section('title', 'Inventory')
@section('header', 'Inventory Management')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#restockModal">
                <i class="fas fa-boxes me-2"></i>Restock Items
            </button>
            <a href="{{ route('inventory.index', ['low_stock' => 1]) }}" class="btn {{ request('low_stock') ? 'btn-danger' : 'btn-outline-danger' }}">
                <i class="fas fa-exclamation-triangle me-2"></i>Show Low Stock Only
            </a>
        </div>
    </div>
    <div class="col-md-6">
        <form action="{{ route('inventory.index') }}" method="GET" class="d-flex">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search products..." value="{{ request('search') }}">
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-outline-primary">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Inventory Status</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Current Stock</th>
                        <th>Min. Stock</th>
                        <th>Status</th>
                        <th>Last Restock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inventory as $item)
                    <tr class="{{ $item->quantity <= $item->minimum_stock ? 'table-danger' : '' }}">
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ $item->product->category->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->minimum_stock }}</td>
                        <td>
                            @if($item->quantity <= $item->minimum_stock)
                                <span class="badge bg-danger">Low Stock</span>
                            @elseif($item->quantity <= $item->minimum_stock * 2)
                                <span class="badge bg-warning text-dark">Medium Stock</span>
                            @else
                                <span class="badge bg-success">Well Stocked</span>
                            @endif
                        </td>
                        <td>{{ $item->last_restock_date ? $item->last_restock_date->format('M d, Y') : 'Never' }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-primary restock-btn" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#updateStockModal"
                                    data-id="{{ $item->id }}"
                                    data-product="{{ $item->product->name }}"
                                    data-current="{{ $item->quantity }}">
                                    <i class="fas fa-plus-circle"></i>
                                </button>
                                <a href="{{ route('inventory.edit', $item->id) }}" class="btn btn-sm btn-info ms-1">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">No inventory items found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-end mt-4">
            {{ $inventory->links() }}
        </div>
    </div>
</div>

<!-- Restock Multiple Modal -->
<div class="modal fade" id="restockModal" tabindex="-1" aria-labelledby="restockModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="restockModalLabel">Restock Multiple Items</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="restock-form" action="{{ route('inventory.restock') }}" method="POST">
                    @csrf
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Current Stock</th>
                                    <th>Add Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lowStockItems as $item)
                                <tr>
                                    <td>{{ $item->product->name }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>
                                        <input type="hidden" name="items[{{ $item->id }}][id]" value="{{ $item->id }}">
                                        <input type="number" name="items[{{ $item->id }}][quantity]" class="form-control" min="1" value="{{ $item->minimum_stock - $item->quantity + 10 }}">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="restock-form" class="btn btn-primary">Restock Items</button>
            </div>
        </div>
    </div>
</div>

<!-- Update Single Stock Modal -->
<div class="modal fade" id="updateStockModal" tabindex="-1" aria-labelledby="updateStockModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateStockModalLabel">Update Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="update-stock-form" action="{{ route('inventory.updateStock', ['id' => ':id']) }}" method="POST">
                    @csrf
                    <input type="hidden" name="inventory_id" id="inventory_id">
                    
                    <div class="mb-3">
                        <label for="product_name" class="form-label">Product</label>
                        <input type="text" class="form-control" id="product_name" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label for="current_stock" class="form-label">Current Stock</label>
                        <input type="number" class="form-control" id="current_stock" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label for="add_quantity" class="form-label">Add Quantity</label>
                        <input type="number" class="form-control" id="add_quantity" name="quantity" min="1" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="update-stock-form" class="btn btn-primary">Update Stock</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Handle update stock modal
        $('.restock-btn').on('click', function() {
            const id = $(this).data('id');
            const product = $(this).data('product');
            const current = $(this).data('current');
            
            $('#inventory_id').val(id);
            $('#product_name').val(product);
            $('#current_stock').val(current);
            $('#add_quantity').val(10); // Default value
            
            // Update form action with the correct ID
            const form = $('#update-stock-form');
            const action = form.attr('action').replace(':id', id);
            form.attr('action', action);
        });
    });
</script>
@endsection