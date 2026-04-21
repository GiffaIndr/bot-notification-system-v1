<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Super Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #7c3aed;
            --primary-dark: #6d28d9;
            --danger: #ef4444;
            --success: #10b981;
            --warning: #f59e0b;
        }

        body {
            background: #f8fafc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .sidebar {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            min-height: 100vh;
            padding: 20px;
            position: fixed;
            width: 250px;
            left: 0;
            top: 0;
            box-shadow: 0 10px 30px rgba(124, 58, 237, 0.1);
        }

        .sidebar .brand {
            color: white;
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.7);
            margin: 10px 0;
            border-radius: 8px;
            padding: 12px 15px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s;
            text-decoration: none;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .sidebar .nav-link i {
            width: 20px;
            text-align: center;
        }

        .main-content {
            margin-left: 250px;
            padding: 30px;
            min-height: 100vh;
        }

        .topbar {
            background: white;
            padding: 15px 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        .card {
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid #e2e8f0;
            padding: 20px;
            border-radius: 12px 12px 0 0;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            text-align: center;
        }

        .stat-card .value {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary);
        }

        .stat-card .label {
            color: #64748b;
            font-size: 14px;
            margin-top: 5px;
        }

        .btn-primary {
            background: var(--primary);
            border: none;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                margin-bottom: 20px;
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>
    @yield('css')
</head>

<body>
    <div class="sidebar">
        <div class="brand">
            <i class="fas fa-crown"></i>
            Super Admin
        </div>
        <nav class="nav flex-column">
            <a class="nav-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}"
                href="{{ route('superadmin.dashboard') }}">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
            <a class="nav-link {{ request()->routeIs('superadmin.users.*') ? 'active' : '' }}"
                href="{{ route('superadmin.users.index') }}">
                <i class="fas fa-users"></i> Users
            </a>
            <a class="nav-link {{ request()->routeIs('superadmin.pricing.*') ? 'active' : '' }}"
                href="{{ route('superadmin.pricing.index') }}">
                <i class="fas fa-tag"></i> Pricing
            </a>
            <a class="nav-link {{ request()->routeIs('superadmin.revenue*') ? 'active' : '' }}"
                href="{{ route('superadmin.revenue') }}">
                <i class="fas fa-money-bill-wave"></i> Revenue
            </a>
            <a class="nav-link {{ request()->routeIs('superadmin.activity-logs*') ? 'active' : '' }}"
                href="{{ route('superadmin.activity-logs') }}">
                <i class="fas fa-history"></i> Activity Logs
            </a>
            <hr style="border-color: rgba(255, 255, 255, 0.1); margin: 20px 0;">
            <a class="nav-link" href="{{ route('superadmin.change-password') }}">
                <i class="fas fa-key"></i> Change Password
            </a>
            <a class="nav-link" href="{{ route('superadmin.logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
            <form id="logout-form" action="{{ route('superadmin.logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </nav>
    </div>

    <div class="main-content">
        <div class="topbar">
            <div>
                <h5 class="mb-0">@yield('page_title', 'Dashboard')</h5>
            </div>
            <div class="user-menu">
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div>
                    <small class="text-muted d-block">Logged as</small>
                    <strong>{{ auth()->user()->name }}</strong>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('js')
</body>

</html>
