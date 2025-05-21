<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADUeats - @yield('title', 'POS System')</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!-- Custom styles -->
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #212529;
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 5px;
        }
        .sidebar .nav-link:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .sidebar .nav-link.active {
            color: white;
            background-color: #0d6efd;
        }
        .main-content {
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
        }
        .brand-text {
            font-weight: bold;
            font-size: 1.5rem;
            color: #0d6efd;
        }
        .logo-container {
            padding: 20px 10px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 15px;
        }
        .user-dropdown {
            cursor: pointer;
        }
        .dropdown-menu {
            right: 0;
            left: auto;
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 px-0 sidebar">
                <div class="logo-container">
                    <span class="brand-text">ADUeats</span>
                </div>
                <ul class="nav flex-column">
                    @if(auth()->check() && auth()->user()->isAdmin())
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('dashboard*') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('pos*') ? 'active' : '' }}" href="{{ route('pos.create') }}">
                            <i class="fas fa-cash-register me-2"></i> POS
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('orders*') ? 'active' : '' }}" href="{{ route('orders.index') }}">
                            <i class="fas fa-shopping-cart me-2"></i> Orders
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('products*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                            <i class="fas fa-utensils me-2"></i> Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('categories*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                            <i class="fas fa-tags me-2"></i> Categories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('inventory*') ? 'active' : '' }}" href="{{ route('inventory.index') }}">
                            <i class="fas fa-boxes me-2"></i> Inventory
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('analytics*') ? 'active' : '' }}" href="{{ route('analytics.sales') }}">
                            <i class="fas fa-chart-line me-2"></i> Analytics
                        </a>
                    </li>
                    @elseif(auth()->check() && auth()->user()->isCustomer())
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('customer/dashboard*') ? 'active' : '' }}" href="{{ route('customer.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('customer/order*') ? 'active' : '' }}" href="{{ route('customer.order') }}">
                            <i class="fas fa-utensils me-2"></i> Order Food
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('customer/orders*') ? 'active' : '' }}" href="{{ route('customer.orders') }}">
                            <i class="fas fa-history me-2"></i> Order History
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('customer/profile*') ? 'active' : '' }}" href="{{ route('customer.profile') }}">
                            <i class="fas fa-user me-2"></i> My Profile
                        </a>
                    </li>
                    @endif
                </ul>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 px-0">
                <div class="header d-flex justify-content-between align-items-center">
                    <h1 class="h3">@yield('header', 'Dashboard')</h1>
                    <div class="d-flex align-items-center">
                        <span class="me-3">{{ date('F d, Y') }}</span>
                        @if(auth()->check())
                        <div class="dropdown user-dropdown">
                            <div class="d-flex align-items-center" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="me-2">{{ auth()->user()->name }}</span>
                                <i class="fas fa-user-circle fa-lg"></i>
                            </div>
                            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                                @if(auth()->user()->isAdmin())
                                <li><a class="dropdown-item" href="{{ route('dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                                @else
                                <li><a class="dropdown-item" href="{{ route('customer.profile') }}"><i class="fas fa-user me-2"></i>My Profile</a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="{{ route('logout') }}" 
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </div>
                        @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-sm">Login</a>
                        @endif
                    </div>
                </div>
                <div class="main-content">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Chart.js for analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    @yield('scripts')
</body>
</html> 