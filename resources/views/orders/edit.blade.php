@extends('layouts.app')

@section('title', 'Edit Order')
@section('header', 'Edit Order #' . $order->id)

@section('content')
<div class="mb-4">
    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Order
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Order Items</h6>
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
                <h6 class="m-0 font-weight-bold text-primary">Edit Order Information</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('orders.update', $order->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="customer_name" class="form-label">Customer Name</label>
                        <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                            id="customer_name" name="customer_name" value="{{ old('customer_name', $order->customer_name) }}">
                        @error('customer_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required>
                            <option value="">Select Payment Method</option>
                            <option value="cash" {{ (old('payment_method', $order->payment_method) == 'cash') ? 'selected' : '' }}>Cash</option>
                            <option value="e-wallet" {{ (old('payment_method', $order->payment_method) == 'e-wallet') ? 'selected' : '' }}>E-Wallet</option>
                        </select>
                        @error('payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select @error('status') is-invalid @enderror" 
                            id="status" name="status">
                            <option value="pending" {{ (old('status', $order->status) == 'pending') ? 'selected' : '' }}>Pending</option>
                            <option value="completed" {{ (old('status', $order->status) == 'completed') ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ (old('status', $order->status) == 'cancelled') ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                            id="notes" name="notes" rows="3">{{ old('notes', $order->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection