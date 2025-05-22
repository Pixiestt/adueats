@extends('layouts.app')

@section('title', 'Place Order')
@section('header', 'Place an Order')

@section('styles')
<style>
    .product-card {
        cursor: pointer;
        transition: all 0.3s;
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    .category-pills {
        overflow-x: auto;
        white-space: nowrap;
        padding: 10px 0;
    }
    .category-pill {
        display: inline-block;
        padding: 8px 15px;
        margin: 0 5px;
        border-radius: 20px;
        background-color: #f8f9fa;
        cursor: pointer;
        transition: all 0.3s;
    }
    .category-pill.active {
        background-color: #0d6efd;
        color: white;
    }
    .order-summary {
        position: sticky;
        top: 20px;
    }
    .cart-item {
        padding: 10px;
        border-bottom: 1px solid #e9ecef;
    }
    .cart-item:last-child {
        border-bottom: none;
    }
    .quantity-control {
        width: 80px;
    }
    #product-grid {
        max-height: 600px;
        overflow-y: auto;
    }
    #cart-items {
        max-height: 300px;
        overflow-y: auto;
    }
</style>
@endsection

@section('content')
<div class="row">
    <!-- Products Section (Left side) -->
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Menu Items</h6>
            </div>
            <div class="card-body">
                <!-- Search Bar -->
                <div class="mb-3">
                    <div class="input-group">
                        <input type="text" id="search-product" class="form-control" placeholder="Search for products...">
                        <button class="btn btn-primary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Category Pills -->
                <div class="category-pills mb-3">
                    <div class="category-pill active" data-category="all">All</div>
                    @foreach($categories as $category)
                        <div class="category-pill" data-category="{{ $category->id }}">{{ $category->name }}</div>
                    @endforeach
                </div>
                
                <!-- Product Grid -->
                <div id="product-grid" class="row">
                    @foreach($products as $product)
                        <div class="col-md-4 mb-4 product-item" data-category="{{ $product->category_id }}">
                            <div class="card product-card" data-id="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->price }}">
                                <div class="card-body text-center">
                                    @if($product->image)
                                        <img src="{{ asset('storage/products/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid mb-2" style="height: 100px; object-fit: cover;">
                                    @else
                                        <div class="bg-light mb-2 d-flex align-items-center justify-content-center" style="height: 100px;">
                                            <i class="fas fa-utensils fa-2x text-secondary"></i>
                                        </div>
                                    @endif
                                    <h6 class="card-title">{{ $product->name }}</h6>
                                    <p class="card-text text-primary">₱{{ number_format($product->price, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    
    <!-- Order Summary (Right side) -->
    <div class="col-lg-4">
        <div class="card shadow order-summary">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Order Summary</h6>
            </div>
            <div class="card-body">
                <!-- Cart Items -->
                <div id="cart-items" class="mb-3">
                    <p class="text-center text-muted">No items in cart</p>
                </div>
                
                <!-- Order Totals -->
                <div class="totals">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span id="subtotal">₱0.00</span>
                    </div>
                    <div class="d-flex justify-content-between border-top pt-2 mb-2">
                        <strong>Total:</strong>
                        <strong id="total">₱0.00</strong>
                    </div>
                </div>
                
                <!-- Customer Notes -->
                <div class="mb-3">
                    <label for="order-notes" class="form-label">Special Instructions (Optional)</label>
                    <textarea class="form-control" id="order-notes" rows="2"></textarea>
                </div>
                
                <!-- Payment Method -->
                <div class="mb-3">
                    <label class="form-label">Payment Method</label>
                    <div class="payment-methods">
                        <button class="btn btn-outline-primary payment-method" data-method="cash">
                            <i class="fas fa-money-bill-wave me-2"></i>Cash
                        </button>
                        <button class="btn btn-outline-primary payment-method" data-method="e-wallet">
                            <i class="fas fa-wallet me-2"></i>E-Wallet
                        </button>
                    </div>
                </div>
                
                <!-- Checkout Button -->
                <button id="checkout-btn" class="btn btn-success btn-block w-100" disabled>
                    <i class="fas fa-check-circle me-2"></i>Place Order
                </button>
                
                <!-- Cancel Button -->
                <button id="cancel-order-btn" class="btn btn-danger btn-block w-100 mt-2">
                    <i class="fas fa-times-circle me-2"></i>Cancel Order
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Order Confirmation Modal -->
<div class="modal fade" id="orderConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Order Confirmation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="fas fa-check-circle text-success fa-4x mb-3"></i>
                    <h4>Your order has been placed!</h4>
                    <p class="mb-1">Order #: <span id="order-number"></span></p>
                    <p class="mb-1">Total: <span id="order-total"></span></p>
                    <p class="mb-3">Status: <span class="badge bg-warning text-dark">Pending</span></p>
                    <p>We'll notify you when your order is ready for pickup.</p>
                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ route('customer.orders') }}" class="btn btn-primary">
                    <i class="fas fa-list me-2"></i>View My Orders
                </a>
                <a href="{{ route('customer.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-home me-2"></i>Go to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        let cart = [];
        let selectedPaymentMethod = '';
        
        // Add product to cart
        $('.product-card').on('click', function() {
            const productId = $(this).data('id');
            const productName = $(this).data('name');
            const productPrice = parseFloat($(this).data('price'));
            
            // Check if product already in cart
            const existingItem = cart.find(item => item.id === productId);
            
            if (existingItem) {
                existingItem.quantity++;
                existingItem.subtotal = existingItem.quantity * existingItem.price;
            } else {
                cart.push({
                    id: productId,
                    name: productName,
                    price: productPrice,
                    quantity: 1,
                    subtotal: productPrice
                });
            }
            
            updateCart();
        });
        
        // Filter products by category
        $('.category-pill').on('click', function() {
            const category = $(this).data('category');
            
            // Update active state
            $('.category-pill').removeClass('active');
            $(this).addClass('active');
            
            // Filter products
            if (category === 'all') {
                $('.product-item').show();
            } else {
                $('.product-item').hide();
                $(`.product-item[data-category="${category}"]`).show();
            }
        });
        
        // Search products
        $('#search-product').on('keyup', function() {
            const searchTerm = $(this).val().toLowerCase();
            
            $('.product-item').each(function() {
                const productName = $(this).find('.card-title').text().toLowerCase();
                if (productName.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
        
        // Select payment method
        $('.payment-method').on('click', function() {
            $('.payment-method').removeClass('btn-primary').addClass('btn-outline-primary');
            $(this).removeClass('btn-outline-primary').addClass('btn-primary');
            selectedPaymentMethod = $(this).data('method');
            updateCheckoutButton();
        });
        
        // Checkout
        $('#checkout-btn').on('click', function() {
            if (!selectedPaymentMethod) {
                alert('Please select a payment method');
                return;
            }
            
            // Prepare order data
            const orderData = {
                notes: $('#order-notes').val(),
                payment_method: selectedPaymentMethod,
                items: cart.map(item => ({
                    product_id: item.id,
                    quantity: item.quantity
                }))
            };
            
            // Send AJAX request
            $.ajax({
                url: "{{ route('customer.order.store') }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    ...orderData
                },
                success: function(response) {
                    // Display success modal
                    $('#order-number').text(response.order_id);
                    $('#order-total').text(`₱${parseFloat(response.total).toFixed(2)}`);
                    $('#orderConfirmModal').modal('show');
                    
                    // Reset cart
                    resetCart();
                },
                error: function(xhr) {
                    alert('Error processing order: ' + xhr.responseJSON.message);
                }
            });
        });
        
        // Cancel order
        $('#cancel-order-btn').on('click', function() {
            if (confirm('Are you sure you want to cancel this order?')) {
                resetCart();
            }
        });
        
        // Helper function to update cart display
        function updateCart() {
            const cartItemsContainer = $('#cart-items');
            let cartHtml = '';
            let subtotal = 0;
            
            if (cart.length === 0) {
                cartHtml = '<p class="text-center text-muted">No items in cart</p>';
            } else {
                cart.forEach((item, index) => {
                    subtotal += item.subtotal;
                    cartHtml += `
                        <div class="cart-item">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">${item.name}</h6>
                                <button class="btn btn-sm btn-outline-danger remove-item" data-index="${index}">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="input-group quantity-control">
                                    <button class="btn btn-sm btn-outline-secondary decrease-qty" data-index="${index}">-</button>
                                    <input type="text" class="form-control form-control-sm text-center item-qty" value="${item.quantity}" readonly>
                                    <button class="btn btn-sm btn-outline-secondary increase-qty" data-index="${index}">+</button>
                                </div>
                                <div class="text-right">
                                    <small>₱${item.price.toFixed(2)} × ${item.quantity}</small>
                                    <p class="mb-0">₱${item.subtotal.toFixed(2)}</p>
                                </div>
                            </div>
                        </div>
                    `;
                });
            }
            
            cartItemsContainer.html(cartHtml);
            $('#subtotal').text(`₱${subtotal.toFixed(2)}`);
            $('#total').text(`₱${subtotal.toFixed(2)}`);
            
            // Event handlers for quantity buttons
            $('.increase-qty').on('click', function() {
                const index = $(this).data('index');
                cart[index].quantity++;
                cart[index].subtotal = cart[index].quantity * cart[index].price;
                updateCart();
            });
            
            $('.decrease-qty').on('click', function() {
                const index = $(this).data('index');
                if (cart[index].quantity > 1) {
                    cart[index].quantity--;
                    cart[index].subtotal = cart[index].quantity * cart[index].price;
                    updateCart();
                }
            });
            
            $('.remove-item').on('click', function() {
                const index = $(this).data('index');
                cart.splice(index, 1);
                updateCart();
            });
            
            updateCheckoutButton();
        }
        
        // Helper function to update checkout button state
        function updateCheckoutButton() {
            if (cart.length > 0 && selectedPaymentMethod) {
                $('#checkout-btn').prop('disabled', false);
            } else {
                $('#checkout-btn').prop('disabled', true);
            }
        }
        
        // Helper function to reset cart
        function resetCart() {
            cart = [];
            selectedPaymentMethod = '';
            $('.payment-method').removeClass('btn-primary').addClass('btn-outline-primary');
            $('#order-notes').val('');
            updateCart();
        }
        
        // Check for products in URL (from featured products)
        const urlParams = new URLSearchParams(window.location.search);
        const productId = urlParams.get('product');
        
        if (productId) {
            const productCard = $(`.product-card[data-id="${productId}"]`);
            if (productCard.length) {
                productCard.click();
            }
        }
    });
</script>
@endsection