@extends('layouts.app')

@section('title', 'Order Details')
@section('header', 'Order Details')

@section('content')
<div class="mb-4">
    <a href="{{ route('orders.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Orders
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Order #{{ $order->id }}</h6>
                <span class="badge 
                    @if($order->status == 'pending') bg-warning text-dark
                    @elseif($order->status == 'completed') bg-success
                    @else bg-danger @endif">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->product_name }}</td>
                                <td>₱{{ number_format($item->unit_price, 2) }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>₱{{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total:</th>
                                <th>₱{{ number_format($order->total_amount, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Order Information</h6>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Customer:</span>
                        <span>{{ $order->customer_name ?? 'Walk-in Customer' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Order Date:</span>
                        <span>{{ $order->created_at->format('M d, Y H:i') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Payment Method:</span>
                        <span>{{ ucfirst($order->payment_method) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Status:</span>
                        <span class="badge 
                            @if($order->status == 'pending') bg-warning text-dark
                            @elseif($order->status == 'completed') bg-success
                            @else bg-danger @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </li>
                    @if($order->notes)
                    <li class="list-group-item">
                        <span class="fw-bold">Notes:</span>
                        <p class="mt-2">{{ $order->notes }}</p>
                    </li>
                    @endif
                </ul>
                
                @if($order->status == 'pending')
                <div class="mt-3">
                    <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-edit me-2"></i>Edit Order
                    </a>
                    <form action="{{ route('orders.destroy', $order->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Are you sure you want to delete this order?')">
                            <i class="fas fa-trash me-2"></i>Delete Order
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 