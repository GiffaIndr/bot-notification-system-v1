@extends('layout.cdn')

@section('content2')
    <div class="min-vh-100 pb-5 text-dark" style="background-color: #f4f7fa; font-family: 'Plus Jakarta Sans', sans-serif;">

        {{-- 1. NAVBAR: Sederhana & Simetris --}}
        <nav class="navbar navbar-expand-lg bg-white border-bottom shadow-sm py-2 mb-0 mb-lg-4">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center gap-2 fw-bold m-0" href="{{ url('/home') }}">
                    <div class="bg-primary text-white rounded-3 d-flex align-items-center justify-content-center shadow-sm"
                        style="width: 32px; height: 32px;">
                        <i class="fa-solid fa-bolt fs-6"></i>
                    </div>
                    <span>Tasku</span>
                </a>
                <form action="{{ route('logout') }}" method="POST" class="ms-auto">
                    @csrf
                    <button class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold border-2" type="submit">
                        <i class="fa-solid fa-right-from-bracket me-1"></i> <span class="d-none d-sm-inline">Keluar</span>
                    </button>
                </form>
            </div>
        </nav>

        <div class="container pt-4 pt-lg-5">
            <div class="row justify-content-center g-4">
                <div class="col-12 col-xxl-11">

                    {{-- 2. TOP SECTION: Pembaruan Terakhir (Kini Mandiri & Simetris) --}}
                    <div class="row g-4 mb-4">
                        <div class="col-12 col-lg-7">
                            <div class="d-flex flex-column justify-content-center h-100 py-2">
                                <h1 class="fw-bold mb-2" style="font-size: 1.75rem; letter-spacing: -1px;">Halo,
                                    {{ auth()->user()->name }}!</h1>
                                <p class="text-muted mb-0 fs-6">Selamat datang kembali di pusat kolaborasi Anda.</p>
                            </div>
                        </div>
                        <div class="col-12 col-lg-5">
                            <div class="card border-0 shadow-sm rounded-4 bg-white border border-info-subtle">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-2 py-1"
                                            style="font-size: 10px;">
                                            <i class="fa-solid fa-bullhorn me-1"></i>UPDATE TERBARU
                                        </span>
                                    </div>
                                    @if ($latestAnnouncement)
                                        <div class="d-flex align-items-start gap-2">
                                            <div class="flex-grow-1 min-w-0">
                                                <div class="fw-bold text-dark small text-truncate">
                                                    {{ $latestAnnouncement->title }}</div>
                                                <div class="text-muted" style="font-size: 11px;">Grup:
                                                    {{ $latestAnnouncement->group->name }} •
                                                    {{ $latestAnnouncement->created_at->diffForHumans() }}</div>
                                            </div>
                                            <a href="/groups/{{ $latestAnnouncement->group_id }}"
                                                class="btn btn-light btn-sm rounded-circle border p-0 d-flex align-items-center justify-content-center shadow-xs"
                                                style="width: 28px; height: 28px;">
                                                <i class="fa-solid fa-chevron-right text-primary"
                                                    style="font-size: 10px;"></i>
                                            </a>
                                        </div>
                                    @else
                                        <p class="text-muted small italic mb-0">Belum ada pembaruan hari ini.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 3. ACTION CARDS: Gabung & Buat (Simetris & Ukuran Standar) --}}
                    <div class="row g-4 mb-5">
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border">
                                <div class="card-body p-4 d-flex flex-column h-100">
                                    <div class="d-flex align-items-center gap-3 mb-3">
                                        <div class="bg-primary bg-opacity-10 text-primary p-2 px-3 rounded-3 border"><i
                                                class="fa-solid fa-user-plus"></i></div>
                                        <h5 class="fw-bold mb-0">Gabung Grup</h5>
                                    </div>
                                    <p class="text-muted small mb-4">Masukkan kode undangan dari admin untuk bergabung.</p>
                                    <form action="{{ url('/join') }}" method="POST" class="mt-auto">
                                        @csrf
                                        <div class="input-group">
                                            <input type="text" name="code"
                                                class="form-control border-2 shadow-none py-2 fw-bold" placeholder="ABC-123"
                                                required style="border-radius: 10px 0 0 10px; text-transform: uppercase;">
                                            <button class="btn btn-primary px-4 fw-bold shadow-sm" type="submit"
                                                style="border-radius: 0 10px 10px 0;">Gabung</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border">
                                <div class="card-body p-4 d-flex flex-column h-100">
                                    <div class="d-flex align-items-center gap-3 mb-3">
                                        <div class="bg-success bg-opacity-10 text-success p-2 px-3 rounded-3 border"><i
                                                class="fa-solid fa-plus"></i></div>
                                        <h5 class="fw-bold mb-0 text-dark">Buat Grup Baru</h5>
                                    </div>
                                    <p class="text-muted small mb-4">Siapkan ruang kerja baru dan kelola anggota tim Anda
                                        sendiri.</p>
                                    <div class="mt-auto">
                                        <a href="{{ url('/dashboard') }}"
                                            class="btn btn-outline-dark w-100 fw-bold py-2 rounded-3 shadow-xs">
                                            Ke Dashboard Manajemen <i class="fa-solid fa-arrow-right ms-2"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 4. BROWSE SECTION: Search & My Groups --}}
                    <div class="bg-white p-3 p-md-4 rounded-4 shadow-sm mb-4 border">
                        <div class="row g-3 align-items-center">
                            <div class="col-12 col-md-6">
                                <h5 class="fw-bold text-dark m-0"><i
                                        class="fa-solid fa-layer-group me-2 text-muted"></i>Grup Saya</h5>
                            </div>
                            <div class="col-12 col-md-6 text-md-end">
                                <div class="d-inline-flex gap-2 p-1 bg-light border rounded-pill shadow-xs">
                                    <button class="btn btn-sm px-3 rounded-pill fw-bold active-filter"
                                        data-group-filter="all">Semua</button>
                                    <button class="btn btn-sm px-3 rounded-pill fw-bold text-muted"
                                        data-group-filter="pemilik">Pemilik</button>
                                    <button class="btn btn-sm px-3 rounded-pill fw-bold text-muted"
                                        data-group-filter="anggota">Anggota</button>
                                </div>
                            </div>
                        </div>
                        <div class="input-group mt-3 border rounded-3 overflow-hidden shadow-xs">
                            <span class="input-group-text bg-white border-0 text-muted ps-3"><i
                                    class="fa-solid fa-search"></i></span>
                            <input type="text" id="groupSearch" class="form-control border-0 py-2"
                                placeholder="Cari nama grup Anda...">
                        </div>
                    </div>

                    {{-- GRID GRUP --}}
                    <div class="row g-3" id="groupGrid">
                        @forelse ($groups as $group)
                            @php
                                $membership = $groupMemberships[$group->id] ?? null;
                                $isOwner = $membership?->role?->is_owner;
                                $initial = strtoupper(substr($group->name, 0, 2));
                            @endphp
                            <div class="col-12 col-md-6 col-xl-4 group-item" data-group-item
                                data-name="{{ strtolower($group->name) }}"
                                data-role="{{ $isOwner ? 'pemilik' : 'anggota' }}">
                                <div
                                    class="card border-0 shadow-sm rounded-4 h-100 transition-all hover-card bg-white border">
                                    <div class="card-body p-3 d-flex align-items-center gap-3">
                                        <div class="d-flex align-items-center justify-content-center rounded-3 bg-secondary bg-opacity-10 text-dark fw-bold flex-shrink-0 shadow-xs"
                                            style="width: 48px; height: 48px; font-size: 1.1rem;">
                                            {{ $initial }}
                                        </div>
                                        <div class="flex-grow-1 min-w-0">
                                            <h6 class="fw-bold text-dark mb-1 text-truncate pe-3">{{ $group->name }}</h6>
                                            <div class="d-flex gap-1 flex-wrap">
                                                <span class="badge border text-muted fw-medium rounded-pill"
                                                    style="font-size: 9px; letter-spacing: 0.5px;">{{ $isOwner ? 'PEMILIK' : 'ANGGOTA' }}</span>
                                                @if ($group->announcements_count > 0)
                                                    <span class="badge bg-warning text-dark rounded-pill fw-bold shadow-xs"
                                                        style="font-size: 9px;">
                                                        <i
                                                            class="fa-solid fa-bell me-1"></i>{{ $group->announcements_count }}
                                                        Info
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <a href="{{ url('/groups/' . $group->id) }}"
                                            class="btn btn-light border btn-sm rounded-pill px-3 fw-bold shadow-xs">Buka</a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5 bg-white rounded-4 border border-dashed shadow-sm">
                                <p class="text-muted small mb-0">Belum ada grup yang diikuti.</p>
                            </div>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>
    </div>

    <style>
        .shadow-xs {
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.06);
        }

        .transition-all {
            transition: all 0.25s ease;
        }

        .hover-card:hover {
            transform: translateY(-3px);
            border-color: var(--tasku-primary) !important;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06) !important;
        }

        .active-filter {
            background: var(--tasku-primary) !important;
            color: white !important;
            box-shadow: 0 2px 8px rgba(51, 118, 163, 0.35);
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('groupSearch');
            const filterBtns = document.querySelectorAll('[data-group-filter]');
            const groupItems = document.querySelectorAll('[data-group-item]');

            const runFilter = () => {
                const query = searchInput.value.toLowerCase().trim();
                const activeFilter = document.querySelector('.active-filter').dataset.groupFilter;

                groupItems.forEach(item => {
                    const isMatch = item.dataset.name.includes(query) &&
                        (activeFilter === 'all' || item.dataset.role === activeFilter);
                    item.style.display = isMatch ? 'block' : 'none';
                });
            };

            filterBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    filterBtns.forEach(b => b.classList.remove('active-filter', 'text-white'));
                    btn.classList.add('active-filter');
                    runFilter();
                });
            });
            searchInput.addEventListener('input', runFilter);
        });
    </script>
@endsection
