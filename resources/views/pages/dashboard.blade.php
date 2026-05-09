@extends('layout.sidebar')

@section('content')
    <div class="container-fluid pb-5 dashboard-shell">
        <div class="dashboard-topbar card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-3 p-lg-4">
                <div class="row g-3 align-items-center">
                    <div class="col-12 col-xl-5">
                        <div class="d-flex align-items-center gap-3">
                            <div class="dashboard-mark rounded-4 d-inline-flex align-items-center justify-content-center">
                                <i class="fas fa-chart-pie"></i>
                            </div>
                            <div>
                                <h2 class="fw-bold fs-4 mb-1 text-dark">Dashboard</h2>
                                <p class="text-muted mb-0 small">Satu tempat untuk kelola grup, akses, dan update terbaru.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-xl-4">
                        <div class="input-group dashboard-search">
                            <span class="input-group-text bg-white border-end-0"><i
                                    class="fas fa-search text-muted"></i></span>
                            <input type="text" id="groupSearch" class="form-control border-start-0 ps-0"
                                placeholder="Cari grup, role, atau status">
                        </div>
                    </div>
                    <div class="col-12 col-xl-3 d-flex gap-2 justify-content-xl-end">
                        <a href="#quick-actions" class="btn btn-outline-secondary rounded-pill fw-semibold px-4">Quick
                            Actions</a>
                        <a href="#group-list" class="btn btn-primary rounded-pill fw-semibold px-4">Lihat Grup</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12 col-xl-8">
                <div class="row g-4 mb-4" id="quick-actions">
                    <x-dashboard-action-card title="Buat Grup" icon="fas fa-plus" tone="success"
                        subtitle="Buat ruang kerja baru dan atur anggota tim dari dashboard ini.">
                        @if (!$subscription)
                            <a href="/payments" class="btn btn-success w-100 py-2 fw-semibold rounded-pill">Beli Akses
                                Grup</a>
                        @elseif ($groupCount >= $maxGroup)
                            @php
                                $pendingUpgrade = \App\Models\Payment::where('user_id', auth()->id())
                                    ->where('status', 'success')
                                    ->where('starts_at', '>', now())
                                    ->with('plan')
                                    ->latest()
                                    ->first();
                            @endphp

                            <div class="mb-3">
                                <p class="fw-semibold mb-1">Batas grup tercapai ({{ $groupCount }}/{{ $maxGroup }})
                                </p>
                                <p class="text-muted small mb-0">Upgrade akses kalau ingin menambah kuota.</p>
                                @if ($pendingUpgrade && $pendingUpgrade->plan->max_group > $maxGroup)
                                    <div class="alert alert-primary py-2 px-3 mt-3 mb-0 small">
                                        Kuota naik jadi <strong>{{ $pendingUpgrade->plan->max_group }}</strong> grup pada
                                        <strong>{{ $pendingUpgrade->starts_at->format('d M Y') }}</strong>.
                                    </div>
                                @endif
                            </div>

                            @if (!$pendingUpgrade || $pendingUpgrade->plan->max_group <= $maxGroup)
                                <a href="/payments" class="btn btn-success w-100 py-2 fw-semibold rounded-pill">Upgrade
                                    Akses</a>
                            @endif
                        @else
                            <form id="formCreateGroup" method="POST" action="/groups" class="d-flex flex-column gap-2">
                                @csrf
                                <input type="text" id="inputGroupName" name="name"
                                    class="form-control py-2 rounded-pill" placeholder="Nama grup baru">
                                <button type="button" class="btn btn-success w-100 py-2 fw-semibold rounded-pill"
                                    onclick="submitCreateGroup()">Buat Sekarang</button>
                            </form>
                        @endif
                    </x-dashboard-action-card>

                    <x-dashboard-action-card title="Gabung Grup" icon="fas fa-link" tone="primary"
                        subtitle="Masukkan kode undangan dan langsung masuk ke grup yang dituju.">
                        <form id="formJoinGroup" method="POST" action="/join" class="d-flex flex-column gap-2">
                            @csrf
                            <input type="text" id="inputJoinCode" name="code"
                                class="form-control py-2 rounded-pill text-center" placeholder="Contoh: ABC-123">
                            <button type="button" class="btn btn-primary w-100 py-2 fw-semibold rounded-pill"
                                onclick="submitJoinGroup()">Gabung Grup</button>
                        </form>
                    </x-dashboard-action-card>
                </div>

                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-3 p-lg-4">
                        <div class="d-flex align-items-center justify-content-between gap-3 mb-3 flex-wrap">
                            <div>
                                <h5 class="fw-bold mb-1"><i class="fas fa-layer-group me-2 text-muted"></i>Grup Saya</h5>
                                <p class="text-muted small mb-0">Cari, filter, lalu buka grup yang ingin kamu kelola.</p>
                            </div>
                            <div class="d-inline-flex gap-2 p-1 bg-light rounded-pill border">
                                <button class="btn btn-sm px-3 rounded-pill fw-semibold active-filter"
                                    data-group-filter="all">Semua</button>
                                <button class="btn btn-sm px-3 rounded-pill fw-semibold text-muted"
                                    data-group-filter="owner">Pemilik</button>
                                <button class="btn btn-sm px-3 rounded-pill fw-semibold text-muted"
                                    data-group-filter="member">Anggota</button>
                            </div>
                        </div>

                        <div class="row g-3" id="group-list">
                            @forelse ($groups as $group)
                                @php
                                    $membership = $groupMemberships[$group->id] ?? null;
                                    $isOwner = $membership?->role?->is_owner;
                                    $initial = strtoupper(substr($group->name, 0, 2));
                                @endphp
                                <div class="col-12 col-md-6 col-xxl-4 group-item" data-group-item
                                    data-name="{{ strtolower($group->name) }}"
                                    data-role="{{ $isOwner ? 'owner' : 'member' }}">
                                    <div class="group-card card border-0 shadow-sm rounded-4 h-100">
                                        <div class="group-card__bar"></div>
                                        <div class="card-body p-3 d-flex align-items-center gap-3">
                                            <div
                                                class="group-avatar rounded-4 d-inline-flex align-items-center justify-content-center flex-shrink-0">
                                                {{ $initial }}
                                            </div>
                                            <div class="flex-grow-1 min-w-0">
                                                <h6 class="fw-bold mb-1 text-truncate">{{ $group->name }}</h6>
                                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                                    <span
                                                        class="badge rounded-pill text-uppercase {{ $isOwner ? 'text-success' : 'text-secondary' }} bg-light border">{{ $isOwner ? 'Pemilik' : 'Anggota' }}</span>
                                                    @if ($group->announcements_count > 0)
                                                        <span
                                                            class="badge rounded-pill text-dark bg-warning-subtle border border-warning-subtle">{{ $group->announcements_count }}
                                                            update</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <a href="/groups/{{ $group->getRouteKey() }}"
                                                class="btn btn-light border rounded-pill fw-semibold px-3">Buka</a>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="empty-state card border-0 shadow-sm rounded-4 text-center p-5">
                                        <i class="fas fa-inbox fs-1 text-muted mb-3"></i>
                                        <h6 class="fw-bold mb-2">Belum ada grup yang diikuti</h6>
                                        <p class="text-muted mb-0">Mulai dengan membuat grup baru atau gabung memakai kode
                                            undangan.</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="fw-bold mb-0">Ringkasan</h5>
                            <span class="badge rounded-pill bg-light text-dark border">{{ $totalGroups }} grup</span>
                        </div>

                        <div class="summary-grid">
                            <div class="summary-card">
                                <span class="summary-card__label">Kuota Owner</span>
                                <strong>{{ $groupCount }}/{{ $maxGroup }}</strong>
                            </div>
                            <div class="summary-card">
                                <span class="summary-card__label">Status</span>
                                <strong>{{ $subscription ? 'Aktif' : 'Belum aktif' }}</strong>
                            </div>
                        </div>

                        <div class="mt-3">
                            @if ($subscription)
                                <div class="alert alert-success py-2 px-3 mb-0 small">
                                    Akses grup aktif. Kamu bisa lanjut mengelola grup dari sini.
                                </div>
                            @else
                                <div class="alert alert-warning py-2 px-3 mb-0 small">
                                    Kamu belum punya akses grup. Beli akses dulu untuk membuat grup baru.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="fw-bold mb-0">Update Terbaru</h5>
                            <i class="fas fa-bullhorn text-muted"></i>
                        </div>

                        @if ($latestAnnouncement)
                            <div class="latest-item">
                                <div class="fw-bold mb-1 text-truncate">{{ $latestAnnouncement->title }}</div>
                                <div class="text-muted small mb-2">{{ $latestAnnouncement->group->name }} •
                                    {{ $latestAnnouncement->created_at->diffForHumans() }}</div>
                                <a href="/groups/{{ $latestAnnouncement->group_id }}"
                                    class="btn btn-outline-primary rounded-pill btn-sm">Buka grup</a>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="far fa-bell-slash fs-1 text-muted mb-3"></i>
                                <p class="text-muted mb-0">Belum ada update terbaru.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
        <div id="toast" class="toast align-items-center text-white border-0 shadow-lg rounded-3" role="alert">
            <div class="d-flex">
                <div class="toast-body fw-bold" id="toastMessage"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    <style>
        .dashboard-shell {
            --accent: var(--tasku-primary);
        }

        .dashboard-mark {
            width: 52px;
            height: 52px;
            background: linear-gradient(135deg, #e9f4fa 0%, #d8edf7 100%);
            color: var(--tasku-primary);
            font-size: 1.2rem;
        }

        .dashboard-search .input-group-text,
        .dashboard-search .form-control {
            border-color: #d8e3ec;
            box-shadow: none;
        }

        .dashboard-action-card {
            overflow: hidden;
            border: 1px solid #e7edf3 !important;
        }

        .dashboard-action-card__accent {
            height: 5px;
        }

        .group-card {
            overflow: hidden;
            border: 1px solid #e7edf3 !important;
        }

        .group-card__bar {
            height: 4px;
            background: linear-gradient(90deg, var(--tasku-primary), #67b3d1);
        }

        .group-avatar {
            width: 54px;
            height: 54px;
            font-weight: 800;
            color: var(--tasku-deep);
            background: linear-gradient(135deg, #edf7fb, #e4eef7);
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .summary-card {
            background: #f8fbfd;
            border: 1px solid #e7edf3;
            border-radius: 18px;
            padding: 14px;
        }

        .summary-card__label {
            display: block;
            font-size: 0.75rem;
            color: #6a7d8b;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .active-filter {
            background: var(--tasku-primary) !important;
            color: #fff !important;
            box-shadow: 0 8px 16px rgba(51, 118, 163, 0.22);
        }

        .group-item[style*="display: none"] {
            display: none !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('groupSearch');
            const filterBtns = document.querySelectorAll('[data-group-filter]');
            const groupItems = document.querySelectorAll('[data-group-item]');

            const runFilter = () => {
                const query = searchInput.value.toLowerCase().trim();
                const activeFilter = document.querySelector('.active-filter')?.dataset.groupFilter || 'all';

                groupItems.forEach(item => {
                    const isMatch = item.dataset.name.includes(query) &&
                        (activeFilter === 'all' || item.dataset.role === activeFilter);
                    item.style.display = isMatch ? '' : 'none';
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

        function showToast(message, type = 'danger') {
            const toast = document.getElementById('toast');
            const toastMsg = document.getElementById('toastMessage');
            toast.className = `toast align-items-center text-white border-0 shadow-lg bg-${type} rounded-3`;
            toastMsg.innerText = message;
            new bootstrap.Toast(toast, {
                delay: 3000
            }).show();
        }

        function submitCreateGroup() {
            const name = document.getElementById('inputGroupName').value.trim();
            if (!name) return showToast('Isi nama grup dulu.', 'warning');
            document.getElementById('formCreateGroup').submit();
        }

        function submitJoinGroup() {
            const code = document.getElementById('inputJoinCode').value.trim();
            if (!code) return showToast('Masukkan kode undangan dulu.', 'warning');
            document.getElementById('formJoinGroup').submit();
        }
    </script>
@endsection
