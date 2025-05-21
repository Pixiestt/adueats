@extends('layouts.app')

@section('title', 'Sales Analytics')
@section('header', 'Sales Analytics')

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
    <!-- Daily Sales Chart -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Daily Sales (Last 30 Days)</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                        <a class="dropdown-item" href="#" id="download-sales-chart">Download Chart</a>
                        <a class="dropdown-item" href="#" id="print-sales-chart">Print Chart</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="dailySalesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales by Payment Method -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Sales by Payment Method</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="salesByPaymentChart"></canvas>
                </div>
                <div class="mt-4 text-center small">
                    <span class="me-2">
                        <i class="fas fa-circle text-primary"></i> Cash
                    </span>
                    <span class="me-2">
                        <i class="fas fa-circle text-success"></i> Card
                    </span>
                    <span class="me-2">
                        <i class="fas fa-circle text-info"></i> E-Wallet
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sales Summary -->
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Sales Summary</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Today's Sales -->
                    <div class="col-md-3 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Today's Sales</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($todaySales, 2) }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- This Week's Sales -->
                    <div class="col-md-3 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            This Week's Sales</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($weeklySales, 2) }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-calendar-week fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- This Month's Sales -->
                    <div class="col-md-3 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            This Month's Sales</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($monthlySales, 2) }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Average Order Value -->
                    <div class="col-md-3 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Avg. Order Value</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($avgOrderValue, 2) }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
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
        // Sales data from controller
        const dailySalesData = @json($dailySales);
        const salesByPaymentData = @json($salesByPayment);
        
        // Daily Sales Chart
        const dailySalesCtx = document.getElementById('dailySalesChart').getContext('2d');
        const dailySalesChart = new Chart(dailySalesCtx, {
            type: 'line',
            data: {
                labels: dailySalesData.map(item => item.date),
                datasets: [{
                    label: 'Daily Sales',
                    data: dailySalesData.map(item => item.total),
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    pointRadius: 3,
                    pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointBorderColor: 'rgba(78, 115, 223, 1)',
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    lineTension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxTicksLimit: 7
                        }
                    },
                    y: {
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10,
                            callback: function(value) {
                                return '₱' + value;
                            }
                        },
                        grid: {
                            color: "rgb(234, 236, 244)",
                            borderColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: "rgb(255, 255, 255)",
                        bodyColor: "#858796",
                        titleMarginBottom: 10,
                        titleColor: '#6e707e',
                        titleFontSize: 14,
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        caretPadding: 10,
                        callbacks: {
                            label: function(context) {
                                return 'Sales: ₱' + context.raw;
                            }
                        }
                    }
                }
            }
        });
        
        // Sales by Payment Method Chart
        const paymentLabels = salesByPaymentData.map(item => 
            item.payment_method.charAt(0).toUpperCase() + item.payment_method.slice(1)
        );
        const paymentValues = salesByPaymentData.map(item => item.total);
        const paymentColors = ['#4e73df', '#1cc88a', '#36b9cc'];
        
        const salesByPaymentCtx = document.getElementById('salesByPaymentChart').getContext('2d');
        const salesByPaymentChart = new Chart(salesByPaymentCtx, {
            type: 'doughnut',
            data: {
                labels: paymentLabels,
                datasets: [{
                    data: paymentValues,
                    backgroundColor: paymentColors,
                    hoverBackgroundColor: paymentColors.map(color => color + 'dd'),
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: "rgb(255, 255, 255)",
                        bodyColor: "#858796",
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        caretPadding: 10,
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ₱' + context.raw;
                            }
                        }
                    }
                },
                cutout: '70%'
            }
        });
        
        // Download chart as image
        $('#download-sales-chart').on('click', function() {
            const link = document.createElement('a');
            link.download = 'daily-sales-chart.png';
            link.href = dailySalesChart.toBase64Image();
            link.click();
        });
        
        // Print chart
        $('#print-sales-chart').on('click', function() {
            const canvas = document.getElementById('dailySalesChart');
            const dataUrl = canvas.toDataURL();
            
            const windowContent = `
                <html>
                <head>
                    <title>Daily Sales Chart</title>
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
                    <h1>Daily Sales Chart</h1>
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