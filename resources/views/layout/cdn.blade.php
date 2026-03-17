<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /* Custom Styling for Fun & Modern Sidebar */
        :root {
            --sidebar-gradient: linear-gradient(180deg, #4e73df 0%, #224abe 100%);
            --accent-color: #f8f9fc;
            --hover-bg: rgba(255, 255, 255, 0.2);
        }

        body {
            background-color: #f4f7f6;
            font-family: 'Nunito', sans-serif;
        }

        .sidebar {
            background: var(--sidebar-gradient);
            min-height: 100vh;
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
        }

        .sidebar-brand {
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #fff;
            padding: 1.5rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .user-section {
            padding: 1rem;
            background: rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            margin: 1rem;
            font-size: 0.9rem;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
            font-weight: 600;
            padding: 0.8rem 1.2rem !important;
            margin: 0.2rem 1rem;
            border-radius: 8px;
            transition: all 0.2s ease-in-out;
        }

        .nav-link:hover {
            background-color: var(--hover-bg);
            color: #fff !important;
            transform: translateX(5px);
        }

        .nav-link.active {
            background-color: #fff !important;
            color: #4e73df !important;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        /* Responsive Tweaks */
        @media (max-width: 768px) {
            .sidebar {
                min-height: auto;
            }
        }
    </style>

    <title>Library App</title>
</head>

<body>

    <div class="container-fluid">
        <div class="row">

            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse p-0">
                <div class="sidebar-brand text-center">
                    <i class="fas fa-robot me-2"></i>AnnounceBot
                </div>

                <div class="user-section text-white text-center">
                    @auth
                        <small class="d-block opacity-75">Welcome back,</small>
                        <strong>{{ Auth::user()->name }}</strong>
                    @endauth

                    @guest
                        <a href="{{ route('login') }}" class="btn btn-light btn-sm mt-2">
                            Login
                        </a>
                    @endguest
                </div>

                <ul class="nav flex-column mt-3">
                    <li class="nav-item">
                        <a href="/dashboard" class="nav-link {{ Request::is('dashboard') ? 'active' : '' }}">
                            <i class="fas fa-chart-pie"></i> Dashboard
                        </a>
                    </li>
                     <li class="nav-item">
                        <a href="/groups" class="nav-link {{ Request::is('groups') ? 'active' : '' }}">
                            <i class="fa-solid fa-chalkboard-user"></i> Groups
                        </a>
                    </li>
                </ul>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="pt-4">
                    @yield('content')
                </div>
            </main>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}">
    </script>
</body>

</html>
