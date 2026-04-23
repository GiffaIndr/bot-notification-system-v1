@extends('layout.sidebar')

@section('content')
    <div class="container-fluid pb-5">
        {{-- Menggunakan fs-4 dan fw-bold untuk judul tanpa CSS --}}
        <h2 class="fw-bold fs-4 text-dark mb-4">Dashboard</h2>

        <div class="row g-4 mb-4 align-items-stretch">

            {{-- Buat Grup --}}
            <x-dashboard-action-card title="Buat Grup" icon="fas fa-plus-circle" :icon-disabled="!$subscription">
                @if (!$subscription)
                    <div class="text-center mb-3">
                        <p class="text-muted small mb-0">Fitur ini terkunci. Silakan beli Akses Grup terlebih dahulu.</p>
                    </div>
                    <a href="/payments" class="btn btn-primary w-100 py-2 mt-auto fw-medium">Beli Akses Grup</a>
                @elseif ($groupCount >= $maxGroup)
                    <div class="text-center mb-3">
                        <p class="small fw-bold text-dark mb-1 d-flex align-items-center justify-content-center gap-1">
                            <span class="text-warning fs-6">⚠️</span> Batas grup tercapai ({{ $groupCount }}/{{ $maxGroup }})
                        </p>
                        <p class="text-muted small mb-0">Beli akses grup untuk menambah kuota.</p>

                        @php
                            $pendingUpgrade = \App\Models\Payment::where('user_id', auth()->id())->where('status', 'success')->where('starts_at', '>', now())->with('plan')->latest()->first();
                        @endphp

                        @if ($pendingUpgrade && $pendingUpgrade->plan->max_group > $maxGroup)
                            <p class="small mt-2 mb-0 text-primary bg-primary-subtle p-2 rounded-3">
                                <i class="fa fa-circle-info me-1"></i> Kuota akan jadi <strong>{{ $pendingUpgrade->plan->max_group }}</strong> grup pada <strong>{{ $pendingUpgrade->starts_at->format('d M Y') }}</strong>
                            </p>
                        @endif
                    </div>

                    @if (!$pendingUpgrade || $pendingUpgrade->plan->max_group <= $maxGroup)
                        <a href="/payments" class="btn btn-primary w-100 py-2 mt-auto fw-medium">Upgrade Akses</a>
                    @endif
                @else
                    <div class="text-center mb-3">
                        <p class="text-muted small mb-0">Sisa kuota grup: <strong>{{ $maxGroup - $groupCount }}</strong></p>
                    </div>
                    <form id="formCreateGroup" method="POST" action="/groups" class="mt-auto d-flex flex-column gap-2">
                        @csrf
                        <input type="text" id="inputGroupName" name="name" class="form-control py-2 text-center" placeholder="Nama grup baru">
                        <button type="button" class="btn btn-primary w-100 py-2 fw-medium" onclick="submitCreateGroup()">Buat Sekarang</button>
                    </form>
                @endif
            </x-dashboard-action-card>

            {{-- Gabung Grup --}}
            <x-dashboard-action-card title="Gabung Grup" icon="fas fa-link">
                <div class="text-center mb-3">
                    <p class="text-muted small mb-0">Masukkan kode undangan untuk bergabung.</p>
                </div>
                <form id="formJoinGroup" method="POST" action="/join" class="mt-auto d-flex flex-column gap-2">
                    @csrf
                    <input type="text" id="inputJoinCode" name="code" class="form-control py-2 text-center" placeholder="Contoh: ABC-123">
                    <button type="button" class="btn btn-primary w-100 py-2 fw-medium" onclick="submitJoinGroup()">Gabung Grup</button>
                </form>
            </x-dashboard-action-card>

        </div>
        {{-- Grup Saya --}}
        <div class="row">
            <div class="col-12">
                {{-- Mengganti style .card dengan utility classes --}}
                <div class="card border-0 shadow-sm rounded-4 h-100 d-flex flex-column">
                    <div class="card-header bg-transparent border-bottom p-4 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-semibold fs-5"><i class="fas fa-user-friends me-2 text-muted"></i>Grup Saya</h5>
                        @if ($totalGroups > 3)
                            <a href="/groups" class="btn btn-sm btn-outline-secondary fw-medium">Lihat Semua ({{ $totalGroups }})</a>
                        @endif
                    </div>
                    <div class="card-body p-4 d-flex flex-column flex-grow-1">
                        {{-- Gunakan utilitas grid gap (g-3) sebagai pengganti margin --}}
                        <div class="row g-3">
                            @forelse ($groups as $group)
                                <div class="col-12 col-md-6 col-xl-4 col-xxl-3">
                                    <div class="card border rounded-3 shadow-none h-100 d-flex flex-column">
                                        <div class="card-body p-3 d-flex flex-column flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="fw-bold mb-0 text-truncate" style="max-width: 85%;">{{ $group->name }}</h6>
                                                <button class="btn btn-link p-0 text-muted border-0"><i class="fas fa-ellipsis-v"></i></button>
                                            </div>

                                            @php
                                                $memberRole = \App\Models\GroupMember::where('group_id', $group->id)
                                                    ->where('user_id', auth()->id())
                                                    ->with('role')
                                                    ->first();
                                            @endphp

                                            <div class="mb-3">
                                                @if ($memberRole?->role)
                                                    {{-- Pengganti .badge-role dengan px-2 py-1 rounded-pill fw-medium --}}
                                                    <span class="badge rounded-pill bg-secondary-subtle text-secondary-emphasis px-2 py-1 fw-medium">
                                                        {{ strtoupper($memberRole->role->name) === 'OWNER' ? 'Pemilik' : $memberRole->role->name }}
                                                    </span>
                                                @endif
                                            </div>

                                            <a href="/groups/{{ $group->id }}" class="btn btn-light border w-100 d-flex justify-content-between align-items-center rounded-4 py-2 fw-semibold text-dark text-decoration-none">
                                    <span>Kelola Grup</span>
                                    <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 26px; height: 26px;">
                                        <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                                    </div>
                                </a>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center py-5">
                                    <img src="https://illustrations.popsy.co/flat/team-building.svg" alt="empty" style="width: 150px;" class="mb-3 opacity-50">
                                    <p class="text-muted mb-0">Kamu belum bergabung di grup manapun.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast & Scripts tetap sama --}}
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
        <div id="toast" class="toast align-items-center text-white border-0 shadow-lg rounded-3" role="alert">
            <div class="d-flex">
                <div class="toast-body fw-bold" id="toastMessage"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    <script>
        function showToast(message, type = 'danger') {
            const toast = document.getElementById('toast');
            const toastMsg = document.getElementById('toastMessage');
            toast.className = `toast align-items-center text-white border-0 shadow-lg bg-${type} rounded-3`;
            toastMsg.innerText = message;
            new bootstrap.Toast(toast, { delay: 3000 }).show();
        }

        function submitCreateGroup() {
            const name = document.getElementById('inputGroupName').value.trim();
            if (!name) return showToast('⚠️ Isi nama grup dulu ya!', 'warning');
            document.getElementById('formCreateGroup').submit();
        }

        function submitJoinGroup() {
            const code = document.getElementById('inputJoinCode').value.trim();
            if (!code) return showToast('⚠️ Masukkan kode undangan dulu ya!', 'warning');
            document.getElementById('formJoinGroup').submit();
        }
    </script>
@endsection
