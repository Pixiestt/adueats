@extends('layouts.app')

@section('title', 'Order Details')
@section('header', 'Order #' . $order->id)

@section('content')
<div class="row">
    <!-- Order Details -->
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Order Items</h6>
                <a href="{{ route('customer.orders') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back to Orders
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th width="10%">Quantity</th>
                                <th width="15%">Unit Price</th>
                                <th width="15%">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->orderItems as $item)
                            <tr>
                                <td>{{ $item->product_name }}</td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-end">₱{{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-end">₱{{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total:</th>
                                <th class="text-end">₱{{ number_format($order->total_amount, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                @if($order->notes)
                <div class="mt-4">
                    <h6 class="font-weight-bold">Special Instructions:</h6>
                    <p class="p-3 bg-light rounded">{{ $order->notes }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Order Summary -->
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Order Summary</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <p class="mb-1"><strong>Order Number:</strong> #{{ $order->id }}</p>
                    <p class="mb-1"><strong>Date Ordered:</strong> {{ $order->created_at->format('M d, Y h:i A') }}</p>
                    <p class="mb-1">
                        <strong>Status:</strong> 
                        @if($order->status == 'pending')
                            <span class="badge bg-warning text-dark">Pending</span>
                        @elseif($order->status == 'completed')
                            <span class="badge bg-success">Completed</span>
                        @elseif($order->status == 'cancelled')
                            <span class="badge bg-danger">Cancelled</span>
                        @else
                            <span class="badge bg-secondary">{{ $order->status }}</span>
                        @endif
                    </p>
                </div>
                
                <hr>
                
                <div class="mb-3">
                    <p class="mb-1"><strong>Customer Name:</strong> {{ $order->customer_name }}</p>
                    <p class="mb-1">
                        <strong>Payment Method:</strong> 
                        @if($order->payment_method == 'cash')
                            <span class="badge bg-success">Cash</span>
                        @elseif($order->payment_method == 'card')
                            <span class="badge bg-primary">Card</span>
                        @else
                            <span class="badge bg-info">E-Wallet</span>
                        @endif
                    </p>
                </div>
                
                <hr>
                
                <div class="text-center mt-4">
                    <a href="{{ route('customer.order') }}" class="btn btn-primary btn-block w-100">
                        <i class="fas fa-utensils me-1"></i> Place New Order
                    </a>
                    
                    @if($order->status == 'pending')
                    <form action="{{ route('customer.orders.cancel', $order->id) }}" method="POST" class="mt-2">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-danger btn-block w-100" onclick="return confirm('Are you sure you want to cancel this order?')">
                            <i class="fas fa-times-circle me-1"></i> Cancel Order
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 