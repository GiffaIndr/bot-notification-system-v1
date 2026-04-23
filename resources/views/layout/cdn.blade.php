<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/tasku-theme.css') }}">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');

        :root {
            --sidebar-bg: var(--tasku-surface);
            --text-main: var(--tasku-deep);
            --text-muted: #587286;
            --hover-bg: #edf7fb;
            /* Icon Colors */
            --color-dashboard: var(--tasku-primary);
            --color-groups: var(--tasku-deep);
            --color-payments: var(--tasku-primary);
            --color-logout: #a03d3d;
        }

        body {
            background-color: var(--tasku-bg);
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .sidebar {
            background: var(--sidebar-bg);
            position: sticky;
            top: 0;
            height: 100vh;
            box-shadow: 10px 0 30px rgba(0, 0, 0, 0.03);
            border-right: 1px solid #edf2f7;
            padding: 1.5rem 1rem !important;
            display: flex;
            flex-direction: column;
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar::-webkit-scrollbar {
            width: 0px;
        }

        .sidebar-brand {
            font-weight: 800;
            font-size: 1.25rem;
            color: var(--text-main);
            padding: 1rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-brand img {
            width: 28px;
            height: 28px;
            object-fit: contain;
        }

        .user-section {
            background: transparent;
            /* Hilangkan warna latar */
            padding: 0.5rem 1rem;
            margin-bottom: 2rem;
            border: none;
            /* Hilangkan garis pinggir */
        }

        .user-section strong {
            color: var(--text-main);
            display: block;
            font-size: 1.1rem;
            font-weight: 800;
        }

        .nav-item {
            margin-bottom: 0.5rem;
        }

        .nav-link {
            color: var(--text-muted) !important;
            font-weight: 600;
            padding: 0.8rem 1rem !important;
            border-radius: 12px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
        }

        .nav-link i {
            margin-right: 12px;
            font-size: 1.1rem;
            transition: transform 0.3s ease;
        }

        /* Hover Effects with Individual Colors */
        .nav-link:hover {
            background-color: var(--hover-bg);
            color: var(--text-main) !important;
            transform: translateX(4px);
        }

        /* Active States */
        .link-dashboard.active {
            background: #e9f4fa !important;
            color: var(--color-dashboard) !important;
        }

        .link-groups.active {
            background: #e6f2f8 !important;
            color: var(--color-groups) !important;
        }

        .link-payments.active {
            background: #e7f4fa !important;
            color: var(--tasku-deep) !important;
        }

        /* Icon Colors per Menu */
        .link-dashboard i {
            color: var(--color-dashboard);
        }

        .link-groups i {
            color: var(--color-groups);
        }

        .link-payments i {
            color: var(--color-payments);
        }

        .btn-logout {
            margin-top: auto;
            color: var(--color-logout) !important;
            border: 1px solid transparent;
            background: #fff1f2;
            width: 100%;
            text-align: left;
            border-radius: 12px;
            font-weight: 700;
        }

        .btn-logout:hover {
            background: var(--color-logout);
            color: white !important;
        }

        @media (max-width: 768px) {
            .sidebar {
                min-height: auto;
                margin-bottom: 20px;
            }
        }
    </style>

    <title>Tasku</title>
</head>

<body>

    @yield('content2')

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}">
    </script>
</body>

</html>
