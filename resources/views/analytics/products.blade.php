@extends('layouts.app')

@section('title', 'Product Analytics')
@section('header', 'Product Analytics')

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
    <!-- Top Selling Products Chart -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Top Selling Products</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                        <a class="dropdown-item" href="#" id="download-products-chart">Download Chart</a>
                        <a class="dropdown-item" href="#" id="print-products-chart">Print Chart</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-bar">
                    <canvas id="topProductsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Products by Category -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Products by Category</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4">
                    <canvas id="productsByCategoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Product Statistics -->
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Product Statistics</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Total Products -->
                    <div class="col-md-3 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Products</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalProducts }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-box fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Active Products -->
                    <div class="col-md-3 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Products</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeProducts }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Low Stock Products -->
                    <div class="col-md-3 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Low Stock Products</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $lowStockProducts }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Categories -->
                    <div class="col-md-3 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Categories</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalCategories }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-tags fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Top Products Chart
        const topProductsData = @json($topProducts);
        const productsByCategoryData = @json($productsByCategory);
        
        const topProductsCtx = document.getElementById('topProductsChart').getContext('2d');
        const topProductsChart = new Chart(topProductsCtx, {
            type: 'bar',
            data: {
                labels: topProductsData.map(item => item.name),
                datasets: [{
                    label: 'Units Sold',
                    data: topProductsData.map(item => item.total_sold),
                    backgroundColor: 'rgba(78, 115, 223, 0.8)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Products by Category Chart
        const categoryColors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'];
        const productsByCategoryCtx = document.getElementById('productsByCategoryChart').getContext('2d');
        const productsByCategoryChart = new Chart(productsByCategoryCtx, {
            type: 'doughnut',
            data: {
                labels: productsByCategoryData.map(item => item.category),
                datasets: [{
                    data: productsByCategoryData.map(item => item.count),
                    backgroundColor: categoryColors.slice(0, productsByCategoryData.length),
                    hoverBackgroundColor: categoryColors.slice(0, productsByCategoryData.length),
                    hoverBorderColor: "rgba(234, 236, 244, 1)"
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                },
                cutout: '70%'
            }
        });

        // Download chart as image
        $('#download-products-chart').on('click', function() {
            const link = document.createElement('a');
            link.download = 'top-products-chart.png';
            link.href = topProductsChart.toBase64Image();
            link.click();
        });
        
        // Print chart
        $('#print-products-chart').on('click', function() {
            const canvas = document.getElementById('topProductsChart');
            const dataUrl = canvas.toDataURL();
            
            const windowContent = `
                <html>
                <head>
                    <title>Top Products Chart</title>
                    <style>
                        body { 
                            text-align: center;
                            font-family: Arial, sans-serif;
                        }
                        img { max-width: 100%; }
                        h1 { margin-bottom: 20px; }
                    </style>
                </head>
                <body>
                    <h1>Top Products Chart</h1>
                    <img src="${dataUrl}">
                </body>
                </html>
            `;
            
            const printWindow = window.open('', '_blank');
            printWindow.document.write(windowContent);
            printWindow.document.close();
            printWindow.onload = function() {
                printWindow.print();
                printWindow.close();
            };
        });
    });
</script>
@endsection