@extends('layout.sidebar')

@section('content')
    <style>
        /* Custom style khusus Dashboard */
        .dashboard-title {
            font-weight: 800;
            color: #334155;
            letter-spacing: -0.5px;
        }

        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid #f1f5f9;
            padding: 1.25rem;
            font-size: 1.1rem;
        }

        .plan-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px;
        }

        .badge-role {
            padding: 0.5rem 0.8rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
        }

        .icon-box {
            width: 45px;
            height: 45px;
            background: #e0e7ff;
            color: #4338ca;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }

        .btn-upgrade {
            background: linear-gradient(90deg, #4e73df 0%, #224abe 100%);
            border: none;
            color: white;
        }
    </style>

    <div class="container-fluid pb-5">
        <h2 class="dashboard-title mb-4 text-primary">Dashboard</h2>

        <div class="row g-4">
            {{-- Subscription Plans --}}

            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-header fw-bold">
                        <i class="fa fa-credit-card text-primary me-2"></i>Langganan
                    </div>
                    <div class="card-body">

                        @if ($subscription)
                            <div class="alert alert-success py-2">
                                ✅ Aktif hingga: <strong>{{ $subscription->expires_at->format('d M Y') }}</strong>
                            </div>
                            <div class="small text-muted mb-3">
                                <div>{{ $subscription->has_whatsapp ? '✅' : '❌' }} WhatsApp Bot</div>
                                <div>{{ $subscription->has_discord ? '✅' : '❌' }} Discord Bot</div>
                                <div>{{ $subscription->has_telegram ? '✅' : '❌' }} Telegram Bot</div>
                                <div>👥 Max {{ $subscription->max_groups }} Group</div>
                                <div>👤 Max {{ $subscription->max_members }} Member/Group</div>
                            </div>
                        @else
                            <div class="alert alert-warning py-2">⚠️ Belum berlangganan.</div>
                        @endif

                        <hr>
                        <h6 class="fw-bold mb-3">Buat Langganan Baru</h6>
                        {{-- Bot Notifikasi --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Bot Notifikasi</label>
                            <div class="d-flex flex-column gap-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="chk_wa"
                                        onchange="calculatePrice()"
                                        {{ $subscription?->has_whatsapp ? 'checked disabled' : '' }}>
                                    <label class="form-check-label small" for="chk_wa">
                                        <i class="fab fa-whatsapp text-success me-1"></i>
                                        WhatsApp — <strong>Rp
                                            {{ number_format($pricing['whatsapp'], 0, ',', '.') }}</strong>
                                        @if ($subscription?->has_whatsapp)
                                            <span class="badge bg-success bg-opacity-10 text-success ms-1"
                                                style="font-size:10px">Aktif</span>
                                        @endif
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="chk_discord"
                                        onchange="calculatePrice()"
                                        {{ $subscription?->has_discord ? 'checked disabled' : '' }}>
                                    <label class="form-check-label small" for="chk_discord">
                                        <i class="fab fa-discord text-primary me-1"></i>
                                        Discord — <strong>Rp {{ number_format($pricing['discord'], 0, ',', '.') }}</strong>
                                        @if ($subscription?->has_discord)
                                            <span class="badge bg-success bg-opacity-10 text-success ms-1"
                                                style="font-size:10px">Aktif</span>
                                        @endif
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="chk_telegram"
                                        onchange="calculatePrice()"
                                        {{ $subscription?->has_telegram ? 'checked disabled' : '' }}>
                                    <label class="form-check-label small" for="chk_telegram">
                                        <i class="fab fa-telegram text-info me-1"></i>
                                        Telegram — <strong>Rp
                                            {{ number_format($pricing['telegram'], 0, ',', '.') }}</strong>
                                        @if ($subscription?->has_telegram)
                                            <span class="badge bg-success bg-opacity-10 text-success ms-1"
                                                style="font-size:10px">Aktif</span>
                                        @endif
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Group --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">
                                Jumlah Group
                                <span class="text-muted">(Rp
                                    {{ number_format($pricing['per_group'], 0, ',', '.') }}/group)</span>
                            </label>
                            <div class="d-flex align-items-center gap-2">
                                <button class="btn btn-sm btn-outline-secondary"
                                    onclick="changeValue('input_groups', -1)">-</button>
                                <input type="number" id="input_groups" class="form-control form-control-sm text-center"
                                    value="{{ $subscription ? $subscription->max_groups : 1 }}"
                                    min="{{ $subscription ? $subscription->max_groups : 1 }}" max="20"
                                    onchange="calculatePrice()" style="width: 60px">
                                <button class="btn btn-sm btn-outline-secondary"
                                    onclick="changeValue('input_groups', 1)">+</button>
                            </div>
                            @if ($subscription)
                                <small class="text-muted">Minimum {{ $subscription->max_groups }} (sudah dipunya)</small>
                            @endif
                        </div>

                        {{-- Member --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">
                                Max Member per Group
                                <span class="text-muted">(Rp
                                    {{ number_format($pricing['per_member'], 0, ',', '.') }}/member)</span>
                            </label>
                            <div class="d-flex align-items-center gap-2">
                                <button class="btn btn-sm btn-outline-secondary"
                                    onclick="changeValue('input_members', -5)">-</button>
                                <input type="number" id="input_members" class="form-control form-control-sm text-center"
                                    value="{{ $subscription ? $subscription->max_members : 10 }}"
                                    min="{{ $subscription ? $subscription->max_members : 5 }}" max="500"
                                    step="5" onchange="calculatePrice()" style="width: 70px">
                                <button class="btn btn-sm btn-outline-secondary"
                                    onclick="changeValue('input_members', 5)">+</button>
                            </div>
                            @if ($subscription)
                                <small class="text-muted">Minimum {{ $subscription->max_members }} (sudah dipunya)</small>
                            @endif
                        </div>

                        {{-- Total --}}
                        <div class="alert alert-primary py-2 mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small fw-semibold">Total / 6 bulan</span>
                                <span class="fw-bold" id="totalPrice">Rp 0</span>
                            </div>
                        </div>

                        <button class="btn btn-primary w-100" onclick="pay()">
                            <i class="fa fa-credit-card me-1"></i>
                            {{ $subscription ? 'Perpanjang / Upgrade' : 'Berlangganan' }}
                        </button>

                    </div>
                </div>
            </div>

            {{-- Right Columns --}}
            <div class="col-lg-8">
                <div class="row g-4">
                    {{-- Create Group --}}
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body p-4 text-center">
                                <div class="icon-box mx-auto">
                                    <i class="fas fa-plus-circle fs-4"></i>
                                </div>
                                <h5 class="fw-bold">Create Group</h5>

                                @if (!$subscription)
                                    <div class="py-3">
                                        <p class="text-muted small">Fitur ini terkunci. Silahkan langganan terlebih dahulu.
                                        </p>
                                        <i class="fas fa-lock fs-1 text-light"></i>
                                    </div>
                                @elseif ($groupCount >= $maxGroup)
                                    <div class="py-3">
                                        <p class="text-danger small mb-0">Batas group tercapai
                                            ({{ $groupCount }}/{{ $maxGroup }})</p>
                                        <p class="text-muted x-small">Upgrade plan untuk menambah.</p>
                                    </div>
                                @else
                                    <p class="text-muted small mb-4">
                                        Sisa kuota group: <strong>{{ $maxGroup - $groupCount }}</strong>
                                    </p>
                                    <form id="formCreateGroup" method="POST" action="/groups">
                                        @csrf
                                        <input type="text" id="inputGroupName" name="name"
                                            class="form-control form-control-lg mb-3 text-center border-light-subtle"
                                            placeholder="Nama Group Baru">
                                        <button type="button" class="btn btn-primary w-100 py-2 fw-bold"
                                            onclick="submitCreateGroup()">
                                            Create Now
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Join Group --}}
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body p-4 text-center">
                                <div class="icon-box mx-auto" style="background: #fff7ed; color: #ea580c;">
                                    <i class="fas fa-link fs-4"></i>
                                </div>
                                <h5 class="fw-bold">Join Group</h5>
                                <p class="text-muted small mb-4">Masukkan kode undangan untuk bergabung.</p>
                                <form id="formJoinGroup" method="POST" action="/join">
                                    @csrf
                                    <input type="text" id="inputJoinCode" name="code"
                                        class="form-control form-control-lg mb-3 text-center border-light-subtle"
                                        placeholder="Contoh: ABC-123">
                                    <button type="button" class="btn btn-warning w-100 py-2 fw-bold text-white"
                                        onclick="submitJoinGroup()">Join Group</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- My Groups --}}
                    <div class="col-12">
                        <div class="card border-0">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                                <h5 class="mb-0 fw-bold text-dark">
                                    <i class="fas fa-user-friends me-2 text-primary"></i>My Groups
                                </h5>
                                @if ($totalGroups > 3)
                                    <a href="/groups" class="btn btn-sm btn-light text-primary fw-bold">
                                        Lihat Semua ({{ $totalGroups }})
                                    </a>
                                @endif
                            </div>
                            <div class="card-body p-4">
                                <div class="row">
                                    @forelse ($groups as $group)
                                        <div class="col-md-4 mb-3">
                                            <div class="card border shadow-none h-100">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                                        <h6 class="fw-bold mb-0">{{ $group->name }}</h6>

                                                        @php
                                                            $memberRole = \App\Models\GroupMember::where(
                                                                'group_id',
                                                                $group->id,
                                                            )
                                                                ->where('user_id', auth()->id())
                                                                ->with('role')
                                                                ->first();
                                                        @endphp

                                                        {{-- Tombol edit hanya untuk owner --}}
                                                        @if ($memberRole?->role?->is_owner)
                                                            <button class="btn btn-sm btn-light" data-bs-toggle="modal"
                                                                data-bs-target="#modalEditGroup"
                                                                data-id="{{ $group->id }}"
                                                                data-name="{{ $group->name }}">
                                                                <i class="fas fa-pen text-muted"
                                                                    style="font-size: 11px;"></i>
                                                            </button>
                                                        @else
                                                            <i class="fas fa-ellipsis-v text-muted small"></i>
                                                        @endif
                                                    </div>

                                                    @if ($memberRole?->role)
                                                        <span class="badge badge-role mb-3 d-inline-block text-white"
                                                            style="background-color: {{ $memberRole->role->color }}">
                                                            {{ $memberRole->role->name }}
                                                        </span>
                                                    @endif

                                                    <a href="/groups/{{ $group->id }}"
                                                        class="btn btn-sm btn-outline-primary w-100 rounded-pill">
                                                        Manage Group <i class="fas fa-arrow-right ms-1"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12 text-center py-4">
                                            <img src="https://illustrations.popsy.co/flat/team-building.svg"
                                                alt="empty" style="width: 150px;" class="mb-3 opacity-50">
                                            <p class="text-muted">Kamu belum bergabung di group manapun.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Modal Edit Group --}}
                    <div class="modal fade" id="modalEditGroup" tabindex="-1">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h6 class="modal-title fw-bold">
                                        <i class="fas fa-pen me-2 text-primary"></i>Edit Nama Group
                                    </h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="POST" id="formEditGroup">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small">Nama Group</label>
                                            <input type="text" name="name" id="editGroupName" class="form-control"
                                                placeholder="Nama Group" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary btn-sm"
                                            data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <script>
                        const modalEditGroup = document.getElementById('modalEditGroup');
                        modalEditGroup.addEventListener('show.bs.modal', function(e) {
                            const btn = e.relatedTarget;
                            const id = btn.getAttribute('data-id');
                            const name = btn.getAttribute('data-name');
                            document.getElementById('editGroupName').value = name;
                            document.getElementById('formEditGroup').action = `/groups/${id}`;
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast & Scripts tetap sama namun saya rapikan styling toastnya --}}
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
        <div id="toast" class="toast align-items-center text-white border-0 shadow-lg" role="alert"
            style="border-radius: 12px;">
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
            toast.className = `toast align-items-center text-white border-0 shadow-lg bg-${type}`;
            toastMsg.innerText = message;
            new bootstrap.Toast(toast, {
                delay: 3000
            }).show();
        }

        function submitCreateGroup() {
            const name = document.getElementById('inputGroupName').value.trim();
            if (!name) return showToast('⚠️ Isi nama group dulu ya!', 'warning');
            document.getElementById('formCreateGroup').submit();
        }

        function submitJoinGroup() {
            const code = document.getElementById('inputJoinCode').value.trim();
            if (!code) return showToast('⚠️ Masukkan invitation code dulu ya!', 'warning');
            document.getElementById('formJoinGroup').submit();
        }

        const pricing = {
            whatsapp: {{ $pricing['whatsapp'] }},
            discord: {{ $pricing['discord'] }},
            telegram: {{ $pricing['telegram'] }},
            per_group: {{ $pricing['per_group'] }},
            per_member: {{ $pricing['per_member'] }},
        };

        const currentSubscription = {
            has_whatsapp: {{ $subscription?->has_whatsapp ? 'true' : 'false' }},
            has_discord: {{ $subscription?->has_discord ? 'true' : 'false' }},
            has_telegram: {{ $subscription?->has_telegram ? 'true' : 'false' }},
            max_groups: {{ $subscription?->max_groups ?? 0 }},
            max_members: {{ $subscription?->max_members ?? 0 }},
        };

        function calculatePrice() {
            let total = 0;

            if (document.getElementById('chk_wa')?.checked && !currentSubscription.has_whatsapp) {
                total += pricing.whatsapp;
            }
            if (document.getElementById('chk_discord')?.checked && !currentSubscription.has_discord) {
                total += pricing.discord;
            }
            if (document.getElementById('chk_telegram')?.checked && !currentSubscription.has_telegram) {
                total += pricing.telegram;
            }

            const groups = parseInt(document.getElementById('input_groups')?.value) || 1;
            const members = parseInt(document.getElementById('input_members')?.value) || 10;

            const extraGroups = Math.max(0, groups - currentSubscription.max_groups);
            const extraMembers = Math.max(0, members - currentSubscription.max_members);

            total += extraGroups * pricing.per_group;
            total += extraMembers * pricing.per_member;

            const el = document.getElementById('totalPrice');
            if (el) el.innerText = 'Rp ' + total.toLocaleString('id-ID');

            return total;
        }

        function changeValue(id, delta) {
            const input = document.getElementById(id);
            const newVal = parseInt(input.value) + delta;
            if (newVal >= parseInt(input.min) && newVal <= parseInt(input.max)) {
                input.value = newVal;
                calculatePrice();
            }
        }

        function pay() {
            const hasWa = document.getElementById('chk_wa')?.checked ?? false;
            const hasDiscord = document.getElementById('chk_discord')?.checked ?? false;
            const hasTelegram = document.getElementById('chk_telegram')?.checked ?? false;
            const maxGroups = document.getElementById('input_groups')?.value ?? 1;
            const maxMembers = document.getElementById('input_members')?.value ?? 10;

            if (!hasWa && !hasDiscord && !hasTelegram) {
                showToast('⚠️ Pilih minimal 1 bot notifikasi!', 'warning');
                return;
            }

            fetch('/payment/snap-token', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        has_whatsapp: hasWa,
                        has_discord: hasDiscord,
                        has_telegram: hasTelegram,
                        max_groups: maxGroups,
                        max_members: maxMembers,
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.error) {
                        showToast(data.error, 'danger');
                        return;
                    }
                    snap.pay(data.token, {
                        onSuccess: function(result) {
                            fetch('/payment/sync-bots', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]').content
                                    },
                                    body: JSON.stringify({
                                        order_id: result.order_id
                                    })
                                })
                                .then(res => res.json())
                                .then(() => {
                                    window.location.href = '/payment/receipt/' + result.order_id;
                                });
                        },
                        onPending: function(result) {
                            showToast('⏳ Menunggu pembayaran...', 'warning');
                        },
                        onError: function(result) {
                            showToast('❌ Pembayaran gagal!', 'danger');
                        },
                        onClose: function() {
                            fetch('/payment/check-pending', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]').content
                                    }
                                })
                                .then(res => res.json())
                                .then(data => {
                                    if (data.synced) {
                                        window.location.href = '/payment/receipt/' + data.order_id;
                                    }
                                });
                        }
                    });
                });
        }

        // Cek payment pending saat halaman pertama kali dibuka
        document.addEventListener('DOMContentLoaded', function() {
            calculatePrice();

            // Auto cek payment pending
            fetch('/payment/check-pending', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.synced) {
                        window.location.href = '/payment/receipt/' + data.order_id;
                    }
                });
        });
        document.addEventListener('DOMContentLoaded', calculatePrice);
    </script>
@endsection
