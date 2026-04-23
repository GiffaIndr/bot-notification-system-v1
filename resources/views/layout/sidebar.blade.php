@extends('layout.cdn')

@section('content2')
    {{-- 1. NAVBAR MOBILE (Hanya muncul di layar kecil < 768px) --}}
    <nav class="navbar navbar-expand-md navbar-light bg-white border-bottom d-md-none sticky-top shadow-sm px-3 py-2">
        <div class="container-fluid p-0">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2 m-0" href="#">
                <img src="{{ asset('logos/logo_transparan.png') }}" alt="Tasku"
                    style="width: 32px; height: 32px; object-fit: contain;">
                <span style="letter-spacing: -0.5px;">Tasku</span>
            </a>

            {{-- Tombol Hamburger Modern --}}
            <button class="navbar-toggler border-0 p-0" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
                <div class="bg-light rounded-3 p-2 text-dark border">
                    <i class="fa-solid fa-bars-staggered"></i>
                </div>
            </button>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            {{-- 2. SIDEBAR (Sekarang punya ID 'sidebarMenu' agar bisa dibuka-tutup) --}}
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar collapse bg-white border-end">

                {{-- Brand Logo (Hidden on Mobile because already in Top Nav) --}}
                <div class="sidebar-brand d-none d-md-flex">
                    <img src="{{ asset('logos/logo_transparan.png') }}" alt="Tasku">
                    <span>Tasku</span>
                </div>

                {{-- Area Profil --}}
                <div class="d-flex align-items-center gap-3 px-3 mb-4 mt-4 mt-md-2">
                    @auth
                        <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center border border-primary border-opacity-25 shadow-xs"
                            style="width: 42px; height: 42px;">
                            <i class="fas fa-user text-primary"></i>
                        </div>
                        <div class="lh-sm text-start">
                            <small class="text-muted d-block" style="font-size: 0.7rem;">Selamat datang,</small>
                            <strong class="text-dark">{{ Auth::user()->name }}</strong>
                        </div>
                    @endauth
                    @guest
                        <a href="{{ route('login') }}" class="btn btn-primary btn-sm w-100 rounded-pill fw-bold">
                            Masuk Akun
                        </a>
                    @endguest
                </div>

                <ul class="nav flex-column gap-1">
                    <li class="nav-item">
                        <a href="/dashboard" class="nav-link link-dashboard {{ Request::is('dashboard') ? 'active' : '' }}">
                            <i class="fas fa-chart-pie me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/payments" class="nav-link link-payments {{ Request::is('payments*') ? 'active' : '' }}">
                            <i class="fa-solid fa-wallet me-2"></i> Beli Akses Grup
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/groups" class="nav-link link-groups {{ Request::is('groups*') ? 'active' : '' }}">
                            <i class="fa-solid fa-layer-group me-2"></i> Daftar Grup
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/paymentlogs" class="nav-link link-payments {{ Request::is('paymentlogs*') ? 'active' : '' }}">
                            <i class="fa-solid fa-receipt me-2"></i> Riwayat Pembayaran
                        </a>
                    </li>
                </ul>

                @auth
                    <div class="mt-4 px-2 pt-4 border-top">
                        <a href="/home" class="nav-link rounded-4 border-0 shadow-xs mb-3" style="background: #e9f4fa; color: var(--tasku-deep) !important; font-weight: 700;">
                            <i class="fas fa-house me-2"></i> Beranda Utama
                        </a>

                        <form action="{{ route('logout') }}" method="POST" class="mt-auto p-0">
                            @csrf
                            <button type="submit" class="btn btn-logout nav-link w-100 border-0 text-start px-3 py-2 rounded-4">
                                <i class="fas fa-sign-out-alt me-2 text-danger"></i> Keluar Akun
                            </button>
                        </form>
                    </div>
                @endauth
            </nav>

            {{-- 3. MAIN CONTENT area --}}
            <main class="col-md-9 ms-sm-auto col-lg-10 px-0">
                {{-- Padding diatur agar di mobile tidak tertutup Navbar --}}
                <div class="px-md-4 py-4 pt-3 pt-md-4">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    {{-- Sedikit CSS tambahan untuk transisi mobile yang mulus --}}
    <style>
        @media (max-width: 767.98px) {
            .sidebar {
                position: fixed;
                top: 60px; /* Tinggi navbar mobile */
                left: 0;
                width: 100%;
                height: calc(100vh - 60px);
                z-index: 1000;
                overflow-y: auto;
                background-color: white !important;
                padding-bottom: 80px;
            }
            /* Efek animasi slide down */
            .sidebar.collapse:not(.show) {
                display: none;
            }
            .sidebar.collapsing {
                height: 0;
                transition: height 0.3s ease;
            }
        }
        .shadow-xs { box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    </style>
@endsection
