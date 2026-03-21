@extends('layout.sidebar')

@section('content')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');

        .custom-group-card {
            background: #ffffff;
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            border: 1px solid rgba(0, 0, 0, 0.03) !important;
        }

        .custom-group-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08) !important;
            background: #fff;
        }

        .avatar-group-ui {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 800;
            font-size: 0.9rem;
            letter-spacing: 1px;
        }

        .role-pill {
            display: inline-flex;
            align-items: center;
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
            background: var(--role-color);
            background-color: color-mix(in srgb, var(--role-color), transparent 85%);
            color: var(--role-color);
        }

        .role-pill .dot {
            width: 6px;
            height: 6px;
            background-color: var(--role-color);
            border-radius: 50%;
            margin-right: 8px;
        }

        .btn-group-action {
            background: #f1f4f9;
            color: #1e293b;
            border: none;
            border-radius: 12px;
            padding: 12px 20px;
            font-weight: 700;
            font-size: 0.85rem;
            transition: 0.3s;
        }

        .btn-group-action:hover {
            background: #1e293b;
            color: #ffffff;
        }

        .btn-edit {
            width: 35px;
            height: 35px;
            transition: 0.3s;
        }

        .btn-edit:hover {
            background: #0d6efd;
            color: white !important;
        }

        .btn-edit:hover i {
            color: white !important;
        }

        .empty-animation {
            font-size: 4rem;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-20px);
            }
        }

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

        .card-subscription {
            border: none;
            background: #ffffff;
            transition: all 0.3s ease;
        }

        .platform-card {
            cursor: pointer;
            transition: all 0.2s ease;
            border: 2px solid #f8f9fa !important;
        }

        .platform-card:hover {
            border-color: #e9ecef !important;
            background-color: #f8f9fa;
        }

        /* Efek saat checkbox dipilih */
        .form-check-input:checked+.platform-label-wrapper {
            color: #0d6efd;
        }

        .custom-option input:checked~label {
            color: #0d6efd;
        }

        .custom-option input:disabled~label {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .price-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(118, 75, 162, 0.2);
        }

        .btn-upgrade {
            background: linear-gradient(45deg, #0d6efd, #0b5ed7);
            border: none;
            letter-spacing: 0.5px;
            transition: transform 0.2s;
        }

        .btn-upgrade:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
        }

        .input-group-custom {
            background: #f1f3f5;
            border-radius: 12px;
            padding: 5px;
        }

        .input-group-custom .btn {
            border-radius: 8px !important;
            background: white;
            border: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .subscription-card {
            background: #ffffff;
        }

        .divider-text {
            text-align: center;
            position: relative;
        }

        .divider-text::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 0;
            width: 100%;
            height: 1px;
            background: #f0f0f0;
            z-index: 1;
        }

        .divider-text span {
            position: relative;
            background: #fff;
            padding: 0 15px;
            z-index: 2;
            font-size: 10px;
            letter-spacing: 1px;
        }

        .custom-checkbox-card input:checked+label {
            border-color: #4e73df !important;
            background-color: rgba(78, 115, 223, 0.05);
            box-shadow: 0 4px 12px rgba(78, 115, 223, 0.1);
        }

        .custom-checkbox-card input:checked+label .platform-icon-sm {
            background-color: #fff !important;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .custom-checkbox-card input:disabled+label {
            opacity: 0.7;
            cursor: not-allowed;
            background-color: #f8f9fc;
        }

        .custom-stepper .btn:hover {
            background-color: #ececec;
        }

        .btn-pay:hover {
            transform: scale(1.02);
            filter: brightness(1.1);
        }

        .pointer {
            cursor: pointer;
        }

        .tracking-wider {
            letter-spacing: 1px;
        }

        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }

        .transition-hover {
            transition: all 0.3s ease;
        }

        .transition-hover:hover {
            transform: translateY(-5px);
            border-color: var(--bs-primary) !important;
        }

        .shadow-sm-hover:hover {
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
        }

        .btn-icon {
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .grayscale {
            filter: grayscale(1);
        }
    </style>

    <div class="container-fluid pb-5">
        <h2 class="dashboard-title mb-4 text-primary">Dashboard</h2>

        <div class="row g-4">
            {{-- Subscription Plans --}}

            <div class="col-md-4">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden subscription-card">
                    <div class="card-header bg-white border-bottom border-light py-4 px-4">
                        <h6 class="fw-bold mb-0 d-flex align-items-center text-dark">
                            <div class="icon-badge me-2 bg-warning bg-opacity-10 p-2 rounded-3">
                                <i class="fa fa-crown text-warning"></i>
                            </div>
                            Status & Paket Langganan
                        </h6>
                    </div>

                    <div class="card-body p-4">
                        @if ($subscription)
                            <div
                                class="subscription-status p-3 rounded-4 bg-success bg-opacity-10 border border-success border-opacity-25 mb-4 position-relative overflow-hidden">
                                <div class="d-flex align-items-center mb-3">
                                    <span class="badge bg-success rounded-pill px-3 py-2 shadow-sm me-2">Aktif</span>
                                    <span class="small fw-bold text-success">
                                        Hingga {{ $subscription->expires_at->format('d M Y') }}
                                    </span>
                                </div>

                                <div class="platform-icons d-flex gap-3 mb-3">
                                    <div class="platform-item {{ $subscription->has_whatsapp ? 'active' : 'opacity-25' }}">
                                        <i class="fab fa-whatsapp fs-5 text-success"></i>
                                    </div>
                                    <div class="platform-item {{ $subscription->has_discord ? 'active' : 'opacity-25' }}">
                                        <i class="fab fa-discord fs-5 text-primary"></i>
                                    </div>
                                    <div class="platform-item {{ $subscription->has_telegram ? 'active' : 'opacity-25' }}">
                                        <i class="fab fa-telegram fs-5 text-info"></i>
                                    </div>
                                </div>

                                <div class="pt-2 border-top border-success border-opacity-10">
                                    <div class="d-flex justify-content-between small fw-bold text-dark">
                                        <span><i class="fa fa-users me-1 text-muted"></i> Limit:</span>
                                        <span>{{ $subscription->max_groups }} GRP / {{ $subscription->max_members }}
                                            MEM</span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="p-4 rounded-4 bg-light border border-dashed mb-4 text-center">
                                <div class="mb-2 opacity-50"><i class="fas fa-receipt fa-2x"></i></div>
                                <p class="small text-muted mb-0 fw-medium">Belum memiliki paket aktif</p>
                            </div>
                        @endif

                        <div class="divider-text mb-4">
                            <span class="small fw-bold text-muted text-uppercase tracking-wider">Konfigurasi Baru</span>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-secondary mb-3">Pilih Platform Bot</label>
                            <div class="platform-grid d-flex flex-column gap-2">
                                @foreach (['whatsapp' => ['fa-whatsapp', 'text-success', 'chk_wa'], 'discord' => ['fa-discord', 'text-primary', 'chk_discord'], 'telegram' => ['fa-telegram', 'text-info', 'chk_telegram']] as $key => $info)
                                    <div class="custom-checkbox-card transition-all">
                                        <input class="form-check-input d-none" type="checkbox" id="{{ $info[2] }}"
                                            onchange="calculatePrice()"
                                            {{ $subscription?->{'has_' . $key} ? 'checked disabled' : '' }}>
                                        <label
                                            class="d-flex align-items-center justify-content-between p-3 border rounded-4 pointer w-100 mb-0"
                                            for="{{ $info[2] }}">
                                            <div class="d-flex align-items-center">
                                                <div
                                                    class="platform-icon-sm me-3 {{ $info[1] }} bg-light rounded-3 p-2">
                                                    <i class="fab {{ $info[0] }} fs-5"></i>
                                                </div>
                                                <span class="fw-bold text-dark">{{ ucfirst($key) }}</span>
                                            </div>
                                            <span class="badge bg-light text-dark border fw-bold px-2 py-1">
                                                Rp {{ number_format($pricing[$key], 0, ',', '.') }}
                                            </span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <label class="form-label fw-bold small text-secondary mb-2">Jumlah Group</label>
                                <div class="input-group custom-stepper rounded-pill overflow-hidden border">
                                    <button class="btn btn-light border-0 px-3" onclick="changeValue('input_groups', -1)"><i
                                            class="fas fa-minus small"></i></button>
                                    <input type="number" id="input_groups"
                                        class="form-control border-0 text-center fw-bold bg-white"
                                        value="{{ $subscription ? $subscription->max_groups : 1 }}"
                                        min="{{ $subscription ? $subscription->max_groups : 1 }}" max="20"
                                        onchange="calculatePrice()">
                                    <button class="btn btn-light border-0 px-3" onclick="changeValue('input_groups', 1)"><i
                                            class="fas fa-plus small"></i></button>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small text-secondary mb-2">Maksimal Member</label>
                                <div class="input-group custom-stepper rounded-pill overflow-hidden border">
                                    <button class="btn btn-light border-0 px-3"
                                        onclick="changeValue('input_members', -5)"><i
                                            class="fas fa-minus small"></i></button>
                                    <input type="number" id="input_members"
                                        class="form-control border-0 text-center fw-bold bg-white"
                                        value="{{ $subscription ? $subscription->max_members : 10 }}"
                                        min="{{ $subscription ? $subscription->max_members : 5 }}" max="500"
                                        step="5" onchange="calculatePrice()">
                                    <button class="btn btn-light border-0 px-3" onclick="changeValue('input_members', 5)"><i
                                            class="fas fa-plus small"></i></button>
                                </div>
                            </div>
                        </div>

                        <div class="total-estimation p-4 rounded-4 mb-4 text-center shadow-sm position-relative overflow-hidden"
                            style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
                            <div class="position-relative z-1">
                                <p class="small text-white text-opacity-75 mb-1 fw-medium">Total Estimasi (6 Bulan)</p>
                                <h3 class="fw-bold text-white mb-0" id="totalPrice">Rp 0</h3>
                            </div>
                            <div class="position-absolute bg-white opacity-10 rounded-circle"
                                style="width: 100px; height: 100px; top: -30px; right: -30px;"></div>
                        </div>

                        <button class="btn btn-primary w-100 py-3 fw-bold rounded-pill shadow btn-pay transition-all"
                            onclick="pay()">
                            <i class="fa fa-bolt me-2"></i>
                            {{ $subscription ? 'Upgrade Paket Sekarang' : 'Aktifkan Paket' }}
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
                        <div class="card border-0 shadow-lg rounded-4 overflow-hidden"
                            style="background: linear-gradient(145deg, #ffffff, #f8f9ff);">

                            <div
                                class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center ">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box me-3 bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center"
                                        style="width: 45px; height: 45px;">
                                        <i class="fas fa-layer-group fs-4"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0 fw-extrabold text-dark tracking-tight">My Universe</h5>
                                        <p class="text-muted small mb-0">Buat dan atur dunia mu!</p>
                                    </div>
                                </div>
                                @if ($totalGroups > 3)
                                    <a href="/groups" class="btn btn-dark btn-sm rounded-pill px-4 shadow-sm fw-bold">
                                        View All <span class="badge bg-primary ms-1">{{ $totalGroups }}</span>
                                    </a>
                                @endif
                            </div>

                            <div class="card-body p-4 pt-2">
                                <div class="row g-4">
                                    @forelse ($groups as $group)
                                        <div class="col-md-6">
                                            <div class="card h-100 border-0 shadow-sm custom-group-card rounded-4">
                                                <div class="card-body p-4">

                                                    <div class="d-flex justify-content-between mb-4">
                                                        <div class="avatar-group-ui shadow-sm"
                                                            style="background: linear-gradient(45deg, #6a11cb 0%, #2575fc 100%);">
                                                            {{ strtoupper(substr($group->name, 0, 2)) }}
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

                                                        @if ($memberRole?->role?->is_owner)
                                                            <button
                                                                class="btn btn-sm btn-light rounded-circle shadow-sm btn-edit"
                                                                data-bs-toggle="modal" data-bs-target="#modalEditGroup"
                                                                data-id="{{ $group->id }}"
                                                                data-name="{{ $group->name }}">
                                                                <i class="fas fa-pen-nib text-primary"></i>
                                                            </button>
                                                        @endif
                                                    </div>

                                                    <h6 class="fw-bold text-dark mb-1 h5">{{ $group->name }}</h6>

                                                    @if ($memberRole?->role)
                                                        <div class="role-pill mb-4"
                                                            style="--role-color: {{ $memberRole->role->color }}">
                                                            <span class="dot"></span>
                                                            {{ $memberRole->role->name }}
                                                        </div>
                                                    @endif

                                                    <a href="/groups/{{ $group->id }}"
                                                        class="btn btn-group-action w-100 d-flex justify-content-between align-items-center mt-auto">
                                                        <span>Enter Workspace</span>
                                                        <i class="fas fa-arrow-right shadow-sm"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12 text-center py-5">
                                            <div class="empty-animation mb-3">🚀</div>
                                            <h5 class="text-dark fw-bold">No groups found</h5>
                                            <p class="text-muted small">Start your journey by creating or joining a group.
                                            </p>
                                            <button class="btn btn-primary rounded-pill px-5">Get Started</button>
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

        document.addEventListener('DOMContentLoaded', function() {
            calculatePrice();

            // Auto cek payment pending setiap kali halaman dibuka
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
                })
                .catch(() => {});
        });
    </script>
@endsection
