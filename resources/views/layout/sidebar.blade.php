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
            <button class="navbar-toggler border-0 p-0" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu"
                aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
                <div class="bg-light rounded-3 p-2 text-dark border">
                    <i class="fa-solid fa-bars-staggered"></i>
                </div>
            </button>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            {{-- 2. SIDEBAR (Sekarang punya ID 'sidebarMenu' agar bisa dibuka-tutup) --}}
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar collapse">

                {{-- Brand Logo (Hidden on Mobile because already in Top Nav) --}}
                <div class="sidebar-brand d-none d-md-flex align-items-center gap-2">
                    <img src="{{ asset('logos/logo_transparan.png') }}" alt="Tasku">
                    <span>Tasku</span>
                </div>


                @auth
                    <a href="{{ route('account.profile') }}"
                        class="d-flex align-items-center justify-content-between gap-2 px-2 py-2 mb-2 rounded-3 text-decoration-none"
                        style="background:#f7faff;color:#1f2937;border:1px solid #e5eefc;">
                        <span class="d-flex align-items-center gap-2 min-w-0">
                            <span class="rounded-circle d-inline-flex align-items-center justify-content-center"
                                style="width:30px;height:30px;background:#e6f0ff;color:#0c66e4;">
                                <i class="fa-solid fa-user"></i>
                            </span>
                            <strong class="text-truncate" style="max-width:120px;">{{ Auth::user()->name }}</strong>
                        </span>
                        <i class="fa-solid fa-chevron-right" style="font-size:0.7rem;"></i>
                    </a>
                @endauth
                <div class="sidebar-section-title px-2 mb-2">Menu</div>


                <ul class="nav flex-column gap-1">
                    <li class="nav-item">
                        <a href="/groups" class="nav-link link-dashboard {{ Request::is('groups') ? 'active' : '' }}">
                            <i class="fa-solid fa-layer-group"></i> Daftar Grup
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/payments" class="nav-link link-payments {{ Request::is('payments*') ? 'active' : '' }}">
                            <i class="fa-solid fa-wallet"></i> Beli Akses
                        </a>
                    </li>

                </ul>



                @auth
                    <div class="sidebar-section-title px-2 mt-4 mb-2">Grup Aktif</div>
                    <div class="workspace-group-list mx-2 mb-3">
                        @php
                            $userGroups = Auth::user()->groups()->orderBy('name')->get();
                        @endphp
                        @if ($userGroups->isEmpty())
                            <div class="text-muted small px-2 py-2">Belum ada grup.<br><a href="/groups"
                                    class="text-primary">Buat atau join grup</a></div>
                        @else
                            <ul class="list-unstyled mb-0">
                                @foreach ($userGroups as $group)
                                    @php
                                        $isActive =
                                            Request::is('groups/' . $group->id) ||
                                            Request::is('groups/' . $group->id . '/*');
                                    @endphp
                                    <li class="mb-1">
                                        <a href="{{ route('groups.show', $group) }}"
                                            class="d-flex align-items-center gap-2 px-2 py-2 rounded-3 sidebar-group-link {{ $isActive ? 'active' : '' }}"
                                            style="transition:background 0.15s, color 0.15s; text-decoration:none; {{ $isActive ? 'background:#e6f0ff;color:#0c66e4;font-weight:600;' : 'color:#222;' }}">
                                            <span class="workspace-avatar"
                                                style="background:{{ $isActive ? '#d0e6ff' : '#eef4ff' }};color:#0c66e4;width:28px;height:28px;display:flex;align-items:center;justify-content:center;border-radius:50%;font-weight:600;">
                                                {{ strtoupper(mb_substr($group->name, 0, 1)) }}
                                            </span>
                                            <span class="text-truncate" style="max-width:120px;">{{ $group->name }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
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
                top: 60px;
                left: 0;
                width: 100%;
                height: calc(100vh - 60px);
                z-index: 1000;
                overflow-y: auto;
                background-color: var(--sidebar-bg) !important;
                padding-bottom: 80px;
            }

            .sidebar.collapse:not(.show) {
                display: none;
            }

            .sidebar.collapsing {
                height: 0;
                transition: height 0.3s ease;
            }
        }

        .shadow-xs {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
    </style>
@endsection
