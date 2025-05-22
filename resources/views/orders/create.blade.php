@extends('layouts.app')

@section('title', 'Create Order')
@section('header', 'Create New Order')

@section('content')
<div class="mb-4">
    <a href="{{ route('orders.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Orders
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Order Form</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('orders.store') }}" method="POST" id="orderForm">
            @csrf
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="customer_name" class="form-label">Customer Name (Optional)</label>
                        <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                            id="customer_name" name="customer_name" value="{{ old('customer_name') }}">
                        @error('customer_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required>
                            <option value="">Select Payment Method</option>
                            <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="e-wallet" {{ old('payment_method') == 'e-wallet' ? 'selected' : '' }}>E-Wallet</option>
                        </select>
                        @error('payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="mb-4">
                <label for="notes" class="form-label">Notes (Optional)</label>
                <textarea class="form-control @error('notes') is-invalid @enderror" 
                    id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <hr class="my-4">
            <h5 class="mb-3">Order Items</h5>

            <div id="orderItemsContainer">
                <div class="row order-item-row mb-3">
                    <div class="col-md-5">
                        <label for="items[0][product_id]" class="form-label">Product</label>
                        <select class="form-select product-select @error('items.0.product_id') is-invalid @enderror" 
                            name="items[0][product_id]" required>
                            <option value="" selected disabled>Select a product</option>
                            @php
                                $categories = \App\Models\Category::with(['products' => function($query) {
                                    $query->where('available', true);
                                }])->get();
                            @endphp
                            
                            @foreach($categories as $category)
                                @if($category->products->count() > 0)
                                    <optgroup label="{{ $category->name }}">
                                        @foreach($category->products as $product)
                                            <option value="{{ $product->id }}" 
                                                data-price="{{ $product->price }}">
                                                {{ $product->name }} - ₱{{ number_format($product->price, 2) }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            @endforeach
                        </select>
                        @error('items.0.product_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="items[0][quantity]" class="form-label">Quantity</label>
                        <input type="number" class="form-control quantity-input @error('items.0.quantity') is-invalid @enderror" 
                            name="items[0][quantity]" value="1" min="1" required>
                        @error('items.0.quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Subtotal</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="text" class="form-control subtotal" readonly value="0.00">
                        </div>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-danger remove-item-btn mb-2" disabled>
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between mb-4">
                <button type="button" id="addItemBtn" class="btn btn-success">
                    <i class="fas fa-plus-circle me-2"></i>Add Item
                </button>
                <div class="d-flex align-items-center">
                    <h5 class="mb-0 me-3">Total: ₱<span id="orderTotal">0.00</span></h5>
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save me-2"></i>Create Order
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let itemCounter = 0;
        
        // Add item button
        document.getElementById('addItemBtn').addEventListener('click', function() {
            itemCounter++;
            
            const container = document.getElementById('orderItemsContainer');
            const newRow = document.createElement('div');
            newRow.className = 'row order-item-row mb-3';
            newRow.innerHTML = `
                <div class="col-md-5">
                    <label for="items[${itemCounter}][product_id]" class="form-label">Product</label>
                    <select class="form-select product-select @error('items.${itemCounter}.product_id') is-invalid @enderror" 
                        name="items[${itemCounter}][product_id]" required>
                        <option value="" selected disabled>Select a product</option>
                        @foreach($categories as $category)
                            @if($category->products->count() > 0)
                                <optgroup label="{{ $category->name }}">
                                    @foreach($category->products as $product)
                                        <option value="{{ $product->id }}" 
                                            data-price="{{ $product->price }}">
                                            {{ $product->name }} - ₱{{ number_format($product->price, 2) }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="items[${itemCounter}][quantity]" class="form-label">Quantity</label>
                    <input type="number" class="form-control quantity-input" 
                        name="items[${itemCounter}][quantity]" value="1" min="1" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Subtotal</label>
                    <div class="input-group">
                        <span class="input-group-text">₱</span>
                        <input type="text" class="form-control subtotal" readonly value="0.00">
                    </div>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-danger remove-item-btn mb-2">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            
            container.appendChild(newRow);
            setupEventListeners();
            updateRemoveButtons();
        });
        
        // Calculate subtotal and update totals
        function calculateSubtotal(row) {
            const productSelect = row.querySelector('.product-select');
            const quantityInput = row.querySelector('.quantity-input');
            const subtotalInput = row.querySelector('.subtotal');
            
            if (productSelect.selectedIndex > 0) {
                const option = productSelect.options[productSelect.selectedIndex];
                const price = parseFloat(option.getAttribute('data-price'));
                const quantity = parseInt(quantityInput.value);
                
                if (!isNaN(price) && !isNaN(quantity) && quantity > 0) {
                    const subtotal = price * quantity;
                    subtotalInput.value = subtotal.toFixed(2);
                    updateOrderTotal();
                }
            } else {
                subtotalInput.value = '0.00';
                updateOrderTotal();
            }
        }
        
        // Update order total
        function updateOrderTotal() {
            const subtotals = document.querySelectorAll('.subtotal');
            let total = 0;
            
            subtotals.forEach(function(input) {
                total += parseFloat(input.value || 0);
            });
            
            document.getElementById('orderTotal').textContent = total.toFixed(2);
        }
        
        // Handle remove item button click
        function handleRemoveItem(e) {
            if (e.target.classList.contains('remove-item-btn') || e.target.closest('.remove-item-btn')) {
                const button = e.target.classList.contains('remove-item-btn') ? e.target : e.target.closest('.remove-item-btn');
                const row = button.closest('.order-item-row');
                row.remove();
                updateRemoveButtons();
                updateOrderTotal();
            }
        }
        
        // Update remove buttons (disable if only one row)
        function updateRemoveButtons() {
            const rows = document.querySelectorAll('.order-item-row');
            const buttons = document.querySelectorAll('.remove-item-btn');
            
            buttons.forEach(function(button) {
                button.disabled = rows.length <= 1;
            });
        }
        
        // Set up event listeners for a row
        function setupEventListeners() {
            document.querySelectorAll('.product-select').forEach(function(select) {
                select.addEventListener('change', function() {
                    calculateSubtotal(this.closest('.order-item-row'));
                });
            });
            
            document.querySelectorAll('.quantity-input').forEach(function(input) {
                input.addEventListener('input', function() {
                    calculateSubtotal(this.closest('.order-item-row'));
                });
            });
            
            document.getElementById('orderItemsContainer').addEventListener('click', handleRemoveItem);
        }
        
        // Form validation
        document.getElementById('orderForm').addEventListener('submit', function(e) {
            const productSelects = document.querySelectorAll('.product-select');
            let valid = false;
            
            productSelects.forEach(function(select) {
                if (select.value) {
                    valid = true;
                }
            });
            
            if (!valid) {
                e.preventDefault();
                alert('Please add at least one product to the order.');
            }
        });
        
        // Initialize
        setupEventListeners();
        updateRemoveButtons();
    });
</script>
@endsection