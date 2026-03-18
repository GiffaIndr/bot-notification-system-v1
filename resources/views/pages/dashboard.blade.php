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

                        <div class="plan-list mt-3">
                            @foreach ($plans as $plan)
                                <div class="card plan-card mb-3 border-0 shadow-sm">
                                    <div class="card-body p-3">

                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <h6 class="fw-bold mb-0 text-dark">{{ $plan->name }}</h6>
                                            <span class="badge bg-primary-subtle text-primary rounded-pill fw-bold"
                                                style="font-size: 0.75rem;">
                                                Rp {{ number_format($plan->price, 0, ',', '.') }}
                                            </span>
                                        </div>

                                        <p class="text-muted mb-3" style="font-size: 0.82rem; line-height: 1.4;">
                                            {{ $plan->description }}
                                        </p>

                                        <hr class="my-2 opacity-25">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="d-flex gap-2">
                                                <span title="WhatsApp">
                                                    {!! $plan->whatsapp
                                                        ? '<i class="fab fa-whatsapp text-success"></i>'
                                                        : '<i class="fas fa-times text-danger opacity-25"></i>' !!}
                                                </span>
                                                <span title="Discord">
                                                    {!! $plan->discord
                                                        ? '<i class="fab fa-discord text-primary"></i>'
                                                        : '<i class="fas fa-times text-danger opacity-25"></i>' !!}
                                                </span>
                                                <span title="Telegram">
                                                    {!! $plan->telegram
                                                        ? '<i class="fab fa-telegram text-info"></i>'
                                                        : '<i class="fas fa-times text-danger opacity-25"></i>' !!}
                                                </span>
                                            </div>

                                            <div class="ms-auto">
                                                <small class="fw-bold text-secondary" style="font-size: 0.75rem;">
                                                    <i class="fas fa-layer-group me-1"></i>Max {{ $plan->max_group }} Groups
                                                </small>
                                            </div>
                                        </div>

                                        <button class="btn btn-sm btn-upgrade w-100 fw-bold py-2 shadow-sm rounded-3"
                                            onclick="pay({{ $plan->id }})">
                                            <i class="fas fa-shopping-cart me-1"></i>
                                            {{ $subscription ? 'Upgrade Plan' : 'Subscribe Now' }}
                                        </button>

                                    </div>
                                </div>
                            @endforeach
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
                                <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-user-friends me-2 text-primary"></i> My
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

        function pay(planId) {
            fetch('/payment/snap-token', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        plan_id: planId
                    })
                })
                .then(res => res.json())
                .then(data => {
                    snap.pay(data.token, {
                        onSuccess: (result) => {
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
                            }).then(() => {
                                // Redirect ke halaman receipt setelah sync
                                window.location.href = '/payment/receipt/' + result.order_id;
                            });
                        }
                    });
                });
        }
    </script>
@endsection
