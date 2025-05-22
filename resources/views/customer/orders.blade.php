@extends('layouts.app')

@section('title', 'My Orders')
@section('header', 'Order History')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">My Orders</h6>
        <a href="{{ route('customer.order') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i> Place New Order
        </a>
    </div>
    <div class="card-body">
        @if($orders->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Payment Method</th>
                        <th>Status</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->created_at->format('M d, Y h:i A') }}</td>
                        <td>₱{{ number_format($order->total_amount, 2) }}</td>
                        <td>
                            @if($order->payment_method == 'cash')
                                <span class="badge bg-success">Cash</span>
                            @elseif($order->payment_method == 'e-wallet')
                                <span class="badge bg-info">E-Wallet</span>
                            @endif
                        </td>
                        <td>
                            @if($order->status == 'pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                            @elseif($order->status == 'completed')
                                <span class="badge bg-success">Completed</span>
                            @elseif($order->status == 'cancelled')
                                <span class="badge bg-danger">Cancelled</span>
                            @else
                                <span class="badge bg-secondary">{{ $order->status }}</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $orders->links() }}
        </div>
        @else
        <div class="text-center py-4">
            <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
            <h5>No orders yet</h5>
            <p class="text-muted">You haven't placed any orders yet.</p>
            <a href="{{ route('customer.order') }}" class="btn btn-primary mt-2">
                <i class="fas fa-utensils me-1"></i> Order Now
            </a>
        </div>
        @endif
    </div>
</div>
@endsection