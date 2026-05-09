<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/png" href="{{ asset('logos/logo_transparan.png') }}" sizes="32x32">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/tasku-theme.css') }}">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');

        :root {
            --sidebar-bg: #f7f8fa;
            --sidebar-surface: #ffffff;
            --sidebar-surface-2: #eef1f5;
            --sidebar-border: rgba(15, 23, 42, 0.08);
            --sidebar-text: #172b4d;
            --sidebar-muted: #6b778c;
            --sidebar-hover: rgba(9, 30, 66, 0.06);
            --sidebar-active: #e9f2ff;
            --sidebar-active-text: #0c66e4;
            --color-dashboard: #0c66e4;
            --color-groups: #42526e;
            --color-payments: #0c66e4;
            --color-logout: #c9372c;
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
            box-shadow: 10px 0 30px rgba(9, 30, 66, 0.08);
            border-right: 1px solid var(--sidebar-border);
            padding: 1rem 1rem 1.25rem !important;
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
            font-size: 1.05rem;
            color: var(--sidebar-text);
            padding: 0.25rem 0.25rem 1rem;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-brand img {
            width: 30px;
            height: 30px;
            object-fit: contain;
        }

        .sidebar-brand__mark {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: linear-gradient(135deg, #0c66e4, #4c9aff);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.85rem;
            box-shadow: 0 6px 16px rgba(13, 110, 253, 0.25);
        }

        .user-section {
            background: transparent;
            padding: 0.5rem 1rem;
            margin-bottom: 2rem;
            border: none;
        }

        .user-section strong {
            color: var(--sidebar-text);
            display: block;
            font-size: 1.1rem;
            font-weight: 800;
        }

        .nav-item {
            margin-bottom: 0.35rem;
        }

        .nav-link {
            color: var(--sidebar-text) !important;
            font-weight: 600;
            padding: 0.75rem 0.9rem !important;
            border-radius: 10px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
        }

        .nav-link i {
            margin-right: 10px;
            font-size: 1rem;
            transition: transform 0.3s ease;
        }

        .nav-link:hover {
            background-color: var(--sidebar-hover);
            color: var(--sidebar-text) !important;
        }

        .link-dashboard.active {
            background: var(--sidebar-active) !important;
            color: var(--sidebar-active-text) !important;
        }

        .link-home.active,
        .link-templates.active,
        .link-groups.active {
            background: var(--sidebar-active) !important;
            color: var(--sidebar-active-text) !important;
        }

        .link-payments.active {
            background: var(--sidebar-active) !important;
            color: var(--sidebar-active-text) !important;
        }

        .link-dashboard i {
            color: var(--color-dashboard);
        }

        .link-home i,
        .link-templates i,
        .link-groups i {
            color: var(--color-groups);
        }

        .link-payments i {
            color: var(--color-payments);
        }

        .btn-logout {
            margin-top: auto;
            color: var(--sidebar-text) !important;
            border: 1px solid var(--sidebar-border);
            background: #ffffff;
            width: 100%;
            text-align: left;
            border-radius: 10px;
            font-weight: 700;
        }

        .btn-logout:hover {
            background: #f1f4f9;
            color: var(--sidebar-text) !important;
        }

        .sidebar-section-title {
            color: var(--sidebar-muted);
            font-size: 0.88rem;
            font-weight: 700;
            letter-spacing: 0.01em;
        }

        .workspace-card {
            background: var(--sidebar-surface);
            border: 1px solid var(--sidebar-border);
            border-radius: 12px;
        }

        .workspace-avatar {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: linear-gradient(135deg, #3ad29f, #2d8f6b);
            color: #ffffff;
            font-weight: 900;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar-subtle {
            color: var(--sidebar-muted);
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
    <script
        src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
        data-client-key="{{ config('midtrans.client_key') }}"></script>
</body>

</html>
