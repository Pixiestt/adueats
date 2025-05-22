@extends('layouts.app')

@section('title', 'Inventory Analytics')
@section('header', 'Inventory Analytics')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <div class="btn-group" role="group">
            <a href="{{ route('analytics.sales') }}" class="btn {{ request()->is('analytics/sales') ? 'btn-primary' : 'btn-outline-primary' }}">Sales Analytics</a>
            <a href="{{ route('analytics.products') }}" class="btn {{ request()->is('analytics/products') ? 'btn-primary' : 'btn-outline-primary' }}">Product Analytics</a>
            <a href="{{ route('analytics.inventory') }}" class="btn {{ request()->is('analytics/inventory') ? 'btn-primary' : 'btn-outline-primary' }}">Inventory Analytics</a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Low Stock Items -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Low Stock Items</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Current Stock</th>
                                <th>Minimum Stock</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($lowStockItems as $item)
                            <tr>
                                <td>{{ $item->product->name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ $item->minimum_stock }}</td>
                                <td>
                                    <span class="badge bg-danger">Low Stock</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">No low stock items found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Value by Category -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Inventory Value by Category</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4">
                    <canvas id="inventoryValueChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Inventory Value by Category Chart
    var ctx = document.getElementById('inventoryValueChart').getContext('2d');
    var inventoryValueChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($inventoryByCategory->pluck('category_name')) !!},
            datasets: [{
                data: {!! json_encode($inventoryByCategory->pluck('total_value')) !!},
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
            }]
        },
        options: {
            maintainAspectRatio: false,
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        var value = data.datasets[0].data[tooltipItem.index];
                        return data.labels[tooltipItem.index] + ': ₱' + value.toFixed(2);
                    }
                }
            }
        }
    });
</script>
@endsection