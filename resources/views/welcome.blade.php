<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADUeats - POS System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
            <style>
        body {
            background-color: #f8f9fc;
            font-family: 'Nunito', sans-serif;
        }
        .hero {
            background-color: #4e73df;
            color: white;
            padding: 80px 0;
        }
        .feature-icon {
            font-size: 3rem;
            color: #4e73df;
            margin-bottom: 15px;
        }
        .section {
            padding: 60px 0;
        }
        .footer {
            background-color: #212529;
            color: rgba(255, 255, 255, 0.8);
            padding: 30px 0;
        }
            </style>
    </head>
<body>
    <div class="hero">
        <div class="container text-center">
            <h1 class="display-4 mb-3">ADUeats POS System</h1>
            <p class="lead mb-4">Streamlining transactions, reducing wait times, and optimizing operations with data analytics</p>
            <a href="{{ route('dashboard') }}" class="btn btn-light btn-lg px-4">Go to Dashboard</a>
        </div>
    </div>
    
    <div class="section">
        <div class="container">
            <div class="row text-center">
                <div class="col-12 mb-5">
                    <h2>Key Features</h2>
                    <p class="text-muted">Our powerful system enhances the Adamson University canteen experience</p>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="p-4 bg-white rounded shadow-sm">
                        <div class="feature-icon">
                            <i class="fas fa-cash-register"></i>
                        </div>
                        <h4>Fast POS System</h4>
                        <p class="text-muted">Streamlined order processing for reduced wait times</p>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="p-4 bg-white rounded shadow-sm">
                        <div class="feature-icon">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <h4>Inventory Management</h4>
                        <p class="text-muted">Real-time tracking and automated stock level alerts</p>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="p-4 bg-white rounded shadow-sm">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4>Data Analytics</h4>
                        <p class="text-muted">Insights on sales trends, customer patterns and peak hours</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="footer">
        <div class="container text-center">
            <p>&copy; 2025 ADUeats POS System - Adamson University</p>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Redirect to dashboard after 3 seconds
        setTimeout(function() {
            window.location.href = "{{ route('dashboard') }}";
        }, 3000);
    </script>
    </body>
</html>
