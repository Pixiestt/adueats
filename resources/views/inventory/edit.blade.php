@extends('layouts.app')

@section('title', 'Edit Inventory')
@section('header', 'Edit Inventory Item')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Edit Inventory for {{ $inventory->product->name }}</h6>
                </div>
Route::post('/inventory/{id}/update-stock', [InventoryController::class, 'updateStock'])->name('inventory.updateStock');                <div class="card-body">
                    <form action="{{ route('inventory.updateStock', $inventory->id) }}" method="POST">
                        @csrf
                        @method('POST')
                        
                        <input type="hidden" name="product_id" value="{{ $inventory->product_id }}">
                        
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Current Stock</label>
                            <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                id="quantity" name="quantity" value="{{ old('quantity', $inventory->quantity) }}" min="0">
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="minimum_stock" class="form-label">Minimum Stock Level</label>
                            <input type="number" class="form-control @error('minimum_stock') is-invalid @enderror" 
                                id="minimum_stock" name="minimum_stock" value="{{ old('minimum_stock', $inventory->minimum_stock) }}" min="0">
                            @error('minimum_stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Inventory
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection