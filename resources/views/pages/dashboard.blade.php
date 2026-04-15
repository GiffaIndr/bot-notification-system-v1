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

        .config-label {
            font-size: 0.78rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            margin-bottom: 6px;
        }

        .bot-check {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 10px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            cursor: pointer;
            transition: all 0.2s ease;
            background: #fff;
        }

        .bot-check:hover {
            border-color: #c7d2fe;
            background: #f8faff;
        }

        .bot-check input {
            margin-right: 8px;
        }

        .price-summary {
            border-radius: 14px;
            border: 1px solid #dbeafe;
            background: linear-gradient(135deg, #eff6ff 0%, #eef2ff 100%);
            padding: 14px;
        }

        .price-summary .label {
            font-size: 0.82rem;
            color: #475569;
            font-weight: 700;
        }

        .price-summary .value {
            font-size: 1.65rem;
            font-weight: 800;
            color: #1e40af;
            margin: 4px 0 0;
        }

        .form-control.compact-number {
            font-weight: 700;
            text-align: center;
        }
    </style>

    <div class="container-fluid pb-5">
        <h2 class="dashboard-title mb-4 text-primary">Dashboard</h2>

        <div class="row g-4">
            {{-- Subscription Config --}}
            <div class="col-lg-4">
                <div class="card h-100 border-top border-primary border-5">
                    <div class="card-header fw-bold d-flex align-items-center">
                        <i class="fas fa-crown text-warning me-2"></i> Subscription Plan
                    </div>
                    <div class="card-body">
                        @if ($subscription)
                            <div class="alert alert-success border-0 shadow-sm mb-4">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-check-circle me-2 fs-4"></i>
                                    <div>
                                        <small class="d-block">Aktif hingga:</small>
                                        <strong>{{ $subscription->expires_at->format('d M Y') }}</strong>
                                    </div>

                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning border-0 shadow-sm mb-4 text-dark">
                                <i class="fas fa-exclamation-triangle me-2"></i> Belum berlangganan.
                            </div>
                        @endif

                        <div class="mt-3">
                            <label class="config-label">Bot Yang Dipakai</label>
                            <div class="d-flex flex-column gap-2 mb-3">
                                <label class="bot-check">
                                    <span><input type="checkbox" id="chk_wa" onchange="calculatePrice()"
                                            {{ $subscription?->has_whatsapp ? 'checked' : '' }}><i
                                            class="fab fa-whatsapp text-success me-1"></i> WhatsApp</span>
                                    <span class="badge bg-light text-dark border">Rp
                                        {{ number_format($pricing['whatsapp'], 0, ',', '.') }}</span>
                                </label>
                                <label class="bot-check">
                                    <span><input type="checkbox" id="chk_discord" onchange="calculatePrice()"
                                            {{ $subscription?->has_discord ? 'checked' : '' }}><i
                                            class="fab fa-discord text-primary me-1"></i> Discord</span>
                                    <span class="badge bg-light text-dark border">Rp
                                        {{ number_format($pricing['discord'], 0, ',', '.') }}</span>
                                </label>
                                <label class="bot-check">
                                    <span><input type="checkbox" id="chk_telegram" onchange="calculatePrice()"
                                            {{ $subscription?->has_telegram ? 'checked' : '' }}><i
                                            class="fab fa-telegram text-info me-1"></i> Telegram</span>
                                    <span class="badge bg-light text-dark border">Rp
                                        {{ number_format($pricing['telegram'], 0, ',', '.') }}</span>
                                </label>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="config-label">Durasi</label>
                                    <input type="number" id="input_duration" class="form-control compact-number"
                                        value="{{ $subscription?->duration_months ?? 6 }}" min="1" max="24"
                                        onchange="calculatePrice()">
                                </div>
                                <div class="col-6">
                                    <label class="config-label">Maks Anggota</label>
                                    <input type="number" id="input_members" class="form-control compact-number"
                                        value="{{ $subscription?->max_members ?? 2 }}" min="2" max="500"
                                        step="1" onchange="calculatePrice()">
                                </div>
                            </div>

                            <small class="text-muted d-block mb-3">
                                Paket ini berlaku untuk <strong>1 group</strong> per pembayaran.
                            </small>

                            <div class="price-summary mb-3">
                                <div class="label">Total Estimasi</div>
                                <div class="value" id="totalPrice">Rp 0</div>
                            </div>

                            <button class="btn btn-sm btn-upgrade w-100 fw-bold py-2 shadow-sm rounded-3"
                                onclick="payCustom()">
                                <i class="fas fa-shopping-cart me-1"></i>
                                Beli Paket
                            </button>
                        </div>
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

                                        {{-- Cek apakah ada upgrade pending yang bisa nambah kuota --}}
                                        @php
                                            $pendingUpgrade = \App\Models\Payment::where('user_id', auth()->id())
                                                ->where('status', 'success')
                                                ->where('starts_at', '>', now())
                                                ->with('plan')
                                                ->latest()
                                                ->first();
                                        @endphp

                                        @if ($pendingUpgrade && $pendingUpgrade->plan->max_group > $maxGroup)
                                            <p class="text-success small mt-2 mb-0">
                                                <i class="fa fa-circle-info me-1"></i>
                                                Kuota akan bertambah jadi
                                                <strong>{{ $pendingUpgrade->plan->max_group }}</strong> group
                                                pada <strong>{{ $pendingUpgrade->starts_at->format('d M Y') }}</strong>
                                            </p>
                                        @endif
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
                                <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-user-friends me-2 text-primary"></i>
                                    My
                                    Groups</h5>
                                @if ($totalGroups > 3)
                                    <a href="/groups" class="btn btn-sm btn-light text-primary fw-bold">Lihat Semua
                                        ({{ $totalGroups }})</a>
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
                                                        <i class="fas fa-ellipsis-v text-muted small"></i>
                                                    </div>

                                                    @php
                                                        $memberRole = \App\Models\GroupMember::where(
                                                            'group_id',
                                                            $group->id,
                                                        )
                                                            ->where('user_id', auth()->id())
                                                            ->with('role')
                                                            ->first();
                                                    @endphp

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
        // JS Logic tetap sama sesuai kode originalmu
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
        const fixedGroups = 1;

        function calculatePrice() {
            const hasWa = document.getElementById('chk_wa').checked;
            const hasDiscord = document.getElementById('chk_discord').checked;
            const hasTelegram = document.getElementById('chk_telegram').checked;
            const maxGroups = fixedGroups;
            const maxMembers = Math.max(2, parseInt(document.getElementById('input_members').value || '2', 10));
            const durationMonths = Math.max(1, Math.min(parseInt(document.getElementById('input_duration').value || '6',
                10), 24));
            document.getElementById('input_members').value = maxMembers;
            document.getElementById('input_duration').value = durationMonths;

            let packageCostFor6Months = 0;
            if (hasWa) packageCostFor6Months += pricing.whatsapp;
            if (hasDiscord) packageCostFor6Months += pricing.discord;
            if (hasTelegram) packageCostFor6Months += pricing.telegram;
            packageCostFor6Months += (maxGroups * pricing.per_group);
            packageCostFor6Months += (maxMembers * pricing.per_member);

            const total = Math.round((packageCostFor6Months / 6) * durationMonths);
            document.getElementById('totalPrice').innerText = 'Rp ' + total.toLocaleString('id-ID');
            return {
                hasWa,
                hasDiscord,
                hasTelegram,
                maxGroups,
                maxMembers,
                durationMonths,
            };
        }

        function payCustom() {
            const payload = calculatePrice();
            const hasWa = payload.hasWa;
            const hasDiscord = payload.hasDiscord;
            const hasTelegram = payload.hasTelegram;
            const maxGroups = payload.maxGroups;
            const maxMembers = payload.maxMembers;
            const durationMonths = payload.durationMonths;

            if (!hasWa && !hasDiscord && !hasTelegram) {
                showToast('Pilih minimal 1 bot notifikasi.', 'warning');
                return;
            }

            const params = new URLSearchParams({
                has_whatsapp: hasWa ? '1' : '0',
                has_discord: hasDiscord ? '1' : '0',
                has_telegram: hasTelegram ? '1' : '0',
                max_groups: String(maxGroups),
                max_members: String(maxMembers),
                duration_months: String(durationMonths),
                from_dashboard: '1',
            });

            window.location.href = '/payments?' + params.toString();
        }

        document.addEventListener('DOMContentLoaded', function() {
            calculatePrice();
        });
    </script>
@endsection
