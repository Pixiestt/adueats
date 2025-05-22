@extends('layouts.app')

@section('title', 'Orders')
@section('header', 'Orders')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <a href="{{ route('pos.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i>New Order
        </a>
    </div>
    <div class="col-md-6">
        <form action="{{ route('orders.index') }}" method="GET" class="d-flex">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search order #..." value="{{ request('search') }}">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                <button type="submit" class="btn btn-outline-primary">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">All Orders</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Total Amount</th>
                        <th>Items</th>
                        <th>Payment Method</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->customer_name ?? 'Walk-in Customer' }}</td>
                        <td>₱{{ number_format($order->total_amount, 2) }}</td>
                        <td>{{ $order->items->count() }}</td>
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
                            @else
                                <span class="badge bg-danger">Cancelled</span>
                            @endif
                        </td>
                        <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-info me-1">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($order->status == 'pending')
                                <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-sm btn-primary me-1">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('orders.destroy', $order->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this order?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">No orders found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-end mt-4">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection